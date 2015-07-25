'use strict';

(function(angular) {
    angular
        .module('cms', [
            'ngRoute',
            'ngCookies',
            'toaster',
            'LocalStorageModule',
            //'cms.filters',
            'cms.services',
            //'cms.genServices',
            'cms.directives',
            'cms.controllers',
            'angularFileUpload',
            'pickadate',
            'ngCkeditor',
            'angular-momentjs'
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

                $routeProvider.when('/article/articles', {
                    templateUrl: 'components/article/articles.html',
                    controller: 'ArticleController',
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