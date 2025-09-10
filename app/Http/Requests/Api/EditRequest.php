<?php

namespace App\Http\Requests\Api;

use App\Models\User;
use App\Services\UsersService;
use Illuminate\Foundation\Http\FormRequest;

class EditRequest extends FormRequest
{
    public function rules(): array
    {
        $validationData = [
            'name'  => ['required', 'string', 'max:255'],
        ];

        $user = $this->user();

        /** @var UsersService */
        $usersService = app()->make(UsersService::class);
        $user = $usersService->getById($user->getId());

        if ($user->isCompany()) {
            $validationData['ice'] = ['required', 'string', 'size:15'];
        }

        return $validationData;
    }

    public function getUserData(): array
    {
        return [
            User::NAME_COLUMN   => $this->input('name'),
            User::ICE_COLUMN    => $this->input('ice'),
        ];
    }
}
