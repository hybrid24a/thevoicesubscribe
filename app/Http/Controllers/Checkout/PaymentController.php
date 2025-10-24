<?php

namespace App\Http\Controllers\Checkout;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PaymentDetails;
use App\Services\CMIService;
use App\Services\OrdersService;
use App\Services\UsersService;

class PaymentController extends Controller
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
        Log::info('Redirecting to CMI payment', ['order_number' => $order->getNumber()]);

        // $cmiParams = $this->cmiService->getCMIParams($lastPaymentDetails, $order->getUser(), $order->getNumber());
        // $hash = $this->cmiService->getCMIHash($cmiParams);

        // return view('nulled.payments.cmi', [
        //     'cmiUrl'    => $cmiUrl,
        //     'cmiParams' => $cmiParams,
        //     'hash'      => $hash
        // ]);

        //Get the URL and credentials for your paywall
        $merchantAccount = 'Thevoice_TEST';
        $paywallSecretKey = '84ThzOEPuZEGebMB';
        $paywallUrl = 'https://payment-sandbox.payzone.ma/pwthree/launch';
        $notificationKey = 'bAu0pSGHNZALR2AO';

        //Fill in the payload with parameters for the customer, charge and behavior you want
        $payload = [
            // Authentication parameters
            'merchantAccount'  => $merchantAccount,
            'timestamp'        => time(),
            'skin'             => 'vps-1-vue', // fixed value

            // Customer parameters
            'customerId'      =>  $order->getUserId(), // must be unique for each custumer
            'customerCountry' => 'MA',	  // fixed value
            'customerLocale'  => 'fr_FR',

            // Charge parameters
            'chargeId'        => time(),					// Optional, if defined, it must be unique for each redirection to the payment page
            'orderId'         => $order->getNumber(),                  // Optional, to identify the cart
            'price'           => $order->getTotal(),
            'currency'        => 'MAD',
            'description'     => 'مجلة لسان المغرب',

            // Deep linking
            'mode' => 'DEEP_LINK',	// fixed value
            'paymentMethod' => 'CREDIT_CARD',	 // fixed value
            'showPaymentProfiles' => 'false',
            'callbackUrl' => route('checkout.pay.callback'),
            'successUrl' => route('checkout.pay.ok', ['number' => $order->getNumber()]),
            'failureUrl' => route('checkout.pay.fail', ['number' => $order->getNumber()]),
            'cancelUrl' => config('app.site_url'),
        ];

        // Encode the payload
        $json_payload = json_encode($payload);
        $signature    = hash('sha256', $paywallSecretKey . $json_payload);
?>
<!-- POST the parameters to the paywall -->
<form id="openPaywall" action="<?php echo $paywallUrl; ?>" method="POST" >
	<input type="hidden" name="payload" value='<?php echo $json_payload; ?>' />
	<input type="hidden" name="signature" value="<?php echo $signature; ?>" />
</form>

<script type="text/javascript">
	document.getElementById("openPaywall").submit();
</script>
<?php
    }

    public function paymentCallback(Request $request)
    {
        Log::info('Payment Callback received', ['request' => $request->all()]);

        // $orderNumber = $request->get('oid');
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

        // $order = $this->ordersService->getByNumber($orderNumber);
        // $this->ordersService->fulfillOrder($order);
    }

    public function ok(Request $request, string $number)
    {
        return redirect(config('app.site_url') . '/thank-you?order=' . $number);
    }

    public function fail(Request $request, string $number)
    {
        echo 'عملية الدفع فشلت. الرجاء المحاولة مرة أخرى.';
    }
}
