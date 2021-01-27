/**
 * (c) Redbox Parcel Lockers <thamer@redboxsa.com>
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * Built by Redbox Technologies, <thamer@redboxsa.com>
 */

define([
    'jquery',
    'uiComponent',
    'ko',
    'Magento_Checkout/js/model/quote',
    'Magento_Ui/js/modal/modal',
    'mage/translate',
    'Magento_Checkout/js/model/full-screen-loader',
    'Magento_Checkout/js/model/resource-url-manager',
    'mage/storage',
    'googleMaps',
    'Magento_Checkout/js/model/shipping-save-processor/default',
    'uiRegistry',
    'MarkerClusterer'
], function (
    $,
    Component,
    ko,
    quote,
    modal,
    $t,
    fullScreenLoader,
    resourceUrlManager,
    storage,
    googleMaps,
    shippingSaveProcessor,
    registry
) {
    'use strict';
    var map = null;
    var myMarker;
    var myLatlng;
    var shippingPosition = {};
    var points = [];
    var markersPosition = [];
    var defaultLocation = {
        lat: 24.7135517,
        lng: 46.67529569999999
    };
    var self = this;

    return Component.extend({
        points: ko.observableArray([]),
        /**
         * @return {exports}
         */
        initialize: function () {
            this._super();
            return this;
        },

        confirmCanAccessThisLocation: function (data, event) {
            if ($(event.currentTarget).prop("checked") === true) {
                $(event.currentTarget).parent().parent().parent().parent().parent().find('.area-select-point button').removeAttr('disabled');
                $(event.currentTarget).parent().parent().parent().parent().parent().find('.area-select-point button').removeClass('disabled');
            } else {
                $(event.currentTarget).parent().parent().parent().parent().parent().find('.area-select-point button').attr('disabled', true);
                $(event.currentTarget).parent().parent().parent().parent().parent().find('.area-select-point button').addClass('disabled');
            }
        },

        controlPopupWarningRestricted: function (data, event) {
            if ($(event.currentTarget).hasClass('fa-times')) {
                $(event.currentTarget).parent().prev().hide();
                $(event.currentTarget).attr('class', 'fa fa-exclamation-triangle');
            } else {
                $(event.currentTarget).parent().prev().show()
                $(event.currentTarget).attr('class', 'fa fa-times')
            }
        },

        addYourLocationButton: function (map, lat, lng) {
            var marker = new google.maps.Marker({
                position: {
                    lat: lat,
                    lng: lng
                },
                map: map,
                title: 'Your shipping address',
                icon: {
                    url: 'https://stage.redboxsa.com/marker_my_location.png'
                }
            });
        },

        setClickMarker: function(infowindow, marker, point, positionTop) {
            marker.addListener('click', function(event) {
                var contentString = `<h4>${point.host_name_en}</h4><p>${point.name}</p>`;

                infowindow.setContent(contentString);

                infowindow.open(map, marker);
                $('#list-point').animate({
                    scrollTop: positionTop
                }, 'slow');
            });
        },

        setFindAreaMap: function (map) {
            var card = document.getElementById('pac-card');
            var input = document.getElementById('pac-input');

            map.controls[google.maps.ControlPosition.TOP_RIGHT].push(card);

            const autocomplete = new google.maps.places.Autocomplete(input);
            autocomplete.bindTo('bounds', map);
            autocomplete.setFields(
                ['name', 'formatted_address', 'address_components', 'geometry', 'icon']
            );
            autocomplete.addListener('place_changed', function() {
                const place = autocomplete.getPlace();
                if (place) {
                    getPoints(place.geometry.location.lat(), place.geometry.location.lng())
                }
            });
        },

        initializeGMap: function (lat, lng, lat1, lng1, pickedPoint) {
            var self = this;
            var bounds = new google.maps.LatLngBounds();

            if (!pickedPoint) {
                let myLatLng = new google.maps.LatLng(lat, lng);
                let myLatLng1 = new google.maps.LatLng(lat1, lng1);
                bounds.extend(myLatLng);
                bounds.extend(myLatLng1);
            }

            var center = {
                lat: lat,
                lng: lng
            };
            let temp = markersPosition.find(e => e.selected)
            if (temp) {
                center = temp.location;
            }

            var myOptions = {
                zoom: 17,
                center: center,
                mapTypeControl: false,
                scaleControl: true,
                zoomControl: true,
            };

            map = new google.maps.Map(document.getElementById("map"), myOptions);

            if (!pickedPoint) {
                map.fitBounds(bounds);
            }

            var infowindow = new google.maps.InfoWindow({
                content: ''
            });
            markersPosition.forEach(function (point) {
                var marker = new google.maps.Marker({
                    position: point.location,
                    map: map,
                    title: point.name,
                    icon: {
                        url: 'https://stage.redboxsa.com/marker_redbox.png'
                    }
                });
                var positionTop = $(`.per-point[value-id=${point.id}]`).position().top;
                self.setClickMarker(infowindow, marker, point, positionTop);
            });
            self.setFindAreaMap(map);
            // self.addYourLocationButton(map, lat, lng);
        },

        setMapWithPoint: function (id) {
            if (map) {
                let pointThis = points.find(e => e.id == id)
                var latLng = new google.maps.LatLng(pointThis.location.lat, pointThis.location.lng);
                map.setCenter(latLng);
                map.setZoom(17);
            }
        },

        pickPoint: function (id) {
            $('.per-point').removeClass('selected');
            $(`.per-point[value-id=${id}]`).addClass('selected');
            var point = points.find(e => e.id === id);
            $('#locker-name').val(`${point.host_name_en} (${point.point_name})`);
            $('#locker-id').val(id);
            this.setMapWithPoint(id);
            window.localStorage.removeItem('selected_point');
            var address = quote.shippingAddress();

            if (!address.hasOwnProperty('extension_attributes')) {
                Object.defineProperty(address, 'extension_attributes', {
                    value: {},
                    writable: true,
                    enumerable: true,
                    configurable: true
                });
            }

            if (!address.extension_attributes.hasOwnProperty('point_id')) {
                Object.defineProperty(address.extension_attributes, 'point_id', {
                    writable: true,
                    enumerable: true,
                    configurable: true
                });
            }

            address.extension_attributes.point_id = $.isEmptyObject(point) ? false : point.id;
            window.localStorage.setItem('selected_point', JSON.stringify(point));
            $('#button-reset-selected-locker').trigger('click');
        },

        initRedbox: function () {
            var self = this;
            points = JSON.parse(window.localStorage.getItem('points'));
            points.forEach(function (point) {
                markersPosition.push({
                    id: point.id,
                    name: point.point_name,
                    host_name_en: point.host_name_en,
                    location: point.location,
                    selected: false
                });
            });
            this.points(points);
            self.initializeGMap(defaultLocation.lat, defaultLocation.lng, markersPosition[0].location.lat, markersPosition[0].location.lng);
        }
    });
});
