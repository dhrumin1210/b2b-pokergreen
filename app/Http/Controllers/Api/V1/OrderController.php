<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\Create;
use App\Http\Resources\Order\Collection;
use App\Http\Resources\Order\Resource;
use App\Services\OrderService;
use App\Traits\ApiResponser;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

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
        $orders = $this->orderService->myOrders($request->all());
        return $this->collection(new Collection($orders));
    }

    public function show(int $id)
    {
        $order = $this->orderService->myOrder($id);
        return $this->resource(new Resource($order));
    }

    public function store(Create $request)
    {
        $order = $this->orderService->createOrder($request->validated());
        return $this->resource(new Resource($order));
    }

    public function downloadInvoice(int $id)
    {
        $order = $this->orderService->myOrder($id);
        $order->load(['user', 'orderProducts.product', 'orderProducts.productVariant']);
        
        $pdf = PDF::loadView('invoices.order-invoice', ['order' => $order]);
        
        return $pdf->download("order-{$order->id}-invoice.pdf");
    }
}
