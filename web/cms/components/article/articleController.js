'use strict';

function ArticleController(ArticleService) {

    var vm = this;

    vm.articles = [];

    activate();


    function activate () {
        ArticleService.getArticles()
            .then(function (data) {
                vm.articles = data;
                return vm.data;
            });

    }
}

function ArticleDetailController(ArticleService, LanguageService, ArticleCategoryService, NavigationService, $routeParams, $scope, $location) {
    var vm = this,
        articleId = $routeParams.id;

    vm.article = {};
    vm.article.content = ''; // init this to solve a problem with ckEditor
    vm.languages = [];
    vm.articleCategories = [];

    activate(articleId);

    vm.saveArticle = function() {
        saveArticle()
    };

    vm.deleteArticle = function() {
        deleteArticle();
    };

    function activate(articleId) {
        $scope.backgroundColorClass = NavigationService.getBackgroundColorClass($location.path());
        ArticleService.getArticle(articleId)
            .then(function (data) {
                vm.article = data;
                return data;
            });
        LanguageService.getLanguages()
            .then(function (data) {
                vm.languages = data;
                return data;
            });
        ArticleCategoryService.getArticleCategories()
            .then(function (data) {
                vm.articleCategories = data;
                return data;
            });
    }

    function saveArticle() {
        ArticleService.putArticle(vm.article);
    }

    function deleteArticle() {
        ArticleService.deleteArticle(vm.article.id);
    }
}

function ArticleAddController(ArticleService, LanguageService, ArticleCategoryService, StringService, NavigationService, $scope, $location) {
    var vm = this,
        defaultTitle = 'new Article';

    vm.article = {};
    vm.article.content = ''; // init this to solve a problem with ckEditor

    activate();

    function activate() {
        $scope.backgroundColorClass = NavigationService.getBackgroundColorClass($location.path());
        ArticleService.getEmptyArticle()
            .then(function (data) {
                vm.article = data;
                vm.article.title = defaultTitle;

                LanguageService.getLanguages()
                    .then(function (data) {
                        vm.languages = data;
                        // assign first language as default
                        if (vm.languages.length > 0) {
                            vm.article.language = vm.languages[0];
                        }
                    });
                ArticleCategoryService.getArticleCategories()
                    .then(function (data) {
                        vm.articleCategories = data;
                        // assign first page category as default
                        if (vm.articleCategories.length > 0) {
                            vm.article.category = vm.articleCategories[0];
                        }
                    });

                return vm.article;
            });
    }

    vm.saveArticle = function() {
        createArticle();
    };

    function createArticle() {
        ArticleService.postArticle(vm.article);
    }

    $scope.$watch(function() {
        return vm.article.title;
    }, function (currentVal, newVal) {
        if (undefined === currentVal ||
            '' === currentVal) {
            return;
        }

        vm.article.url_name = StringService.buildUrlString(currentVal);
    });
}

(function () {
    angular
        .module('cms.controllers')
        .controller('ArticleController', ArticleController)
        .controller('ArticleDetailController', ArticleDetailController)
        .controller('ArticleAddController', ArticleAddController);

    ArticleController.$inject = ['ArticleService'];
    ArticleDetailController.$inject = ['ArticleService', 'LanguageService', 'ArticleCategoryService', 'NavigationService', '$routeParams', '$scope', '$location'];
    ArticleAddController.$inject = ['ArticleService', 'LanguageService', 'ArticleCategoryService', 'StringService', 'NavigationService', '$scope', '$location'];

}());