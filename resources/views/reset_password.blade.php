@extends('layout.login_master')


@section('content')
    <div class="login-box">
        <div class="card login-card" style="padding-left: 0px !important;">
            <div class="card-header text-center">
                <img src="{{ asset('images/fg.png') }}" height="100px">
            </div>
            <div class="card-body">
                <p class="login-box-msg text-white">Reset Password </p>
                <form action="{{ route('do.reset.password') }}" method="post">
                    @csrf
                    <input type="hidden" name="user_id" value="{{ encrypt($user_id) }}">
                    @if ($errors->any())
                        <div id="toast-container" class="toast-top-right" data-bs-delay="2000" data-bs-autohide="false">
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
                    <div class="input-group mb-3">
                        <input type="password" class="form-control login-input" name="password" placeholder="Password">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" class="form-control login-input" name="password_confirmation"
                            placeholder="Confirm Password">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn signinbtn btn-block">Change password</button>
                        </div>
                        <!-- /.col -->
                    </div>
                </form>
            </div>
            <!-- /.login-card-body -->
        </div>
    </div>
@endsection
