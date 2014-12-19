'use strict';

angular.module('cms.controllers')
    .controller('settingCtrl', ['MenuService', '$scope', 'genService', '$timeout', '$route', '$rootScope', 'toaster', function (MenuService, $scope, genService, $timeout, $route, $rootScope, toaster) {
        // set menu according to its Name
        MenuService.update("Einstellungen");

        // cancel this promise on route change
        var redirectTimeoutPromise;

        $scope.apiPath  = 'settings';

        genService.getAllObjects('settings').then(function (response) {
            $scope.settings = response;
        });

        $scope.saveSettings = function (pSettings) {
            $scope.loading = true;

            // workaround for url-scheme
            pSettings.id = 1; // does not have any impact

            genService.updateObject($scope.apiPath, pSettings).then(function () {
                toaster.pop('success', null, "Einstellungen wurden aktualisiert");
                $rootScope.debugModus = pSettings.debug_mode.value;

                redirectTimeoutPromise = $timeout(function () {
                    $route.reload();
                    $scope.loading = false;
                }, 1500);
            });
        };

        // cancel redirect promises on route change
        $scope.$on('$locationChangeStart', function () {
            $timeout.cancel(redirectTimeoutPromise);
        });
    }])
    .controller('settingLogCtrl', ['MenuService', function (MenuService) {
        // set menu according to its Name
        MenuService.update("Einstellungen");
    }]);