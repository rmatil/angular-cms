/*jslint browser: true, sloppy: true, plusplus: true, nomen: true*/
/*globals angular, moment */
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
    .controller('eventDetailCtrl', ['MenuService', '$scope', '$routeParams', '$timeout', '$location', 'genService', 'toaster', 'dateFilter', '$moment', function (MenuService, $scope, $routeParams, $timeout, $location, genService, toaster, dateFilter, $moment) {
        // set Menu according to its Name
        MenuService.update("Veranstaltungen");

        // cancel this promise on route change
        var redirectTimeoutPromise,
            now = $moment();

        $scope.apiPath   = 'events';
        $scope.menuName  = 'Veranstaltung bearbeiten';
        $scope.deleteMsg = 'Löschen';

        // these values get merged to a datetime object on save
        $scope.eventStartDate = now.format('DD.MM.YYYY');
        $scope.eventStartTime = now.format('HH.mm');
        $scope.eventEndDate = now.format('DD.MM.YYYY');
        $scope.eventEndTime = now.add(1, 'h').format('HH.mm');

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
            $scope.eventStartDate = dateFilter(startDate, 'dd.MM.yyyy');
            $scope.eventEndDate   = dateFilter(endDate, 'dd.MM.yyyy');
            $scope.eventStartTime = ('0' + startDate.getHours()).substr(-2) + ':' + ('0' + startDate.getMinutes()).substr(-2);
            $scope.eventEndTime   = ('0' + endDate.getHours()).substr(-2) + ':' + ('0' + endDate.getMinutes()).substr(-2);
        });

        genService.getAllObjects('repeatOptions').then(function (response) {
            $scope.allRepeatOptions = response;
        });

        genService.getAllObjects('files').then(function (response) {
            $scope.allFiles = [{
                'id': -1,
                'name':  'Kein Anhang'
            }];

            response.forEach(function (element, index, array) {
                $scope.allFiles.push(element);
            });
        });

        genService.getAllObjects('locations').then(function (response) {
            $scope.allLocations = [{
                'id': -1,
                'name': 'Kein Veranstaltungsort'
            }];

            response.forEach(function (element, index, array) {
                $scope.allLocations.push(element);
            })
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

            if (angular.isString(pEvent.file)) {
                pEvent.file = JSON.parse(pEvent.file);

                if (pEvent.file.id === -1) {
                    pEvent.file = null;
                }
            }

            if (angular.isString(pEvent.location)) {
                pEvent.location = JSON.parse(pEvent.location);

                if (pEvent.location.id === -1) {
                    pEvent.location = null;
                }
            }

            // merge time and date
            var startDate = $moment($scope.eventStartDate + ' ' + $scope.eventStartTime, 'MM.DD.YYYY HH:mm');
            var endDate = $moment($scope.eventEndDate + ' ' + $scope.eventEndTime, 'MM.DD.YYYY HH:mm');

            if (!startDate.isValid()) {
                toaster.pop('warning', null, 'Das Startdatum ist invalid');
                return;
            }

            if (!endDate.isValid()) {
                toaster.pop('warning', null, 'Das Enddatum ist invalid');
                return;
            }

            pEvent.start_date = startDate.format();
            pEvent.end_date = endDate.format();

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
    .controller('eventAddCtrl', ['genService', 'MenuService', '$scope', '$location', '$timeout', 'toaster', '$moment', function (genService, MenuService, $scope, $location, $timeout, toaster, $moment) {
        // set Menu according to its Name
        MenuService.update("Veranstaltungen");

        // cancel this promise on route change
        var redirectTimeoutPromise,
            now = $moment();

        $scope.apiPath   = 'events';
        $scope.menuName  = 'Veranstaltung hinzufügen';

        // these values get merged to a datetime object on save
        $scope.eventStartDate = now.format('DD.MM.YYYY');
        $scope.eventStartTime = now.format('HH.mm');
        $scope.eventEndDate = now.format('DD.MM.YYYY');
        $scope.eventEndTime = now.add(1, 'h').format('HH.mm');

        // init content of ckEditor and prevent empty content
        $scope.event = {
            'description': ''
        };

        // get event
        genService.getEmptyObject('event').then(function (response) {
            if (!response) {
                toaster.pop('error', null, 'Der angeforderte Event exisitert nicht (mehr).');
                $location.path("/events");
            }
            $scope.event = response;
        });

        genService.getAllObjects('repeatOptions').then(function (response) {
            $scope.allRepeatOptions = response;
        });

        genService.getAllObjects('files').then(function (response) {
            $scope.allFiles = [{
                'id': -1,
                'name':  'Kein Anhang'
            }];

            response.forEach(function (element, index, array) {
                $scope.allFiles.push(element);
            });
        });

        genService.getAllObjects('locations').then(function (response) {
            $scope.allLocations = [{
                'id': -1,
                'name': 'Kein Veranstaltungsort'
            }];

            response.forEach(function (element, index, array) {
                $scope.allLocations.push(element);
            })
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

            if (!$scope.eventStartTime) {
                 toaster.pop('warning', null, 'Die Startzeit muss angegeben werden');
                 return;
            }

            if (!$scope.eventEndTime) {
                 toaster.pop('warning', null, 'Die Endzeit muss angegeben werden');
                 return;
            }

            if (angular.isString(pEvent.file)) {
                pEvent.file = JSON.parse(pEvent.file);

                if (pEvent.file.id === -1) {
                    pEvent.file = null;
                }
            }

            if (angular.isString(pEvent.location)) {
                pEvent.location = JSON.parse(pEvent.location);

                if (pEvent.location.id === -1) {
                    pEvent.location = null;
                }
            }

            // merge time and date
            var startDate = $moment($scope.eventStartDate + ' ' + $scope.eventStartTime, 'MM.DD.YYYY HH:mm');
            var endDate = $moment($scope.eventEndDate + ' ' + $scope.eventEndTime, 'MM.DD.YYYY HH:mm');

            if (!startDate.isValid()) {
                toaster.pop('warning', null, 'Das Startdatum ist invalid');
                return;
            }

            if (!endDate.isValid()) {
                toaster.pop('warning', null, 'Das Enddatum ist invalid');
                return;
            }

            pEvent.start_date = startDate.format();
            pEvent.end_date = endDate.format();

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