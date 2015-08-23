'use strict';

function EventGraphDirective (EventService, EventGraphService, LoggerService) {
    var circleAttrs = {
            "fill": "#ee51da",
            "stroke": "#ee51da",
            "stroke-width": "1"
        },
        activeCircleAttrs = {
            "fill": "#8c8c8c",
            "stroke": "#8c8c8c",
            "stroke-width": "1"
        },
        lineAttrs = {
            "stroke": "#ee51da",
            "stroke-width": "1"
        },
        containerId = 'event-graph-container',
        events = [];


    return {
        restrict: 'E',
        isolate: true,
        link: function ($scope, $elm, $attrs) {

            EventService.getEvents()
                .then(function (data) {
                    EventGraphService.drawGraph(containerId, data, circleAttrs, activeCircleAttrs, lineAttrs);
                    events = data;
                });

            // redraw whole graph on resize of element or window
            window.addEventListener('resize', function () {
                // remove graph first
                var cont = document.getElementById(containerId);
                while (cont.firstChild) {
                    cont.removeChild(cont.firstChild);
                }

                EventGraphService.drawGraph(containerId, events, circleAttrs, activeCircleAttrs, lineAttrs);
            }, true);

        },
        scope: true,
        templateUrl: "components/event-dashboard/event-graph.html"
    };
}


(function (angular) {

    angular.module('cms.directives')
        .directive('eventGraph', EventGraphDirective);

    EventGraphDirective.$inject = ['EventService', 'EventGraphService', 'LoggerService'];

})(angular);

