<?php
/**
 * Copyright © Soft Commerce Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace SoftCommerce\ProfileSchedule\Model;

use SoftCommerce\ProfileSchedule\Api\Data\ScheduleInterface;
use SoftCommerce\ProfileSchedule\Model\ResourceModel;

/**
 * @inheritDoc
 */
class IsActiveSchedule implements IsActiveScheduleInterface
{
    /**
     * @var array
     */
    private array $data = [];

    /**
     * @var ResourceModel\Schedule
     */
    private ResourceModel\Schedule $resource;

    /**
     * @param ResourceModel\Schedule $resource
     */
    public function __construct(ResourceModel\Schedule $resource)
    {
        $this->resource = $resource;
    }

    /**
     * @inheritDoc
     */
    public function execute(string $typeId): bool
    {
        if (!isset($this->data[$typeId])) {
            $this->data[$typeId] = $this->getData($typeId);
        }

        return (bool) $this->data[$typeId];
    }

    /**
     * @param string $typeId
     * @return string
     */
    private function getData(string $typeId)
    {
        $connection = $this->resource->getConnection();
        $select = $connection->select()
            ->from($connection->getTableName(ScheduleInterface::DB_TABLE_NAME), ScheduleInterface::STATUS)
            ->where(ScheduleInterface::TYPE_ID . ' = ?', $typeId);
        return $connection->fetchOne($select);
    }
}
