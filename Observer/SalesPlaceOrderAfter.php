<?php
/**
 * (c) Redbox Parcel Lockers <thamer@redboxsa.com>
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * Built by Redbox Technologies, <thamer@redboxsa.com>
 */

namespace Redbox\Shipping\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Redbox\Shipping\Api\Checkout\AddressRepositoryInterface;

class SalesPlaceOrderAfter implements ObserverInterface
{
    private $addressRepository;
    private $quoteFactory;
    private $helper;
    private $collection;

    public function __construct(
        AddressRepositoryInterface $addressRepository,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Redbox\Shipping\Helper\Lockers $helper,
        \Redbox\Shipping\Model\ResourceModel\Machine\DataCollection $collection
    ) {

        $this->helper = $helper;
        $this->addressRepository = $addressRepository;
        $this->quoteFactory = $quoteFactory;
        $this->collection = $collection;
    }

    public function execute(Observer $observer)
    {
        $order = $observer->getOrder();
        if ($order->getShippingMethod() == 'redbox_shipping' && $this->helper->isActive()) {
            $quote = $this->quoteFactory->create()->loadByIdWithoutStore($order->getQuoteId());
            $quoteAddressId = $quote->getShippingAddress()->getId();
            if ($quoteAddressId) {
                $shippingAddress = $order->getShippingAddress();
                $machineId = $this->addressRepository->getByQuoteAddressId($quoteAddressId)->getLockerMachine();
                $locker = $this->collection
                    ->addFieldToFilter('id', $machineId)
                    ->setPageSize(1, 1)
                    ->getLastItem();
                if ($locker->getId()) {
                    $shippingAddress->setPostcode($locker->getPostCode());
                    $shippingAddress->setStreet(
                        [
                            $locker->getStreet(),
                            "Locker ID ($machineId)"
                        ]
                    );
                    $shippingAddress->setCity($locker->getCity());
                    $shippingAddress->setCustomerAddressId(null);

                    if ($inpostPhone = $quote->getInpostPhone()) {
                        $shippingAddress->setTelephone($inpostPhone);
                    }

                    $shippingAddress->setCompany($locker->getBuildingNo());
                    $shippingAddress->save();
                }
            }
        }
    }
}
