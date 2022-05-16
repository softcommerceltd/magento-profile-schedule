<?php
/**
 * Copyright © Soft Commerce Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace SoftCommerce\ProfileSchedule\Controller\Adminhtml\CronSchedule;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;
use SoftCommerce\ProfileSchedule\Model\ResourceModel\CronSchedule\Collection;
use SoftCommerce\ProfileSchedule\Model\ResourceModel\CronSchedule\CollectionFactory;

/**
 * Class AbstractMassAction
 */
abstract class AbstractMassAction extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'SoftCommerce_Profile::profile';

    /**
     * @var string
     */
    protected $redirectUrl = '*/*/index';

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        Filter $filter,
        Context $context
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->filter = $filter;
        parent::__construct($context);
    }

    /**
     * @inheritDoc
     * @return Redirect
     */
    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        if (!$this->canExecute()) {
            $this->messageManager->addErrorMessage(__('Could not process given request.'));
            return $resultRedirect->setPath($this->getComponentRefererUrl());
        }

        try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            $this->massAction($collection);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        $resultRedirect->setPath($this->getComponentRefererUrl());
        return $resultRedirect;
    }

    /**
     * Return component referer url
     *
     * @return string
     */
    protected function getComponentRefererUrl()
    {
        return $this->filter->getComponentRefererUrl() ?: $this->_redirect->getRefererUrl();
    }

    /**
     * @param Collection $collection
     * @return void
     */
    abstract protected function massAction(Collection $collection): void;

    /**
     * @return bool
     */
    private function canExecute(): bool
    {
        return $this->_formKeyValidator->validate($this->getRequest()) && $this->getRequest()->isPost();
    }
}
