'use strict';

/* controller for dashboard */
angular.module('cms.controllers')
    .controller('overviewCtrl', ['MenuService', 'genService', '$scope', function (MenuService, genService, $scope) {
        // set Menu according to its Name
        MenuService.update("Dashboard");

        genService.getAllObjects('statistics').then(function (response) {
            $scope.statistics = response;
        });
    }]);