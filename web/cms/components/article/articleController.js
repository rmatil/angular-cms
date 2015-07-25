'use strict';

function ArticleController(GenericApiService) {

    var vm = this,
        apiService = GenericApiService;

    vm.articles = [];

    getArticles();


    function getArticles () {
        vm.articles = apiService.getArticles();
    }
}

(function () {
    angular
        .module('cms.controllers')
        .controller('ArticleController',ArticleController);

    ArticleController.$inject = ['GenericApiService']
}());