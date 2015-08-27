'use strict';

function LocationService(GenericApiService, $log) {

    var LOCATIONS = 'locations';

    this.getLocations = function () {
        return GenericApiService.get(LOCATIONS);
    };

    this.getLocation = function (locationId) {
        return GenericApiService.getObject(LOCATIONS, locationId);
    };

    this.getEmptyLocation = function () {
        return GenericApiService.getEmptyObject(LOCATIONS);
    }

    this.postLocation = function (location) {
        return GenericApiService.post(LOCATIONS, location);
    };

    this.putLocation = function (location) {
        return GenericApiService.put(LOCATIONS, location);
    };

    this.deleteLocation = function (locationId) {
        return GenericApiService.remove(LOCATIONS, locationId);
    }

}

(function () {
    angular
        .module('cms.services')
        .service('LocationService', LocationService);

    LocationService.$inject = [ 'GenericApiService', '$log'];
}());