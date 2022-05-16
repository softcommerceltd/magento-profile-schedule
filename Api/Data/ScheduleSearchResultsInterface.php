<?php
/**
 * Copyright © Soft Commerce Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace SoftCommerce\ProfileSchedule\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;
use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface ScheduleSearchResultsInterface
 */
interface ScheduleSearchResultsInterface extends SearchResultsInterface
{
    /**
     * @return ExtensibleDataInterface[]
     */
    public function getItems();

    /**
     * @param array $items
     * @return SearchResultsInterface
     */
    public function setItems(array $items);
}
