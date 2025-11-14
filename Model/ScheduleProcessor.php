<?php
/**
 * Copyright © Soft Commerce Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace SoftCommerce\ProfileSchedule\Model;

use Magento\Framework\Exception\LocalizedException;
use SoftCommerce\Core\Logger\LogProcessorInterface;
use SoftCommerce\Core\Model\Source\StatusInterface;
use SoftCommerce\Profile\Api\Data\ProfileInterface;
use SoftCommerce\Profile\Model\GetProfileDataByTypeIdInterface;
use SoftCommerce\ProfileHistory\Api\HistoryManagementInterface;
use SoftCommerce\ProfileSchedule\Model\Config\ScheduleConfigInterface;
use SoftCommerce\ProfileSchedule\Model\Config\ScheduleConfigInterfaceFactory;
use SoftCommerce\ProfileSchedule\Model\ScheduleProcessor\QueueProcessorInterface;

/**
 * @inheritDoc
 */
class ScheduleProcessor implements ScheduleProcessorInterface
{
    /**
     * @var ScheduleConfigInterface[]
     */
    private array $scheduleConfig = [];

    /**
     * @param GetProfileDataByTypeIdInterface $getProfileDataByTypeId
     * @param HistoryManagementInterface $historyManagement
     * @param IsActiveScheduleInterface $isActiveSchedule
     * @param LogProcessorInterface $logger
     * @param ScheduleConfigInterfaceFactory $scheduleConfigFactory
     * @param QueueProcessorInterface[] $queues
     */
    public function __construct(
        private readonly GetProfileDataByTypeIdInterface $getProfileDataByTypeId,
        private readonly HistoryManagementInterface $historyManagement,
        private readonly IsActiveScheduleInterface $isActiveSchedule,
        private readonly LogProcessorInterface $logger,
        private readonly ScheduleConfigInterfaceFactory $scheduleConfigFactory,
        private array $queues = []
    ) {
    }

    /**
     * @inheritDoc
     */
    public function execute(string $typeId): void
    {
        if (!$this->isActiveSchedule->execute($typeId)) {
            return;
        }

        $profileId = (int) $this->getProfileDataByTypeId->execute($typeId, ProfileInterface::ENTITY_ID);
        if (!$profileId) {
            return;
        }

        try {
            $this->processQueue($profileId);
        } catch (\Exception $e) {
            $message = $this->buildErrorMessage($typeId, $e->getMessage());
            $this->processHistory($profileId, $typeId, StatusInterface::ERROR, $message);
            $this->logger->execute(StatusInterface::ERROR, $message);
        }
    }

    /**
     * @param int $profileId
     * @return void
     * @throws LocalizedException
     */
    private function processQueue(int $profileId): void
    {
        foreach ($this->queues as $taskCode => $queue) {
            try {
                $messageCollector = $queue->execute($profileId);
                $messages = $messageCollector->getMessages();
                $status = $messageCollector->getOverallStatus();
            } catch (\Exception $e) {
                $status = StatusInterface::ERROR;
                $messages = $this->buildErrorMessage($taskCode, $e->getMessage());
                $this->logger->execute(StatusInterface::ERROR, $messages);
            }

            $this->processHistory($profileId, $taskCode, $status, $messages);
        }
    }

    /**
     * @param int $profileId
     * @param string $taskCode
     * @param string $status
     * @param array $messages
     * @return void
     * @throws LocalizedException
     */
    private function processHistory(
        int $profileId,
        string $taskCode,
        string $status,
        array $messages
    ): void {
        if (!$this->scheduleConfig($profileId)->isActiveHistory()) {
            return;
        }

        $this->historyManagement->create(
            $profileId,
            $taskCode,
            $status,
            $messages
        );
    }

    /**
     * Build error message in MessageCollector format
     *
     * @param string $taskCode
     * @param $message
     * @return array
     */
    private function buildErrorMessage(string $taskCode, $message): array
    {
        return [
            $taskCode => [
                [
                    'message' => $message,
                    'status' => StatusInterface::ERROR
                ]
            ]
        ];
    }

    /**
     * @param int $profileId
     * @return ScheduleConfigInterface
     */
    private function scheduleConfig(int $profileId): ScheduleConfigInterface
    {
        if (!isset($this->scheduleConfig[$profileId])) {
            $this->scheduleConfig[$profileId] = $this->scheduleConfigFactory->create(['profileId' => $profileId]);
        }

        return $this->scheduleConfig[$profileId];
    }
}
