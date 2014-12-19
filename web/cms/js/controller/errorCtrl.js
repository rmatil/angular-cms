'use strict';

angular.module('cms.controllers')
    .controller('errorCtrl', ['MenuService', function (MenuService) {
        // route Params are the string in the url after error/
        MenuService.update("Fehler");
    }]);