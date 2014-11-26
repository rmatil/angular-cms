'use strict';

angular.module('cms.genServices', [])
.service('genService', ['$http', '$log', '$rootScope', function($http, $log, $rootScope) {
	/**
	 * Returns an empty object specified by the api-pageName
	 * @param {String} pageName Name of the page of which to retreive the object
	 */
	this.getEmptyObject = function(pageName) {
		var promise = $http.get(
						'/api/empty/' 
						+ pageName)
					.then(function(response) {
						return response.data;
					}, function(response) {
						if ($rootScope.debugModus) {
							$log.error('[' 
								+ response.config.method
								+ ']' 
								+ ' '
								+ response.config.url
								+ ' : Response: ' 
								+ '[' 
								+ response.headers.status
								+ '] '
								+ response.data );
						}
					});

		return promise;
	};

	/**
	 * Returns an object specified by the api-pageName and its id.
	 * @param {String} pageName Name of the page of which to retreive the object
	 * @param {int} objectId Id of the object to retreive
	 */
	this.getObjectById = function(pageName, objectId) {
		var promise = $http.get(
						'/api/' 
						+ pageName + '/'
						+ objectId)
					.then(function(response) {
						return response.data;
					}, function(response) {
						if ($rootScope.debugModus) {
							$log.error('[' 
								+ response.config.method
								+ ']' 
								+ ' '
								+ response.config.url
								+ ' : Response: ' 
								+ '[' 
								+ response.headers.status
								+ '] '
								+ response.data );
						}
					});

		return promise;
	};

	/**
	 * Returns all objects specified by the api-pageName
	 * @param {String} pageName Name of the page of which to retreive the objects
	 */
	this.getAllObjects = function(pageName) {
		var promise = $http.get(
						'/api/' 
						+ pageName)
					.then(function(response) {
						return response.data;
					}, function(response) {
						if ($rootScope.debugModus) {
							$log.error('[' 
								+ response.config.method
								+ ']' 
								+ ' '
								+ response.config.url
								+ ' : Response: ' 
								+ '[' 
								+ response.headers.status
								+ '] '
								+ response.data );
						}
					});

		return promise;
	};

	/**
	 * Inserts a new object specified by the api-pageName into the database.
	 * @param {String} pageName Name of the page of which to insert the object
	 * @param {object} object Object to insert into the database
	 */
	this.insertObject = function(pageName, object) {
		var promise = $http({
						headers: 	{ 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'},
						url: 		'/api/' + pageName,
						method: 	'POST',
						data: 		object
					}).then(function(response) {
						return response;
					}, function(response) {
						if ($rootScope.debugModus) {
							$log.error('[' 
								+ response.config.method
								+ ']' 
								+ ' '
								+ response.config.url
								+ ' : Response: ' 
								+ '[' 
								+ response.headers.status
								+ '] '
								+ response.data );
						}
					});

		return promise;
	}

	/**
	 * Updates a given object specified by its api-pageName in the database.
	 * @param {String} pageName Name of the page of which to update the object
	 * @param {object} object Object to update
	 */
	this.updateObject = function(pageName, object) {
		var promise = $http({
						headers: 	{ 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'},
						url: 		'/api/' + pageName + '/update/' + object.id,
						method: 	'POST',
						data: 		object
					}).then(function(response) {
						return response;
					}, function(response) {
						if ($rootScope.debugModus) {
							$log.error('[' 
								+ response.config.method
								+ ']' 
								+ ' '
								+ response.config.url
								+ ' : Response: ' 
								+ '[' 
								+ response.headers.status
								+ '] '
								+ response.data );
						}
					});

		return promise;
	}

	/**
	 * Removes a given object specified by its api-pageName from the database.
	 * @param {String} pageName Name of the page of which to update the object
	 * @param {int} id Id of the object to remove
	 */
	this.deleteObjectById = function(pageName, id) {
		var promise = $http.delete(
						'/api/' 
						+ pageName + '/'
						+ id)
					.then(function(response) {
						return response;
					}, function(response) {
						if ($rootScope.debugModus) {
							$log.error('[' 
								+ response.config.method
								+ ']' 
								+ ' '
								+ response.config.url
								+ ' : Response: ' 
								+ '[' 
								+ response.headers.status
								+ '] '
								+ response.data );
						}
					});

		return promise;
	}
		

}]);









