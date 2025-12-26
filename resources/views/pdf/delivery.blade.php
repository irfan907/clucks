<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery {{ $delivery->identifier }}</title>
    <style>
        @page {
            margin: 20mm;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }
        .header .identifier {
            font-size: 18px;
            color: #666;
            margin-top: 5px;
        }
        .info-section {
            margin-bottom: 25px;
        }
        .info-row {
            display: flex;
            margin-bottom: 8px;
        }
        .info-label {
            font-weight: bold;
            width: 150px;
        }
        .info-value {
            flex: 1;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
        }
        .status-submitted {
            background-color: #fef3c7;
            color: #92400e;
        }
        .status-approved {
            background-color: #d1fae5;
            color: #065f46;
        }
        .status-delivered {
            background-color: #dbeafe;
            color: #1e40af;
        }
        .status-draft {
            background-color: #f3f4f6;
            color: #374151;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            margin-bottom: 30px;
        }
        thead {
            background-color: #f3f4f6;
        }
        th {
            text-align: left;
            padding: 10px;
            border-bottom: 2px solid #333;
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
        }
        td {
            padding: 8px 10px;
            border-bottom: 1px solid #ddd;
            font-size: 11px;
        }
        tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .notes-section {
            margin-top: 30px;
            padding: 15px;
            background-color: #f9fafb;
            border-left: 4px solid #333;
        }
        .notes-section h3 {
            margin: 0 0 10px 0;
            font-size: 14px;
            font-weight: bold;
        }
        .footer {
            margin-top: 40px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            font-size: 10px;
            color: #666;
            text-align: center;
        }
        .remaining-highlight {
            color: #dc2626;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Delivery Order</h1>
        <div class="identifier">ID: {{ $delivery->identifier }}</div>
    </div>

    <div class="info-section">
        <div class="info-row">
            <div class="info-label">Status:</div>
            <div class="info-value">
                @if($delivery->status === \App\DeliveryStatus::Submitted)
                    <span class="status-badge status-submitted">Submitted</span>
                @elseif($delivery->status === \App\DeliveryStatus::Approved)
                    <span class="status-badge status-approved">Approved</span>
                @elseif($delivery->status === \App\DeliveryStatus::Delivered)
                    <span class="status-badge status-delivered">Delivered</span>
                @else
                    <span class="status-badge status-draft">Draft</span>
                @endif
            </div>
        </div>
        <div class="info-row">
            <div class="info-label">Created By:</div>
            <div class="info-value">{{ $delivery->creator->name }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Created Date:</div>
            <div class="info-value">{{ $delivery->created_at->format('F d, Y H:i') }}</div>
        </div>
        @if($delivery->submitted_at)
        <div class="info-row">
            <div class="info-label">Submitted Date:</div>
            <div class="info-value">{{ $delivery->submitted_at->format('F d, Y H:i') }}</div>
        </div>
        @endif
        @if($delivery->approved_at)
        <div class="info-row">
            <div class="info-label">Approved Date:</div>
            <div class="info-value">{{ $delivery->approved_at->format('F d, Y H:i') }}</div>
        </div>
        @endif
        @if($delivery->finalized_at)
        <div class="info-row">
            <div class="info-label">Finalized Date:</div>
            <div class="info-value">{{ $delivery->finalized_at->format('F d, Y H:i') }}</div>
        </div>
        @endif
    </div>

    <h2 style="margin-top: 30px; margin-bottom: 15px; font-size: 16px; font-weight: bold;">Delivery Items</h2>

    @if($remainingQuantities->isEmpty())
        <p style="color: #666; font-style: italic;">No items in this delivery.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Product</th>
                    <th>Unit</th>
                    <th class="text-right">Ordered</th>
                    <th class="text-right">Received</th>
                    <th class="text-right">Remaining</th>
                    <th>Note</th>
                </tr>
            </thead>
            <tbody>
                @foreach($remainingQuantities as $index => $item)
                    @php
                        $deliveryItem = $item['delivery_item'];
                        $ordered = $item['ordered_quantity'];
                        $received = $item['total_received'];
                        $remaining = $item['remaining_quantity'];
                    @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $deliveryItem->product_name_snapshot }}</td>
                        <td>{{ $deliveryItem->unit_snapshot }}</td>
                        <td class="text-right">{{ number_format($ordered, 2) }}</td>
                        <td class="text-right">{{ number_format($received, 2) }}</td>
                        <td class="text-right {{ $remaining > 0 ? 'remaining-highlight' : '' }}">{{ number_format($remaining, 2) }}</td>
                        <td>{{ $deliveryItem->item_note ? \Illuminate\Support\Str::limit($deliveryItem->item_note, 50) : '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if($delivery->notes)
        <div class="notes-section">
            <h3>Notes</h3>
            <p style="margin: 0; white-space: pre-wrap;">{{ $delivery->notes }}</p>
        </div>
    @endif

    <div class="footer">
        <p>Generated on {{ now()->format('F d, Y H:i') }}</p>
    </div>
</body>
</html>

