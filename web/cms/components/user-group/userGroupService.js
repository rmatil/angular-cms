'use strict';

function UserGroupService(GenericApiService, $log) {

    var USER_GROUPS = 'usergroups';

    this.getUserGroups = function () {
        return GenericApiService.get(USER_GROUPS);
    };

    this.getUserGroup = function (userGroupId) {
        return GenericApiService.getObject(USER_GROUPS, userGroupId);
    };

    this.getEmptyUserGroup = function () {
        return GenericApiService.getEmptyObject(USER_GROUPS);
    };

    this.postUserGroup = function (userGroup) {
        return GenericApiService.post(USER_GROUPS, userGroup);
    };

    this.putUserGroup = function (userGroup) {
        return GenericApiService.put(USER_GROUPS, userGroup);
    };

    this.deleteUserGroup = function (userGroupId) {
        return GenericApiService.remove(USER_GROUPS, userGroupId);
    }

}

(function () {
    angular
        .module('cms.services')
        .service('UserGroupService', UserGroupService);

    UserGroupService.$inject = [ 'GenericApiService', '$log'];
}());