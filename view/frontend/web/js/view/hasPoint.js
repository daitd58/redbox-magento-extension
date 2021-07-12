define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/additional-validators',
        'Redbox_Shipping/js/model/hasPoint'
    ],
    function (Component, additionalValidators, redboxValidation) {
        'use strict';
        additionalValidators.registerValidator(redboxValidation);
        return Component.extend({});
    }
);