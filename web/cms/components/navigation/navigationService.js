'use strict';

function NavigationService() {

    var menuArray = {
        "dashboard": {
            "backgroundColorClass": "lightyellow",
            "topBorderClass": "lightyellow-top-border",
            "menuArray": [
                {
                    "name": "Übersicht",
                    "link": "\/cms\/dashboard\/"
                }
            ]
        },
        "article": {
            "backgroundColorClass": "darkred",
            "topBorderClass": "lightred-top-border",
            "menuArray": [
                {
                    "name": "Article Overview",
                    "link": "article\/articles\/"
                },
                {
                    "name": "Add Article",
                    "link": "article\/add\/"
                },
                {
                    "name": "Page Overview",
                    "link": "pages\/page\/"
                },
                {
                    "name": "Add Page",
                    "link": "pages\/add\/"
                }
            ]
        }
        //"media": {
        //    "backgroundColorClass": "darkpurple",
        //    "topBorderClass": "lightpurple-top-border",
        //    "menuArray": [
        //        {
        //            "name": "Übersicht",
        //            "link": "#\/files\/"
        //        },
        //        {
        //            "name": "Upload",
        //            "link": "#\/add-file\/"
        //        }
        //    ]
        //},
        //"event": {
        //    "backgroundColorClass": "darkpink",
        //    "topBorderClass": "lightpink-top-border",
        //    "menuArray": [
        //        {
        //            "name": "Übersicht",
        //            "link": "#\/events\/"
        //        },
        //        {
        //            "name": "Event hinzufügen",
        //            "link": "#\/add-event\/"
        //        },
        //        {
        //            "name": "Veranstaltungsort hinzufügen",
        //            "link": "#\/add-location"
        //        }
        //    ]
        //},
        //"user": {
        //    "backgroundColorClass": "darkblue",
        //    "topBorderClass": "lightblue-top-border",
        //    "menuArray": [
        //        {
        //            "name": "Übersicht",
        //            "link": "#\/users\/"
        //        },
        //        {
        //            "name": "Benutzer hinzufügen",
        //            "link": "#\/add-user\/"
        //        }
        //    ]
        //},
        //"setting": {
        //    "backgroundColorClass": "darkgreen",
        //    "topBorderClass": "lightgreen-top-border",
        //    "menuArray": [
        //        {
        //            "name": "Übersicht",
        //            "link": "#\/settings\/"
        //        },
        //        {
        //            "name": "System Logging",
        //            "link": "#\/settings\/system-logging\/"
        //        },
        //        {
        //            "name": "Datenbank Logging",
        //            "link": "#\/settings\/database-logging\/"
        //        }
        //    ]
        //}
    };

    /**
     * Returns the menu properties for the given menu name
     *
     * @param menuName The menu name of which to fetch its properties
     * @returns { object|null }
     */
     this.getMenuProperty = function (menuName) {
        if (menuName in menuArray) {
            return menuArray[menuName];
        }

        return null;
    };

    /**
     * Returns the subnavigation array of a given parent route.
     *
     * @param currentUrl The url of which to get the submenues
     * @returns { object }
     */
    this.getSubnavigation = function (currentUrl) {
        var routeElements = currentUrl.split("/");
        for (var property in routeElements) {
            if (routeElements.hasOwnProperty(property) && routeElements[property].length > 0) {
                if (routeElements[property] in menuArray) {
                    var subNav = {};
                    subNav.menuArray = menuArray[routeElements[property]].menuArray;
                    subNav.topBorderClass = menuArray[routeElements[property]].topBorderClass;
                    return subNav;
                }
            }
        }

        return null;
    }

}
(function () {
    angular
        .module('cms.services')
        .service('NavigationService', NavigationService);
}());
