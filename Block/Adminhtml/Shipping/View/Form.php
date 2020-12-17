<?php
/**
 * (c) Redbox Parcel Lockers <thamer@redboxsa.com>
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * Built by Redbox Technologies, <thamer@redboxsa.com>
 */

namespace Redbox\Shipping\Block\Adminhtml\Shipping\View;

use Magento\Backend\Block\Widget\Button;

class Form extends \Magento\Shipping\Block\Adminhtml\View\Form
{


    public function getCreateLabelButton()
    {
        if ($this->getShipment()->getOrder()->getShippingMethod() !== 'redbox_redbox') {
            $data['shipment_id'] = $this->getShipment()->getId();
            $url                 = $this->getUrl('adminhtml/order_shipment/createLabel', $data);
            return $this->getLayout()->createBlock(
                Button::class
            )->setData(
                [
                    'label'   => __('Create Shipping Label...'),
                    'onclick' => 'packaging.showWindow();',
                    'class'   => 'action-create-label',
                ]
            )->toHtml();
        }

    }//end getCreateLabelButton()


}//end class
