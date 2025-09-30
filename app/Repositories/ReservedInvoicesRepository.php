<?php

namespace App\Repositories;

use App\Models\ReservedInvoice;
use Illuminate\Support\Collection;

class ReservedInvoicesRepository
{
    public function getById(int $id): ?ReservedInvoice
    {
        return ReservedInvoice::query()
            ->where(ReservedInvoice::ID_COLUMN, $id)
            ->first();
    }

    /**
     * @return ReservedInvoice[]|Collection
     */
    public function getAll()
    {
        return ReservedInvoice::query()
            ->orderByDesc(ReservedInvoice::YEAR_COLUMN)
            ->orderByDesc(ReservedInvoice::NUMBER_COLUMN)
            ->get();
    }

    public function create(array $data): ReservedInvoice
    {
        return ReservedInvoice::query()
            ->create([
                ReservedInvoice::YEAR_COLUMN   => $data[ReservedInvoice::YEAR_COLUMN],
                ReservedInvoice::NUMBER_COLUMN => $data[ReservedInvoice::NUMBER_COLUMN],
            ]);
    }

    public function delete(int $id): bool
    {
        return 1 === ReservedInvoice::query()
            ->where(ReservedInvoice::ID_COLUMN, $id)
            ->delete();
    }
}
