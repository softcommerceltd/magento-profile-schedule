<?php
/**
 * Copyright © Soft Commerce Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace SoftCommerce\ProfileSchedule\Controller\Adminhtml\ProfileSchedule;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\DataObject;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\Exception\LocalizedException;
use SoftCommerce\ProfileSchedule\Api\Data\ScheduleInterface;
use SoftCommerce\ProfileSchedule\Model\Schedule\CronExpressionValidatorInterface;

/**
 * @inheritDoc
 */
class Validate extends Action implements HttpPostActionInterface, HttpGetActionInterface
{
    /**
     * @var CronExpressionValidatorInterface
     */
    private CronExpressionValidatorInterface $cronExpressionValidator;

    /**
     * @var DataObjectFactory
     */
    private DataObjectFactory $dataObjectFactory;

    /**
     * @var JsonFactory
     */
    private JsonFactory $resultJsonFactory;

    /**
     * @param CronExpressionValidatorInterface $cronExpressionValidator
     * @param DataObjectFactory $dataObjectFactory
     * @param JsonFactory $jsonFactory
     * @param Context $context
     */
    public function __construct(
        CronExpressionValidatorInterface $cronExpressionValidator,
        DataObjectFactory $dataObjectFactory,
        JsonFactory $jsonFactory,
        Context $context
    ) {
        $this->cronExpressionValidator = $cronExpressionValidator;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->resultJsonFactory = $jsonFactory;
        parent::__construct($context);
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $response = $this->dataObjectFactory->create();
        $response->setError(0);

        $this->validate($response);
        $resultJson = $this->resultJsonFactory->create();
        if ($response->getError()) {
            $response->setError(true);
            $response->setMessages($response->getMessages());
        }

        $resultJson->setData($response);
        return $resultJson;
    }

    /**
     * @param DataObject $response
     */
    private function validate(DataObject $response): void
    {
        $errors = [];

        try {
            $this->validateCronExpression();
        } catch (\Exception $e) {
            $errors[] = $e->getMessage();
        }

        if ($errors) {
            $messages = $response->getMessages() ?: [];
            foreach ($errors as $error) {
                $messages[] = $error;
            }
            $response->setMessages($messages);
            $response->setError(1);
        }
    }

    /**
     * @throws LocalizedException
     */
    private function validateCronExpression(): void
    {
        $request = $this->getRequest()->getParam('general');
        if ($cronExpression = $request[ScheduleInterface::CRON_EXPRESSION] ?? '') {
            $this->cronExpressionValidator->isValid($cronExpression);
        }
    }
}
