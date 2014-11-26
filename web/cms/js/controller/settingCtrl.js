'use strict';

angular.module('cms.controllers')
	.controller('settingCtrl', ['MenuService', '$routeParams', '$scope', 'genService', '$log', '$timeout', '$location', '$rootScope', '$route', 'toaster', function(MenuService, $routeParams, $scope, genService, $log, $timeout, $location, $rootScope, $route, toaster) {
		// set menu according to its Name
		MenuService.update("Einstellungen");

		// cancel this promise on route change
		var redirectTimeoutPromise;

		genService.getAllObjects('settings').then(function(response) {
			$scope.settings = response[0];
			// convert port from string to int for use in input value
			$scope.settings.mailserver_port = parseInt($scope.settings.mailserver_port);
			if ($scope.debugModus) {
				$log.log($scope.settings);
			}
			// preselect the current frontPage
			for (var i = 0; i < $scope.settings.allPages.length; i++) {
				if ($scope.settings.allPages[i].pageId == $scope.settings.curFrontpage.pageId) {
					$scope.settings.curFrontpage = $scope.settings.allPages[i];
				
					if ($scope.debugModus) {
						$log.log($scope.settings.allPages[i].pageId);
						$log.log($scope.settings.curFrontpage.pageId);
					}
					break;
				}
			}
		});

		
		$scope.saveSettings = function(pSettings) {
			$scope.loading = true;
			genService.updateObject('settings', pSettings).then(function(response) {
				if ($scope.debugModus) $log.log(response);
				if (response.data !== "") {
					toaster.pop('error', null, "Einstellungen konnten nicht aktualisiert werden: " + response.data);					
				} else {
					toaster.pop('success', null, "Einstellungen wurden aktualisiert");
					$rootScope.debugModus = pSettings['debugModus'];
					redirectTimeoutPromise = $timeout(function() {
						$route.reload();
						$scope.loading = false;
					}, 1500);
				}
			});
		};

		// cancel redirect promises on route change
		$scope.$on('$locationChangeStart', function(){
		    $timeout.cancel(redirectTimeoutPromise);
		});
		
	}])
	.controller('settingLogCtrl', ['genService', 'MenuService', '$routeParams', '$scope', 'SettingsService', '$log', '$timeout', '$location', '$rootScope', function(genService, MenuService, $routeParams, $scope, SettingsService, $log, $timeout, $location, $rootScope) {
		// set menu according to its Name
		MenuService.update("Einstellungen");
		genService.getAllObjects('settings').then(function(response) {
			$scope.settings = response;
		});


		SettingsService.getAllLoginContent().then(function(response) {
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