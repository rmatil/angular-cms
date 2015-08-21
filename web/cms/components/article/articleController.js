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

function ArticleDetailController(ArticleService, LanguageService, ArticleCategoryService, $routeParams) {
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

function ArticleAddController(ArticleService, LanguageService, ArticleCategoryService) {
    var vm = this;

    vm.article = {};
    vm.article.content = ''; // init this to solve a problem with ckEditor

    activate();

    function activate() {
        ArticleService.getEmptyArticle()
            .then(function (data) {
                vm.article = data;
                return vm.article;
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

    vm.saveArticle = function() {
        createArticle();
    };

    function createArticle() {
        ArticleService.postArticle(vm.article);
    }
}

(function () {
    angular
        .module('cms.controllers')
        .controller('ArticleController', ArticleController)
        .controller('ArticleDetailController', ArticleDetailController)
        .controller('ArticleAddController', ArticleAddController);

    ArticleController.$inject = ['ArticleService'];
    ArticleDetailController.$inject = ['ArticleService', 'LanguageService', 'ArticleCategoryService', '$routeParams'];
    ArticleAddController.$inject = ['ArticleService', 'LanguageService', 'ArticleCategoryService'];

}());