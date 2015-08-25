'use strict';

function MapService ($q) {
    var that = this,
        geoCoder = new google.maps.Geocoder(),
        infoWindow = new google.maps.InfoWindow(), // infowindow used to display address
        marker, // google maps Marker object
        map, // map instance
        mapOptions ={
            zoom: 16,
            mapTypeId: 'roadmap',
            panControl: false,
            zoomControl: true,
            scaleControl: false,
            streetViewControl: false
        },
        curInputTime = 0, // time of input
        lastInputTime = 0, // time of last input
        inputTimeDiff = 0;

    /**
     * Initalizes a Google Map object in the container. Uses
     * the specified lat-lng-pair and the given address to init.
     *
     * @param containerId The id of the element in which the map should be initalized.
     * @param lat
     * @param lng
     * @param address
     */
    this.initMap = function (containerId, lat, lng, address) {
        var mapContainer = document.getElementById(containerId),
            latlng = new google.maps.LatLng(lat, lng);

        if (mapContainer.offsetHeight < 10) {
            // set a height for the container
            mapContainer.style.height = "250px";
        }

        mapOptions['center'] = latlng;

        // necessary to set height of map-canvas -> see main.css
        map = new google.maps.Map(mapContainer, mapOptions);
        marker = new google.maps.Marker({
            position: latlng,
            map: map
        });
        infoWindow.setContent(address);
        infoWindow.open(map, marker);
    };

    /**
     * Returns the difference between the last input action and now.
     *
     * @returns {number} The difference
     */
    this.calcInputSpeedDiff = function () {
        curInputTime = new Date().getTime();
        if (lastInputTime !== 0) {
            inputTimeDiff = curInputTime - lastInputTime;
        }

        lastInputTime = curInputTime;

        return inputTimeDiff;
    };

    /**
     * Sets the map to the given address.
     *
     * @param address The address to which the map should be point to.
     * @returns {{lat: Number, lng: Number, formattedAddress: String}} Returns an object containing the lat-long-pair and the formatted address
     */
    this.setMapToAddress = function (address) {
        return that.getPositionForAddress(address)
            .then(function (ret) {
                that.setMapToPosition(ret.lat, ret.lng);
                return ret;
            });
    };

    /**
     * Sets the map to the given lat-lng pair
     *
     * @param lat The latitude of the position
     * @param lng The longitude of the position
     * @returns {*} A promise which is resolved once the map is updated
     */
    this.setMapToPosition = function (lat, lng) {
        // get a promise
        var $defer = $q.defer();
        var latlng = new google.maps.LatLng(lat, lng);
        geoCoder.geocode({'latLng': latlng}, function (results, status) {
            if (status === google.maps.GeocoderStatus.OK) {
                if (results[1]) {
                    map.setZoom(16);
                    map.setCenter(latlng);
                    marker = new google.maps.Marker({
                        position: latlng,
                        map: map
                    });
                    infoWindow.setContent(results[1].formatted_address);
                    infoWindow.open(map, marker);
                    // map has updated -> resolve promise
                    $defer.resolve();
                } else {
                    $defer.reject('No results found');
                }
            } else {
                $defer.reject('Geocoder failed tue to: ' + status);
            }
        });

        return $defer.promise;
    };

    /**
     * Searches for the lat-lng-pair of the given address.
     *
     * @param address The address to look for
     * @returns {object} A promise which is resolved once the position is fetched
     */
    this.getPositionForAddress = function (address) {
        var $defer = $q.defer();
        var parsedResult;
        geoCoder.geocode({ address : address }, function (result, status) {

            if (status === google.maps.GeocoderStatus.OK) {
                parsedResult = {
                    lat: result[0].geometry.location.lat(),
                    lng: result[0].geometry.location.lng(),
                    formattedAddress: result[0].formatted_address
                };
                $defer.resolve(parsedResult);
            } else {
                $defer.reject('No results found for the given address');
            }
        });

        return $defer.promise;
    };
}

(function (angular) {
    angular
        .module('cms.services')
        .service('MapService', MapService);

    MapService.$inject = ['$q'];

})(angular);