<?php
/**
 * Copyright © Soft Commerce Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace SoftCommerce\ProfileSchedule\Model;

use Magento\Framework\Exception\LocalizedException;

/**
 * Interface ScheduleProcessorInterface used
 * to process cron scheduled tasks.
 */
interface ScheduleProcessorInterface
{
    /**
     * @param string $typeId
     * @return void
     * @throws LocalizedException
     */
    public function execute(string $typeId): void;
}
