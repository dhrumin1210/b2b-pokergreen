<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponser;
use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Admin / Dashboard')]
class DashboardController extends Controller
{
    use ApiResponser;

    #[OA\Get(
        path: '/api/v1/admin/dashboard-stats',
        tags: ['Admin / Dashboard'],
        summary: 'Get dashboard statistics',
        operationId: 'getDashboardStats',
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
            new OA\Response(response: 200, description: 'Dashboard statistics.'),
            new OA\Response(response: 401, description: 'Unauthorized'),
        ],
    )]
    public function stats(Request $request)
    {
        $today = now()->startOfDay();
        $todayOrderCount = Order::whereDate('created_at', $today)->count();
        $totalOrderCount = Order::count();
        $productCount = Product::count();
        $categoryCount = Category::count();
        $userCount = User::count();
        $receivedOrderCount = Order::where('status', 'received')->count();
        $processedOrderCount = Order::where('status', 'processed')->count();
        $deliveredOrderCount = Order::where('status', 'delivered')->count();


        return $this->success([
            'today_order_count' => $todayOrderCount,
            'total_order_count' => $totalOrderCount,
            'product_count' => $productCount,
            'category_count' => $categoryCount,
            'user_count' => $userCount,
            'received_order_count' => $receivedOrderCount,
            'processed_order_count' => $processedOrderCount,
            'delivered_order_count' => $deliveredOrderCount,
        ]);
    }
}
