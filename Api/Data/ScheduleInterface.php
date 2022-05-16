<?php
/**
 * Copyright © Soft Commerce Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace SoftCommerce\ProfileSchedule\Api\Data;

/**
 * Interface ScheduleInterface
 * used to provide profile schedule interface layer
 */
interface ScheduleInterface
{
    public const DB_TABLE_NAME = 'softcommerce_profile_schedule';

    public const ENTITY_ID = 'entity_id';
    public const NAME = 'name';
    public const TYPE_ID = 'type_id';
    public const STATUS = 'status';
    public const CRON_EXPRESSION = 'cron_expression';
    public const MESSAGE = 'message';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    /**
     * @return int
     */
    public function getEntityId(): int;

    /**
     * @return string|null
     */
    public function getName(): ?string;

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name);

    /**
     * @return string|null
     */
    public function getTypeId(): ?string;

    /**
     * @param string $typeId
     * @return $this
     */
    public function setTypeId(string $typeId);

    /**
     * @return int
     */
    public function getStatus(): int;

    /**
     * @param int $status
     * @return $this
     */
    public function setStatus(int $status);

    /**
     * @return string|null
     */
    public function getCronExpression(): ?string;

    /**
     * @param $code
     * @return $this
     */
    public function setCronExpression(string $cronExpression);

    /**
     * @return array
     */
    public function getMessage(): array;

    /**
     * @param string|array $message
     * @return $this
     */
    public function setMessage($message);

    /**
     * @return string|null
     */
    public function getCreatedAt(): ?string;

    /**
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt(string $createdAt);

    /**
     * @return string|null
     */
    public function getUpdatedAt(): ?string;

    /**
     * @param string $updatedAt
     * @return $this
     */
    public function setUpdatedAt(string $updatedAt);
}
