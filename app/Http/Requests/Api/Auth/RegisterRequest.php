<?php

namespace App\Http\Requests\Api\Auth;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'type'                  => ['required', 'string', 'in:individual,company'],
            'name'                  => ['required', 'string', 'max:255'],
            'ice' => [
                'required_if:type,company',
                'string',
                'size:15'
            ],
            'email'                 => ['required', 'lowercase', 'string', 'email', 'unique:users,email'],
            'password'              => ['required', 'confirmed', 'string'],
            'password_confirmation' => ['required', 'string', 'same:password'],
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
