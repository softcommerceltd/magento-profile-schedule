<?php
/**
 * Copyright © Soft Commerce Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace SoftCommerce\ProfileSchedule\Block\Adminhtml\Edit;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget;
use Magento\Framework\Serialize\SerializerInterface;

/**
 * @inheritDoc
 */
class Popup extends Widget
{
    /**
     * @var string
     */
    protected $_template = 'SoftCommerce_ProfileSchedule::edit/popup.phtml';

    /**
     * @var SerializerInterface
     */
    private SerializerInterface $serializer;

    /**
     * @param Context $context
     * @param SerializerInterface $serializer
     * @param array $data
     */
    public function __construct(
        Context $context,
        SerializerInterface $serializer,
        array $data = []
    ) {
        $this->serializer = $serializer;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getCloseButtonHtml(): string
    {
        return (string) $this->getChildHtml('close_button');
    }

    /**
     * @return string
     */
    public function getResponseJson(): string
    {
        return $this->serializer->serialize(
            [
                'schedule' => [
                    'id' => $this->getRequest()->getParam('id'),
                    'type_id' => $this->getRequest()->getParam('type_id'),
                    'label' => $this->getRequest()->getParam('name')
                ]
            ]
        );
    }
}
