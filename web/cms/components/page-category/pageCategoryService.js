'use strict';

function PageCategoryService(GenericApiService, $log) {

    var PAGE_CATEGORY = 'page-categories';

    this.getPageCategories = function () {
        return GenericApiService.get(PAGE_CATEGORY);
    };

    this.getPageCategory = function (pageCategoryId) {
        return GenericApiService.getObject(PAGE_CATEGORY, pageCategoryId);
    };

    this.getEmptyPageCategory = function () {
        return GenericApiService.getEmptyObject(PAGE_CATEGORY);
    };

    this.postPageCategory = function (pageCategory) {
        return GenericApiService.post(PAGE_CATEGORY, pageCategory);
    };

    this.putPageCategory = function (pageCategory) {
        return GenericApiService.put(PAGE_CATEGORY, pageCategory);
    };

    this.deletePageCategory = function (pageCategoryId) {
        return GenericApiService.remove(PAGE_CATEGORY, pageCategoryId);
    };

}

(function () {
    angular
        .module('cms.services')
        .service('PageCategoryService', PageCategoryService);

    PageCategoryService.$inject = [ 'GenericApiService', '$log'];
}());