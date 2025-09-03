<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\OrdersService;
use Illuminate\Foundation\Http\FormRequest;

class OrdersController extends Controller
{
    /** @var OrdersService */
    private $ordersService;

    public function __construct(
        OrdersService $ordersService
    ) {
        $this->ordersService = $ordersService;
    }

    public function get(FormRequest $request, string $number)
    {
        $order = $this->ordersService->getByNumber($number);

        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        return response()->json([
            'number'  => $order->getNumber(),
            'status'  => $order->getStatus(),
            'total'   => $order->getTotal(),
            'item'    => $order->getItem(),
            'details' => $order->getItemDetails(),
            'date'    => $order->getUpdatedAt()->format('Y-m-d H:i:s'),
        ]);
    }
}
