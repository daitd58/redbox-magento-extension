<?php
/**
 * (c) Redbox Parcel Lockers <thamer@redboxsa.com>
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * Built by Redbox Technologies, <thamer@redboxsa.com>
 */

namespace Redbox\Shipping\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\Api\CriteriaInterfaceFactory;
use Redbox\Shipping\Api\MachineRepositoryInterface;
use Redbox\Shipping\Api\Data\MachineInterface;

class LockerMachines extends Template
{
    /**
     * @var MachineRepositoryInterface
     */
    private $machineRepository;

    /**
     * @var CriteriaInterfaceFactory
     */
    private $criteriaFactory;

    /**
     * LockerMachines constructor
     *
     * @param Template\Context $context
     * @param MachineRepositoryInterface $machineRepository
     * @param CriteriaInterfaceFactory $scriteriaFactory
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        MachineRepositoryInterface $machineRepository,
        CriteriaInterfaceFactory $criteriaFactory,
        array $data = []
    ) {
        $this->machineRepository = $machineRepository;
        $this->criteriaFactory = $criteriaFactory;

        parent::__construct($context, $data);
    }

    /**
     * @return MachineInterface[]
     */
    public function getMachines()
    {
        $criteria = $this->criteriaFactory->create();
        $criteria->setNearestCoordinateFilter(
            ['latitude' => '51.635110', 'longitude' => '-0.053000']        );

        $result = $this->machineRepository->getList($criteria);

        return $result->getItems();
    }
}
