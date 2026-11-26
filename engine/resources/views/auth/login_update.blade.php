@extends('layouts.login_layout_update')
@section('content')
    <form id="loginForm" action="{{ url('/login') }}" method="post">
        {{ csrf_field() }}

        <span class="login-update-form-title">Welcome to PrimeServe</span>

        <div class="wrap-input100 validate-input m-b-10">
            <input class="input100" type="text" name="username_or_email" placeholder="Username" value="{{ old('username_or_email') }}" autocomplete="off">
            <span class="focus-input100"></span>
            <span class="symbol-input100">
              <i class="fa fa-user"></i>
            </span>
        </div>
        @if ($errors->has('username_or_email'))
            <div class="error help-block text-center">
                <strong>{{ $errors->first('username_or_email') }}</strong><div class="clearfix">&nbsp;</div>
            </div>
        @endif

        <div class="wrap-input100 validate-input m-b-10">
            <input class="input100" type="password" name="password" placeholder="Password" autocomplete="off" value="" id="myInput">
            <span class="focus-input100"></span>
            <span class="symbol-input100">
                <i class="fa fa-lock"></i>
            </span>
        </div>
        <div class="pass-show" style="padding-left: 20px; margin-left: 10px; font-size: 10px;">
            <input type="checkbox" onclick="myFunction()"> <span class="">Show Password</span>
        </div>
        @if ($errors->has('password'))
            <div class="error help-block text-center">
                <strong>{{ $errors->first('password') }}</strong><div class="clearfix">&nbsp;</div>
            </div>
        @endif

        <div class="container-login100-form-btn p-t-10 p-b-100">
            <button type="submit" id="submitBtn" class="login100-form-btn">Sign In</button>
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
    </form>

    <!-- JSEncrypt লাইব্রেরি (Local Public Asset) -->
    <script src="{{ URL::asset('public/js/jsencrypt.min.js') }}"></script>

    <script>
        // শো/হাইড পাসওয়ার্ড ফাংশন
        function myFunction() {
            var x = document.getElementById("myInput");
            if (x.type === "password") {
                x.type = "text";
            } else {
                x.type = "password";
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            var form = document.getElementById('loginForm');
            var passwordField = document.getElementById('myInput');

            // পাবলিক কি (public_key.pem ফাইলের কনটেন্ট)
            var publicKey = `{!! file_exists(storage_path('app/keys/public_key.pem')) ? trim(file_get_contents(storage_path('app/keys/public_key.pem'))) : "-----BEGIN PUBLIC KEY-----\nMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAyB7W+LEjEaunuxNK9fZK\ncuYvY3TK0778x8rt0F89MT9YmQHeKrGGPlSXDdqkuPQGK3GZHfiqdhGhIbWEP7sC\nMqTCp0ZgfwqBIXMl0tGW45QXGZR/MQ852CvajAtLytPC9yH+nziwV6MLnhvzK4ru\n38JRZ93sDSRg/kVICv41B7U4nhSA6aLXYMA4C/u6g3MY7+x6hjUNQ6AzYgVe7aP6\na2+1ThOy2oVWE7p3+vkya/Lshy8hTFZ8+DTyf5AlPCxk8tLSlZ8cXHavV0TyRB1h\npMyq4+u4c7CG8mBEE/fWsCC3Zux9sJDyKd5Fs0MgeBwBN1F4J2U04lGPGyQ9WE6E\nlQIDAQAB\n-----END PUBLIC KEY-----" !!}`;

            form.addEventListener('submit', function (e) {
                var plainPassword = passwordField.value;

                if (plainPassword) {
                    if (typeof JSEncrypt === 'undefined') {
                        console.error('JSEncrypt library is not loaded.');
                        return;
                    }

                    try {
                        var encrypt = new JSEncrypt();
                        encrypt.setPublicKey(publicKey);

                        var encrypted = encrypt.encrypt(plainPassword);

                        if (encrypted) {
                            // পাসওয়ার্ড ইনপুটের ভ্যালু পরিবর্তন করে এনক্রিপ্টেড স্ট্রিং পাঠানো হচ্ছে
                            passwordField.value = encrypted;
                        } else {
                            e.preventDefault();
                            alert('Encryption failed. Please reload and try again.');
                        }
                    } catch (err) {
                        console.error('Encryption error:', err);
                        e.preventDefault();
                        alert('Encryption failed. Please reload and try again.');
                    }
                }
            });
        });
    </script>
@endsection
