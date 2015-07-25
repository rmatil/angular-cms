'use strict';


(function(angular) {
/* controller for dashboard */
angular
    .module('cms.controllers')
    .controller('DashboardController', [
        'NavigationService',
        //'genService',
        '$scope',
        function (NavigationService, $scope) {
        // set Menu according to its Name
        //MenuService.update("Dashboard");
        //
        //genService.getAllObjects('statistics').then(function (response) {
        //    $scope.statistics = response;
        //});

            NavigationService.getMenuProperty('Dashboard');

            console.log("Invoked Dashboard Controller");


    }]);

    console.log("DashboardController");

})(angular);
