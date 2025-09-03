<?php

namespace App\Services;

use Illuminate\Support\Str;
use App\Models\Order;
use App\Models\Cart;
use App\Models\PaymentDetails;
use App\Models\User;
use App\Repositories\OrdersRepository;
use App\Services\UsersService;
use App\Events\Orders\OrderMarkedAsPaid;
use App\Services\UsersEntitlementsService;

class OrdersService
{
    /** @var OrdersRepository */
    private $ordersRepository;

    /** @var UsersService */
    private $usersService;

    /** @var PaymentDetailsService */
    private $paymentDetailsService;

    /** @var SubscriptionsService */
    private $subscriptionsService;

    /** @var CartsService */
    private $cartsService;

    /** @var UsersEntitlementsService */
    private $usersEntitlementsService;

    public function __construct(
        OrdersRepository $ordersRepository,
        UsersService $usersService,
        PaymentDetailsService $paymentDetailsService,
        SubscriptionsService $subscriptionsService,
        CartsService $cartsService,
        UsersEntitlementsService $usersEntitlementsService
    ) {
        $this->ordersRepository = $ordersRepository;
        $this->usersService = $usersService;
        $this->paymentDetailsService = $paymentDetailsService;
        $this->subscriptionsService = $subscriptionsService;
        $this->cartsService = $cartsService;
        $this->usersEntitlementsService = $usersEntitlementsService;
    }

    public function getById(int $id): ?Order
    {
        $order = $this->ordersRepository->getById($id);

        if ($order instanceof Order) {
            $order = $this->hydrate($order);
        }

        return $order;
    }

    public function getByNumber(string $number): ?Order
    {
        $order = $this->ordersRepository->getByNumber($number);

        if ($order instanceof Order) {
            $order = $this->getById($order->getId());
        }

        return $order;
    }

    public function getByCartId(int $cartId): ?Order
    {
        $order = $this->ordersRepository->getByCartId($cartId);

        if ($order instanceof Order) {
            $order = $this->getById($order->getId());
        }

        return $order;
    }

    public function create(array $data): Order
    {
        return $this->ordersRepository->create($data);
    }

    public function makeOrder(Cart $cart): Order {
        $user = $cart->getUser();

        $order = $this->create([
            Order::NUMBER_COLUMN        => $this->generateOrderNumber(),
            Order::USER_ID_COLUMN       => $user->getId(),
            Order::CART_ID_COLUMN       => $cart->getId(),
            Order::STATUS_COLUMN        => Order::OPEN_STATUS,
            Order::ITEM_COLUMN          => $cart->getItem(),
            Order::ITEM_DETAILS_COLUMN  => $cart->getItemDetails(),
            Order::TOTAL_COLUMN         => $cart->getTotal(),
        ]);

        $this->paymentDetailsService->create($order, [
            PaymentDetails::AMOUNT_COLUMN         => $cart->getTotal(),
            PaymentDetails::STATUS_COLUMN         => PaymentDetails::PENDING_STATUS,
            PaymentDetails::PAYMENT_METHOD_COLUMN => PaymentDetails::CMI,
        ]);

        $order = $this->getById($order->getId());

        // if ($order->getItem)

        return $order;
    }

    public function markPaymentAsPaid(Order $order)
    {
        $isUpdated = $this->paymentDetailsService->update($order->getLastPaymentDetails(), [
            PaymentDetails::STATUS_COLUMN => PaymentDetails::PAID_STATUS,
        ]);

        if ($isUpdated) {
            event(new OrderMarkedAsPaid($order));
        }

        return $isUpdated;
    }

    public function markAsFulfilled(Order $order): bool
    {
        return $this->ordersRepository->update($order->getId(), [
            Order::STATUS_COLUMN => Order::FULFILLED_STATUS,
        ]);
    }

    public function fulfillOrder(Order $order)
    {
        if ($order->haveASubscriptionItem()) {
            $this->subscriptionsService->subscribe($order->getUserId(), $order->getItem());
        }

        $itemDetails = $order->getItemDetails();
        if (isset($itemDetails['id'])) {
            $this->usersEntitlementsService->grantEntitlement($order->getUserId(), $itemDetails);
        }

        $this->markAsFulfilled($order);
        $user = $this->usersService->getById($order->getUserId());

        $this->usersService->updateWpSession($user, $order->getCart()->getSessionId());
    }

    public function updatePayload(Order $order, array $payload): bool
    {
        return $this->paymentDetailsService->update($order->getLastPaymentDetails(), [
            PaymentDetails::PAYLOAD_COLUMN => $payload,
        ]);
    }

    private function hydrate(Order $order): Order
    {
        $order = $this->hydrateUser($order);
        $order = $this->hydratePaymentDetails($order);
        $order = $this->hydrateCart($order);

        return $order;
    }

    private function hydrateUser(Order $order): Order
    {
        $userId = $order->getUserId();
        $user = $this->usersService->getById($userId);

        if($user instanceof User) {
            $order->setUser($user);
        }

        return $order;
    }

    private function hydratePaymentDetails(Order $order): Order
    {
        $paymentDetails = $this->paymentDetailsService->getByOrder($order);

        $order->setPaymentDetails($paymentDetails);

        return $order;
    }

    private function hydrateCart(Order $order): Order
    {
        $cart = $this->cartsService->getById($order->getCartId());

        if ($cart instanceof Cart) {
            $order->setCart($cart);
        }

        return $order;
    }

    private function generateOrderNumber(): string
    {
        $orderNumber = Str::random(8);

        while ($this->ordersRepository->getByNumber($orderNumber) instanceof Order) {
            $orderNumber = Str::random(8);
        }

        return mb_strtoupper($orderNumber, 'UTF-8');
    }
}
