<?php

namespace App\Repositories;

use App\Models\Order;
use App\Services\QueryBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class OrdersRepository
{
    public function getById(int $id): ?Order
    {
        return Order::query()
            ->where(Order::ID_COLUMN, $id)
            ->first();
    }

    public function getByNumber(string $number): ?Order
    {
        return Order::query()
            ->select(Order::ID_COLUMN)
            ->where(Order::NUMBER_COLUMN, $number)
            ->first();
    }

    public function getByCartId(int $cartId): ?Order
    {
        return Order::query()
            ->select(Order::ID_COLUMN)
            ->where(Order::CART_ID_COLUMN, $cartId)
            ->first();
    }

    public function create(array $data): Order
    {
        return Order::query()
            ->create([
                Order::NUMBER_COLUMN        => $data[Order::NUMBER_COLUMN],
                Order::USER_ID_COLUMN       => $data[Order::USER_ID_COLUMN],
                Order::CART_ID_COLUMN       => $data[Order::CART_ID_COLUMN],
                Order::ITEM_COLUMN          => $data[Order::ITEM_COLUMN],
                Order::ITEM_DETAILS_COLUMN  => $data[Order::ITEM_DETAILS_COLUMN],
                Order::STATUS_COLUMN        => $data[Order::STATUS_COLUMN],
                Order::TOTAL_COLUMN         => $data[Order::TOTAL_COLUMN],
            ]);
    }

    public function update(int $id, array $data): bool
    {
        $data = Arr::only($data, [
            Order::USER_ID_COLUMN,
            Order::STATUS_COLUMN,
            Order::TOTAL_COLUMN,
        ]);

        return 1 === Order::query()
            ->where(Order::ID_COLUMN, $id)
            ->update($data);
    }
}
