@extends('admin.layout.admin_master')

@section('custom_css')
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('assets/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    <!-- iCheck for checkboxes and radio inputs -->
    <link rel="stylesheet" href="{{ asset('assets/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('assets/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/datatables-buttons/css/buttons.bootstrap4.min.css') }}">

    <link rel="stylesheet" href="{{ asset('assets/bs-stepper/css/bs-stepper.min.css') }}">
    {{-- <style>
        .table-responsive {
            overflow-x: auto;
        }

        table {
            max-width: 100%;
            table-layout: fixed;
        }

        .bs-stepper-header {
            flex-wrap: wrap;
        }

        .bs-stepper .step {
            flex: 1 1 auto;
        }

        .bs-stepper-content {
            overflow-x: hidden;
        }

        html,
        body {
            overflow-x: hidden;
        }

        .content-wrapper,
        .container-fluid,
        .card,
        .bs-stepper {
            max-width: 100%;
        }
    </style> --}}
@endsection

@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper user-management">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>{{ $title }}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ asset('dashboard.php') }}">Home</a></li>
                            <li class="breadcrumb-item active">{{ $title }}</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <!-- /.card-header -->
                            <div class="card-body">
                                <form action="javascript:void(0)" id="UserForm" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="id" id="user_id" value="{{ $user->id ?? '' }}">
                                    <input type="hidden" name="profile" value="1">
                                    <div class="form-group row">
                                        <div class="col-md-4">
                                            <label for="exampleInputEmail1" class="asterisk">First Name</label>
                                            <input type="text" class="form-control" id="first_name" name="first_name"
                                                placeholder="First Name" value="{{ $user->first_name ?? '' }}" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="exampleInputEmail1" class="asterisk">Last Name</label>
                                            <input type="text" class="form-control" id="last_name" name="last_name"
                                                placeholder="Last Name" value="{{ $user->last_name ?? '' }}" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="exampleInputEmail1" class="asterisk">Email</label>
                                            <input type="text" class="form-control" id="email" name="email"
                                                placeholder="Email" value="{{ $user->email ?? '' }}" required>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-md-4">
                                            <label for="exampleInputEmail1" class="asterisk">Phone</label>
                                            <input type="number" class="form-control" id="phone" name="phone"
                                                placeholder="Phone" value="{{ $user->phone ?? '' }}" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="exampleInputEmail1" class="asterisk">User Name</label>
                                            <input type="text" class="form-control" id="username" name="username"
                                                placeholder="User Name" value="{{ $user->username ?? '' }}" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="exampleInputEmail1"
                                                class="@if (!isset($user)) asterisk @endif">Password</label>
                                            <input type="text" class="form-control" id="password" name="password"
                                                placeholder="Password" @if (!isset($user)) required @endif>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        @if (auth()->user()->user_type_id)
                                            <input type="hidden" name="user_type_id" id="user_type_id"
                                                value="{{ auth()->user()->user_type_id }}">
                                        @else
                                            <div class="col-md-4">
                                                <label for="exampleInputEmail1" class="asterisk">User Type</label>
                                                <select class="form-control select2" name="user_type_id"
                                                    id="user_type_id" required>
                                                    <option value="">Select User Type</option>
                                                    @foreach ($user_types as $user_type)
                                                        <option value="{{ $user_type->id }}"
                                                            {{ isset($user) && $user->user_type_id == $user_type->id ? 'selected' : '' }}>
                                                            {{ $user_type->user_type }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        @endif
                                        @if (auth()->user()->company_id)
                                            <input type="hidden" name="company_id" id="company_id"
                                                value="{{ auth()->user()->company_id }}">
                                        @else
                                            <div class="col-md-4">
                                                <label class="asterisk">Company</label>
                                                <select class="form-control select2" name="company_id" id="company_id"
                                                    required>
                                                    <option value="">Select Company</option>
                                                    @foreach ($companies as $company)
                                                        <option value="{{ $company->id }}"
                                                            {{ isset($user) && $user->company_id == $company->id ? 'selected' : '' }}>
                                                            {{ $company->company_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        @endif

                                        <div class="col-md-4">
                                            <label class="@if (!isset($user)) asterisk @endif">Upload
                                                Profile Photo</label>
                                            <input type="file" name="profile_photo" id="profile_photo"
                                                class="form-control" @if (!isset($user)) required @endif>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-info mr-2">Submit</button>

                            </div>
                        </div>
                        </form>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
            <!-- /.col -->
    </div>
    <!-- /.row -->
    </div>
    <!-- /.container-fluid -->

    </section>
    <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
@endsection

@section('custom_js')
    <!-- Select2 -->
    <script src="{{ asset('assets/select2/js/select2.full.min.js') }}"></script>
    <!-- BS-Stepper -->
    <script src="{{ asset('assets/bs-stepper/js/bs-stepper.min.js') }}"></script>
    @include('admin.user.validation-js')

    <script>
        // BS-Stepper Init
        // document.addEventListener('DOMContentLoaded', function() {
        //     window.stepper = new Stepper(document.querySelector('.bs-stepper'))
        // })

        if ($('#user_id').val()) {
            $('#company_id').prop('disabled', true);
            $('#user_type_id').prop('disabled', true);
        }


        $('.masterParent').on('change', function() {
            const row = $(this).data('row');
            $('.masterChild' + row).prop('checked', this.checked);
        });

        $('#phone').on('blur', function() {
            phoneValidation(this, 'phone');
        });

        $('#UserForm').submit(function(e) {
            e.preventDefault();
            $('#company_id').prop('disabled', false);
            $('#user_type_id').prop('disabled', false);

            var form = document.getElementById('UserForm');
            var fdata = new FormData(form);

            $.ajax({
                type: "POST",
                url: "{{ route('user.store') }}",
                data: fdata,
                dataType: "json",
                processData: false,
                contentType: false,
                success: function(response) {
                    toastr.success(response.message);
                    window.location.href = "{{ route('dashboard.index') }}";
                },
                error: function(errors) {
                    toastr.error(errors.responseJSON.message);
                    if ($('#user_id').val()) {
                        $('#company_id').prop('disabled', true);
                        $('#user_type_id').prop('disabled', true);
                    }
                }
            });
        });
    </script>
@endsection
