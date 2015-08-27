'use strict';

function GenericApiService($http, $log) {

    var apiEndPoint = '/api';

    var printError = function (error) {
        // TODO: make a generic version of this
        // TODO: set log level for information to debug in $logProvider
        $log.error('[' + error.config.method + '] ' + error.status + ' "' + error.statusText + '" "' + error.config.url + '"');
    };

    this.get = function (objectIdentifier) {
        return $http.get(apiEndPoint + '/' + objectIdentifier)
            .then(function (response) {
                return response.data;
            }).catch(function (error) {
                printError(error);
            });
    };

    this.getObject = function (objectIdentifier, objectId) {
        return $http.get(apiEndPoint + '/' + objectIdentifier + '/' + objectId)
            .then(function (response) {
                return response.data;
            }).catch(function (error) {
                printError(error);
            });
    };

    this.getEmptyObject = function (objectIdentifier) {
        return $http.get(apiEndPoint + '/empty/' + objectIdentifier )
            .then(function (response) {
                return response.data;
            }).catch(function (error) {
                printError(error);
            });
    };

    this.post = function (objectIdentifier, object) {
        return $http({
            headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'},
            url: apiEndPoint + '/' + objectIdentifier,
            method: 'POST',
            data: object
        }).then(function (response) {
            return response.data;
        }).catch(function (error) {
            printError(error);
        });
    };

    this.put = function (objectIdentifier, object) {
        return $http({
            headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'},
            url: apiEndPoint + '/' + objectIdentifier + '/' + object.id,
            method: 'PUT',
            data: object
        }).then(function (response) {
            return response.data;
        }).catch(function (error) {
            printError(error);
        });
    }

    this.remove = function (objectIdentifier, objectId) {
        return $http.delete(apiEndPoint + '/' + objectIdentifier + '/' + objectId)
            .then(function (response) {
                return response.data;
            }).catch(function (error) {
                printError(error);
            });
    }

}

(function () {
    angular
        .module('cms.services')
        .service('GenericApiService', GenericApiService);

        GenericApiService.$inject = [ '$http', '$log'];
}());