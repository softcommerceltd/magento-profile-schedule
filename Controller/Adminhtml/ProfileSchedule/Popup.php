<?php
/**
 * Copyright © Soft Commerce Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace SoftCommerce\ProfileSchedule\Controller\Adminhtml\ProfileSchedule;

use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;

/**
 * @inheritDoc
 */
class Popup extends Action implements HttpGetActionInterface
{
    /**
     * @inheritDoc
     */
    public function execute()
    {
        return $this->resultFactory->create(ResultFactory::TYPE_LAYOUT);
    }
}
