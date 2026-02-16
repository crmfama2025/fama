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
                        <h1>Receivables Clearing</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="../dashboard.php">Home</a></li>
                            <li class="breadcrumb-item active">Receivables Clearing</li>
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
                            <div class="card-body">

                                <div class="card card-danger card-outline">
                                    <div class="card-header shadow-sm">
                                        <h5 class="card-title mb-0">Filter Cheques</h5>
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
                                                <label for="dateFrom" class="asterisk">From</label>
                                                <div class="input-group date" id="dateFrom" data-target-input="nearest">
                                                    <input type="text" class="form-control datetimepicker-input" required
                                                        data-target="#dateFrom" placeholder="dd-mm-YYYY" />
                                                    <div class="input-group-append" data-target="#dateFrom"
                                                        data-toggle="datetimepicker">
                                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- To Date -->
                                            <div class="form-group col-md-2">
                                                <label for="dateTo" class="asterisk">To</label>
                                                <div class="input-group date" id="dateTo" data-target-input="nearest">
                                                    <input type="text" class="form-control datetimepicker-input" required
                                                        data-target="#dateTo" placeholder="dd-mm-YYYY" />
                                                    <div class="input-group-append" data-target="#dateTo"
                                                        data-toggle="datetimepicker">
                                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Property -->
                                            <div class="form-group col-md-2">
                                                <label for="propertySelect">Property</label>
                                                <select class="form-control select2" id="propertySelect" name="property_id">
                                                    <option value="">Select Property</option>
                                                    @foreach ($properties as $property)
                                                        <option value="{{ $property->id }}">{{ $property->property_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <!-- Unit -->
                                            <div class="form-group col-md-2">
                                                <label for="unitSelect">Unit</label>
                                                <select class="form-control select2" id="unitSelect" name="unit_id">
                                                    <option value="">Select Unit</option>
                                                    {{-- @foreach ($units as $unit)
                                                        <option value="{{ $unit->id }}">{{ $unit->unit_number }}
                                                        </option>
                                                    @endforeach --}}
                                                </select>
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label for="unitSelect">Tenant</label>
                                                <select class="form-control select2" id="tenantSelect" name="tenant_id">
                                                    <option value="">Select Tenant</option>
                                                    @foreach ($tenants as $tenant)
                                                        <option value="{{ $tenant->id }}">{{ $tenant->tenant_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <!-- Unit -->
                                            <div class="form-group col-md-2">
                                                <label for="unitSelect">Payment Mode</label>
                                                <select class="form-control select2" id="modeSelect" name="mode_id">
                                                    <option value="">Select PaymentMode</option>
                                                    @foreach ($agpaymentmodes as $mode)
                                                        <option value="{{ $mode->id }}">{{ $mode->payment_mode_name }}
                                                        </option>
                                                    @endforeach
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

                                <!-- /.card -->

                                <div class="card searchCheque">
                                    <!-- /.card-header -->
                                    <div class="card-header">
                                        <button type="button" class="btn btn-success float-right mx-1 clearChequeBtn"
                                            data-form="bulk">Clear
                                            All</button>
                                        <button type="button" class="btn btn-danger float-right mx-1 cheque-bounce"
                                            title="bounced">Bounced</button>
                                        <a href="{{ route('finance.receivables.report') }}"
                                            class="btn btn-outline-maroon float-right mx-1 btn-flat"
                                            title="View Report"><i class="fa fa-book mr-1"></i>View
                                            Report</a>
                                        {{-- <button type="button" class="btn btn-outline-info btn-block btn-flat"><i
                                                class="fa fa-book"></i> .btn-block .btn-flat</button> --}}
                                    </div>
                                    <div class="card-body">

                                        <!-- Company Filter Buttons -->
                                        <div class="card card-info mb-3">
                                            <div class="card-body text-center">
                                                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                                    <label class="btn btn-outline-info active">
                                                        <input type="radio" name="companyFileter" value="all"
                                                            autocomplete="off" checked> All
                                                    </label>
                                                    @foreach ($companies as $company)
                                                        <label class="btn btn-outline-info">
                                                            <input type="radio" name="companyFileter"
                                                                class="companyFilter" value="{{ $company->id }}"
                                                                autocomplete="off">
                                                            {{ $company->company_short_code }}
                                                        </label>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Table -->
                                        <div class="card">
                                            <div class="card-body table-responsive">
                                                <table id="tenantChequeTable" class="table table-striped  nowrap"
                                                    style="width:100%">
                                                    <thead>
                                                        <tr>
                                                            {{-- <th class="text-center">
                                                                <input type="checkbox" id="selectAll"
                                                                    onclick="toggleAllCheckboxes()">Select All
                                                            </th> --}}
                                                            <th>
                                                                <div class="icheck-primary d-inline">
                                                                    <input type="checkbox" name="selectall"
                                                                        id="selectAll" value="1"
                                                                        onclick="toggleAllCheckboxes()">
                                                                    <label for="selectAll">Select All
                                                                    </label>
                                                                </div>
                                                            </th>
                                                            <th>#</th>
                                                            <th>Action</th>

                                                            <th>Project</th>
                                                            <th>Company</th>
                                                            <th>Tenant</th>
                                                            <th>Building</th>
                                                            <th>Unit</th>
                                                            <th>Subunit</th>
                                                            <th>Due Date</th>
                                                            <th>Payment Mode</th>
                                                            <th>Amount</th>
                                                            <th>Composition</th>
                                                            <th>Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <!-- Data populated by DataTables / Blade -->
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

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



            {{-- <div class="modal fade" id="modal-single-clear">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">You Are Going To Pass This Cheque!</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <form action="" id="receivableClearForm" method="POST" enctype="multipart/form-data">
                            <div class="modal-body">
                                <div class="card-body">



                                    <!-- Clearing Date -->
                                    <div class="form-group">
                                        <label>Clearing Date</label>
                                        <div class="input-group date" id="clearingdateSingle"
                                            data-target-input="nearest">
                                            <input type="text" class="form-control datetimepicker-input"
                                                id="clearing_date_input" data-target="#clearingdateSingle"
                                                placeholder="dd-mm-YYYY" />
                                            <div class="input-group-append" data-target="#clearingdateSingle"
                                                data-toggle="datetimepicker">
                                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Clearing Amount -->
                                    <div class="form-group">
                                        <label>Clearing Amount</label>
                                        <input type="text" class="form-control" id="clearing_amount_input"
                                            placeholder="Clearing Amount">
                                    </div>

                                    <!-- Payment Type -->
                                    <label><b>Select Payment Type</b></label>
                                    <div class="form-group d-flex ">

                                        <!-- Cheque Payment -->
                                        <div class="form-check m-2">
                                            <input type="radio" name="payment_type" id="cheque_payment" value=3
                                                class="form-check-input payment_mode">
                                            <label for="cheque_payment" class="form-check-label">Cheque</label>
                                        </div>

                                        <!-- Bank Transfer -->
                                        <div class="form-check m-2">
                                            <input type="radio" name="payment_type" id="bank_transfer" value=2
                                                class="form-check-input payment_mode">
                                            <label for="bank_transfer" class="form-check-label">Bank Transfer</label>
                                        </div>
                                    </div>

                                    <!-- Bank Selection -->
                                    <div class="form-group" id="bank_div" style="display:none;">
                                        <label>Select Bank</label>
                                        <select id="bank_id" class="form-control">
                                            <option value="">Select Bank</option>
                                            @foreach ($banks as $bank)
                                                <option value="{{ $bank->id }}">{{ $bank->bank_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Cheque Number -->
                                    <div class="form-group" id="cheque_div" style="display:none;">
                                        <label>Cheque Number</label>
                                        <input type="text" id="cheque_no" class="form-control"
                                            placeholder="Enter Cheque Number">
                                    </div>

                                    <div class="form-group" id="mode_change_reason_div" style="display:none;">
                                        <label>Reason for Changing Payment Mode</label>
                                        <textarea id="mode_change_reason" class="form-control" rows="3"
                                            placeholder="Enter reason for changing payment mode"></textarea>
                                    </div>

                                </div>
                            </div>

                            <div class="modal-footer justify-content-between">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                <button type="button" id="clearBtn" class="btn btn-info">Clear</button>
                            </div>

                        </form>
                    </div>
                </div>
            </div> --}}
            <div class="modal fade" id="modal-single-clear">
                <div class="modal-dialog  modal-dialog-centered">
                    <div class="modal-content">

                        <div class="modal-header bg-info">
                            <h5 class="modal-title">
                                {{-- <i class="fa fa-check-circle mr-2 text-info"></i> --}}
                                Tenant Receivables
                            </h5>
                            <button type="button" class="close text-white" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>

                        <form id="receivableClearForm" method="POST">
                            @csrf
                            <div class="modal-body">
                                <input type="hidden" id="payment_detail_id" name="payment_detail_id" value="">
                                {{-- <div id="inputs" class="d-none">
                                    <input type="hidden" id="current_mode_id" name="paid_mode_id" value="">
                                    <input type="hidden" id="current_bank_id" name="paid_bank_id" value="">
                                    <input type="hidden" id="current_cheque_number" name="paid_cheque_number"
                                        value="">
                                </div> --}}

                                <!-- Clearing Date -->
                                <div class="form-group">
                                    <label class="asterisk">Clearing Date</label>
                                    <div class="input-group date" id="clearingdateSingle" data-target-input="nearest">
                                        <input type="text" name="paid_date" class="form-control datetimepicker-input"
                                            id="clearing_date_input" data-target="#clearingdateSingle"
                                            placeholder="dd-mm-YYYY" required />
                                        <div class="input-group-append" data-target="#clearingdateSingle"
                                            data-toggle="datetimepicker">
                                            <span class="input-group-text">
                                                <i class="fa fa-calendar"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Clearing Amount -->
                                <div class="form-group" id="clearing_amount_div">
                                    <label>Clearing Amount</label>
                                    <input type="number" step="any" name="paid_amount" class="form-control"
                                        id="clearing_amount_input" placeholder="Enter amount">
                                </div>
                                <div id="clearing_diff_box" class="alert alert-default-info mt-2" style="display:none;">
                                    <span id="clearing_diff_text"></span>
                                </div>

                                <!-- Payment Type -->
                                <div class="form-group" id="payment_modes">
                                    <label><strong>Select Payment Type</strong></label>
                                    <div class="d-flex">

                                        <!-- Cheque -->
                                        <div class="form-check mr-4 cheque">
                                            <input type="checkbox" name="paid_mode_id" value="3"
                                                class="form-check-input payment_mode" id="cheque_payment">
                                            <label class="form-check-label" for="cheque_payment">
                                                Cheque
                                            </label>
                                        </div>
                                        {{-- <div class="custom-control custom-checkbox cheque">
                                            <input class="custom-control-input payment_mode" type="checkbox"
                                                id="customCheckbox1" value="3">
                                            <label for="cheque_payment" class="custom-control-label">
                                                Cheque</label>
                                        </div>
                                        <div class="custom-control custom-checkbox bank">
                                            <input class="custom-control-input payment_mode" type="checkbox"
                                                name="paid_mode_id" id="customCheckbox2" value="2">
                                            <label for="bank_transfer" class="custom-control-label">
                                                Bank Transfer</label>
                                        </div> --}}

                                        <!-- Bank -->
                                        <div class="form-check bank">
                                            <input type="checkbox" name="paid_mode_id" value="2"
                                                name="paid_mode_id" class="form-check-input payment_mode"
                                                id="bank_transfer">
                                            <label class="form-check-label" for="bank_transfer">
                                                Bank Transfer
                                            </label>
                                        </div>

                                    </div>
                                </div>


                                <!-- Bank -->
                                <div class="form-group" id="bank_div" style="display:none;">
                                    <div class="mb-3">
                                        <label class="asterisk">Select Company</label>
                                        <select id="company_id" name="paid_company_id" class="form-control" required>
                                            <option value="">Select Company</option>
                                            @foreach ($companies as $com)
                                                <option value="{{ $com->id }}">{{ $com->company_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label>Select Bank</label>
                                        <select id="bank_id" name="paid_bank_id" class="form-control">
                                            {{-- <option value="">Select Bank</option>
                                            @foreach ($banks as $bank)
                                                <option value="{{ $bank->id }}">{{ $bank->bank_name }}</option>
                                            @endforeach --}}
                                        </select>
                                    </div>
                                </div>

                                <!-- Cheque Number -->
                                <div class="form-group" id="cheque_div" style="display:none;">
                                    <label>Cheque Number</label>
                                    <input type="text" id="cheque_no" name="paid_cheque_number" class="form-control"
                                        placeholder="Enter cheque number">
                                </div>

                                <!-- Mode Change Reason -->
                                <div class="form-group" id="mode_change_reason_div" style="display:none;">
                                    <label>Pyament Remarks</label>
                                    <textarea id="mode_change_reason" name="payment_remarks" class="form-control" rows="3"
                                        placeholder="Enter reason"></textarea>
                                </div>


                            </div>

                            <div class="modal-footer bg-light">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                    Close
                                </button>
                                <button type="button" id="clearBtn" class="btn btn-info">
                                    Clear
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>






            <div class="modal fade" id="modal-bounced-cheque" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow-sm">

                        <!-- Modal Header -->
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title mb-0">Bounced Cheque</h5>
                            <button type="button" class="close text-white" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>

                        <!-- Modal Body -->
                        <form id="bouncedChequeForm" class="p-3">
                            <!-- Cheque Returning Date -->
                            <div class="mb-3">
                                <label for="bounced_date_input" class="form-label asterisk">Returning Date</label>
                                <div class="input-group" id="bouncedDate" data-target-input="nearest">
                                    <input type="text" id="bounced_date_input" name="bounced_date" required
                                        class="form-control datetimepicker-input" data-target="#bouncedDate"
                                        placeholder="dd-mm-YYYY" />
                                    <span class="input-group-text" data-target="#bouncedDate"
                                        data-toggle="datetimepicker">
                                        <i class="fa fa-calendar"></i>
                                    </span>
                                </div>
                            </div>

                            <!-- Reason for Bounce -->
                            <div class="mb-3">
                                <label for="bounced_reason" class="form-label asterisk">Reason for Bounced Cheque</label>
                                <textarea id="bounced_reason" name="bounced_reason" class="form-control" rows="3"
                                    placeholder="Enter the reason for the cheque bounce" required></textarea>
                            </div>
                        </form>

                        <!-- Modal Footer -->
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-danger" id="bouncedClearBtn">Save</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- /.modal -->
        </section>
    </div>
    <div class="modal fade" id="bouncedChequeModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content shadow-lg">

                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle mr-2"></i> Bounced Cheque Details
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">

                    <div class="alert alert-light border-left border-danger p-3 shadow-sm">
                        <h6 class="font-weight-bold text-danger mb-2">
                            <i class="fas fa-info-circle mr-1"></i> Reason
                        </h6>
                        <p id="bouncedReasonText" class="mb-0"></p>
                    </div>

                    <div id="bouncedDateBox" class="mt-3 d-none">
                        <h6 class="font-weight-bold text-dark mb-1">
                            <i class="fas fa-calendar-alt mr-1 text-primary"></i> Returning Date
                        </h6>
                        <p id="bouncedDateText" class="text-muted"></p>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">
                        Close
                    </button>
                </div>

            </div>
        </div>
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
        $('#dateFrom').datetimepicker({
            format: 'DD-MM-YYYY'
        });

        $('#dateTo').datetimepicker({
            format: 'DD-MM-YYYY'
        });

        $(document).ready(function() {
            // $('.searchCheque').hide();

            // $('.propertyselect').hide();
            // $('.unitselect').hide();
        });

        $('.searchbtnchq').click(function() {
            $('.searchCheque').show();
            const searchformContainer = document.querySelector('.fileterform');
            if (!validateformContainer(searchformContainer)) {

                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'warning',
                    title: "Please fill all required fields correctly!",
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
                return;
            }
            table.ajax.reload();
        });

        // $('.radioType').click(function() {
        //     var value = $(this).val();
        //     if (value == "1") {
        //         $('.vendorselect').show();
        //         $('.propertyselect').hide();
        //         $('.unitselect').hide();
        //     } else {
        //         $('.vendorselect').hide();
        //         $('.propertyselect').show();
        //         $('.unitselect').show();
        //     }
        // });

        function toggleAllCheckboxes() {
            document.getElementById('selectAll').addEventListener('change', function() {
                const itemCheckboxes = document.querySelectorAll('.groupCheckbox');
                itemCheckboxes.forEach(checkbox => {
                    checkbox.checked = this
                        .checked;
                });
            });
        }

        $('#clearingdate').datetimepicker({
            format: 'DD-MM-YYYY'
        });
        $('#clearingdateSingle').datetimepicker({
            format: 'DD-MM-YYYY'
        });

        $('#bouncedDate').datetimepicker({
            format: 'DD-MM-YYYY'
        });
    </script>
    <script>
        let table = '';
        $(function() {
            table = $('#tenantChequeTable').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 5,


                ajax: {
                    url: "{{ route('tenant.cheque.list') }}",
                    data: function(d) {
                        let companyId = $('.companyFilter:checked').val() || 'all';
                        if (companyId === 'all') {
                            companyId = null;
                        }
                        d.company_id = companyId;
                        d.date_from = $('#dateFrom input').val();
                        d.date_to = $('#dateTo input').val();
                        d.property_id = $('#propertySelect').val();
                        d.unit_id = $('#unitSelect').val();
                        d.mode_id = $('#modeSelect').val();
                        d.tenant_id = $('#tenantSelect').val();

                    },
                },
                columns: [{
                        data: 'checkbox',
                        name: 'checkbox',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'DT_RowIndex',
                        name: 'agreement_payment_details.id',
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
                        data: 'project_number',
                        name: 'agreement.contract.project_number',
                        render: function(data, type, row) {
                            return data ? data : '';
                        },
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: 'company_name',
                        name: 'agreement.contract.company.company_name',
                    },
                    {
                        data: 'tenant_name',
                        name: 'agreement.tenant.tenant_name',
                        render: function(data, type, row) {
                            return data ? data : '';
                        },
                        orderable: false,
                        searchable: true
                    }, {
                        data: 'property_name',
                        name: 'agreement.contract.property.property_name',
                    },
                    {
                        data: 'unit_number',
                        name: 'agreement.agreement_units.contractUnitDetail.unit_number',

                    },
                    {
                        data: 'subunit_no',
                        name: 'agreement.agreement_units.contractSubunitDetail.subunit_no',
                        render: function(data, type, row) {
                            return data ? data : ' - ';
                        },
                        orderable: false,
                        searchable: true

                    },
                    {
                        data: 'payment_date',
                        name: 'agreement_payment_details.payment_date',

                    },
                    {
                        data: 'payment_mode_name',
                        name: 'paymentMode.payment_mode_name',
                    },
                    {
                        data: 'payment_amount',
                        name: 'agreement_payment_details.payment_amount',
                    },

                    {
                        data: 'installment_name',
                        name: 'agreementPayment.installment.installment_name',
                    },

                    {
                        data: 'is_payment_received',
                        name: 'is_payment_received',
                        render: function(data, type, row) {
                            // Priority: Bounced
                            if (row.has_bounced) {
                                return '<span class="badge bg-danger">Bounced</span>';
                            }

                            switch (data) {
                                case 0:
                                    return '<span class="badge bg-warning">Pending</span>';
                                case 2:
                                    return '<span class="badge bg-info">Partially Paid</span>';
                                case 1:
                                    return '<span class="badge bg-success">Paid</span>';
                                default:
                                    return '<span class="badge bg-secondary">-</span>';
                            }
                        },
                        orderable: true,
                        searchable: true
                    },





                ],
                rowCallback: function(row, data, index) {
                    if (data.is_payment_received == 0 && data.has_bounced == 1) {
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
                            action: "{{ route('tanantReceivables.export') }}",
                            method: 'POST'
                        });

                        // CSRF token
                        form.append($('<input>', {
                            type: 'hidden',
                            name: '_token',
                            value: '{{ csrf_token() }}'
                        }));

                        // Global search
                        form.append($('<input>', {
                            type: 'hidden',
                            name: 'search',
                            value: searchValue
                        }));

                        // Add all filters
                        form.append($('<input>', {
                            type: 'hidden',
                            name: 'company_id',
                            value: $('.companyFilter:checked').val() || ''
                        }));
                        form.append($('<input>', {
                            type: 'hidden',
                            name: 'date_from',
                            value: $('#dateFrom input').val()
                        }));
                        form.append($('<input>', {
                            type: 'hidden',
                            name: 'date_to',
                            value: $('#dateTo input').val()
                        }));
                        form.append($('<input>', {
                            type: 'hidden',
                            name: 'property_id',
                            value: $('#propertySelect').val()
                        }));
                        form.append($('<input>', {
                            type: 'hidden',
                            name: 'unit_id',
                            value: $('#unitSelect').val()
                        }));
                        form.append($('<input>', {
                            type: 'hidden',
                            name: 'mode_id',
                            value: $('#modeSelect').val()
                        }));

                        // Append to body and submit
                        form.appendTo('body').submit();
                    }
                }]
            });
        });
    </script>
    <script>
        let currentAmount = 0;
        let current_bank_id = null;
        let current_cheque_number = null;

        $(document).on('click', '.clearChequeBtn', function() {
            // $('#receivableClearForm')[0].reset();
            let date = $(this).data('date');
            currentAmount = $(this).data('amount');
            // console.log(currentAmount);
            let paymentMode = $(this).data('payment-mode');
            let formtype = $(this).data('form');
            let payment_id = $(this).data('id');
            current_bank_id = $(this).data('bank-id');
            current_cheque_number = $(this).data('cheque-number');
            $('#current_mode_id').val(paymentMode);
            $('#current_bank_id').val(current_bank_id);
            $('#current_cheque_number').val(current_cheque_number);
            checkRowCheckbox(payment_id);

            // let formattedDate = "";
            // if (date) {
            //     let d = new Date(date);
            //     let day = ("0" + d.getDate()).slice(0 - 2);
            //     let month = ("0" + (d.getMonth() + 1)).slice(-2);
            //     let year = d.getFullYear();
            //     formattedDate = day + "-" + month + "-" + year;
            // }

            // Fill fields
            // $('#clearing_date_input').val(formattedDate);
            resetform();
            let checkedCount = $('#tenantChequeTable tbody input.groupCheckbox:checked').length;
            if (formtype === "bulk" && checkedCount === 0) {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'warning',
                    title: "Please select at least one record to clear.",
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
                return;
            }
            if (formtype == "bulk") {
                $('.cheque').hide();
                $('#clearing_amount_div').hide();
                $('#clearing_diff_box').hide();


            } else {
                $('#clearing_date_input').prop('required', true);
                $('#payment_modes').show();
                $('.cheque').show();
                $('#clearing_amount_div').show();
                $('#clearing_amount_input').prop('required', true);
                addClassAsterisk('#clearing_amount_input');
            }
            $('#clearing_amount_input').val(currentAmount);
            $('#modal-single-clear').data('original-mode', paymentMode);

            $('#payment_detail_id').val(payment_id);
            $('#modal-single-clear').modal('show');

        });
        $(document).on('change', '.payment_mode', function() {
            if (!$(this).is(':checked')) {
                resetform();
                return;
            }
            $('.payment_mode').not(this).prop('checked', false);
            let selectedMode = parseInt($(this).val());
            let originalMode = parseInt($('#modal-single-clear').data('original-mode'));

            if (selectedMode !== originalMode) {
                $('#mode_change_reason_div').show();
                $('#mode_change_reason').prop('required', true);
                addClassAsterisk('#mode_change_reason')
            } else {
                $('#mode_change_reason_div').hide();
                $('#mode_change_reason').prop('required', false);
                $('#mode_change_reason').val('');
            }
            togglePaymentFields(selectedMode);


        });

        function resetform() {
            $('.payment_mode').prop('checked', false);
            $('#bank_div').hide();
            $('#cheque_div').hide();
            $('#mode_change_reason_div').hide();
            $('#mode_change_reason').val('');
            $('#bank_id').val('');
            $('#cheque_no').val('');

        }
        $('#clearing_amount_input').on('input', function() {
            let entered = parseFloat($(this).val()) || 0;
            let diff = currentAmount - entered;
            if (entered <= 0) {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'warning',
                    title: "Amount should be greater than zero!",
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
                $('#clearBtn').prop('disabled', true);
            }
            if (entered > currentAmount) {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: "Entered amount cannot exceed the payable amount!",
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
                $('#clearBtn').prop('disabled', true);
            } else {
                $('#clearBtn').prop('disabled', false);
            }


            if (diff !== 0) {
                console.log(diff);
                let text = diff > 0 ?
                    `Amount is short by AED ${diff}` :
                    `Amount exceeds by AED ${Math.abs(diff)}`;

                $('#clearing_diff_text').text(text);
                $('#clearing_diff_box').show();
            } else {
                $('#clearing_diff_box').hide();
            }
        });

        function togglePaymentFields(mode) {

            $('#bank_div').hide();
            $('#bank_id').prop('required', false).val('');

            $('#cheque_div').hide();
            $('#cheque_no').prop('required', false).val('');

            if (mode === 3) {
                $('#bank_div').show();
                $('#bank_id').prop('required', true);
                addClassAsterisk('#bank_id');

                $('#cheque_div').show();
                $('#cheque_no').prop('required', true);
                addClassAsterisk('#cheque_no');

            } else if (mode === 2) {
                $('#bank_div').show();
                $('#bank_id').prop('required', true);
                addClassAsterisk('#bank_id');

            } else {
                $('#bank_id').prop('required', false).val('');
                $('#cheque_no').prop('required', false).val('');
            }
        }

        function checkRowCheckbox(rowId) {
            $('#ichek' + rowId).prop('checked', true);
        }
    </script>
    {{-- Filetr section --}}
    <script>
        let units = @json($units);
        let tenants = @json($tenants);
        let agreements = @json($agreements);
        let banks = @json($banks);
        // console.log('units', units)
        console.log(agreements);

        $(document).on('change', '#propertySelect', function() {
            propertyChange();
        });

        function propertyChange() {
            let property_id = $('#propertySelect').val();
            let unitSelect = $('#unitSelect');

            unitSelect.empty();
            unitSelect.append('<option value="">Select Unit</option>');

            if (property_id) {
                let filteredUnits = units.filter(u => u.contract.property_id == property_id);

                filteredUnits.forEach(u => {
                    unitSelect.append('<option value="' + u.id + '">' + u.unit_number + '</option>');
                });
            }

            unitSelect.trigger('change');
        }
        $(document).on('change', '#unitSelect', function() {
            unitChange();
        });

        function unitChange() {
            let unit_id = $('#unitSelect').val();
            let tenantSelect = $('#tenantSelect');

            tenantSelect.empty();
            tenantSelect.append('<option value="">Select Tenant</option>');

            if (!unit_id) return;

            let addedTenants = new Set();

            agreements.forEach(agreement => {
                // Check if this agreement has the selected unit
                let hasUnit = agreement.agreement_units?.some(
                    au => au.contract_unit_details_id == unit_id
                );

                if (hasUnit && agreement.tenant) {
                    if (!addedTenants.has(agreement.tenant.id)) {
                        addedTenants.add(agreement.tenant.id);

                        tenantSelect.append(
                            `<option value="${agreement.tenant.id}">
                        ${agreement.tenant.tenant_name}
                    </option>`
                        );
                    }
                }
            });

            tenantSelect.trigger('change');
        }



        $('input[name="companyFileter"]').on('change', function() {
            table.ajax.reload();
        });
        $(document).on('click', '.reset', function() {
            $('.fileterform').trigger('reset');

            $('#propertySelect').val(null).trigger('change');
            $('#unitSelect').val(null).trigger('change');
            $('#modeSelect').val(null).trigger('change');

            $('#dateFrom input').val('');
            $('#dateTo input').val('');
            $('#tenantSelect').val(null).trigger('change');
            showLoader();

            setTimeout(() => {
                window.location.reload();
            }, 1000);
        });
    </script>
    <script>
        $('#clearBtn').on('click', function(e) {
            const formContainer = document.querySelector('#modal-single-clear');
            if (!validateformContainer(formContainer)) {

                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'warning',
                    title: "Please fill all required fields correctly!",
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
                return;
            }
            let paymentIds = [];

            // Loop through checked checkboxes
            $('#tenantChequeTable tbody input.groupCheckbox:checked').each(function() {
                paymentIds.push($(this).val());
            });

            // console.log(paymentIds);


            const form = document.getElementById('receivableClearForm');

            // Create FormData object
            let formData = new FormData(form);
            // let formData = $('#receivableClearForm').serialize();
            //
            //Append payment_detail_ids[]
            paymentIds.forEach(function(id) {
                formData.append('payment_detail_ids[]', id);
            });

            // CSRF (FormData already includes _token if inside form,
            // but keeping this is safe)
            formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

            $.ajax({
                url: "{{ route('receivable.cheque.clear.submit') }}",
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,

                beforeSend: function() {
                    $('#clearBtn').prop('disabled', true).text('Processing...');
                },
                success: function(response) {
                    $('#clearBtn').prop('disabled', false).text('Clear');
                    if (response.success) {
                        $('#modal-single-clear').modal('hide');
                        toastr.success(response.message);
                        location.reload();
                        // Optionally reload table/list
                        // location.reload();
                    } else {
                        // alert(response.message || 'Something went wrong!');
                        toastr.error(response.message || 'Something went wrong!');
                    }
                },
                error: function(xhr) {
                    $('#clearBtn').prop('disabled', false).text('Clear');
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        let messages = [];
                        $.each(errors, function(key, val) {
                            messages.push(val[0]);
                        });
                        // alert(messages.join("\n"));
                        toastr.error(message);
                    } else {
                        // alert('Error: ' + xhr.statusText);
                        toastr.error(xhr.statusText);
                    }
                }
            });
        });



        function validateformContainer(formContainer) {
            let isValid = true;

            // Select all required fields except radio buttons
            formContainer.querySelectorAll('[required]:not([type="radio"])').forEach(field => {
                // Skip hidden fields
                if (field.offsetParent === null) return;

                // Use browser validation API
                if (!field.checkValidity()) {
                    field.classList.add('is-invalid');
                    field.classList.remove('is-valid');
                    isValid = false;
                } else {
                    field.classList.remove('is-invalid');
                    field.classList.add('is-valid');
                }
            });

            return isValid;
        }
        $('.cheque-bounce').on('click', function() {
            let checkedCount = $('#tenantChequeTable tbody input.groupCheckbox:checked').length;
            if (checkedCount === 0) {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'warning',
                    title: "Please select at least one record to clear.",
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
                return;
            } else {
                $('#modal-bounced-cheque').modal('show');
            }
        });
        $(document).on('click', '#bouncedClearBtn', function() {

            const bounceformContainer = document.querySelector('#modal-bounced-cheque');
            if (!validateformContainer(bounceformContainer)) {

                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'warning',
                    title: "Please fill all required fields correctly!",
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
                return;
            }

            const form = document.getElementById('bouncedChequeForm');
            let formData = new FormData(form);

            let paymentIds = [];
            $('#tenantChequeTable tbody input.groupCheckbox:checked').each(function() {
                paymentIds.push($(this).val());
            });

            paymentIds.forEach(function(id) {
                formData.append('payment_detail_ids[]', id);
            });

            $.ajax({
                url: "{{ route('receivable.cheque.bounce.submit') }}",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {
                    $('#bouncedClearBtn').prop('disabled', true).text('Saving...');
                },
                success: function(response) {

                    // Swal.fire({
                    //     toast: true,
                    //     position: 'top-end',
                    //     icon: 'success',
                    //     title: response.message || 'Bounced cheque saved successfully',
                    //     showConfirmButton: false,
                    //     timer: 3000,
                    //     timerProgressBar: true
                    // });
                    toastr.success(response.message);

                    $('#modal-bounced-cheque').modal('hide');
                    form[0].reset();


                    // Optional: reload DataTable
                    $('#tenantChequeTable').DataTable().ajax.reload(null, false);
                },
                error: function(xhr) {
                    // console.log(xhr);
                    let msg = 'Something went wrong';

                    if (xhr.status === 422) {
                        msg = Object.values(xhr.responseJSON.errors).join('<br>');
                        toastr.error(msg);
                    } else {
                        toastr.error(xhr.statusText);
                    }

                    // Swal.fire({
                    //     icon: 'error',
                    //     title: 'Error',
                    //     html: msg
                    // });
                },
                complete: function() {
                    $('#bouncedClearBtn').prop('disabled', false).text('Save');
                }
            });
        });
        $(document).on('click', '.bouncedInfoBtn', function() {
            let reason = $(this).data('reason');
            let date = $(this).data('date');
            // console.log(reason, date);

            $('#bouncedReasonText').text(reason);

            if (date && date !== '-') {
                $('#bouncedDateText').text(date);
                $('#bouncedDateBox').removeClass('d-none');
            } else {
                $('#bouncedDateBox').addClass('d-none');
            }

            $('#bouncedChequeModal').modal('show');
        });
    </script>
    <script>
        function addClassAsterisk(inputSelector) {
            // alert("test");
            $(inputSelector).prev('label').addClass('asterisk');
        }
    </script>
    <script>
        $('#company_id').on('change keyup', function() {
            filterBanksByCompany();
        });

        function filterBanksByCompany() {
            let companyId = $('#company_id').val();
            let bankSelect = $('#bank_id');
            console.log(bankSelect);

            bankSelect.empty();
            bankSelect.append('<option value="">Select Bank</option>');

            if (!companyId) {
                return;
            }

            let filteredBanks = banks.filter(bank => bank.company_id == companyId);

            filteredBanks.forEach(bank => {
                bankSelect.append(
                    `<option value="${bank.id}">${bank.bank_name}</option>`
                );
            });
        }
    </script>
@endsection
