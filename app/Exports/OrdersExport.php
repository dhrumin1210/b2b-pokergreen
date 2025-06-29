<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class OrdersExport implements FromQuery, WithHeadings, WithMapping
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = Order::query()->with(['user', 'orderProducts.product', 'orderProducts.productVariant']);

        if (isset($this->filters['start_date'])) {
            $query->whereDate('created_at', '>=', $this->filters['start_date']);
        }

        if (isset($this->filters['end_date'])) {
            $query->whereDate('created_at', '<=', $this->filters['end_date']);
        }

        if (isset($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        if (isset($this->filters['user_id'])) {
            $query->where('user_id', $this->filters['user_id']);
        }

        if (isset($this->filters['product_id'])) {
            $query->whereHas('orderProducts', function ($q) {
                $q->where('product_id', $this->filters['product_id']);
            });
        }

        return $query;
    }

    public function headings(): array
    {
        $exportType = $this->getExportType();
        return match ($exportType) {
            'user_product' => [
                'Order ID',
                'Order Date',
                'Quantity',
                'Weight',
                'Unit',
                'Total Weight',
                'Order Status',
                'Location',
            ],
            'product' => [
                'User Name',
                'Order Date',
                'Quantity',
                'Weight',
                'Unit',
                'Total Weight',
                'Order Status',
                'Location',
            ],
            'user' => [
                'Order ID',
                'Order Date',
                'Product',
                'Weight',
                'Unit',
                'Quantity',
                'Total Weight',
                'Order Status',
                'Location',
            ],
            default => [
                'Order ID',
                'Customer Name',
                'Location',
                'Product',
                'Weight',
                'Unit',
                'Quantity',
                'Total Weight',
                'Order Status',
                'Date',
            ],
        };
    }

    public function map($order): array
    {
        $exportType = $this->getExportType();
        $rows = [];

        foreach ($order->orderProducts as $item) {
            // Skip if product filter is applied and this item doesn't match
            if (isset($this->filters['product_id']) && $item->product_id != $this->filters['product_id']) {
                continue;
            }

            $totalWeight = $item->weight * $item->quantity;

            $rows[] = match ($exportType) {
                'user_product' => [
                    $order->id,
                    $order->created_at->format('Y-m-d H:i:s'),
                    $item->quantity,
                    $item->weight,
                    $item->unit,
                    $totalWeight . ' ' . $item->unit,
                    $order->status,
                    $order->address,
                ],
                'product' => [
                    $order->user->name,
                    $order->created_at->format('Y-m-d H:i:s'),
                    $item->quantity,
                    $item->weight,
                    $item->unit,
                    $totalWeight . ' ' . $item->unit,
                    $order->status,
                    $order->address,
                ],
                'user' => [
                    $order->id,
                    $order->created_at->format('Y-m-d H:i:s'),
                    $item->product_name,
                    $item->weight,
                    $item->unit,
                    $item->quantity,
                    $totalWeight . ' ' . $item->unit,
                    $order->status,
                    $order->address,
                ],
                default => [
                    $order->id,
                    $order->user->name,
                    $order->address,
                    $item->product_name,
                    $item->weight,
                    $item->unit,
                    $item->quantity,
                    $totalWeight . ' ' . $item->unit,
                    $order->status,
                    $order->created_at->format('Y-m-d H:i:s'),
                ],
            };
        }


        return $rows;
    }

    private function getExportType(): string
    {
        if (isset($this->filters['user_id']) && isset($this->filters['product_id'])) {
            return 'user_product';
        }

        if (isset($this->filters['product_id'])) {
            return 'product';
        }

        if (isset($this->filters['user_id'])) {
            return 'user';
        }

        return 'general';
    }
}