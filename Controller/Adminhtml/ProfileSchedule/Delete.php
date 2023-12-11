<?php
/**
 * Copyright © Soft Commerce Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace SoftCommerce\ProfileSchedule\Controller\Adminhtml\ProfileSchedule;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Controller\ResultInterface;
use SoftCommerce\Profile\Model\TypeInstanceOptionsInterface;
use SoftCommerce\ProfileSchedule\Api\Data\ScheduleInterface;
use SoftCommerce\ProfileSchedule\Api\ScheduleRepositoryInterface;

/**
 * @inheritDoc
 */
class Delete extends Action implements HttpPostActionInterface
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
     * @param Context $context
     */
    public function __construct(
        WriterInterface $configWriter,
        ScheduleRepositoryInterface $scheduleRepository,
        TypeInstanceOptionsInterface $typeInstanceOptions,
        Context $context
    ) {
        $this->configWriter = $configWriter;
        $this->scheduleRepository = $scheduleRepository;
        $this->typeInstanceOptions = $typeInstanceOptions;
        parent::__construct($context);
    }

    /**
     * @inheritDoc
     */
    public function execute(): ResultInterface
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        if (!$this->canExecute()) {
            $this->messageManager->addErrorMessage(__('Schedule could not be deleted.'));
            return $resultRedirect->setPath('*/*');
        }

        if ($id = $this->getRequest()->getParam('id')) {
            try {
                $model = $this->scheduleRepository->get($id);
                $this->processCrontabDelete($model);
                $this->scheduleRepository->delete($model);
                $this->messageManager->addSuccessMessage(__('The schedule with ID %1 has been deleted.', $id));
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        } else {
            $this->messageManager->addErrorMessage(__('Could not retrieve schedule ID from request data.'));
        }

        return $resultRedirect->setPath('*/*/');
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

    /**
     * @return bool
     */
    private function canExecute(): bool
    {
        return $this->_formKeyValidator->validate($this->getRequest()) && $this->getRequest()->isPost();
    }
}
