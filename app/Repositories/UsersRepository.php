<?php

namespace App\Repositories;

use App\Models\User;

class UsersRepository
{
    public function getById(int $id): ?User
    {
        return User::query()
            ->where(User::ID_COLUMN, $id)
            ->first();
    }

    public function getByEmail(string $email): ?User
    {
        return User::query()
            ->select(User::ID_COLUMN)
            ->where(User::EMAIL_COLUMN, $email)
            ->first();
    }

    public function create(array $data): User
    {
        return User::query()
            ->create([
                User::NAME_COLUMN      => $data[User::NAME_COLUMN],
                User::EMAIL_COLUMN     => $data[User::EMAIL_COLUMN],
                User::PASSWORD_COLUMN  => $data[User::PASSWORD_COLUMN],
            ]);
    }
}
