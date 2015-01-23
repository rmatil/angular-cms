/*jslint browser: true, sloppy: true, plusplus: true, nomen: true*/
/*globals angular, window */
'use strict';

// Declare app level module which depends on filters, and services
angular.module('cms', [
    'ngRoute',
    'ngCookies',
    'toaster',
    'LocalStorageModule',
    'cms.filters',
    'cms.services',
    'cms.genServices',
    'cms.directives',
    'cms.controllers',
    'angularFileUpload',
    'pickadate',
    'ngCkeditor'
]).config(['$routeProvider', '$locationProvider', '$provide', '$httpProvider', function ($routeProvider, $locationProvider, $provide, $httpProvider) {
    $routeProvider.when('/', {templateUrl: 'partials/overview.html', controller: 'overviewCtrl'});
    $routeProvider.when('/overview', {templateUrl: 'partials/overview.html', controller: 'overviewCtrl'});
    // articles
    $routeProvider.when('/articles', { templateUrl: 'partials/articles.html', controller: 'articleCtrl'});
    $routeProvider.when('/articles/:articleId', {templateUrl: 'partials/article.html', controller: 'articleDetailCtrl'});
    $routeProvider.when('/add-article', { templateUrl: 'partials/article.html', controller: 'articleAddCtrl'});
    // pages
    $routeProvider.when('/pages', {templateUrl: 'partials/pages.html', controller: 'pageCtrl'});
    $routeProvider.when('/pages/:pageId', {templateUrl: 'partials/page.html', controller: 'pageDetailCtrl'});
    $routeProvider.when('/add-page', {templateUrl: 'partials/page.html', controller: 'pageAddCtrl'});
    // events
    $routeProvider.when('/events', {templateUrl: 'partials/events.html', controller: 'eventCtrl'});
    $routeProvider.when('/events/:eventId', {templateUrl: 'partials/event.html', controller: 'eventDetailCtrl'});
    $routeProvider.when('/add-event', {templateUrl: 'partials/event.html', controller: 'eventAddCtrl'});
    $routeProvider.when('/locations/:locationId', {templateUrl: 'partials/location.html', controller: 'locationDetailCtrl'});
    $routeProvider.when('/add-location', {templateUrl: 'partials/location.html', controller: 'locationAddCtrl'});
    // media
    $routeProvider.when('/files', {templateUrl: 'partials/media.html', controller: 'mediaCtrl'});
    $routeProvider.when('/add-file', {templateUrl: 'partials/add-media.html', controller: 'mediaAddCtrl'});
    // users
    $routeProvider.when('/users', {templateUrl: 'partials/users.html', controller: 'userCtrl'});
    $routeProvider.when('/users/:userId', {templateUrl: 'partials/user.html', controller: 'userDetailCtrl'});
    $routeProvider.when('/add-user', {templateUrl: 'partials/user.html', controller: 'userAddCtrl'});
    // settings
    $routeProvider.when('/settings', {templateUrl: 'partials/settings.html', controller: 'settingCtrl'});
    $routeProvider.when('/settings/database-logging', {templateUrl: 'partials/database-logging.html', controller: 'settingLogCtrl'});
    $routeProvider.when('/settings/system-logging', {templateUrl: 'partials/system-logging.html', controller: 'settingLogCtrl'});

    // otherwise
    $routeProvider.otherwise({templateUrl: 'partials/404.html', controller: 'errorCtrl'});

    $locationProvider.html5Mode(true);

    // Intercept http calls.
    $provide.factory('authInterceptor', ['$q', '$injector', function ($q) {
        return {
            // On request success
            request: function (config) {
                // console.log(config); // Contains the data about the request before it is sent.

                // Return the config or wrap it in a promise if blank.
                return config || $q.when(config);
            },

            // On request failure
            requestError: function (rejection) {
                // console.log(rejection); // Contains the data about the error on the request.

                // Return the promise rejection.
                return $q.reject(rejection);
            },

            // On response success
            response: function (response) {
                // console.log(response); // Contains the data from the response.

                // Return the response or promise.
                return response || $q.when(response);
            },

            // On response failture
            responseError: function (rejection) {
                switch (rejection.status) {
                case 401:
                    window.location.href = 'login';
                    break;
                }

                // Return the promise rejection.
                return $q.reject(rejection);
            }
        };
    }]);

    // Add the interceptor to the $httpProvider.
    $httpProvider.interceptors.push('authInterceptor');
}]).run(['genService', '$rootScope', function (genService, $rootScope) {
    // determine debug status
    genService.getAllObjects('settings').then(function (response) {
        $rootScope.debugModus = response.debug_mode.value;
    });
}]);
