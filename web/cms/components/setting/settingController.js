'use strict';

function SettingController(SettingService, NavigationService, $location) {
    var vm = this;

    vm.settings = [];

    activate();

    function activate() {
        vm.backgroundColorClass = NavigationService.getBackgroundColorClass($location.path());
        SettingService.getSettings()
            .then(function (data) {
                vm.settings = data;
            });
    }

}

(function (angular) {
    angular
        .module('cms.controllers')
        .controller('SettingController', SettingController);

    SettingController.$inject = ['SettingService', 'NavigationService', '$location'];

})(angular);