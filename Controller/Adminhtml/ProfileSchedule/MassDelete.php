<?php
/**
 * Copyright © Soft Commerce Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace SoftCommerce\ProfileSchedule\Controller\Adminhtml\ProfileSchedule;

use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\Data\Collection;
use SoftCommerce\ProfileSchedule\Api\Data\ScheduleInterface;
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
    const ADMIN_RESOURCE = 'SoftCommerce_ProfileSchedule::delete';

    /**
     * @var ResourceModel\Schedule
     */
    private $resource;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param ResourceModel\Schedule $resource
     * @param ResourceModel\Schedule\CollectionFactory $collectionFactory
     */
    public function __construct(
        ResourceModel\Schedule $resource,
        ResourceModel\Schedule\CollectionFactory $collectionFactory,
        Filter $filter,
        Context $context
    ) {
        $this->resource = $resource;
        parent::__construct($collectionFactory, $filter, $context);
    }

    /**
     * @inheritDoc
     */
    protected function massAction(Collection $collection): void
    {
        $ids = $collection->getAllIds();
        $result = $this->resource->remove(
            [
                ScheduleInterface::ENTITY_ID . ' IN (?)' => $ids
            ]
        );

        if ($result > 0) {
            $this->messageManager->addSuccessMessage(
                __(
                    'Selected schedules have been deleted. Effected IDs: %1',
                    implode(', ', $ids)
                )
            );
        }
    }
}
