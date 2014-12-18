'use strict';

/* controller for dashboard */
angular.module('cms.controllers')
	.controller('overviewCtrl', ['MenuService', 'genService', '$scope', '$log', function(MenuService, genService, $scope, $log) {
		// set Menu according to its Name
		MenuService.update("Dashboard");

		genService.getAllObjects('statistics').then(function (response) {
			if ($scope.debugModus) {
				$log.log(response);
			}
			$scope.statistics = response;
		});
		
	}]);