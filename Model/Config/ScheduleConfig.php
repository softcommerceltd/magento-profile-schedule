<?php
/**
 * Copyright © Soft Commerce Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace SoftCommerce\ProfileSchedule\Model\Config;

use Magento\Cron\Model\Config\Source\Frequency;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\Data\CollectionFactory;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use SoftCommerce\Core\Framework\DataStorageInterfaceFactory;
use SoftCommerce\Profile\Model\Config\ConfigModel;
use SoftCommerce\Profile\Model\Config\ConfigScopeInterface;
use SoftCommerce\Profile\Model\GetProfileTypeIdInterface;

/**
 * @inheritDoc
 */
class ScheduleConfig extends ConfigModel implements ScheduleConfigInterface
{
    /**
     * @var array
     */
    private array $dataInMemory = [];

    /**
     * @param CacheInterface $cache
     * @param DateTime $dateTime
     * @param ConfigScopeInterface $configScope
     * @param CollectionFactory $dataCollectionFactory
     * @param DataObjectFactory $dataObjectFactory
     * @param DataStorageInterfaceFactory $dataStorageFactory
     * @param GetProfileTypeIdInterface $getProfileTypeId
     * @param SerializerInterface $serializer
     * @param array $data
     * @param int|null $profileId
     */
    public function __construct(
        private readonly CacheInterface $cache,
        private readonly DateTime $dateTime,
        ConfigScopeInterface $configScope,
        CollectionFactory $dataCollectionFactory,
        DataObjectFactory $dataObjectFactory,
        DataStorageInterfaceFactory $dataStorageFactory,
        GetProfileTypeIdInterface $getProfileTypeId,
        SerializerInterface $serializer,
        array $data = [],
        ?int $profileId = null
    ) {
        parent::__construct(
            $configScope,
            $dataCollectionFactory,
            $dataObjectFactory,
            $dataStorageFactory,
            $getProfileTypeId,
            $serializer,
            $data,
            $profileId
        );
    }

    /**
     * @inheritDoc
     */
    public function isActive(): bool
    {
        return (bool) $this->getConfig($this->getTypeId() . self::XML_PATH_STATUS);
    }

    /**
     * @inheritDoc
     */
    public function getScheduleId(): ?int
    {
        return (int) $this->getConfig($this->getTypeId() . self::XML_PATH_SCHEDULE_ID) ?: null;
    }

    /**
     * @inheritDoc
     */
    public function getProcessBatchSize(): ?int
    {
        return (int) $this->getConfig($this->getTypeId() . self::XML_PATH_PROCESS_BATCH_SIZE) ?: null;
    }

    /**
     * @inheritDoc
     */
    public function isActiveHistory(): bool
    {
        return (bool) $this->getConfig($this->getTypeId() . self::XML_PATH_ENABLE_HISTORY);
    }

    /**
     * @inheritDoc
     */
    public function isRetryOnFailureEnabled(): bool
    {
        return (bool) $this->getConfig($this->getTypeId() . self::XML_PATH_ENABLE_RETRY_ON_FAILURE);
    }

    /**
     * @inheritDoc
     */
    public function shouldRetryOnError(): bool
    {
        return (bool) $this->getConfig($this->getTypeId() . self::XML_PATH_RETRY_ON_ERROR);
    }

    /**
     * @inheritDoc
     */
    public function isActiveOnetimeProcess(): bool
    {
        return (bool) $this->getConfig($this->getTypeId() . self::XML_PATH_ENABLE_1TIME_FULL_PROCESS);
    }

    /**
     * @inheritDoc
     */
    public function getOnetimeProcessFrequency(): ?string
    {
        if (!isset($this->dataInMemory[__FUNCTION__])) {
            $this->dataInMemory[__FUNCTION__] = $this->getConfig(
                $this->getTypeId() . self::XML_PATH_1TIME_FULL_PROCESS_FREQUENCY
            );
        }

        return $this->dataInMemory[__FUNCTION__] ?? '';
    }

    /**
     * @inheritDoc
     */
    public function getOnetimeProcessInstructions(?string $metadata = null): array|string|bool|null
    {
        if (isset($this->dataInMemory[__FUNCTION__])) {
            return $metadata
                ? ($this->dataInMemory[__FUNCTION__][$metadata])
                : $this->dataInMemory[__FUNCTION__];
        }

        try {
            $this->dataInMemory[__FUNCTION__] = $this->serializer->unserialize(
                $this->cache->load($this->getCacheKeyOnetimeProcessIdentifier()) ?: ''
            );
        } catch (\InvalidArgumentException) {
            $this->dataInMemory[__FUNCTION__] = [
                'timestamp' => null,
                'can_process' => true
            ];
        }

        if (isset($this->dataInMemory[__FUNCTION__]['timestamp'])) {
            return $metadata
                ? ($this->dataInMemory[__FUNCTION__][$metadata])
                : $this->dataInMemory[__FUNCTION__];
        }

        $time = $this->getConfig($this->getTypeId() . self::XML_PATH_1TIME_FULL_PROCESS_TIME);
        $frequency = $this->getOnetimeProcessFrequency();

        if (!$time && !$frequency) {
            return null;
        }

        $time = explode(':', $time);
        $minute = (int) $time[1];
        $hour = (int) $time[0];
        $day = $frequency == Frequency::CRON_MONTHLY ? 1 : (int) $this->dateTime->gmtDate('d');
        $month = (int) $this->dateTime->gmtDate('m');
        $weekDayNo = 7 - $this->dateTime->gmtDate('w');

        $cronTimestamp = mktime(
            $hour,
            $minute,
            0,
            $month,
            $day
        );

        $cronTimestamp = $this->dateTime->gmtDate('Y-m-d H:00', $cronTimestamp);

        $this->dataInMemory[__FUNCTION__]['timestamp'] = match ($frequency) {
            Frequency::CRON_DAILY, Frequency::CRON_MONTHLY => $cronTimestamp,
            Frequency::CRON_WEEKLY => $this->dateTime->gmtDate(
                'Y-m-d H:00',
                strtotime("+$weekDayNo day", strtotime($cronTimestamp))
            ),
        };

        $this->saveOnetimeProcessInstructions($this->dataInMemory[__FUNCTION__]['timestamp'], true);

        return $metadata
            ? ($this->dataInMemory[__FUNCTION__][$metadata])
            : $this->dataInMemory[__FUNCTION__];
    }

    /**
     * @inheritDoc
     */
    public function saveOnetimeProcessInstructions(string $timestamp, bool $canProcessFlag = false): void
    {
        $data = $this->serializer->serialize([
            'timestamp' => $timestamp,
            'can_process' => $canProcessFlag
        ]);

        $this->cache->save(
            $data,
            $this->getCacheKeyOnetimeProcessIdentifier(),
            [
                self::CACHE_KEY_ONETIME_PROCESS
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function getOnetimeProcessNextTimestamp(): ?string
    {
        if (!$currentTimestamp = $this->getOnetimeProcessInstructions('timestamp')) {
            return null;
        }

        return match ($this->getOnetimeProcessFrequency()) {
            Frequency::CRON_DAILY => $this->dateTime->gmtDate(
                'Y-m-d H:00',
                strtotime("+1 day", strtotime($currentTimestamp))
            ),
            Frequency::CRON_WEEKLY => $this->dateTime->gmtDate(
                'Y-m-d H:00',
                strtotime("+1 week", strtotime($currentTimestamp))
            ),
            Frequency::CRON_MONTHLY => $this->dateTime->gmtDate(
                'Y-m-d H:00',
                strtotime('+1 month', strtotime($currentTimestamp)))
        };
    }

    /**
     * @inheritDoc
     */
    public function canRunOnetimeProcess(): bool
    {
        if (!$this->isActiveOnetimeProcess()) {
            return false;
        }

        $cronTimestamp = $this->getOnetimeProcessInstructions('timestamp');
        $canProcess = $this->getOnetimeProcessInstructions('can_process');

        $format = match($this->getOnetimeProcessFrequency()) {
            Frequency::CRON_DAILY, Frequency::CRON_WEEKLY => 'd H',
            Frequency::CRON_MONTHLY => 'm-d H'
        };

        $scheduleTimestamp = $this->dateTime->gmtDate($format, strtotime($cronTimestamp));
        $currentTimestamp = $this->dateTime->gmtDate($format);

        if ($scheduleTimestamp < $currentTimestamp && $canProcess) {
            $this->saveOnetimeProcessInstructions(
                $this->getOnetimeProcessNextTimestamp(),
                true
            );
            return false;
        }

        return $scheduleTimestamp === $currentTimestamp && $canProcess;
    }

    /**
     * @return string
     * @throws LocalizedException
     */
    private function getCacheKeyOnetimeProcessIdentifier(): string
    {
        return self::CACHE_KEY_ONETIME_PROCESS . '_' . $this->getProfileId();
    }
}
