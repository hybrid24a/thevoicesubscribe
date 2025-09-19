<?php
namespace App\Http\Controllers\Checkout;

use App\Http\Controllers\Controller;
use App\Http\Requests\Checkout\CheckoutRequest;
use App\Http\Requests\Checkout\MakeOrderRequest;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Subscription;
use App\Models\User;
use App\Services\CartsService;
use App\Services\OrdersService;
use App\Services\UsersService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    /** @var CartsService */
    private $cartsService;

    /** @var UsersService */
    private $usersService;

    /** @var OrdersService */
    private $ordersService;

    public function __construct(
        CartsService $cartsService,
        UsersService $usersService,
        OrdersService $ordersService
    ) {
        $this->cartsService = $cartsService;
        $this->usersService = $usersService;
        $this->ordersService = $ordersService;
    }

    public function index(CheckoutRequest $request)
    {
        $sessionId = $request->getSessionId();

        if (!$sessionId) {
            return redirect(config('app.site_url'));
        }

        $wpData = $request->getWpData();

        if (empty($wpData['cartData'])) {
            return redirect(config('app.site_url'));
        }

        $cartData = $wpData['cartData'];
        $item = $cartData['item']['sku'];
        $itemDetails = [];

        if (isset($cartData['item']['details'])) {
            $itemDetails = $cartData['item']['details'];
        }

        if ($item === Cart::MAGAZINE_ITEM && $itemDetails['is_free']) {
            return redirect(config('app.site_url') . '/account');
        }

        $tip = $cartData['tip'] ?? 0;

        if (!is_int($tip) || $tip < 10) {
            $tip = 0;
        }

        $user = $wpData['user'];

        if ($user instanceof User) {
            if ($item === Cart::MAGAZINE_ITEM) {
                foreach ($user->getEntitlements() as $entitlement) {
                    if ($entitlement->getItemType() === 'mag' &&
                        isset($entitlement->getItemDetails()['number']) &&
                        isset($itemDetails['number']) &&
                        $entitlement->getItemDetails()['number'] === $itemDetails['number']) {

                        return redirect(config('app.site_url') . '/account');
                    }
                }
            }

            if ($user->hasActiveSubscription()) {
                $activeSubscription = $user->getActiveSubscription();
                $currentPlan = $activeSubscription->getPlan();

                // if downgrade
                if ($item === Subscription::YEARLY_PLAN && $currentPlan === Subscription::YEARLY_ARCHIVE_PLAN) {
                    return redirect(config('app.site_url') . '/account');
                }

                // if magazine item and yearly archive plan
                if ($item === Cart::MAGAZINE_ITEM && $currentPlan === Subscription::YEARLY_ARCHIVE_PLAN) {
                    return redirect(config('app.site_url') . '/account');
                }

                // if magazine item and yearly plan, check date
                if ($item === Cart::MAGAZINE_ITEM && $currentPlan === Subscription::YEARLY_PLAN) {
                    if (!$itemDetails['date']) {
                        return redirect(config('app.site_url') . '/account');
                    }

                    $itemDate = Carbon::createFromFormat('Y-m-d H:i:s', $itemDetails['date']);

                    $subscriptions = $user->getSubscriptions();

                    foreach ($subscriptions as $subscription) {
                        $from = $subscription->getFrom();
                        $to = $subscription->getTo();

                        if ($itemDate->between($from, $to)) {
                            return redirect(config('app.site_url') . '/account');
                        }
                    }
                }
            }
        }

        $cart = $this->cartsService->getBySessionAndExternalId($sessionId, $cartData['id']);

        $price = $this->getItemPrice($item);

        if ($cart instanceof Cart) {
            $this->cartsService->update($cart, [
                Cart::ITEM_COLUMN         => $item,
                Cart::ITEM_DETAILS_COLUMN => $itemDetails,
                Cart::TIP_COLUMN          => $tip,
                Cart::PRICE_COLUMN        => $price,
            ]);

            $cart = $this->cartsService->getById($cart->getId());
        } else {
            $cart = $this->cartsService->create([
                Cart::USER_ID_COLUMN      => $user instanceof User ? $user->getId() : null,
                Cart::SESSION_ID_COLUMN   => $sessionId,
                Cart::EXTERNAL_ID_COLUMN  => $cartData['id'],
                Cart::STATUS_COLUMN       => Cart::PENDING_STATUS,
                Cart::ITEM_COLUMN         => $item,
                Cart::ITEM_DETAILS_COLUMN => $itemDetails,
                Cart::TIP_COLUMN          => $tip,
                Cart::PRICE_COLUMN        => $price,
            ]);
        }

        $price = $cartData['price'];
        $price = number_format($price, 2);

        return view('payment', [
            'isAuthenticated' => $wpData['isAuthenticated'],
            'user'            => $user,
            'cart'            => $cart,
            'price'           => $price,
        ]);
    }

    public function store(MakeOrderRequest $request)
    {
        $sessionId = $request->getSessionId();

        if (!$sessionId) {
            return redirect(config('app.site_url'));
        }

        $wpData = $request->getWpData();

        if (empty($wpData['cartData'])) {
            return redirect(config('app.site_url'));
        }

        $cartData = $wpData['cartData'];
        $cart = $this->cartsService->getBySessionAndExternalId($sessionId, $cartData['id']);

        if (!$cart instanceof Cart) {
            return redirect(config('app.site_url'));
        }

        $user = $wpData['user'];

        if (false === $user instanceof User) {
            $user = $this->usersService->create($request->getUserData());

            $this->cartsService->attachUserToCart($cart, $user);
        }

        $cart = $this->cartsService->getById($cart->getId());

        $cartOrder = $this->ordersService->getByCartId($cart->getId());

        if ($cartOrder instanceof Order) {
            if ($cartOrder->getStatus() === Order::FULFILLED_STATUS || $cartOrder->hasBeenPaid()) {
                $request->deleteWpCartSession();
                echo "La commande est déjà traitée.";

                return;
            }

            $order = $cartOrder;
        } else {
            $order = $this->ordersService->makeOrder($cart);
        }

        // Http::post(route('checkout.cmi.callback'), [
        //     'oid' => $order->getNumber(),
        // ]);


        $order = $this->ordersService->getByNumber($order->getNumber());
        $this->ordersService->fulfillOrder($order);

        return view('simulate-cmi', ['order' => $order]);
    }

    public function checkoutLogin()
    {
        return redirect(config('app.site_url') . '/login?redirect_to=checkout');
    }

    private function getItemPrice(string $item): int
    {
        if (isset(Cart::AVAILABLE_ITEMS[$item])) {
            return Cart::AVAILABLE_ITEMS[$item]['price'];
        }

        if (isset(Subscription::AVAILABLE_PLANS[$item])) {
            return Subscription::AVAILABLE_PLANS[$item]['price'];
        }

        return 0;
    }
}
