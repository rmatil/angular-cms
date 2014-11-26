'use strict';

angular.module('cms.controllers')
	.controller('errorCtrl', ['MenuService', '$routeParams', function(MenuService, $routeParams) {
		// route Params are the string in the url after error/
		MenuService.update("Fehler");
	}]);