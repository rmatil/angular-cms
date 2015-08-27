'use strict';

function ArrayService() {
    /**
     * Returns the elements in the first array which are not present in the second one.
     *
     * @param first The first array
     * @param second The second array
     * @returns {article} All articles which are present in the first but not in the second array
     */
    this.arrayDiff = function (first, second) {
        return first.filter(function (valInFirst) {
            return second.filter(function (valInSecond) {
                    return valInSecond.id === valInFirst.id
                }).length === 0;
        });
    };;

    /**
     * Returns the index on which the element with the given id
     * is located in the given haystack.
     *
     * @param haystack An array containing elements
     * @param id The id to look for in haystack
     * @returns {number} The index on which the element is located
     */
    this.arrayFind = function (haystack, id) {
        for (var i=0; i<haystack.length; i++) {
            if (haystack[i].hasOwnProperty('id') && haystack[i].id === id) {
                return i;
            }
        }
        return -1;
    };
}

(function () {
    angular
        .module('cms.services')
        .service('ArrayService', ArrayService);
}());

