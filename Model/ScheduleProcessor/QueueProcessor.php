<?php
/**
 * Copyright © Soft Commerce Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace SoftCommerce\ProfileSchedule\Model\ScheduleProcessor;

use SoftCommerce\Core\Framework\MessageStorageInterface;
use SoftCommerce\Core\Framework\MessageStorageInterfaceFactory;

/**
 * @inheritDoc
 */
abstract class QueueProcessor implements QueueProcessorInterface
{
    /**
     * @var MessageStorageInterface
     */
    protected $messageStorage;

    /**
     * @param MessageStorageInterfaceFactory $messageStorageFactory
     */
    public function __construct(MessageStorageInterfaceFactory $messageStorageFactory)
    {
        $this->messageStorage = $messageStorageFactory->create();
    }

    /**
     * @return void
     */
    protected function init(): void
    {
        $this->messageStorage->resetData();
    }
}
