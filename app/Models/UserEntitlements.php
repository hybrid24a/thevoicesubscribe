<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;

class UserEntitlements extends Model
{
    // Eloquent Related Attributes

    protected $guarded = [];

    // Columns names

    const ID_COLUMN = 'id';
    const USER_ID_COLUMN = 'user_id';
    const ITEM_TYPE_COLUMN = 'item_type';
    const ITEM_ID_COLUMN = 'item_id';
    const ITEM_DETAILS_COLUMN = 'item_details';
    const CREATED_AT_COLUMN = 'created_at';
    const UPDATED_AT_COLUMN = 'updated_at';

    protected $casts = [
        self::ITEM_DETAILS_COLUMN => 'array',
    ];

    // Getters

    public function getId(): int
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->user_id;
    }

    public function getItemType(): string
    {
        return $this->item_type;
    }

    public function getItemId(): int
    {
        return $this->item_id;
    }

    public function getItemDetails(): array
    {
        return $this->item_details;
    }
}
