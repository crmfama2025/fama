@extends('admin.layout.admin_master')

@section('custom_css')
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('assets/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('assets/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
@endsection

@section('content')
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

                            <!-- /.card-header -->
                            <div class="card-body table-responsive">
                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <label>Company</label>
                                        <select id="filter_company" class="form-control select2">
                                            <option value="">All Companies</option>
                                            @foreach ($companies as $company)
                                                <option value="{{ $company->id }}">{{ $company->company_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                                        <label>Allocated By</label>
                                        <select id="filter_added_by" class="form-control select2">
                                            <option value="">All Users</option>
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}">
                                                    {{ $user->first_name }} {{ $user->last_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                                        <label>Paid Date</label>
                                        <input type="date" id="filter_paid_date" class="form-control">
                                    </div>

                                    <div class="col-md-3 d-flex align-items-end">
                                        <button type="button" id="filter_btn" class="btn btn-primary mr-2">
                                            <i class="fas fa-search"></i> Filter
                                        </button>
                                        <button type="button" id="reset_filter" class="btn btn-secondary">
                                            Reset
                                        </button>
                                    </div>
                                </div>
                                <table id="allocationsTable" class="table table-striped table-hover" style="width: 100%">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Company Name</th>
                                            <th>Allocation Amount</th>
                                            <th>Paid Date</th>
                                            <th>Bank Reference</th>
                                            <th>Allocated By</th>
                                            <th>Created At</th>
                                            <!-- <th>Projects</th> -->
                                            <th>Projects</th>
                                            <th>Action</th>
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





        </section>
        <!-- /.content -->

        {{-- once, at the bottom of the page --}}
        <div class="modal fade" id="unitsModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Projects &amp; Units</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" id="unitsModalBody"></div>
                </div>
            </div>
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
    <script>
        $(function() {
            let table = $('#allocationsTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: "{{ route('receivable.allocations.data') }}",
                    type: 'GET',
                    data: function(d) {
                        d.company_id = $('#filter_company').val();
                        d.added_by = $('#filter_added_by').val();
                        d.paid_date = $('#filter_paid_date').val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'id',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'company_name',
                        name: 'company.company_name'
                    },
                    {
                        data: 'allocation_amount',
                        name: 'allocated_amount'
                    },
                    {
                        data: 'allocation_date',
                        name: 'cleared_date'
                    },
                    {
                        data: 'bank_reference',
                        name: 'cleared_receivable_allocations.bank_reference'
                    },
                    {
                        data: 'allocated_by',
                        name: 'addedBy.first_name'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'project_numbers',
                        name: 'project_numbers',
                        title: 'Project Number',
                        defaultContent: '-'
                    },
                    // {
                    //   data: 'project_units',
                    // name: 'project_units',
                    //title: 'Project Number',
                    //defaultContent: '-'
                    //},
                    {
                        data: 'action',
                        name: 'action',
                        searchable: false,
                        orderable: false
                    },
                ],
                order: [
                    [2, 'desc']
                ],
                dom: 'Bfrtip',
                buttons: [{
                    extend: 'excelHtml5',
                    text: 'Export Excel',
                    title: 'Company Data',
                    action: function(e, dt, node, config) {
                        // redirect to your Laravel export route

                        let params = {
                            search: dt.search(),
                            company_id: $('#filter_company').val(),
                            added_by: $('#filter_added_by').val(),
                            paid_date: $('#filter_paid_date').val()
                        };

                        let url = "{{ route('receipt-allocation.export') }}" + '?' +
                            $.param(params);

                        window.location.href = url;
                    }
                }]
            });
            $('#filter_btn').on('click', function() {
                table.ajax.reload();
            });

            $('#reset_filter').on('click', function() {
                $('#filter_company').val('').trigger('change');
                $('#filter_added_by').val('').trigger('change');
                $('#filter_paid_date').val('');

                table.ajax.reload();
            });
        });



        function deleteConf(id) {
            // alert(id);
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
                        // url: '/company/' + id,

                        url: "{{ route('company.destroy', ':id') }}".replace(':id', id),
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        dataType: "json",
                        success: function(response) {
                            toastr.success(response.message);
                            $('#companyTable').DataTable().ajax.reload();
                        }
                    });

                }
                //  else {
                //     toastr.error(errors.responseJSON.message);
                // }
            });
        }
    </script>

    <script>
        const projectUnitsUrl = "{{ route('allocations.project-units', ':id') }}";

        $(document).on('click', '.view-units', function() {
            const id = $(this).data('id');
            const url = projectUnitsUrl.replace(':id', id);

            $('#unitsModalBody').html('<div class="text-center p-3">Loading...</div>');
            $('#unitsModal').modal('show');

            $.get(url, function(data) {
                const rows = Object.entries(data).map(([project, units]) =>
                    `<tr><td>${project}</td><td>${units.length ? units.join(', ') : '-'}</td></tr>`
                ).join('');

                $('#unitsModalBody').html(
                    `<table class="table table-sm mb-0">
                <thead><tr><th>Project No.</th><th>Units - Subunit</th></tr></thead>
                <tbody>${rows || '<tr><td colspan="2">No data</td></tr>'}</tbody>
            </table>`
                );
            }).fail(function() {
                $('#unitsModalBody').html('<div class="text-danger p-3">Failed to load data.</div>');
            });
        });
    </script>
@endsection
