<?php

namespace App\Services;

use App\Repositories\PaymentDetailsRepository;
use App\Models\Order;
use App\Models\PaymentDetails;

class PaymentDetailsService
{
    /** @var PaymentDetailsRepository */
    private $paymentDetailsRepository;

    public function __construct(
        PaymentDetailsRepository $paymentDetailsRepository
    ) {
        $this->paymentDetailsRepository = $paymentDetailsRepository;
    }

    public function getById(int $id): ?PaymentDetails
    {
        $paymentDetails = $this->paymentDetailsRepository->getById($id);

        if ($paymentDetails instanceof PaymentDetails) {
            $paymentDetails = $this->hydrate($paymentDetails);
        }

        return $paymentDetails;
    }

    /**
     * @return PaymentDetails[]|Collection
     */
    public function getByOrder(Order $order)
    {
        $paymentsDetails = $this->paymentDetailsRepository->getByOrderId($order->getId());

        $paymentsDetails = $paymentsDetails->transform(function (PaymentDetails $paymentDetails) {
            return $this->getById($paymentDetails->getId());
        });

        return $paymentsDetails;
    }

    public function create(Order $order, array $data): PaymentDetails
    {
        return $this->paymentDetailsRepository->create($order->getId(), $data);
    }

    public function update(PaymentDetails $paymentDetails, array $data): bool
    {
        return $this->paymentDetailsRepository->update($paymentDetails->getId(), $data);
    }

    public function hydrate(PaymentDetails $paymentDetails): PaymentDetails
    {
        return $paymentDetails;
    }
}
