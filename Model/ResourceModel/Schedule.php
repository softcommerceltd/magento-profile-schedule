<?php
/**
 * Copyright © Soft Commerce Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace SoftCommerce\ProfileSchedule\Model\ResourceModel;

use Magento\Framework\Exception\LocalizedException;
use SoftCommerce\Core\Model\Source\Status;
use SoftCommerce\Core\Model\ResourceModel\AbstractResource;
use SoftCommerce\ProfileSchedule\Api\Data\ScheduleInterface;

/**
 * @inheritDoc
 */
class Schedule extends AbstractResource
{
    /**
     * @var string
     */
    protected $_useIsObjectNew = true;

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(ScheduleInterface::DB_TABLE_NAME, ScheduleInterface::ENTITY_ID);
    }

    /**
     * @param string $typeId
     * @param string|array|null $fields
     * @return array
     * @throws LocalizedException
     */
    public function getByTypeId(string $typeId, $fields = '*'): array
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from($this->getMainTable(), $fields)
            ->where(ScheduleInterface::TYPE_ID.' = ?', $typeId);
        return $connection->fetchRow($select) ?: [];
    }

    /**§
     * @return array
     * @throws LocalizedException
     */
    public function getPendingSchedules()
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from($this->getMainTable())
            ->where(ScheduleInterface::STATUS.' = ?', Status::PENDING);
        return $connection->fetchAll($select);
    }

    /**
     * @param $table
     * @return $this
     */
    public function truncate($table)
    {
        if ($this->getConnection()->getTransactionLevel() > 0) {
            $this->getConnection()->delete($table);
        } else {
            $this->getConnection()->truncateTable($table);
        }
        return $this;
    }
}
