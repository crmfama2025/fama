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
                            {{-- @can('tenant.add') --}}
                            <div class="card-header">
                                {{-- <h3 class="card-title">Area Details</h3> --}}
                                <span class="float-right">
                                    @if (auth()->user()->hasAnyPermission(['tenant-registration.add']))
                                        <a href="{{ route('tenant-registration.create') }}"
                                            class="btn btn-info float-right m-1">
                                            Add Tenant
                                        </a>
                                    @endif



                                </span>
                            </div>
                            {{-- @endcan --}}
                            <!-- /.card-header -->
                            <div class="card-body table-responsive">
                                <table id="tenantsRegistraionTable" class="table table-striped table-hover display nowrap"
                                    style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Action</th>
                                            <th>Status</th>
                                            <th>Agreement Code</th>
                                            <th>Tenant Details</th>
                                            <th>Business Type</th>
                                            <th>Property / Unit / SubUnit</th>
                                            <th>Rent Details</th>
                                            <th>Start Date</th>
                                            <th>End Date</th>
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

    <!-- Approval Modal -->
    <div class="modal fade" id="approvalModal" tabindex="-1" aria-labelledby="approvalModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="approvalForm" method="POST" action="">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="approvalModalLabel">Approval Tenant</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="approvalRemarks">Remarks</label>
                            <textarea class="form-control" id="approvalRemarks" name="approved_comments" rows="3"
                                placeholder="Enter your remarks here..." required></textarea>
                        </div>
                        <input type="hidden" name="status" id="approvalStatus">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
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
            let table = $('#tenantsRegistraionTable').DataTable({
                // alert("test");
                processing: true,
                serverSide: true,
                responsive: true,

                ajax: {
                    url: "{{ route('tenant-registration.list') }}",
                    data: function(d) {
                        // d.company_id = $('#companyFilter').val();
                    },
                    error: function(xhr, error, thrown) {
                        console.error('Status:', xhr.status);
                        console.error('Response:', xhr.responseText);
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'id',
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
                        data: 'is_approved',
                        name: 'is_approved',
                        render: function(data, type, row) {
                            let badgeClass = '';
                            let text = '';

                            switch (data) {
                                case 0:
                                    badgeClass = 'badge badge-warning text-white';
                                    text = 'Pending';
                                    break;
                                case 1:
                                    badgeClass = 'badge badge-success text-black';
                                    text = 'Approved';
                                    break;
                                case 2:
                                    badgeClass = 'badge badge-danger text-white';
                                    text = 'Rejected';
                                    break;

                            }

                            return '<span class="' + badgeClass + '">' + text + '</span>';
                        },
                    },
                    {
                        data: 'sales_agreement_code',
                        name: 'sales_agreement_code'
                    },
                    {
                        data: 'tenant_details',
                        name: 'tenant_details',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'business_type',
                        name: 'business_type'
                    },
                    {
                        data: 'property_details',
                        name: 'property_details',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'rent_details',
                        name: 'rent_details',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'start_date',
                        name: 'start_date'
                    },
                    {
                        data: 'end_date',
                        name: 'end_date'
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
                        let url = "{{ route('tenant-registraion.export') }}" + "?search=" +
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
                //text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "DELETE",
                        url: '/tenant-registration/' + id,
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        processData: false,
                        contentType: false,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        dataType: "json",
                        success: function(response) {
                            toastr.success(response.message);
                            $('#tenantsRegistraionTable').DataTable().ajax.reload();
                        }
                    });

                }
                //else {
                //     toastr.error(errors.responseJSON.message);
                // }
            });
        }
        $(document).on('click', '.open-approval-modal', function() {

            let url = $(this).data('url');

            // set form action dynamically
            $('#approvalForm').attr('action', url);

            // open modal
            $('#approvalModal').modal('show');

        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '{{ session('success') }}',
                timer: 3000,
                showConfirmButton: false
            });
            // OR using Toastr:
            // toastr.success('{{ session('success') }}');
        @endif

        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: '{{ session('error') }}',
                timer: 3000,
                showConfirmButton: false
            });
            // OR using Toastr:
            // toastr.error('{{ session('error') }}');
        @endif
    </script>
    {{-- @include('admin.sales.approval-js'); --}}
@endsection
