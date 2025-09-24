<?php
/**
 * Copyright © Soft Commerce Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace SoftCommerce\ProfileSchedule\Model\Config;

use Magento\Framework\Exception\LocalizedException;
use SoftCommerce\Profile\Model\Config\ConfigInterface;

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
    public const XML_PATH_ENABLE_1TIME_FULL_PROCESS = '/schedule_config/enable_onetime_full_process';
    public const XML_PATH_1TIME_FULL_PROCESS_FREQUENCY = '/schedule_config/onetime_full_process_frequency';
    public const XML_PATH_1TIME_FULL_PROCESS_TIME = '/schedule_config/onetime_full_process_time';

    // Cache Keys
    public const CACHE_KEY_ONETIME_PROCESS = 'softcommerce_profile_schedule_onetime_process';

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

    /**
     * @return bool
     * @throws LocalizedException
     */
    public function isActiveOnetimeProcess(): bool;

    /**
     * @return string|null
     * @throws LocalizedException
     */
    public function getOnetimeProcessFrequency(): ?string;

    /**
     * @param string|null $metadata
     * @return array|string|bool|null
     * @throws LocalizedException
     */
    public function getOnetimeProcessInstructions(?string $metadata = null): array|string|bool|null;

    /**
     * @param string $timestamp
     * @param bool $canProcessFlag
     * @return void
     * @throws LocalizedException
     */
    public function saveOnetimeProcessInstructions(string $timestamp, bool $canProcessFlag = false): void;

    /**
     * @return string|null
     * @throws LocalizedException
     */
    public function getOnetimeProcessNextTimestamp(): ?string;

    /**
     * @return bool
     * @throws LocalizedException
     */
    public function canRunOnetimeProcess(): bool;
}
