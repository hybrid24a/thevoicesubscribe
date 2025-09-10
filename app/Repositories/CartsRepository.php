<?php

namespace App\Repositories;

use App\Models\Cart;
use App\Services\QueryBuilder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;

class CartsRepository
{
    const ALLOWED_SEARCH_ATTRIBUTES = [];

    const ALLOWED_ORDER_ATTRIBUTES = [
        Cart::CREATED_AT_COLUMN,
        Cart::UPDATED_AT_COLUMN,
    ];

    public function getById(int $id): ?Cart
    {
        return Cart::query()
            ->where(Cart::ID_COLUMN, $id)
            ->first();
    }

    public function getBySessionAndExternalId(string $sessionId, string $externalId): ?Cart
    {
        return Cart::query()
            ->select(Cart::ID_COLUMN)
            ->where(Cart::SESSION_ID_COLUMN, $sessionId)
            ->where(Cart::EXTERNAL_ID_COLUMN, $externalId)
            ->first();
    }

    public function create(array $data): Cart
    {
        return Cart::query()
            ->create([
                Cart::USER_ID_COLUMN      => $data[Cart::USER_ID_COLUMN],
                Cart::SESSION_ID_COLUMN   => $data[Cart::SESSION_ID_COLUMN],
                Cart::EXTERNAL_ID_COLUMN  => $data[Cart::EXTERNAL_ID_COLUMN],
                Cart::ITEM_COLUMN         => $data[Cart::ITEM_COLUMN],
                Cart::ITEM_DETAILS_COLUMN => $data[Cart::ITEM_DETAILS_COLUMN],
                Cart::STATUS_COLUMN       => $data[Cart::STATUS_COLUMN],
                Cart::TIP_COLUMN          => $data[Cart::TIP_COLUMN] ?? 0,
            ]);
    }

    public function update(int $id, array $data): bool
    {
        $data = Arr::only($data, [
            Cart::USER_ID_COLUMN,
            Cart::ITEM_COLUMN,
            Cart::ITEM_DETAILS_COLUMN,
            Cart::STATUS_COLUMN,
            Cart::TIP_COLUMN,
        ]);

        return 1 === Cart::query()
            ->where(Cart::ID_COLUMN, $id)
            ->update($data);
    }

    public function delete(int $id): bool
    {
        return 1 === Cart::query()
            ->where(Cart::ID_COLUMN, $id)
            ->delete();
    }
}
