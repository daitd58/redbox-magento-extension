<?php
/**
 * (c) Redbox Parcel Lockers <thamer@redboxsa.com>
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * Built by Redbox Technologies, <thamer@redboxsa.com>
 */

namespace Redbox\Shipping\Block\Email;

use Magento\Framework\View\Element\Template;
use Redbox\Shipping\Api\Checkout\AddressRepositoryInterface;

class Details extends Template
{
    /** @var \Magento\Quote\Model\QuoteFactory  */
    private $quoteFactory;
    /** @var AddressRepositoryInterface  */
    private $addressRepository;
    /** @var \Redbox\Shipping\Model\ResourceModel\Machine\DataCollection  */
    private $collection;

    public function __construct(
        Template\Context $context,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        AddressRepositoryInterface $addressRepository,
        \Redbox\Shipping\Model\ResourceModel\Machine\DataCollection $collection,
        array $data = []
    ) {
    
        parent::__construct($context, $data);
        $this->quoteFactory = $quoteFactory;
        $this->collection = $collection;
        $this->addressRepository = $addressRepository;
    }

    public function isInpost()
    {
        if ($this->getOrder()->getShippingMethod() == 'redbox_shipping') {
            return true;
        }
        return false;
    }

    public function getLocker()
    {
        $quote = $this->quoteFactory->create()->loadByIdWithoutStore($this->getOrder()->getQuoteId());
        $quoteAddressId = $quote->getShippingAddress()->getId();
        if ($quoteAddressId) {
            $machineId = $this->addressRepository->getByQuoteAddressId($quoteAddressId)->getLockerMachine();
            $locker = $this->collection
                ->addFieldToFilter('id', $machineId)
                ->setPageSize(1, 1)
                ->getLastItem();
            ;
            return $locker;
        }
    }
}
