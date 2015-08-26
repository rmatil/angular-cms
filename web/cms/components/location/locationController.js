'use strict';

function LocationDetailController (LocationService, MapService, NavigationService, $location, $routeParams, $scope, $timeout) {
    var vm = this,
        locationId = $routeParams.id,
        reloadMapPromise;

    vm.location = {};

    activate();

    function activate() {
        vm.backgroundColorClass = NavigationService.getBackgroundColorClass($location.path());
        LocationService.getLocation(locationId)
            .then(function (data) {
                vm.location = data;

                MapService.initMap('map-canvas', vm.location.latitude, vm.location.longitude, vm.location.address);
            });
    }

    vm.saveLocation = function () {
        saveLocation();
    };

    function saveLocation() {
        LocationService.putLocation(vm.location);
    }

    $scope.$watch('vm.location.address', function () {
        var inputDiff = MapService.calcInputSpeedDiff();
        if (0 === inputDiff) {
            return;
        }

        if (reloadMapPromise) {
            // cancel timeout to reload map
            $timeout.cancel(reloadMapPromise);
        }

        if (800 < inputDiff) {
            MapService.setMapToAddress(vm.location.address)
                .then(function (ret) {
                    vm.location.latitude = ret.lat;
                    vm.location.longitude = ret.lng;
                });
            return;
        }

        reloadMapPromise = $timeout(function () {
            MapService.setMapToAddress(vm.location.address)
                .then(function (ret) {
                    vm.location.latitude = ret.lat;
                    vm.location.longitude = ret.lng;
                });
        }, 800);
    });

}

function LocationAddController (LocationService, MapService, NavigationService, $location, $scope, $timeout) {
    var vm = this,
        reloadMapPromise;

    vm.location = {};

    activate();

    function activate() {
        vm.backgroundColorClass = NavigationService.getBackgroundColorClass($location.path());
        LocationService.getEmptyLocation()
            .then(function (data) {
                vm.location = data;

                MapService.initMap('map-canvas', 50.0662735, -5.714346400000068, "Land's end");
            });
    }

    vm.saveLocation = function () {
        saveLocation();
    };

    function saveLocation() {
        LocationService.postLocation(vm.location);
    }

    $scope.$watch('vm.location.address', function () {
        var inputDiff = MapService.calcInputSpeedDiff();
        if (0 === inputDiff) {
            return;
        }

        if (reloadMapPromise) {
            // cancel timeout to reload map
            $timeout.cancel(reloadMapPromise);
        }

        if (800 < inputDiff) {
            MapService.setMapToAddress(vm.location.address)
                .then(function (ret) {
                    vm.location.latitude = ret.lat;
                    vm.location.longitude = ret.lng;
                });
            return;
        }

        reloadMapPromise = $timeout(function () {
            MapService.setMapToAddress(vm.location.address)
                .then(function (ret) {
                    vm.location.latitude = ret.lat;
                    vm.location.longitude = ret.lng;
                });
        }, 800);
    });
}

(function (angular) {
    angular
        .module('cms.controllers')
        .controller('LocationAddController', LocationAddController)
        .controller('LocationDetailController', LocationDetailController);

        LocationDetailController.$inject = ['LocationService', 'MapService', 'NavigationService', '$location', '$routeParams', '$scope', '$timeout'];
        LocationAddController.$inject = ['LocationService', 'MapService', 'NavigationService', '$location', '$scope', '$timeout'];
}(angular));