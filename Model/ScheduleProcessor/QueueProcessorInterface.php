<?php
/**
 * Copyright © Soft Commerce Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace SoftCommerce\ProfileSchedule\Model\ScheduleProcessor;

use Magento\Framework\Exception\LocalizedException;
use SoftCommerce\Core\Framework\MessageStorageInterface;

/**
 * Interface QueueProcessorInterface used
 * to process scheduled tasks
 */
interface QueueProcessorInterface
{
    /**
     * @param int $profileId
     * @return MessageStorageInterface
     * @throws LocalizedException
     */
    public function execute(int $profileId): MessageStorageInterface;
}
