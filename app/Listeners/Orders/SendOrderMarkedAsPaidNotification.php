<?php

namespace App\Listeners\Orders;

use App\Events\Orders\OrderMarkedAsPaid;
use App\Notifications\Orders\OrderMarkedAsPaidNotification;

class SendOrderMarkedAsPaidNotification
{
    public function handle(OrderMarkedAsPaid $event)
    {
        $order = $event->getOrder();
        $order->getUser()->notify(new OrderMarkedAsPaidNotification($order));
    }
}
