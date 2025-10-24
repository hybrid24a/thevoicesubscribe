<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Order;
use App\Models\PaymentDetails;
use App\Models\User;
use App\Services\CurrenciesService;

class PayzoneService
{
    public function __construct()
    {
    }

    public function generateFormData(Order $order)
    {
        //Get the URL and credentials for your paywall
        $merchantAccount = config('payzone.merchant_account');
        $paywallSecretKey = config('payzone.paywall_secret_key');
        $paywallUrl = config('payzone.paywall_url');

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
        $jsonPayload = json_encode($payload);
        $signature = hash('sha256', $paywallSecretKey . $jsonPayload);

        return [
            'paywallUrl'   => $paywallUrl,
            'payload'      => $jsonPayload,
            'signature'    => $signature,
        ];
    }
}
