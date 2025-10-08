<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Invoice - DSComputer</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .container {
            padding: 20px 40px;
        }

        /* HEADER */
        .header-container {
            position: relative;
        }

        .logo {
            display: inline-block;
        }

        .logo img {
            width: 100px;
        }

        .invoice {
            position: fixed;
            top: 0;
            right: 40px;
            text-align: right;
        }

        .invoice .title {
            font-size: 18px;
            font-weight: bold;
        }

        .invoice .no {
            color: green;
        }

        .info-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 4px 0px;
            vertical-align: top;
            min-width: 100px;
        }

        .info-label {
            font-weight: bold;
            width: 100px;
            white-space: nowrap;
        }

        /* PRODUCT TABLE */
        .product-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .product-table th,
        .product-table td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
        }

        .product-table th {
            background-color: #eee;
            font-weight: bold;
        }

        .product-table td:first-child {
            text-align: left;
        }

        /* TOTALS */
        .totals {
            margin-top: 20px;
            width: 50%;
            margin-left: auto;
            border-collapse: collapse;
        }

        .totals td {
            padding: 6px;
        }

        .totals td:first-child {
            font-weight: bold;
        }

        .totals tr:last-child td {
            border-top: 2px solid #000;
            font-size: 14px;
        }

        /* FOOTER */
        .footer {
            margin-top: 40px;
            font-size: 11px;
            text-align: center;
            color: #555;
        }
    </style>
</head>

<body>
    <div class="container">

        <!-- HEADER -->
        <div class="header-container">
            <div class="logo">
                <img src="storage/logo.webp" alt="Logo">
            </div>
            <div class="invoice">
                <div class="title">Purchase Order</div>
                <div class="no">{{ $order->invoice }}</div>
            </div>
        </div>

        <!-- INFO -->
        <table class="info-table">
            <tr>
                <td class="info-label">Penjual</td>
                <td>DS Computer</td>
                <td class="info-label">Nama</td>
                <td>{{ $order->order_address->recipient }}</td>
            </tr>
            <tr>
                <td class="info-label">Kurir</td>
                <td>
                    @isset($order->shipping_method)
                        {{ $order->shipping_method->courier_name }} - {{ $order->shipping_method->courier_service_name }}
                    @else
                        Manual - {{ $order->shipping_method_manual }}
                    @endisset
                </td>
                <td class="info-label">Alamat</td>
                <td>
                    {{ $order->order_address->address }} <br>
                    {{ $order->order_address->area_name }}
                </td>
            </tr>
            <tr>
                <td class="info-label">Pembayaran</td>
                <td>{{ $order->payment_method }}</td>
                <td class="info-label">Telp / WA</td>
                <td>{{ $order->order_address->hp }}</td>
            </tr>
        </table>

        <!-- PRODUCT LIST -->
        <table class="product-table">
            <thead>
                <tr>
                    <th>Deskripsi Produk</th>
                    <th>Jumlah</th>
                    <th>Harga Satuan</th>
                    <th>Diskon</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalBayar = 0;
                    $totalQty = 0;
                @endphp
                @foreach ($order->order_detail as $orderDetail)
                    @php
                        $subtotal = $orderDetail->price * $orderDetail->quantity;
                        $discount = $orderDetail->discount ?? 0;
                        $discountAmount = $discount > 0 ? $subtotal * ($discount / 100) : 0;
                        $subtotalAfterDiscount = $subtotal - $discountAmount;

                        $totalBayar += $subtotalAfterDiscount;
                        $totalQty += $orderDetail->quantity;
                    @endphp
                    <tr>
                        <td>
                            {{ $orderDetail->product->name }}
                            @if ($orderDetail->product_variant && $orderDetail->product_variant->product_detail->count())
                                <br>
                                ( @foreach ($orderDetail->product_variant->product_detail as $index => $detail)
                                    {{ $detail->variant_value->variant->variant }} -
                                    {{ $detail->variant_value->value }}
                                    @if ($index < $orderDetail->product_variant->product_detail->count() - 1)
                                        ,
                                    @endif
                                @endforeach )
                            @endif
                        </td>
                        <td>{{ $orderDetail->quantity }}</td>
                        <td>{{ number_format($orderDetail->price, 0, ',', '.') }}</td>
                        <td>
                            @if ($discount > 0)
                                {{ number_format($discountAmount, 0, ',', '.') }} ({{ $discount }}%)
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ number_format($subtotalAfterDiscount, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- TOTALS -->
        @php
            $voucherAmount = 0;
            $voucherCode = null;

            if ($order && $order->coupon_discount > 0) {
                $voucherAmount = $order->coupon_discount;
                $voucherCode = $order->coupon_code;
            }

            $grandTotal = max($totalBayar - $voucherAmount, 0);
        @endphp

        <table class="totals">
            <tr>
                <td>Total Harga</td>
                <td style="text-align: right;">Rp {{ number_format($totalBayar, 0, ',', '.') }}</td>
            </tr>

            @if ($voucherAmount > 0)
                <tr>
                    <td>Voucher ({{ $voucherCode }})</td>
                    <td style="text-align: right; color: red;">- Rp {{ number_format($voucherAmount, 0, ',', '.') }}
                    </td>
                </tr>
            @endif

            <tr>
                <td><strong>Total Bayar</strong></td>
                <td style="text-align: right;"><strong>Rp {{ number_format($grandTotal, 0, ',', '.') }}</strong></td>
            </tr>
        </table>

        <div class="footer">
            <p>Terima kasih telah berbelanja di DS Computer.</p>
        </div>
    </div>
</body>

</html>
