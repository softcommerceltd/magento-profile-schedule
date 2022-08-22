<?php
/**
 * Copyright © Soft Commerce Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace SoftCommerce\ProfileSchedule\Ui\DataProvider;

use Magento\Framework\Api\Filter;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
use SoftCommerce\Profile\Model\TypeInstanceOptionsInterface;
use SoftCommerce\ProfileSchedule\Model\ResourceModel\CronSchedule\Collection;
use SoftCommerce\ProfileSchedule\Model\ResourceModel\CronSchedule\CollectionFactory;

/**
 * @inheritDoc
 * Class CronScheduleListingDataProvider used to provide
 * cron schedule listing data.
 */
class CronScheduleListingDataProvider extends AbstractDataProvider
{
    /**
     * @var PoolInterface
     */
    private $pool;

    /**
     * @var TypeInstanceOptionsInterface
     */
    private $typeInstanceOptions;

    /**
     * @param CollectionFactory $collectionFactory
     * @param PoolInterface $pool
     * @param TypeInstanceOptionsInterface $typeInstanceOptions
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        PoolInterface $pool,
        TypeInstanceOptionsInterface $typeInstanceOptions,
        string $name,
        string $primaryFieldName,
        string $requestFieldName,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        $this->pool = $pool;
        $this->typeInstanceOptions = $typeInstanceOptions;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * @inheritDoc
     */
    public function getData(): array
    {
        /** @var Collection $collection */
        $collection = $this->getCollection();
        $data = $collection->toArray();

        foreach ($this->pool->getModifiersInstances() as $modifier) {
            $data = $modifier->modifyData($data);
        }

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function addFilter(Filter $filter): void
    {
        /** @var Collection $collection */
        $collection = $this->getCollection();

        if ($filter->getField() === 'fulltext') {
            $collection->addFullTextFilter(trim($filter->getValue()));
        } else {
            $collection->addFieldToFilter(
                "main_table.{$filter->getField()}",
                [$filter->getConditionType() => $filter->getValue()]
            );
        }
    }

    /**
     * @inheritDoc
     * @throws LocalizedException
     */
    public function getMeta(): array
    {
        $meta = parent::getMeta();
        foreach ($this->pool->getModifiersInstances() as $modifier) {
            $meta = $modifier->modifyMeta($meta);
        }

        return $meta;
    }

    /**
     * @return AbstractCollection
     */
    public function getCollection(): AbstractCollection
    {
        $collection = parent::getCollection();
        $collection->addFieldToFilter('job_code', ['in' => array_keys($this->typeInstanceOptions->getTypes())]);
        return $collection;
    }
}
