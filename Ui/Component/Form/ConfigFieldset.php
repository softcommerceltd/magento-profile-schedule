<?php
/**
 * Copyright © Soft Commerce Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace SoftCommerce\ProfileSchedule\Ui\Component\Form;

use Magento\Framework\View\Element\ComponentVisibilityInterface;
use Magento\Ui\Component\Form\Fieldset;

/**
 * @inheritDoc
 */
class ConfigFieldset extends Fieldset implements ComponentVisibilityInterface
{
    /**
     * @inheritDoc
     */
    public function isComponentVisible(): bool
    {
        return (bool) $this->context->getRequestParam('id');
    }
}
