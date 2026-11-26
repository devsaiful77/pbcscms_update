<!doctype html>
<html class="no-js" lang="">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Prime Bank PLC. # PrimeServe-Requisition | {{ (!empty($title)) ? $title : "" }}</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ URL::asset('public/img/favicon.ico') }}">
    {{-- Stylesheets --}}
    <link rel="stylesheet" href="{{ URL::asset('public/css/latest-v/bootstrap-5.3.1.min.css') }}"> {{-- bootstrap --}}
    <link rel="stylesheet" href="{{ URL::asset('public/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('public/css/meanmenu/meanmenu.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('public/css/animate.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('public/css/normalize.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('public/css/latest-v/dataTables-1.13.6.min.css') }}"> {{-- datatable --}}
    <link rel="stylesheet" href="{{ URL::asset('public/css/notika-custom-icon.css') }}">
    <link rel="stylesheet" href="{{ asset('public/css/icheck/all.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('public/css/main.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('public/css/style.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('public/css/responsive.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('public/vendors/jquery-ui/css/jquery-ui.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('public/css/latest-v/select2-4.0.3.min.css') }}"> {{-- select 2 --}}
    <link rel="stylesheet" href="{{ URL::asset('public/css/common.css') }}"><!-- Custom CSS -->
    <link rel="stylesheet" href="{{ URL::asset('public/vendors/summernote/summernote-bs4.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('public/css/latest-v/jquery-confirm-3.3.0.min.css') }}"> {{-- datatable --}}
    <link rel="stylesheet" href="{{ URL::asset('public/css/data-table/fixedHeader.dataTables.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('public/css/latest-v/flatpickr.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('public/css/magnific/magnific-popup.css') }}">
    @stack('stylesheets')
    {{-- JavaScript --}}
    <script src="{{ URL::asset('public/js/vendor/modernizr-2.8.3.min.js') }}"></script>
    <script src="{{ URL::asset('public/js/latest-v/jquery-3.7.1.min.js') }}"></script> {{-- jquery --}}
    <script src="{{ URL::asset('public/js/latest-v/highcharts-11.4.6.js') }}"></script>
    <script src="{{ URL::asset('public/js/latest-v/jquery-ui-1.14.0.min.js') }}"></script>
    <script src="{{ URL::asset('public/js/latest-v/flatpickr-4.6.13.min.js') }}"></script>
    <script src="{{ URL::asset('public/js/vendor/jquery-migrate-1.4.1.min.js') }}"></script>
    <script src="{{ URL::asset('public/js/latest-v/bootstrap-5.3.1.bundle.min.js') }}"></script> {{-- bootstrap --}}
    <script type="text/javascript">
        window.Laravel = <?php echo json_encode(['csrfToken' => csrf_token()]); ?>;
        var base_url = "{{URL::to('/')}}";
        var controllerName = "{{$controller}}";
        var actionName = "{{$action}}";
        var currentUrl = "{{$currentUrl}}";
        var _token = "{{csrf_token()}}";
        var loginUsing = 1;
    </script>
    <style>
        label {
            padding-top: 0.75rem;
        }

        @keyframes blinker {
            50% {
                opacity: 0;
            }
        }

        .backToSourceBG td {
            background-color: #d9f2f7 !important;
        }

        .backToSourceBG-blink {
            animation: blinker 1s linear infinite;
        }


        a#handlerExport {
            background: orange;
            border: 1px solid orange;
            color: white;
            transition: all 0.3s ease;
        }

        a#handlerExport:hover {
            background: darkorange;
            border-color: darkorange;
            color: white;
        }

        
    </style>
</head>

<body id="page-container">
    <!--[if lt IE 8]>
        <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
    <![endif]-->

    <!-- Header Top Area -->
    @include('requisition::includes.header')

    @include('requisition::includes.main_menu')
    {{--@include('requisition::includes.flash_message')--}}


    <div class="form-element-area">
        <div class="container-fluid">
            <div class="row">
                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                    @if (flash()->message)
                        {{-- <div class="alert alert-warning alert-dismissible fade show" role="alert"> --}}

                        <div class="mb-1 font-weight-bold text-center alert alert-{{ flash()->class }}" style="font-size: 14px;">
                            {!! flash()->message !!}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>

                            {{-- <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">&times;</span> </button> --}}
                        </div>
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                    <div class="form-element-list">
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Footer Area -->
    @include('requisition::includes.footer')
    <div class="overlay" style="display: none;">
        <i class="fa fa-spinner fa-spin fa-4x"></i>
        <p>Please wait...</p>
    </div>

    @stack('scripts')
    {{-- Additional JavaScript --}}
    <script src="{{ URL::asset('public/js/wow.min.js') }}"></script>
    <script src="{{ URL::asset('public/js/jquery.scrollUp.min.js') }}"></script>
    <script src="{{ URL::asset('public/js/meanmenu/jquery.meanmenu.js') }}"></script>
    <script src="{{ URL::asset('public/js/counterup/jquery.counterup.min.js') }}"></script>
    <script src="{{ URL::asset('public/js/counterup/waypoints.min.js') }}"></script>
    <script src="{{ URL::asset('public/js/counterup/counterup-active.js') }}"></script>
    <script src="{{ URL::asset('public/js/icheck/icheck.min.js') }}"></script>
    <script src="{{ URL::asset('public/js/icheck/icheck-active.js') }}"></script>
    <script src="{{ URL::asset('public/js/plugins.js') }}"></script>
    <script src="{{ URL::asset('public/js/main.js') }}"></script>
    <script src="{{ URL::asset('public/js/magnific/magnific-popup.js') }}"></script>
    <script src="{{ URL::asset('public/js/latest-v/jquery.dataTables-1.13.6.min.js') }}"></script>
    <script src="{{ URL::asset('public/js/data-table/dataTables.fixedHeader.js') }}"></script>
    <script src="{{ URL::asset('public/vendors/jquery-ui/js/jquery-ui.js') }}"></script>
    <script src="{{ URL::asset('public/js/latest-v/select2-4.0.3.min.js') }}"></script>
    <script src="{{ URL::asset('public/vendors/jquery-confirm/js/jquery-confirm.min.js') }}"></script>
    <script src="{{ URL::asset('public/js/common.js?'.time()) }}"></script>
    <script src="{{ URL::asset('public/vendors/summernote/summernote-bs4.min.js') }}"></script>
    <script>
        function printByClass(className) {
            var printContents = "";
            var elements = document.getElementsByClassName(className);

            Array.from(elements).forEach(function(element) {
                printContents += element.innerHTML + "<br><br>"; // spacing
            });

            var originalContents = document.body.innerHTML;

            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
            window.location.reload();
        }
    </script>
    <script>
        $(document).ready(function() {
            $(".btn-confirm").on("click", function () {
                var action = $(this).attr('data-action');
                $(".confirm").attr('href', action);
            });

            if ($.fn.DataTable.isDataTable('#data-table-basic')) {
                $('#data-table-basic').DataTable().destroy();
            }
            $('#data-table-basic').DataTable();

            if ($.fn.DataTable.isDataTable('.commonDataTableAllAsc')) {
                $('.commonDataTableAllAsc').DataTable().destroy();
            }

            $('.commonDataTableAllAsc').DataTable({
                ordering: false,
                fixedHeader: {
                    header: true
                }
            });

            if ($.fn.DataTable.isDataTable('.commonDataTableAll')) {
                $('.commonDataTableAll').DataTable().destroy();
            }
            $('.commonDataTableAll').DataTable({
                "order": [[ 0, "desc" ]],
                fixedHeader: {
                    header: true
                }
            });
        });
    </script>

    <div class="modal fade" id="confirm-delete" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Delete Confirmation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to request for delete?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <a class="btn btn-danger confirm btn-sm">Confirm</a>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="confirm-requested-delete" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Delete Confirmation</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <a class="btn btn-danger confirm btn-sm">Confirm</a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
