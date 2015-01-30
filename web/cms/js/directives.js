/*jslint browser: true, sloppy: true, plusplus: true, nomen: true*/
/*globals angular, window */
'use strict';

/* Directives */


angular.module('cms.directives', []).
    directive('appVersion', ['version', function(version) {
        return function(scope, elm, attrs) {
          elm.text(version);
        };
    }])
    .directive('articleUrlName', [ function() {
       
        return {
            restrict: 'A', // only activate on element attribute
            link: function link(scope, element, attributes) {

                var changeToUrlName = function(pInputValue) {
                    if (!pInputValue) {
                        return '';
                    }
                    var articleTitle = pInputValue;
                    var urlName = articleTitle.replace(/['ä']/g, 'a').replace(/['ö']/g, 'o').replace(/['ü']/g, 'u').replace(/[\x7f-\xff]/g, '-').replace(/[\s]/g, '-');
                    return urlName;    
                };

                scope.$watch(attributes.ngModel, function(inputValue) {
                    // prevent typeError undefined for scope.article when initalizing
                    if (scope.article) {
                        scope.article.url_name = changeToUrlName(inputValue);
                    }
                });
            }
        };
        
    }])
    .directive('pageUrlName', [ function() {
       
        return {
            restrict: 'A', // only activate on element attribute
            link: function link(scope, element, attributes) {

                var changeToUrlName = function(pInputValue) {
                    if (!pInputValue) {
                        return '';
                    }
                    var pageTitle = pInputValue;
                    var urlName = pageTitle.replace(/[\x7f-\xff]/g, '').replace(/[ \t\r\n\v\f]/g, '-');
                    return urlName;    
                };

                scope.$watch(attributes.ngModel, function(inputValue) {
                    // prevent typeError undefined for scope.article when initalizing
                    if (scope.page) {
                        scope.page.url_name = changeToUrlName(inputValue);
                    }
                });
            }
        };
        
    }])
    .directive('sameAs', function() {
        return {
            require: 'ngModel',
            link: function(scope, elm, attrs, ctrl) {
                ctrl.$parsers.unshift(function(viewValue) {
                    if (scope.debugModus) {
                        console.log("pw1: "+scope[attrs.sameAs]);
                        console.log("pw2: "+viewValue);
                    }
                    var isEqual = viewValue === scope[attrs.sameAs];
                    ctrl.$setValidity('sameas', isEqual);
                    
                });
            }
        };
    })
    .directive('username', [ function() {
        return {
            restrict: 'A', // only activate on element attribute
            link: function link(scope, element, attributes) {
                console.log(attributes);
                // inital values
                var firstnameShort = '',
                    lastnameShort  = '',
                    user_name;


                scope.$watch('user.first_name', function() {
                    if (scope.user) {
                        firstnameShort = scope.user.first_name.substr(0,2).toLowerCase();
                        // update scope
                        scope.user.user_name = firstnameShort + lastnameShort;   
                    }
                });
                scope.$watch('user.last_name', function() {
                    if (scope.user) {
                        lastnameShort = scope.user.last_name.substr(0,5).toLowerCase();
                        // update scope
                        scope.user.user_name = firstnameShort + lastnameShort;    
                    }
                });
            }
        };
        
    }])
    .directive('geocode', [ function () {
        return {
            restrict: 'A',
            link: function (scope, element, attrs) {
                element.bind("keydown", function (event) {
                    // check if key is between 0 and z
                    // 96-105: numpad numbers
                    // 8: backspace
                    // 13: enter
                    // 32: space
                    if(!((event.which >= 48 && event.which <= 90) ||
                         (event.which >= 96 && event.which <= 105) ||
                          event.which === 8 ||
                          event.which === 13 ||
                          event.which === 32)) {
                        return;
                    }
                    scope.$apply(function () {                        
                        scope.$eval('checkTypeSpeed()');
                    });

                });
            }
        }
    }])
    .directive('deleteObject', ['$interval', '$timeout', 'genService', '$location', function($interval, $timeout, genService, $location) {
        return {
            restrict: 'A',
            link: function(scope, elem, attrs) {
                    var decreasePromise,
                        resetPromise,
                        timeToRemoval = 6; // 6 seconds until removal gets initiated

                    elem.bind("click", function() {
                        if (elem.val() === "started") {
                            elem.val("stopped"); 
                            $interval.cancel(decreasePromise);
                            scope.deleteMsg = 'Abgebrochen';

                            resetPromise = $timeout(function () {
                                scope.deleteMsg = 'Löschen';
                            }, 800);

                        } else {
                            elem.val("started");

                            $timeout.cancel(resetPromise);

                            decreasePromise = $interval( function(ctr) {
                                scope.deleteMsg = 'Abbrechen [' + (timeToRemoval - ctr - 1) + ']';
                            }, 1000, timeToRemoval);

                            decreasePromise.then(function () {
                                scope.deleteMsg = 'Gelöscht';
                                genService.deleteObjectById(attrs.deleteObjectPath, attrs.deleteObjectId).then(function () {
                                    $timeout(function() {
                                        $location.path('/' + attrs.deleteObjectPath);
                                    }, 2500);
                                });
                            });
                        }
                    })

                    elem.on('$destroy', function() {
                        $timeout.cancel(resetPromise);
                        $interval.cancel(decreasePromise);
                    });
            }
        }
    }])
    .directive('goBack', ['$interval', '$timeout', 'genService', '$location', '$window', function($interval, $timeout, genService, $location, $window) {
        return {
            restrict: 'A',
            link: function(scope, elem, attrs) {
                    elem.bind("click", function() {
                        scope.isLocked = !scope.isLocked;
                        $window.history.back();
                    })
                }
        }
    }]);
