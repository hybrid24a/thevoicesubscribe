<?php

namespace App\Traits;

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Collection;

trait CartOrderCommonTrait
{
    const ID_COLUMN = 'id';
    const USER_ID_COLUMN = 'user_id';
    const STATUS_COLUMN = 'status';
    const ITEM_COLUMN = 'item';
    const ITEM_DETAILS_COLUMN = 'item_details';
    const PRICE_COLUMN = 'price';
    const TIP_COLUMN = 'tip';
    const CREATED_AT_COLUMN = 'created_at';
    const UPDATED_AT_COLUMN = 'updated_at';
    const DELETED_AT_COLUMN = 'deleted_at';

    const MAGAZINE_ITEM = 'mag';

    const AVAILABLE_ITEMS = [
        'mag' => [
            'price' => 10,
        ],
    ];

    const TIP_TITLE = [
        'ar' => 'دعم الموقع',
        'fr' => 'Soutien du site',
    ];

    const YEARLY_SUBSCRIPTION_TITLE = [
        'ar' => 'اشتراك سنوي',
        'fr' => 'Abonnement annuel',
    ];

    const YEARLY_SUBSCRIPTION_ARCHIVE_TITLE = [
        'ar' => 'اشتراك سنوي + أرشيف',
        'fr' => 'Abonnement annuel + Archive',
    ];

    const MAGAZINE_NUMBER_TITLE = [
        'ar' => 'العدد',
        'fr' => 'Magazine numéro',
    ];

    const FREE_LABEL = [
        'ar' => 'مجاني',
        'fr' => 'Gratuit',
    ];

    /** @var User */
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

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getItem(): string
    {
        return $this->item;
    }

    public function isMagazineItem(): bool
    {
        return $this->item === self::MAGAZINE_ITEM;
    }

    public function isSubscriptionItem(): bool
    {
        return $this->item === Subscription::YEARLY_PLAN;
    }

    public function isSubscriptionArchiveItem(): bool
    {
        return $this->item === Subscription::YEARLY_ARCHIVE_PLAN;
    }

    public function haveASubscriptionItem(): bool
    {
        $subscriptions = Subscription::ALLOWED_SUBSCRIPTIONS;

        return in_array($this->item, $subscriptions);
    }

    public function getItemDetails(): ?array
    {
        return $this->item_details;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getTip(): float
    {
        return $this->tip;
    }

    public function getTotal(): float
    {
        return $this->total;
    }

    public function getFormattedTotal(): string
    {
        return number_format($this->getTotal(), 2);
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->created_at;
    }

    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updated_at;
    }

    public function getFormattedUpdatedAt(): string
    {
        return $this->updated_at->format('d/m/Y');
    }

    public function getDeletedAt(): ?\DateTimeInterface
    {
        return $this->deleted_at;
    }

    public function getDisplayItems(): array
    {
        $items = [];

        if ($this->getTip() > 0) {
            $items[] = [
                'thumbnail' => '/build/images/tips.jpg',
                'title'     => self::TIP_TITLE['ar'],
                'price'     => $this->getTip() . ' Dh',
            ];
        }

        if ($this->isSubscriptionArchiveItem()) {
            $items[] = [
                'thumbnail' => '/build/images/sub-yearly-archive.jpg',
                'title'     => self::YEARLY_SUBSCRIPTION_ARCHIVE_TITLE['ar'],
                'price'     => $this->price . ' Dh',
            ];

            return $items;
        }

        $details = $this->getItemDetails();

        if ($this->isMagazineItem()) {
            $magazineNumberText = self::MAGAZINE_NUMBER_TITLE['ar'] . ' ' . $details['number'];
            $items[] = [
                'thumbnail' => $details['thumbnail'] ?? null,
                'title'     => $details['title'],
                'subtitle'  => $magazineNumberText,
                'price'     => $this->price . ' Dh',
            ];

            return $items;
        }

        $items[] = [
            'thumbnail' => '/build/images/sub-yearly.jpg',
            'title'     => self::YEARLY_SUBSCRIPTION_TITLE['ar'],
            'price'     => $this->price . ' Dh',
        ];

        if (isset($details['number'])) {
            $magazineNumberText = self::MAGAZINE_NUMBER_TITLE['ar'] . ' ' . $details['number'];
            $items[] = [
                'thumbnail' => $details['thumbnail'] ?? null,
                'title'     => $details['title'],
                'subtitle'  => $magazineNumberText,
                'price'     => self::FREE_LABEL['ar'],
            ];
        }

        return $items;
    }

    public function getUser(): ?User
    {
        return $this->user;
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
