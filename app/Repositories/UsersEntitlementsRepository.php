<?php

namespace App\Repositories;

use Illuminate\Support\Collection;
use App\Models\UserEntitlements;

class UsersEntitlementsRepository
{
    public function getById(int $id): ?UserEntitlements
    {
        return UserEntitlements::query()
            ->where(UserEntitlements::ID_COLUMN, $id)
            ->first();
    }

    /**
     * @return Collection|UserEntitlements[]
     */
    public function getByUserId(int $userId)
    {
        return UserEntitlements::query()
            ->where(UserEntitlements::USER_ID_COLUMN, $userId)
            ->get();
    }

    public function create(array $data): UserEntitlements
    {
        return UserEntitlements::query()
            ->create([
                UserEntitlements::USER_ID_COLUMN       => $data[UserEntitlements::USER_ID_COLUMN],
                UserEntitlements::ITEM_TYPE_COLUMN     => $data[UserEntitlements::ITEM_TYPE_COLUMN],
                UserEntitlements::ITEM_ID_COLUMN       => $data[UserEntitlements::ITEM_ID_COLUMN],
                UserEntitlements::ITEM_DETAILS_COLUMN  => $data[UserEntitlements::ITEM_DETAILS_COLUMN],
            ]);
    }
}
