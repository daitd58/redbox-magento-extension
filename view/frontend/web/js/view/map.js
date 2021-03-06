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
    'mage/translate',
    'Magento_Checkout/js/model/resource-url-manager',
    'mage/storage',
    'MarkerClusterer',
    'Magento_Checkout/js/checkout-data'
], function (
    $,
    Component,
    ko,
    quote,
    $t,
    resourceUrlManager,
    storage,
    MarkerClusterer,
    checkoutData
) {
    'use strict';
    var map = null;
    var points = [];
    var markers = [];
    var markerCluster = null;
    var isFirstTime = true;
    var defaultLocation = {
        lat: 24.7135517,
        lng: 46.67529569999999
    };
    var yourLocation = null;

    return Component.extend({
        points: ko.observableArray([]),
        currentPoint: ko.observable(),
        invalid: ko.observable(false),
        redboxLoading: ko.observable(false),
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
                $(event.currentTarget).parent().parent().parent().parent().hide();
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
                title: $t('Your address'),
                icon: {
                    url: window.myMarkerIconPath
                }
            });
        },

        clearPoint: function () {
            this.currentPoint(null);
        },

        closeModal: function () {
            $('#button-reset-selected-locker').trigger('click');
        },

        validate: function () {
            var hasPoint = points.find(function (point) {
                return point.selected;
            });

            if (hasPoint) {
                this.invalid(false);
                this.closeModal();
            } else {
                this.invalid(true);
            }
        },

        setClickMarker: function(marker, point) {
            var self = this;
            marker.addListener('click', function() {
                self.currentPoint(point);
                console.log()
            });
        },

        setFindAreaMap: function (map) {
            var self = this;
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
                if (place && place.geometry) {
                    self.getPoints(place.geometry.location.lat(), place.geometry.location.lng())
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
            if (yourLocation) {
                center = yourLocation;
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

            var infoWindow = new google.maps.InfoWindow();
            const locationButton = document.createElement("button");
            locationButton.title = $t("Use my location");
            locationButton.setAttribute('aria-label', $t('Use my location'));
            locationButton.innerHTML = `<img src="${window.myLocationIconPath}" />`;
            locationButton.classList.add("custom-map-control-button");
            map.controls[google.maps.ControlPosition.TOP_RIGHT].push(locationButton);
            locationButton.addEventListener("click", () => {
                // Try HTML5 geolocation.
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function (position) {
                        yourLocation = {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude,
                        };
                        self.getPoints(yourLocation.lat, yourLocation.lng);
                    }, function () {
                        self.handleLocationError(true, infoWindow, map.getCenter());
                    });
                } else {
                    // Browser doesn't support Geolocation
                    handleLocationError(false, infoWindow, map.getCenter());
                }
            });
                
            markers = points.map(function (point) {
                var marker = new google.maps.Marker({
                    position: point.location,
                    map: map,
                    title: point.name,
                    icon: {
                        url: self.getMarkerImage(point.type_point)
                    },
                    id: point.id,
                    type: point.type_point
                });
                self.setClickMarker(marker, point);
                return marker;
            });
            markerCluster = new MarkerClusterer(map, markers, {
                imagePath: window.clusterIconPath,
                averageCenter: true,
            });
            self.setFindAreaMap(map);
            if (yourLocation) {
                self.addYourLocationButton(map, yourLocation.lat, yourLocation.lng);
            }
        },

        getMarkerImage: function (pointType) {
			switch (pointType) {
				case 'Both':
					return window.markerCounterLockerIconPath;
				case 'Counter':
					return window.markerCounterIconPath;
				case 'Locker':
					return window.markerLockerIconPath;
				default:
					return window.markerLockerIconPath;
			}
		},

		getPointImage: function (pointType) {
			switch (pointType) {
				case 'Both':
					return window.counterLockerIconPath;
				case 'Counter':
					return window.counterIconPath;
				case 'Locker':
					return window.lockerIconPath;
				default:
					return window.lockerIconPath;
			}
		},

        getLocationName: function (pointType) {
            switch (pointType) {
				case 'Both':
					return $t('Counter & Locker');
				case 'Counter':
					return $t('Counter');
				case 'Locker':
					return $t('Locker');
				default:
					return $t('Locker');
			}
        },

        handleLocationError: function (browserHasGeolocation, infoWindow, pos) {
            infoWindow.setPosition(pos);
            infoWindow.setContent(
                browserHasGeolocation
                ? $t("Error: The Geolocation service failed.")
                : $t("Error: Your browser doesn't support geolocation.")
            );
            infoWindow.open(map);
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
            var self = this;
            self.invalid(false);
            markers.forEach(function (marker) {
				if (marker.id == id) {
					marker.setIcon(window.markerSelectedIconPath);
				} else {
					marker.setIcon(self.getMarkerImage(marker.type_point));
				}
			});
            points = points.map(function (point) {
                point.selected = point.id === id;
                return point;
            });
            self.points(points);
            self.setMapWithPoint(id);
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

        getPoints: function (lat, lng, callback) {
            var self = this;
            var locale = $('html').attr('lang');
            self.redboxLoading(true);
            storage.get(
                resourceUrlManager.getUrl({'default': `/redbox/get-points?lat=${lat}&lng=${lng}`}, {})
            ).done(
                function (response) {
                    points = response[1];
                    points = points.map(function (point) {
                        point.name = point.point_name;
                        point.host_name = locale === 'ar' ? point.host_name_ar : point.host_name_en;
                        point.selected = false;
                        point.estimateTime = self.getEstimateTime(point.estimateTime);
                        point.image = self.getPointImage(point.type_point);
                        point.type = self.getLocationName(point.type_point);
                        return point;
                    });
                    window.localStorage.setItem('points', JSON.stringify(points));
                    self.points(points);
                    self.redboxLoading(false);
                    self.initializeGMap(lat, lng, points[0].location.lat, points[0].location.lng);
                }
            ).fail(
                function (response) {
                    console.log('Error', response);
                }
            );
        },

        getEstimateTime: function (hours) {
            var days = hours / 24;
            if (days < 2) {
                return '1-2';
            } else {
                var floor10 = Math.floor(days);
                return `${floor10}-${floor10 + 1}`;
            }
        },

        initRedbox: function () {
            if (!isFirstTime) {
                return;
            }
            var self = this;
            isFirstTime = false;
            self.getPoints(defaultLocation.lat, defaultLocation.lng)
        }
    });
});
