<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cart extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    const ID_COLUMN = 'id';
    const USER_ID_COLUMN = 'user_id';
    const SESSION_ID_COLUMN = 'session_id';
    const EXTERNAL_ID_COLUMN = 'external_id';
    const STATUS_COLUMN = 'status';
    const ITEM_COLUMN = 'item';
    const TIP_COLUMN = 'tip';
    const ITEM_DETAILS_COLUMN = 'item_details';
    const CREATED_AT_COLUMN = 'created_at';
    const UPDATED_AT_COLUMN = 'updated_at';
    const DELETED_AT_COLUMN = 'deleted_at';

    const PENDING_STATUS = 'pending';
    const ORDERED_STATUS = 'ordered';

    const MAGAZINE_ITEM = 'mag';
    const SUBSCRIPTION_ITEM = 'sub-yearly';
    const SUBSCRIPTION_ARCHIVE_ITEM = 'sub-yearly-archive';

    const AVAILABLE_ITEMS = [
        'mag' => [
            'price' => 10,
        ],
    ];

    protected $casts = [
        'item_details' => 'array',
    ];

    /** @var User|null */
    private $user;

    /** @var float */
    private $total;

    public function getId(): int
    {
        return $this->id;
    }

    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    public function getSessionId(): string
    {
        return $this->session_id;
    }

    public function getExternalId(): string
    {
        return $this->external_id;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getTip(): int
    {
        return $this->tip;
    }

    public function getItem(): string
    {
        return $this->item;
    }

    public function isMagazineItem(): bool
    {
        return $this->item === Cart::MAGAZINE_ITEM;
    }

    public function isSubscriptionItem(): bool
    {
        return $this->item === Cart::SUBSCRIPTION_ITEM;
    }

    public function isSubscriptionArchiveItem(): bool
    {
        return $this->item === Cart::SUBSCRIPTION_ARCHIVE_ITEM;
    }

    public function getItemDetails(): ?array
    {
        return $this->item_details;
    }

    public function getDisplayItems(int $price): array
    {
        $items = [];

        if ($this->getTip() > 0) {
            $items[] = [
                'thumbnail' => '/build/images/tips.jpg',
                'title'     => 'دعم الموقع',
                'price'     => $this->getTip() . ' Dh',
            ];
        }

        if ($this->isSubscriptionArchiveItem()) {
            $items[] = [
                'thumbnail' => '/build/images/sub-yearly-archive.jpg',
                'title'     => 'اشتراك سنوي + أرشيف',
                'price'     => $price . ' Dh',
            ];

            return $items;
        }

        $details = $this->getItemDetails();

        if ($this->isMagazineItem()) {
            $items[] = [
                'thumbnail' => $details['thumbnail'] ?? null,
                'title'     => $details['title'] ?? 'Magazine',
                'subtitle'  => 'العدد ' . $details['number'],
                'price'     => $price . ' Dh',
            ];

            return $items;
        }

        $items[] = [
            'thumbnail' => '/build/images/sub-yearly.jpg',
            'title'     => 'اشتراك سنوي',
            'price'     => $price . ' Dh',
        ];

        if (isset($details['number'])) {
            $items[] = [
                'thumbnail' => $details['thumbnail'] ?? null,
                'title'     => $details['title'] ?? 'Magazine',
                'subtitle'  => 'العدد ' . $details['number'],
                'price'     => 'مجاني',
            ];
        }

        return $items;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function getTotal(): float
    {
        return $this->total + $this->getTip();
    }

    public function getFormattedTotal(): string
    {
        return number_format($this->getTotal(), 2);
    }

    public function setUser(?User $user): void
    {
        $this->user = $user;
    }

    public function setTotal(float $total): void
    {
        $this->total = $total;
    }
}
