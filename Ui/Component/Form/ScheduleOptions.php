<?php
/**
 * Copyright © Soft Commerce Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace SoftCommerce\ProfileSchedule\Ui\Component\Form;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Data\OptionSourceInterface;
use SoftCommerce\ProfileSchedule\Api\Data\ScheduleInterface;
use SoftCommerce\ProfileSchedule\Model\GetScheduleDataInterface;

/**
 * @inheritDoc
 */
class ScheduleOptions implements OptionSourceInterface
{
    /**
     * @var GetScheduleDataInterface
     */
    private GetScheduleDataInterface $getScheduleData;

    /**
     * @var array|null
     */
    private ?array $options = null;

    /**
     * @var RequestInterface
     */
    private RequestInterface $request;

    /**
     * @param GetScheduleDataInterface $getScheduleData
     * @param RequestInterface $request
     */
    public function __construct(
        GetScheduleDataInterface $getScheduleData,
        RequestInterface $request
    ) {
        $this->getScheduleData = $getScheduleData;
        $this->request = $request;
    }

    /**
     * @inheritDoc
     */
    public function toOptionArray(): array
    {
        if (null === $this->options) {
            $this->options = [];
            $scheduleData = $this->getScheduleData->execute();
            if ($typeId = $this->request->getParam(ScheduleInterface::TYPE_ID)) {
                $scheduleData = $this->getScheduleData->applySearchCriteria(ScheduleInterface::TYPE_ID, $typeId);
            }

            foreach ($scheduleData as $item) {
                $this->options[] = [
                    'value' => $item[ScheduleInterface::ENTITY_ID],
                    'label' => $item[ScheduleInterface::NAME]
                ];
            }
        }

        return $this->options;
    }
}
