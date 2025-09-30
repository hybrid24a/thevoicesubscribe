<?php

namespace App\Services;

use App\Models\Order;
use App\Repositories\OrdersRepository;
use App\Services\UsersService;
use App\Services\UsersEntitlementsService;
use Barryvdh\DomPDF\Facade\Pdf;


class InvoicesService
{
    public function __construct(
    ) {
    }

    public function generateInvoice(Order $order): string
    {
        $logo = public_path('/build/images/the-voice-logo.png');
        $pdf = Pdf::loadView('invoices.invoice', ['order' => $order, 'logo' => $logo]);

        // lets save the file to storage
        $directory = storage_path('app/public/invoices');

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $filePath = 'invoices/invoice_' . $order->getNumber() . '.pdf';
        $pdf->save(storage_path('app/public/' . $filePath));

        return $filePath;
    }
}
