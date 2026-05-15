<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فاتورة الطلب #{{ $order->id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            padding: 20px;
            color: #333;
            direction: rtl;
        }

        .invoice-container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            box-shadow: 0 0 30px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            overflow: hidden;
        }

        /* Header */
        .invoice-header {
            background: linear-gradient(135deg, #1a1a2e 0%, #2d2d44 100%);
            color: white;
            padding: 40px;
            position: relative;
            overflow: hidden;
        }

        .invoice-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 300px;
            height: 300px;
            background: rgba(212, 175, 55, 0.1);
            border-radius: 50%;
        }

        .header-content {
            position: relative;
            z-index: 1;
        }

        .company-info {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 30px;
        }

        .company-logo {
            font-size: 32px;
            font-weight: 900;
            color: #d4af37;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .company-details {
            text-align: left;
            color: rgba(255, 255, 255, 0.9);
        }

        .company-details p {
            margin: 5px 0;
            font-size: 14px;
        }

        .invoice-title {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 2px solid rgba(212, 175, 55, 0.3);
            padding-top: 20px;
        }

        .invoice-title h1 {
            font-size: 28px;
            font-weight: 700;
        }

        .order-number {
            background: #d4af37;
            color: #1a1a2e;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 18px;
            font-weight: 700;
        }

        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 8px 20px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            margin-top: 15px;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-processing {
            background: #d1ecf1;
            color: #0c5460;
        }

        .status-completed {
            background: #d4edda;
            color: #155724;
        }

        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
        }

        /* Info Section */
        .invoice-info {
            padding: 40px;
            background: #fafafa;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 30px;
        }

        .info-box {
            background: white;
            padding: 25px;
            border-radius: 10px;
            border-right: 4px solid #d4af37;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .info-box h3 {
            font-size: 16px;
            color: #666;
            margin-bottom: 15px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .info-box p {
            margin: 10px 0;
            font-size: 15px;
            color: #333;
        }

        .info-box strong {
            color: #1a1a2e;
            font-weight: 600;
            display: inline-block;
            min-width: 120px;
        }

        /* Products Table */
        .products-section {
            padding: 40px;
        }

        .section-title {
            font-size: 22px;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 3px solid #d4af37;
        }

        .products-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .products-table thead {
            background: linear-gradient(135deg, #1a1a2e 0%, #2d2d44 100%);
            color: white;
        }

        .products-table th {
            padding: 18px 15px;
            text-align: right;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .products-table tbody tr {
            border-bottom: 1px solid #eee;
            transition: background 0.3s ease;
        }

        .products-table tbody tr:hover {
            background: #f9f9f9;
        }

        .products-table tbody tr:last-child {
            border-bottom: none;
        }

        .products-table td {
            padding: 20px 15px;
            text-align: right;
        }

        .product-name {
            font-weight: 600;
            color: #1a1a2e;
            font-size: 15px;
        }

        .product-details {
            font-size: 13px;
            color: #666;
            margin-top: 5px;
        }

        .product-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 5px;
            font-size: 12px;
            margin-left: 5px;
            font-weight: 600;
        }

        .badge-size {
            background: #e3f2fd;
            color: #1976d2;
        }

        .badge-price {
            background: #f3e5f5;
            color: #7b1fa2;
        }

        .badge-qty {
            background: #fff3e0;
            color: #e65100;
        }

        /* Summary */
        .summary-section {
            padding: 0 40px 40px 40px;
        }

        .summary-box {
            background: linear-gradient(135deg, #fafafa 0%, #f0f0f0 100%);
            border-radius: 10px;
            padding: 30px;
            margin-top: 20px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #ddd;
        }

        .summary-row:last-child {
            border-bottom: none;
        }

        .summary-label {
            font-size: 16px;
            color: #666;
            font-weight: 500;
        }

        .summary-value {
            font-size: 18px;
            font-weight: 700;
            color: #1a1a2e;
        }

        .summary-total {
            background: linear-gradient(135deg, #1a1a2e 0%, #2d2d44 100%);
            margin: 20px -30px -30px -30px;
            padding: 25px 30px;
            border-radius: 0 0 10px 10px;
        }

        .summary-total .summary-label,
        .summary-total .summary-value {
            color: white;
            font-size: 22px;
        }

        .summary-total .summary-value {
            color: #d4af37;
        }

        /* Footer */
        .invoice-footer {
            background: #1a1a2e;
            color: white;
            padding: 30px 40px;
            text-align: center;
        }

        .footer-content {
            max-width: 600px;
            margin: 0 auto;
        }

        .footer-content p {
            margin: 10px 0;
            font-size: 14px;
            color: rgba(255, 255, 255, 0.8);
        }

        .footer-divider {
            height: 2px;
            background: linear-gradient(to left, transparent, #d4af37, transparent);
            margin: 20px 0;
        }

        /* Print Styles */
        @media print {
            body {
                background: white;
                padding: 0;
            }

            .invoice-container {
                box-shadow: none;
                border-radius: 0;
            }

            .print-button {
                display: none;
            }
        }

        /* Print Button */
        .print-button {
            position: fixed;
            bottom: 30px;
            left: 30px;
            background: #d4af37;
            color: #1a1a2e;
            border: none;
            padding: 15px 30px;
            border-radius: 50px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(212, 175, 55, 0.4);
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .print-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(212, 175, 55, 0.6);
        }

        .print-button i {
            margin-left: 8px;
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Header -->
        <div class="invoice-header">
            <div class="header-content">
                <div class="company-info">
                    <div class="company-logo">
                        White Style
                    </div>
                    <div class="company-details">
                        <p><strong>📍</strong> فلسطين - رام الله</p>
                        <p><strong>📞</strong> +970 599 999 999</p>
                        <p><strong>✉️</strong> info@whitestyle.ps</p>
                    </div>
                </div>

                <div class="invoice-title">
                    <div>
                        <h1>فاتورة الطلب</h1>
                        @if($order->status === 'pending')
                            <span class="status-badge status-pending">قيد الانتظار</span>
                        @elseif($order->status === 'processing')
                            <span class="status-badge status-processing">قيد المعالجة</span>
                        @elseif($order->status === 'completed')
                            <span class="status-badge status-completed">مكتمل</span>
                        @else
                            <span class="status-badge status-cancelled">ملغي</span>
                        @endif
                    </div>
                    <div class="order-number">
                        #{{ $order->id }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Information -->
        <div class="invoice-info">
            <div class="info-grid">
                <div class="info-box">
                    <h3>معلومات العميل</h3>
                    <p><strong>الاسم:</strong> {{ $order->customer_name }}</p>
                    <p><strong>الهاتف:</strong> {{ $order->customer_phone }}</p>
                    <p><strong>المدينة:</strong> {{ $order->city->name ?? 'غير محدد' }}</p>
                    <p><strong>العنوان:</strong> {{ $order->address }}</p>
                </div>

                <div class="info-box">
                    <h3>تفاصيل الطلب</h3>
                    <p><strong>تاريخ الطلب:</strong> {{ $order->created_at->format('Y-m-d H:i') }}</p>
                    <p><strong>عدد المنتجات:</strong> {{ count($order->items) }} منتج</p>
                    <p><strong>رسوم التوصيل:</strong> {{ number_format($order->delivery_price, 2) }} ₪</p>
                </div>
            </div>

            @if($order->description)
            <div class="info-box" style="margin-top: 30px; grid-column: 1 / -1;">
                <h3>ملاحظات إضافية</h3>
                <p>{{ $order->description }}</p>
            </div>
            @endif
        </div>

        <!-- Products -->
        <div class="products-section">
            <h2 class="section-title">المنتجات المطلوبة</h2>

            <table class="products-table">
                <thead>
                    <tr>
                        <th style="width: 60px;">#</th>
                        <th>المنتج</th>
                        <th style="width: 120px; text-align: center;">الكمية</th>
                        <th style="width: 120px; text-align: center;">السعر</th>
                        <th style="width: 150px; text-align: center;">الإجمالي</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $index => $item)
                    @php
                        // تحويل إلى array للتأكد من التوافق
                        $itemArray = is_array($item) ? $item : (array) $item;

                        // الحصول على القيم بالأسماء الصحيحة
                        $productName = $itemArray['product_name'] ?? $itemArray['title'] ?? 'منتج';
                        $productPrice = $itemArray['price'] ?? 0;
                        $productQuantity = $itemArray['quantity'] ?? 1;
                        $productSize = $itemArray['size'] ?? null;
                    @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <div class="product-name">{{ $productName }}</div>
                            <div class="product-details">
                                @if($productSize)
                                    <span class="product-badge badge-size">المقاس: {{ $productSize }}</span>
                                @endif
                                <span class="product-badge badge-price">{{ number_format($productPrice, 2) }} ₪</span>
                                <span class="product-badge badge-qty">× {{ $productQuantity }}</span>
                            </div>
                        </td>
                        <td style="text-align: center; font-weight: 600;">{{ $productQuantity }}</td>
                        <td style="text-align: center; font-weight: 600;">{{ number_format($productPrice, 2) }} ₪</td>
                        <td style="text-align: center; font-weight: 700; color: #1a1a2e;">
                            {{ number_format($productPrice * $productQuantity, 2) }} ₪
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Summary -->
        <div class="summary-section">
            <div class="summary-box">
                <div class="summary-row">
                    <span class="summary-label">المجموع الفرعي:</span>
                    <span class="summary-value">{{ number_format($order->subtotal, 2) }} ₪</span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">رسوم التوصيل:</span>
                    <span class="summary-value">{{ number_format($order->delivery_price, 2) }} ₪</span>
                </div>
                <div class="summary-total">
                    <div class="summary-row">
                        <span class="summary-label">الإجمالي النهائي:</span>
                        <span class="summary-value">{{ number_format($order->total, 2) }} ₪</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="invoice-footer">
            <div class="footer-content">
                <p style="font-size: 16px; font-weight: 600; margin-bottom: 15px;">شكراً لتعاملكم معنا!</p>
                <div class="footer-divider"></div>
                <p>White Style - متجر الملابس العصرية</p>
                <p>جميع الحقوق محفوظة © {{ date('Y') }}</p>
            </div>
        </div>
    </div>

    <!-- Print Button -->
    <button class="print-button" onclick="window.print()">
        🖨️ طباعة الفاتورة
    </button>
</body>
</html>
