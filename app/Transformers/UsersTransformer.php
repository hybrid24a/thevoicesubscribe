<?php

namespace App\Transformers;

use App\Models\User;
use App\Models\UserEntitlements;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class UsersTransformer
{
    public static function transform(User $user, ?Collection $orders = null): array
    {
        $userData = [
            'user'  => [
                'id'    => $user->getId(),
                'type'  => $user->getType(),
                'name'  => $user->getName(),
                'ice'   => $user->getIce(),
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

        if ($user->getSubscriptions()) {
            $userData['user']['subscriptions'] = $user->getSubscriptions()->map(function ($subscription) {
                return [
                    'plan' => $subscription->getPlan(),
                    'from' => $subscription->getFrom()->toDateTimeString(),
                    'to'   => $subscription->getTo()->toDateTimeString(),
                ];
            });
        }

        if ($user->getEntitlements() && $user->getEntitlements()->isNotEmpty()) {
            $userData['user']['entitlements'] = $user->getEntitlements()->map(function (UserEntitlements $entitlement) {
                return [
                    'type'    => $entitlement->getItemType(),
                    'details' => $entitlement->getItemDetails(),
                ];
            });
        }

        if ($orders === null) {
            $userData['user']['orders'] = [];

            return $userData;
        }

        $userData['user']['orders'] = $orders->map(function ($order) {
            return [
                'number'      => $order->getNumber(),
                'total'       => $order->getTotal(),
                'invoicePath' => rtrim(env('APP_CHECKOUT_URL'), '/') . '/' . ltrim($order->getInvoicePath(), '/'),
                'item'        => $order->getItem(),
                'itemDetails' => $order->getItemDetails(),
                'createdAt'   => $order->getCreatedAt()->format('Y-m-d H:i'),
            ];
        });

        return $userData;
    }
}
