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
                            <li class="breadcrumb-item active">{{ $title }}/li>
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


                                <div class="card card-df card-outline">
                                    <div class="card-header shadow-sm">
                                        <h5 class="card-title mb-0">Filter</h5>
                                    </div>
                                    <div class="d-flex justify-content-end mx-4">
                                        <button type="button" class="btn btn-secondary reset">
                                            <i class="fa fa-undo-alt"></i> Reset
                                        </button>
                                    </div>

                                    <div class="card-body">
                                        <form class="form-row align-items-end fileterform justify-content-end">
                                            <!-- From Date -->
                                            <div class="form-group col-md-2">
                                                <label for="dateFrom">Investment Date From</label>
                                                <div class="input-group date" id="dateFrom" data-target-input="nearest">
                                                    <input type="text" class="form-control datetimepicker-input"
                                                        data-target="#dateFrom" placeholder="dd-mm-YYYY" />
                                                    <div class="input-group-append" data-target="#dateFrom"
                                                        data-toggle="datetimepicker">
                                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- To Date -->
                                            <div class="form-group col-md-2">
                                                <label for="dateTo">Investment Date To</label>
                                                <div class="input-group date" id="dateTo" data-target-input="nearest">
                                                    <input type="text" class="form-control datetimepicker-input"
                                                        data-target="#dateTo" placeholder="dd-mm-YYYY" />
                                                    <div class="input-group-append" data-target="#dateTo"
                                                        data-toggle="datetimepicker">
                                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Property -->
                                            <div class="form-group col-md-2">
                                                <label for="propertySelect">Investor</label>
                                                <select class="form-control select2" id="investorSelect" name="investor_id">
                                                    <option value="">Select Investor</option>
                                                    @foreach ($investors as $investor)
                                                        <option value="{{ $investor->id }}">
                                                            {{ $investor->investor_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label for="projectSelect">Company</label>
                                                <select class="form-control select2" id="companySelect" name="company_id">
                                                    <option value="">Select Company</option>
                                                    @foreach ($companies as $com)
                                                        <option value="{{ $com->id }}">
                                                            {{ $com->company_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label for="investmentTermTypeSelect">Investment Term</label>
                                                <select class="form-control select2" id="investmentTermTypeSelect"
                                                    name="investment_term_type">
                                                    <option value="">All Terms</option>
                                                    <option value="1">Long Term</option>
                                                    <option value="2">Short Term</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label for="investmentStatusSelect">Investment Status</label>
                                                <select class="form-control select2" id="investmentStatusSelect"
                                                    name="investment_status">
                                                    <option value="">All Status</option>
                                                    <option value="1">Active</option>
                                                    <option value="0">Inactive</option>
                                                </select>
                                            </div>



                                            <!-- Search Button -->
                                            <div class="form-group col-md-2">
                                                <button type="button" class="btn btn-primary btn-block searchbtnchq">
                                                    <i class="fa fa-search"></i> Search
                                                </button>
                                            </div>

                                        </form>
                                    </div>
                                </div>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body table-responsive">
                                <table id="investmentsTable" class="table table-striped  nowrap"width="100%">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Investment Code</th>
                                            <th>Status</th>
                                            <th>Company Name</th>
                                            <th>Invested Company</th>
                                            <th>Investor Name</th>
                                            <th>Investment Amount</th>
                                            <th>Received Amount</th>
                                            <th>Investment Date</th>
                                            <th>Investment Tenure</th>
                                            <th>Profit Interval</th>
                                            <th>Profit %</th>
                                            <th>Maturity date</th>
                                            <th>Profit Release Date</th>
                                            <th>Grace Period </th>
                                            <th>Payout Batch</th>
                                            <th>Nominee Details</th>
                                            <th>Commission Amount</th>
                                            <th>Commission %</th>
                                            <th>Duration Type</th>
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
            table = $('#investmentsTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                // pageLength: 5,
                ajax: {
                    url: "{{ route('investment-report.list') }}",
                    data: function(d) {
                        // You can add filters here if needed
                        d.date_from = $('#dateFrom input').val();
                        d.date_to = $('#dateTo input').val();
                        d.company_id = $('#companySelect').val();
                        d.investor_id = $('#investorSelect').val();
                        d.investment_term_type = $('#investmentTermTypeSelect').val();
                        d.investment_status = $('#investmentStatusSelect').val();

                    },
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'id'
                    },

                    {
                        data: 'investment_code',
                        name: 'investment_code'
                    },
                    {
                        data: 'investment_status',
                        name: 'investment_status',
                        render: function(data, type, row) {
                            let mainStatus = '';
                            let mainClass = '';

                            let terminateStatus = '';
                            let terminateClass = '';

                            // Main status
                            if (row.investment_status == 1) {
                                mainStatus = 'Active';
                                mainClass = 'badge-success';
                            } else {
                                mainStatus = 'Inactive';
                                mainClass = 'badge-secondary';
                            }

                            // Termination status
                            if (row.terminate_status == 1) {
                                terminateStatus = 'Termination Requested';
                                terminateClass = 'badge-warning';
                            } else if (row.terminate_status == 2) {
                                terminateStatus = 'Terminated';
                                terminateClass = 'badge-danger';
                            }

                            return `
                                <span class="badge ${mainClass}">${mainStatus}</span><br>
                                <span class="badge ${terminateClass}">${terminateStatus}</span>
                        `;
                        }

                    },
                    {
                        data: 'company_name',
                        name: 'company.company_name'
                    },
                    {
                        data: 'invested_company_name',
                        name: 'investedCompany.company_name'
                    },
                    {
                        data: 'investor_name',
                        name: 'investor.investor_name'
                    },
                    {
                        data: 'investment_amount',
                        name: 'investment_amount'
                    },
                    {
                        data: 'total_received_amount',
                        name: 'total_received_amount'
                    },
                    {
                        data: 'investment_date',
                        name: 'investment_date'
                    },
                    {
                        data: 'investment_tenure',
                        name: 'investment_tenure'
                    },
                    {
                        data: 'profit_interval',
                        name: 'profitInterval.profit_interval_name'
                    },
                    {
                        data: 'profit_perc',
                        name: 'profit_perc'
                    },
                    {
                        data: 'maturity_date',
                        name: 'maturity_date'
                    },
                    {
                        data: 'profit_release_date',
                        name: 'profit_release_date'
                    },
                    {
                        data: 'grace_period',
                        name: 'grace_period'
                    },
                    {
                        data: 'batch_name',
                        name: 'payoutBatch.batch_name'
                    },
                    {
                        data: 'nominee_details',
                        name: 'nominee_name'
                    },
                    {
                        data: 'referral_commission_amount',
                        name: 'investmentReferral.referral_commission_amount'
                    },

                    {
                        data: 'referral_commission_perc',
                        name: 'investmentReferral.referral_commission_perc'
                    },
                    {
                        data: 'investment_term_type',
                        name: 'investment_term_type'
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
                        let url = "{{ route('investment-report.export') }}" + "?search=" +
                            encodeURIComponent(searchValue);
                        window.location.href = url;
                    }
                }]
            });

        });

        $('#dateFrom').datetimepicker({
            format: 'DD-MM-YYYY'
        });

        $('#dateTo').datetimepicker({
            format: 'DD-MM-YYYY'
        });
        $('#terminationdate').datetimepicker({
            format: 'DD-MM-YYYY'
        });
        $('.searchbtnchq').click(function() {
            $('.searchCheque').show();
            // alert("test");
            // const searchformContainer = document.querySelector('.fileterform');
            // if (!validateformContainer(searchformContainer)) {

            //     Swal.fire({
            //         toast: true,
            //         position: 'top-end',
            //         icon: 'warning',
            //         title: "Please fill all required fields correctly!",
            //         showConfirmButton: false,
            //         timer: 3000,
            //         timerProgressBar: true
            //     });
            //     return;
            // }
            table.ajax.reload();
        });
    </script>
@endsection
