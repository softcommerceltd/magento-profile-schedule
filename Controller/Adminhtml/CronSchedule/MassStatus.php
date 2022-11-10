<?php
/**
 * Copyright © Soft Commerce Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace SoftCommerce\ProfileSchedule\Controller\Adminhtml\CronSchedule;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Ui\Component\MassAction\Filter;
use SoftCommerce\ProfileSchedule\Model\ResourceModel\CronSchedule\Collection;
use SoftCommerce\ProfileSchedule\Model\ResourceModel\CronSchedule\CollectionFactory;

/**
 * @inheritDoc
 */
class MassStatus extends AbstractMassAction implements HttpPostActionInterface
{
    /**
     * @var ResourceConnection
     */
    private ResourceConnection $resourceConnection;

    /**
     * @param ResourceConnection $resourceConnection
     * @param CollectionFactory $collectionFactory
     * @param Filter $filter
     * @param Context $context
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        CollectionFactory $collectionFactory,
        Filter $filter,
        Context $context
    ) {
        $this->resourceConnection = $resourceConnection;
        parent::__construct($collectionFactory, $filter, $context);
    }

    /**
     * @param Collection $collection
     * @return void
     */
    protected function massAction(Collection $collection): void
    {
        $result = $this->resourceConnection->getConnection()->update(
            $this->resourceConnection->getTableName('cron_schedule'),
            [
                'status' => $this->getRequest()->getParam('status')
            ],
            [
                'schedule_id IN (?)' => $collection->getAllIds()
            ]
        );

        if ($result > 0) {
            $this->messageManager->addSuccessMessage(
                __(
                    'Selected cron schedules have been changed to status: %1.',
                    $this->getRequest()->getParam('status')
                )
            );
        }
    }
}
