<?php
/**
 * Copyright © Soft Commerce Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace SoftCommerce\ProfileSchedule\Model;

/**
 * Interface IsActiveScheduleInterface used to
 * provide status of scheduler.
 */
interface IsActiveScheduleInterface
{
    /**
     * @param string $typeId
     * @return bool
     */
    public function execute(string $typeId): bool;
}
