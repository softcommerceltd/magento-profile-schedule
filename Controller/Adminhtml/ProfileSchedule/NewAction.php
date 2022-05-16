<?php
/**
 * Copyright © Soft Commerce Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace SoftCommerce\ProfileSchedule\Controller\Adminhtml\ProfileSchedule;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;

/**
 * @inheritDoc
 */
class NewAction extends Action implements HttpGetActionInterface
{
    /**
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'SoftCommerce_ProfileSchedule::manage';

    /**
     * @var ForwardFactory
     */
    private $resultForwardFactory;

    /**
     * @param ForwardFactory $resultForwardFactory
     * @param Context $context
     */
    public function __construct(
        ForwardFactory $resultForwardFactory,
        Context $context
    ) {
        $this->resultForwardFactory = $resultForwardFactory;
        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|ResultInterface|void
     */
    public function execute()
    {
        $resultForward = $this->resultForwardFactory->create();
        return $resultForward->forward('edit');
    }
}
