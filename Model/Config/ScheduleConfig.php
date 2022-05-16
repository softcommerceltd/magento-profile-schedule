<?php
/**
 * Copyright © Soft Commerce Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace SoftCommerce\ProfileSchedule\Model\Config;

use SoftCommerce\ProfileConfig\Model\AbstractConfig;

/**
 * @inheritDoc
 */
class ScheduleConfig extends AbstractConfig implements ScheduleConfigInterface
{
    /**
     * @inheritDoc
     */
    public function isActive(): bool
    {
        return (bool) $this->getConfig($this->getTypeId() . self::XML_PATH_STATUS);
    }

    /**
     * @inheritDoc
     */
    public function getScheduleId(): ?int
    {
        return (int) $this->getConfig($this->getTypeId() . self::XML_PATH_SCHEDULE_ID) ?: null;
    }

    /**
     * @inheritDoc
     */
    public function getProcessBatchSize(): ?int
    {
        return (int) $this->getConfig($this->getTypeId() . self::XML_PATH_PROCESS_BATCH_SIZE) ?: null;
    }

    /**
     * @inheritDoc
     */
    public function isActiveHistory(): bool
    {
        return (bool) $this->getConfig($this->getTypeId() . self::XML_PATH_ENABLE_HISTORY);
    }
}
