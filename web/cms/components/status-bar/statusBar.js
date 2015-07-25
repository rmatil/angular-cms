'use strict';


angular.module('cms.directives')
    .directive('statusBar', ['$location', function ($location) {
        return {
            restrict: 'E',
            isolate: true,
            link: function ($scope, $elm, $attrs) {},
            scope: true,
            templateUrl: "components/status-bar/statusBar.html"
        };
    }]);