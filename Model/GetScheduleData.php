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
class GetScheduleData implements GetScheduleDataInterface
{
    /**
     * @var array
     */
    private $data;

    /**
     * @var ResourceModel\Schedule
     */
    private $resource;

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
    public function execute(): array
    {
        if (null === $this->data) {
            $this->data = $this->getData();
        }

        return $this->data;
    }

    /**
     * @inheritDoc
     */
    public function applySearchCriteria(string $searchKey, $searchValue): array
    {
        return array_filter($this->data ?: [], function ($item) use ($searchKey, $searchValue) {
            return is_array($searchValue)
                ? isset($item[$searchKey]) && in_array($item[$searchKey], $searchValue)
                : isset($item[$searchKey]) && $item[$searchKey] == $searchValue;
        });
    }

    /**
     * @return array
     */
    private function getData(): array
    {
        $connection = $this->resource->getConnection();
        $select = $connection->select()
            ->from($connection->getTableName(ScheduleInterface::DB_TABLE_NAME));
        return $connection->fetchAll($select);
    }
}
