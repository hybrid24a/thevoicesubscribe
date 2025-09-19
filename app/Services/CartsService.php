<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Subscription;
use App\Models\User;
use App\Repositories\CartsRepository;
use App\Services\UsersService;

class CartsService
{
    /** @var CartsRepository */
    private $cartsRepository;

    /** @var UsersService */
    private $usersService;

    public function __construct(
        CartsRepository $cartsRepository,
        UsersService $usersService
    ) {
        $this->cartsRepository = $cartsRepository;
        $this->usersService = $usersService;
    }

    public function getById(int $id): ?Cart
    {
       $cart = $this->cartsRepository->getById($id);

       if ($cart instanceof Cart) {
           $cart = $this->hydrate($cart);
       }

       return $cart;
    }

    public function getBySessionAndExternalId(string $sessionId, string $externalId): ?Cart
    {
       $cart = $this->cartsRepository->getBySessionAndExternalId($sessionId, $externalId);

       if ($cart instanceof Cart) {
           $cart = $this->getById($cart->getId());
       }

       return $cart;
    }

    public function create(array $data): Cart
    {
        $cart = $this->cartsRepository->create($data);
        $cart = $this->hydrate($cart);

        return $cart;
    }

    public function update(Cart $cart, array $data): bool
    {
        return $this->cartsRepository->update($cart->getId(), $data);
    }

    public function attachUserToCart(Cart $cart, User $user): bool
    {
        return $this->cartsRepository->update($cart->getId(), [
            Cart::USER_ID_COLUMN => $user->getId(),
        ]);
    }

    public function delete(int $id): bool
    {
        return $this->cartsRepository->delete($id);
    }


    private function hydrate(Cart $cart): Cart
    {
        $cart = $this->hydrateUser($cart);
        $cart = $this->hydrateTotal($cart);

        return $cart;
    }

    private function hydrateUser(Cart $cart): Cart
    {
        if ($cart->getUserId() === null) {
            return $cart;
        }

        $user = $this->usersService->getById($cart->getUserId());
        $cart->setUser($user);

        return $cart;
    }

    private function hydrateTotal(Cart $cart): Cart
    {
        $cart->setTotal($cart->getPrice() + $cart->getTip());
        $total = $cart->getPrice() + $cart->getTip();
        $cart->setTotal($total);

        return $cart;
    }
}
