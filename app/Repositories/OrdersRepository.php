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

    /**
     * @return Collection|Order[]
     */
    public function getByUserId(int $userId)
    {
        return Order::query()
            ->select(Order::ID_COLUMN)
            ->where(Order::USER_ID_COLUMN, $userId)
            ->orderByDesc(Order::CREATED_AT_COLUMN)
            ->get();
    }

    /**
     * @return Collection|Order[]
     */
    public function getFulfilledByUserId(int $userId)
    {
        return Order::query()
            ->select(Order::ID_COLUMN)
            ->where(Order::USER_ID_COLUMN, $userId)
            ->where(Order::STATUS_COLUMN, Order::FULFILLED_STATUS)
            ->orderByDesc(Order::CREATED_AT_COLUMN)
            ->get();
    }

    public function getLastInvoicedOrderByYear(int $year): ?Order
    {
        return Order::query()
            ->whereNotNull(Order::INVOICE_NUMBER_COLUMN)
            ->whereYear(Order::CREATED_AT_COLUMN, $year)
            ->orderByDesc(Order::INVOICE_NUMBER_COLUMN)
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
                Order::PRICE_COLUMN         => $data[Order::PRICE_COLUMN],
                Order::TIP_COLUMN           => $data[Order::TIP_COLUMN],
                Order::INVOICE_PATH_COLUMN  => null,
                Order::INVOICE_NUMBER_COLUMN=> null,
            ]);
    }

    public function update(int $id, array $data): bool
    {
        $data = Arr::only($data, [
            Order::USER_ID_COLUMN,
            Order::STATUS_COLUMN,
            Order::INVOICE_PATH_COLUMN,
            Order::INVOICE_NUMBER_COLUMN,
        ]);

        return 1 === Order::query()
            ->where(Order::ID_COLUMN, $id)
            ->update($data);
    }
}
