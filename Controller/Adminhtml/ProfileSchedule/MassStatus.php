<?php
/**
 * Copyright © Soft Commerce Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace SoftCommerce\ProfileSchedule\Controller\Adminhtml\ProfileSchedule;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Framework\Data\Collection;
use Magento\Ui\Component\MassAction\Filter;
use SoftCommerce\ProfileSchedule\Model\ResourceModel;

/**
 * @inheritDoc
 */
class MassStatus extends AbstractMassAction implements HttpPostActionInterface
{
    /**
     * @param ResourceModel\Schedule $resource
     * @param ResourceModel\Schedule\CollectionFactory $collectionFactory
     * @param Filter $filter
     * @param Context $context
     */
    public function __construct(
        private readonly ResourceModel\Schedule $resource,
        ResourceModel\Schedule\CollectionFactory $collectionFactory,
        Filter $filter,
        Context $context
    ) {
        parent::__construct($collectionFactory, $filter, $context);
    }

    /**
     * @inheritDoc
     */
    protected function massAction(Collection $collection): void
    {
        $ids = $collection->getAllIds();
        $status = (int) $this->getRequest()->getParam('status');

        $this->resource->updateStatus($status, $ids);

        $this->messageManager->addSuccessMessage(
            __('Selected profiles have been updated with new status.')
        );
    }
}
