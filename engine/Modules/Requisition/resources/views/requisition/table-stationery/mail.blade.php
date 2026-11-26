<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Requisition Email</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
        }
        .header, .footer {
            padding: 10px 0;
            text-align: center;
            background-color: #f5f5f5;
        }
        .container {
            padding: 20px;
        }
        .info-section {
            margin-bottom: 20px;
        }
        .info-section h3 {
            margin-bottom: 5px;
        }
        .info-table td {
            padding: 4px 8px;
        }
        table.items {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table.items th, table.items td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        table.items th {
            background-color: #f0f0f0;
            text-align: left;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Prime Bank Requisition</h2>
        <p>{{ \Carbon\Carbon::now()->format('F d, Y') }}</p>
    </div>

    <div class="container">
        <div class="info-table">
            <h3>Requisition Number : {{ $requisition_number }}</h3>
        </div>

        <div class="info-section">
            <h3>Vendor Information</h3>
            <table class="info-table">
                <tr>
                    <td><strong>Name:</strong></td>
                    <td>{{ $current_vendor['vendor']['name'] ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td><strong>Email:</strong></td>
                    <td>{{ $current_vendor['vendor']['email'] ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td><strong>Phone:</strong></td>
                    <td>{{ $current_vendor['vendor']['phone'] ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td><strong>Address:</strong></td>
                    <td>{{ $current_vendor['vendor']['office_address'] ?? 'N/A' }}</td>
                </tr>
            </table>
        </div>

        <div class="info-section">
            <h3>Bank Information</h3>
            <table class="info-table">
                <tr>
                    <td><strong>Bank Name:</strong></td>
                    <td>Prime Bank Ltd.</td>
                </tr>
                <tr>
                    <td><strong>Branch:</strong></td>
                    <td>{{ $bank_info['branch'] ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td><strong>Contact Person:</strong></td>
                    <td>{{ $bank_info['contact_person'] ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td><strong>Email:</strong></td>
                    <td>{{ $bank_info['contact_person_email'] ?? 'N/A' }}</td>
                </tr>
            </table>
        </div>

        <h3>Requisition Items</h3>
        <table class="items">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Item Description</th>
                    <th>Quantity</th>
                    <th>Unit</th>
                </tr>
            </thead>
            <tbody>
                @foreach($current_vendor['products'] as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item['product_name'] ?? 'N/A' }}</td>
                    <td>{{ $item['approve_qty'] ?? 0 }}</td>
                    <td>{{ $item['unit_name'] ?? 'N/A' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <p style="margin-top: 30px;">
            Please process the requisition at your earliest convenience. Feel free to reach out if you require additional details.
        </p>

        <p>Best regards,<br>
            <strong>{{ $bank_info['contact_person'] ?? 'N/A' }}</strong><br>
            Prime Bank Ltd.
        </p>
    </div>

    <div class="footer">
        <p>&copy; {{ now()->year }} Prime Bank Ltd. All rights reserved.</p>
    </div>
</body>
</html>
