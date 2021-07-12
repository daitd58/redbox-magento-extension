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
        isFirstTime = true,
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
                if (method) {
                    var popupButtonVisible = method.available && method.carrier_code === self.methodCode;
                    self.isPopUpButtonVisible(popupButtonVisible);
                }
            });

            return this;
        },
        showLockersMap: function () {
            var self = this;
            self.showLockerPopUp();
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
        },
        resetSelectedLocker: function () {
            var locale = $('html').attr('lang');
            var self = this,
                address = quote.shippingAddress();
            if (address.hasOwnProperty('extension_attributes') && address.extension_attributes.hasOwnProperty('point_id') && address.extension_attributes.point_id) {
                var points = JSON.parse(window.localStorage.getItem('points'));
                if (points) {
                    var point = points.find(function (item) {
                        return item.id === address.extension_attributes.point_id;
                    });
                    if (point) {
                        var html = "<p class='title'>" + $t("Shipping to") + ":</p>" +
                            "<p>" + point.point_name + ", " + point.host_name + "</p>" +
                            "<p>" + point.address.street + "</p>" +
                            "<p>" + point.address.district + "</p>" +
                            "<p>" + point.address.city + ", " + point.address.postCode + "</p>";
                        self.selectedPointAddress(html);
                    }
                }
            }
            self.getPopUp().closeModal();
        }
    });
});
