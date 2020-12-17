<?php
/**
  * (c) Redbox Parcel Lockers <thamer@redboxsa.com>
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * Built by Redbox Technologies, <thamer@redboxsa.com>
 */

namespace Redbox\Shipping\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\Data\AddressExtensionInterface;
use Magento\Quote\Api\Data\AddressExtensionInterfaceFactory;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote\Address;
use Psr\Log\LoggerInterface as PsrLoggerInterface;
use Redbox\Shipping\Api\Data\AddressRepositoryInterface;
use Redbox\Shipping\Api\Data\AddressInterface;
use Redbox\Shipping\Model\Carrier\Redbox as Carrier;

/**
 * Class LoadShippingAddressObserver
 * @package Redbox\Shipping\Observer
 */
class LoadShippingAddressObserver implements ObserverInterface
{
    /**
     * @var AddressRepositoryInterface
     */
    private $addressRepository;

    /**
     * @var AddressExtensionInterfaceFactory
     */
    private $addressExtensionFactory;

    private $logger;

    /**
     * SaveCheckoutFieldsObserver constructor.
     * @param AddressRepositoryInterface $addressRepository
     * @param AddressExtensionInterfaceFactory $addressExtensionFactory
     */
    public function __construct(
        AddressRepositoryInterface $addressRepository,
        AddressExtensionInterfaceFactory $addressExtensionFactory,
        PsrLoggerInterface $logger
    ) {
        $this->logger = $logger;
        $this->logger->info('LoadShippingAddressObserver------');
        $this->addressRepository = $addressRepository;
        $this->addressExtensionFactory = $addressExtensionFactory;
    }

    /**
     * Load quote shipping address locker machine identifier
     * Triggered by:
     *      - sales_quote_address_load_after
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $this->logger->info('LoadShippingAddressObserver------');
        if ($observer->hasData('shipping_assignment')) {
            /** @var ShippingAssignmentInterface $shippingAssignment */
            $shippingAssignment = $observer->getData('shipping_assignment');
            $quoteAddress = $shippingAssignment->getShipping()->getAddress();
        } elseif ($observer->hasData('quote_address')) {
            $quoteAddress = $observer->getData('quote_address');
        }

        /** @var Address $quoteAddress */
        if (!$quoteAddress || $quoteAddress->getAddressType() !== Address::ADDRESS_TYPE_SHIPPING
            || $quoteAddress->getShippingMethod() !== Carrier::CODE . '_' . Carrier::CODE
        ) {
            return;
        }

        try {
            /** @var AddressInterface $address */
            $address = $this->addressRepository->getByQuoteAddressId($quoteAddress->getId());
        } catch (NoSuchEntityException $e) {
            return;
        }

        $extensionAttributes = $quoteAddress->getExtensionAttributes();
        if (!($extensionAttributes instanceof AddressExtensionInterface)) {
            $extensionAttributes = $this->addressExtensionFactory->create();
        }

        $extensionAttributes->setPointId($address->getPointId());
        $quoteAddress->setExtensionAttributes($extensionAttributes);
    }
}
