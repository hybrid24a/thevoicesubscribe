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
            'type'                  => ['required', 'string', 'in:individual,company'],
            'name'                  => ['required', 'string', 'max:255'],
            'ice' => [
                'required_if:type,company',
                'nullable',
                'string',
                'size:15'
            ],
            'email'                 => ['required', 'string', 'email', 'unique:users,email'],
            'password'              => ['required', 'string'],
            'password_confirmation' => ['required', 'string', 'same:password'],
            'accept_cgv'            => ['accepted'],
        ];
    }

    public function getUserData(): array
    {
        return [
            User::TYPE_COLUMN     => $this->input('type'),
            User::NAME_COLUMN     => $this->input('name'),
            User::ICE_COLUMN      => $this->input('ice'),
            User::EMAIL_COLUMN    => $this->input('email'),
            User::PASSWORD_COLUMN => $this->input('password'),
        ];
    }
}
