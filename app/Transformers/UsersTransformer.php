<?php

namespace App\Transformers;

use App\Models\User;
use App\Models\UserEntitlements;

class UsersTransformer
{
    public static function transform(User $user): array
    {
        $userData = [
            'user'  => [
                'id'    => $user->getId(),
                'name'  => $user->getName(),
                'email' => $user->getEmail(),
            ],
        ];

        if ($user->getActiveSubscription() !== null) {
            $userData['user']['subscription'] = [
                'plan' => $user->getActiveSubscription()->getPlan(),
                'from' => $user->getActiveSubscription()->getFrom()->toDateTimeString(),
                'to'   => $user->getActiveSubscription()->getTo()->toDateTimeString(),
            ];
        }

        if ($user->getEntitlements()->isNotEmpty()) {
            $userData['user']['entitlements'] = $user->getEntitlements()->map(function (UserEntitlements $entitlement) {
                return [
                    'type'    => $entitlement->getItemType(),
                    'details' => $entitlement->getItemDetails(),
                ];
            });
        }

        return $userData;
    }
}
