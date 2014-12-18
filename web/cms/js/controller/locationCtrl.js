'use strict';

/* controller for locations */
angular.module('cms.controllers')
	.controller('locationDetailCtrl', ['genService','MenuService', '$scope', '$routeParams', '$timeout', '$location', '$log', '$q', 'toaster', function (genService, MenuService, $scope, $routeParams, $timeout, $location, $log, $q, toaster) {
		// set Menu according to its Name
		MenuService.update("Veranstaltungen");

		// cancel this promise on route change
		var redirectTimeoutPromise;
		// google maps Geocoder object
		var geocoder;
		// google maps Map object
		var map;
		// infowindow used to display address
		var infowindow = new google.maps.InfoWindow();
		// google maps Marker object
		var marker;
		// last time of key press
		var iLastTime = 0;
		// current time of key press
		var iTime = 0;
		// promise of timeout event for geocode
		var timeoutPromise;

		// used to display loading-gif
		$scope.addressIsLoading = false;
		// message to display while looking up the given address
		$scope.mapMessage = "Die Adresse wird gesucht...";
		// indicates whether results are found or not
		$scope.noResultsFound = false;
		// time difference between key press'
		$scope.timeDifference = 0;

		$scope.apiPath 	 = 'locations';
		$scope.menuName  = 'Veranstaltungsort bearbeiten';
		$scope.deleteMsg = 'Löschen';

		genService.getObjectById('locations', $routeParams.locationId).then(function (response) {
			if ($scope.debugModus) {
                $log.log("locations received");
                $log.log(response);
            }

            if (!response) {
            	toaster.pop('error', null, 'Uups. Der angeforderte Veranstaltungsort exisitert nicht (mehr).');
                $location.path("/locations");
            }
			$scope.location = response;

			$scope.initializeMap();
		});

		$scope.saveLocation = function (pLocation) {
			if (!pLocation.name) {
				toaster.pop('warning', null, "Der Veranstaltungsort muss angegeben werden");
				return;
			}

			$scope.loading = true;
			genService.updateObject('locations', pLocation).then(function (response) {
				if ($scope.debugModus) {
					$log.log(response);
				}
				if (response.data !== "") {
					$scope.loading = false;
					toaster.pop('error', null, "Der Veranstaltungsort konnte nicht aktualisiert werden: " + response.data);
				} else {
					toaster.pop('success', null, "Veranstaltungsort wurde aktualisiert");
					redirectTimeoutPromise = $timeout(function () {
						$location.path('/locations');
						$scope.loading = false;
					}, 2500);
				}
			});
		};

		// cancel redirect promises on route change
		$scope.$on('$locationChangeStart', function (){
		    $timeout.cancel(redirectTimeoutPromise);
		});

		// initializes the map with the coordinates of chur
		$scope.initializeMap = function () {
		  geocoder = new google.maps.Geocoder();
		  var latlng = new google.maps.LatLng($scope.location.latitude, $scope.location.longitude);
		  var mapOptions = {
			zoom: 16,
			center: latlng,
			mapTypeId: 'roadmap',
			panControl: false,
			zoomControl: true,
			scaleControl: false,
			streetViewControl: false, 
		  }
		  // necessary to set height of map-canvas -> see style.css
		  map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
 		  marker = new google.maps.Marker({
							position: latlng,
							map: map
						});
		  infowindow.setContent($scope.location.address);
		  infowindow.open(map, marker);		

		}

		// computes the time between two keypress events
		$scope.checkTypeSpeed = function () {
			iTime = new Date().getTime();
			if (iLastTime != 0) {
				$scope.timeDifference = iTime - iLastTime;
				if ($scope.debugModus) {
					$log.log($scope.timeDifference);
				}
			}
			iLastTime = iTime;
		}

		// check time difference to fire geocode()
		$scope.$watch('timeDifference', function () {
			$scope.noResultsFound = false;
			
			// don't geocode when timeDifference is zero
			if (!$scope.timeDifference) {
				return;
			}
			$scope.addressIsLoading = true;

			// cancel previous timeout
			if (timeoutPromise) {
				$timeout.cancel(timeoutPromise);
			}

			if ($scope.timeDifference > 800) {
				$scope.geocode();
			} else {
				timeoutPromise = $timeout($scope.geocode, 800);
			}
		});

		// get latitude and longitude from the given address (user input)
		$scope.geocode = function () {
			var $defer = $q.defer();
			var parsedResult;
			geocoder.geocode({ address : $scope.location.address }, function (result, status) {

				if (status === google.maps.GeocoderStatus.OK) {
					parsedResult = {
						lat: result[0].geometry.location.lat(),
						lng: result[0].geometry.location.lng(),
						formattedAddress: result[0].formatted_address
					};
					$defer.resolve();
				} else {
					$scope.noResultsFound = true;
					$scope.errorMsg = "Keine Resultate gefunden";
					$scope.addressIsLoading = false;
					
					$scope.location.longitude = 0;
					$scope.location.latitude = 0;

					if ($scope.debugModus) {
						$log.log(err.message);
					}
				}
				$defer.promise.then(function () {
					$scope.codeLatLng(parsedResult['lat'], parsedResult['lng']);
				});
			});
		}

		// display place for given lat, lng
		$scope.codeLatLng = function (lat, lng) {
			// get a promise
			var $defer = $q.defer();
			var latlng = new google.maps.LatLng(lat, lng);
			geocoder.geocode({'latLng': latlng}, function (results, status) {
				if (status == google.maps.GeocoderStatus.OK) {
					if (results[1]) {
						map.setZoom(16);
						map.setCenter(latlng);
						marker = new google.maps.Marker({
							position: latlng,
							map: map
						});
						infowindow.setContent(results[1].formatted_address);
						infowindow.open(map, marker);
						// map has updated -> resolve promise
						$defer.resolve();
					} else {
						$scope.mapMessage = "Keine Resultate gefunden";
					}
				} else {
					$scope.mapMessage = "Ein Problem ist aufgetreten. Bitte versuche es nocheinmal";
					console.error('Geocoder failed due to: ' + status);
				}
			});
			$defer.promise.then(function () {
				$scope.location.longitude = lng;
				$scope.location.latitude = lat;
				$scope.addressIsLoading = false;
			});
		}
		
	}])
	.controller('locationAddCtrl', ['genService','MenuService', '$scope', '$location', '$timeout', '$filter', '$log', '$q', 'toaster', function (genService, MenuService, $scope, $location, $timeout,$filter, $log, $q, toaster) {
		// set Menu according to its Name
		MenuService.update("Veranstaltungen");

		// cancel this promise on route change
		var redirectTimeoutPromise;

		$scope.apiPath 	 = 'locations';
		$scope.menuName  = 'Veranstaltungsort hinzufügen';

		genService.getEmptyObject('location').then(function (response) {
			$scope.initializeMap();

			$scope.location = response;
		});

		$scope.saveLocation = function (pLocation) {
			if (!pLocation.name) {
				toaster.pop('warning', "Ort", "Der Ort muss angegeben werden");
				return;
			}

			$scope.loading = true;
			genService.insertObject('locations', pLocation).then(function (response) {
				if ($scope.debugModus) {
					$log.log(response);
				}
				if (response.data !== "") {
					$scope.loading = false;
					toaster.pop('error', null, "Der Ort konnte nicht hinzugefügt werden: " + response.data);
				} else {
					toaster.pop('success', null, "Ort wurde hinzugefügt");
					redirectTimeoutPromise = $timeout(function () {
						$location.path('/events');
						$scope.loading = false;
					}, 2500);
				}
			});
		}

		// cancel redirect promises on route change
		$scope.$on('$locationChangeStart', function () {
		    $timeout.cancel(redirectTimeoutPromise);
		});
		
		// used to display loading-gif
		$scope.addressIsLoading = false;
		// message to display while looking up the given address
		$scope.mapMessage = "Die Adresse wird gesucht...";
		// indicates whether results are found or not
		$scope.noResultsFound = false;
		// time difference between key press'
		$scope.timeDifference = 0;
		// google maps Geocoder object
		var geocoder;
		// google maps Map object
		var map;
		// infowindow used to display address
		var infowindow = new google.maps.InfoWindow();
		// google maps Marker object
		var marker;
		// last time of key press
		var iLastTime = 0;
		// current time of key press
		var iTime = 0;
		// promise of timeout event for geocode
		var timeoutPromise;

		// initializes the map with the coordinates of chur
		$scope.initializeMap = function () {
		  geocoder = new google.maps.Geocoder();
		  var latlng = new google.maps.LatLng(46.85726210000001,9.526730499999985);
		  var mapOptions = {
			zoom: 10,
			center: latlng,
			mapTypeId: 'roadmap',
			panControl: false,
			zoomControl: true,
			scaleControl: false,
			streetViewControl: false, 
		  }
		  // necessary to set height of map-canvas -> see style.css
		  map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
		}

		// computes the time between two keypress events
		$scope.checkTypeSpeed = function () {
			iTime = new Date().getTime();
			if (iLastTime != 0) {
				$scope.timeDifference = iTime - iLastTime;
				if ($scope.debugModus) {
					$log.log($scope.timeDifference);
				}
			}
			iLastTime = iTime;
		}

		// check time difference to fire geocode()
		$scope.$watch('timeDifference', function () {
			$scope.noResultsFound = false;
			
			// don't geocode when timeDifference is zero
			if (!$scope.timeDifference) {
				return;
			}
			$scope.addressIsLoading = true;

			// cancel previous timeout
			if (timeoutPromise) {
				$timeout.cancel(timeoutPromise);
			}

			if ($scope.timeDifference > 800) {
				$scope.geocode();
			} else {
				timeoutPromise = $timeout($scope.geocode, 800);
			}
		});

		// get latitude and longitude from the given address (user input)
		$scope.geocode = function () {
			var $defer = $q.defer();
			var parsedResult;
			geocoder.geocode({ address : $scope.location.address }, function (result, status) {

				if (status === google.maps.GeocoderStatus.OK) {
					parsedResult = {
						lat: result[0].geometry.location.lat(),
						lng: result[0].geometry.location.lng(),
						formattedAddress: result[0].formatted_address
					};
					$defer.resolve();
				} else {
					$scope.noResultsFound = true;
					$scope.errorMsg = "Keine Resultate gefunden";
					$scope.addressIsLoading = false;
					
					$scope.location.longitude = 0;
					$scope.location.latitude = 0;

					if ($scope.debugModus) {
						$log.log(err.message);
					}
				}
				$defer.promise.then(function () {
					$scope.codeLatLng(parsedResult['lat'], parsedResult['lng']);
				});
			});
		}

		// display place for given lat, lng
		$scope.codeLatLng = function (lat, lng) {
			// get a promise
			var $defer = $q.defer();
			var latlng = new google.maps.LatLng(lat, lng);
			geocoder.geocode({'latLng': latlng}, function (results, status) {
				if (status == google.maps.GeocoderStatus.OK) {
					if (results[1]) {
						map.setZoom(16);
						map.setCenter(latlng);
						marker = new google.maps.Marker({
							position: latlng,
							map: map
						});
						infowindow.setContent(results[1].formatted_address);
						infowindow.open(map, marker);
						// map has updated -> resolve promise
						$defer.resolve();
					} else {
						$scope.mapMessage = "Keine Resultate gefunden";
					}
				} else {
					$scope.mapMessage = "Ein Problem ist aufgetreten. Bitte versuche es nocheinmal";
					console.error('Geocoder failed due to: ' + status);
				}
			});
			$defer.promise.then(function () {
				$scope.location.longitude = lng;
				$scope.location.latitude = lat;
				$scope.addressIsLoading = false;
			});
		}
		
	}])
