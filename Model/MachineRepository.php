<?php
/**
 * (c) Redbox Parcel Lockers <thamer@redboxsa.com>
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * Built by Redbox Technologies, <thamer@redboxsa.com>
 */

namespace Redbox\Shipping\Model;

use Magento\Framework\Api\CriteriaInterface;
use Magento\Framework\DB\QueryBuilderFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Redbox\Shipping\Api\Data\MachineInterface;
use Redbox\Shipping\Api\Data\MachineInterfaceFactory;
use Redbox\Shipping\Api\MachineRepositoryInterface;
use Redbox\Shipping\Model\ResourceModel\Machine as ResourceMachine;
use Redbox\Shipping\Model\ResourceModel\Machine\CollectionFactory;

/**
 * Class MachineRepository
 * @package Redbox\Shipping\Model
 */
class MachineRepository implements MachineRepositoryInterface
{
    /**
     * @var ResourceMachine
     */
    private $resource;

    /**
     * @var MachineInterfaceFactory
     */
    private $machineFactory;

    /**
     * @var CollectionFactory
     */
    private $machineCollectionFactory;

    /**
     * @var QueryBuilderFactory
     */
    private $queryBuilderFactory;

    /**
     * MachineRepository constructor
     *
     * @param ResourceMachine $resource
     * @param MachineInterfaceFactory $machineFactory
     * @param CollectionFactory $machineCollectionFactory
     * @param QueryBuilderFactory $queryBuilderFactory
     */
    public function __construct(
        ResourceMachine $resource,
        MachineInterfaceFactory $machineFactory,
        CollectionFactory $machineCollectionFactory,
        QueryBuilderFactory $queryBuilderFactory
    ) {
        $this->resource = $resource;
        $this->machineFactory = $machineFactory;
        $this->machineCollectionFactory = $machineCollectionFactory;
        $this->queryBuilderFactory = $queryBuilderFactory;
    }

    /**
     * Save machine
     *
     * @param MachineInterface $machine
     * @return MachineInterface
     * @throws CouldNotSaveException
     */
    public function save(MachineInterface $machine)
    {
        try {
            $this->resource->save($machine);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $machine;
    }

    /**
     * Retrieve machine
     *
     * @param $machineId
     * @return MachineInterface|mixed
     * @throws NoSuchEntityException
     */
    public function getById($machineId)
    {
        $machine = $this->machineFactory->create();
        $this->resource->load($machine, $machineId);

        if (!$machine->getId()) {
            throw new NoSuchEntityException(__('InPost machine with id "%1" does not exist.', $machineId));
        }

        return $machine;
    }

    /**
     * Retrieve machines matching the specified criteria
     *
     * @param CriteriaInterface $criteria
     */
    public function getList(CriteriaInterface $criteria)
    {
        $queryBuilder = $this->queryBuilderFactory->create();
        $queryBuilder->setCriteria($criteria);
        $queryBuilder->setResource($this->resource);
        $query = $queryBuilder->create();
        $collection = $this->machineCollectionFactory->create(['query' => $query]);

        return $collection;
    }
}
