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
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Statement</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="../dashboard.php">Home</a></li>
                            <li class="breadcrumb-item active">Statement</li>
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
                            <div class="row justify-content-end">
                                <a href="{{ route('tenant.cheque.clearing') }}" class="btn btn-info mr-4 mt-2 "><i
                                        class="fa fa-arrow-left mr-1"></i>Back</a>

                            </div>

                            <!-- /.card-header -->
                            <div class="card-body">


                                <div class="card card-info card-outline">

                                    <!-- /.card-header -->
                                    <div class="card-header shadow-sm">
                                        <h5 class="card-title mb-0">Filter Report</h5>
                                    </div>
                                    <!-- form start -->
                                    <form class="form-horizontal">
                                        <div class="form-group row m-4 ">
                                            <div class="col-md-2">
                                                <label for="exampleInputEmail1">Cleared Date From</label>
                                                <div class="input-group date" id="dateFrom" data-target-input="nearest">
                                                    <input type="text" class="form-control datetimepicker-input"
                                                        data-target="#dateFrom" placeholder="dd-mm-YYYY" />
                                                    <div class="input-group-append" data-target="#dateFrom"
                                                        data-toggle="datetimepicker">
                                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <label for="exampleInputEmail1">Cleared Date To</label>
                                                <div class="input-group date" id="dateTo" data-target-input="nearest">
                                                    <input type="text" class="form-control datetimepicker-input"
                                                        data-target="#dateTo" placeholder="dd-mm-YYYY" />
                                                    <div class="input-group-append" data-target="#dateTo"
                                                        data-toggle="datetimepicker">
                                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <button type="button"
                                                    class="btn btn-info w-100 searchbtnchq">Search</button>
                                            </div>
                                            {{-- <div class="col-md-2">
                                                <button type="button" class="btn btn-secondary w-100 reset"
                                                    style="margin-top: 31px;">
                                                    <i class="fa fa-undo-alt"></i> Reset
                                                </button>
                                            </div> --}}
                                        </div>
                                    </form>
                                </div>
                                <!-- /.card -->

                                <div class="card searchCheque">
                                    <!-- /.card-header -->
                                    <div class="card-body table-responsive">
                                        <table id="receivablesReport" class="table table-striped  nowrap"
                                            style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Project</th>
                                                    <th>Tenant</th>
                                                    <th>Building</th>
                                                    <th>Unit Details</th>
                                                    <th>Due Date</th>
                                                    <th>Cleared Date</th>
                                                    <th>Payment Mode</th>
                                                    <th>Company Name</th>
                                                    <th>Amount paid</th>
                                                    <th>Pending</th>
                                                    <th>Composition</th>
                                                    <th>Status</th>
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
    {{-- <script src="{{ asset('assets/icheck-bootstrap/icheck.min.js') }}"></script> --}}
    <script>
        let table = '';

        $(function() {
            table = $('#receivablesReport').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 5,

                ajax: {
                    url: "{{ route('tenant.receivables.report.list') }}",
                    data: function(d) {
                        let companyId = $('.companyFilter:checked').val() || 'all';
                        if (companyId === 'all') companyId = null;

                        d.company_id = companyId;
                        d.date_from = $('#dateFrom input').val();
                        d.date_to = $('#dateTo input').val();
                        d.property_id = $('#propertySelect').val();
                        d.unit_id = $('#unitSelect').val();
                        d.mode_id = $('#modeSelect').val();
                    },
                },

                columns: [{
                        data: 'DT_RowIndex',
                        name: 'cleared_receivables.id'
                    },
                    {
                        data: 'project_number',
                        name: 'agreementPaymentDetail.agreement.contract.project_number'
                    },
                    {
                        data: 'tenant_name',
                        name: 'agreementPaymentDetail.agreement.tenant.tenant_name'
                    },
                    {
                        data: 'property_name',
                        name: 'agreementPaymentDetail.agreement.contract.property.property_name'
                    },
                    {
                        data: 'unit_number',
                        name: 'agreementPaymentDetail.agreement.agreement_units.contractUnitDetail.unit_number'
                    },
                    {
                        data: 'payment_date',
                        name: 'payment_date'
                    },
                    {
                        data: 'paid_date',
                        name: 'cleared_receivables.paid_date'
                    },
                    {
                        data: 'payment_mode_name',
                        // name: 'agreementPaymentDetail.paymentMode.payment_mode_name'
                        name: 'paidMode.payment_mode_name'

                    },
                    {
                        data: 'company_name',
                        name: 'paidCompany.company_name'
                    },
                    {
                        data: 'paid_amount',
                        name: 'cleared_receivables.paid_amount'
                    },
                    {
                        data: 'pending_amount',
                        name: 'cleared_receivables.pending_amount'
                    },
                    {
                        data: 'installment_name',
                        name: 'agreementPaymentDetail.agreementPayment.installment.installment_name'
                    },
                    {
                        data: 'is_payment_received',
                        name: 'is_payment_received',
                        orderable: false,
                        searchable: false
                    },
                ],

                rowCallback: function(row, data, index) {
                    // optional: highlight bounced rows
                    if (data.is_payment_received_status == 3) {
                        $(row).css('background-color', '#f8d7da');
                        $(row).css('color', '#721c24');
                    }
                },

                order: [
                    [0, 'desc']
                ],

                dom: 'Bfrtip',
                buttons: [{
                    extend: 'excelHtml5',
                    text: 'Export Excel',
                    title: 'Receivables Data',
                    action: function(e, dt, node, config) {
                        let searchValue = dt.search();
                        let form = $('<form>', {
                            action: "{{ route('receivableReport.export') }}",
                            method: 'POST'
                        }).append(
                            $('<input>', {
                                type: 'hidden',
                                name: 'search',
                                value: searchValue
                            }),
                            $('<input>', {
                                type: 'hidden',
                                name: '_token',
                                value: '{{ csrf_token() }}'
                            }),
                            $('<input>', {
                                type: 'hidden',
                                name: 'date_from',
                                value: $('#dateFrom input').val()
                            }),
                            $('<input>', {
                                type: 'hidden',
                                name: 'date_to',
                                value: $('#dateTo input').val()
                            })
                        ).appendTo('body');

                        form.submit();
                    }

                }]
            });
        });

        $('#dateFrom').datetimepicker({
            format: 'DD-MM-YYYY',
            defaultDate: moment().startOf('month')
        });
        $('#dateTo').datetimepicker({
            format: 'DD-MM-YYYY',
            defaultDate: moment()
        });

        $(document).ready(function() {
            // $('.searchCheque').hide();
            $('.propertyselect').hide();
            $('.unitselect').hide();
        });

        $('.searchbtnchq').click(function() {
            $('.searchCheque').show();
            table.ajax.reload();
        });
    </script>
@endsection
