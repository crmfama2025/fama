@extends('admin.layout.admin_master')

@section('custom_css')
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

                            <!-- /.card-header -->

                            <!-- /.card -->

                            <div class="card-header">
                                {{-- <div class="card card-info">
                                    <!-- /.card-header -->
                                    <!-- form start -->
                                    <form class="form-horizontal">
                                        <div class="form-group row m-4">
                                            <div class="col-md-3">
                                                <label for="inputPassword3">Month</label>
                                                <select class="form-control select2" name="month" id="month">
                                                    <option value="">Select Month</option>
                                                    <?php for ($m = 1; $m <= 12; ++$m) { ?>
                                                    <option value="{{ $m }}">
                                                        <?= date('F', mktime(0, 0, 0, $m, 1)) ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label for="inputPassword3">Batch</label>
                                                <select class="form-control select2" name="batch_id" id="batch_id">
                                                    <option value="">Select Batch</option>
                                                    @foreach ($payoutbatches as $payoutbatch)
                                                        <option value="{{ $payoutbatch->id }}">
                                                            {{ $payoutbatch->batch_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label for="inputPassword3">Investor</label>
                                                <select class="form-control select2" name="investor_id" id="investor_id">
                                                    <option value="">Select Investor</option>
                                                    @foreach ($investors as $investor)
                                                        <option value="{{ $investor->id }}">
                                                            {{ $investor->investor_name }}
                                                        </option>
                                                    @endforeach

                                                </select>
                                            </div>


                                            <div class="col-md-1 float-right">
                                                <button type="button" class="btn btn-info searchbtnchq">Search</button>
                                            </div>
                                    </form>
                                </div> --}}
                                <!-- From Date -->
                                <div class="card card-df card-outline">
                                    <div class="card-header shadow-sm">
                                        <h5 class="card-title mb-0">Filter</h5>
                                    </div>
                                    <div class="d-flex justify-content-end mx-4 my-2">
                                        <button type="button" class="btn btn-secondary reset">
                                            <i class="fa fa-undo-alt"></i> Reset
                                        </button>
                                    </div>

                                    <div class="card-body">
                                        <form class="form-row align-items-end fileterform ">
                                            <!-- From Date -->
                                            {{-- <div class="form-group col-md-2">
                                                <label for="dateFrom">Payout Date From</label>
                                                <div class="input-group date" id="dateFrom" data-target-input="nearest">
                                                    <input type="text" class="form-control datetimepicker-input"
                                                        data-target="#dateFrom" placeholder="dd-mm-YYYY" />
                                                    <div class="input-group-append" data-target="#dateFrom"
                                                        data-toggle="datetimepicker">
                                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                    </div>
                                                </div>
                                            </div> --}}

                                            <!-- To Date -->
                                            {{-- <div class="form-group col-md-2">
                                                <label for="dateTo">Payout Date To</label>
                                                <div class="input-group date" id="dateTo" data-target-input="nearest">
                                                    <input type="text" class="form-control datetimepicker-input"
                                                        data-target="#dateTo" placeholder="dd-mm-YYYY" />
                                                    <div class="input-group-append" data-target="#dateTo"
                                                        data-toggle="datetimepicker">
                                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                    </div>
                                                </div>
                                            </div> --}}

                                            <div class="form-group col-lg-3 col-md-4">
                                                <label for="inputPassword3">Month</label>
                                                <select class="form-control select2" name="month" id="month">
                                                    <option value="">Select Month</option>
                                                    <?php for ($m = 1; $m <= 12; ++$m) { ?>
                                                    <option value="{{ $m }}">
                                                        <?= date('F', mktime(0, 0, 0, $m, 1)) ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                            <!-- Property -->
                                            <div class="form-group col-lg-3 col-md-4">
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
                                            <div class="form-group col-lg-3 col-md-4">
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
                                            <div class="form-group col-lg-3 col-md-4">
                                                <label for="investmentTermTypeSelect">Investment Term</label>
                                                <select class="form-control select2" id="investmentTermTypeSelect"
                                                    name="investment_term_type">
                                                    <option value="">All Terms</option>
                                                    <option value="1">Long Term</option>
                                                    <option value="2">Short Term</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-lg-3 col-md-4">
                                                <label for="investmentStatusSelect">Investment Status</label>
                                                <select class="form-control select2" id="investmentStatusSelect"
                                                    name="investment_status">
                                                    <option value="">All Status</option>
                                                    <option value="1">Active</option>
                                                    <option value="0">Inactive</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-lg-3 col-md-4">
                                                <label for="projectSelect">Payout Batch</label>
                                                <select class="form-control select2" id="batchSelect"
                                                    name="payout_batch_id">
                                                    <option value="">Select Batch</option>
                                                    @foreach ($payoutbatches as $batch)
                                                        <option value="{{ $batch->id }}">
                                                            {{ $batch->batch_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group col-lg-3 col-md-4">
                                                <label for="investmentStatusSelect">Payout Status</label>
                                                <select class="form-control select2" id="payoutSelect" name="is_processed">
                                                    <option value="">All Status</option>
                                                    <option value="1">Paid</option>
                                                    <option value="0">Not Paid</option>
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
                            <div class="card-body">
                                <table id="payoutPendingTable" class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Investor Name</th>
                                            <th>Company Name</th>
                                            <th>Investment Code</th>
                                            <th>Payout Date</th>
                                            <th>Payout Type</th>
                                            <th>payout Amount</th>
                                            <th>Paid Amount</th>
                                            <th>Amount Pending</th>
                                            <th width="188">Payment Mode</th>
                                            <th>Paid Date</th>
                                            <th width="188">Paid Mode</th>
                                            <th>Paid Company </th>
                                            <th>Paid Bank</th>
                                            <th>Paid Cheque Number</th>
                                            <th>Status</th>
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


            <!-- /.modal-dialog -->
        </section>
    </div>
@endsection


@section('custom_js')
    <!-- Select2 -->
    <script src="{{ asset('assets/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('assets/moment/moment.min.js') }}"></script>
    <!-- Tempusdominus Bootstrap 4 -->
    <script src="{{ asset('assets/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>
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
        $('#dateFrom').datetimepicker({
            format: 'DD-MM-YYYY'
        });

        $('#dateTo').datetimepicker({
            format: 'DD-MM-YYYY'
        });
        $(function() {
            let table = $('#payoutPendingTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,

                ajax: {
                    url: "{{ route('investment-payout.list') }}",
                    data: function(d) {
                        // d.month = $('#month').val();
                        // d.batch_id = $('#batch_id').val();
                        // d.investor_id = $('#investor_id').val();
                        // Current visible filters
                        d.date_from = $('#dateFrom input').val();
                        d.date_to = $('#dateTo input').val();

                        d.investor_id = $('#investorSelect').val();
                        d.company_id = $('#companySelect').val();

                        d.investment_term_type = $('#investmentTermTypeSelect').val();
                        d.investment_status = $('#investmentStatusSelect').val();
                        d.month = $('#month').val();
                        d.payout_batch_id = $('#batchSelect').val();
                        d.is_processed = $('#payoutSelect').val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'id'
                    }, {
                        data: 'investor_name',
                        name: 'investor_name',
                    },
                    {
                        data: 'company_name',
                        name: 'company_name',
                    },
                    {
                        data: 'investment_code',
                        name: 'investment.investment_code',
                    },
                    {
                        data: 'payout_date',
                        name: 'payout_date',
                    },
                    {
                        data: 'payout_type',
                        name: 'payout_type',
                    },
                    // {
                    //     data: 'property_name',
                    //     name: 'contract.property.property_name',
                    // },
                    {
                        data: 'payout_amount',
                        name: 'payout_amount',
                    },
                    {
                        data: 'amount_paid',
                        name: 'ivestor_payment_distributions.amount_paid',
                    },
                    {
                        data: 'amount_pending',
                        name: 'amount_pending',
                    },
                    {
                        data: 'payment_mode',
                        name: 'payment_mode',
                    },
                    {
                        data: 'paid_date',
                        name: 'paid_date',
                    },
                    {
                        data: 'paid_mode',
                        name: 'paid_mode',
                    },
                    {
                        data: 'paid_company',
                        name: 'paidCompany.company_name',
                    },
                    {
                        data: 'paid_bank',
                        name: 'paid_bank',
                    },
                    {
                        data: 'paid_cheque_number',
                        name: 'paid_cheque_number',
                    },
                    {
                        data: 'status',
                        name: 'status',
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

                        let params = {

                            // DataTables global search
                            search: dt.search(),

                            // Current filters
                            date_from: $('#dateFrom input').val(),
                            date_to: $('#dateTo input').val(),

                            investor_id: $('#investorSelect').val(),
                            company_id: $('#companySelect').val(),
                            month: $('#month').val(),

                            investment_term_type: $('#investmentTermTypeSelect').val(),

                            investment_status: $('#investmentStatusSelect').val(),
                            payout_batch_id: $('#batchSelect').val(),
                            is_processed: $('#payoutSelect').val()
                        };

                        console.log('Export Filters:', params);

                        let url =
                            "{{ route('payout-report.export') }}" +
                            '?' +
                            $.param(params);
                        window.location.href = url;
                    }
                }]
            });

            $('.searchbtnchq').on('click', function(e) {

                e.preventDefault();

                table.ajax.reload(null, true);
            });


            /*
            |--------------------------------------------------------------------------
            | Reset
            |--------------------------------------------------------------------------
            */

            /*
            |--------------------------------------------------------------------------
            | Reset
            |--------------------------------------------------------------------------
            */

            $('.reset').on('click', function() {

                // Clear date fields
                $('#dateFrom input').val('');
                $('#dateTo input').val('');

                // Reset Select2 fields
                $('#month').val('').trigger('change');
                $('#investorSelect').val('').trigger('change');
                $('#companySelect').val('').trigger('change');
                $('#investmentTermTypeSelect').val('').trigger('change');
                $('#investmentStatusSelect').val('').trigger('change');
                $('#batchSelect').val('').trigger('change');
                $('#payoutSelect').val('').trigger('change');

                // Clear DataTable search
                table.search('').draw();

                // Reload table with cleared filters
                table.ajax.reload(null, true);
            });
        });
    </script>
@endsection
