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
    'uiRegistry'
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
    uiRegistry
) {
    'use strict';

    var popUp = null,
        localStorage = [];
    return Component.extend({
        defaults: {
            methodCode: 'redbox'
        },
        isLockerPopUpVisible: ko.observable(false),
        isPopUpButtonVisible: ko.observable(false),
        selectedPointAddress: ko.observable(''),
        getPostCode: ko.observable(''),
        findOutShow: ko.observable(false),
        defaultPostCode: ko.observable('London'),
        showPhoneField: ko.observable(false),

        /**
         * @return {exports}
         */
        initialize: function () {
            var self = this;
            this._super();

            this.isLockerPopUpVisible.subscribe(function (value) {
                if (value) {
                    self.getPopUp().openModal();
                }
            });

            quote.shippingMethod.subscribe(function (method) {
                var popupButtonVisible = method.carrier_code === self.methodCode;
                // if (!popupButtonVisible) {
                //     self.setLocker({});
                // }
                self.isPopUpButtonVisible(popupButtonVisible);
            });

            quote.shippingAddress.subscribe(function (address) {
                console.log(address);
                // self.setLocker({});
            });

            return this;
        },
        showLockersMap: function () {
            fullScreenLoader.startLoader();
            var self = this;
            var lat = 21.0500889;
            var lng = 105.7976686;
            storage.get(
                resourceUrlManager.getUrl({'default': `/redbox/get-points?lat=${lat}&lng=${lng}`}, {})
            ).done(
                function (response) {
                    var lockers = response[1];
                    window.localStorage.setItem('points', JSON.stringify(lockers));
                    self.showLockerPopUp();
                }
            ).fail(
                function (response) {
                    console.log('Error', response);
                }
            );
        },
        getPopUp: function () {
            var self = this,
                buttons;

            if (!popUp) {
                this.popUpForm.options.closed = function () {
                    self.isLockerPopUpVisible(false);
                };

                this.popUpForm.options.modalCloseBtnHandler = this.onClosePopUp.bind(this);
                this.popUpForm.options.keyEventHandlers = {
                    escapeKey: this.onClosePopUp.bind(this)
                };

                popUp = modal(this.popUpForm.options, $(this.popUpForm.element));
            }

            return popUp;
        },
        onClosePopUp: function () {
            this.getPopUp().closeModal();
        },
        showLockerPopUp: function () {
            this.isLockerPopUpVisible(true);
            $('#init-redbox').trigger('click');
            fullScreenLoader.stopLoader();
        },
        resetSelectedLocker: function () {
            var self = this,
                address = quote.shippingAddress();
            if (address.extensionAttributes.hasOwnProperty('point_id') && address.extensionAttributes.point_id) {
                var points = JSON.parse(window.localStorage.getItem('points'));
                if (points) {
                    var point = points.find(function (item) {
                        return item.id === address.extensionAttributes.point_id;
                    });
                    if (point) {
                        var html = "<p class='title'>Point selected:</p>" +
                            "<p>" + point.point_name + ", " + point.host_name_en + "</p>" +
                            "<p>" + point.address.street + "</p>" +
                            "<p>" + point.address.district + "</p>" +
                            "<p>" + point.address.city + ", " + point.address.postCode + "</p>";
                        self.selectedPointAddress(html);
                        self.getPopUp().closeModal();
                    }
                }
            }
        }
    });
});
