<?php
/**
 * (c) Redbox Parcel Lockers <thamer@redboxsa.com>
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * Built by Redbox Technologies, <thamer@redboxsa.com>
 */

namespace Redbox\Shipping\Model\Checkout;

use Magento\Framework\Model\AbstractModel;
use Redbox\Shipping\Api\Data\Checkout\AddressInterface;
use Redbox\Shipping\Model\ResourceModel\Checkout\Address as ResourceAddress;

/**
 * Class Address
 * @package Redbox\Shipping\Model\Checkout
 */
class Address extends AbstractModel implements AddressInterface
{
    // @codingStandardsIgnoreStart
    /**
     * @var string
     */
    public $_eventPrefix = 'inpost_checkout_address';

    /**
     * @var string
     */
    public $_eventObject = 'checkout_address';
    // @codingStandardsIgnoreEnd

    /**
     * Initialize resource model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(ResourceAddress::class);
        parent::_construct();
    }

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->_getData(static::ENTITY_ID);
    }

    /**
     * @return int|null
     */
    public function getShippingAddressId()
    {
        return $this->_getData(static::SHIPPING_ADDRESS_ID);
    }

    /**
     * @return string|null
     */
    public function getLockerMachine()
    {
        return $this->_getData(static::LOCKER_MACHINE);
    }

    /**
     * @param int $entityId
     * @return AddressInterface
     */
    public function setId($entityId)
    {
        return $this->setData(static::ENTITY_ID, $entityId);
    }

    /**
     * @param int $addressId
     * @return AddressInterface
     */
    public function setShippingAddressId($addressId)
    {
        return $this->setData(static::SHIPPING_ADDRESS_ID, $addressId);
    }

    /**
     * @param string $lockerMachine
     * @return AddressInterface
     */
    public function setLockerMachine($lockerMachine)
    {
        return $this->setData(static::LOCKER_MACHINE, $lockerMachine);
    }
}
