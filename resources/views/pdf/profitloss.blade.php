<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بيان الأرباح والخسائر</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Cairo', sans-serif;
            color: #333;
            margin: 0;
            padding: 20px;
            font-size: 12px;
        }

        .container {
            padding: 20px;
            border: 2px solid #1e1e2c;
            border-radius: 8px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header img {
            width: 100px;
        }

        h1, p {
            text-align: center;
            color: #1e1e2c;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .table th,
        .table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: right;
        }

        .table th {
            background-color: #1e1e2c;
            color: #f29f67;
        }

        tfoot .totals {
            font-weight: bold;
            background-color: #f0f0f0;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <img src="{{ public_path('logo.png') }}" alt="Company Logo">
            <h1>بيان الأرباح والخسائر</h1>
            <p>عن الفترة المنتهية في: {{ $date }}</p>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>البيان</th>
                    <th>المبلغ (KES)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>بضاعة أول المدة</td>
                    <td>{{ number_format($opening_stock, 2) }}</td>
                </tr>
                <tr>
                    <td>المشتريات</td>
                    <td>{{ number_format($total_purchases, 2) }}</td>
                </tr>
                <tr>
                    <td>بضاعة آخر المدة</td>
                    <td>{{ number_format($closing_stock, 2) }}</td>
                </tr>
                <tr>
                    <td><strong>تكلفة البضاعة المباعة</strong></td>
                    <td><strong>{{ number_format($opening_stock + $total_purchases - $closing_stock, 2) }}</strong></td>
                </tr>
                <tr>
                    <td>إيرادات المبيعات</td>
                    <td>{{ number_format($total_revenue, 2) }}</td>
                </tr>
                <tr>
                    <td><strong>مجمل الربح</strong> (إيرادات المبيعات - تكلفة البضاعة المباعة)</td>
                    <td><strong>{{ number_format($total_revenue - ($opening_stock + $total_purchases - $closing_stock), 2) }}</strong>
                    </td>
                </tr>
            </tbody>
            <tfoot>
                @php
                    $net_profit = $total_revenue - ($opening_stock + $total_purchases - $closing_stock);
                @endphp
                <tr>
                    <td class="totals">صافي الربح</td>
                    <td class="totals" style="color: {{ $net_profit < 0 ? 'red' : 'green' }};">{{ number_format($net_profit, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</body>

</html>
