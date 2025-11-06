<?php
/**
 * Copyright © Soft Commerce Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace SoftCommerce\ProfileSchedule\Model;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchResults;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use SoftCommerce\ProfileSchedule\Api\Data\ScheduleInterface;
use SoftCommerce\ProfileSchedule\Api\Data\ScheduleSearchResultsInterface;
use SoftCommerce\ProfileSchedule\Api\Data\ScheduleSearchResultsInterfaceFactory;
use SoftCommerce\ProfileSchedule\Api\ScheduleRepositoryInterface;

/**
 * @inheritDoc
 */
class ScheduleRepository implements ScheduleRepositoryInterface
{
    /**
     * @param ResourceModel\Schedule $resource
     * @param ResourceModel\Schedule\CollectionFactory $collectionFactory
     * @param ScheduleFactory $modelFactory
     * @param ScheduleSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        private readonly ResourceModel\Schedule $resource,
        private readonly ResourceModel\Schedule\CollectionFactory $collectionFactory,
        private readonly ScheduleFactory $modelFactory,
        private readonly ScheduleSearchResultsInterfaceFactory $searchResultsFactory,
        private readonly CollectionProcessorInterface $collectionProcessor,
        private readonly SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getList(?SearchCriteriaInterface $searchCriteria = null)
    {
        if (null === $searchCriteria) {
            $searchCriteria = $this->searchCriteriaBuilder->create();
        }

        /** @var ResourceModel\Schedule\Collection $collection */
        $collection = $this->collectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var ScheduleSearchResultsInterface|SearchResults $searchResults */
        $searchResult = $this->searchResultsFactory->create();
        $searchResult->setSearchCriteria($searchCriteria);
        $searchResult->setItems($collection->getItems());
        $searchResult->setTotalCount($collection->getSize());

        return $searchResult;
    }

    /**
     * @inheritDoc
     */
    public function get($entityId, ?string $field = null)
    {
        /** @var ScheduleInterface|Schedule $model */
        $model = $this->modelFactory->create();
        $this->resource->load($model, $entityId, $field);
        if (!$model->getId()) {
            throw new NoSuchEntityException(__('The entity with ID "%1" doesn\'t exist.', $entityId));
        }

        return $model;
    }

    /**
     * @inheritDoc
     */
    public function getByTypeId(string $typeId)
    {
        /** @var ScheduleInterface|Schedule $model */
        $model = $this->modelFactory->create();
        $this->resource->load($model, $typeId, ScheduleInterface::TYPE_ID);
        if (!$model->getId()) {
            throw new NoSuchEntityException(__('The entity with type ID "%1" doesn\'t exist.', $typeId));
        }

        return $model;
    }

    /**
     * @inheritDoc
     */
    public function save(ScheduleInterface $schedule)
    {
        if (!$this->canSave($schedule)) {
            throw new AlreadyExistsException(
                __('Schedule type: "%1" already exists.', $schedule->getTypeId())
            );
        }

        try {
            $this->resource->save($schedule);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $schedule;
    }

    /**
     * @inheritDoc
     */
    public function delete(ScheduleInterface $model)
    {
        try {
            $this->resource->delete($model);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById($entityId)
    {
        return $this->delete($this->get($entityId));
    }

    /**
     * @param ScheduleInterface|Schedule $schedule
     * @return bool
     * @throws LocalizedException
     */
    private function canSave(ScheduleInterface $schedule): bool
    {
        return $schedule->getOrigData(ScheduleInterface::ENTITY_ID)
            || !$this->resource->getByTypeId($schedule->getTypeId());
    }
}
