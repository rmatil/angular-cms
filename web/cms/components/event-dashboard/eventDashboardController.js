'use strict';

function EventDashboardController (EventService, LocationService) {
    var vm = this;

    vm.events = [];
    vm.locations = [];

    activate();

    function activate() {
        // TODO: get events, locations, funny stats, etc
        EventService.getEvents()
            .then(function (data) {
                vm.events = data;
            });
        LocationService.getLocations()
            .then(function (data) {
                vm.locations = data;
            });
    }
}

(function(angular){
    angular
        .module('cms.controllers')
        .controller('EventDashboardController', EventDashboardController);

    EventDashboardController.$inject = ['EventService', 'LocationService']
})(angular);