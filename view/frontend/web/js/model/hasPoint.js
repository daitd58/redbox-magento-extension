define(
    [
        'Magento_Checkout/js/model/quote',
        'Redbox_Shipping/js/view/point',
        'mage/validation'
    ],
    function (quote, point) {
        'use strict';

        return {

            /**
             * Validate checkout agreements
             *
             * @returns {Boolean}
             */
            validate: function () {
                var shippingAddress = quote.shippingAddress();
                var hasPointId = shippingAddress.hasOwnProperty('extension_attributes') &&
                    typeof shippingAddress.extension_attributes === 'object' &&
                    shippingAddress.extension_attributes.hasOwnProperty('point_id') &&
                    !!shippingAddress.extension_attributes.point_id;

                if (quote.shippingMethod() && quote.shippingMethod().carrier_code === point().methodCode) {
                    var selectedPoint = JSON.parse(window.localStorage.getItem('selected_point'));
                    if (!hasPointId || !selectedPoint) {
                        return false;
                    }
                }
                return true;
            }
        };
    }
);
