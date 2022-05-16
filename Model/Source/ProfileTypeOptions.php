<?php
/**
 * Copyright © Soft Commerce Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace SoftCommerce\ProfileSchedule\Model\Source;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Data\OptionSourceInterface;
use SoftCommerce\Profile\Model\TypeInstanceOptionsInterface;
use SoftCommerce\ProfileSchedule\Api\Data\ScheduleInterface;
use SoftCommerce\ProfileSchedule\Model\GetScheduleDataInterface;

/**
 * @inheritDoc
 */
class ProfileTypeOptions implements OptionSourceInterface
{
    /**
     * @var GetScheduleDataInterface
     */
    private $getScheduleData;

    /**
     * @var array
     */
    private $options;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var TypeInstanceOptionsInterface
     */
    private $typeInstanceOptions;

    /**
     * @param GetScheduleDataInterface $getScheduleData
     * @param RequestInterface $request
     * @param TypeInstanceOptionsInterface $typeInstanceOptions
     */
    public function __construct(
        GetScheduleDataInterface $getScheduleData,
        RequestInterface $request,
        TypeInstanceOptionsInterface $typeInstanceOptions
    ) {
        $this->getScheduleData = $getScheduleData;
        $this->request = $request;
        $this->typeInstanceOptions = $typeInstanceOptions;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        if (null !== $this->options) {
            return $this->options;
        }

        $this->options = [];
        $scheduleData = $this->getScheduleData->execute();
        $options = $this->typeInstanceOptions->getOptionArray();

        if ($scheduleId = $this->request->getParam('id')) {
            $scheduleData = array_filter($scheduleData, function ($item) use ($scheduleId) {
                return isset($item[ScheduleInterface::ENTITY_ID])
                    && $item[ScheduleInterface::ENTITY_ID] !== $scheduleId;
            });
        }

        if ($scheduleData = array_column($scheduleData, ScheduleInterface::TYPE_ID)) {
            $options = array_filter($options, function ($item) use ($scheduleData) {
                return !in_array($item, $scheduleData);
            }, ARRAY_FILTER_USE_KEY);
        }

        if ($typeId = $this->request->getParam(ScheduleInterface::TYPE_ID)) {
            $options = array_filter($options, function ($item) use ($typeId) {
                return isset($item[ScheduleInterface::TYPE_ID]) && $item[ScheduleInterface::TYPE_ID] !== $typeId;
            });
        }

        foreach ($options as $index => $value) {
            $this->options[] = [
                'value' => $index,
                'label' => $value
            ];
        }

        return $this->options;
    }
}
