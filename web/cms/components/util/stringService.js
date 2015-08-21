'use strict';

function StringService() {

    /**
     * Builds an Url name of the given string, i.e.
     * strips all whitespace chars and replaces them with dashes.
     * Finally, it converts the given string to lower case.
     *
     * @param val The string to strip whitespaces of
     * @returns {string} The cleaned string
     */
    this.buildUrlString = function (val) {
        if (!val) {
            return '';
        }

        return val.replace(/[\x7f-\xff]/g, '').replace(/[ \t\r\n\v\f]/g, '-').toLowerCase();
    };
}

(function () {
    angular
        .module('cms.services')
        .service('StringService', StringService);
}());

