<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>PO #{{ $order->po_number }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header, .footer { width: 100%; }
        .header { margin-bottom: 15px; }
        .header-title { font-size: 22px; font-weight: bold; letter-spacing: 2px; }
        .company-info, .purchase-info { font-size: 12px; }
        .bold { font-weight: bold; }
        .section-title { background: #27a2ad; color: #fff; padding: 6px 8px; font-size: 14px; font-weight: bold; }
        .info-table td { padding: 2px 4px; }
        .box { border: 1px solid #aaa; padding: 10px 10px 0 10px; margin-bottom: 12px;}
        .mt-2 { margin-top: 12px; }
        .mb-0 { margin-bottom: 0; }
        .mb-1 { margin-bottom: 6px; }
        table.order-items { width: 100%; border-collapse: collapse; margin-top: 8px; }
        table.order-items th, table.order-items td { border: 1px solid #27a2ad; padding: 5px 4px; text-align: left; }
        table.order-items th { background: #27a2ad; color: #fff; }
        .text-right { text-align: right; }
        .summary-table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        .summary-table td { border: 1px solid #555; padding: 4px 8px; }
        .summary-table .label { background: #444; color: #fff; }
        .summary-table .value { background: #f5f5f5; }
        .balance { font-size: 17px; color: #27a2ad; font-weight: bold; }
        .comments, .terms { margin-top: 14px; }
        .comments-title, .terms-title { font-weight: bold; }
    </style>
</head>
<body>
    <!-- Header -->
    <table class="header">
        <tr>
            <td>
                <span class="header-title">CORMIER LIGHT COMPANY</span><br>
                <div class="company-info">
                    1909 Layman Avenue, North Carolina, 28306, USA<br>
                    Tel: (123) 0456-1234, Fax: (123) 0456-1230, Email: info@cormierlight.com<br>
                    Website: www.cormierlight.com, Tax Registration Number: 58-1111111
                </div>
            </td>
            <td style="text-align: right; vertical-align: top;">
                <div style="background: #444; color: #fff; padding: 8px 22px; font-size: 22px; font-weight: bold; border-radius: 4px;">PURCHASE<br>ORDER</div>
                <div class="purchase-info mt-2">
                    <table style="font-size: 12px;">
                        <tr>
                            <td class="bold">PO #:</td><td>{{ $order->po_number }}</td>
                        </tr>
                        <tr>
                            <td class="bold">Date:</td><td>{{ \Carbon\Carbon::parse($order->order_date)->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <td class="bold">Credit Terms:</td><td>{{ $order->credit_terms ?? 'Cash' }}</td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>
    <!-- Info Section -->
    <table width="100%" style="margin-top:12px; margin-bottom:10px;">
        <tr>
            <td width="50%">
                <div class="section-title">Purchase From</div>
                <table class="info-table">
                    <tr><td class="bold">Vendor Name</td><td>{{ $order->supplier }}</td></tr>
                    <tr><td>Street Address</td><td>{{ $order->supplier_address ?? '-' }}</td></tr>
                    <tr><td>City, State/Province, Zip/Post code</td><td>{{ $order->supplier_city ?? '-' }}</td></tr>
                    <tr><td>Country</td><td>{{ $order->supplier_country ?? '-' }}</td></tr>
                    <tr><td>Attn: Contact Person</td><td>{{ $order->supplier_contact ?? '-' }}</td></tr>
                </table>
            </td>
            <td width="50%">
                <div class="section-title">Deliver To</div>
                <table class="info-table">
                    <tr><td class="bold">Deliver-To-Name</td><td>{{ $order->deliver_to_name ?? '-' }}</td></tr>
                    <tr><td>Ship To Street Address</td><td>{{ $order->deliver_to_address ?? '-' }}</td></tr>
                    <tr><td>City, State/Province, Zip/Post code</td><td>{{ $order->deliver_to_city ?? '-' }}</td></tr>
                    <tr><td>Country</td><td>{{ $order->deliver_to_country ?? '-' }}</td></tr>
                    <tr><td>Attn: Contact Person</td><td>{{ $order->deliver_to_contact ?? '-' }}</td></tr>
                </table>
            </td>
        </tr>
    </table>
    <!-- Order Items Table -->
    <table class="order-items">
        <thead>
            <tr>
                <th>Order Code</th>
                <th>Item</th>
                <th>Qty</th>
                <th>Units</th>
                <th>Unit Price</th>
                <th>Total Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td>{{ $item->order_code ?? '-' }}</td>
                <td>{{ $item->item->name ?? '-' }}</td>
                <td class="text-right">{{ $item->quantity }}</td>
                <td>{{ $item->units ?? '-' }}</td>
                <td class="text-right">${{ number_format($item->unit_price, 2) }}</td>
                <td class="text-right">${{ number_format($item->total_price, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <!-- Summary & Comments -->
    <table width="100%" style="margin-top:18px;">
        <tr>
            <td width="60%" style="vertical-align: top;">
                <div class="comments">
                    <span class="comments-title">Comments</span><br>
                    {{ $order->comments ?? 'N/A' }}
                </div>
                <div class="terms mt-2">
                    <span class="terms-title">Terms & Conditions</span><br>
                    {{ $order->terms ?? 'N/A' }}
                </div>
            </td>
            <td width="40%">
                <table class="summary-table">
                    <tr>
                        <td class="label">Sub Total</td>
                        <td class="value text-right">${{ number_format($order->subtotal, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="label">Tax 10%</td>
                        <td class="value text-right">${{ number_format($order->tax, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="label">Freight</td>
                        <td class="value text-right">${{ number_format($order->freight, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="label">Paid</td>
                        <td class="value text-right">${{ number_format($order->paid, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="label balance">Balance</td>
                        <td class="value balance text-right">${{ number_format($order->balance, 2) }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
