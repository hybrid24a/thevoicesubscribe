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
        Log::info('Payment Callback received', $request->all());

        $orderNumber = $request->get('orderId');
        $order = $this->ordersService->getByNumber($orderNumber);

        // $cmiParams = $request->all();
        // $orderedCmiParams = $this->cmiService->orderParams($cmiParams);

        // $actualHash = $this->cmiService->getCMIHash($orderedCmiParams);
        // $retrievedHash = $cmiParams['HASH'];


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

        //Get the URL and credentials for your paywall
        $notificationKey = 'bAu0pSGHNZALR2AO';

        $input = file_get_contents('php://input');
        $signature = hash_hmac('sha256',$input,$notificationKey);
        $headers = apache_request_headers();

        Log::info('Headers received', $headers);
        Log::info('Request body', ['body' => $input]);
        Log::info('Verifying signature', ['calculated' => $signature, 'received' => $headers['X-Callback-Signature']]);

        if (strcasecmp($signature, $headers['X-Callback-Signature']) == 0) {
            $input_array = json_decode($input ,true);

            if($input_array['status'] == 'CHARGED'){
                $transaction_data = null;
                foreach($input_array['transactions'] as $transaction){
                    if($transaction['state'] == 'APPROVED'){
                            $transaction_data = $transaction;
                    }
                }

                if ($transaction_data['resultCode'] === 0) {
                    //successful payment
                    $data = ['status' => 'OK', 'message' => 'Status recorded successfully'];
                    header('Content-Type: application/json');
                    echo json_encode($data);
                } else {
                    $data = ['status' => 'KO', 'message' => 'Status not recorded successfully'];
                    header('Content-Type: application/json');
                    echo json_encode($data);
                }
            } elseif($input_array['status'] == 'DECLINED') {
                $transaction_data = null;

                foreach($input_array['transactions'] as $transaction){
                    if($transaction['state'] == 'DECLINED'){
                        $transaction_data = $transaction;
                    }
                }

                $data = ['status' => 'KO', 'message' => 'Status not recorded successfully'];
                header('Content-Type: application/json');
                echo json_encode($data);
            }
        } else {
            $data = ['status' => 'KO', 'message' => 'Error signature'];
            header('Content-Type: application/json');
            echo json_encode($data);
        }
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
