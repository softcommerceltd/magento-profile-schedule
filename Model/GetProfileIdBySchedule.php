<?php
/**
 * Copyright © Soft Commerce Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace SoftCommerce\ProfileSchedule\Model;

use Magento\Framework\Exception\LocalizedException;
use SoftCommerce\Profile\Api\Data\ProfileInterface;
use SoftCommerce\Profile\Model\GetProfileDataByTypeIdInterface;
use SoftCommerce\ProfileSchedule\Model\Config\ScheduleConfigInterface;

/**
 * @inheritDoc
 */
class GetProfileIdBySchedule implements GetProfileIdByScheduleInterface
{
    /**
     * @var array
     */
    private array $data = [];

    /**
     * @param GetProfileDataByTypeIdInterface $getProfileDataByTypeId
     * @param ScheduleConfigInterface $scheduleConfig
     */
    public function __construct(
        private readonly GetProfileDataByTypeIdInterface $getProfileDataByTypeId,
        private readonly ScheduleConfigInterface $scheduleConfig
    ) {
    }

    /**
     * @inheritDoc
     */
    public function execute(string $typeId, ?int $scheduleId = null): array
    {
        if (!isset($this->data[$typeId])) {
            $this->data[$typeId] = $this->getData($typeId);
        }

        if (null === $scheduleId) {
            return $this->data[$typeId] ?? [];
        }

        return array_filter($this->data[$typeId] ?? [], function ($item) use ($scheduleId) {
            return isset($item[self::SCHEDULE_ID]) && $item[self::SCHEDULE_ID] == $scheduleId;
        });
    }

    /**
     * @param string $typeId
     * @return array
     * @throws LocalizedException
     */
    private function getData(string $typeId): array
    {
        if (!$profileId = $this->getProfileDataByTypeId->execute($typeId, ProfileInterface::ENTITY_ID)) {
            return [];
        }

        $this->scheduleConfig->setProfileId((int) $profileId);
        $result[$profileId] = [
            self::PROFILE_ID => $profileId,
            self::SCHEDULE_ID => $this->scheduleConfig->getScheduleId(),
            self::TYPE_ID => $typeId
        ];

        return $result;
    }
}
