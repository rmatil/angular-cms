'use strict';

function FileService(GenericApiService, $log) {

    var FILES = 'files';

    this.getFiles = function () {
        return GenericApiService.get(FILES);
    };

    this.getFile = function (locationId) {
        return GenericApiService.getObject(FILES, locationId);
    };

    this.postFile = function (location) {
        return GenericApiService.post(FILES, location);
    };

    this.deleteFile = function (locationId) {
        return GenericApiService.remove(FILES, locationId);
    }

}

(function () {
    angular
        .module('cms.services')
        .service('FileService', FileService);

    FileService.$inject = [ 'GenericApiService', '$log'];
}());