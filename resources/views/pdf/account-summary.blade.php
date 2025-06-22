<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ملخص الحسابات</title>
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

        h1, h3 {
            text-align: center;
            color: #1e1e2c;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: right;
        }

        th {
            background-color: #1e1e2c;
            color: #f29f67;
        }

        .totals {
            font-weight: bold;
            background-color: #f0f0f0;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <img src="{{ public_path('logo.png') }}" alt="Company Logo">
        </div>
        <h1>ملخص الحسابات</h1>
        <h3>للفترة {{ $date }}</h3>
        <table>
            <thead>
                <tr>
                    <th>الوصف</th>
                    <th>المبلغ (KES)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>إجمالي المبيعات</td>
                    <td>{{ number_format($total_sales, 2) }}</td>
                </tr>
                <tr>
                    <td>مدفوعات المبيعات المستلمة</td>
                    <td>{{ number_format($total_sales_payments, 2) }}</td>
                </tr>
                <tr>
                    @php
                        $outstanding_receivables = $total_sales - $total_sales_payments;
                    @endphp
                    <td>الذمم المدينة (المستحقات)</td>
                    <td style="color: {{ $outstanding_receivables > 0 ? 'red' : 'green' }};">{{ number_format($outstanding_receivables, 2) }}</td>
                </tr>
                <tr>
                    <td>إجمالي المشتريات</td>
                    <td>{{ number_format($total_purchases, 2) }}</td>
                </tr>
                <tr>
                    <td>مدفوعات المشتريات</td>
                    <td>{{ number_format($total_purchase_payments, 2) }}</td>
                </tr>
                <tr>
                    @php
                        $outstanding_payables = $total_purchases - $total_purchase_payments;
                    @endphp
                    <td>الذمم الدائنة (المستحقات)</td>
                    <td style="color: {{ $outstanding_payables > 0 ? 'red' : 'green' }};">{{ number_format($outstanding_payables, 2) }}</td>
                </tr>
                <tr>
                    @php
                        $net_cash_flow = $total_sales_payments - $total_purchase_payments;
                    @endphp
                    <td class="totals">صافي التدفق النقدي</td>
                    <td class="totals" style="color: {{ $net_cash_flow < 0 ? 'red' : 'green' }};">{{ number_format($net_cash_flow, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</body>

</html>
