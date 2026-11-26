<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Requisition Export</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #000;
            padding: 6px 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
    </style>
</head>
<body>

<table>
    <thead>
        <tr>
            <th>S/L</th>
            <th>Requisition Number</th>
            <th>Logger</th>
            <th>Product Name</th>
            <th>Requested Qty</th>
            <th>Current Stock</th>
        </tr>
    </thead>
    <tbody>
        @php $sl = 1; @endphp

        @foreach ($dataForView as $requisition)

            @foreach ($requisition->printings as $item)
                <tr>
                    <td>{{ $sl++ }}</td>
                    <td>{{ $requisition->requisition_number ?? 'N/A' }}</td>
                    <td>{{ $requisition->created_by ?? 'N/A' }}</td>
                    <td>{{ $item?->requItem?->product_name ?? 'N/A' }}</td>
                    <td>{{ $item->quantity ?? 0 }}</td>
                    <td>{{ $item?->requItem?->stock?->quantity ?? 0 }}</td>
                </tr>
            @endforeach

            @foreach ($requisition->tableStationeries as $item)
                <tr>
                    <td>{{ $sl++ }}</td>
                    <td>{{ $requisition->requisition_number ?? 'N/A' }}</td>
                    <td>{{ $requisition->created_by ?? 'N/A' }}</td>
                    <td>{{ $item?->requItem?->product_name ?? 'N/A' }}</td>
                    <td>{{ $item->quantity ?? 0 }}</td>
                    <td>{{ $item?->requItem?->stock?->quantity ?? 0 }}</td>
                </tr>
            @endforeach

            @foreach ($requisition->generalServices as $item)
                <tr>
                    <td>{{ $sl++ }}</td>
                    <td>{{ $requisition->requisition_number ?? 'N/A' }}</td>
                    <td>{{ $requisition->created_by ?? 'N/A' }}</td>
                    <td>{{ $item?->requItem?->product_name ?? 'N/A' }}</td>
                    <td>{{ $item->quantity ?? 0 }}</td>
                    <td>{{ $item?->requItem?->stock?->quantity ?? 0 }}</td>
                </tr>
            @endforeach

        @endforeach
    </tbody>
</table>

</body>
</html>
