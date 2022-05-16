<?php
/**
 * Copyright © Soft Commerce Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace SoftCommerce\ProfileSchedule\Model;

/**
 * Interface GetScheduleDataInterface used to
 * provide schedule data in array format.
 */
interface GetScheduleDataInterface
{
    /**
     * @return array
     */
    public function execute(): array;

    /**
     * @param string $searchKey
     * @param string|array|mixed $searchValue
     * @return array
     */
    public function applySearchCriteria(string $searchKey, $searchValue): array;
}
