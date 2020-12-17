<?php
/**
 * (c) Redbox Parcel Lockers <thamer@redboxsa.com>
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * Built by Redbox Technologies, <thamer@redboxsa.com>
 */

namespace Redbox\Shipping\Block;

class Success extends \Magento\Checkout\Block\Onepage\Success
{
    public function isInpost()
    {
        $order = $this->_checkoutSession->getLastRealOrder();
        if ($order->getShippingMethod() == 'redbox_shipping') {
            return true;
        }
        return false;
    }
}
