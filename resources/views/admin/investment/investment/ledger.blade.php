@extends('admin.layout.admin_master')

@section('custom_css')
    <!-- daterange picker -->
    <link rel="stylesheet" href="{{ asset('assets/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet" href="{{ asset('assets/select2/css/select2.min.css') }}">
    <!-- iCheck for checkboxes and radio inputs -->
    <link rel="stylesheet" href="{{ asset('assets/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('assets/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <!-- DataTables -->
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
                            <li class="breadcrumb-item"><a href="../dashboard.php">Home</a></li>
                            <li class="breadcrumb-item active">Investment</li>
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
                                <span class="float-right">
                                    <a class="btn btn-info float-right m-1" href="{{ route('investment.index') }}">Back to
                                        Investments</a>
                                    {{-- <button class="btn btn-secondary float-right m-1" data-toggle="modal"
                                        data-target="#modal-import">Import</button> --}}
                                </span>
                            </div>

                            <!-- /.card-header -->
                            <div class="card-body table-responsive">
                                <table id="investmentLedgerTable" class="table table-striped  nowrap"width="100%">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Action</th>
                                            <th>Investment Code</th>
                                            <th>Investor Name</th>
                                            <th>Company Name</th>
                                            <th>Transaction Type</th>
                                            <th>Credit/Debit</th>
                                            <th>Transaction Amount</th>
                                            <th>Transaction Date</th>
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
    </div>
    <!-- /.content-wrapper -->
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
        let table = '';
        $(function() {
            // alert("test");
            table = $('#investmentLedgerTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                // pageLength: 5,
                ajax: {
                    url: "{{ route('investment.ledgers', $investment->id) }}",
                    data: function(d) {
                        // You can add filters here if needed
                        d.investment_id = "{{ $investment->id }}";
                    },
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'id'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'investment_code',
                        name: 'investment_code'
                    },


                    {
                        data: 'investor_name',
                        name: 'investor.investor_name'
                    },


                    {
                        data: 'company_name',
                        name: 'investment.company.company_name'
                    },
                    {
                        data: 'transaction_type',
                        name: 'transactionType.transaction_type'
                    },
                    {
                        data: 'credit_debit',
                        name: 'credit_debit'
                    },
                    {
                        data: 'transaction_amount',
                        name: 'transaction_amount'
                    },
                    {
                        data: 'transaction_date',
                        name: 'transaction_date'
                    },
                    //  Main Document



                ],

                order: [
                    [0, 'desc']
                ],
                // dom: 'Bfrtip',
                // buttons: [{
                //     extend: 'excelHtml5',
                //     text: 'Export Excel',
                //     title: 'Investments Data',
                //     action: function(e, dt, node, config) {
                //         let searchValue = dt.search();
                //         let url = "{{ route('investment.export') }}" + "?search=" +
                //             encodeURIComponent(searchValue);
                //         window.location.href = url;
                //     }
                // }]
            });

        });

        $('#receiveddate').datetimepicker({
            format: 'DD-MM-YYYY'
        });

        $('#requesteddate').datetimepicker({
            format: 'DD-MM-YYYY'
        });
        $('#terminationdate').datetimepicker({
            format: 'DD-MM-YYYY'
        });
    </script>
@endsection
