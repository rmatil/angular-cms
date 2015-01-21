/*jslint browser: true, sloppy: true, plusplus: true, nomen: true*/
/*globals angular, window, moment */
'use strict';

/* controller for articles */
angular.module('cms.controllers')
    .controller('eventCtrl', ['genService', 'MenuService', '$scope', '$timeout', function (genService, MenuService, $scope, $timeout) {
        // set Menu according to its Name
        MenuService.update("Veranstaltungen");

        $scope.loadingEvents = true;
        genService.getAllObjects('locations').then(function (response) {
            $scope.allLocations = response;
            $timeout(function () {
                $scope.loadingEvents = false;
            }, 300);
        });

        genService.getAllObjects('events').then(function (response) {
            $scope.allEvents = response;
        });
    }])
    .controller('eventDetailCtrl', ['MenuService', '$scope', '$routeParams', '$timeout', '$location', 'genService', 'toaster', 'dateFilter', function (MenuService, $scope, $routeParams, $timeout, $location, genService, toaster, dateFilter) {
        // set Menu according to its Name
        MenuService.update("Veranstaltungen");

        // cancel this promise on route change
        var redirectTimeoutPromise;

        $scope.apiPath   = 'events';
        $scope.menuName  = 'Veranstaltung bearbeiten';
        $scope.deleteMsg = 'Löschen';

        // these values get merged to a datetime object on save
        $scope.eventStartDate = {};
        $scope.eventEndDate = {};
        $scope.eventStartTime = {};
        $scope.eventEndTime = {};

        // init content of ckEditor and prevent empty content
        $scope.event = {};
        $scope.event.description = '';

        // get event
        genService.getObjectById('events', $routeParams.eventId).then(function (response) {
            if (!response) {
                toaster.pop('error', null, 'Uups. Der angeforderte Event exisitert nicht (mehr).');
                $location.path("/events");
            }
            $scope.event = response;

            // setup start resp. end date
            var startDate         = new Date($scope.event.start_date);
            var endDate           = new Date($scope.event.end_date);
            $scope.eventStartDate = dateFilter(startDate, 'yyyy-MM-dd');
            $scope.eventEndDate   = dateFilter(endDate, 'yyyy-MM-dd');
            $scope.eventStartTime = startDate.getHours() + ':' + startDate.getMinutes();
            $scope.eventEndTime   = endDate.getHours() + ':' + endDate.getMinutes();
        });

        genService.getAllObjects('repeatOptions').then(function (response) {
            $scope.allRepeatOptions = response;
        });

        genService.getAllObjects('files').then(function (response) {
            $scope.allFiles = response;
        });

        genService.getAllObjects('locations').then(function (response) {
            $scope.allLocations = response;
        });

        $scope.deleteMsg = "Event löschen";
        $scope.ctr = 5;
        $scope.cancelled = false;

        // save changes in event
        $scope.saveEvent = function (pEvent) {
            if (!pEvent.name) {
                toaster.pop('warning', null, "Der Eventname muss angegeben werden");
                return;
            }

            if (!$scope.eventStartDate) {
                toaster.pop('warning', null, 'Das Startdatum muss angegeben werden');
                return;
            }

            if (!$scope.eventStartTime) {
                toaster.pop('warning', null, 'Die Startzeit muss angegeben werden');
                return;
            }

            if (!$scope.eventEndTime) {
                toaster.pop('warning', null, 'Die Endzeit muss angegeben werden');
                return;
            }

            if (!pEvent.repeat_option) {
                toaster.pop('warning', null, 'Eine Wiederholoption muss angegeben werden');
                return;
            }

            // Unfortunately angular supports only strings in model
            // -> parse file and repeat_option to JSON if string
            // -> http://stackoverflow.com/questions/14832405/angularjs-ng-model-converts-object-to-string
            if (angular.isString(pEvent.repeat_option)) {
                pEvent.repeat_option = JSON.parse(pEvent.repeat_option);
            }

            if (angular.isString(pEvent.file)) {
                pEvent.file = JSON.parse(pEvent.file);
            }

            if (angular.isString(pEvent.location)) {
                pEvent.location = JSON.parse(pEvent.location);
            }

            // merge time and date
            var startDate = moment(new Date($scope.eventStartDate + ' ' + $scope.eventStartTime));
            // momentjs converts to a correct iso 8601 date string
            pEvent.start_date = startDate.format();
            if ($scope.eventEndDate) {
                var endDate = moment(new Date($scope.eventEndDate + ' ' + $scope.eventEndTime));
                // momentjs converts to a correct iso 8601 date string
                pEvent.end_date = endDate.format();
            } else {
                pEvent.end_date = pEvent.start_date;
            }

            $scope.loading = true;
            genService.updateObject('events', pEvent).then(function () {
                toaster.pop('success', null, "Event wurde aktualisiert");
                redirectTimeoutPromise = $timeout(function () {
                    $location.path('/events');
                    $scope.loading = false;
                }, 2500);
            });
        };

        // cancel redirect promises on route change
        $scope.$on('$locationChangeStart', function () {
            $timeout.cancel(redirectTimeoutPromise);
        });
    }])
    .controller('eventAddCtrl', ['genService', 'MenuService', '$scope', '$location', '$timeout', 'toaster', function (genService, MenuService, $scope, $location, $timeout, toaster) {
        // set Menu according to its Name
        MenuService.update("Veranstaltungen");

        // cancel this promise on route change
        var redirectTimeoutPromise;

        $scope.apiPath   = 'events';
        $scope.menuName  = 'Veranstaltung hinzufügen';

        // these values get merged to a datetime object on save
        $scope.eventStartDate = {};
        $scope.eventEndDate = {};
        $scope.eventStartTime = {};
        $scope.eventEndTime = {};

        // init content of ckEditor and prevent empty content
        $scope.event = {};
        $scope.event.description = '';

        // get event
        genService.getEmptyObject('event').then(function (response) {
            if (!response) {
                toaster.pop('error', null, 'Uups. Der angeforderte Event exisitert nicht (mehr).');
                $location.path("/events");
            }
            $scope.event = response;
        });

        genService.getAllObjects('repeatOptions').then(function (response) {
            $scope.allRepeatOptions = response;
        });

        genService.getAllObjects('files').then(function (response) {
            $scope.allFiles = response;
        });

        genService.getAllObjects('locations').then(function (response) {
            $scope.allLocations = response;
        });


        $scope.cancelled = false;

        // save changes in event
        $scope.saveEvent = function (pEvent) {
            if (!pEvent.name) {
                toaster.pop('warning', null, "Der Eventname muss angegeben werden");
                return;
            }

            if (!$scope.eventStartDate) {
                toaster.pop('warning', null, 'Das Startdatum muss angegeben werden');
                return;
            }

            // if (!$scope.eventStartTime) {
            //     toaster.pop('warning', null, 'Die Startzeit muss angegeben werden');
            //     return;
            // }

            if (!$scope.eventEndTime) {
                toaster.pop('warning', null, 'Die Endzeit muss angegeben werden');
                return;
            }

            // if (!pEvent.repeat_option) {
            //     toaster.pop('warning', null, 'Eine Wiederholoption muss angegeben werden');
            //     return;
            // }

            // Unfortunately angular supports only strings in model
            // -> parse file and repeat_option to JSON if string
            // -> http://stackoverflow.com/questions/14832405/angularjs-ng-model-converts-object-to-string
            // if (angular.isString(pEvent.repeat_option)) {
            //     pEvent.repeat_option = JSON.parse(pEvent.repeat_option);
            // }

            if (angular.isString(pEvent.file)) {
                pEvent.file = JSON.parse(pEvent.file);
            }

            if (angular.isString(pEvent.location)) {
                pEvent.location = JSON.parse(pEvent.location);
            }

            // merge time and date
            var startDate = moment(new Date($scope.eventStartDate + ' ' + $scope.eventStartTime));
            // momentjs converts to a correct iso 8601 date string
            pEvent.start_date = startDate.format();
            // if ($scope.eventEndDate) {
            //     var endDate = moment(new Date($scope.eventEndDate + ' ' + $scope.eventEndTime));
            //     // momentjs converts to a correct iso 8601 date string
            //     pEvent.end_date = endDate.format();
            // } else {
            //     pEvent.end_date = pEvent.start_date;
            // }

            $scope.loading = true;
            genService.insertObject('events', pEvent).then(function () {
                toaster.pop('success', null, "Event wurde aktualisiert");
                redirectTimeoutPromise = $timeout(function () {
                    $location.path('/events');
                    $scope.loading = false;
                }, 2500);
            });
        };

        // cancel redirect promises on route change
        $scope.$on('$locationChangeStart', function () {
            $timeout.cancel(redirectTimeoutPromise);
        });
    }]);