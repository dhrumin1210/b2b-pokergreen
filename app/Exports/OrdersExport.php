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
            'Total Weight',
            'Status',
            'Created At',
            'Products'
        ];
    }

    public function map($order): array
    {
        return [
            $order->id,
            $order->user->name,
            $order->total_weight,
            $order->status,
            $order->created_at->format('Y-m-d H:i:s'),
            $order->orderProducts->map(function($item) {
                return $item->product->name . ' (' . $item->quantity . ' ' . $item->unit . ')';
            })->implode(', ')
        ];
    }
}