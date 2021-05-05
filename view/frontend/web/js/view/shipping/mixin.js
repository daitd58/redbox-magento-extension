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
    'Magento_Checkout/js/action/select-shipping-address',
    'mage/translate'
], function (
    translate,
    quote,
    messageList,
    point,
    ko,
    $,
    selectShippingAddress,
    $t
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
            console.log(shippingAddress);
            var hasPointId = shippingAddress.hasOwnProperty('extension_attributes') &&
                typeof shippingAddress.extension_attributes === 'object' &&
                shippingAddress.extension_attributes.hasOwnProperty('point_id') &&
                !!shippingAddress.extension_attributes.point_id;

            if (!this._super()) {
                return false;
            }
            if (quote.shippingMethod().carrier_code === point().methodCode) {
                var selectedPoint = JSON.parse(window.localStorage.getItem('selected_point'));
                if (!hasPointId && !pointAddress) {
                    this.lockerErrors($t('Please pickup a point.'));
                    return false;
                }
                this.getChild('redbox-shipping-method').selectedPointAddress(pointAddress);
                if (!shippingAddress.hasOwnProperty('extension_attributes')) {
                    Object.defineProperty(shippingAddress, 'extension_attributes', {
                        value: {},
                        writable: true,
                        enumerable: true,
                        configurable: true
                    });
                }
    
                if (!shippingAddress.extension_attributes.hasOwnProperty('point_id')) {
                    Object.defineProperty(shippingAddress.extension_attributes, 'point_id', {
                        writable: true,
                        enumerable: true,
                        configurable: true
                    });
                }
                shippingAddress.extension_attributes.point_id = selectedPoint.id;
            } else {
                if (shippingAddress.hasOwnProperty('extension_attributes') &&
                    typeof shippingAddress.extension_attributes === 'object' &&
                    shippingAddress.extension_attributes.hasOwnProperty('point_id')) {
                    shippingAddress.extension_attributes.point_id = null;
                }
                if (window.localStorage.getItem('selected_point')) {
                    window.localStorage.removeItem('selected_point');
                }
            }
            return true;
        },

        selectShippingMethod: function (shippingMethod) {
            var shippingAddress = quote.shippingAddress();
            var hasPointId = shippingAddress.hasOwnProperty('extension_attributes') &&
                typeof shippingAddress.extension_attributes === 'object' &&
                shippingAddress.extension_attributes.hasOwnProperty('point_id') &&
                !!shippingAddress.extension_attributes.point_id;
            if (shippingMethod.available && shippingMethod.carrier_code === 'redbox' && !hasPointId) {
                $('.choose-locker').trigger('click');
            }
            return this._super();
        }
    };

    return function (target) {
        return target.extend(mixin);
    };
});
