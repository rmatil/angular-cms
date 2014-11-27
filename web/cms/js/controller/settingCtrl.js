'use strict';

angular.module('cms.controllers')
    .controller('settingCtrl', ['MenuService', '$routeParams', '$scope', 'genService', '$log', '$timeout', '$route', '$rootScope','toaster', function(MenuService, $routeParams, $scope, genService, $log, $timeout, $route, $rootScope, toaster) {
        // set menu according to its Name
        MenuService.update("Einstellungen");

        // cancel this promise on route change
        var redirectTimeoutPromise;

        $scope.apiPath  = 'settings';

        genService.getAllObjects('settings').then(function (response) {
            if ($scope.debugModus) {
                $log.log('settings received');
                $log.log(response);
            }
            $scope.settings = response;
        });

        
        $scope.saveSettings = function(pSettings) {
            $scope.loading = true;

            // workaround for url-scheme
            pSettings.id = 1; // does not have any impact

            genService.updateObject($scope.apiPath, pSettings).then(function(response) {
                if (response.data !== "") {
                    toaster.pop('error', null, "Einstellungen konnten nicht aktualisiert werden: " + response.data);                    
                } else {
                    toaster.pop('success', null, "Einstellungen wurden aktualisiert");
                    $rootScope.debugModus = pSettings['debug_mode'].value;
                    
                    redirectTimeoutPromise = $timeout(function() {
                        $route.reload();
                        $scope.loading = false;
                    }, 1500);
                }
            });
        };

        // cancel redirect promises on route change
        $scope.$on('$locationChangeStart', function () {
            $timeout.cancel(redirectTimeoutPromise);
        });
        
    }])
    .controller('settingLogCtrl', ['genService', 'MenuService', '$routeParams', '$scope', 'SettingsService', '$log', '$timeout', '$location', '$rootScope', function(genService, MenuService, $routeParams, $scope, SettingsService, $log, $timeout, $location, $rootScope) {
        // set menu according to its Name
        MenuService.update("Einstellungen");
        genService.getAllObjects('settings').then(function(response) {
            $scope.settings = response;
        });


        SettingsService.getAllLoginContent().then(function (response) {
            if ($scope.debugModus) {
                $log.log(response); 
            }
            $scope.sysLog = response[0];
            $scope.dbLog = response[1]; 
            
            if ($scope.debugModus) {
                $log.log($scope.dbLog);
            }
        })
    }]);