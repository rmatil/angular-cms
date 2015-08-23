'use strict';

function EventGraphDirective (EventService, LoggerService) {
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
        compareEventFn = function (firstEvent, secondEvent) {
            if (firstEvent.date.isBefore(secondEvent.date)) {
                return -1;
            }

            if (firstEvent.date.isAfter(secondEvent.date)) {
                return 1;
            }

            return 0;
        };

    /**
     * Draws a circle on the given (x,y) position with the given radius.
     * Attributes, like fill, can be provided in the attrs object with the attribute
     * name as key and its value as value.
     *
     * @param xPos The position on the x-axis to start drawing (not the center)
     * @param yPos The position on the y-axis to start drawing (not the center)
     * @param circleRadius The radius of the circle
     * @param attrs An object with attribute key-value pairs to apply to the circle
     * @param paper A Raphaël.js paper
     */
    function drawCircle(xPos, yPos, circleRadius, attrs, paper) {
        var circle = paper.circle(xPos, yPos, circleRadius);

        for (var idx in attrs) {
            if (attrs.hasOwnProperty(idx)) {
                circle.attr(idx, attrs[idx]);
            }
        }
    }

    function drawLine(xStartPos, xEndPos, yStartPos, yEndPos, attrs, paper) {
        var line = paper.path( ["M", xStartPos, yStartPos, "L", xEndPos, yEndPos, "Z"] );

        for (var idx in attrs) {
            if (attrs.hasOwnProperty(idx)) {
                line.attr(idx, attrs[idx]);
            }
        }
    }

    /**
     * Draws a line of circles on the yPos on the given paper.
     *
     * @param xStartPos The start position on the x-axis between to draw the line of circles
     * @param xEndPos The end position on the x-axis between to draw the line of circles
     * @param yPos The position on the y-axis to draw the line of circles (the height on which the line should be drawn)
     * @param circleRadius The radius of a circle
     * @param activeCircleRadius The radius of the circle representing the next event
     * @param paper A Raphaë.js-paper
     * @param events An array containing objects with an event and a moment.js date object
     */
    function drawEvents(xStartPos, xEndPos, yPos, circleRadius, activeCircleRadius, paper, events) {
        if (events.length < 1) {
            return;
        }

        var now = moment(), // number of days in the current month
            nrOfDays = moment().endOf('month').date(), // Returns the number of the last day in the month
            middleOfMonth = moment(now).date(Math.round((nrOfDays / 2) - 1)), // the day in the middle of the month
            width = xEndPos - xStartPos, // the width of the line
            spaceWidth = Math.floor(width / nrOfDays), // amount of space between each circle (excl. circle radius)
            nextEvent = false, // will be an object containing the next event
            eventsBefore = [], // events before next event
            eventsAfter = [],
            drawEvent = function (elm) {
                LoggerService.debug("BeforeEvent " + elm.date.format("DD.MM.YYYY HH:mm:ss"));
                LoggerService.debug("Diff to 1st day of month: " + (Math.abs(moment(now).subtract(now.date(), 'days').diff(elm.date, 'days'))));
                LoggerService.debug("Diff between nextEvent and middleOfMonth " + Math.abs(nextEvent.date.diff(middleOfMonth, 'days')));
                LoggerService.debug("Calculated offset for multiplication" + (Math.abs(moment(now).subtract(now.date(), 'days').diff(elm.date, 'days')) - Math.abs(nextEvent.date.diff(middleOfMonth, 'days'))));

                drawCircle(
                    (
                        Math.abs(moment(now).subtract(now.date(), 'days').diff(elm.date, 'days')) - // days from 1st to elm
                        Math.abs(nextEvent.date.diff(middleOfMonth, 'days')) // diff in days between middle of month & elm.date
                    ) * spaceWidth - circleRadius,
                    25,
                    circleRadius,
                    circleAttrs,
                    paper
                );
            }; // events after next event

        events.forEach(function (elm) {
            if (elm.date.isAfter(now) && false === nextEvent) {
                // this is the upcoming event
                nextEvent = elm;
            } else if (elm.date.isAfter(now) && false !== nextEvent) {
                eventsAfter.push(elm);
            } else {
                eventsBefore.push(elm);
            }
        });

        // classify events and remove all which are more than the half of the month before the next event
        eventsBefore = eventsBefore.filter(function (elm) {
            return elm.date.isAfter(moment(nextEvent.date).subtract(middleOfMonth.date(), 'days'));
        });
        // classify after-events and remove all which are more than the half of the month after the next event
        eventsAfter = eventsAfter.filter(function (elm) {
            return elm.date.isBefore(moment(nextEvent.date).add(middleOfMonth.date(), 'days'));
        });

        LoggerService.debug("Next event " + nextEvent.date.format("DD.MM.YYYY HH:mm:ss"));
        LoggerService.debug("Middle of month: " + middleOfMonth.format('DD.MM.YYYY HH:mm:ss'));

        // draw next event
        drawCircle(
            Math.floor(width / 2) - activeCircleRadius,
            yPos,
            activeCircleRadius,
            activeCircleAttrs,
            paper
        );

        // draw all events before
        eventsBefore.forEach(drawEvent);
        // draw all events after
        eventsAfter.forEach(drawEvent);
    }



    return {
        restrict: 'E',
        isolate: true,
        link: function ($scope, $elm, $attrs) {
            var container = document.getElementById("event-graph-container"),
                containerWidth = container.offsetWidth,
                containerHeight = container.offsetHeight || 50,
                circleYPos = Math.floor(containerHeight / 2),
                circleRadius = 3,
                activeCircleRadius = 5,
                paper = Raphael(container, containerWidth, containerHeight);

            drawLine(0, containerWidth, circleYPos, circleYPos, lineAttrs, paper);

            EventService.getEvents()
                .then(function (data) {
                    var eventObjects = [];

                    data.forEach( function (elm) {
                        var eventDate = moment(elm.start_date, moment.ISO_8601);
                        var ev = {"date": eventDate, "event": elm};

                        eventObjects.push(ev);
                    });

                    eventObjects.sort(compareEventFn);


                    drawEvents(0, containerWidth, circleYPos, circleRadius, activeCircleRadius, paper, eventObjects);
                });

        },
        scope: true,
        templateUrl: "components/event-dashboard/event-graph.html"
    };
}


(function (angular) {

    angular.module('cms.directives')
        .directive('eventGraph', EventGraphDirective);

    EventGraphDirective.$inject = ['EventService', 'LoggerService'];

})(angular);

