'use strict';


function EventGraphService (LoggerService) {
    var that = this,
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
    this.drawCircle = function (xPos, yPos, circleRadius, attrs, paper) {
        var circle = paper.circle(xPos, yPos, circleRadius);

        for (var idx in attrs) {
            if (attrs.hasOwnProperty(idx)) {
                circle.attr(idx, attrs[idx]);
            }
        }
    };

    /**
     * Draws a line from the given (xStart,yStart) to (xEnd,yEnd).
     * Attributes can be provided in attrs.
     *
     * @param xStartPos The start position on x-axis
     * @param xEndPos The start position on -axis
     * @param yStartPos The start position on y-axis
     * @param yEndPos The end position on y-axis
     * @param attrs An object with attribute key-value pairs to apply to the line
     * @param paper A Raphaël.js paper
     */
    this.drawLine = function (xStartPos, xEndPos, yStartPos, yEndPos, attrs, paper) {
        var line = paper.path( ["M", xStartPos, yStartPos, "L", xEndPos, yEndPos, "Z"] );

        for (var idx in attrs) {
            if (attrs.hasOwnProperty(idx)) {
                line.attr(idx, attrs[idx]);
            }
        }
    };

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
     * @param circleAttrs
     * @param activeCircleAttrs
     */
    this.drawEvents = function (xStartPos, xEndPos, yPos, circleRadius, activeCircleRadius, paper, events, circleAttrs, activeCircleAttrs) {
        if (events.length < 1) {
            return;
        }

        events.sort(compareEventFn);

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

                that.drawCircle(
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

        var classifiedEvents = that.getClassifiedEvents(events);
        nextEvent = classifiedEvents.nextEvent;
        eventsBefore = classifiedEvents.eventsBefore;
        eventsAfter = classifiedEvents.eventsAfter;

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
        that.drawCircle(
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
    };

    this.drawGraph = function (containerId, events, circleAttrs, activeCircleAttrs, lineAttrs) {
        var container = document.getElementById(containerId),
            containerWidth = container.offsetWidth,
            containerHeight = container.offsetHeight || 50,
            circleYPos = Math.floor(containerHeight / 2),
            circleRadius = 3,
            activeCircleRadius = 5,
            eventObjects = [],
            paper = Raphael(container, containerWidth, containerHeight);


        eventObjects = that.buildEventObjects(events);

        that.drawLine(0, containerWidth, circleYPos, circleYPos, lineAttrs, paper);
        that.drawEvents(0, containerWidth, circleYPos, circleRadius, activeCircleRadius, paper, eventObjects, circleAttrs, activeCircleAttrs);
    };

    this.buildEventObjects = function (events) {
        var eventObjects = [];

        events.forEach( function (elm) {
            if (!('start_date' in elm)) {
                throw {
                    "err": "The event must contain a field named 'start_date'",
                    "obj": elm
                };
            }

            var eventDate = moment(elm.start_date, moment.ISO_8601);
            var ev = {"date": eventDate, "event": elm};

            eventObjects.push(ev);
        });

        return eventObjects;
    };

    /**
     * Returns an object with the classified events in it.
     *
     * @param events An array of event objects with their dates as moment.js-date
     * @returns {{eventsBefore: Array, nextEvent: boolean, eventsAfter: Array}}
     */
    this.getClassifiedEvents = function (events) {
        var classifiedEvents = {
                "eventsBefore": [],
                "nextEvent": false,
                "eventsAfter": []
            },
            now = moment();

        events.forEach(function (elm) {
            if (elm.date.isAfter(now) && false === classifiedEvents.nextEvent) {
                // this is the upcoming event
                classifiedEvents.nextEvent = elm;
            } else if (elm.date.isAfter(now) && false !== classifiedEvents.nextEvent) {
                classifiedEvents.eventsAfter.push(elm);
            } else {
                classifiedEvents.eventsBefore.push(elm);
            }
        });

        return classifiedEvents;
    };
}

(function (angular) {
    angular
        .module('cms.services')
        .service('EventGraphService', EventGraphService);

    EventGraphService.$inject = ['LoggerService'];

}(angular));