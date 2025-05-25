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

        return $query;
    }

    public function headings(): array
    {
        return [
            'Order ID',
            'Customer Name',
            'Location',
            'Product',
            'Weight',
            'Unit',
            'Quantity',
            'Total Weight',
            'Order Status',
            'Created At',
        ];
    }

    public function map($order): array
    {
        $rows = [];

        foreach ($order->orderProducts as $item) {
            $totalWeight = $item->weight * $item->quantity;

            $rows[] = [
                $order->id,
                $order->user->name,
                $order->address,
                $item->product->name,
                $item->weight,
                $item->unit,
                $item->quantity,
                $totalWeight . ' ' . $item->unit,
                $order->status,
                $order->created_at->format('Y-m-d H:i:s'),
            ];
        }

        return $rows;
    }
}
