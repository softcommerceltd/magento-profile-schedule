<?php
/**
 * Copyright © Soft Commerce Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace SoftCommerce\ProfileSchedule\Model\ResourceModel\Schedule;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use SoftCommerce\ProfileSchedule\Api\Data\ScheduleInterface;
use SoftCommerce\ProfileSchedule\Model\Schedule;
use SoftCommerce\ProfileSchedule\Model\ResourceModel;

/**
 * @inheritDoc
 */
class Collection extends AbstractCollection
{
    /**
     * @inheritDoc
     */
    protected $_idFieldName = ScheduleInterface::ENTITY_ID;

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(Schedule::class, ResourceModel\Schedule::class);
    }

    /**
     * @param $status
     * @return $this
     */
    public function addStatusFilter($status)
    {
        $this->addFieldToFilter(ScheduleInterface::STATUS, ['eq' => $status]);
        return $this;
    }
}
