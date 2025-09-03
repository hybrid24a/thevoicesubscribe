<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    // Eloquent Related Attributes

    protected $guarded = [];

    protected $casts = [
        self::FROM_COLUMN => 'datetime',
        self::TO_COLUMN   => 'datetime',
    ];

    const YEARLY_PLAN = 'sub-yearly';
    const YEARLY_ARCHIVE_PLAN = 'sub-yearly-archive';

    const ALLOWED_SUBSCRIPTIONS = [
        self::YEARLY_PLAN,
        self::YEARLY_ARCHIVE_PLAN,
    ];

    const AVAILABLE_PLANS = [
        self::YEARLY_PLAN => [
            'level' => 1,
            'price' => 200,
        ],
        self::YEARLY_ARCHIVE_PLAN => [
            'level' => 2,
            'price' => 400,
        ],
    ];

    // Columns names

    const ID_COLUMN = 'id';
    const USER_ID_COLUMN = 'user_id';
    const PLAN_COLUMN = 'plan';
    const FROM_COLUMN = 'from';
    const TO_COLUMN = 'to';
    const STATUS_COLUMN = 'status';
    const CREATED_AT_COLUMN = 'created_at';
    const UPDATED_AT_COLUMN = 'updated_at';

    const PENDING_STATUS_VALUE = 'pending';
    const ACTIVE_STATUS_VALUE = 'active';
    const REPLACED_STATUS_VALUE = 'replaced';
    const CANCELED_STATUS_VALUE = 'canceled';

    // Getters

    public function getId(): int
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->user_id;
    }

    public function getPlan(): string
    {
        return $this->plan;
    }

    public function getPlanPrice(): float
    {
        return self::AVAILABLE_PLANS[$this->plan]['price'] ?? 0;
    }

    public function getFrom(): Carbon
    {
        return $this->from;
    }

    public function getTo(): ?Carbon
    {
        return $this->to;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function isActive(): bool
    {
        return $this->status === self::ACTIVE_STATUS_VALUE;
    }

    public function hasExpired(): bool
    {
        if($this->getTo() === null) {
            return false;
        }

        return $this->getTo()->lt(Carbon::now());
    }
}
