<?php

namespace App\Http\Controllers\Checkout;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PaymentDetails;
use App\Services\PayzoneService;
use App\Services\OrdersService;
use App\Services\UsersService;

class PaymentController extends Controller
{
    /** @var OrdersService */
    private $ordersService;

    /** @var PayzoneService */
    private $payzoneService;

    /** @var UsersService */
    private $usersService;

    public function __construct(
        OrdersService $ordersService,
        PayzoneService $payzoneService,
        UsersService $usersService
    ) {
        $this->ordersService = $ordersService;
        $this->payzoneService = $payzoneService;
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

        if ($lastPaymentDetails->getPaymentMethod() !== PaymentDetails::PAYZONE) {
            return redirect()->route('store.cart.show');
        }

        $payzoneData = $this->payzoneService->generateFormData($order);

        return view('payzone', $payzoneData);
    }

    public function paymentCallback(Request $request)
    {
        $orderNumber = $request->get('orderId');
        Log::info('Payment Callback received', ['order_number' => $orderNumber]);
        $order = $this->ordersService->getByNumber($orderNumber);

        if (false === $order instanceof Order) {
            Log::error('Order not found in payment callback', ['order_number' => $orderNumber]);

            $data = ['status' => 'KO', 'message' => 'Order not found'];
            header('Content-Type: application/json');
            echo json_encode($data);

            return;
        }

        $notificationKey = config('payzone.notification_key');

        $input = file_get_contents('php://input');
        $signature = hash_hmac('sha256', $input, $notificationKey);
        $headers = apache_request_headers();
        $inputData = json_decode($input ,true);

        if (strcasecmp($signature, $headers['X-Callback-Signature']) != 0) {
            $this->ordersService->cancelOrder($order, $inputData);

            $data = ['status' => 'KO', 'message' => 'Error signature'];
            header('Content-Type: application/json');
            echo json_encode($data);
        }

        if($inputData['status'] == 'CHARGED'){
            $transactionData = null;

            foreach($inputData['transactions'] as $transaction){
                if($transaction['state'] == 'APPROVED'){
                    $transactionData = $transaction;
                }
            }

            if ($transactionData['resultCode'] === 0) {
                $this->ordersService->fulfillOrder($order, $inputData);

                $data = ['status' => 'OK', 'message' => 'Status recorded successfully'];
                header('Content-Type: application/json');
                echo json_encode($data);

                return;
            }

            $this->ordersService->cancelOrder($order, $inputData);

            $data = ['status' => 'KO', 'message' => 'Status not recorded successfully'];
            header('Content-Type: application/json');
            echo json_encode($data);

            return;
        }

        if($inputData['status'] == 'DECLINED') {
            $transactionData = null;

            foreach($inputData['transactions'] as $transaction){
                if($transaction['state'] == 'DECLINED'){
                    $transactionData = $transaction;
                }
            }

            $this->ordersService->cancelOrder($order, $inputData);

            $data = ['status' => 'KO', 'message' => 'Status not recorded successfully'];
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
