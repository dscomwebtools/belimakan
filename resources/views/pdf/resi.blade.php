<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Label Pengiriman - DSComputer</title>
    <style>
        @page {
            margin: 0;
        }

        body {
            margin: 20px;
            padding: 0;
            text-align: center;
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        .label-container {
            display: inline-block;
            text-align: left;
            margin: 0px auto;
            border: 1px solid #000;
            border-collapse: collapse;
            width: 100%;
            max-width: 600px;
        }

        /* HEADER */
        .header {
            text-align: center;
            padding: 8px 10px;
            border-bottom: 1px solid #000;
        }

        .header table {
            border: none;
            margin-bottom: 0;
        }

        .header img {
            display: block;
        }

        .brand {
            font-size: 20px;
            font-weight: bold;
            margin: 0;
        }

        .subtext {
            font-size: 12px;
            color: #555;
        }

        /* BARCODE AREA */
        .barcode {
            text-align: center;
            padding: 10px 0 4px;
        }

        .barcode img {
            width: 90%;
            height: 55px;
        }

        .resi {
            text-align: center;
            font-weight: bold;
            border-bottom: 1px solid #000;
            padding: 6px 0;
        }

        /* SERVICE INFO */
        .service-info {
            padding: 6px 16px 6px 16px;
            border-bottom: 1px solid #000;
            font-size: 16px;
            text-align: center;
        }

        .service-info div {
            margin: 2px 0;
        }

        /* ADDRESS */
        .address-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 1px solid #000;
        }

        .address-table td {
            vertical-align: top;
            border-right: 1px solid #000;
            padding: 6px 10px;
            width: 50%;
            position: relative;
        }

        .address-table td:last-child {
            border-right: none;
        }

        .address-header {
            font-weight: bold;
            font-size: 13px;
            margin-bottom: 2px;
        }

        .address-phone {
            position: absolute;
            top: 6px;
            right: 8px;
            font-size: 11px;
            color: #333;
        }

        .address-table p {
            margin: 2px 0;
            line-height: 1.3;
        }

        /* CATATAN */
        .note-section {
            border-top: 1px solid #000;
            padding: 6px 10px;
        }

        .note-section strong {
            display: inline-block;
            width: 70px;
        }

        /* DETAIL PESANAN */
        .order-summary {
            display: table;
            width: 100%;
            border-bottom: 1px solid #000;
        }

        .order-summary .left {
            width: 70%;
        }

        .order-summary .right {
            width: 30%;
        }

        .order-summary .left,
        .order-summary .right {
            padding: 6px 10px;
            display: table-cell;
            vertical-align: top;
        }

        .order-summary .left {
            text-align: center;
            border-right: 1px solid #000;
            padding-right: 10px;
        }

        .order-summary .right {
            padding-left: 10px;
            vertical-align: middle;
        }

        .order-summary img {
            max-width: 90%;
            height: 70px;
            display: block;
            margin: 0 auto 4px;
        }

        .order-summary .label {
            font-weight: bold;
            font-size: 12px;
            margin-top: 4px;
        }

        .order-summary .right div {
            margin: 2px 0;
        }

        /* ITEM LIST */
        .items {
            width: 100%;
            border-collapse: collapse;
            border: none;
            padding: 6px 10px;
        }

        .items th,
        .items td {
            padding: 4px 0 4px 0;
            text-align: left;
            border: none;
        }

        .items th {
            font-weight: bold;
            background: none;
        }

        .items td {
            background: none;
        }

        /* FOOTER */
        .footer {
            text-align: center;
            font-size: 11px;
            padding: 6px;
            border-top: 1px solid #000;
        }
    </style>
</head>

<body>
    <div class="label-container">

        <!-- HEADER -->
        <div class="header">
            <table style="width:100%; border:none; vertical-align:middle;">
                <tr>
                    <!-- Kolom kiri: Logo + teks DSComputer -->
                    <td style="text-align:left; width:70%; vertical-align:middle;">
                        <table style="border:none; border-spacing:0; margin:0; padding:0;">
                            <tr>
                                <td style="vertical-align:middle; padding:0; margin:0;">
                                    <img src="{{ public_path('storage/logo.webp') }}" alt="Logo DSComputer"
                                        style="width:40px; margin-right:10px; padding:0; display:inline-block;">
                                </td>
                                <td style="vertical-align:middle; padding:0; margin:0;">
                                    <div class="brand"
                                        style="font-size:18px; font-weight:bold; margin:0; line-height:1;">DSComputer
                                    </div>
                                    <div class="subtext" style="font-size:12px; color:#555; margin:0; line-height:1;">
                                        dscomputer.id</div>
                                </td>
                            </tr>
                        </table>
                    </td>

                    <!-- Kolom kanan: Logo ekspedisi -->
                    <td style="text-align:right; width:30%; vertical-align:middle;">
                        @if (!empty($shippingLogo) && file_exists($shippingLogo))
                            <img src="{{ $shippingLogo }}" alt="Logo Ekspedisi"
                                style="max-width:100px; max-height:50px; object-fit:contain; display:inline-block;">
                        @endif
                    </td>
                </tr>
            </table>
        </div>

        <!-- BARCODE RESI -->
        <div class="barcode">
            <img src="data:image/png;base64,{{ $barcode }}" alt="barcode">
        </div>
        <div class="resi">
            Nomor Resi - {{ $order->receipt->receipt_number ?? 'Tidak ada resi' }}
        </div>

        <!-- ONGKIR DAN LAYANAN -->
        <div class="service-info">
            <div>Ongkos Kirim: <strong>Rp {{ number_format($order->shipping_method->price ?? 0, 0, ',', '.') }}</strong>
            </div>
        </div>
        <div class="service-info">
            <div>
                Jenis Layanan -
                @isset($order->shipping_method)
                    {{ $order->shipping_method->courier_name }} - {{ $order->shipping_method->courier_service_name }}
                @else
                    Manual - {{ $order->shipping_method_manual ?? '-' }}
                @endisset
            </div>
        </div>

        <!-- ALAMAT PENGIRIM & PENERIMA -->
        <table class="address-table">
            <tr>
                <td>
                    <div class="address-header">Alamat Penerima:</div>
                    <div class="address-phone">{{ formatPhoneUniversal($order->order_address->hp ?? '-') }}</div>
                    <p>{{ $order->order_address->recipient ?? '-' }}</p>
                    <p>{{ $order->order_address->address ?? '-' }}</p>
                    <p>{{ $order->order_address->area_name ?? '' }}</p>
                </td>
                <td>
                    <div class="address-header">Alamat Pengirim:</div>
                    <div class="address-phone">{{ formatPhoneUniversal($origin->hp ?? '-') }}</div>
                    <p>{{ $origin->address ?? '-' }}</p>
                    <p>{{ $origin->area_name ?? '' }}</p>
                </td>
            </tr>
        </table>

        <!-- ORDER SUMMARY -->
        <div class="order-summary">
            <div class="left">
                <div class="label">Purchase Number</div>
                <img src="data:image/png;base64,{{ $reference }}" alt="Order ID Barcode">
                <div>{{ $order->invoice }}</div>
            </div>
        </div>
        <div class="order-summary">
            <div class="right">
                <div><strong>Quantity:</strong> {{ $order->order_detail->sum('quantity') }}</div>
                <div><strong>Weight:</strong> {{ $totalWeightKg ?? 1 }} Kg</div>
            </div>
        </div>

        <!-- ITEM LIST -->
        <table class="items">
            <thead>
                <tr>
                    <th>Nama Produk</th>
                    <th>SKU</th>
                    <th>Qty</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->order_detail as $detail)
                    <tr>
                        <td>
                            {{ $detail->product->name }}
                            @if ($detail->product_variant && $detail->product_variant->product_detail->count())
                                <br>
                                ( @foreach ($detail->product_variant->product_detail as $i => $v)
                                    {{ $v->variant_value->variant->variant }} - {{ $v->variant_value->value }}
                                    @if ($i < $detail->product_variant->product_detail->count() - 1)
                                        ,
                                    @endif
                                @endforeach )
                            @endif
                        </td>
                        <td>{{ $detail->product->sku ?? '-' }}</td>
                        <td>{{ $detail->quantity }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- CATATAN -->
        <div class="note-section">
            Catatan: {{ $order->delivery_note ?? 'Tidak ada catatan' }}
        </div>

        <!-- FOOTER -->
        <div class="footer">
            Pengiriman melalui platform DSComputer<br>
            dscomputer.id
        </div>

    </div>
</body>

</html>
