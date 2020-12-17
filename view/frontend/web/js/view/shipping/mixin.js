/**
 * (c) Redbox Parcel Lockers <thamer@redboxsa.com>
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * Built by Redbox Technologies, <thamer@redboxsa.com>
 */

define([
    'mage/translate',
    'Magento_Checkout/js/model/quote',
    'Magento_Ui/js/model/messageList',
    'Redbox_Shipping/js/view/point',
    'ko',
    'jquery',
    'Magento_Checkout/js/action/select-shipping-address'
], function (
    translate,
    quote,
    messageList,
    point,
    ko,
    $,
    selectShippingAddress
) {
    'use strict';

    var mixin = {
        lockerErrors: ko.observable(),

        /**
         * Extend parent method to validate extension attributes data
         *
         * @returns {*}
         */
        validateShippingInformation: function () {
            var shippingAddress = quote.shippingAddress(),
                pointAddress = this.getChild('redbox-shipping-method').selectedPointAddress();
            this.lockerErrors('');
            var hasPointId = shippingAddress.hasOwnProperty('extensionAttributes') &&
                typeof shippingAddress.extensionAttributes === 'object' &&
                shippingAddress.extensionAttributes.hasOwnProperty('point_id') &&
                !!shippingAddress.extensionAttributes.point_id;

            if (!this._super()) {
                return false;
            }
            if (quote.shippingMethod().carrier_code === point().methodCode) {
                var selectedPoint = JSON.parse(window.localStorage.getItem('selected_point'));
                if (!hasPointId || !selectedPoint) {
                    this.lockerErrors('Please choose Redbox point.');
                    return false;
                }
                this.getChild('redbox-shipping-method').selectedPointAddress(pointAddress);
                quote.shippingAddress().extensionAttributes.point_id = selectedPoint.id;
            } else {
                if (shippingAddress.hasOwnProperty('extensionAttributes') &&
                    typeof shippingAddress.extensionAttributes === 'object' &&
                    shippingAddress.extensionAttributes.hasOwnProperty('point_id')) {
                    shippingAddress.extensionAttributes.point_id = null;
                }
                if (window.localStorage.getItem('selected_point')) {
                    window.localStorage.removeItem('selected_point');
                }
            }
            return true;
        },

        selectShippingMethod: function (shippingMethod) {
            if (shippingMethod.carrier_code === 'redbox') {
                $('.choose-locker').trigger('click');
            }
            return this._super();
        }
    };

    return function (target) {
        return target.extend(mixin);
    };
});
