<?php

namespace App\Models;

use App\Traits\CartOrderCommonTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cart extends Model
{
    use SoftDeletes, CartOrderCommonTrait;

    protected $guarded = [];

    const SESSION_ID_COLUMN = 'session_id';
    const EXTERNAL_ID_COLUMN = 'external_id';

    const PENDING_STATUS = 'pending';
    const ORDERED_STATUS = 'ordered';

    protected $casts = [
        'item_details' => 'array',
    ];

    public function getSessionId(): string
    {
        return $this->session_id;
    }

    public function getExternalId(): string
    {
        return $this->external_id;
    }
}
