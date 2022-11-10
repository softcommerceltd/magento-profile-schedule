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
use Magento\Framework\Controller\ResultInterface;
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
     * @var ScheduleRepositoryInterface
     */
    private ScheduleRepositoryInterface $scheduleRepository;

    /**
     * @param ScheduleRepositoryInterface $scheduleRepository
     * @param Context $context
     */
    public function __construct(
        ScheduleRepositoryInterface $scheduleRepository,
        Context $context
    ) {
        $this->scheduleRepository = $scheduleRepository;
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
                $this->scheduleRepository->deleteById($id);
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
     * @return bool
     */
    private function canExecute(): bool
    {
        return $this->_formKeyValidator->validate($this->getRequest()) && $this->getRequest()->isPost();
    }
}
