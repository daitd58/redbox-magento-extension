/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'uiComponent',
    'Magento_Customer/js/customer-data',
    'Magento_Checkout/js/model/quote'
], function (Component, customerData, quote) {
    'use strict';

    var countryData = customerData.get('directory-data');
    var locker = null;

    return Component.extend({
        defaults: {
            template: 'Redbox_Shipping/shipping-information/address-renderer/redbox'
        },

        /**
         * @param {*} countryId
         * @return {String}
         */
        getCountryName: function (countryId) {
            return countryData()[countryId] != undefined ? countryData()[countryId].name : '';
        },

        isRedbox: function (address) {
            if (window.localStorage.getItem('selected_locker')) {
                locker = JSON.parse(window.localStorage.getItem('selected_locker'));
                return true;
            }
            return false;
        },

        getLockerStreet: function () {
            return locker.street;
        },

        getLockerName: function () {
            return 'Point name: ' + locker.building_no;
        },

        getLockerPostcode: function () {
            return locker.post_code;
        },

        getLockerCity: function () {
            return locker.city;
        }

    });
});
