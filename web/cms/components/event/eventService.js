'use strict';

function EventService(GenericApiService, $log) {

    var EVENTS = 'events';

    this.getEvents = function () {
        return GenericApiService.get(EVENTS);
    };

    this.getEvent = function (eventId) {
        return GenericApiService.getObject(EVENTS, eventId);
    };

    this.getEmptyEvent = function () {
        return GenericApiService.getEmptyObject(EVENTS);
    }

    this.postEvent = function (event) {
        return GenericApiService.post(EVENTS, event);
    };

    this.putEvent = function (event) {
        return GenericApiService.put(EVENTS, event);
    };

    this.deleteEvent = function (eventId) {
        return GenericApiService.remove(EVENTS, eventId);
    }

}

(function () {
    angular
        .module('cms.services')
        .service('EventService', EventService);

    EventService.$inject = [ 'GenericApiService', '$log'];
}());