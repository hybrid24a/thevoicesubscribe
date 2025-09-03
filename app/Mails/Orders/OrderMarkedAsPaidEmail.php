<?php

namespace App\Mails\Orders;

use App\Models\Order;
use App\Services\SettingsService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderMarkedAsPaidEmail extends Mailable
{
    use Queueable, SerializesModels;

    /** @var string */
    private $brandName;

    /** @var string */
    private $userName;

    /** @var Order  */
    private $order;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(
        string $userName,
        Order $order
    ) {
        $this->brandName = 'thevoice.ma';
        $this->userName = $userName;
        $this->order = $order;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $paymentDetails = $this->order->getLastPaymentDetails();

        return $this->view('emails.orders.paid', [
            'brandName'      => $this->brandName,
            'userName'       => $this->userName,
            'order'          => $this->order,
            'paymentDetails' => $paymentDetails,
        ]);
    }
}
