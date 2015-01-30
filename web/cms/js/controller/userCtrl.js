'use strict';

angular.module('cms.controllers')
    .controller('userCtrl', ['MenuService', 'genService', '$scope', '$timeout', function (MenuService, genService, $scope, $timeout) {
        // set menu according to its Name
        MenuService.update("Benutzer");

        $scope.apiPath   = 'users';
        $scope.deleteMsg = 'Löschen';

        // get all Users
        $scope.loadingUsers = true;
        genService.getAllObjects('users').then(function (response) {
            $scope.users = response;
            $timeout(function () {
                $scope.loadingUsers = false;
            }, 300);
        });

    }])
    .controller('userDetailCtrl', ['MenuService', 'genService', '$scope', '$routeParams', '$location', '$timeout', 'toaster', function (MenuService, genService, $scope, $routeParams, $location, $timeout, toaster) {
        // set Menu according to its Name
        MenuService.update("Benutzer");

        // cancel this promise on route change
        var redirectTimeoutPromise;

        $scope.apiPath   = 'users'; // used for genService
        $scope.menuName  = 'Benutzer bearbeiten';
        $scope.deleteMsg = 'Löschen';

        // represent fields for passwords
        $scope.pass1    = '';

        genService.getObjectById('users', $routeParams.userId).then(function (response) {
            $scope.user = response;
        });

        genService.getAllObjects('usergroups').then(function (response) {
            $scope.allUserGroups = response;
        });

        // save changes in page
        $scope.saveUser = function (pUser) {
            // save user with no new pass if empty
            if ($scope.pass1.length > 0) {
                pUser.plain_password = $scope.pass1;
            }

            if (pUser.user_name.length < 1) {
                toaster.pop('error', null, 'Vorname und Nachname muss ausgefüllt werden');
                return;
            }
            if (pUser.email.length < 1) {
                toaster.pop('error', null, 'Email muss ausgefüllt werden');
                return;
            }

            if (!pUser.user_group) {
                toaster.pop('error', null, 'Usergroup muss ausgewählt werden');
                return;
            }

            // Unfortunately angular supports only strings in model
            // -> parse usergroup to JSON
            // -> http://stackoverflow.com/questions/14832405/angularjs-ng-model-converts-object-to-string
            if (angular.isString(pUser.user_group)) {
                pUser.user_group = JSON.parse(pUser.user_group);
            }

            $scope.loading = true;
            genService.updateObject('users', pUser).then(function () {
                toaster.pop('success', null, "Benutzer wurde aktualisiert");
                redirectTimeoutPromise = $timeout(function () {
                    $location.path('/users');
                    $scope.loading = false;
                }, 2500);
            });
        };

        // cancel redirect promises on route change
        $scope.$on('$locationChangeStart', function () {
            $timeout.cancel(redirectTimeoutPromise);
        });
    }])
    .controller('userAddCtrl', ['genService', 'MenuService', '$scope', '$location', '$timeout', 'toaster', function (genService, MenuService, $scope, $location, $timeout, toaster) {
        // set Menu according to its Name
        MenuService.update("Benutzer");

        // cancel this promise on route change
        var redirectTimeoutPromise;

        $scope.apiPath   = 'users'; // used for genService
        $scope.menuName  = 'Benutzer hinzufügen';

        // represent fields for passwords
        $scope.pass1    = '';

        genService.getEmptyObject('user').then(function (response) {
            $scope.user = response;
        });

        genService.getAllObjects('usergroups').then(function (response) {
            $scope.allUserGroups = response;
        });

        // save changes in page
        $scope.saveUser = function (pUser) {
            if (pUser.username === '') {
                toaster.pop('error', null, 'Vorname und Nachname muss ausgefüllt werden');
                return;
            }
            if (pUser.email === '') {
                toaster.pop('error', null, 'Email muss ausgefüllt werden');
                return;
            }

            // Unfortunately angular supports only strings in model
            // -> parse usergroup to JSON
            // -> http://stackoverflow.com/questions/14832405/angularjs-ng-model-converts-object-to-string
            if (angular.isString(pUser.user_group)) {
                pUser.user_group = JSON.parse(pUser.user_group);
            }

            $scope.loading = true;
            genService.insertObject('users', pUser).then(function () {
                toaster.pop('success', null, "Benutzer wurde aktualisiert");
                redirectTimeoutPromise = $timeout(function () {
                    $location.path('/users');
                    $scope.loading = false;
                }, 2500);
            });
        };

        // cancel redirect promises on route change
        $scope.$on('$locationChangeStart', function () {
            $timeout.cancel(redirectTimeoutPromise);
        });
    }]);