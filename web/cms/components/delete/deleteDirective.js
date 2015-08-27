'use strict';

function DeleteObject ($interval, $timeout, GenericApiService, $location) {
    return {
        restrict: 'E',
        scope: true,
        template: '<button type="button" class="delete">{{ deleteMsg }}</button>',
        link: function (scope, elem, attrs) {
            var decreasePromise,
                resetPromise,
                timeToRemoval = 6; // 6 seconds until removal gets initiated

            scope.deleteMsg = 'Remove';

            elem.bind("click", function () {
                if (elem.val() === "started") {
                    elem.val("stopped");
                    $interval.cancel(decreasePromise);
                    scope.deleteMsg = 'Aborted';

                    resetPromise = $timeout(function () {
                        scope.deleteMsg = 'Remove';
                    }, 800);

                } else {
                    elem.val("started");

                    $timeout.cancel(resetPromise);

                    decreasePromise = $interval(function (ctr) {
                        scope.deleteMsg = 'Remove [' + (timeToRemoval - ctr - 1) + ']';
                    }, 1000, timeToRemoval);

                    decreasePromise.then(function () {
                        scope.deleteMsg = 'Removed';
                        GenericApiService.remove(attrs.objecttype, attrs.objectid);
                    });
                }
            });

            elem.on('$destroy', function () {
                $timeout.cancel(resetPromise);
                $interval.cancel(decreasePromise);
            });
        }
    };
}


(function (angular) {

    angular
        .module('cms.directives')
        .directive('deleteObject', DeleteObject);

    DeleteObject.$inject = ['$interval', '$timeout', 'GenericApiService', '$location'];

})(angular);