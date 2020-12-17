<?php
/**
 * (c) Redbox Parcel Lockers <thamer@redboxsa.com>
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * Built by Redbox Technologies, <thamer@redboxsa.com>
 */

namespace Redbox\Shipping\Block\Adminhtml\Order;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Sales\Helper\Admin;
use Redbox\Shipping\Api\Data\AddressRepositoryInterface;

class AbstractOrder extends \Magento\Sales\Block\Adminhtml\Order\AbstractOrder
{

    public $pointId;
    public $order;
    public $quoteFactory;
    public $addressRepository;
    public $shippingAddress;

    public function __construct(
        Context $context,
        Registry $registry,
        Admin $adminHelper,
        AddressRepositoryInterface $addressRepository,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        array $data=[]
    ) {
        $this->_adminHelper  = $adminHelper;
        $this->_coreRegistry = $registry;
        $this->quoteFactory = $quoteFactory;
        $this->addressRepository = $addressRepository;
        parent::__construct($context, $registry, $adminHelper, $data);

    }//end __construct()


    public function isShippingRedbox()
    {
        $this->order = $this->getOrder();
        if ($this->order->getId()) {
            if ($this->order->getShippingMethod() == 'redbox_redbox') {
                $quote = $this->quoteFactory->create()->loadByIdWithoutStore($this->order->getQuoteId());
                $this->shippingAddress = $this->order->getShippingAddress();
                $quoteAddressId = $quote->getShippingAddress()->getId();
                if ($quoteAddressId) {
                    $this->pointId = $this->addressRepository->getByQuoteAddressId($quoteAddressId)->getPointId();
                    if ($this->pointId) {
                        return true;
                    }
                }
                return true;
            }
        }

        return false;

    }//end isShippingRedbox()


    public function getPointId()
    {
        return $this->pointId;

    }//end getPoint()


    public function getPointAddress()
    {
        if ($this->pointId) {
            return sprintf(
                '%s, %s, %s, %s',
                $this->shippingAddress->getStreet()[0],
                $this->shippingAddress->getCompany(),
                $this->shippingAddress->getCity(),
                $this->shippingAddress->getPostCode()
            );
        }

        return '';

    }//end getPointAddress()


}//end class
