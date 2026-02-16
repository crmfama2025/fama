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
    <style>
        #referralsTable {
            table-layout: fixed;
        }

        #referralsTable td {
            max-width: 220px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        #referralsTable th {
            white-space: nowrap;
            font-weight: 600;
            vertical-align: middle;
        }
    </style>
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
                            <div class="card-header">

                            </div>
                            <!-- /.card-header -->
                            <div class="card-body table-responsive">
                                <table id="referralsTable"
                                    class="table table-striped table-bordered table-hover nowrap align-middle"
                                    width="100%">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Action</th>
                                            <th>Date of Referral</th>
                                            <th>Name</th>
                                            <th>Company Name</th>
                                            <th>Rate</th>
                                            <th>Commission Amount</th>
                                            <th>Referral Status</th>
                                            <th>Frequency</th>
                                            <th>Payment Terms</th>
                                            <th>Referred Investor Name</th>
                                            <th>Referred Investment Amount</th>
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
            table = $('#referralsTable').DataTable({
                processing: true,
                serverSide: true,
                // responsive: true,
                pageLength: 5,

                ajax: {
                    url: "{{ route('referrals.list') }}"
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
                        data: 'investment_date',
                        name: 'investment.investment_date'
                    }, {
                        data: 'investor_name',
                        name: 'referrer.investor_name'
                    }, {
                        data: 'company_name',
                        name: 'referrer.investment.company.company_name'
                    },
                    {
                        data: 'referral_commission_perc',
                        name: 'referral_commission_perc'
                    },
                    {
                        data: 'referral_commission_amount',
                        name: 'referral_commission_amount'
                    },
                    {
                        data: 'referral_status',
                        name: 'referral_status'
                    }, {
                        data: 'referral_commission_frequency',
                        name: 'commissionFrequency.commission_frequency_name'
                    },
                    {
                        data: 'term_name',
                        name: 'paymentTerm.term_name'
                    },
                    {
                        data: 'referred_investor_name',
                        name: 'investor.investor_name'
                    },
                    {
                        data: 'referred_investment_amount',
                        name: 'investment.investment_amount'
                    },

                ],

                order: [
                    [0, 'desc']
                ],
                dom: 'Bfrtip',
                buttons: [{
                    extend: 'excelHtml5',
                    text: 'Export Excel',
                    title: 'Investments Data',
                    action: function(e, dt, node, config) {
                        let searchValue = dt.search();
                        let url = "{{ route('referral.export') }}" + "?search=" +
                            encodeURIComponent(searchValue);
                        window.location.href = url;
                    }
                }],
                columnDefs: [{
                        targets: [0],
                        className: 'text-center font-weight-bold'
                    }, // #
                    {
                        targets: [1],
                        className: 'text-center'
                    }, // Action
                    {
                        targets: [2],
                        className: 'text-nowrap text-center'
                    }, // Date
                    {
                        targets: [4, 5, 10],
                        className: 'text-right'
                    }, // Numbers
                    {
                        targets: [6, 7, 8],
                        className: 'text-center'
                    }, // Status / Frequency / Terms
                ],
            });
        });
    </script>
@endsection
