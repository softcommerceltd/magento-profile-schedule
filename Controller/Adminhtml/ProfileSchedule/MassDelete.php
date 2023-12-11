<?php
/**
 * Copyright © Soft Commerce Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace SoftCommerce\ProfileSchedule\Controller\Adminhtml\ProfileSchedule;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\Data\Collection;
use SoftCommerce\Profile\Model\TypeInstanceOptionsInterface;
use SoftCommerce\ProfileSchedule\Api\Data\ScheduleInterface;
use SoftCommerce\ProfileSchedule\Api\ScheduleRepositoryInterface;
use SoftCommerce\ProfileSchedule\Model\ResourceModel;

/**
 * @inheritDoc
 */
class MassDelete extends AbstractMassAction
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'SoftCommerce_ProfileSchedule::manage';

    /**
     * @var WriterInterface
     */
    private WriterInterface $configWriter;

    /**
     * @var ScheduleRepositoryInterface
     */
    private ScheduleRepositoryInterface $scheduleRepository;

    /**
     * @var TypeInstanceOptionsInterface
     */
    private TypeInstanceOptionsInterface $typeInstanceOptions;

    /**
     * @param WriterInterface $configWriter
     * @param ScheduleRepositoryInterface $scheduleRepository
     * @param TypeInstanceOptionsInterface $typeInstanceOptions
     * @param ResourceModel\Schedule\CollectionFactory $collectionFactory
     * @param Filter $filter
     * @param Context $context
     */
    public function __construct(
        WriterInterface $configWriter,
        ScheduleRepositoryInterface $scheduleRepository,
        TypeInstanceOptionsInterface $typeInstanceOptions,
        ResourceModel\Schedule\CollectionFactory $collectionFactory,
        Filter $filter,
        Context $context
    ) {
        $this->configWriter = $configWriter;
        $this->scheduleRepository = $scheduleRepository;
        $this->typeInstanceOptions = $typeInstanceOptions;
        parent::__construct($collectionFactory, $filter, $context);
    }

    /**
     * @inheritDoc
     */
    protected function massAction(Collection $collection): void
    {
        $result = [];
        /** @var ScheduleInterface $item */
        foreach ($collection->getItems() as $item) {
            try {
                $this->processCrontabDelete($item);
                $this->scheduleRepository->delete($item);
                $result[] = $item->getEntityId();
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }

        $this->messageManager->addSuccessMessage(
            __(
                'Selected schedules have been deleted. Effected IDs: %1',
                implode(', ', $result)
            )
        );
    }

    /**
     * @param ScheduleInterface $model
     * @return void
     */
    private function processCrontabDelete(ScheduleInterface $model): void
    {
        $typeId = $model->getTypeId();
        if (!$typeId || !$cronGroup = $this->typeInstanceOptions->getCronGroupByTypeId($typeId)) {
            return;
        }

        $this->configWriter->delete(
            sprintf(ScheduleInterface::CRON_SCHEDULE_PATH, $cronGroup, $typeId)
        );
    }
}
