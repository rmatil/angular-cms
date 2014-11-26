'use strict';

/* Services */

angular.module('cms.services', []).
  value('version', '0.1')
  .service( 'MenuService', ['$http', '$rootScope', '$log', function( $http, $rootScope, $log) {
    this.update = function( pMenuName) {
      $http.get('json/menuProperties.json').then(function (response) {
        // success handler of $http, using the "synchronous" way
        var menu = response.data;
        if($rootScope.debugModus) $log.log("MenuProperties loaded");

        if ($rootScope.menu === null) {
          $rootScope.menu = menu["Dashboard"];
          $rootScope.activeMenuPoint = "Dashboard";
          $rootScope.backgroundColorClass = menu['Dashboard'].backgroundColorClass;
          $rootScope.topBorderClass = menu['Dashboard'].topBorderClass;
        } else {
          $rootScope.activeMenuPoint = pMenuName;
          $rootScope.menuArray = menu[pMenuName].menuArray;
          $rootScope.backgroundColorClass = menu[pMenuName].backgroundColorClass;
          $rootScope.topBorderClass = menu[pMenuName].topBorderClass;
        }
      }, function (response) {
        // error handler of $http, using the "synchronous" way
        if($rootScope.debugModus) $log.error("MenuProperties could not be loaded. Server response failed");
      });
    };
  }])
  .service( 'DebugService', ['$http', '$log', '$rootScope', function($http, $log, $rootScope) {
    this.getDebugStatus = function() {
      var promise = $http.get('restful/cmsBackendAccess.php?page=settings&type=debugModus').then(function (response) {
          $log.log("Debug Modus: " + response.data[0].debugModus);
          return response.data[0].debugModus;
        }, function(response) {
          $log.error("Can't determine debug modus.");
          return false;
        });
      return promise;
    };
    this.isEnabled = function() {
      if($rootScope.debugModus) $log.log("debug modus is enabled");
    };
  }])
.service( 'SettingsService', ['$http', '$log', '$rootScope', function ($http, $log, $rootScope) {
  this.getAllLoginContent = function() {
      var promise = $http.get('restful/cmsBackendAccess.php?page=settings&type=allLog').then(function(response) { 
        return response.data;
      }, function(response) {
        $log.error("Failed to get logging content");
        return false;
      });
      return promise;
    };
}])
.service( 'OverviewService', ['$http', '$log', '$rootScope', function ($http, $log, $rootScope) {
  this.getNewestArticles = function() {
      var promise = $http.get("restful/cmsBackendAccess.php?page=statistics&type=newestArticles").then( function(response) {
        if($rootScope.debugModus) $log.log(response.data);
        return response.data;
      }, function(response) {
        $log.error("Failed to load newest articles");
        return false;
      });
      return promise;
    };
  this.getNewestUser = function() {
      var promise = $http.get("restful/cmsBackendAccess.php?page=statistics&type=newestUser").then( function(response) {
        if($rootScope.debugModus) $log.log(response.data);
        return response.data[0];
      }, function(response) {
        $log.error("Failed to load newest user");
        return false;
      });
      return promise;
    };
  this.getNewestItems = function() {
    var promise = $http.get("restful/cmsBackendAccess.php?page=statistics&type=newest").then( function(response) {
      if($rootScope.debugModus) $log.log(response.data);
      return response.data;
    }, function(response) {
      $log.error("Failed to load newest items");
      return false;
    });
    return promise;
  };
  this.getNewestEvents = function() {
      var promise = $http.get("restful/cmsBackendAccess.php?page=statistics&type=newestEvents").then( function(response) {
        if($rootScope.debugModus) $log.log(response.data);
        return response.data;
      }, function(response) {
        $log.error("Failed to load newest events");
        return false;
      });
      return promise;
    };
}]);

