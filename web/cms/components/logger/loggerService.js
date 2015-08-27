'use strict';

function LoggerService () {
    var that = this;

    this.levels = {
        "debug": 0,
        "info": 1,
        "warn": 2,
        "error": 3
    };

    this.logLevel = '0';

    this.debug = function (obj) {
        if (this.levels.debug >= this.logLevel) {
            console.debug(obj);
        }
    };

    this.info = function (obj) {
        if (this.levels.info >= this.logLevel) {
            console.log(obj);
        }
    };

    this.warn = function (obj) {
        if (this.levels.warn >= this.logLevel) {
            console.warn(obj);
        }
    };

    this.error = function (obj) {
        if (this.levels.error >= this.logLevel) {
            console.error(obj);
        }
    };

    this.setLogLevel = function (level) {
        that.logLevel = level;
    };
}

(function(angular) {
    angular
        .module('cms.services')
        .service('LoggerService', LoggerService);

}(angular));