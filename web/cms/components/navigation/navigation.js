'use strict';


angular.module('cms.directives')
    .directive('currentSite', ['NavigationService', '$location', function (NavigationService, $location) {
        return {
            restrict: 'E',
            isolated: true,
            scope: true,
            link: function ($scope, $elm, $attrs) {

                updateActivePoint();

                $scope.$on('$locationChangeStart', function(event) {
                    updateActivePoint();
                });

                function updateActivePoint () {
                    $scope.backgroundColorClass = NavigationService.getBackgroundColorClass($location.path());
                    $scope.pageName = NavigationService.getPageName($location.path());
                }
            },
            template: '<div ng-class="backgroundColorClass">{{ pageName }}</div>'
        };
    }])
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
                var submenuProperties = NavigationService.getSubNavigation($location.path(), true);
                if (null !== submenuProperties) {
                    $scope.subNavigation = submenuProperties;
                }
            },
            scope: true,
            templateUrl: "components/navigation/content-navigation.html"
        };
    }])
    .directive('pageName', ['NavigationService', '$location', function (NavigationService, $location) {
        return {
            restrict: 'E',
            isolate: true,
            link: function ($scope, $elm, $attrs) {
                $scope.pageName = NavigationService.getPageName($location.path());
            },
            scope: true,
            template: '{{ pageName }}'
        };
    }]);