<?php
/**
 * Copyright © Soft Commerce Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace SoftCommerce\ProfileSchedule\Model;

use Magento\Framework\Exception\LocalizedException;
use SoftCommerce\Core\Framework\MessageStorage\StatusPredictionInterface;
use SoftCommerce\Core\Framework\MessageStorageInterface;
use SoftCommerce\Core\Logger\LogProcessorInterface;
use SoftCommerce\Core\Model\Source\Status;
use SoftCommerce\Profile\Api\Data\ProfileInterface;
use SoftCommerce\Profile\Model\GetProfileDataByTypeIdInterface;
use SoftCommerce\ProfileHistory\Api\HistoryManagementInterface;
use SoftCommerce\ProfileSchedule\Model\Config\ScheduleConfigInterface;
use SoftCommerce\ProfileSchedule\Model\Config\ScheduleConfigInterfaceFactory;

/**
 * @inheritDoc
 */
class ScheduleProcessor implements ScheduleProcessorInterface
{
    /**
     * @var GetProfileDataByTypeIdInterface
     */
    private $getProfileDataByTypeId;

    /**
     * @var HistoryManagementInterface
     */
    private $historyManagement;

    /**
     * @var IsActiveScheduleInterface
     */
    private $isActiveSchedule;

    /**
     * @var LogProcessorInterface
     */
    private $logger;

    /**
     * @var ScheduleConfigInterface[]|null
     */
    private $scheduleConfig;

    /**
     * @var ScheduleConfigInterfaceFactory
     */
    private $scheduleConfigFactory;

    /**
     * @var StatusPredictionInterface
     */
    private $statusPrediction;

    /**
     * @var ScheduleProcessor\QueueProcessorInterface[]
     */
    private $queues;

    /**
     * @param GetProfileDataByTypeIdInterface $getProfileDataByTypeId
     * @param HistoryManagementInterface $historyManagement
     * @param IsActiveScheduleInterface $isActiveSchedule
     * @param LogProcessorInterface $logger
     * @param ScheduleConfigInterfaceFactory $scheduleConfigFactory
     * @param StatusPredictionInterface $statusPrediction
     * @param array $queues
     */
    public function __construct(
        GetProfileDataByTypeIdInterface $getProfileDataByTypeId,
        HistoryManagementInterface $historyManagement,
        IsActiveScheduleInterface $isActiveSchedule,
        LogProcessorInterface $logger,
        ScheduleConfigInterfaceFactory $scheduleConfigFactory,
        StatusPredictionInterface $statusPrediction,
        array $queues = []
    ) {
        $this->getProfileDataByTypeId = $getProfileDataByTypeId;
        $this->historyManagement = $historyManagement;
        $this->isActiveSchedule = $isActiveSchedule;
        $this->logger = $logger;
        $this->scheduleConfigFactory = $scheduleConfigFactory;
        $this->statusPrediction = $statusPrediction;
        $this->queues = $queues;
    }

    /**
     * @inheritDoc
     */
    public function execute(string $typeId): void
    {
        if (!$this->isActiveSchedule->execute($typeId)
            || !$profileId = (int) $this->getProfileDataByTypeId->execute($typeId, ProfileInterface::ENTITY_ID)
        ) {
            return;
        }

        try {
            $this->processQueue($profileId);
        } catch (\Exception $e) {
            $message = $this->buildErrorMessage($typeId, $e->getMessage());
            $this->processHistory($profileId, $typeId, $message, Status::ERROR);
            $this->logger->execute(Status::ERROR, $message);
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
                $response = $queue->execute($profileId);
                $response = $response->getData();
            } catch (\Exception $e) {
                $response = $this->buildErrorMessage($taskCode, $e->getMessage());
                $this->logger->execute(Status::ERROR, $response);
            }

            $this->processHistory($profileId, $taskCode, $response);
        }
    }

    /**
     * @param int $profileId
     * @param string $taskCode
     * @param array $response
     * @param string|null $status
     * @return void
     * @throws LocalizedException
     */
    private function processHistory(
        int $profileId,
        string $taskCode,
        array $response,
        ?string $status = null
    ): void {
        if (!$this->scheduleConfig($profileId)->isActiveHistory()) {
            return;
        }

        $this->historyManagement->create(
            $profileId,
            $taskCode,
            $status ?: $this->statusPrediction->execute($response),
            $response
        );
    }

    /**
     * @param string $taskCode
     * @param $message
     * @return array
     */
    private function buildErrorMessage(string $taskCode, $message): array
    {
        return [
            MessageStorageInterface::ENTITY => $taskCode,
            MessageStorageInterface::STATUS => Status::ERROR,
            MessageStorageInterface::MESSAGE => $message
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
