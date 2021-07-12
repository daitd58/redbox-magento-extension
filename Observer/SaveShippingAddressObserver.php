<?php
/**
  * (c) Redbox Parcel Lockers <thamer@redboxsa.com>
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * Built by Redbox Technologies, <thamer@redboxsa.com>
 */

namespace Redbox\Shipping\Observer;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\Quote\Address;
use Psr\Log\LoggerInterface as PsrLoggerInterface;
use Redbox\Shipping\Api\Data\AddressRepositoryInterface;
use Redbox\Shipping\Model\Carrier\Redbox as Carrier;
use Redbox\Shipping\Api\Data\AddressInterfaceFactory;

/**
 * Class SaveShippingAddressObserver
 * @package Redbox\Shipping\Observer
 */
class SaveShippingAddressObserver implements ObserverInterface
{

    /**
     * @var AddressRepositoryInterface
     */
    private $addressRepository;

    /**
     * @var AddressInterfaceFactory
     */
    private $addressFactory;

    /** @var RequestInterface  */
    private $request;

    private $logger;

    /**
     * SaveShippingAddressObserver constructor
     * @param AddressRepositoryInterface $addressRepository
     * @param RequestInterface $request
     */
    public function __construct(
        AddressRepositoryInterface $addressRepository,
        RequestInterface $request,
        AddressInterfaceFactory $addressFactory,
        PsrLoggerInterface $logger
    ) {
        $this->logger = $logger;
        $this->logger->info('SaveShippingAddressObserver------');
        $this->addressRepository = $addressRepository;
        $this->addressFactory = $addressFactory;
        $this->request = $request;
    }

    /**
     * Save quote shipping address locker machine identifier
     * Triggered by:
     *      - sales_quote_address_save_after
     *
     * @param Observer $observer
     * @throws CouldNotSaveException
     * @throws LocalizedException
     */
    public function execute(Observer $observer)
    {
        $this->logger->info('SaveShippingAddressObserver------');
        /** @var Address $quoteAddress */
        $quoteAddress = $observer->getData('quote_address');

        if ($quoteAddress->getAddressType() !== Address::ADDRESS_TYPE_SHIPPING
            || $quoteAddress->getShippingMethod() !== Carrier::CODE . '_' . Carrier::CODE
        ) {
            return;
        }

        if (!$quoteAddress->getExtensionAttributes()
            || !$quoteAddress->getExtensionAttributes()->getPointId()
        ) {
            return;
        }
        $extensionAttributes = $quoteAddress->getExtensionAttributes();
        $pointId = $extensionAttributes->getPointId();
        $this->logger->info('$pointId------' . $pointId);

        try {
            $address = $this->addressRepository->getByQuoteAddressId($quoteAddress->getId());
        } catch (NoSuchEntityException $e) {
            $address = $this->addressFactory->create();
            $address->setShippingAddressId($quoteAddress->getId());
        }

        $address->setPointId($pointId);
        $this->addressRepository->save($address);
    }
}
