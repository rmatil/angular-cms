'use strict';

function SettingController(SettingService) {
    var vm = this;

    vm.settings = [];

    activate();

    function activate() {
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

    SettingController.$inject = ['SettingService'];

})(angular);