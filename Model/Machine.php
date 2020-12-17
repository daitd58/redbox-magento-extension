<?php
/**
 * (c) Redbox Parcel Lockers <thamer@redboxsa.com>
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * Built by Redbox Technologies, <thamer@redboxsa.com>
 */

namespace Redbox\Shipping\Model;

class Machine extends \Magento\Framework\Model\AbstractModel implements
    MachineModelInterface,
    \Magento\Framework\DataObject\IdentityInterface
{

    const CACHE_TAG = 'inpost_lockers_machine';

    public function _construct()
    {
        $this->_init(\Redbox\Shipping\Model\ResourceModel\Machine::class);
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function updateAttributes()
    {
        $this->save();
    }
}
