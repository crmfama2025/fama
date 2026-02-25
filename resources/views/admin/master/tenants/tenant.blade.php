@extends('admin.layout.admin_master')

@section('custom_css')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('assets/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
@endsection

@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>{{ $title }}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
                            <li class="breadcrumb-item active">"{{ $title }}</li>
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
                            {{-- @can('area.add') --}}
                            <div class="card-header">
                                {{-- <h3 class="card-title">Area Details</h3> --}}
                                <span class="float-right">

                                    <a href="{{ route('tenant.create') }}" class="btn btn-info float-right m-1">
                                        Add Tenant
                                    </a>

                                    <button class="btn btn-secondary float-right m-1" data-toggle="modal"
                                        data-target="#modal-import">Import</button>

                                </span>
                            </div>
                            {{-- @endcan --}}
                            <!-- /.card-header -->
                            <div class="card-body table-responsive">
                                <table id="tenantsTable" class="table table-striped table-hover" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Action</th>
                                            <th>Tenant Name</th>
                                            <th>Email</th>
                                            <th>Mobile</th>
                                            <th>Nationality</th>
                                            <th>Address</th>
                                            <th>Street</th>
                                            <th>City</th>
                                            <th>Contact Person</th>
                                            <th>Contact Email</th>
                                            <th>Contact Number</th>
                                            <th>Department</th>
                                            <th>Payment Mode</th>
                                            <th>Payment Frequency</th>
                                            <th>Security Cheque</th>
                                            <th>Tenant Type</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
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




            <div class="modal fade" id="modal-import">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Import</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="" id="areaImportForm" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="modal-body">
                                <div class="card-body">
                                    <div class="form-group row">
                                        <label for="inputEmail3" class="col-sm-3 col-form-label">Import excel</label>
                                        <input type="file" name="file" class="col-sm-9 form-control">
                                    </div>
                                </div>
                                <!-- /.card-body -->
                            </div>
                            <div class="modal-footer justify-content-between">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                <button type="button" id="importBtn" class="btn btn-info">Import</button>
                            </div>
                        </form>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
            <!-- /.modal -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
@endsection
@section('custom_js')
    <!-- DataTables  & Plugins -->
    <script src="{{ asset('assets/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/jszip/jszip.min.js') }}"></script>
    <script src="{{ asset('assets/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('assets/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('assets/datatables-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('assets/datatables-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('assets/datatables-buttons/js/buttons.colVis.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.22.5/dist/sweetalert2.all.min.js"></script>
    <script>
        $(function() {
            let table = $('#tenantsTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,

                ajax: {
                    url: "{{ route('tenant.list') }}",
                    data: function(d) {
                        // d.company_id = $('#companyFilter').val();
                    },
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'agreement_tenants.id',
                        orderable: true,
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'tenant_name',
                        name: 'agreement_tenants.tenant_name'
                    },
                    {
                        data: 'tenant_email',
                        name: 'agreement_tenants.tenant_email'
                    },
                    {
                        data: 'tenant_mobile',
                        name: 'agreement_tenants.tenant_mobile'
                    },
                    {
                        data: 'nationality_name',
                        name: 'nationality.nationality_name'
                    },
                    {
                        data: 'tenant_address',
                        name: 'agreement_tenants.tenant_address'
                    },
                    {
                        data: 'tenant_street',
                        name: 'agreement_tenants.tenant_street'
                    },
                    {
                        data: 'tenant_city',
                        name: 'agreement_tenants.tenant_city'
                    },
                    {
                        data: 'contact_person',
                        name: 'agreement_tenants.contact_person'
                    },
                    {
                        data: 'contact_email',
                        name: 'agreement_tenants.contact_email'
                    },
                    {
                        data: 'contact_number',
                        name: 'agreement_tenants.contact_number'
                    },
                    {
                        data: 'contact_person_department',
                        name: 'agreement_tenants.contact_person_department'
                    },
                    {
                        data: 'payment_mode',
                        name: 'paymentMode.payment_mode_name'
                    },
                    {
                        data: 'payment_frequency',
                        name: 'paymentFrequency.profit_interval_name'
                    },
                    {
                        data: 'security_cheque_status',
                        name: 'agreement_tenants.security_cheque_status'
                    },
                    {
                        data: 'tenant_type',
                        name: 'agreement_tenants.tenant_type'
                    },

                ],
                order: [
                    [0, 'desc']
                ],
                dom: 'Bfrtip', // This is important for buttons
                buttons: [{
                    extend: 'excelHtml5',
                    text: 'Export Excel',
                    title: 'Area Data',
                    action: function(e, dt, node, config) {
                        // redirect to your Laravel export route
                        let searchValue = dt.search();
                        let url = "{{ route('tenant.export') }}" + "?search=" +
                            encodeURIComponent(searchValue);
                        window.location.href = url;
                    }
                }]
            });

            // $('#companyFilter').change(function() {
            //     table.ajax.reload();
            // });


        });

        function deleteConf(id) {
            Swal.fire({
                title: "Are you sure?",
                // text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "DELETE",
                        url: '/tenant/' + id,
                        // data: {
                        //     _token: $('meta[name="csrf-token"]').attr('content')
                        // },
                        processData: false,
                        contentType: false,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        dataType: "json",
                        success: function(response) {
                            toastr.success(response.message);
                            $('#tenantsTable').DataTable().ajax.reload();
                        }
                    });

                }
                //  else {
                //     toastr.error(errors.responseJSON.message);
                // }
            });
        }
    </script>
@endsection
