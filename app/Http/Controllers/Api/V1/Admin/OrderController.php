<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Order\Collection;
use App\Http\Resources\Order\Resource;
use App\Models\Order;
use App\Services\OrderService;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\OrdersExport;

class OrderController extends Controller
{
    use ApiResponser;
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index(Request $request)
    {
        $orders = $this->orderService->getOrders($request->all());
        return $this->collection(new Collection($orders));
    }

    public function updateStatus(Order $order, Request $request)
    {
        $request->validate([
            'status' => 'required|string|in:pending,processing,completed,cancelled'
        ]);

        $order = $this->orderService->updateOrderStatus($order->id, $request->status);
        return $this->resource(new Resource($order));
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(new OrdersExport($request->all()), 'orders.xlsx');
    }
}