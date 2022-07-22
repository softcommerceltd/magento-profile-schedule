<?php
/**
 * Copyright © Soft Commerce Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace SoftCommerce\ProfileSchedule\Model\Config;

use Magento\Framework\Exception\LocalizedException;
use SoftCommerce\ProfileConfig\Model\ConfigInterface;

/**
 * Interface ScheduleConfigInterface used to provide
 * schedule data that's assigned to profile configuration.
 */
interface ScheduleConfigInterface extends ConfigInterface
{
    public const ENTITY = 'schedule';

    // config paths
    public const XML_PATH_STATUS = '/schedule_config/status';
    public const XML_PATH_SCHEDULE_ID = '/schedule_config/schedule_id';
    public const XML_PATH_PROCESS_BATCH_SIZE = '/schedule_config/process_batch_size';
    public const XML_PATH_RETRY_ON_ERROR = '/schedule_config/retry_on_error';
    public const XML_PATH_ENABLE_HISTORY = '/schedule_config/enable_history';

    /**
     * @return bool
     * @throws LocalizedException
     */
    public function isActive(): bool;

    /**
     * @return int|null
     * @throws LocalizedException
     */
    public function getScheduleId(): ?int;

    /**
     * @return int|null
     * @throws LocalizedException
     */
    public function getProcessBatchSize(): ?int;

    /**
     * @return bool
     * @throws LocalizedException
     */
    public function isActiveHistory(): bool;

    /**
     * @return bool
     * @throws LocalizedException
     */
    public function shouldRetryOnError(): bool;
}
