<?php
/**
 * Copyright © Soft Commerce Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace SoftCommerce\ProfileSchedule\Controller\Adminhtml\ProfileSchedule;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\LayoutFactory;
use SoftCommerce\Profile\Model\TypeInstanceOptionsInterface;
use SoftCommerce\ProfileConfig\Model\ConfigScopeWriterInterface;
use SoftCommerce\ProfileSchedule\Api\Data\ScheduleInterface;
use SoftCommerce\ProfileSchedule\Api\ScheduleRepositoryInterface;
use SoftCommerce\ProfileSchedule\Model\GetProfileIdByScheduleInterface;
use SoftCommerce\ProfileSchedule\Model\Schedule;
use SoftCommerce\ProfileSchedule\Model\ScheduleFactory;

/**
 * @inheritDoc
 */
class Save extends Action implements HttpPostActionInterface
{
    /**
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'SoftCommerce_ProfileSchedule::manage';

    const CRON_SCHEDULE_PATH = 'crontab/%s/jobs/%s/schedule/cron_expr';

    /**
     * @var ConfigScopeWriterInterface
     */
    private $configScopeWriter;

    /**
     * @var GetProfileIdByScheduleInterface
     */
    private $getProfileIdBySchedule;

    /**
     * @var ScheduleInterface
     */
    private $model;

    /**
     * @var ScheduleFactory
     */
    private $modelFactory;

    /**
     * @var LayoutFactory
     */
    private $layoutFactory;

    /**
     * @var ScheduleRepositoryInterface
     */
    private $repository;

    /**
     * @var TypeInstanceOptionsInterface
     */
    private $typeInstanceOptions;

    /**
     * @var WriterInterface
     */
    private $configWriter;

    /**
     * @param ConfigScopeWriterInterface $configScopeWriter
     * @param GetProfileIdByScheduleInterface $getProfileIdBySchedule
     * @param LayoutFactory $layoutFactory
     * @param ScheduleFactory $modelFactory
     * @param ScheduleRepositoryInterface $repository
     * @param TypeInstanceOptionsInterface $typeInstanceOptions
     * @param WriterInterface $configWriter
     * @param Context $context
     */
    public function __construct(
        ConfigScopeWriterInterface $configScopeWriter,
        GetProfileIdByScheduleInterface $getProfileIdBySchedule,
        LayoutFactory $layoutFactory,
        ScheduleFactory $modelFactory,
        ScheduleRepositoryInterface $repository,
        TypeInstanceOptionsInterface $typeInstanceOptions,
        WriterInterface $configWriter,
        Context $context
    ) {
        $this->configScopeWriter = $configScopeWriter;
        $this->getProfileIdBySchedule = $getProfileIdBySchedule;
        $this->layoutFactory = $layoutFactory;
        $this->modelFactory = $modelFactory;
        $this->repository = $repository;
        $this->typeInstanceOptions = $typeInstanceOptions;
        $this->configWriter = $configWriter;
        parent::__construct($context);
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $request = $this->getRequest();
        if (!$request->isPost()) {
            $this->messageManager->addErrorMessage(__('Wrong request.'));
            return $this->processRedirectAfterFailureSave();
        }

        try {
            $this->processSave();
            $this->processCrontabSave();
            $this->processProfileAssignment();
            $this->messageManager->addSuccessMessage(__('The profile schedule has been saved.'));
            $resultRedirect = $this->processRedirectAfterSuccessSave();
        } catch (AlreadyExistsException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $resultRedirect = $this->processRedirectAfterFailureSave('*/*/index');
        } catch (CouldNotSaveException $e) {
            $this->messageManager->addErrorMessage(__('Could not save profile schedule. Reason: %1', $e->getMessage()));
            $resultRedirect = $this->processRedirectAfterFailureSave();
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $resultRedirect = $this->processRedirectAfterFailureSave();
        }

        return $resultRedirect;
    }

    /**
     * @throws CouldNotSaveException
     * @throws LocalizedException
     */
    private function processSave(): void
    {
        if (!$request = $this->getRequest()->getParam('general')) {
            throw new LocalizedException(__('Could not retrieve request data for save.'));
        }

        /** @var ScheduleInterface|Schedule $model */
        $model = $this->getModel();
        if ($model->getOrigData(ScheduleInterface::ENTITY_ID)) {
            $model->addData($request);
        } else {
            $model->setData($request);
        }

        $this->model = $this->repository->save($model);
    }

    /**
     * @return void
     */
    private function processCrontabSave(): void
    {
        $typeId = $this->getModel()->getTypeId();
        $cronExpression = $this->getModel()->getCronExpression() ?: null;

        if (!$this->getModel()->isActive()) {
            $cronExpression = null;
        }

        if (!$typeId || !$cronGroup = $this->typeInstanceOptions->getCronGroupByTypeId($typeId)) {
            return;
        }

        $this->configWriter->save(
            sprintf(self::CRON_SCHEDULE_PATH, $cronGroup, $typeId),
            $cronExpression
        );
    }

    /**
     * @return void
     * @throws LocalizedException
     */
    private function processProfileAssignment(): void
    {
        $request = $this->getRequest()->getParam('profiles');
        if (!$assignProfileRequest = $request['assigned_profiles'] ?? []) {
            return;
        }

        $path = "{$this->getModel()->getTypeId()}/schedule_config/schedule_id";
        foreach ($assignProfileRequest as $profileId) {
            $this->configScopeWriter->save((int) $profileId, $path, $this->getModel()->getEntityId());
        }

        $existingProfileIds = $this->getProfileIdBySchedule->execute($this->getModel()->getTypeId());
        $existingProfileIds = array_column($existingProfileIds, GetProfileIdByScheduleInterface::PROFILE_ID);
        $deleteRequest = array_diff($existingProfileIds, $assignProfileRequest);
        foreach ($deleteRequest as $profileId) {
            $this->configScopeWriter->delete((int) $profileId, $path);
        }

        $this->configScopeWriter->clean();
    }

    /**
     * @return Json|Redirect
     */
    private function processRedirectAfterSuccessSave()
    {
        if ($this->isAjax()) {
            return $this->resultRedirect(
                '',
                [
                    'id' => $this->getModel()->getEntityId(),
                    'type_id' => $this->getModel()->getTypeId(),
                    'label' => $this->getModel()->getName()
                ],
                ['error' => false]
            );
        } elseif ($this->isPopup()) {
            $params = [
                'id' => $this->getModel()->getEntityId(),
                'type_id' => $this->getModel()->getTypeId(),
                'name' => $this->getModel()->getName(),
                '_current' => true
            ];
            return $this->resultRedirect('softcommerce/profileschedule/popup', $params, ['error' => false]);
        }

        if ($this->getRequest()->getParam('back', false)) {
            return $this->resultRedirect(
                '*/*/edit',
                ['id' => $this->getModel()->getEntityId(), '_current' => true],
                ['error' => false]
            );
        } elseif ($this->getRequest()->getParam('redirect_to_new')) {
            return $this->resultRedirect('*/*/new', [], ['error' => false]);
        } else {
            return $this->resultRedirect('*/*/', [], ['error' => false]);
        }
    }

    /**
     * @param string|null $path
     * @return Json|Redirect
     */
    private function processRedirectAfterFailureSave(?string $path = null)
    {
        if ($this->isAjax()) {
            return $this->resultRedirect(
                '',
                ['id' => $this->getModel()->getEntityId(), 'label' => $this->getModel()->getName()],
                ['error' => true]
            );
        } elseif ($this->isPopup()) {
            $request = ['id' => $this->getModel()->getEntityId(), '_current' => true];
            return $this->resultRedirect('softcommerce/profileschedule/popup', $request, ['error' => true]);
        }

        if ($entityId = $this->getModel()->getEntityId()) {
            return $this->resultRedirect(
                $path ?: '*/*/new',
                ['id' => $entityId, '_current' => true,],
                ['error' => true]
            );
        } else {
            return $this->resultRedirect($path ?: '*/*/new', [], ['error' => true]);
        }
    }

    /**
     * @param string $path
     * @param array $params
     * @param array $response
     * @return Json|Redirect
     */
    private function resultRedirect(string $path = '', array $params = [], array $response = [])
    {
        if (!$this->isAjax()) {
            return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath($path, $params);
        }

        $layout = $this->layoutFactory->create();
        $layout->initMessages();

        $response['messages'] = [$layout->getMessagesBlock()->getGroupedHtml()];
        $response['params'] = $params;

        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($response);
    }

    /**
     * @return ScheduleInterface
     */
    private function getModel(): ScheduleInterface
    {
        if (null !== $this->model) {
            return $this->model;
        }

        if (!$entityId = $this->getRequest()->getParam('general')[ScheduleInterface::ENTITY_ID] ?? null) {
            $this->model = $this->modelFactory->create();
            return $this->model;
        }

        try {
            $this->model = $this->repository->get($entityId);
        } catch (\Exception $e) {
            $this->model = $this->modelFactory->create();
        }

        return $this->model;
    }

    /**
     * @return bool
     */
    private function isAjax(): bool
    {
        return (bool) $this->getRequest()->getParam('isAjax', false);
    }

    /**
     * @return bool
     */
    private function isPopup(): bool
    {
        return (bool) $this->getRequest()->getParam('popup', false);
    }
}
