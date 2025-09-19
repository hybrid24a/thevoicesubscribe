<?php

namespace App\Http\Controllers\Checkout;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PaymentDetails;
use App\Services\CMIService;
use App\Services\OrdersService;
use App\Services\UsersService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CMIController extends Controller
{
    /** @var OrdersService */
    private $ordersService;

    /** @var CMIService */
    private $cmiService;

    /** @var UsersService */
    private $usersService;

    public function __construct(
        OrdersService $ordersService,
        CMIService $cmiService,
        UsersService $usersService
    ) {
        $this->ordersService = $ordersService;
        $this->cmiService = $cmiService;
        $this->usersService = $usersService;
    }

    public function preparePayment(Request $request, string $order)
    {
        $order = $this->ordersService->getByNumber($order);

        if (!$order instanceof Order) {
            return redirect()->route('store.cart.show');
        }

        $lastPaymentDetails = $order->getLastPaymentDetails();

        if (!$lastPaymentDetails instanceof PaymentDetails) {
            return redirect()->route('store.cart.show');
        }

        if ($lastPaymentDetails->getPaymentMethod() !== 'cmi') {
            return redirect()->route('store.cart.show');
        }

        $cmiUrl = config('cmi.payment_url');

        $cmiParams = $this->cmiService->getCMIParams($lastPaymentDetails, $order->getUser(), $order->getNumber());
        $hash = $this->cmiService->getCMIHash($cmiParams);

        return view('nulled.payments.cmi', [
            'cmiUrl'    => $cmiUrl,
            'cmiParams' => $cmiParams,
            'hash'      => $hash
        ]);
    }

    public function paymentCallback(Request $request)
    {
        $orderNumber = $request->get('oid');
        // $cmiParams = $request->all();
        // $orderedCmiParams = $this->cmiService->orderParams($cmiParams);

        // $actualHash = $this->cmiService->getCMIHash($orderedCmiParams);
        // $retrievedHash = $cmiParams['HASH'];

        // $order = $this->ordersService->getByNumber($orderNumber);

        // if ($order instanceof Order) {
        //     $this->ordersService->updatePayload($order, $cmiParams);
        // }

        // if($retrievedHash == $actualHash)	{
        //     if($_POST["ProcReturnCode"] == "00")	{
        //         echo "ACTION=POSTAUTH";

        //         if ($order instanceof Order) {
        //             $this->ordersService->markPaymentAsPaid($order);
        //         }
        //     } else {
        //         echo "APPROVED";
        //     }
        // } else {
        //     echo "FAILURE";
        // }

        $order = $this->ordersService->getByNumber($orderNumber);
        $this->ordersService->fulfillOrder($order);
    }

    public function ok(Request $request, string $number)
    {
        return redirect(config('app.site_url') . '/thank-you?order=' . $number);

        // return redirect()->route('store.orders.show', [
        //     'number'       => $orderId,
        //     'confirmation' => true,
        //     'cmi_status'   => 'success'
        // ]);
    }

    public function fail(Request $request)
    {
        $orderId = $request->get('oid');

        return redirect()->route('store.orders.show', [
            'number'       => $orderId,
            'confirmation' => true,
            'cmi_status'   => 'failure'
        ]);
    }
}
