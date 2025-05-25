<?php

namespace App\Services;

use App\Exceptions\CustomException;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\ProductVariant;
use App\Traits\PaginationTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderService
{
    use PaginationTrait;

    protected Order $order;

    public function __construct()
    {
      
        $this->order = new Order;
    }

    public function getOrders(array $inputs)
    {
        $query = $this->order->with(['user', 'orderProducts.product', 'orderProducts.productVariant']);

        if (isset($inputs['start_date'])) {
            $query->whereDate('created_at', '>=', $inputs['start_date']);
        }

        if (isset($inputs['end_date'])) {
            $query->whereDate('created_at', '<=', $inputs['end_date']);
        }

        if (isset($inputs['status'])) {
            $query->where('status', $inputs['status']);
        }

        $query->orderBy('created_at', 'desc');

        return $this->paginationAttribute($query, $inputs);
    }

    public function updateOrderStatus(int $orderId, string $status)
    {
        $order = $this->order->findOrFail($orderId);
        $order->update(['status' => $status]);
        return $order->fresh();
    }

    public function myOrders(array $inputs)
    {
        $query = $this->order->where('user_id', Auth::id());

        if (isset($inputs['status'])) {
            $query->where('status', $inputs['status']);
        }
        $query->orderBy('created_at', 'desc');

        return $this->paginationAttribute($query, $inputs);
    }

    public function myOrder(int $id)
    {
        return $this->order->where('user_id', Auth::id())
            ->findOrFail($id);
    }
    /**
     * Create a new order.
     *
     * @param array $input
     * @return Order
     */
    public function createOrder(array $input)
    {
        // Start a transaction
        DB::beginTransaction();

        try {

            foreach ($input['order_products'] as $orderProduct) {
                $productVariant = ProductVariant::findOrFail($orderProduct['product_variant_id']);

                if ($productVariant->product_id !== $orderProduct['product_id']) {
                    throw new CustomException('The selected product variant does not belong to the specified product.', 422);
                }
            }

            // Create the order
            $user = Auth::user();
            $order = Order::create([
                'user_id' => $user->id,
                'total_weight' => $input['total_weight'],  // Assuming you calculate the total weight before
                'status' => $input['status'] ?? 'received',
                'address' => $input['address'] ?? $user->address ?? null,
            ]);

            // Loop through order items and add them to the order
            foreach ($input['order_products'] as $orderProduct) {
                $variant = ProductVariant::findOrFail($orderProduct['product_variant_id']);  // Ensure the variant exists

                $product = $variant->product;
                $order->orderProducts()->create([
                    'product_id' => $orderProduct['product_id'],
                    'product_name' => $product->name,
                    'product_description' => $product->description,
                    'product_variant_id' => $orderProduct['product_variant_id'],
                    'variant_weight' => $variant->weight,
                    'variant_unit' => $variant->unit,
                    'weight' => $variant->weight,
                    'unit' => $variant->unit,
                    'quantity' => $orderProduct['quantity'],
                    'total_weight' => $orderProduct['quantity'] * $variant->weight,  // Calculate total weight
                ]);
            }

            // Commit transaction
            DB::commit();

            return $order;
        } catch (\Exception $e) {
            // Rollback transaction in case of error
            DB::rollBack();
            throw $e;
        }
    }
}
