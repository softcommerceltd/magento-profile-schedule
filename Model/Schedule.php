<?php
/**
 * Copyright © Soft Commerce Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace SoftCommerce\ProfileSchedule\Model;

use Magento\Framework\DataObject\IdentityInterface;
use SoftCommerce\Core\Model\AbstractModel;
use SoftCommerce\ProfileSchedule\Model\ResourceModel;
use SoftCommerce\ProfileSchedule\Api\Data\ScheduleInterface;

/**
 * @inheritDoc
 */
class Schedule extends AbstractModel implements ScheduleInterface, IdentityInterface
{
    /**
     * @var string
     */
    private const CACHE_TAG = 'softcommerce_profile_schedule';

    /**
     * @var string
     */
    protected $_cacheTag = 'softcommerce_profile_schedule';

    /**
     * @var string
     */
    protected $_eventPrefix = 'softcommerce_profile_schedule';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\Schedule::class);
    }

    /**
     * @inheritDoc
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @inheritDoc
     */
    public function getEntityId(): int
    {
        return (int) $this->getData(self::ENTITY_ID);
    }

    /**
     * @inheritDoc
     */
    public function getName(): ?string
    {
        return $this->getData(self::NAME);
    }

    /**
     * @inheritDoc
     */
    public function setName(string $name)
    {
        $this->setData(self::NAME, $name);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getTypeId(): ?string
    {
        return $this->getData(self::TYPE_ID);
    }

    /**
     * @inheritDoc
     */
    public function setTypeId(string $typeId)
    {
        $this->setData(self::TYPE_ID, $typeId);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getStatus(): int
    {
        return (int) $this->getData(self::STATUS);
    }

    /**
     * @inheritDoc
     */
    public function setStatus(int $status)
    {
        $this->setData(self::STATUS, $status);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getCronExpression(): ?string
    {
        return $this->getData(self::CRON_EXPRESSION);
    }

    /**
     * @inheritDoc
     */
    public function setCronExpression(string $cronExpression)
    {
        $this->setData(self::CRON_EXPRESSION, $cronExpression);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getUpdatedAt(): ?string
    {
        return $this->getData(self::UPDATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setUpdatedAt(string $updatedAt)
    {
        $this->setData(self::UPDATED_AT, $updatedAt);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isActive(): bool
    {
        return (bool) $this->getStatus();
    }
}
