<?php

namespace App\Http\Requests\Checkout;

use App\Services\UsersService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Http;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function getSessionId()
    {
        return $this->cookie('thevoice_sess');
    }

    public function getWpData()
    {
        $sessionId = $this->getSessionId();

        $response = Http::withHeaders([
            'Cookie' => 'thevoice_sess=' . $sessionId,
            'Accept' => 'application/json',
        ])->timeout(3)
            ->get('http://the.voice:8080/wp-json/thevoice/v1/checkout-state');

        $wpData = $response->json();

        $cartData = $wpData['cart'];
        $userData = null;
        $user = null;

        if (isset($wpData['authed']) && $wpData['authed'] === true) {
            $userData = $wpData['user'] ?? null;
        }

        if ($userData && isset($userData['email'])) {
            $usersService = app(UsersService::class);
            $user = $usersService->getByEmail($userData['email']);
        }

        return [
            'isAuthenticated' => $userData && isset($userData['email']),
            'cartData'        => $cartData,
            'user'            => $user,
        ];
    }

    public function deleteWpCartSession()
    {
        $sessionId = $this->getSessionId();

        Http::withHeaders([
            'Cookie' => 'thevoice_sess=' . $sessionId,
            'Accept' => 'application/json',
        ])->timeout(3)
            ->post('http://the.voice:8080/wp-json/thevoice/v1/checkout-state/delete');
    }
}
