<?php
/**
 * (c) Redbox Parcel Lockers <thamer@redboxsa.com>
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * Built by Redbox Technologies, <thamer@redboxsa.com>
 */

namespace Redbox\Shipping\Api;

use Magento\Framework\Api\CriteriaInterface;
use Magento\Framework\Exception\LocalizedException;
use Redbox\Shipping\Api\Data\MachineInterface;

interface MachineRepositoryInterface
{
    /**
     * Save machine
     *
     * @param MachineInterface $machine
     * @return MachineInterface
     * @throws LocalizedException
     */
    public function save(MachineInterface $machine);

    /**
     * Retrieve machine
     *
     * @param $machineId
     * @return mixed
     */
    public function getById($machineId);

    /**
     * Retrieve machines matching the specified criteria
     *
     * @param CriteriaInterface $criteria
     * @throws LocalizedException
     */
    public function getList(CriteriaInterface $criteria);
}
