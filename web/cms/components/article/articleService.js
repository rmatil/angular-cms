'use strict';

function ArticleService(GenericApiService, $log) {

    var ARTICLES = 'articles';

    this.getArticles = function () {
        return GenericApiService.get(ARTICLES);
    };

    this.getArticle = function (articleId) {
        return GenericApiService.getObject(ARTICLES, articleId);
    };

    this.getEmptyArticle = function () {
        return GenericApiService.getEmptyObject(ARTICLES);
    }

    this.postArticle = function (article) {
        return GenericApiService.post(ARTICLES, article);
    };

    this.putArticle = function (article) {
        return GenericApiService.put(ARTICLES, article);
    };

    this.deleteArticle = function (articleId) {
        return GenericApiService.remove(ARTICLES, articleId);
    }

}

(function () {
    angular
        .module('cms.services')
        .service('ArticleService', ArticleService);

    ArticleService.$inject = [ 'GenericApiService', '$log'];
}());