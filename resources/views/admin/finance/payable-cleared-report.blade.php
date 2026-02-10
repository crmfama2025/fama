@extends('admin.layout.admin_master')

@section('custom_css')
    <!-- daterange picker -->
    <link rel="stylesheet" href="{{ asset('assets/daterangepicker/daterangepicker.css') }}">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet" href="{{ asset('assets/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
    <!-- iCheck for checkboxes and radio inputs -->
    <link rel="stylesheet" href="{{ asset('assets/icheck-bootstrap/icheck-bootstrap.min.css') }}">
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
                        <h1>Payable Report</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="../dashboard.php">Home</a></li>
                            <li class="breadcrumb-item active">Payable Report</li>
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
                            <div class="card-header">
                                <a href="{{ route('finance.payable.clearing') }}" class="btn btn-info float-right mr-1">
                                    <i class="fas fa-arrow-left mr-2"></i> Back</a>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">

                                <div class="card card-info">
                                    <!-- /.card-header -->
                                    <!-- form start -->
                                    <form class="form-horizontal">
                                        <div class="form-group row m-4">
                                            <div class="col-md-2">
                                                <label for="exampleInputEmail1">From</label>
                                                <div class="input-group date" id="dateFrom" data-target-input="nearest">
                                                    <input type="text" class="form-control datetimepicker-input"
                                                        data-target="#dateFrom" id="date_From" placeholder="dd-mm-YYYY"
                                                        value="{{ request('from_date', date('01-m-Y')) }}" />
                                                    <div class="input-group-append" data-target="#dateFrom"
                                                        data-toggle="datetimepicker">
                                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <label for="exampleInputEmail1">To</label>
                                                <div class="input-group date" id="dateTo" data-target-input="nearest">
                                                    <input type="text" class="form-control datetimepicker-input"
                                                        data-target="#dateTo" id="date_To" placeholder="dd-mm-YYYY"
                                                        value="{{ request('from_date', date('d-m-Y')) }}" />
                                                    <div class="input-group-append" data-target="#dateTo"
                                                        data-toggle="datetimepicker">
                                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-1">
                                                <button type="button" class="btn btn-info searchbtnchq">Search</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <!-- /.card -->

                                <div class="card searchCheque">
                                    <!-- /.card-header -->
                                    <div class="card-body">
                                        <table id="clearedList" class="table table-striped projects display nowrap">
                                            <thead>
                                                <tr>
                                                    <th style="width: 1%">#</th>
                                                    <th>Project</th>
                                                    <th>Company</th>
                                                    <th>Vendor</th>
                                                    <th>Building</th>
                                                    <th>Due Date</th>
                                                    <th>Clear Date</th>
                                                    <th>Amount Paid</th>
                                                    <th>Pending</th>
                                                    <th>Payment Mode</th>
                                                    <th>Composition</th>

                                                </tr>
                                            </thead>
                                            <tbody>

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </div>

        </section>
    </div>
@endsection


@section('custom_js')
    <script src="{{ asset('assets/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('assets/moment/moment.min.js') }}"></script>
    <script src="{{ asset('assets/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>
    <script src="{{ asset('assets/daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ asset('assets/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/jszip/jszip.min.js') }}"></script>
    <script src="{{ asset('assets/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('assets/datatables-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('assets/datatables-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('assets/datatables-buttons/js/buttons.colVis.min.js') }}"></script>

    <script>
        $('#dateFrom').datetimepicker({
            format: 'DD-MM-YYYY'
        });

        $('#dateTo').datetimepicker({
            format: 'DD-MM-YYYY'
        });
    </script>

    <script>
        $(function() {
            let table = $('#clearedList').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,

                ajax: {
                    url: "{{ route('cleared.data') }}",
                    data: function(d) {
                        d.date_from = $('#date_From').val();
                        d.date_to = $('#date_To').val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'id',
                        orderable: true,
                        searchable: false
                    }, {
                        data: 'project_number',
                        name: 'project_number',
                    },
                    // {
                    //     data: 'contract_type',
                    //     name: 'contract_types.contract_type',
                    // },
                    {
                        data: 'company_name',
                        name: 'contract.company.company_name',
                    },
                    {
                        data: 'vendor_name',
                        name: 'contract.vendor.vendor_name',
                    },
                    {
                        data: 'property_name',
                        name: 'contract.property.property_name',
                    },
                    {
                        data: 'payment_date',
                        name: 'payment_date',
                    },
                    {
                        data: 'paid_date',
                        name: 'paid_date',
                    },
                    {
                        data: 'paid_amount',
                        name: 'paid_amount',
                    },
                    {
                        data: 'pending_amount',
                        name: 'contract_payable_clears.pending_amount',
                    },
                    {
                        data: 'payment_mode',
                        name: 'paidMode.payment_mode_name',

                    },
                    {
                        data: 'composition',
                        name: 'composition',
                        orderable: true,
                        searchable: true
                    },
                ],
                rowCallback: function(row, data, index) {
                    // Example: Highlight pending payments
                    console.log(data.has_returned);
                    if (data.has_returned === 1) {
                        console.log(data.has_returned);
                        $(row).css('background-color', '#ffe1e1'); // light red
                    }

                },
                order: [
                    [0, 'desc']
                ],
                dom: 'Bfrtip', // This is important for buttons
                buttons: [{
                    extend: 'excelHtml5',
                    text: 'Export Excel',
                    title: 'Contract Data',
                    action: function(e, dt, node, config) {
                        // redirect to your Laravel export route
                        let searchValue = dt.search();
                        let url = "{{ route('payables.report.export') }}" + "?search=" +
                            encodeURIComponent(searchValue);
                        window.location.href = url;
                    }
                }]
            });

            // Filter buttons
            $('.filter-btn').on('click', function() {
                let filterValue = $(this).data('filter');

                // Reset ALL buttons
                $('.filter-btn').each(function() {
                    let solidClass = $(this).attr('add-class'); // btn-warning
                    let outlineClass = solidClass ? 'btn-outline-' + solidClass.replace('btn-',
                        '') : '';

                    if (solidClass) {
                        $(this).removeClass(solidClass).addClass(outlineClass);
                    }
                });

                // Apply ACTIVE state to clicked button
                let solidClass = $(this).attr('add-class'); // e.g. btn-warning
                let outlineClass = solidClass ? 'btn-outline-' + solidClass.replace('btn-', '') : '';


                if (solidClass) {
                    $(this).removeClass(outlineClass).addClass(solidClass);
                }

                // Apply DataTable search column filter (status = column index 1)
                table.column(2).search(filterValue).draw();
            });

            $('.searchbtnchq').on('click', function(e) {
                e.preventDefault();
                table.ajax.reload();
            });

        });
    </script>
@endsection
