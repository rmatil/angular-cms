'use strict';

function UserService(GenericApiService, $log) {

    var USERS = 'users';

    this.getUsers = function () {
        return GenericApiService.get(USERS);
    };

    this.getUser = function (userId) {
        return GenericApiService.getObject(USERS, userId);
    };

    this.getEmptyUser = function () {
        return GenericApiService.getEmptyObject(USERS);
    };

    this.postUser = function (user) {
        return GenericApiService.post(USERS, user);
    };

    this.putUser = function (user) {
        return GenericApiService.put(USERS, user);
    };

    this.deleteUser = function (userId) {
        return GenericApiService.remove(USERS, userId);
    }

}

(function () {
    angular
        .module('cms.services')
        .service('UserService', UserService);

    UserService.$inject = [ 'GenericApiService', '$log'];
}());