<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Notifications\ResetPasswordNotification;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable;

    const ID_COLUMN = 'id';
    const NAME_COLUMN = 'name';
    const EMAIL_COLUMN = 'email';
    const PASSWORD_COLUMN = 'password';

    protected $fillable = [
        self::NAME_COLUMN,
        self::EMAIL_COLUMN,
        self::PASSWORD_COLUMN,
    ];

    protected $hidden = [
        self::PASSWORD_COLUMN,
    ];

    protected function casts(): array
    {
        return [
            self::PASSWORD_COLUMN   => 'hashed',
        ];
    }

    public function getId(): int
    {
        return $this->getAttribute(self::ID_COLUMN);
    }

    public function getName(): string
    {
        return $this->getAttribute(self::NAME_COLUMN);
    }

    public function getEmail(): string
    {
        return $this->getAttribute(self::EMAIL_COLUMN);
    }
}
