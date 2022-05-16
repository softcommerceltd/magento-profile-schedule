<?php
/**
 * Copyright © Soft Commerce Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace SoftCommerce\ProfileSchedule\Block\Adminhtml\System\Config\Form\Field;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Module\PackageInfo;

/**
 * Class Version used to provide module version number.
 */
class Version extends Field
{
    /**
     * @var PackageInfo
     */
    private $packageInfo;

    /**
     * @param PackageInfo $packageInfo
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        PackageInfo $packageInfo,
        Context $context,
        array $data = []
    ) {
        $this->packageInfo = $packageInfo;
        parent::__construct($context, $data);
    }

    /**
     * @inheritDoc
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $element->setData('text', $this->packageInfo->getVersion('SoftCommerce_ProfileSchedule'));
        return parent::_getElementHtml($element);
    }
}
