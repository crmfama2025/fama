@extends('admin.layout.admin_master')
@section('custom_css')
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('assets/fontawesome-free/css/all.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('css/adminlte.min.css') }}">
    <style>
        /* table {
                background: url('{{ asset('images/fama-letterhead.png') }}') center center;
                background-size: cover;
            } */
    </style>
@endsection
@section('content')
    <div class="content-wrapper">

        <section class="content">
            <div class="container-fluid ">
                <section class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-12">
                                <h1>Tenant Agreement</h1>
                            </div>

                        </div>
                    </div><!-- /.container-fluid -->
                </section>
                <div class="table-responsive">
                    @include('admin.projects.agreement.partials.agreement_content', [
                        'agreement' => $agreement,
                    ])
                </div>
                <div class="mt-5 text-center">
                    <a href="{{ route('agreement.index') }}" class="btn btn-secondary mb-4"><i
                            class="fas fa-arrow-left"></i>
                        Back</a>
                    <a href="{{ route('agreement.print', $agreement->id) }}" rel="noopener" target="_blank"
                        class="btn signinbtn mb-4">Print</a>
                </div>
            </div>
        </section>
    </div>
@endsection
@section('custom_js')
    <!-- jQuery -->
    <script src="{{ asset('assets/jquery/jquery.min.js') }}"></script>
    <!-- Bootstrap 4 -->
    <script src="{{ asset('assets/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- AdminLTE App -->
    {{-- <script src="{{ asset('js/adminlte.min.js') }}"></script> --}}
    <!-- AdminLTE for demo purposes -->
    <script src="{{ asset('/js/demo.js') }}"></script>

    <script src="{{ asset('assets/select2/js/select2.full.min.js') }}"></script>
@endsection
