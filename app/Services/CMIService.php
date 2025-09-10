<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\PaymentDetails;
use App\Models\User;
use App\Services\CurrenciesService;

class CMIService
{
    public function __construct()
    {
    }

    public function getCMIDefaultsParams()
    {
        return [
            'storetype'     => '3D_PAY_HOSTING',
            'trantype'      => 'PreAuth',
            'rnd'           => microtime(),
            'lang'          => 'fr',
            'hashAlgorithm' => 'ver3',
            'encoding'      => 'UTF-8',
            'refreshtime'   => '5'
        ];
    }

    public function getCMIParams(PaymentDetails $lastPaymentDetails, User $user, string $orderNumber)
    {
        $clientid = config('cmi.client_id');

        $amount = $lastPaymentDetails->getPrice();

        $cmiParams = [
            'clientid'    => $clientid,
            'amount'      => $amount,
            'okUrl'       => route('checkout.cmi.ok'),
            'failUrl'     => route('checkout.cmi.fail'),
            'callbackUrl' => route('checkout.cmi.callback'),
            'shopurl'     => config('app.site_url'),
            'currency'    => '504',
            'BillToName'  => $user->getName(),
            'email'       => $user->getEmail(),
            'oid'         => $orderNumber,
        ];

        // $cmiParams['amountCur'] = $lastPaymentDetails->getObjAmount()->getRoundLocalPrice();
        // $cmiParams['symbolCur'] = $currency->getCode();

        $cmiParams = array_merge($cmiParams, $this->getCMIDefaultsParams());

        return $this->orderParams($cmiParams);
    }

    public function orderParams(array $params): array
    {
        $orderedParams = [];

        $paramsKeys = array_keys($params);
        natcasesort($paramsKeys);

        foreach ($paramsKeys as $paramsKey) {
            $orderedParams[$paramsKey] = $params[$paramsKey];
        }

        return $orderedParams;
    }

    public function getCMIHash(array $cmiParams)
    {
        $storeKey = config('cmi.store_key');

        $hashval = "";

        foreach ($cmiParams as $paramKey => $paramValue){
            $paramValue = trim($paramValue);
            $paramValue = str_replace("|", "\\|", str_replace("\\", "\\\\", $paramValue));

            $paramKey = strtolower($paramKey);

            if($paramKey != "hash" && $paramKey != "encoding" )	{
                $hashval = $hashval . $paramValue . "|";
            }
        }

        $escapedStoreKey = str_replace("|", "\\|", str_replace("\\", "\\\\", $storeKey));
        $hashval = $hashval . $escapedStoreKey;


        $calculatedHashValue = hash('sha512', $hashval);
        $hash = base64_encode(pack('H*',$calculatedHashValue));

        return $hash;
    }
}
