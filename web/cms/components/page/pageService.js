'use strict';

function PageService(GenericApiService, $log) {

    var PAGES = 'pages';

    this.getPages = function () {
        return GenericApiService.get(PAGES);
    };

    this.getPage = function (articleId) {
        return GenericApiService.getObject(PAGES, articleId);
    };

    this.getEmptyPage = function () {
        return GenericApiService.getEmptyObject(PAGES);
    };

    this.postPage = function (article) {
        return GenericApiService.post(PAGES, article);
    };

    this.putPage = function (article) {
        return GenericApiService.put(PAGES, article);
    };

    this.deletePage = function (articleId) {
        return GenericApiService.remove(PAGES, articleId);
    }

}

(function () {
    angular
        .module('cms.services')
        .service('PageService', PageService);

    PageService.$inject = [ 'GenericApiService', '$log'];
}());