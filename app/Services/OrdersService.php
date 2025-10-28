<?php

namespace App\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Collection;
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

    /** @var InvoicesService */
    private $invoicesService;

    /** @var ReservedInvoicesService */
    private $reservedInvoicesService;

    public function __construct(
        OrdersRepository $ordersRepository,
        UsersService $usersService,
        PaymentDetailsService $paymentDetailsService,
        SubscriptionsService $subscriptionsService,
        CartsService $cartsService,
        UsersEntitlementsService $usersEntitlementsService,
        InvoicesService $invoicesService,
        ReservedInvoicesService $reservedInvoicesService
    ) {
        $this->ordersRepository = $ordersRepository;
        $this->usersService = $usersService;
        $this->paymentDetailsService = $paymentDetailsService;
        $this->subscriptionsService = $subscriptionsService;
        $this->cartsService = $cartsService;
        $this->usersEntitlementsService = $usersEntitlementsService;
        $this->invoicesService = $invoicesService;
        $this->reservedInvoicesService = $reservedInvoicesService;
    }

    public function getById(int $id, bool $hydrateUser = true): ?Order
    {
        $order = $this->ordersRepository->getById($id);

        if ($order instanceof Order) {
            $order = $this->hydrate($order, $hydrateUser);
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

    /**
     * @return Collection|Order[]
     */
    public function getAll()
    {
        $orders = $this->ordersRepository->getAll();

        foreach ($orders as $key => $order) {
            $orders[$key] = $this->getById($order->getId());
        }

        return $orders;
    }

    /**
     * @return Collection|Order[]
     */
    public function getByUser(User $user)
    {
        $orders = $this->ordersRepository->getByUserId($user->getId());

        foreach ($orders as $key => $order) {
            $orders[$key] = $this->getById($order->getId(), false);
        }

        return $orders;
    }

    /**
     * @return Collection|Order[]
     */
    public function getFulfilledByUser(User $user)
    {
        $orders = $this->ordersRepository->getFulfilledByUserId($user->getId());

        foreach ($orders as $key => $order) {
            $orders[$key] = $this->getById($order->getId(), false);
        }

        return $orders;
    }

    public function getCurrentYearLastInvoicedOrder(): ?Order
    {
        $order = $this->ordersRepository->getLastInvoicedOrderByYear(date('Y'));

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
            Order::PRICE_COLUMN         => $cart->getPrice(),
            Order::TIP_COLUMN           => $cart->getTip(),
        ]);

        $this->paymentDetailsService->create($order, [
            PaymentDetails::AMOUNT_COLUMN         => $cart->getTotal(),
            PaymentDetails::STATUS_COLUMN         => PaymentDetails::PENDING_STATUS,
            PaymentDetails::PAYMENT_METHOD_COLUMN => PaymentDetails::PAYZONE,
        ]);

        $order = $this->getById($order->getId());

        return $order;
    }

    public function update(Order $order, array $data): bool
    {
        return $this->ordersRepository->update($order->getId(), $data);
    }

    public function markPaymentAsPaid(Order $order, array $payload)
    {
        return $this->paymentDetailsService->update($order->getLastPaymentDetails(), [
            PaymentDetails::STATUS_COLUMN  => PaymentDetails::PAID_STATUS,
            PaymentDetails::PAYLOAD_COLUMN => $payload,
        ]);
    }

    public function markPaymentAsCanceled(Order $order, array $payload)
    {
        return $this->paymentDetailsService->update($order->getLastPaymentDetails(), [
            PaymentDetails::STATUS_COLUMN  => PaymentDetails::CANCELED_STATUS,
            PaymentDetails::PAYLOAD_COLUMN => $payload,
        ]);
    }

    public function markAsFulfilled(Order $order): bool
    {
        return $this->update($order, [
            Order::STATUS_COLUMN => Order::FULFILLED_STATUS,
        ]);
    }

    public function markAsCanceled(Order $order): bool
    {
        return $this->update($order, [
            Order::STATUS_COLUMN => Order::CANCELED_STATUS,
        ]);
    }

    private function generateInvoice(Order $order): Order
    {
        $invoiceNumber = $this->previouslyUsedInvoiceNumber() + 1;
        $order->setInvoiceNumber($invoiceNumber);

        $invoicePath = $this->invoicesService->generateInvoice($order);

        $this->update($order, [
            Order::INVOICE_PATH_COLUMN   => $invoicePath,
            Order::INVOICE_NUMBER_COLUMN => $invoiceNumber,
        ]);

        $order->setInvoicePath($invoicePath);

        return $order;
    }

    public function fulfillOrder(Order $order, array $payload = [])
    {
        $this->markPaymentAsPaid($order, $payload);

        if ($order->haveASubscriptionItem()) {
            $this->subscriptionsService->subscribe($order->getUserId(), $order->getItem());
        }

        $itemDetails = $order->getItemDetails();

        if (isset($itemDetails['id'])) {
            $this->usersEntitlementsService->grantEntitlement($order->getUserId(), $itemDetails);
        }

        $this->markAsFulfilled($order);
        $order = $this->generateInvoice($order);

        $user = $this->usersService->getById($order->getUserId());
        $orders = $this->getFulfilledByUser($user);
        $this->usersService->updateWpSession($user, $order->getCart()->getSessionId(), $orders);

        event(new OrderMarkedAsPaid($order));
    }

    public function cancelOrder(Order $order, array $payload = [])
    {
        $this->markPaymentAsCanceled($order, $payload);
        $this->markAsCanceled($order);
    }

    public function updatePayload(Order $order, array $payload): bool
    {
        return $this->paymentDetailsService->update($order->getLastPaymentDetails(), [
            PaymentDetails::PAYLOAD_COLUMN => $payload,
        ]);
    }

    public function previouslyUsedInvoiceNumber(): int
    {
        $lastOrder = $this->getCurrentYearLastInvoicedOrder();
        $lastInvoiceNumber = $lastOrder ? $lastOrder->getInvoiceNumber() : 0;
        $reservedInvoices = $this->reservedInvoicesService->getAll();
        $currentYearReservedInvoices = $reservedInvoices->filter(function ($invoice) {
            return $invoice->getYear() === (int) date('Y');
        });

        if ($currentYearReservedInvoices->isNotEmpty()) {
            $lastReservedInvoiceNumber = $currentYearReservedInvoices->first()->getNumber();

            if ($lastReservedInvoiceNumber > $lastInvoiceNumber) {
                $lastInvoiceNumber = $lastReservedInvoiceNumber;
            }
        }

        return $lastInvoiceNumber;
    }

    private function hydrate(Order $order, bool $hydrateUser): Order
    {
        if ($hydrateUser) {
            $order = $this->hydrateUser($order);
        }

        $order = $this->hydratePaymentDetails($order);
        $order = $this->hydrateCart($order);
        $order = $this->hydrateTotal($order);

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

    private function hydrateTotal(Order $order): Order
    {
        $order->setTotal($order->getPrice() + $order->getTip());
        $total = $order->getPrice() + $order->getTip();
        $order->setTotal($total);

        return $order;
    }

    private function generateOrderNumber(): string
    {
        $orderNumber = Str::random(12);

        while ($this->ordersRepository->getByNumber($orderNumber) instanceof Order) {
            $orderNumber = Str::random(8);
        }

        return mb_strtoupper($orderNumber, 'UTF-8');
    }
}
