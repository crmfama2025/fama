@extends('layout.login_master')


@section('content')
    <div class="login-box ">
        <!-- /.login-logo -->
        <div class="card login-card">
            <div class="card-header text-center" style="padding-left: 0px !important;">
                <img src="{{ asset('images/fg.png') }}" height="100px">
            </div>
            <div class="card-body">
                <p class="login-box-msg text-white">Sign in to start your session</p>
                @if ($errors->any())
                    <div id="toast-container" class="toast-top-right" data-bs-delay="2000" data-bs-autohide="true">
                        <div class="toast toast-error" aria-live="polite" style="">
                            @foreach ($errors->all() as $error)
                                <div class="toast-message">{{ $error }}</div>
                            @endforeach
                        </div>
                    </div>
                @endif
                @if (session()->has('message'))
                    <div id="toast-container" class="toast-top-right" data-bs-delay="2000" data-bs-autohide="true">
                        <div class="toast {{ session()->get('status') == 'success' ? 'toast-success' : 'toast-error' }}"
                            aria-live="polite" style="">
                            <div class="toast-message">{{ session()->get('message') }}
                            </div>
                        </div>
                    </div>
                @endif

                <form action="{{ route('do.login') }}" method="post">
                    @csrf
                    <div class="input-group mb-3">
                        <input type="text" name="username" class="form-control login-input" placeholder="Email">
                        <div class="input-group-append">
                            <div class="input-group-text login-input-group">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" name="password" class="form-control login-input" placeholder="Password">
                        <div class="input-group-append">
                            <div class="input-group-text login-input-group">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn  font-weight-bolder signinbtn btn-block">Sign
                                In</button>
                        </div>

                    </div>
                </form>

                <p class="mb-1 mt-3">
                    <a href="{{ route('forgot.password') }}" class="textfama ">I forgot my password</a>
                </p>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>

    <!-- /.login-box -->
@endsection

@section('custom_js')
    <script>
        window.history.forward();
        window.onload = function() {
            window.history.forward();
        };
        window.onpageshow = function(evt) {
            if (evt.persisted) window.history.forward();
        };
    </script>
@endsection
