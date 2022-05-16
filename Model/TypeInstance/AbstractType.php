<?php
/**
 * Copyright © Soft Commerce Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace SoftCommerce\ProfileSchedule\Model\TypeInstance;

use SoftCommerce\Core\Framework\MessageStorageInterface;

/**
 * Class AbstractType
 */
abstract class AbstractType
{
    private $data;

    protected $messageStorage;

    public function __construct(
        MessageStorageInterface $messageStorage,
        array $data = []
    ) {
        $this->messageStorage = $messageStorage;
        $this->data = $data;
    }


}
