'use strict';

function LocationDetailController (LocationService, MapService, $routeParams, $scope, $timeout) {
    var vm = this,
        locationId = $routeParams.id,
        reloadMapPromise;

    vm.location = {};

    activate();

    function activate() {
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

function LocationAddController (LocationService) {

}

(function (angular) {
    angular
        .module('cms.controllers')
        .controller('LocationAddController', LocationAddController)
        .controller('LocationDetailController', LocationDetailController);

        LocationDetailController.$inject = ['LocationService', 'MapService', '$routeParams', '$scope', '$timeout'];
        LocationAddController.$inject = ['LocationService'];
}(angular));