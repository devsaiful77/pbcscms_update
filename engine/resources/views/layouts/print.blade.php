<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print</title>
    <link rel="stylesheet" href="{{ URL::asset('public/css/bootstrap.min.css') }}">
    <style media="print">
        @page {
            size: auto;
            margin-left: 0;
            margin-right: 0;
        }
    </style>
    <style>
        body {
            font-family: "Times New Roman", Times, serif;
        }
        tr {
            font-size: 12px;
        }
        .table-condensed>tbody>tr>td,
        .table-condensed>tbody>tr>th,
        .table-condensed>tfoot>tr>td,
        .table-condensed>tfoot>tr>th,
        .table-condensed>thead>tr>td,
        .table-condensed>thead>tr>th {
            padding: 3px;
        }
        .h4, .h5, .h6, h4, h5, h6 {
            margin-top: 5px;
            margin-bottom: 5px;
        }
        .graybg {
            background-color: gainsboro;
        }
        .table {
            width: 100%;
            max-width: 100%;
            margin-bottom: 4px;
        }
        .main-header-wrap, .navbar, .fotter-pat {
            display: none !important;
        }
        .row-for-footer {
            display: flex;
            flex-wrap: nowrap;
            justify-content: space-around;
        }
        .row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: nowrap;
            margin: 0;
        }
        .col-md-6 {
            flex: 1;
            padding: 0 15px;
        }
        .text-right {
            text-align: right;
        }
        .border-top-color {
            border-top: 1px solid #000;
        }
        .border-all {
            border: 1px solid #000;
            padding: 1rem;
        }
        table td, table th {
            font-size: 0.9rem;
        }
        @media print {
            .row {
                flex-wrap: nowrap;
            }
            .col-md-6 {
                width: 50%;
            }
            .img-fluid {
                max-width: 100%;
                height: auto;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        @yield('content')
    </div>
    <script type="text/javascript">
        window.print();
    </script>
</body>
</html>