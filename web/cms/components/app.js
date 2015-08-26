'use strict';

(function(angular) {
    angular
        .module('cms', [
            'ngRoute',
            'ngCookies',
            'ngFileUpload',
            'toaster',
            'LocalStorageModule',
            //'cms.filters',
            'cms.services',
            //'cms.genServices',
            'cms.directives',
            'cms.controllers',
            'ngCkeditor',
            'angular-momentjs',
        ])
        .config([
            '$routeProvider',
            '$locationProvider',
            '$provide',
            '$httpProvider',
            function ($routeProvider, $locationProvider, $provide, $httpProvider) {

                console.log("App Configuration");

                $routeProvider.when('/', {
                    templateUrl: 'components/dashboard/dashboard.html',
                    controller: 'DashboardController'
                });

                $routeProvider.when('/articles/list', {
                    templateUrl: 'components/article/articles.html',
                    controller: 'ArticleController',
                    controllerAs: 'vm'
                });

                $routeProvider.when('/articles/article/:id', {
                    templateUrl: 'components/article/article.html',
                    controller: 'ArticleDetailController',
                    controllerAs: 'vm'
                });

                $routeProvider.when('/articles/add', {
                    templateUrl: 'components/article/article.html',
                    controller: 'ArticleAddController',
                    controllerAs: 'vm'
                });

                $routeProvider.when('/pages/list', {
                    templateUrl: 'components/page/pages.html',
                    controller: 'PageController',
                    controllerAs: 'vm'
                });

                $routeProvider.when('/pages/page/:id', {
                    templateUrl: 'components/page/page.html',
                    controller: 'PageDetailController',
                    controllerAs: 'vm'
                });

                $routeProvider.when('/pages/add', {
                    templateUrl: 'components/page/page.html',
                    controller: 'PageAddController',
                    controllerAs: 'vm'
                });

                $routeProvider.when('/events/list', {
                    templateUrl: 'components/event-dashboard/event-dashboard',
                    controller: 'EventDashboardController',
                    controllerAs: 'vm'
                });

                $routeProvider.when('/events/event/:id', {
                    templateUrl: 'components/event/event.html',
                    controller: 'EventDetailController',
                    controllerAs: 'vm'
                });

                $routeProvider.when('/events/add', {
                    templateUrl: 'components/event/event.html',
                    controller: 'EventAddController',
                    controllerAs: 'vm'
                });

                $routeProvider.when('/locations/location/:id', {
                    templateUrl: 'components/location/location.html',
                    controller: 'LocationDetailController',
                    controllerAs: 'vm'
                });

                $routeProvider.when('/locations/add', {
                    templateUrl: 'components/location/location.html',
                    controller: 'LocationAddController',
                    controllerAs: 'vm'
                });

                $routeProvider.when('/files/list', {
                    templateUrl: 'components/media/media.html',
                    controller: 'MediaController',
                    controllerAs: 'vm'
                });

                $routeProvider.when('/files/add', {
                    templateUrl: 'components/media/add-media.html',
                    controller: 'MediaAddController',
                    controllerAs: 'vm'
                });

                $routeProvider.when('/files/file/:id', {
                    templateUrl: 'components/media/media-detail.html',
                    controller: 'MediaDetailController',
                    controllerAs: 'vm'
                });

                $routeProvider.when('/users/list', {
                    templateUrl: 'components/user/users.html',
                    controller: 'UserController',
                    controllerAs: 'vm'
                });

                $routeProvider.when('/users/add', {
                    templateUrl: 'components/user/user.html',
                    controller: 'UserAddController',
                    controllerAs: 'vm'
                });

                $routeProvider.when('/users/user/:id', {
                    templateUrl: 'components/user/user.html',
                    controller: 'UserDetailController',
                    controllerAs: 'vm'
                });

                // use HTML5 history API
                $locationProvider.html5Mode(true);

        }])
        .run([ function () {
            console.log("run app");
        }]);


    console.log("app.config.js");

})(angular);