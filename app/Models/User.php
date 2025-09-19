<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Notifications\ResetPasswordNotification;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    const ID_COLUMN = 'id';
    const TYPE_COLUMN = 'type';
    const NAME_COLUMN = 'name';
    const ICE_COLUMN = 'ice';
    const EMAIL_COLUMN = 'email';
    const PASSWORD_COLUMN = 'password';
    const REMEMBER_TOKEN_COLUMN = 'remember_token';
    const EMAIL_VERIFIED_AT_COLUMN = 'email_verified_at';

    protected $fillable = [
        self::TYPE_COLUMN,
        self::NAME_COLUMN,
        self::EMAIL_COLUMN,
        self::PASSWORD_COLUMN,
        self::ICE_COLUMN,
    ];

    protected $hidden = [
        self::PASSWORD_COLUMN,
        self::REMEMBER_TOKEN_COLUMN,
    ];

    protected function casts(): array
    {
        return [
            self::EMAIL_VERIFIED_AT_COLUMN => 'datetime',
            self::PASSWORD_COLUMN          => 'hashed',
        ];
    }

    /** @var Subscription|null */
    private $activeSubscription;

    /** @var Collection|Subscription[] */
    private $subscriptions;

    /** @var Collection|UserEntitlements[] */
    private $entitlements;

    /** @var Collection|Order[] */
    private $orders;

    public function getId(): int
    {
        return $this->getAttribute(self::ID_COLUMN);
    }

    public function getType(): string
    {
        return $this->getAttribute(self::TYPE_COLUMN);
    }

    public function isIndividual(): bool
    {
        return $this->getType() === 'individual';
    }

    public function isCompany(): bool
    {
        return $this->getType() === 'company';
    }

    public function getName(): string
    {
        return $this->getAttribute(self::NAME_COLUMN);
    }

    public function getIce(): ?string
    {
        return $this->getAttribute(self::ICE_COLUMN);
    }

    public function getEmail(): string
    {
        return $this->getAttribute(self::EMAIL_COLUMN);
    }

    /**
     * @return Collection|Subscription[]
     */
    public function getSubscriptions()
    {
        if (!($this->subscriptions instanceof Collection)) {
            return collect();
        }

        return $this->subscriptions;
    }

    public function getActiveSubscription(): ?Subscription
    {
        return $this->activeSubscription;
    }

    public function hasActiveSubscription(): bool
    {
        return $this->activeSubscription instanceof Subscription;
    }

    /**
     * @return Collection|UserEntitlements[]
     */
    public function getEntitlements()
    {
        return $this->entitlements;
    }

    /**
     * @return Collection|Order[]
     */
    public function getOrders()
    {
        if (!($this->orders instanceof Collection)) {
            return collect();
        }

        return $this->orders;
    }

    public function setActiveSubscription(?Subscription $subscription): void
    {
        $this->activeSubscription = $subscription;
    }

    public function setSubscriptions(Collection $subscriptions): void
    {
        $this->subscriptions = $subscriptions;
    }

    /**
     * @param Collection|UserEntitlements[] $entitlements
     */
    public function setEntitlements(Collection $entitlements): void
    {
        $this->entitlements = $entitlements;
    }

    /**
     * @param Collection|Order[] $orders
     */
    public function setOrders(Collection $orders): void
    {
        $this->orders = $orders;
    }

    // app/Models/User.php
    public function sendPasswordResetNotification($token): void
    {
        $url = config('app.site_url') . '/reset-password?token=' . $token . '&email=' . urlencode($this->getEmailForPasswordReset());

        $this->notify(new ResetPasswordNotification($url));
    }
}
