<?php
/**
 * Copyright © Soft Commerce Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace SoftCommerce\ProfileSchedule\Model\Schedule;

use Magento\Framework\Exception\CronException;

/**
 * @inheritDoc
 */
class CronExpressionValidator implements CronExpressionValidatorInterface
{
    /**
     * @inheritDoc
     */
    public function isValid(string $cronExpression): bool
    {
        $e = preg_split('#\s+#', $cronExpression, 0, PREG_SPLIT_NO_EMPTY);
        if (count($e) < 5 || count($e) > 6) {
            throw new CronException(__('Invalid cron expression: %1', $cronExpression));
        }
        return true;
    }
}
