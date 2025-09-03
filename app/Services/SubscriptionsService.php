<?php

namespace App\Services;

use App\Models\Subscription;
use App\Repositories\SubscriptionsRepository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class SubscriptionsService
{
    /** @var SubscriptionsRepository */
    private $subscriptionsRepository;

    public function __construct(SubscriptionsRepository $subscriptionsRepository)
    {
        $this->subscriptionsRepository = $subscriptionsRepository;
    }

    public function getById(int $id): ?Subscription
    {
        return $this->subscriptionsRepository->getById($id);
    }

    public function getActiveByUserId(int $userId): ?Subscription
    {
        return $this->subscriptionsRepository->getActiveByUserId($userId);
    }

    public function create(array $data): Subscription
    {
        return $this->subscriptionsRepository->create($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->subscriptionsRepository->update($id, $data);
    }


    public function extendActiveByOneYearAtomic(int $id): bool
    {
        return $this->subscriptionsRepository->extendActiveByOneYearAtomic($id);
    }

    public function subscribe(int $userId, string $plan): Subscription
    {
        if (!isset(Subscription::AVAILABLE_PLANS[$plan])) {
            throw new \InvalidArgumentException('Unknown plan: ' . $plan);
        }

        $now     = Carbon::now();
        $activeSubscription = $this->subscriptionsRepository->getActiveByUserId($userId);

        if (!$activeSubscription) {
            return $this->subscriptionsRepository->create([
                Subscription::USER_ID_COLUMN => $userId,
                Subscription::PLAN_COLUMN    => $plan,
                Subscription::FROM_COLUMN    => $now,
                Subscription::TO_COLUMN      => $now->copy()->addYear(),
                Subscription::STATUS_COLUMN  => Subscription::ACTIVE_STATUS_VALUE,
            ]);
        }

        if ($activeSubscription->getPlan() === $plan) {
            $this->extendActiveByOneYearAtomic($activeSubscription->getId());

            return $this->getById($activeSubscription->getId());
        }

        return DB::transaction(function () use ($activeSubscription, $userId, $plan, $now) {
            $isDone = $this->subscriptionsRepository->endActiveNow($activeSubscription->getId(), $now);

            if (!$isDone) {
                throw new \RuntimeException('Failed to end active subscription');
            }

            return $this->subscriptionsRepository->create([
                Subscription::USER_ID_COLUMN => $userId,
                Subscription::PLAN_COLUMN    => $plan,
                Subscription::FROM_COLUMN    => $now,
                Subscription::TO_COLUMN      => $now->copy()->addYear(),
                Subscription::STATUS_COLUMN  => Subscription::ACTIVE_STATUS_VALUE,
            ]);
        });
    }
}
