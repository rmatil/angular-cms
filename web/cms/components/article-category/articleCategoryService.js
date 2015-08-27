'use strict';

function ArticleCategoryService(GenericApiService, $log) {

    var ARTICLE_CATEGORY = 'article-categories';

    this.getArticleCategories = function () {
        return GenericApiService.get(ARTICLE_CATEGORY);
    };

    this.getArticleCategory = function (articleCategoryId) {
        return GenericApiService.getObject(ARTICLE_CATEGORY, articleCategoryId);
    };

    this.getEmptyArticleCategory = function () {
        return GenericApiService.getEmptyObject(ARTICLE_CATEGORY);
    }

    this.postArticleCategory = function (articleCategory) {
        return GenericApiService.post(ARTICLE_CATEGORY, articleCategory);
    };

    this.putArticleCategory = function (articleCategory) {
        return GenericApiService.put(ARTICLE_CATEGORY, articleCategory);
    };

    this.deleteArticleCategory = function (articleCategoryId) {
        return GenericApiService.remove(ARTICLE_CATEGORY, articleCategoryId);
    }

}

(function () {
    angular
        .module('cms.services')
        .service('ArticleCategoryService', ArticleCategoryService);

    ArticleCategoryService.$inject = [ 'GenericApiService', '$log'];
}());