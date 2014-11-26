'use strict';

/* controller for dashboard */
angular.module('cms.controllers')
	.controller('overviewCtrl', ['MenuService', 'OverviewService', '$scope', function(MenuService, OverviewService, $scope) {
		// set Menu according to its Name
		MenuService.update("Dashboard");

		// select latest added articles
		OverviewService.getNewestArticles().then(function(response) {
			if (response == 'null') {
				return;
			}
			$scope.newestArticles = response;
		});

		// select latest items
		OverviewService.getNewestItems().then(function(response) {
			if (response == 'null') {
				return;
			}
			$scope.newestItems = response;
		});

		// select latest added user
		OverviewService.getNewestUser().then(function(response) {
			if (response == 'null') {
				return;
			}
			$scope.newestUser = response;
		});

		// select next 5 events
		OverviewService.getNewestEvents().then(function(response) {
			if (response == 'null') {
				return;
			}
			$scope.newestEvents = response;
		});
	}]);