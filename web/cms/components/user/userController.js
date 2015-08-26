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

function UserDetailController (UserService, UserGroupService, NavigationService, $location, $routeParams) {
    var vm = this,
        userId = $routeParams.id;

    vm.user = {};
    vm.userGroups = [];

    activate();

    vm.saveUser = function () {
        saveUser();
    };

    function activate() {
        vm.backgroundColorClass = NavigationService.getBackgroundColorClass($location.path());
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

function UserAddController (UserService, UserGroupService, NavigationService, $location) {
    var vm = this;

    vm.user = {};
    vm.userGroups = [];

    vm.saveUser = function () {
        saveUser();
    };

    activate();

    function activate () {
        vm.backgroundColorClass = NavigationService.getBackgroundColorClass($location.path());
        UserService.getEmptyUser()
            .then(function (data) {
                vm.user = data;
                vm.user.registration_date = moment().format('YYYY-MM-DDTHH:mm:ssZZ');

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
    UserDetailController.$inject = ['UserService', 'UserGroupService', 'NavigationService', '$location', '$routeParams'];
    UserAddController.$inject = ['UserService', 'UserGroupService', 'NavigationService', '$location'];

})(angular);