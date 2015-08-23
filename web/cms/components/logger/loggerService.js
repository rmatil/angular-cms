'use strict';

function LoggerService () {
    this.logLevel = 'log';

    this.debug = function (obj) {
        console.debug(obj);
    };

    this.info = function (obj) {
        console.log(obj);
    };

    this.warn = function (obj) {
        console.warn(obj);
    };

    this.error = function (obj) {
        console.error(obj);
    }
}

(function(angular) {
    angular
        .module('cms.services')
        .service('LoggerService', LoggerService);

}(angular));