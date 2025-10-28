<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReservedInvoice;
use App\Services\OrdersService;
use App\Services\ReservedInvoicesService;
use Illuminate\Foundation\Http\FormRequest;

class ParamsController extends Controller
{
    /** @var OrdersService */
    private $ordersService;

    /** @var ReservedInvoicesService */
    private $reservedInvoicesService;

    public function __construct(
        OrdersService $ordersService,
        ReservedInvoicesService $reservedInvoicesService
    ) {
        $this->ordersService = $ordersService;
        $this->reservedInvoicesService = $reservedInvoicesService;
    }

    public function index(FormRequest $request)
    {
        $admin = auth()->guard('admin')->user();

        $lastInvoiceNumber = $this->ordersService->previouslyUsedInvoiceNumber();

        $reservedInvoices = $this->reservedInvoicesService->getAll();

        return view('admin.params.index', [
            'admin'                  => $admin,
            'reservedInvoices'       => $reservedInvoices,
            'availableInvoiceNumber' => $lastInvoiceNumber + 1,
        ]);
    }

    public function store(FormRequest $request)
    {
        $request->validate([
            'invoice_number' => 'required|integer|min:1',
        ]);

        $lastInvoiceNumber = $this->ordersService->previouslyUsedInvoiceNumber();

        $invoiceNumber = (int) $request->input('invoice_number');

        if ($invoiceNumber <= $lastInvoiceNumber) {
            return redirect()->route('admin.params')->withErrors([
                'invoice_number' => 'Le numéro de facture doit être supérieur au dernier numéro de facture utilisé ou réservé.',
            ])->withInput();
        }

        $this->reservedInvoicesService->create([
            ReservedInvoice::YEAR_COLUMN   => date('Y'),
            ReservedInvoice::NUMBER_COLUMN => $invoiceNumber,
        ]);

        return redirect()->route('admin.params')
            ->with('success', 'Numéro de facture réservé avec succès.');
    }
}
