'use strict';

function NavigationService() {

    var that = this,
        menuArray = {
        "dashboard": {
            "backgroundColorClass": "lightyellow",
            "topBorderClass": "lightyellow-top-border",
            "menuArray": [
                {
                    "name": "Dashboard",
                    "link": "\/cms\/"
                }
            ]
        },
        "articles": {
            "backgroundColorClass": "darkred",
            "topBorderClass": "lightred-top-border",
            "menuArray": [
                {
                    "name": "Article Overview",
                    "link": "articles\/list",
                    "inMenu": true
                },
                {
                    "name": "Add Article",
                    "link": "articles\/add",
                    "inMenu": true
                },
                {
                    "name": "Edit Article",
                    "link": "articles\/article\/:id",
                    "inMenu": false
                },
                {
                    "name": "Page Overview",
                    "link": "pages\/list",
                    "inMenu": true
                },
                {
                    "name": "Add Page",
                    "link": "pages\/add",
                    "inMenu": true
                },
                {
                    "name": "Edit Page",
                    "link": "pages\/page\/:id",
                    "inMenu": false
                }
            ]
        },
        "pages": {
            "backgroundColorClass": "darkred",
            "topBorderClass": "lightred-top-border",
            "menuArray": [
                {
                    "name": "Article Overview",
                    "link": "articles\/list",
                    "inMenu": true
                },
                {
                    "name": "Add Article",
                    "link": "articles\/add",
                    "inMenu": true
                },
                {
                    "name": "Edit Article",
                    "link": "articles\/article\/:id",
                    "inMenu": false
                },
                {
                    "name": "Page Overview",
                    "link": "pages\/list",
                    "inMenu": true
                },
                {
                    "name": "Add Page",
                    "link": "pages\/add",
                    "inMenu": true
                },
                {
                    "name": "Edit Page",
                    "link": "pages\/page\/:id",
                    "inMenu": false
                }
            ]
        },
        "events": {
            "backgroundColorClass": "darkpink",
            "topBorderClass": "lightpink-top-border",
            "menuArray": [
                {
                    "name": "Event Dashboard",
                    "link": "events\/list",
                    "inMenu": true
                },
                {
                    "name": "Add Event",
                    "link": "events\/add",
                    "inMenu": true
                },
                {
                    "name": "Add Location",
                    "link": "locations\/add",
                    "inMenu": true
                }
            ]
        }
        //"files": {
        //    "backgroundColorClass": "darkpurple",
        //    "topBorderClass": "lightpurple-top-border",
        //    "menuArray": [
        //        {
        //            "name": "Übersicht",
        //            "link": "#\/files"
        //        },
        //        {
        //            "name": "Upload",
        //            "link": "#\/add-file"
        //        }
        //    ]
        //},
        //"events": {
        //    "backgroundColorClass": "darkpink",
        //    "topBorderClass": "lightpink-top-border",
        //    "menuArray": [
        //        {
        //            "name": "Übersicht",
        //            "link": "#\/events"
        //        },
        //        {
        //            "name": "Event hinzufügen",
        //            "link": "#\/add-event"
        //        },
        //        {
        //            "name": "Veranstaltungsort hinzufügen",
        //            "link": "#\/add-location"
        //        }
        //    ]
        //},
        //"users": {
        //    "backgroundColorClass": "darkblue",
        //    "topBorderClass": "lightblue-top-border",
        //    "menuArray": [
        //        {
        //            "name": "Übersicht",
        //            "link": "#\/users"
        //        },
        //        {
        //            "name": "Benutzer hinzufügen",
        //            "link": "#\/add-user"
        //        }
        //    ]
        //},
        //"settings": {
        //    "backgroundColorClass": "darkgreen",
        //    "topBorderClass": "lightgreen-top-border",
        //    "menuArray": [
        //        {
        //            "name": "Übersicht",
        //            "link": "#\/settings"
        //        },
        //        {
        //            "name": "System Logging",
        //            "link": "#\/settings\/system-logging"
        //        },
        //        {
        //            "name": "Datenbank Logging",
        //            "link": "#\/settings\/database-logging"
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
     * Returns the background color class for the given url
     *
     * @param currentUrl The url of which to get the background color class
     * @returns {string} The class of the background color
     */
    this.getBackgroundColorClass = function (currentUrl) {
        var subNav = that.getSubNavigation(currentUrl, false);

        if (null !== subNav) {
            return subNav.backgroundColorClass;
        }

        return '';
    };

    /**
     * Returns the sub-navigation array of a given parent route.
     *
     * @param currentUrl The url of which to get the submenues
     * @param isInMenuCheck Whether to return only entries which are inMenu
     * @returns { object }
     */
    this.getSubNavigation = function (currentUrl, isInMenuCheck) {
        var routeElements = currentUrl.split("/");

        for (var property in routeElements) {
            if (routeElements.hasOwnProperty(property) && routeElements[property].length > 0) {
                if (routeElements[property] in menuArray) {
                    var subNav = {},
                        entries = [];

                    for (var key in menuArray[routeElements[property]].menuArray) {
                        if (menuArray[routeElements[property]].menuArray.hasOwnProperty(key)) {
                            if ((true === isInMenuCheck && true === menuArray[routeElements[property]].menuArray[key].inMenu) ||
                                false === isInMenuCheck) {
                                entries.push(menuArray[routeElements[property]].menuArray[key]);
                            }

                        }
                    }

                    subNav.menuArray = entries;
                    subNav.topBorderClass = menuArray[routeElements[property]].topBorderClass;
                    subNav.backgroundColorClass = menuArray[routeElements[property]].backgroundColorClass;
                    return subNav;
                }
            }
        }

        return null;
    };

    /**
     * Returns the name registered in the nav array above for a page
     * on the given url
     *
     * @param currentUrl The url of the page of which to get its name
     * @returns {string} A string representing the found name. Is empty if not found
     */
    this.getPageName = function (currentUrl) {
        var subNav = that.getSubNavigation(currentUrl, false);

        if (!('menuArray' in subNav)) {
            return '';
        }

        // last two elements must match, e.g. 'article/add'
        var routeElements = currentUrl.split("/");

        if (routeElements.length < 2) {
            // we can not handle this route
            return '';
        }

        var route = routeElements[routeElements.length - 2] + "/" + routeElements[routeElements.length -1];

        for (var entry in subNav.menuArray) {
            if (subNav.menuArray.hasOwnProperty(entry) &&
                subNav.menuArray[entry].hasOwnProperty('link') &&
                subNav.menuArray[entry].hasOwnProperty('name')) {

                if (route === subNav.menuArray[entry].link ) {
                    // simple route
                    return subNav.menuArray[entry].name;
                }

                if (subNav.menuArray[entry].link.indexOf(':id') === (subNav.menuArray[entry].link.length - 3)) {
                    // link in menuArray ends with :id
                    // does current route end with an id?
                    var regEx = new RegExp("\\d+");
                    var lastRouteElement = routeElements[routeElements.length - 1];
                    var secondLastRouteElement = routeElements[routeElements.length - 2];
                    var menuArrayRouteElements = subNav.menuArray[entry].link.split("/");

                    if (true === regEx.test(lastRouteElement) &&
                        menuArrayRouteElements[menuArrayRouteElements.length -2 ] === secondLastRouteElement) {
                        return subNav.menuArray[entry].name;
                    }
                }

            }
        }

        return '';
    };

}
(function () {
    angular
        .module('cms.services')
        .service('NavigationService', NavigationService);
}());
