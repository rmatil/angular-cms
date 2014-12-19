/*jslint browser: true, sloppy: true, plusplus: true, nomen: true*/
/*globals angular, window */
'use strict';

/* controller for articles */
angular.module('cms.controllers')
    .controller('articleCtrl', ['genService', 'MenuService', '$scope', '$timeout', function (genService, MenuService, $scope, $timeout) {
        // set Menu according to its Name
        MenuService.update("Artikel");

        $scope.loadingArticles = true;
        genService.getAllObjects('articles').then(function (response) {
            $scope.articles = response;
            $timeout(function () {
                $scope.loadingArticles = false;
            }, 300);
        });
    }])
    .controller('articleDetailCtrl', ['genService', 'MenuService', '$scope', '$routeParams', '$timeout', '$location', 'toaster', 'localStorageService', function (genService, MenuService, $scope, $routeParams, $timeout, $location, toaster, localStorageService) {
        // set Menu according to its Name
        MenuService.update("Artikel");

        var redirectTimeoutPromise, // cancel this promise when changing route to prevent reloads to different routes
            articleContent; // local storage variable 

        $scope.apiPath   = 'articles'; // used for genService
        $scope.menuName  = 'Artikel bearbeiten';
        $scope.deleteMsg = 'Löschen';

        // init content of ckEditor and prevent empty content
        $scope.article = {};
        $scope.article.content = '';

        genService.getObjectById('articles', $routeParams.articleId).then(function (response) {
            // redirect to overview in case article doesn't exist anymore
            if (!response) {
                toaster.pop('error', null, 'Uups. Der angeforderte Artikel exisitert nicht (mehr).');
                $location.path("/articles");
            }
            $scope.article                  = response;
            // show locked message if article is locked
            $scope.$parent.isLocked         = $scope.article.is_locked_by ? true : false;
            $scope.$parent.isLockedMessage  = 'Dein Artikel wird momentan bearbeitet. Trotzdem weiterfahren?';

            if (localStorageService.isSupported) {
                if (localStorageService.get('article_content_' + $scope.article.id)) {
                    articleContent = localStorageService.get('article_content_' + $scope.article.id);

                    // only show popup if content is not the same
                    if (articleContent !== $scope.article.content) {
                        toaster.pop('info', null, 'Es gibt noch eine ungespeicherte Variante deines Artikels. Soll diese geladen werden?', 15000, null, function () {
                            $scope.article.content = articleContent;
                        });
                    }
                }

                $scope.$watch('article.content', function (value) {
                    localStorageService.set('article_content_' + $scope.article.id, value);
                });
            }
        });

        genService.getAllObjects('languages').then(function (response) {
            $scope.allLanguages = response;
        });

        genService.getAllObjects('articleCategories').then(function (response) {
            $scope.allArticleCategories = response;
        });

         // save changes in article
        $scope.saveArticle = function (pArticle) {
            if (!pArticle.title) {
                toaster.pop('warning', null, "Der Titel für den Artikel muss angegeben werden");
                return;
            }

            // Unfortunately angular supports only strings in model
            // -> parse category and language to JSON if string
            // -> http://stackoverflow.com/questions/14832405/angularjs-ng-model-converts-object-to-string
            if (angular.isString(pArticle.language)) {
                pArticle.language = JSON.parse(pArticle.language);
            }

            if (angular.isString(pArticle.category)) {
                pArticle.category = JSON.parse(pArticle.category);
            }

            $scope.loading = true;
            genService.updateObject($scope.apiPath, pArticle).then(function () {
                toaster.pop('success', null, "Artikel wurde aktualisiert");
                redirectTimeoutPromise = $timeout(function () {
                    $location.path($scope.apiPath);
                    $scope.loading = false;
                }, 2500);
            });
        };

        // cancel redirect promises on route change
        $scope.$on('$locationChangeStart', function () {
            $timeout.cancel(redirectTimeoutPromise);
        });
    }])
    .controller('articleAddCtrl', ['genService', 'MenuService', '$scope', '$location', '$timeout', 'toaster', function (genService, MenuService, $scope, $location, $timeout, toaster) {
        // set Menu according to its Name
        MenuService.update("Artikel");

        // cancel this promise when changing route
        var redirectTimeoutPromise;

        $scope.apiPath   = 'articles';
        $scope.menuName  = 'Artikel hinzufügen';

        // init content of ckEditor and prevent empty content
        $scope.article = {};
        $scope.article.content = '';

        genService.getEmptyObject('article').then(function (response) {
            // redirect to overview in case article doesn't exist
            if (!response) {
                toaster.pop('error', null, 'Uups. Es konnte kein neuer Artikel erstellt werden');
                $location.path("/articles");
            }
            $scope.article = response;
        });

        genService.getAllObjects('languages').then(function (response) {
            $scope.allLanguages = response;
        });

        genService.getAllObjects('articleCategories').then(function (response) {
            $scope.allArticleCategories = response;
        });

         // save changes in article
        $scope.saveArticle = function (pArticle) {
            if (!pArticle.title) {
                toaster.pop('warning', null, "Der Titel für den Artikel muss angegeben werden");
                return;
            }

            // Unfortunately angular supports only strings in model
            // -> parse category and language to JSON if string
            // -> http://stackoverflow.com/questions/14832405/angularjs-ng-model-converts-object-to-string
            if (angular.isString(pArticle.language)) {
                pArticle.language = JSON.parse(pArticle.language);
            }

            if (angular.isString(pArticle.category)) {
                pArticle.category = JSON.parse(pArticle.category);
            }

            $scope.loading = true;
            genService.insertObject($scope.apiPath, pArticle).then(function () {
                toaster.pop('success', null, "Artikel wurde aktualisiert");
                redirectTimeoutPromise = $timeout(function () {
                    $location.path($scope.apiPath);
                    $scope.loading = false;
                }, 2500);
            });
        };

        // cancel redirect promises on route change
        $scope.$on('$locationChangeStart', function () {
            $timeout.cancel(redirectTimeoutPromise);
        });
    }]);