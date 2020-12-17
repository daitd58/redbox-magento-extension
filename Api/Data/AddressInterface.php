<?php
/**
  * (c) Redbox Parcel Lockers <thamer@redboxsa.com>
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * Built by Redbox Technologies, <thamer@redboxsa.com>
 */

namespace Redbox\Shipping\Api\Data;

interface AddressInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const ENTITY_ID = 'entity_id';
    const SHIPPING_ADDRESS_ID = 'shipping_address_id';
    const POINT_ID = 'point_id';
    /**#@-*/

    /**
     * @return int|null
     */
    public function getId();

    /**
     * @return int|null
     */
    public function getShippingAddressId();

    /**
     * @return string|null
     */
    public function getPointId();

    /**
     * @param int $entityId
     * @return AddressInterface
     */
    public function setId($entityId);

    /**
     * @param int $addressId
     * @return AddressInterface
     */
    public function setShippingAddressId($addressId);

    /**
     * @param string $pointId
     * @return AddressInterface
     */
    public function setPointId($pointId);
}
