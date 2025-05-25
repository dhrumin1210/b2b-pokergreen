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
        return Order::query()
            ->with(['user', 'orderProducts.product', 'orderProducts.productVariant'])
            ->when($this->filters['start_date'] ?? null, fn($q, $v) => $q->whereDate('created_at', '>=', $v))
            ->when($this->filters['end_date'] ?? null, fn($q, $v) => $q->whereDate('created_at', '<=', $v))
            ->when($this->filters['status'] ?? null, fn($q, $v) => $q->where('status', $v));
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
        $item = $order->orderProducts->first(); // Simplified

        if (!$item) return array_fill(0, 10, '-');

        $totalWeight = $item->weight * $item->quantity;

        return [
            $order->id,
            $order->user->name ?? '-',
            $order->address ?? '-',
            $item->product->name ?? '-',
            $item->weight,
            $item->unit,
            $item->quantity,
            $totalWeight . ' ' . $item->unit,
            $order->status,
            $order->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
