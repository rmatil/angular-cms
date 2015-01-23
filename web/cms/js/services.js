'use strict';

/* Services */

angular.module('cms.services', [])
    .service('MenuService', ['$http', '$rootScope', '$log', function ($http, $rootScope, $log) {
        this.update = function (pMenuName) {
            $http.get('json/menuProperties.json').then(function (response) {
                // success handler of $http, using the "synchronous" way
                var menu = response.data;
                if ($rootScope.debugModus) {
                    $log.log("MenuProperties loaded");
                }

                if ($rootScope.menu === null) {
                    $rootScope.menu = menu.Dashboard;
                    $rootScope.activeMenuPoint = "Dashboard";
                    $rootScope.backgroundColorClass = menu.Dashboard.backgroundColorClass;
                    $rootScope.topBorderClass = menu.Dashboard.topBorderClass;
                } else {
                    $rootScope.activeMenuPoint = pMenuName;
                    $rootScope.menuArray = menu[pMenuName].menuArray;
                    $rootScope.backgroundColorClass = menu[pMenuName].backgroundColorClass;
                    $rootScope.topBorderClass = menu[pMenuName].topBorderClass;
                }
            }, function () {
                // error handler of $http, using the "synchronous" way
                if ($rootScope.debugModus) {
                    $log.error("MenuProperties could not be loaded. Server response failed");
                }
            });
        };
    }]);
