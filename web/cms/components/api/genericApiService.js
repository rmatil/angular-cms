'use strict';

function GenericApiService() {

    this.getArticles = function () {
        return [{"title": "article1", "content": "Content 1"}];
    }

}

(function () {
    angular
        .module('cms.services')
        .service('GenericApiService', GenericApiService);
}());