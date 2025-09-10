<?php

namespace App\Services;

use Illuminate\Auth\Passwords\TokenRepositoryInterface;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Repositories\UsersRepository;
use App\Transformers\UsersTransformer;
use Illuminate\Support\Facades\Http;

class UsersService
{
    /** @var TokenRepositoryInterface */
    protected $tokens;

    /** @var UsersRepository */
    private $usersRepository;

    /** @var SubscriptionsService */
    private $subscriptionsService;

    /** @var UsersTransformer */
    private $usersTransformer;

    /** @var UsersEntitlementsService */
    private $usersEntitlementsService;

    public function __construct(
        UsersRepository $usersRepository,
        SubscriptionsService $subscriptionsService,
        UsersTransformer $usersTransformer,
        UsersEntitlementsService $usersEntitlementsService
    ) {
        $this->usersRepository = $usersRepository;
        $this->subscriptionsService = $subscriptionsService;
        $this->usersTransformer = $usersTransformer;
        $this->usersEntitlementsService = $usersEntitlementsService;
    }

    public function getById(int $id): ?User
    {
        $user = $this->usersRepository->getById($id);

        if ($user instanceof User) {
            $user = $this->hydrate($user);
        }

        return $user;
    }

    public function getByEmail(string $email): ?User
    {
        $user = $this->usersRepository->getByEmail($email);

        if ($user instanceof User) {
            $user = $this->getById($user->getId());
        }

        return $user;
    }

    public function create(array $data): User
    {
        $data[User::PASSWORD_COLUMN] = Hash::make($data[User::PASSWORD_COLUMN]);

        $user = $this->usersRepository->create($data);

        event(new Registered($user));

        return $user;
    }

    public function update(User $user, array $data): bool
    {
        if (isset($data[User::PASSWORD_COLUMN])) {
            $data[User::PASSWORD_COLUMN] = Hash::make($data[User::PASSWORD_COLUMN]);
        }

        return $this->usersRepository->update($user->getId(), $data);
    }

    public function updateWpSession(User $user, string $sessionId)
    {
        $userData = $this->usersTransformer->transform($user);

        $res = Http::withHeaders([
            'Cookie' => 'thevoice_sess=' . $sessionId,
            'Accept' => 'application/json',
        ])->post(config('app.site_url') . '/wp-json/thevoice/v1/session/refresh', $userData);
    }

    public function hydrate(User $user): User
    {
        $subscription = $this->subscriptionsService->getActiveByUserId($user->getId());

        $user->setActiveSubscription($subscription);

        $entitlements = $this->usersEntitlementsService->getByUserId($user->getId());
        $user->setEntitlements($entitlements);

        return $user;
    }
}
