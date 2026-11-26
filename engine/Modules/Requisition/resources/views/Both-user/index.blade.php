@extends('layouts.login_layout_update')
@section('content')
<style>
    .login-update-form-title {
        display: block;
        text-align: center;
        font-size: 24px;
        font-weight: bold;
        margin-bottom: 40px;
        color: #333;
    }

    .module-container {
        display: flex;
        justify-content: center;
        gap: 80px;
        margin-bottom: 100px;
    }

    .module-box {
        width: 220px;
        height: 160px;
        border: 2px solid #03427e;
        display: flex;
        justify-content: center;
        align-items: center;
        color: #03427e;
        font-size: 18px;
        font-weight: 500;
        border-radius: 10px;
        transition: transform 0.3s ease;
        cursor: pointer;
        text-align: center;
    }

    .module-box:hover {
        transform: scale(1.05);
        background-color: #e4f7dda3;
        color: #0d375c;
        font-weight: 600;
    }

    .footer-copy-center {
        text-align: center;
        color: #999;
        font-size: 14px;
        margin-top: 30px;
    }

    .footer-copy-center img {
        margin-left: 5px;
    }
    .float-end {
        float: right;
        margin-top: 5px;
    }
    .d-done {
        display: block !important;
    }
</style>

    <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
        {{ csrf_field() }}
    </form>
    <span class="login-update-form-title">Welcome to PrimeServe</span>

    <div class="module-container">
        <div><a href="{{ route('redirect.service') }}" class="module-box">Service & Complaint</a></div>
        <div><a href="{{ route('redirect.requisition') }}" class="module-box">Requisition</a></div>
    </div>

    <div class="footer-copyright-area footer-rap" id="footer">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

                    <div class="footer-copy-center" style="text-align:center; font-color:white">
                        <p><a href="http://www.fieldbooster.com/" target="_blank">Developed by : <img class="img-responsive" src="{{ URL::asset('public/img/logo/fbl.png') }}"  alt="Fieldbooster Limited." style="height: 18px; width: 18px;"> Fieldbooster Limited</a></p>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
