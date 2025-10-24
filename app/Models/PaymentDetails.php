<?php

namespace App\Models;

use App\Models\Traits\HasTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentDetails extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    const ID_COLUMN = 'id';
    const ORDER_ID_COLUMN = 'order_id';
    const PAYMENT_METHOD_COLUMN = 'payment_method';
    const AMOUNT_COLUMN = 'amount';
    const STATUS_COLUMN = 'status';
    const PAYLOAD_COLUMN = 'payload';
    const CREATED_AT_COLUMN = 'created_at';
    const UPDATED_AT_COLUMN = 'updated_at';
    const DELETED_AT_COLUMN = 'deleted_at';

    const PAID_STATUS = 'paid';
    const PENDING_STATUS = 'pending';
    const REFUNDED_STATUS = 'refunded';
    const VOIDED_STATUS = 'voided';
    const CANCELED_STATUS = 'canceled';

    const PAYZONE = 'payzone';

    public function getId(): int
    {
        return $this->id;
    }

    public function getOrderId(): int
    {
        return $this->order_id;
    }

    public function getPaymentMethod(): string
    {
        return $this->payment_method;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function isPending(): bool
    {
        return $this->status == self::PENDING_STATUS;
    }

    public function isPaid(): bool
    {
        return $this->status == self::PAID_STATUS;
    }

    public function isRefunded(): bool
    {
        return $this->status == self::REFUNDED_STATUS;
    }

    public function isVoided(): bool
    {
        return $this->status == self::VOIDED_STATUS;
    }

    public function getPayload(): ?string
    {
        return $this->payload;
    }
}
