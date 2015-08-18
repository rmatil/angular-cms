'use strict';

function LanguageService(GenericApiService, $log) {

    var LANGUAGES = 'languages';

    this.getLanguages = function () {
        return GenericApiService.get(LANGUAGES);
    };

    this.getLanguage = function (languageId) {
        return GenericApiService.getObject(LANGUAGES, languageId);
    };

    this.getEmptyLanguage = function () {
        return GenericApiService.getEmptyObject(LANGUAGES);
    }

    this.postLanguage = function (language) {
        return GenericApiService.post(LANGUAGES, language);
    };

    this.putLanguage = function (language) {
        return GenericApiService.put(LANGUAGES, language);
    };

    this.deleteLanguage = function (languageId) {
        return GenericApiService.remove(LANGUAGES, languageId);
    }

}

(function () {
    angular
        .module('cms.services')
        .service('LanguageService', LanguageService);

    LanguageService.$inject = [ 'GenericApiService', '$log'];
}());