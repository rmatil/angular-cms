'use strict';

function UserController (UserService) {
    var vm = this;

    vm.users = [];

    activate();

    function activate() {
        UserService.getUsers()
            .then(function (data) {
                vm.users = data;
            });
    }

}

function UserDetailController (UserService, UserGroupService, $routeParams) {
    var vm = this,
        userId = $routeParams.id;

    vm.user = {};
    vm.userGroups = [];

    activate();

    vm.saveUser = function () {
        saveUser();
    };

    function activate() {
        UserService.getUser(userId)
            .then(function (data) {
                vm.user = data;

                UserGroupService.getUserGroups()
                    .then(function (data) {
                        vm.userGroups = data;
                    });
            });
    }

    function saveUser() {
        UserService.putUser(vm.user);
    }

}

function UserAddController (UserService, UserGroupService) {
    var vm = this;

    vm.user = {};
    vm.userGroups = [];

    vm.saveUser = function () {
        saveUser();
    };

    activate();

    function activate () {
        UserService.getEmptyUser()
            .then(function (data) {
                vm.user = data;

                UserGroupService.getUserGroups()
                    .then(function (data) {
                        vm.userGroups = data;
                    });
            });
    }

    function saveUser() {
        UserService.postUser(vm.user);
    }
}

(function (angular) {
    angular
        .module('cms.controllers')
        .controller('UserController', UserController)
        .controller('UserDetailController', UserDetailController)
        .controller('UserAddController', UserAddController);

    UserController.$inject = ['UserService'];
    UserDetailController.$inject = ['UserService', 'UserGroupService', '$routeParams'];
    UserAddController.$inject = ['UserService', 'UserGroupService'];

})(angular);