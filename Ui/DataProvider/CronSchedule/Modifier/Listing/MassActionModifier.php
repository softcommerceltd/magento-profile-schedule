<?php
/**
 * Copyright © Soft Commerce Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace SoftCommerce\ProfileSchedule\Ui\DataProvider\CronSchedule\Modifier\Listing;

use Magento\Ui\DataProvider\Modifier\ModifierInterface;

/**
 * @inheritDoc
 */
class MassActionModifier implements ModifierInterface
{
    /**
     * @inheritDoc
     */
    public function modifyData(array $data): array
    {
        return $data;
    }

    /**
     * @inheritDoc
     */
    public function modifyMeta(array $meta): array
    {
        return $meta;
    }
}
