<?php
/**
 * (c) Redbox Parcel Lockers <thamer@redboxsa.com>
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * Built by Redbox Technologies, <thamer@redboxsa.com>
 */

namespace Redbox\Shipping\Model\ResourceModel;

class Machine extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    public function _construct()
    {
        $this->_init('inpost_machine', 'machine_id');
    }

    public function removeMachineById($machine)
    {
        if ($machine->getId()) {
            $this->delete($machine);
        }
    }
}
