<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

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

    /** @var Collection|UserEntitlements[] */
    private $entitlements;

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

    public function setActiveSubscription(?Subscription $subscription): void
    {
        $this->activeSubscription = $subscription;
    }

    /**
     * @param Collection|UserEntitlements[] $entitlements
     */
    public function setEntitlements(Collection $entitlements): void
    {
        $this->entitlements = $entitlements;
    }
}
