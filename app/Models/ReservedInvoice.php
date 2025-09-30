<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReservedInvoice extends Model
{
    protected $guarded = [];

    const ID_COLUMN = 'id';
    const YEAR_COLUMN = 'year';
    const NUMBER_COLUMN = 'number';
    const CREATED_AT_COLUMN = 'created_at';
    const UPDATED_AT_COLUMN = 'updated_at';


    public function getId(): int
    {
        return $this->id;
    }

    public function getYear(): int
    {
        return $this->year;
    }

    public function getNumber(): int
    {
        return $this->number;
    }

    public function getCreatedAt()
    {
        return $this->created_at;
    }
}
