<?php
/**
 * Copyright © Soft Commerce Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace SoftCommerce\ProfileSchedule\Model;

use Magento\Framework\Exception\LocalizedException;

/**
 * Interface GetProfileIdByScheduleInterface used to
 * obtain profile ID(s) data in array format.
 */
interface GetProfileIdByScheduleInterface
{
    public const PROFILE_ID = 'profile_id';
    public const SCHEDULE_ID = 'schedule_id';
    public const TYPE_ID = 'type_id';

    /**
     * @param string $typeId
     * @param int|null $scheduleId
     * @return array
     * @throws LocalizedException
     */
    public function execute(string $typeId, ?int $scheduleId = null): array;
}
