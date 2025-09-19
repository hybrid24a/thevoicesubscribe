<?php

namespace App\Repositories;

use App\Models\Subscription;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class SubscriptionsRepository
{
    public function getById(int $id): ?Subscription
    {
        return Subscription::query()
            ->where(Subscription::ID_COLUMN, $id)
            ->first();
    }

    public function getActiveByUserId(int $userId): ?Subscription
    {
        $now = Carbon::now();

        return Subscription::query()
            ->where(Subscription::USER_ID_COLUMN, $userId)
            ->where(Subscription::FROM_COLUMN, '<=', $now)
            ->where(Subscription::TO_COLUMN, '>', $now)
            ->where(Subscription::STATUS_COLUMN, Subscription::ACTIVE_STATUS_VALUE)
            ->orderByDesc(Subscription::TO_COLUMN)
            ->first();
    }

    public function getAllByUserId(int $userId)
    {
        return Subscription::query()
            ->where(Subscription::USER_ID_COLUMN, $userId)
            ->orderByDesc(Subscription::TO_COLUMN)
            ->get();
    }

    public function create(array $data): Subscription
    {
        return Subscription::query()->create([
            Subscription::USER_ID_COLUMN => $data[Subscription::USER_ID_COLUMN],
            Subscription::PLAN_COLUMN    => $data[Subscription::PLAN_COLUMN],
            Subscription::FROM_COLUMN    => $data[Subscription::FROM_COLUMN],
            Subscription::TO_COLUMN      => $data[Subscription::TO_COLUMN],
            Subscription::STATUS_COLUMN  => $data[Subscription::STATUS_COLUMN],
        ]);
    }

    public function update(int $id, array $data): bool
    {
        $data = Arr::only($data, [
            Subscription::STATUS_COLUMN,
            Subscription::TO_COLUMN,
            Subscription::FROM_COLUMN,
            Subscription::PLAN_COLUMN,
        ]);

        return 1 === Subscription::query()
            ->where(Subscription::ID_COLUMN, $id)
            ->update($data);
    }

    public function extendActiveByOneYearAtomic(int $id): bool
    {
        $toCol = Subscription::TO_COLUMN;

        return 1 === Subscription::query()
            ->where(Subscription::ID_COLUMN, $id)
            ->where(Subscription::STATUS_COLUMN, Subscription::ACTIVE_STATUS_VALUE)
            ->update([
                $toCol => DB::raw('DATE_ADD(`' . $toCol . '`, INTERVAL 1 YEAR)'),
            ]);
    }

    public function endActiveNow(int $id, Carbon $now): bool
    {
        return 1 === Subscription::query()
            ->where(Subscription::ID_COLUMN, $id)
            ->where(Subscription::STATUS_COLUMN, Subscription::ACTIVE_STATUS_VALUE)
            ->update([
                Subscription::TO_COLUMN     => $now,
                Subscription::STATUS_COLUMN => Subscription::REPLACED_STATUS_VALUE,
            ]);
    }
}
