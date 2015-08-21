'use strict';

function PageController(PageService) {
    var vm = this;

    vm.pages = [];

    activate();

    function activate() {
        PageService.getPages()
            .then(function (data) {
                vm.pages = data;
                return vm.pages;
            });
    }
}

function PageDetailController(PageService, LanguageService, PageCategoryService, ArticleService, ArrayService, $routeParams) {
    var vm = this,
        pageId = $routeParams.id;

    vm.page = {};
    vm.articles = [];
    vm.languages = [];
    vm.pageCategories = [];

    activate(pageId);

    vm.savePage = function() {
        savePage()
    };

    vm.deletePage = function() {
        deletePage();
    };

    vm.addArticleToPage = function (articleId) {
        addArticleToPage(articleId);
    };

    vm.removeArticleFromPage = function (articleId) {
        removeArticleFromPage(articleId);
    };

    function activate(pageId) {
        PageService.getPage(pageId)
            .then(function (data) {
                vm.page = data;
                // set unselected articles
                ArticleService.getArticles()
                    .then(function (data) {
                        vm.articles = ArrayService.arrayDiff(data, vm.page.articles);
                    });

                return data;
            });
        LanguageService.getLanguages()
            .then(function (data) {
                vm.languages = data;
                return data;
            });
        PageCategoryService.getPageCategories()
            .then(function (data) {
                vm.pageCategories = data;
                return data;
            });
    }

    function savePage() {
        PageService.putPage(vm.page);
    }

    function deletePage() {
        PageService.deletePage(vm.page.id);
    }

    /**
     * Adds the article with the given id to the current page
     *
     * @param articleId
     */
    function addArticleToPage(articleId) {
        var idx = ArrayService.arrayFind(vm.articles, articleId);
        if (idx > -1) {
            // remove element from unselected articles
            var removed = vm.articles.splice(idx, 1);
            vm.page.articles.push(removed[0]);
        }
    }

    /**
     * Removes the article with the given id from the current page
     * @param articleId
     */
    function removeArticleFromPage(articleId) {
        var idx = ArrayService.arrayFind(vm.page.articles, articleId);
        if (idx > -1) {
            // add element back to unselected articles
            var removed = vm.page.articles.splice(idx, 1);
            vm.articles.push(removed[0]);
        }
    }
}

function PageAddController(PageService, LanguageService, PageCategoryService, ArticleService, ArrayService, $scope) {
    var vm = this,
        defaultTitle = 'new Page';

    vm.page = {};
    vm.languages = [];
    vm.pageCategories = [];

    activate();

    function activate() {
        PageService.getEmptyPage()
            .then(function (data) {
                vm.page = data;
                vm.page.title = defaultTitle;

                // get available articles
                ArticleService.getArticles()
                    .then(function (data) {
                        vm.articles = data;
                    });

                LanguageService.getLanguages()
                    .then(function (data) {
                        vm.languages = data;
                        // assign first language as default
                        if (vm.languages.length > 0) {
                            vm.page.language = vm.languages[0];
                        }
                    });

                PageCategoryService.getPageCategories()
                    .then(function (data) {
                        vm.pageCategories = data;
                        // assign first page category as default
                        if (vm.pageCategories.length > 0) {
                            vm.page.category = vm.pageCategories[0];
                        }
                    });

                return data;
            });
    }

    $scope.$watch(function() {
        return vm.page.title;
    }, function (currentVal, newVal) {
        if (undefined === currentVal ||
            '' === currentVal) {
            return;
        }

        vm.page.url_name = buildUrlName(currentVal);
    });

    vm.savePage = function () {
        createPage();
    };

    vm.addArticleToPage = function (articleId) {
        addArticleToPage(articleId);
    };

    vm.removeArticleFromPage = function (articleId) {
        removeArticleFromPage(articleId);
    };

    function createPage() {
        PageService.postPage(vm.page);
    }

    /**
     * Adds the article with the given id to the current page
     *
     * @param articleId
     */
    function addArticleToPage(articleId) {
        var idx = ArrayService.arrayFind(vm.articles, articleId);
        if (idx > -1) {
            // remove element from unselected articles
            var removed = vm.articles.splice(idx, 1);
            vm.page.articles.push(removed[0]);
        }
    }

    /**
     * Removes the article with the given id from the current page
     * @param articleId
     */
    function removeArticleFromPage(articleId) {
        var idx = ArrayService.arrayFind(vm.page.articles, articleId);
        if (idx > -1) {
            // add element back to unselected articles
            var removed = vm.page.articles.splice(idx, 1);
            vm.articles.push(removed[0]);
        }
    }

    var buildUrlName = function (val) {
        if (!val) {
            return '';
        }

        return val.replace(/[\x7f-\xff]/g, '').replace(/[ \t\r\n\v\f]/g, '-').toLowerCase();
    };
}


(function () {
    angular
        .module('cms.controllers')
        .controller('PageController', PageController)
        .controller('PageDetailController', PageDetailController)
        .controller('PageAddController', PageAddController);

    PageController.$inject = ['PageService'];
    PageDetailController.$inject = ['PageService', 'LanguageService', 'PageCategoryService', 'ArticleService', 'ArrayService', '$routeParams'];
    PageAddController.$inject = ['PageService', 'LanguageService', 'PageCategoryService', 'ArticleService', 'ArrayService', '$scope'];
})();