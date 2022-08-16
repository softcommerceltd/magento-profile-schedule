<?php
/**
 * Copyright © Soft Commerce Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace SoftCommerce\ProfileSchedule\Controller\Adminhtml\ProfileSchedule;

use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;

/**
 * @inheritDoc
 */
class Edit extends Action implements HttpGetActionInterface
{
    /**
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'SoftCommerce_ProfileSchedule::manage';

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        /** @var Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        if ($this->getRequest()->getParam('popup')) {
            $resultPage->addHandle(['popup', 'softcommerce_profileschedule_new_popup']);
            $pageConfig = $resultPage->getConfig();
            $pageConfig->addBodyClass('profile-popup');
        } else {
            $resultPage
                ->setActiveMenu('SoftCommerce_ProfileSchedule::profile_schedule')
                ->addBreadcrumb(__('Profile Schedule'), __('Profile Schedule'))
                ->addBreadcrumb(
                    $id ? __('Manage Profile Schedule') : __('New Profile Schedule'),
                    $id ? __('Manage Profile Schedule') : __('New Profile Schedule')
                );
        }

        $resultPage->getConfig()->getTitle()->prepend(
            $id ? __('Manage Profile Schedule') : __('New Profile Schedule')
        );

        return $resultPage;
    }
}
