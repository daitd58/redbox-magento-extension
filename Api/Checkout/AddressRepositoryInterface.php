<?php
/**
 * (c) Redbox Parcel Lockers <thamer@redboxsa.com>
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * Built by Redbox Technologies, <thamer@redboxsa.com>
 */

namespace Redbox\Shipping\Api\Checkout;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Redbox\Shipping\Api\Data\Checkout\AddressInterface;

/**
 * Interface AddressRepositoryInterface
 * @package Redbox\Shipping\Api\Checkout
 */
interface AddressRepositoryInterface
{
    /**
     * Load address by entity id
     *
     * @param string|int $addressId
     * @return AddressInterface
     * @throws NoSuchEntityException
     */
    public function getById($addressId);

    /**
     * Load address by quote address id
     *
     * @param string|int $quoteAddressId
     * @return AddressInterface
     * @throws NoSuchEntityException
     */
    public function getByQuoteAddressId($quoteAddressId);

    /**
     * Save address
     *
     * @param AddressInterface $address
     * @return AddressInterface
     * @throws CouldNotSaveException
     */
    public function save(AddressInterface $address);
}
