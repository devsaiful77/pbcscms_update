<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Requisition Email</title>
</head>
<body style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #333; margin: 0; padding: 0; background-color: #f4f6f8;">

    <!-- Header -->
    <div style="background-color: #004785; color: #ffffff; text-align: center; padding: 20px;">
        <h2 style="margin: 0;">Prime Bank Requisition</h2>
        <p style="margin: 5px 0 0;">{{ \Carbon\Carbon::now()->format('F d, Y') }}</p>
    </div>

    <!-- Body Content -->
    <div style="background-color: #ffffff; padding: 30px 20px;">
        <!-- Requisition Info -->
        <div style="margin-bottom: 25px; border: 1px solid #ddd; background-color: #e9edf1; padding: 12px 16px;">
            <h3 style="margin: 0 0 5px 0; color: #004785; font-size: 16px;">Requisition Number</h3>
            <p style="margin: 0; font-size: 16px; font-weight: bold;">{{ $requisition_number }}</p>
        </div>

        <div style="margin-bottom: 25px;">
            <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                <tr>
                    <!-- Vendor Info -->
                    <td style="width: 50%; vertical-align: top; border: 1px solid #ddd;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="background-color: #e9edf1;">
                                    <th colspan="2" style="padding: 8px; border: 1px solid #ddd; text-align: left; color: #004785;">Vendor Information</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="width: 120px; padding: 8px; border: 1px solid #ddd;"><strong>Name</strong></td>
                                    <td style="padding: 8px; border: 1px solid #ddd;">{{ $current_vendor['vendor']['name'] ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px; border: 1px solid #ddd;"><strong>Email</strong></td>
                                    <td style="padding: 8px; border: 1px solid #ddd;">{{ $current_vendor['vendor']['email'] ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px; border: 1px solid #ddd;"><strong>Phone</strong></td>
                                    <td style="padding: 8px; border: 1px solid #ddd;">{{ $current_vendor['vendor']['phone'] ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px; border: 1px solid #ddd;"><strong>Address</strong></td>
                                    <td style="padding: 8px; border: 1px solid #ddd;">{{ $current_vendor['vendor']['office_address'] ?? 'N/A' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </td>

                    <!-- Bank Info -->
                    <td style="width: 50%; vertical-align: top; border: 1px solid #ddd;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="background-color: #e9edf1;">
                                    <th colspan="2" style="padding: 8px; border: 1px solid #ddd; text-align: left; color: #004785;">Delivery Contact Person Info</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="width: 120px; padding: 8px; border: 1px solid #ddd;"><strong>Bank Name</strong></td>
                                    <td style="padding: 8px; border: 1px solid #ddd;">Prime Bank Ltd.</td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px; border: 1px solid #ddd;"><strong>Branch</strong></td>
                                    <td style="padding: 8px; border: 1px solid #ddd;">{{ $bank_info['branch'] ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px; border: 1px solid #ddd;"><strong>Branch Address</strong></td>
                                    <td style="padding: 8px; border: 1px solid #ddd;">{{ $bank_info['branch_address'] ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px; border: 1px solid #ddd;"><strong>Contact Person</strong></td>
                                    <td style="padding: 8px; border: 1px solid #ddd;">{{ $bank_info['contact_person'] ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px; border: 1px solid #ddd;"><strong>Email</strong></td>
                                    <td style="padding: 8px; border: 1px solid #ddd;">{{ $bank_info['contact_person_email'] ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px; border: 1px solid #ddd;"><strong>Phone</strong></td>
                                    <td style="padding: 8px; border: 1px solid #ddd;">{{ $bank_info['contact_person_phone'] ?? 'N/A' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Requisition Items -->
        <div style="margin-bottom: 25px;">
            <h3 style="margin-bottom: 10px; color: #004785;">Requisition Items</h3>
            <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                <thead>
                    <tr style="background-color: #e9edf1; text-align: center;">
                        <th style="padding: 8px; border: 1px solid #ddd;">#</th>
                        <th style="padding: 8px; border: 1px solid #ddd;">Item Description</th>
                        <th style="padding: 8px; border: 1px solid #ddd;">Quantity</th>
                        <th style="padding: 8px; border: 1px solid #ddd;">Unit</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($current_vendor['products'] as $index => $item)
                    <tr style="text-align: center;">
                        <td style="padding: 8px; border: 1px solid #ddd;">{{ $index + 1 }}</td>
                        <td style="padding: 8px; border: 1px solid #ddd;">{{ $item['product_name'] ?? 'N/A' }}</td>
                        <td style="padding: 8px; border: 1px solid #ddd;">{{ $item['approve_qty'] ?? 0 }}</td>
                        <td style="padding: 8px; border: 1px solid #ddd;">{{ $item['unit_name'] ?? 'N/A' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Comments -->
        <div style="margin-bottom: 25px;">
            {{-- <p style="font-size: 14px;"><strong>Branch User Remarks:</strong> {{ $branch_user_comment ?? 'N/A' }}</p> --}}
            <p style="font-size: 14px;"><strong>Admin Comment:</strong> {{ $admin_user_comment ?? 'N/A' }}</p>
        </div>

        <!-- Closing -->
        <div style="font-size: 14px;">
            <p>Please process the requisition at your earliest convenience. Feel free to reach out if you require additional details.</p>
            <p style="margin-top: 20px;">
                Best regards,<br>
                <strong>{{ $admin_user_name ?? 'N/A' }}</strong><br>
                Prime Bank PLC.
            </p>
        </div>
    </div>

    <!-- Footer -->
    <div style="background-color: #f1f1f1; color: #666; text-align: center; font-size: 12px; padding: 15px;">
        &copy; {{ now()->year }} Prime Bank PLC. All rights reserved.
    </div>
</body>
</html>
