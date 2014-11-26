'use strict';

/* controller for articles */
angular.module('cms.controllers')
	.controller('eventCtrl', ['genService', 'MenuService', '$scope', '$routeParams', '$filter', '$timeout', function(genService, MenuService, $scope, $routeParams, $filter, $timeout) {
		// set Menu according to its Name
		MenuService.update("Veranstaltungen");

		genService.getAllObjects('locations').then(function(response) {
			if (response == null) {
				return;
			}
			$scope.allLocations = response;
		});

		genService.getAllObjects('events').then(function(response) {
			if (response == null) {
				$scope.message = "Keine Veranstaltungen";
				return;
			}
			$scope.allEvents = response;
		})

	}])
	.controller('eventDetailCtrl', ['MenuService', '$scope', '$routeParams', '$timeout', '$location', '$log', 'genService', 'toaster', 'dateFilter', function(MenuService, $scope, $routeParams, $timeout, $location, $log, genService, toaster, dateFilter) {
		// set Menu according to its Name
		MenuService.update("Veranstaltungen");

		// cancel this promise on route change
		var redirectTimeoutPromise;

		$scope.apiPath 	 = 'events';
		$scope.menuName  = 'Veranstaltung bearbeiten';
		$scope.deleteMsg = 'Löschen';

		// these values get merged to a datetime object on save
		$scope.eventStartDate;
		$scope.eventEndDate;
		$scope.eventStartTime;
		$scope.eventEndTime;

		// get event
		genService.getObjectById('events', $routeParams.eventId).then(function (response) {
			if ($scope.debugModus) {
                $log.log("events received");
                $log.log(response);
            }

            if (!response) {
            	toaster.pop('error', null, 'Uups. Der angeforderte Event exisitert nicht (mehr).');
                $location.path("/events");
            }
			$scope.event = response;

            // setup start resp. end date
            var startDate         = new Date($scope.event.start_date);
            var endDate           = new Date($scope.event.end_date);
            $scope.eventStartDate = dateFilter(startDate, 'yyyy-MM-dd');
            $scope.eventEndDate   = dateFilter(endDate, 'yyyy-MM-dd');
            $scope.eventStartTime = startDate.getHours() + ':' + startDate.getMinutes();
            $scope.eventEndTime   = endDate.getHours() + ':' + endDate.getMinutes();
		});

		genService.getAllObjects('repeatOptions').then(function (response) {
			if ($scope.debugModus) {
				$log.log('repeatOptions received');
				$log.log(response);
			}

			$scope.allRepeatOptions = response;
		});

		genService.getAllObjects('files').then(function (response) {
			if ($scope.debugModus) {
				$log.log('files received');
				$log.log(response);
			}

			$scope.allFiles = response;
		});

		genService.getAllObjects('locations').then(function (response) {
			if ($scope.debugModus) {
				$log.log('locations received');
				$log.log(response);
			}

			$scope.allLocations = response;
		});

		$scope.deleteMsg = "Event löschen";
		$scope.ctr = 5;
		$scope.cancelled = false;

		// save changes in event
		$scope.saveEvent = function(pEvent) {
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
			}

			// Unfortunately angular supports only strings in model
            // -> parse file and repeat_option to JSON if string
            // -> http://stackoverflow.com/questions/14832405/angularjs-ng-model-converts-object-to-string
            if (angular.isString(pEvent.repeat_option)) {
                pEvent.repeat_option = JSON.parse(pEvent.repeat_option);
            }

            if (angular.isString(pEvent.file)) {
                pEvent.file = JSON.parse(pEvent.file);
            }

            // merge time and date
            var startDate = moment(new Date($scope.eventStartDate + ' ' + $scope.eventStartTime));
            // momentjs converts to a correct iso 8601 date string
            pEvent.start_date = startDate.format();
            if ($scope.eventEndDate) {
            	var endDate = moment(new Date($scope.eventEndDate + ' ' + $scope.eventEndTime));
            	// momentjs converts to a correct iso 8601 date string
            	pEvent.end_date = endDate.format();
            } else {
            	pEvent.end_date = pEvent.start_date;
            }

			$scope.loading = true;
			genService.updateObject('events', pEvent).then(function(response){
				if ($scope.debugModus) {
					$log.log(response);
				}
				if (response.data !== "") {
					$scope.loading = false;
					toaster.pop('error', null, "Event konnte nicht aktualisiert werden: " + response.data);
				} else {
					toaster.pop('success', null, "Event wurde aktualisiert");
					redirectTimeoutPromise = $timeout(function() {
						$location.path('/events');
						$scope.loading = false;
					}, 2500);
				}
				
			});
		};

		// cancel redirect promises on route change
		$scope.$on('$locationChangeStart', function(){
		    $timeout.cancel(redirectTimeoutPromise);
		});
		
	}])
	.controller('eventAddCtrl', ['genService', 'MenuService', '$scope', '$location', '$timeout', '$filter', '$log', 'toaster', function(genService, MenuService, $scope, $location, $timeout,$filter, $log, toaster) {
		// set Menu according to its Name
		MenuService.update("Veranstaltungen");

		// cancel this promise on route change
		var redirectTimeoutPromise;

		$scope.apiPath 	 = 'events';
		$scope.menuName  = 'Veranstaltung hinzufügen';
		$scope.deleteMsg = 'Löschen';

		// these values get merged to a datetime object on save
		$scope.eventStartDate;
		$scope.eventEndDate;
		$scope.eventStartTime;
		$scope.eventEndTime;

		// get event
		genService.getEmptyObject('event').then(function (response) {
			if ($scope.debugModus) {
                $log.log("events received");
                $log.log(response);
            }

            if (!response) {
            	toaster.pop('error', null, 'Uups. Der angeforderte Event exisitert nicht (mehr).');
                $location.path("/events");
            }
			$scope.event = response;
		});

		genService.getAllObjects('repeatOptions').then(function (response) {
			if ($scope.debugModus) {
				$log.log('repeatOptions received');
				$log.log(response);
			}

			$scope.allRepeatOptions = response;
		});

		genService.getAllObjects('files').then(function (response) {
			if ($scope.debugModus) {
				$log.log('files received');
				$log.log(response);
			}

			$scope.allFiles = response;
		});

		genService.getAllObjects('locations').then(function (response) {
			if ($scope.debugModus) {
				$log.log('locations received');
				$log.log(response);
			}

			$scope.allLocations = response;
		});

		$scope.deleteMsg = "Event löschen";
		$scope.ctr = 5;
		$scope.cancelled = false;

		// save changes in event
		$scope.saveEvent = function(pEvent) {
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
			}

			// Unfortunately angular supports only strings in model
            // -> parse file and repeat_option to JSON if string
            // -> http://stackoverflow.com/questions/14832405/angularjs-ng-model-converts-object-to-string
            if (angular.isString(pEvent.repeat_option)) {
                pEvent.repeat_option = JSON.parse(pEvent.repeat_option);
            }

            if (angular.isString(pEvent.file)) {
                pEvent.file = JSON.parse(pEvent.file);
            }

            // merge time and date
            var startDate = moment(new Date($scope.eventStartDate + ' ' + $scope.eventStartTime));
            // momentjs converts to a correct iso 8601 date string
            pEvent.start_date = startDate.format();
            if ($scope.eventEndDate) {
            	var endDate = moment(new Date($scope.eventEndDate + ' ' + $scope.eventEndTime));
            	// momentjs converts to a correct iso 8601 date string
            	pEvent.end_date = endDate.format();
            } else {
            	pEvent.end_date = pEvent.start_date;
            }

			$scope.loading = true;
			genService.insertObject('events', pEvent).then(function (response) {
				if ($scope.debugModus) {
					$log.log(response);
				}
				if (response.data !== "") {
					$scope.loading = false;
					toaster.pop('error', null, "Event konnte nicht aktualisiert werden: " + response.data);
				} else {
					toaster.pop('success', null, "Event wurde aktualisiert");
					redirectTimeoutPromise = $timeout(function() {
						$location.path('/events');
						$scope.loading = false;
					}, 2500);
				}
				
			});
		};

		// cancel redirect promises on route change
		$scope.$on('$locationChangeStart', function(){
		    $timeout.cancel(redirectTimeoutPromise);
		});

	}])