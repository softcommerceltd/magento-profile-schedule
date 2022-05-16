<?php
/**
 * Copyright © Soft Commerce Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace SoftCommerce\ProfileSchedule\Ui\DataProvider\Schedule\Modifier\Form;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use SoftCommerce\ProfileSchedule\Api\Data\ScheduleInterface;
use SoftCommerce\ProfileSchedule\Model\GetProfileIdByScheduleInterface;

/**
 * @inheritDoc
 */
class ProfileAssignmentModifier implements ModifierInterface
{
    private const DATA_COMPONENT = 'assigned_profiles';
    private const DATA_SOURCE = 'profiles';

    /**
     * @var GetProfileIdByScheduleInterface
     */
    private $getProfileIdBySchedule;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @param GetProfileIdByScheduleInterface $getProfileIdBySchedule
     * @param RequestInterface $request
     */
    public function __construct(
        GetProfileIdByScheduleInterface $getProfileIdBySchedule,
        RequestInterface $request
    ) {
        $this->getProfileIdBySchedule = $getProfileIdBySchedule;
        $this->request = $request;
    }

    /**
     * @inheritDoc
     * @throws LocalizedException
     */
    public function modifyData(array $data)
    {
        foreach ($data as $scheduleId => $item) {
            if (!$typeId = $item['general'][ScheduleInterface::TYPE_ID] ?? null) {
                continue;
            }

            $profileData = $this->getProfileIdBySchedule->execute($typeId, (int) $scheduleId);
            if ($profileIds = array_column($profileData, GetProfileIdByScheduleInterface::PROFILE_ID)) {
                $data[$scheduleId][self::DATA_SOURCE][self::DATA_COMPONENT] = $profileIds;
            }
        }

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function modifyMeta(array $meta)
    {
        if (!$this->canHideProfileFieldset()) {
            return $meta;
        }

        $meta[self::DATA_SOURCE]['arguments']['data']['config']['visible'] = false;
        return $meta;
    }

    /**
     * @return bool
     */
    private function canHideProfileFieldset(): bool
    {
        return $this->request->getParam('isModal', false) || $this->request->getParam('popup', false);
    }
}
