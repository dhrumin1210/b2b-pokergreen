
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Order Invoice</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        .invoice-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .invoice-details {
            margin-bottom: 30px;
        }
        .invoice-details table {
            width: 100%;
        }
        .invoice-items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .invoice-items th, .invoice-items td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .invoice-items th {
            background-color: #f8f9fa;
        }
        .invoice-total {
            text-align: right;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="invoice-header">
        <h1>Order Invoice</h1>
    </div>

    <div class="invoice-details">
        <table>
            <tr>
                <td>
                    <strong>Order ID:</strong> #{{ $order->id }}<br>
                    <strong>Date:</strong> {{ $order->created_at->format('Y-m-d') }}<br>
                    <strong>Status:</strong> {{ ucfirst($order->status) }}
                </td>
                <td style="text-align: right;">
                    <strong>Customer:</strong><br>
                    {{ $order->user->name }}<br>
                    {{ $order->user->email }}
                </td>
            </tr>
        </table>
    </div>

    <table class="invoice-items">
        <thead>
            <tr>
                <th>Product</th>
                <th>Weight</th>
                <th>Unit</th>
                <th>Quantity</th>
                <th>Total Weight</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->orderProducts as $item)
            <tr>
                <td>{{ $item->product->name }} ({{ $item->productVariant->name }})</td>
                <td>{{ $item->weight }}</td>
                <td>{{ $item->unit }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ $item->total_weight }} {{ $item->unit }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="invoice-total">
        <strong>Total Weight:</strong> {{ $order->total_weight }}
    </div>
</body>
</html>