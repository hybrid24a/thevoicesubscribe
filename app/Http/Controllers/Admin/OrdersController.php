<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrdersService;
use App\Services\ReservedInvoicesService;
use Illuminate\Foundation\Http\FormRequest;

class OrdersController extends Controller
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

    public function list(FormRequest $request)
    {
        $admin = auth()->guard('admin')->user();

        $orders = $this->ordersService->getAll();

        return view('admin.orders.list', [
            'admin'                => $admin,
            'orders'               => $orders,
            'orderStatusesDisplay' => Order::STATUSES_DISPLAY,
        ]);
    }
}
