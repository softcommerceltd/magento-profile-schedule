<?php
/**
 * Copyright © Soft Commerce Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace SoftCommerce\ProfileSchedule\Controller\Adminhtml\CronSchedule;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResourceConnection;
use Magento\Ui\Component\MassAction\Filter;
use SoftCommerce\ProfileSchedule\Model\ResourceModel\CronSchedule\Collection;
use SoftCommerce\ProfileSchedule\Model\ResourceModel\CronSchedule\CollectionFactory;

/**
 * @inheritDoc
 */
class MassDelete extends AbstractMassAction
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

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
     * @inheritDoc
     */
    protected function massAction(Collection $collection): void
    {
        $ids = $collection->getAllIds();
        $result = $this->resourceConnection->getConnection()->delete(
            $this->resourceConnection->getTableName('cron_schedule'),
            [
                'schedule_id IN (?)' => $ids
            ]
        );

        if ($result > 0) {
            $this->messageManager->addSuccessMessage(
                __(
                    'Selected cron schedules have been deleted. Effected IDs: %1',
                    implode(', ', $ids)
                )
            );
        }
    }
}
