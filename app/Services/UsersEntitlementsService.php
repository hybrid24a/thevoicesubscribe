<?php

namespace App\Services;

use Illuminate\Support\Collection;
use App\Models\Cart;
use App\Models\UserEntitlements;
use App\Repositories\UsersEntitlementsRepository;

class UsersEntitlementsService
{
    /** @var UsersEntitlementsRepository */
    private $usersEntitlementsRepository;

    public function __construct(
        UsersEntitlementsRepository $usersEntitlementsRepository
    ) {
        $this->usersEntitlementsRepository = $usersEntitlementsRepository;
    }

    public function getById(int $id): ?UserEntitlements
    {
        return $this->usersEntitlementsRepository->getById($id);
    }

    /**
     * @return Collection|UserEntitlements[]
     */
    public function getByUserId(int $userId)
    {
        return $this->usersEntitlementsRepository->getByUserId($userId);
    }

    public function create(array $data): UserEntitlements
    {
        return $this->usersEntitlementsRepository->create($data);
    }

    public function grantEntitlement(int $userId, array $itemDetails): UserEntitlements
    {
        $data = [
            UserEntitlements::USER_ID_COLUMN       => $userId,
            UserEntitlements::ITEM_TYPE_COLUMN     => Cart::MAGAZINE_ITEM,
            UserEntitlements::ITEM_ID_COLUMN       => $itemDetails['id'],
            UserEntitlements::ITEM_DETAILS_COLUMN  => $itemDetails,
        ];

        return $this->create($data);
    }
}
