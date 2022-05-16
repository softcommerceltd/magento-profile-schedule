<?php
/**
 * Copyright © Soft Commerce Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace SoftCommerce\ProfileSchedule\Model\ResourceModel\CronSchedule;

use Magento\Cron\Model\ResourceModel\Schedule\Collection as CronScheduleCollection;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Psr\Log\LoggerInterface;
use SoftCommerce\Profile\Api\Data\ProfileInterface;
use SoftCommerce\ProfileSchedule\Api\Data\ScheduleInterface;

/**
 * @inheritDoc
 */
class Collection extends CronScheduleCollection
{
    private const FULLTEXT_SEARCH_FIELDS = [
        'job_code',
        'status',
        'messages'
    ];

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @param RequestInterface $request
     * @param EntityFactoryInterface $entityFactory
     * @param LoggerInterface $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param AdapterInterface|null $connection
     * @param AbstractDb|null $resource
     */
    public function __construct(
        RequestInterface $request,
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        AdapterInterface $connection = null,
        AbstractDb $resource = null
    ) {
        $this->request = $request;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    /**
     * @param string $value
     * @return $this
     */
    public function addFullTextFilter(string $value)
    {
        $fields = self::FULLTEXT_SEARCH_FIELDS;
        $whereCondition = '';
        foreach ($fields as $key => $field) {
            $field = 'main_table.' . $field;
            $condition = $this->_getConditionSql(
                $this->getConnection()->quoteIdentifier($field),
                ['like' => "%$value%"]
            );
            $whereCondition .= ($key === 0 ? '' : ' OR ') . $condition;
        }
        if ($whereCondition) {
            $this->getSelect()->where($whereCondition);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    protected function _initSelect()
    {
        parent::_initSelect();

        if ($typeId = $this->request->getParam(ProfileInterface::TYPE_ID)) {
            $this->getSelect()->where('job_code = ?', $typeId);
        } elseif ($searchCriteria = $this->request->getParam('job_code')) {
            if (is_numeric($searchCriteria)) {
                $this->getSelect()->joinLeft(
                    ['sps' => $this->getConnection()->getTableName(ScheduleInterface::DB_TABLE_NAME)],
                    'sps.' . ScheduleInterface::TYPE_ID . ' = main_table.job_code',
                    []
                )->where(
                    'sps.' . ScheduleInterface::ENTITY_ID . ' = ?',
                    $searchCriteria
                );
            } else {
                $this->getSelect()->where('job_code = ?', $searchCriteria);
            }
        }

        return $this;
    }
}
