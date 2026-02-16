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

    <style>
        .permission-table-wrapper {
            max-height: 400px;
            overflow-y: auto;
            position: relative;
        }

        /* Make header sticky */
        .permission-table thead th {
            position: sticky;
            top: 0;
            background: #f8f9fa;
            z-index: 20;
        }

        /* Prevent layout jump */
        .permission-table {
            border-collapse: separate;
            border-spacing: 0;
        }
    </style>
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
                                <div class="d-flex justify-content-center row">
                                    <div class="col-5">
                                        <div class="card card-widget  shadow-sm">
                                            <div class="bg-gradient-olive pl-4 py-2 widget-user-header">

                                                <h5 class="widget-user-username"><i
                                                        class="fa fa-solid mx-1 fa-user"></i>{{ $user->first_name }}
                                                    {{ $user->last_name }}</h5>
                                            </div>
                                            <div class="card-footer p-0">
                                                <div class="row">
                                                    <div class="col-3">
                                                        <img src="{{ asset('storage/' . $user->profile_path) }}"
                                                            alt="Profile Photo" height="125">
                                                    </div>
                                                    <ul class="nav flex-column col-9">
                                                        <li class="nav-item">
                                                            <div class="nav-link">
                                                                Email
                                                                <span
                                                                    class="float-right text-bold">{{ $user->email }}</span>
                                                            </div>
                                                        </li>
                                                        <li class="nav-item">
                                                            <div class="nav-link">
                                                                Phone Number
                                                                <span
                                                                    class="float-right text-bold">{{ $user->phone }}</span>
                                                            </div>
                                                        </li>

                                                        <li class="nav-item">
                                                            <div class="nav-link">
                                                                User Type <span
                                                                    class="float-right text-bold text-capitalize ">{{ $user->user_type->user_type }}</span>
                                                            </div>
                                                        </li>


                                                    </ul>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <form action="javascript:void(0)" id="UserForm" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="id" id="user_id" value="{{ $user->id ?? '' }}">
                                    <input type="hidden" name="profile" value="">
                                    {{-- <div class="bs-stepper">
                                        <div class="bs-stepper-header" role="tablist">
                                            <!-- your steps here -->
                                            <div class="step" data-target="#logins-part">
                                                <button type="button" class="step-trigger" role="tab"
                                                    aria-controls="logins-part" id="logins-part-trigger">
                                                    <span class="bs-stepper-circle"><i class="fas fa-user"></i></span>
                                                    <span class="bs-stepper-label">User Details</span>
                                                </button>
                                            </div>
                                            <div class="line"></div>
                                            <div class="step" data-target="#information-part">
                                                <button type="button" class="step-trigger" role="tab"
                                                    aria-controls="information-part" id="information-part-trigger">
                                                    <span class="bs-stepper-circle"><i class="fas fa-user-tag"></i></span>
                                                    <span class="bs-stepper-label">Permission</span>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="bs-stepper-content card p-3"> --}}
                                    <!-- your steps content here -->
                                    {{-- <div id="logins-part" class="content step-content" role="tabpanel"
                                        aria-labelledby="logins-part-trigger">
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="exampleInputEmail1" class="asterisk">First Name</label>
                                                <input type="text" class="form-control" id="first_name" name="first_name"
                                                    placeholder="First Name" value="{{ $user->first_name ?? '' }}"
                                                    required>
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
                                                    placeholder="Password"
                                                    @if (!isset($user)) required @endif>
                                            </div>
                                        </div>
                                        <div class="form-group row">
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
                                            @if (auth()->user()->company_id)
                                                <input type="hidden" name="company_id" id="company_id"
                                                    value="{{ auth()->user()->company_id }}">
                                            @else
                                                <div class="col-md-4">
                                                    <label class="asterisk">Company</label>
                                                    <select class="form-control select2" name="company_id"
                                                        id="company_id" required>
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
                                        <button type="button" class="btn btn-info nextBtn">Next</button>
                                    </div> --}}
                                    {{-- <div id="information-part" class="content step-content" role="tabpanel"
                                        aria-labelledby="information-part-trigger"> --}}



                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Company</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($companies as $company)
                                                @if (isset($companyPermissionCounts[$company->id]) && $companyPermissionCounts[$company->id] > 0)
                                                    @php
                                                        $permissionText = "<i class='fas fa-pencil-alt'></i>";
                                                        $class = 'btn-info';
                                                    @endphp
                                                @else
                                                    @php
                                                        $permissionText = "<i class='fas fa-plus'></i>";
                                                        $class = 'btn-primary';
                                                    @endphp
                                                @endif
                                                <tr>
                                                    <td>{{ $company->company_name }}</td>
                                                    <td>
                                                        <button class="btn btn-sm {{ $class }}"
                                                            onclick="openPermissionModal({{ $company->id }})">
                                                            {!! $permissionText !!}
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>


                                    <span class="float-right">

                                        <a href="{{ route('user.index') }}" class="btn btn-info mr-2">back</a>
                                        {{-- <button type="submit" class="btn btn-info mr-2">Submit</button> --}}
                                    </span>


                                    {{-- </div> --}}
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

    {{-- <div class="modal fade" id="">
        <div class="modal-dialog modal-xl">
            <form id="PermissionForm">
                @csrf
                <input type="hidden" name="user_id" value="{{ $user->id }}">
                <input type="hidden" name="company_id" id="modal_company_id">

                <div class="modal-body">
                    @include('admin.user.permission-table')
                </div>

                <div class="modal-footer">
                    <button class="btn btn-success">Save</button>
                </div>
            </form>
        </div>
    </div> --}}


    <div class="modal fade" id="permissionModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Permission</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" id="areaImportForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                    <input type="hidden" name="company_id" id="modal_company_id">
                    <div class="modal-body">
                        @include('admin.user.permission-table')
                        <!-- /.card-body -->
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-info" onclick="submitPermissionForm()">Submit</button>
                    </div>
                </form>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
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
        $('.masterParent').on('change', function() {
            const row = $(this).data('row');
            $('.masterChild' + row).prop('checked', this.checked);
        });

        function openPermissionModal(companyId) {
            showLoader();
            $('#modal_company_id').val(companyId);

            // Clear all checkboxes first
            $('input[name="permission_id[]"]').prop('checked', false);

            $.post("{{ route('user.company.permissions') }}", {
                _token: "{{ csrf_token() }}",
                user_id: "{{ $user->id }}",
                company_id: companyId
            }, function(res) {
                res.permission_ids.forEach(function(id) {
                    $('input[name="permission_id[]"][value="' + id + '"]')
                        .prop('checked', true);
                });

                hideLoader();
                $('#permissionModal').modal('show');

            });
        }

        function submitPermissionForm() {
            showLoader();
            $.post(
                "{{ route('user.company.permissions.store') }}",
                $('#areaImportForm').serialize(),
                function(res) {
                    toastr.success(res.message);
                    $('#permissionModal').modal('hide');
                    window.location.reload();
                    hideLoader();
                }
            );
        }
    </script>
@endsection
