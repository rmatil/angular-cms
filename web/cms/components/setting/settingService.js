'use strict';

function SettingService(GenericApiService, $log) {

    var SETTINGS = 'settings';

    this.getSettings = function () {
        return GenericApiService.get(SETTINGS);
    };

    this.getSetting = function (settingId) {
        return GenericApiService.getObject(SETTINGS, settingId);
    };

    this.getEmptySetting = function () {
        return GenericApiService.getEmptyObject(SETTINGS);
    };

    this.postSetting = function (setting) {
        return GenericApiService.post(SETTINGS, setting);
    };

    this.putSetting = function (setting) {
        return GenericApiService.put(SETTINGS, setting);
    };

    this.deleteSetting = function (settingId) {
        return GenericApiService.remove(SETTINGS, settingId);
    }

}

(function () {
    angular
        .module('cms.services')
        .service('SettingService', SettingService);

    SettingService.$inject = [ 'GenericApiService', '$log'];
}());