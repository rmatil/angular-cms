'use strict';

function PageController(PageService) {
    var vm = this;

    vm.pages = [];

    activate();

    function activate() {
        PageService.getPages()
            .then(function (data) {
                vm.pages = data;
                return vm.pages;
            });
    }
}


(function () {
    angular
        .module('cms.controllers')
        .controller('PageController', PageController);

    PageController.$inject = ['PageService'];

})();