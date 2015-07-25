'use strict';


angular.module('cms.directives')
    .directive('cmsNav', ['$location', function ($location) {
        return {
            restrict: 'E',
            isolate: true,
            link: function ($scope, $elm, $attrs) {},
            scope: true,
            templateUrl: "components/navigation/navigation.html"
        };
    }])
    .directive('contentNav', ['NavigationService', '$location', function (NavigationService, $location) {
        return {
            restrict: 'E',
            isolate: true,
            link: function ($scope, $elm, $attrs) {
                var submenuProperties = NavigationService.getSubnavigation($location.path());
                if (null !== submenuProperties) {
                    $scope.subNavigation = submenuProperties;
                }
            },
            scope: true,
            templateUrl: "components/navigation/content-navigation.html"
        };
    }]);