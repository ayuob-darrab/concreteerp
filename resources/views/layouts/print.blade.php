<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'طباعة') - ConcreteERP</title>

    <style>
        @media print {
            @page {
                size: A4;
                margin: 1cm;
            }

            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .no-print {
                display: none !important;
            }
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            background: #fff;
            direction: rtl;
        }

        .print-container {
            max-width: 210mm;
            margin: 0 auto;
            padding: 20px;
        }

        /* Header */
        .print-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding-bottom: 20px;
            border-bottom: 2px solid #333;
            margin-bottom: 20px;
        }

        .print-logo img {
            height: 60px;
        }

        .print-company {
            text-align: center;
            flex: 1;
        }

        .print-company h1 {
            font-size: 20px;
            margin-bottom: 5px;
        }

        .print-company p {
            color: #666;
            font-size: 11px;
        }

        .print-info {
            text-align: left;
            font-size: 10px;
        }

        /* Title */
        .print-title {
            text-align: center;
            margin-bottom: 20px;
        }

        .print-title h2 {
            font-size: 18px;
            margin-bottom: 10px;
        }

        .print-title .date-range {
            color: #666;
            font-size: 12px;
        }

        /* Table */
        .print-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .print-table th,
        .print-table td {
            border: 1px solid #ddd;
            padding: 8px 10px;
            text-align: right;
        }

        .print-table th {
            background: #f5f5f5;
            font-weight: 600;
        }

        .print-table tbody tr:nth-child(even) {
            background: #fafafa;
        }

        /* Summary */
        .print-summary {
            display: flex;
            justify-content: space-around;
            padding: 15px;
            background: #f9f9f9;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .summary-item {
            text-align: center;
        }

        .summary-item .label {
            font-size: 10px;
            color: #666;
            margin-bottom: 3px;
        }

        .summary-item .value {
            font-size: 16px;
            font-weight: 700;
            color: #333;
        }

        /* Footer */
        .print-footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            display: flex;
            justify-content: space-between;
            font-size: 10px;
            color: #666;
        }

        /* Signatures */
        .signatures {
            display: flex;
            justify-content: space-around;
            margin-top: 40px;
            padding-top: 20px;
        }

        .signature-box {
            text-align: center;
            width: 150px;
        }

        .signature-line {
            border-top: 1px solid #333;
            margin-bottom: 5px;
            padding-top: 5px;
        }

        /* Print Button */
        .print-btn {
            position: fixed;
            top: 20px;
            left: 20px;
            padding: 10px 20px;
            background: #3498db;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
            z-index: 100;
        }

        .print-btn:hover {
            background: #2980b9;
        }
    </style>
</head>

<body>
    <button class="print-btn no-print" onclick="window.print()">
        🖨️ طباعة
    </button>

    <div class="print-container">
        @yield('content')
    </div>
</body>

</html>
