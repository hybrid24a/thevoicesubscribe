<?php

namespace App\Http\Requests\Checkout;

use App\Models\User;

class MakeOrderRequest extends CheckoutRequest
{
    public function rules(): array
    {
        $isAuthenticated = $this->getWpData()['isAuthenticated'];

        if ($isAuthenticated) {
            return [];
        }

        return [
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'string', 'email', 'unique:users,email'],
            'password'              => ['required', 'string'],
            'password_confirmation' => ['required', 'string', 'same:password'],
        ];
    }

    public function getUserData(): array
    {
        return [
            User::NAME_COLUMN     => $this->input('name'),
            User::EMAIL_COLUMN    => $this->input('email'),
            User::PASSWORD_COLUMN => $this->input('password'),
        ];
    }
}
