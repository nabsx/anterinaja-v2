<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Receipt #{{ $order->id }}</title>
    <style>
        @media print {
            body {
                font-family: 'Courier New', monospace;
                font-size: 12px;
                width: 80mm;
                margin: 0;
                padding: 10px;
            }
            .no-print {
                display: none !important;
            }
            .receipt-header, .receipt-footer {
                text-align: center;
                margin-bottom: 10px;
            }
            .receipt-title {
                font-weight: bold;
                font-size: 14px;
                margin-bottom: 5px;
            }
            .receipt-info {
                margin: 5px 0;
            }
            .receipt-divider {
                border-top: 1px dashed #000;
                margin: 10px 0;
            }
            .text-right {
                text-align: right;
            }
            .text-center {
                text-align: center;
            }
            table {
                width: 100%;
                border-collapse: collapse;
            }
            table td {
                padding: 3px 0;
            }
            .bold {
                font-weight: bold;
            }
        }
        @media screen {
            body {
                font-family: Arial, sans-serif;
                max-width: 300px;
                margin: 0 auto;
                padding: 20px;
                border: 1px solid #ddd;
            }
            .print-button {
                background-color: #4CAF50;
                color: white;
                padding: 10px 15px;
                border: none;
                border-radius: 4px;
                cursor: pointer;
                margin-bottom: 20px;
            }
        }
    </style>
</head>
<body>
    <button onclick="window.print()" class="print-button no-print">Print Receipt</button>
    
    <div class="receipt-header">
        <div class="receipt-title">Anterinaja</div>
        <div>Jl. Semarang Indah no. 29</div>
        <div>Semarang, Indonesia</div>
        <div>Telp: 021-12345678</div>
    </div>
    
    <div class="receipt-divider"></div>
    
    <div class="receipt-info">
        <div><span class="bold">Order ID:</span> #{{ $order->id }}</div>
        <div><span class="bold">Date:</span> {{ $order->created_at->format('d/m/Y H:i') }}</div>
        <div><span class="bold">Customer:</span> {{ $order->customer->name }}</div>
        @if($order->driver)
        <div><span class="bold">Driver:</span> {{ $order->driver->user->name }}</div>
        @endif
    </div>
    
    <div class="receipt-divider"></div>
    
    <div>
        <div class="bold">Pickup:</div>
        <div>{{ $order->pickup_address }}</div>
    </div>
    
    <div style="margin-top: 10px;">
        <div class="bold">Destination:</div>
        <div>{{ $order->destination_address }}</div>
    </div>
    
    <div class="receipt-divider"></div>
    
    <table>
        <tr>
            <td>Vehicle Type:</td>
            <td class="text-right">{{ $order->vehicle_type_label }}</td>
        </tr>
        <tr>
            <td>Distance:</td>
            <td class="text-right">{{ number_format($order->distance_km, 2) }} km</td>
        </tr>
        <tr>
            <td>Duration:</td>
            <td class="text-right">{{ $order->duration_minutes }} minutes</td>
        </tr>
    </table>
    
    <div class="receipt-divider"></div>
    
    <table>
        <tr>
            <td>Base Fare:</td>
            <td class="text-right">Rp {{ number_format($order->fare_breakdown['base_fare'] ?? 0, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Distance Fare:</td>
            <td class="text-right">Rp {{ number_format($order->fare_breakdown['distance_fare'] ?? 0, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Time Fare:</td>
            <td class="text-right">Rp {{ number_format($order->fare_breakdown['time_fare'] ?? 0, 0, ',', '.') }}</td>
        </tr>
        @if(isset($order->fare_breakdown['waiting_fee']) && $order->fare_breakdown['waiting_fee'] > 0)
        <tr>
            <td>Waiting Fee:</td>
            <td class="text-right">Rp {{ number_format($order->fare_breakdown['waiting_fee'], 0, ',', '.') }}</td>
        </tr>
        @endif
        <tr class="bold">
            <td>Subtotal:</td>
            <td class="text-right">Rp {{ number_format($order->fare_amount, 0, ',', '.') }}</td>
        </tr>
    </table>
    
    <div class="receipt-divider"></div>
    
    <table>
        <tr>
            <td>Payment Method:</td>
            <td class="text-right">Cash</td>
        </tr>
        <tr>
            <td>Status:</td>
            <td class="text-right">{{ ucfirst($order->status) }}</td>
        </tr>
        <tr class="bold">
            <td>TOTAL:</td>
            <td class="text-right">Rp {{ number_format($order->fare_amount, 0, ',', '.') }}</td>
        </tr>
    </table>
    
    <div class="receipt-divider"></div>
    
    <div class="receipt-footer">
        <div>Thank you for using our service!</div>
        <div>www.anterinaja.com</div>
    </div>
    
    <script>
        // Auto-print when loaded (for direct print)
        window.onload = function() {
            if (window.location.search.includes('autoprint')) {
                window.print();
                setTimeout(function() {
                    window.close();
                }, 100);
            }
        };
    </script>
</body>
</html>