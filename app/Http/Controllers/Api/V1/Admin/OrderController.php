<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Models\Order;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use App\Exports\OrdersExport;
use OpenApi\Attributes as OA;
use App\Services\OrderService;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Resources\Order\Resource;
use App\Http\Resources\Order\Collection;
use Illuminate\Support\Facades\Response;

#[OA\Tag(name: 'Admin / Orders')]
class OrderController extends Controller
{
    use ApiResponser;
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    #[OA\Get(
        path: '/api/v1/admin/orders',
        tags: ['Admin / Orders'],
        operationId: 'getOrders',
        summary: 'Get all orders',
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'X-Requested-With',
                in: 'header',
                required: true,
                description: 'Custom header for XMLHttpRequest',
                schema: new OA\Schema(type: 'string', default: 'XMLHttpRequest')
            ),
        ],
        responses: [
            new OA\Response(response: '200', description: 'Success.'),
            new OA\Response(response: '401', description: 'Unauthorized'),
        ],
    )]
    public function index(Request $request)
    {
        $orders = $this->orderService->getOrders($request->all());
        return $this->collection(new Collection($orders));
    }

    #[OA\Put(
        path: '/api/v1/admin/orders/{order}/status',
        tags: ['Admin / Orders'],
        operationId: 'updateOrderStatus',
        summary: 'Update order status',
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'order',
                in: 'path',
                required: true,
                description: 'Order ID',
                schema: new OA\Schema(type: 'integer')
            ),
            new OA\Parameter(
                name: 'X-Requested-With',
                in: 'header',
                required: true,
                description: 'Custom header for XMLHttpRequest',
                schema: new OA\Schema(type: 'string', default: 'XMLHttpRequest')
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: 'object',
                required: ['status'],
                properties: [
                    new OA\Property(property: 'status', type: 'string', example: 'pending'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: '200', description: 'Order status updated successfully.'),
            new OA\Response(response: '401', description: 'Unauthorized'),
            new OA\Response(response: '404', description: 'Order not found'),
            new OA\Response(response: '422', description: 'Validation errors'),
        ],
    )]
    public function updateStatus(Order $order, Request $request)
    {
        $request->validate([
            'status' => 'required|string|in:received,processed,delivered'
        ]);

        $order = $this->orderService->updateOrderStatus($order->id, $request->status);
        return $this->resource(new Resource($order));
    }

    #[OA\Get(
        path: '/api/v1/admin/orders/{order}',
        tags: ['Admin / Orders'],
        operationId: 'showOrder',
        summary: 'Get order details',
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'order',
                in: 'path',
                required: true,
                description: 'Order ID',
                schema: new OA\Schema(type: 'integer')
            ),
            new OA\Parameter(
                name: 'X-Requested-With',
                in: 'header',
                required: true,
                description: 'Custom header for XMLHttpRequest',
                schema: new OA\Schema(type: 'string', default: 'XMLHttpRequest')
            ),
        ],
        responses: [
            new OA\Response(response: '200', description: 'Success.'),
            new OA\Response(response: '401', description: 'Unauthorized'),
            new OA\Response(response: '404', description: 'Order not found'),
        ],
    )]
    public function show(Order $order)
    {
        return $this->resource(new Resource($order));
    }

    #[OA\Get(
        path: '/api/v1/admin/orders/export',
        tags: ['Admin / Orders'],
        operationId: 'exportOrdersExcel',
        summary: 'Export orders to Excel',
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'X-Requested-With',
                in: 'header',
                required: true,
                description: 'Custom header for XMLHttpRequest',
                schema: new OA\Schema(type: 'string', default: 'XMLHttpRequest')
            ),
            new OA\Parameter(
                name: 'start_date',
                in: 'query',
                required: false,
                description: 'Filter by start date (YYYY-MM-DD)',
                schema: new OA\Schema(type: 'string', format: 'date')
            ),
            new OA\Parameter(
                name: 'end_date',
                in: 'query',
                required: false,
                description: 'Filter by end date (YYYY-MM-DD)',
                schema: new OA\Schema(type: 'string', format: 'date')
            ),
            new OA\Parameter(
                name: 'status',
                in: 'query',
                required: false,
                description: 'Filter by order status : ```pending,processing,completed,cancelled```',
            ),
        ],
        responses: [
            new OA\Response(response: '200', description: 'Excel file download.'),
            new OA\Response(response: '401', description: 'Unauthorized'),
        ],
    )]
    public function exportExcel(Request $request)
    {
        return Excel::download(new OrdersExport($request->all()), 'orders.xlsx');
    }
}