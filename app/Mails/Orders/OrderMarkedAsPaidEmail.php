<?php

namespace App\Mails\Orders;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderMarkedAsPaidEmail extends Mailable
{
    use Queueable, SerializesModels;

    /** @var string */
    private $brandName;

    /** @var string */
    private $brandLogo;

    /** @var string */
    private $userName;

    /** @var Order  */
    private $order;

    /** @var array */
    private $actionButtonUrl;

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
        $this->brandLogo = asset('/build/images/the-voice-logo.png');
        $this->actionButtonUrl = config('app.site_url') . '/magazine';
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
            'brandLogo'      => $this->brandLogo,
            'userName'       => $this->userName,
            'order'          => $this->order,
            'paymentDetails' => $paymentDetails,
            'actionButtonUrl'=> $this->actionButtonUrl,
        ]);
    }
}
