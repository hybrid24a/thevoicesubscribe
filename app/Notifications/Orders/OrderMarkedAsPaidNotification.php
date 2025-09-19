<?php

namespace App\Notifications\Orders;

use App\Models\Order;
use App\Mails\Orders\OrderMarkedAsPaidEmail;
use Illuminate\Notifications\Notification;

class OrderMarkedAsPaidNotification extends Notification
{
    /** @var Order */
    private $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Get the notification's channels.
     *
     * @param  mixed  $notifiable
     * @return array|string
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $makeOrderEmail = new OrderMarkedAsPaidEmail($notifiable->getName(), $this->order);
        $invoicePath = $this->order->getInvoicePath();
        $invoicePath = storage_path('app/public/' . $invoicePath);

        return $makeOrderEmail->subject('Nous avons reçu votre paiement')
            ->to($notifiable->email)
            ->attach($invoicePath);
    }
}
