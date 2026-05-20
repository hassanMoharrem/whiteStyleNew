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
            color: #bd2233;
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
            background: #bd2233;
            color: #e5e4e4;
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
            border-right: 4px solid #bd2233;
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
            border-bottom: 3px solid #bd2233;
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
            color: #bd2233;
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
            background: linear-gradient(to left, transparent, #bd2233, transparent);
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
              .cls-1 {
        fill: #e5e4e4;
      }

      .cls-2 {
        fill: #bd2233;
      }
      .logo-svg {
    width: 100%;
    height: auto;
    max-width: 260px;
    transition: transform var(--transition-fast);
}

        /* Print Button */
        .print-button {
            position: fixed;
            bottom: 30px;
            left: 30px;
            background: #bd2233;
            color: #e5e4e4;
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
                                <svg id="Layer_1" data-name="Layer 1" class="logo-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 866.5 217.43">
  <path class="cls-1" d="M134.56,0h20.9c-17.05,48.92-36.02,97.42-53.19,146.52l-14.62-.08c-5.65-20.78-12.82-42.03-19.57-62.51l-8.71,26.78-3.71,11.97c-3.55-9.87-6.8-18.47-10.84-28.17,4.55-12.27,9.79-29.19,13.43-41.88l19.66.08c4.75,17.55,11.82,39.94,17.89,56.93,3.99-10.11,8-24.11,11.73-34.85,8.85-24.99,17.86-49.92,27.02-74.79Z"/>
  <path class="cls-1" d="M357.65,52.62l67.78-.03c-.03,6.27.09,12.66.14,18.94-15.89.04-32.03-.27-47.93-.43.04,6.39,0,12.78-.11,19.17l41.18-.1.12,18.43-39.11-.28c-.62,0-1.05.23-1.69.45-1,2.8-.43,15.63-.35,19.13l48.45-.16-.03,18.49c-22.73-.29-46.11.02-68.9.04.57-13.97.18-28.91.15-42.91l.3-50.74Z"/>
  <path class="cls-1" d="M795.74,52.59l67.55.04-.08,18.82c-15.83-.03-31.67-.15-47.5-.36-.16,6.19.02,12.99.06,19.23,13.74.26,27.34-.42,41.25.06l.06,18.19c-13.49-.16-26.98-.18-40.47-.06l-.96.86c-.21,6.05-.11,12.24-.09,18.3,16.11.42,32.78.06,49,.19l-.07,18.34-68.8-.03.05-93.59Z"/>
  <path class="cls-1" d="M200.82,52.65l20.39-.05-.09,93.69c-4.76-.09-9.68-.04-14.46-.05-2.15.09-3.51.35-5.47-.49-.73-2.75-.37-32.14-.36-37.23-12.07.27-25.11-.02-37.25-.07.18,12.59-.19,25.1.29,37.72h-20.24c.01-23.72.39-48.75-.28-72.35,4.06-5.08,11.56-15.68,14.73-21.2,2.29-.07,2.87-.11,5.08.39.66,1.06.69,33.56.74,37.16,11.87-.29,25,0,36.92.23v-37.75Z"/>
  <path class="cls-1" d="M498.81,51.53c17.43-1.37,26.94,1.78,41.13,12.02-3.99,4.67-7.37,10-10.71,15.14-7.48-5.96-15.17-9.22-24.81-10.03-11.39-1.4-15.62,12.87-5.02,17.49,16.98,7.41,41.33,6.02,42.04,30.68.61,21.17-12.71,29.88-32.5,30.29-17.62-.48-25.66-5.32-39.77-14.76l12.54-15.25c.67.64,1.36,1.25,2.08,1.83,8.28,6.82,17.51,11.16,28.5,10.31,10.02-.45,11.65-12.86,2.89-16.85-13.49-6.15-38.66-6.57-42.07-25.18-3.49-19.03,7.3-32.29,25.72-35.71Z"/>
  <path class="cls-2" d="M548.42,152.37c12.42,1.53-6.9,20.69-3.93,29.47,6.26,5.27,43.11-5.18,53.64-6.01,77.63-6.18,154.75-7.58,232.56-8.06l-9.52-9.35c13.4,3.08,32.41,8.92,45.33,13.42-15.5,4.07-31.07,7.88-46.49,12.15,4.61-3.58,7.89-6.23,11.9-10.45-16.17-.63-35.39-.31-51.61-.2-54.23.58-108.43,2.73-162.53,6.43-17.51.93-33.46,3.51-50.51,7.2-7.04,1.12-16.9,3.86-23.9,1.96-8.92-2.42-3.82-15.97-2.24-20.96-21.05,18.37-38.58,30.19-65.48,39.88-16.88,5.36-39.74,13.67-57.23,7.3-7.58-3.26-9.38-11.8-4.89-18.13,15-21.15,39.27-31.8,63.22-38.23-4.4,3.5-9.55,4.49-15.54,8.05-14.1,8.37-39.21,20.05-44.26,37.2-1.54,5.24,7.83,8.28,13,8.08,30.91-1.2,62.34-14.68,87.53-32.15,11.44-7.95,21.14-17.93,30.94-27.57Z"/>
  <path class="cls-1" d="M691.75,52.65l23.91-.11c-7.71,12.1-15.25,24.3-22.64,36.6-3.87,6.75-8.25,13.76-12.27,20.47.62,11.06.25,25.3.25,36.6-6.68-.16-13.66.03-20.36.12.3-11.9.07-24.74.08-36.71-11.88-18.43-22.48-38.42-34.78-56.9l23.58-.12c6.2,10.54,16.13,26.33,21.16,36.96,6.85-10.81,14.4-25.45,21.07-36.9Z"/>
  <path class="cls-1" d="M722.17,52.7l21.12-.05c-.49,24.29.25,50.42.37,74.88,12.61-.53,27.34-.17,40.1-.16l-.16,18.86-60.74.07c.27-31.2.03-62.4-.69-93.6Z"/>
  <path class="cls-1" d="M0,52.65l22.47-.02c6.36,21.19,13.59,42.54,23,62.52,2.65,5.62,4.81,12.26,7.22,18.08-2.16,5.1-3.16,7.84-4.65,13.16l-9.68-.19-4.39.09c-4.32-10.14-8.82-24.7-12.51-35.4C14.66,91.34,7.51,71.93,0,52.65Z"/>
  <path class="cls-1" d="M240.64,52.66l20.27-.04.07,93.65c-6.63-.19-13.74-.08-20.41-.09l.07-93.52Z"/>
  <path class="cls-1" d="M299.42,75.77c6.02.75,13.37,2.79,20.04,3.74.17,22.25.18,44.5.05,66.76l-20.22-.03.13-70.47Z"/>
  <path class="cls-1" d="M574.69,75.66c6.85,1.44,13.29,2.83,20.23,3.81l-.34,66.6-20.06.09c.58-22.82.15-47.58.17-70.5Z"/>
  <path class="cls-1" d="M272.96,52.69c23.92-.32,48.37-.03,72.37-.13.13,6.28.34,12.69.1,18.96-10.23-.68-22.71-.37-33.07-.37-12.82,0-26.54-.17-39.29.44l-.1-18.91Z"/>
  <path class="cls-1" d="M548.06,52.61l72.39.02c.01,5.93.18,12.87-.23,18.69-24.01-.18-48.02-.16-72.03.04l-.13-18.75Z"/>
        </svg>
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
                        <span class="summary-value" style="color: #FFF !important">{{ number_format($order->total, 2) }} ₪</span>
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
