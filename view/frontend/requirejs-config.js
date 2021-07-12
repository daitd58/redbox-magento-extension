/**
 * (c) Redbox Parcel Lockers <thamer@redboxsa.com>
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * Built by Redbox Technologies, <thamer@redboxsa.com>
 */

var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/view/shipping': {
                'Redbox_Shipping/js/view/shipping/mixin': true
            }
        }
    },
    map: {
        '*': {
            'Magento_Checkout/js/view/shipping-information/list': 'Redbox_Shipping/js/view/shipping-information/list'
        }
    }
};
