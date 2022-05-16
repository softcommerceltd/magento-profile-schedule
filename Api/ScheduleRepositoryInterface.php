<?php
/**
 * Copyright © Soft Commerce Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace SoftCommerce\ProfileSchedule\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResults;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use SoftCommerce\ProfileSchedule\Api\Data\ScheduleInterface;
use SoftCommerce\ProfileSchedule\Api\Data\ScheduleSearchResultsInterface;
use SoftCommerce\ProfileSchedule\Model\Schedule;

/**
 * Interface ScheduleRepositoryInterface
 * used to provide profile schedule entity data.
 */
interface ScheduleRepositoryInterface
{
    /**
     * @param SearchCriteriaInterface|null $searchCriteria
     * @return ScheduleSearchResultsInterface|SearchResults
     */
    public function getList(?SearchCriteriaInterface $searchCriteria = null);

    /**
     * @param int $entityId
     * @param string|null $field
     * @return ScheduleInterface
     * @throws NoSuchEntityException
     */
    public function get($entityId, ?string $field = null);

    /**
     * @param string $typeId
     * @return ScheduleInterface|Schedule
     * @throws NoSuchEntityException
     */
    public function getByTypeId(string $typeId);

    /**
     * @param ScheduleInterface $schedule
     * @return ScheduleInterface
     * @throws AlreadyExistsException
     * @throws CouldNotSaveException
     * @throws LocalizedException
     */
    public function save(ScheduleInterface $schedule);

    /**
     * @param ScheduleInterface $model
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(ScheduleInterface $model);

    /**
     * @param int $entityId
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById($entityId);
}
