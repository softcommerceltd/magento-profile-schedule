<?php
/**
 * Copyright © Soft Commerce Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace SoftCommerce\ProfileSchedule\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\LayoutFactory;
use Magento\Framework\View\Result\PageFactory;
use SoftCommerce\Profile\Api\Data\ProfileInterface;
use SoftCommerce\Profile\Api\ProfileRepositoryInterface;
use SoftCommerce\Profile\Model\Profile;
use SoftCommerce\Profile\Model\ProfileFactory;
use SoftCommerce\Profile\Model\RegistryLocatorInterface;
use SoftCommerce\Profile\Model\TypeInstanceOptionsInterface;

/**
 * @inheritDoc
 */
abstract class ProfileSchedule extends Action
{
    /**
     * @inheritDoc
     */
    public const ADMIN_RESOURCE = 'SoftCommerce_ProfileSchedule::manage';

    /**
     * @var ProfileInterface|null
     */
    protected ?ProfileInterface $currentProfile = null;

    /**
     * @var Registry
     */
    protected Registry $coreRegistry;

    /**
     * @var ProfileFactory
     */
    protected ProfileFactory $profileFactory;

    /**
     * @var ProfileRepositoryInterface
     */
    protected ProfileRepositoryInterface $profileRepository;

    /**
     * @var LayoutFactory
     */
    protected LayoutFactory $resultLayoutFactory;

    /**
     * @var PageFactory
     */
    protected PageFactory $resultPageFactory;

    /**
     * @var JsonFactory
     */
    protected JsonFactory $resultJsonFactory;

    /**
     * @var TypeInstanceOptionsInterface
     */
    protected TypeInstanceOptionsInterface $typeInstanceOptions;

    /**
     * @param Registry $coreRegistry
     * @param LayoutFactory $resultLayoutFactory
     * @param PageFactory $resultPageFactory
     * @param JsonFactory $resultJsonFactory
     * @param ProfileFactory $profileFactory
     * @param ProfileRepositoryInterface $profileRepository
     * @param TypeInstanceOptionsInterface $typeInstanceOptions
     * @param Action\Context $context
     */
    public function __construct(
        Registry $coreRegistry,
        LayoutFactory $resultLayoutFactory,
        PageFactory $resultPageFactory,
        JsonFactory $resultJsonFactory,
        ProfileFactory $profileFactory,
        ProfileRepositoryInterface $profileRepository,
        TypeInstanceOptionsInterface $typeInstanceOptions,
        Action\Context $context
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->resultLayoutFactory = $resultLayoutFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->profileFactory = $profileFactory;
        $this->profileRepository = $profileRepository;
        $this->typeInstanceOptions = $typeInstanceOptions;
        parent::__construct($context);
    }

    /**
     * @return ProfileInterface
     */
    protected function initCurrentProfile(): ProfileInterface
    {
        $this->currentProfile = $this->profileFactory->create();

        if ($id = $this->getRequest()->getParam('id')) {
            try {
                $this->currentProfile = $this->profileRepository->getById($id);
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('Could not find profile with ID: %1.', $id));
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('*/*/');
                return $this->currentProfile;
            }
        }

        $this->coreRegistry->register(RegistryLocatorInterface::CURRENT_PROFILE, $this->currentProfile);

        return $this->currentProfile;
    }

    /**
     * @return int|null
     */
    protected function initCurrentProfileId(): ?int
    {
        if ($profileId = $this->getRequest()->getParam('id')) {
            $this->coreRegistry->register('current_profile_id', (int) $profileId);
        }
        return $profileId;
    }

    /**
     * @return ProfileInterface|Profile
     */
    protected function getProfile(): ProfileInterface
    {
        return $this->currentProfile;
    }
}
