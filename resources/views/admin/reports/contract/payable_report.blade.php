@extends('admin.layout.admin_master')

@section('custom_css')
    <link rel="stylesheet" href="{{ asset('assets/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
@endsection

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>{{ $title }}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="../dashboard.php">Home</a></li>
                            <li class="breadcrumb-item active">{{ $title }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="card card-info card-outline">
                                    <div class="card-header shadow-sm">
                                        <h5 class="card-title mb-0">Filter</h5>
                                    </div>
                                    <div class="d-flex justify-content-end mx-4 my-2">
                                        <button type="button" class="btn btn-secondary reset">
                                            <i class="fa fa-undo-alt"></i> Reset
                                        </button>
                                    </div>

                                    <div class="card-body">
                                        <div class="row mb-3">
                                            <div class="col-md-3">
                                                <label for="companyFilter">Company</label>

                                                <select id="companyFilter" class="form-control select2">
                                                    <option value="">All</option>

                                                    @foreach ($companies as $company)
                                                        <option value="{{ $company->id }}">
                                                            {{ $company->company_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col-md-3">
                                                <label for="paymentStatusFilter">Payment Status</label>

                                                <select id="paymentStatusFilter" class="form-control">
                                                    <option value="">All</option>
                                                    <option value="paid">Paid</option>
                                                    <option value="partial">Partially paid</option>
                                                    <option value="unpaid">Unpaid</option>
                                                    <option value="overdue">Overdue</option>
                                                </select>
                                            </div>

                                            <div class="col-md-2">
                                                <label for="dateFrom">Due Date From</label>
                                                <input type="date" id="dateFrom" class="form-control">
                                            </div>

                                            <div class="col-md-2">
                                                <label for="dateTo">Due Date To</label>
                                                <input type="date" id="dateTo" class="form-control">
                                            </div>

                                            <div class="col-md-2 d-flex align-items-end">
                                                <button type="button" id="applyFilter" class="btn btn-primary mr-2">
                                                    Apply
                                                </button>

                                                <button type="button" id="resetFilter" class="btn btn-secondary">
                                                    Reset
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body table-responsive">
                                <table id="contractPayablesTable" class="table table-striped nowrap" width="100%">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            {{-- <th>Payment Detail ID</th> --}}
                                            <th>Project Number</th>
                                            <th>Company</th>
                                            <th>Vendor</th>
                                            <th>Composition</th>
                                            <th>Payment Date</th>
                                            <th>Payable Amount</th>
                                            <th>Payment Status</th>
                                            <th>Area</th>
                                            <th>Locality</th>
                                            <th>Property</th>
                                            <th>Contract Start</th>
                                            <th>Contract End</th>
                                            <th>Contract Type</th>
                                            <th>Direct/Indirect</th>
                                            <th>Contract Status</th>
                                            <th>Scheduled Mode</th>
                                            <th>Original Cheque No.</th>
                                            <th>Total Paid</th>
                                            <th>Outstanding</th>
                                            <th>Latest Paid Date</th>
                                            <th>Paid Mode</th>
                                            <th>Paid Bank</th>
                                            <th>Paid Cheque No.</th>
                                            <th>Payment Remarks</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('custom_js')
    <script src="{{ asset('assets/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('assets/moment/moment.min.js') }}"></script>
    <script src="{{ asset('assets/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>
    <script src="{{ asset('assets/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/daterangepicker/daterangepicker.js') }}"></script>
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
        $('.select2').select2({
            theme: 'bootstrap4',
            width: '100%'
        });

        $('#applyFilter').on('click', function() {
            table.ajax.reload();
        });

        $('#resetFilter').on('click', function() {
            $('#companyFilter').val('').trigger('change');
            $('#paymentStatusFilter').val('');
            $('#dateFrom').val('');
            $('#dateTo').val('');

            table.search('');
            table.ajax.reload();
        });
    </script>

    <script>
        let table = '';
        $(function() {
            table = $('#contractPayablesTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                scrollX: true,

                pageLength: 25,
                ajax: {
                    url: "{{ route('payable-report.data') }}",
                    data: function(d) {
                        d.keyword = d.search.value;
                        d.search.value = '';

                        d.company_id = $('#companyFilter').val();
                        d.payment_status = $('#paymentStatusFilter').val();
                        d.date_from = $('#dateFrom').val();
                        d.date_to = $('#dateTo').val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        searchable: false,
                        orderable: false
                    },
                    // {
                    //     data: 'payment_detail_id',
                    //     name: 'cpd.id'
                    // },
                    {
                        data: 'project_number',
                        name: 'c.project_number'
                    },
                    {
                        data: 'company_name',
                        name: 'co.company_name'
                    },
                    {
                        data: 'vendor_name',
                        name: 'v.vendor_name'
                    },
                    {
                        data: 'composition',
                        name: 'composition',
                        searchable: false,
                        orderable: false,
                        defaultContent: '-'
                    },
                    {
                        data: 'due_date',
                        name: 'cpd.payment_date'
                    },
                    {
                        data: 'payable_amount',
                        name: 'cpd.payment_amount',
                        className: 'text-right'
                    },
                    {
                        data: 'payment_status',
                        name: 'payment_status',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'area_name',
                        name: 'a.area_name'
                    },
                    {
                        data: 'locality_name',
                        name: 'l.locality_name'
                    },
                    {
                        data: 'property_name',
                        name: 'p.property_name'
                    },
                    {
                        data: 'contract_start_date',
                        name: 'cd.start_date'
                    },
                    {
                        data: 'contract_end_date',
                        name: 'cd.end_date'
                    },
                    {
                        data: 'contract_type',
                        name: 'ct.contract_type',
                        defaultContent: '-'
                    },
                    {
                        data: 'contract_source_name',
                        name: 'contract_source',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'contract_status_name',
                        name: 'c.contract_status',
                        searchable: false
                    },
                    {
                        data: 'scheduled_payment_mode',
                        name: 'original_pm.payment_mode_name',
                        defaultContent: '-'
                    },
                    {
                        data: 'cheque_no',
                        name: 'cpd.cheque_no',
                        defaultContent: '-'
                    },
                    {
                        data: 'total_paid',
                        name: 'total_paid',
                        searchable: false,
                        orderable: false,
                        className: 'text-right'
                    },
                    {
                        data: 'outstanding_amount',
                        name: 'outstanding_amount',
                        searchable: false,
                        orderable: false,
                        className: 'text-right'
                    },

                    {
                        data: 'latest_paid_date',
                        name: 'last_clear.paid_date',
                        defaultContent: '-'
                    },
                    {
                        data: 'paid_payment_mode',
                        name: 'paid_pm.payment_mode_name',
                        defaultContent: '-'
                    },
                    {
                        data: 'paid_bank_name',
                        name: 'paid_bank.bank_name',
                        defaultContent: '-'
                    },
                    {
                        data: 'paid_cheque_number',
                        name: 'last_clear.paid_cheque_number',
                        defaultContent: '-'
                    },
                    {
                        data: 'payment_remarks',
                        name: 'last_clear.payment_remarks',
                        defaultContent: '-'
                    }
                ],
                order: [
                    [8, 'asc']
                ],
                dom: 'Bfrtip',
                buttons: [{
                    text: 'Export Excel',
                    className: 'btn btn-success',
                    action: function() {
                        const params = $.param({
                            company_id: $('#companyFilter').val(),
                            payment_status: $('#paymentStatusFilter').val(),
                            date_from: $('#dateFrom').val(),
                            date_to: $('#dateTo').val(),
                            search: table.search()
                        });

                        window.location.href =
                            "{{ route('payable-report.export') }}?" + params;
                    }
                }]
            });
        });
    </script>
@endsection
