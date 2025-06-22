<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فاتورة</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Cairo', sans-serif;
            margin: 0;
            padding: 0;
            background: #fff;
            color: #333;
            font-size: 12px;
        }

        .container {
            width: 95%;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
        }

        .header img {
            margin: 0 auto 15px;
            width: 100px;
            display: block;
        }

        .header p {
            margin: 5px 0;
            font-size: 14px;
        }
        
        .details-grid {
            display: flex;
            justify-content: space-between;
            width: 100%;
            margin-bottom: 30px;
        }

        .details-grid > div {
            width: 48%;
        }

        .details-grid h3 {
            margin: 0 0 10px;
            font-size: 16px;
            color: #555;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }

        .details-grid p {
            margin: 5px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        table th,
        table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: right;
        }

        table th {
            background: #f4f4f4;
            font-weight: bold;
        }

        .total {
            text-align: left;
            font-size: 16px;
            font-weight: bold;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
            color: #888;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <img src="{{ public_path('logo.png') }}" alt="Company Logo">
            <h1>فاتورة</h1>
            <p><strong>تاريخ الفاتورة:</strong> {{ Carbon\Carbon::parse($invoice->invoice_date)->format('Y-m-d') }}</p>
            <p><strong>رقم الفاتورة:</strong> #{{ sprintf('%04d', $invoice->id) }}</p>
        </div>

        <div class="details-grid">
            <div>
                <h3>بيانات البائع</h3>
                <p><strong>الاسم:</strong> {{ env('COMPANY_NAME') }}</p>
                <p><strong>العنوان:</strong> {{ env('COMPANY_ADDRESS') }}</p>
                <p><strong>البريد الإلكتروني:</strong> {{ env('COMPANY_EMAIL') }}</p>
                <p><strong>الهاتف:</strong> {{ env('COMPANY_PHONE') }}</p>
            </div>
            <div>
                <h3>بيانات العميل</h3>
                <p><strong>الاسم:</strong> {{ $invoice->client->name }}</p>
                <p><strong>العنوان:</strong> {{ $invoice->client->address }}</p>
                <p><strong>البريد الإلكتروني:</strong> {{ $invoice->client->email }}</p>
                <p><strong>الهاتف:</strong> {{ $invoice->client->phone_number }}</p>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>الوصف</th>
                    <th>الكمية</th>
                    <th>سعر الوحدة (KES)</th>
                    <th>الإجمالي (KES)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoice->products as $key => $product)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>
                            <strong>{{ $product->name }}</strong>
                            @if($product->description)
                            <p style="font-size:11px; color: #555;">{{ $product->description }}</p>
                            @endif
                        </td>
                        <td>{{ $product->pivot->quantity }} {{ $product->unit->name }}</td>
                        <td>{{ number_format($product->pivot->unit_price, 2) }}</td>
                        <td>{{ number_format($product->pivot->unit_price * $product->pivot->quantity, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <p class="total">المجموع الإجمالي: {{ number_format($invoice->total_amount, 2) }} KES</p>

        <div class="footer">
            <p>شكرا لتعاملكم معنا!</p>
            <p>تم إنشاء هذا المستند إلكترونيًا ولا يتطلب توقيعًا.</p>
        </div>
    </div>
</body>

</html>
