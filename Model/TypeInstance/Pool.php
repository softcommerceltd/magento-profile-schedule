<?php
/**
 * Copyright © Soft Commerce Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace SoftCommerce\ProfileSchedule\Model\TypeInstance;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\ObjectManagerInterface;

/**
 * Class Pool used to create object type instance
 */
class Pool
{
    /**
     * @var ObjectManagerInterface
     */
    private ObjectManagerInterface $objectManager;

    /**
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * @param $className
     * @return AbstractType
     * @throws LocalizedException
     */
    public function get($className)
    {
        $profile = $this->objectManager->get($className);
        if (!$profile instanceof AbstractType) {
            throw new LocalizedException(__('%1 must ben an instance of %2', $className, AbstractType::class));
        }
        return $profile;
    }

    /**
     * @param $className
     * @param array $data
     * @return AbstractType
     * @throws LocalizedException
     */
    public function create($className, array $data = [])
    {
        $profile = $this->objectManager->create($className, $data);
        if (!$profile instanceof AbstractType) {
            throw new LocalizedException(__('%1 must ben an instance of %2', $className, AbstractType::class));
        }
        return $profile;
    }
}
