<?php

namespace App\Repositories;

use App\Models\PaymentDetails;
use Illuminate\Support\Arr;

class PaymentDetailsRepository
{
    public function getById(int $id): ?PaymentDetails
    {
        return PaymentDetails::query()
            ->where(PaymentDetails::ID_COLUMN, $id)
            ->first();
    }

    /**
     * @return PaymentDetails[]|Collection
     */
    public function getByOrderId(int $orderId)
    {
        return PaymentDetails::query()
            ->select(PaymentDetails::ID_COLUMN)
            ->where(PaymentDetails::ORDER_ID_COLUMN, $orderId)
            ->orderBy(PaymentDetails::CREATED_AT_COLUMN, 'desc')
            ->get();
    }

    public function create(int $orderId, array $data): PaymentDetails
    {
        return PaymentDetails::query()
            ->create([
                PaymentDetails::ORDER_ID_COLUMN       => $orderId,
                PaymentDetails::AMOUNT_COLUMN         => $data[PaymentDetails::AMOUNT_COLUMN],
                PaymentDetails::STATUS_COLUMN         => $data[PaymentDetails::STATUS_COLUMN],
                PaymentDetails::PAYMENT_METHOD_COLUMN => $data[PaymentDetails::PAYMENT_METHOD_COLUMN],
            ]);
    }

    public function update(int $id, array $data): bool
    {
        $data = Arr::only($data, [
            PaymentDetails::STATUS_COLUMN,
            PaymentDetails::PAYLOAD_COLUMN,
        ]);

        return 1 === PaymentDetails::query()
            ->where(PaymentDetails::ID_COLUMN, $id)
            ->update($data);
    }

    public function delete(int $id): bool
    {
        return 1 === PaymentDetails::query()
            ->where(PaymentDetails::ID_COLUMN, $id)
            ->delete();
    }
}
