<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

class Order extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    const ID_COLUMN = 'id';
    const NUMBER_COLUMN = 'number';
    const USER_ID_COLUMN = 'user_id';
    const CART_ID_COLUMN = 'cart_id';
    const STATUS_COLUMN = 'status';
    const ITEM_COLUMN = 'item';
    const ITEM_DETAILS_COLUMN = 'item_details';
    const TOTAL_COLUMN = 'total';
    const CREATED_AT_COLUMN = 'created_at';
    const UPDATED_AT_COLUMN = 'updated_at';
    const DELETED_AT_COLUMN = 'deleted_at';

    const OPEN_STATUS = 'open';
    const FULFILLED_STATUS = 'fulfilled';

    const STATUSES = [
        self::OPEN_STATUS,
        self::FULFILLED_STATUS,
    ];

    protected $casts = [
        'item_details' => 'array',
    ];

    /** @var User */
    private $user;

    /** @var Collection|PaymentDetails[] */
    private $paymentsDetails;

    /** @var Cart */
    private $cart;

    public function getId(): int
    {
        return $this->id;
    }

    public function getNumber(): string
    {
        return $this->number;
    }

    public function getUserId(): int
    {
        return $this->user_id;
    }

    public function getCartId(): int
    {
        return $this->cart_id;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getItem(): string
    {
        return $this->item;
    }

    public function getItemDetails(): array
    {
        return $this->item_details;
    }

    public function getTotal(): float
    {
        return $this->total;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->created_at;
    }

    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updated_at;
    }

    public function getDeletedAt(): ?\DateTimeInterface
    {
        return $this->deleted_at;
    }

    public function haveASubscriptionItem(): bool
    {
        $subscriptions = Subscription::ALLOWED_SUBSCRIPTIONS;

        return in_array($this->item, $subscriptions);
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getPaymentsDetails(): Collection|array
    {
        return $this->paymentsDetails;
    }

    public function getLastPaymentDetails(): PaymentDetails
    {
        return $this->paymentsDetails->first();
    }

    public function getCart(): Cart
    {
        return $this->cart;
    }

    public function isFulfilled(): bool
    {
        return $this->status === self::FULFILLED_STATUS;
    }

    public function hasBeenPaid(): bool
    {
        // if one of the payment details has been paid
        return $this->paymentsDetails->contains(fn($payment) => $payment->isPaid());
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    /**
     * @param Collection|PaymentDetails[] $paymentsDetails
     */
    public function setPaymentDetails($paymentsDetails): void
    {
        $this->paymentsDetails = $paymentsDetails;
    }

    public function setCart(Cart $cart): void
    {
        $this->cart = $cart;
    }
}
