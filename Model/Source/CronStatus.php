<?php
/**
 * Copyright © Soft Commerce Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace SoftCommerce\ProfileSchedule\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * @inheritDoc
 */
class CronStatus implements OptionSourceInterface
{
    public const PENDING = 'pending';
    public const RUNNING = 'running';
    public const SUCCESS = 'success';
    public const MISSED = 'missed';
    public const ERROR = 'error';
    public const SKIPPED = 'skipped';

    /**
     * @inheritDoc
     */
    public function toOptionArray(): array
    {
        $options = [];
        foreach ($this->getOptions() as $value => $label) {
            $options[] = [
                'label' => $label,
                'value' => $value,
            ];
        }
        return $options;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return [
            self::PENDING => __('Pending'),
            self::RUNNING => __('Running'),
            self::SUCCESS => __('Success'),
            self::MISSED => __('Missed'),
            self::ERROR => __('Error'),
            self::SKIPPED => __('Skipped')
        ];
    }
}
