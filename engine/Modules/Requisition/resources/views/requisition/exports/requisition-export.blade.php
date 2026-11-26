<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Requisition Export</title>
</head>
<body>
    <table border="1">
        <thead>
            <tr>
                <th>Requisition Number</th>
                <th>Branch Location</th>
                <th>User ID</th>
                <th>Date Time</th>
                <th>Status</th>
                <th>Type</th>
                <th>Current Position</th>
                <th>Last Work By</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($tblData) && is_array($tblData) && count($tblData) > 0)
                @foreach($tblData as $row)
                <tr>
                    <td>{{ isset($row['requisition_number']) ? htmlspecialchars($row['requisition_number'], ENT_QUOTES, 'UTF-8') : '' }}</td>
                    <td>{{ isset($row['subgroup_name']) ? htmlspecialchars($row['subgroup_name'], ENT_QUOTES, 'UTF-8') : '' }}</td>
                    <td>{{ isset($row['user_id']) ? htmlspecialchars($row['user_id'], ENT_QUOTES, 'UTF-8') : '' }}</td>
                    <td>{{ isset($row['date_UNXTIME']) ? htmlspecialchars(str_replace('&', 'and', $row['date_UNXTIME']), ENT_QUOTES, 'UTF-8') : '' }}</td>
                    <td>{{ isset($row['status']) ? htmlspecialchars($row['status'], ENT_QUOTES, 'UTF-8') : '' }}</td>
                    <td>{{ isset($row['type']) ? htmlspecialchars($row['type'], ENT_QUOTES, 'UTF-8') : '' }}</td>
                    <td>{{ isset($row['current_position']) ? htmlspecialchars($row['current_position'], ENT_QUOTES, 'UTF-8') : '' }}</td>
                    <td>{{ isset($row['last_work_by']) ? htmlspecialchars($row['last_work_by'], ENT_QUOTES, 'UTF-8') : '' }}</td>
                </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="8">No data available</td>
                </tr>
            @endif
        </tbody>
    </table>
</body>
</html>