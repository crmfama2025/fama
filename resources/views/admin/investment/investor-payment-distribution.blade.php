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
                            <div class="card-body">
                                <div class="card card-info card-outline">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Investor payout allocation</h5>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">

                                        <div class="form-group row m-4">
                                            <div class="col-md-2">
                                                <label for="inputPassword3">Month</label>
                                                <select class="form-control select2" name="fifo_month" id="fifo_month">
                                                    <option value="">Select Month</option>
                                                    <?php for ($m = 1; $m <= 12; ++$m) { ?>
                                                    <option value="{{ $m }}">
                                                        <?= date('F', mktime(0, 0, 0, $m, 1)) ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label for="inputPassword3">Year</label>
                                                <select class="form-control select2" name="fifo_year" id="fifo_year">
                                                    <option value="">Select Year</option>
                                                    <?php for ($y = 2022; $y <= date('Y') + 2; ++$y) { ?>
                                                    <option value="{{ $y }}">{{ $y }}</option>
                                                    <?php } ?>
                                                </select>
                                            </div>

                                            <div class="col-md-2">
                                                <label for="companySelectAllocation">Company</label>
                                                <select class="form-control select2" name="fifo_company_id"
                                                    id="fifo_company_id">
                                                    <option value="">Select Company</option>

                                                    @foreach ($investedCompanies as $company)
                                                        <option value="{{ $company->id }}">
                                                            {{ $company->company_name }}
                                                        </option>
                                                    @endforeach
                                                </select>

                                            </div>

                                            <div class="col-md-3">
                                                <label for="inputPassword3">Investor</label>
                                                <select class="form-control select2" name="fifo_investor_id"
                                                    id="fifo_investor_id">
                                                    <option value="">Select Investor</option>
                                                    @foreach ($investors as $investor)
                                                        <option value="{{ $investor->id }}">
                                                            {{ $investor->investor_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label for="inputPassword3">Investor</label>
                                                <input type="number" step="any" id="payment_amount_total"
                                                    class="form-control" placeholder="Enter amount to pay">
                                            </div>
                                            <div class="col-md-1 float-right">
                                                <button type="button" id="runInvestorPayoutAllocationBtn"
                                                    class="btn btn-primary mt-31">
                                                    <i class="fas fa-play mr-1"></i> Run
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mx-2 " id="payoutAllocationCard" style="display:none;">
                                        <div class="alert alert-success py-2 mb-2" id="in_covered_summary"
                                            style="display:none;">
                                            <div><strong>Total Outstanding:</strong> <span id="in_total">AED 0.00</span>
                                            </div>
                                            <div><strong>Total Covered Amount:</strong> <span id="in_total_covered">AED
                                                    0.00</span></div>
                                        </div>

                                        <!-- Allocation rows -->
                                        <div id="in_rows"></div>

                                        <!-- Banner -->
                                        <div id="in_banner" class="alert mt-2 py-2" style="display:none;font-size:13px;">
                                        </div>

                                        <div class="form-group col-md-2 float-right">
                                            <button type="button" id="in_submit_btn" class="btn btn-success btn-block"
                                                disabled>
                                                <i class="fa fa-check mr-1"></i> Submit Payment
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card">

                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="card card-info card-outline">
                                <!-- /.card-header -->
                                <!-- form start -->
                                <form class="form-horizontal">
                                    <div class="form-group row m-4">
                                        <div class="col-md-2">
                                            <label for="inputPassword3">Month</label>
                                            <select class="form-control select2" name="month" id="month">
                                                <option value="">Select Month</option>
                                                <?php for ($m = 1; $m <= 12; ++$m) { ?>
                                                <option value="{{ $m }}">
                                                    <?= date('F', mktime(0, 0, 0, $m, 1)) ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label for="inputPassword3">Year</label>
                                            <select class="form-control select2" name="year" id="year">
                                                <option value="">Select Year</option>
                                                <?php for ($y = 2022; $y <= date('Y') + 2; ++$y) { ?>
                                                <option value="{{ $y }}">{{ $y }}</option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
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
                                        <div class="col-md-2">
                                            <label for="company_id">Invested Company</label>

                                            <select class="form-control select2" name="company_id" id="company_id">
                                                <option value="">Select Company</option>

                                                @foreach ($investedCompanies as $company)
                                                    <option value="{{ $company->id }}">
                                                        {{ $company->company_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>


                                        <div class="col-md-1 float-right">
                                            <button type="button" class="btn btn-info searchbtnchq">Search</button>
                                        </div>
                                </form>
                            </div>
                            <!-- /.card -->

                            <div class="card">
                                <div class="card-header">
                                    <!-- <h3 class="card-title">Property Details</h3> -->
                                    <span class="float-right">
                                        <!-- <button class="btn btn-info float-right m-1" data-toggle="modal"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        data-target="#modal-Property">Add Investor Payout</button> -->

                                        <button class="btn btn-success float-right m-1 bulktriggerbtn" data-toggle="modal"
                                            data-target="#modal-payout" data-clear-type="bulk">Payout All</button>


                                        <a href="{{ route('distributed.report') }}" class="btn btn-outline-maroon m-1"><i
                                                class="fas fa-book"></i>
                                            View Report</a>
                                        <button class="btn btn-secondary float-right m-1 reinvestAllBtn"
                                            data-toggle="modal" data-target="#modal-payout" data-clear-type="bulk"
                                            data-reinvest="1">
                                            <i class="fas fa-redo"></i> Reinvest All</button>
                                    </span>
                                </div>
                                <div class="card-body">
                                    <div id="selectionInfo" class="alert alert-info d-none mb-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong id="selectedCount">0</strong> Payouts Selected
                                            </div>
                                            <div>
                                                Total Payout Amount:
                                                <strong id="selectedTotal">0.00</strong>
                                            </div>
                                        </div>
                                    </div>
                                    <table id="payoutPendingTable" class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>
                                                    <div class="icheck-primary d-inline">
                                                        <input type="checkbox" name="selectall" id="selectAll"
                                                            value="1" onclick="toggleAllCheckboxes()">
                                                        <label for="selectAll">Select All
                                                        </label>
                                                    </div>
                                                </th>
                                                <th>Investor Name</th>
                                                <th>Company Name</th>
                                                <th>Investment Code</th>
                                                <th>Payout Date</th>
                                                <th>Payout Type</th>
                                                <th>payout Amount</th>
                                                <th width="188">Payment Mode</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {{-- <tr>
                                                    <td>
                                                        <div class="icheck-primary d-inline">
                                                            <input type="checkbox" id="ichek1" class="groupCheckbox"
                                                                name="installment_id[]">
                                                            <label for="ichek1">
                                                            </label>
                                                        </div>
                                                    </td>
                                                    <td>Investor</td>
                                                    <td>AED 1000</td>
                                                    <td>08/08/2024</td>
                                                    <td>AED 1000</td>
                                                    <td>AED 200</td>
                                                    <td>AED 1200</td>
                                                    <td>Bank Transfer</td>
                                                    <td>Fama</td>
                                                    <td>
                                                        <button class="btn btn-info float-right m-1 singleClear"
                                                            data-toggle="modal" data-target="#modal-payout"
                                                            data-clear-type="single">Payout</button> </td>
                                                </tr> --}}
                                        </tbody>
                                    </table>
                                </div>
                                <!-- /.card-body -->
                            </div>
                        </div>
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
    </div>
    <!-- /.container-fluid -->


    <div class="modal fade" id="modal-payout">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-info">
                    <h4 class="modal-title">Investor Payouts</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" id="PayoutSubmitForm" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="method" id="method">
                    <input type="hidden" name="payoutId" id="payoutId">
                    <input type="hidden" name="reinvest" id="reinvest">
                    <div class="modal-body">
                        <div class="card-body">
                            <div class="form-group row">
                                <label for="exampleInputEmail1">Payout Date</label>
                                <div class="input-group date" id="investmentdate" data-target-input="nearest">
                                    <input type="text" class="form-control datetimepicker-input" name="paid_date"
                                        data-target="#investmentdate" placeholder="dd-mm-YYYY" required />
                                    <div class="input-group-append" data-target="#investmentdate"
                                        data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row clrngamnt">
                                <label for="exampleInputEmail1">Payout Amount</label>
                                <input type="number" class="form-control" name="paid_amount" id="paid_amount"
                                    placeholder="Clearing Amount" required min="0" step="1">
                                <span id="amountPending" class="text-danger text-sm"></span>
                            </div>
                            <div class="form-group row">
                                @foreach ($paymentmodes as $paymentmode)
                                    @php
                                        $class = $subclass = '';
                                        if ($paymentmode->id == 2) {
                                            $class = 'bank';
                                        } elseif ($paymentmode->id == 3) {
                                            $class = 'chq';
                                        }
                                    @endphp
                                    <div class="icheck-primary {{ $class }} mr-1">
                                        <input type="checkbox" id="{{ $paymentmode->payment_mode_code }}"
                                            class="singleClear" value="{{ $paymentmode->id ?? '' }}" name="paid_mode">
                                        <label
                                            for="{{ $paymentmode->payment_mode_code }}">{{ $paymentmode->payment_mode_name ?? '' }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            <div class="form-group companySingle">
                                <label for="exampleInputEmail1">Company Name</label>
                                <select class="form-control select2" name="paid_company_id" id="paid_company" required>
                                    <option value="">Select Company</option>
                                    @foreach ($companies as $company)
                                        <option value="{{ $company->id }}">{{ $company->company_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group banksingle">
                                <label for="exampleInputEmail1">Bank Name</label>
                                <select class="form-control select2 bank_name" name="paid_bank" id="bank_name" required>
                                    <option value="">Select Bank</option>
                                    {{-- @foreach ($banks as $bank)
                                                <option value="{{ $bank->id }}">{{ $bank->bank_name }} </option>
                                            @endforeach --}}
                                </select>
                            </div>

                            <div class="form-group cheque">
                                <label for="exampleInputEmail1">Cheque No</label>
                                <input type="text" class="form-control cheque_no" id="cheque_no"
                                    name="paid_cheque_number" placeholder="Cheque No" required>
                            </div>

                            <div class="form-group modechange">
                                <label for="exampleInputEmail1">Remarks</label>
                                <textarea name="payment_remarks" id="" cols="10" rows="5" class="form-control"></textarea>
                            </div>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="button" id="submitBtn" class="btn btn-info">submit</button>
                    </div>
                </form>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

    <div class="modal fade" id="modal-in-confirm" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Confirm Payment</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <!-- Summary read-only -->
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted" style="width:40%">Investor</td>
                            <td><strong id="conf_tenant">—</strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted" style="width:40%">Company</td>
                            <td><strong id="conf_company">—</strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Amount</td>
                            <td><strong id="conf_amount">—</strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Payouts covered</td>
                            <td><strong id="conf_count">—</strong></td>
                        </tr>
                    </table>

                    <hr>

                    <!-- Clearing date -->
                    <div class="form-group">
                        <label class="asterisk">Payout Date</label>
                        <div class="input-group date" id="in_clearingDate" data-target-input="nearest">
                            <input type="text" name="paid_date" class="form-control datetimepicker-input"
                                id="in_clearing_date_input" data-target="#in_clearingDate" placeholder="dd-mm-YYYY"
                                required />
                            <div class="input-group-append" data-target="#in_clearingDate" data-toggle="datetimepicker">
                                <span class="input-group-text">
                                    <i class="fa fa-calendar"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    {{-- <input type="hidden" name="allocated_amount_total" id="payment_amount_total" value="0"> --}}

                    <div class="form-group row">
                        @foreach ($paymentmodes as $paymentmode)
                            @php
                                $class = $subclass = '';
                                if ($paymentmode->id == 2) {
                                    $class = 'in_bank';
                                } elseif ($paymentmode->id == 3) {
                                    $class = 'in_chq';
                                }
                            @endphp
                            <div class="icheck-primary {{ $class }} mr-1">
                                <input type="checkbox" id="in_{{ $paymentmode->payment_mode_code }}"
                                    class="in_payment_mode" value="{{ $paymentmode->id ?? '' }}" name="paid_mode">
                                <label
                                    for="in_{{ $paymentmode->payment_mode_code }}">{{ $paymentmode->payment_mode_name ?? '' }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                    <div class="form-group in_companySingle">
                        <label for="exampleInputEmail1">Company Name</label>
                        <select class="form-control select2" name="paid_company_id" id="in_paid_company" required>
                            <option value="">Select Company</option>
                            @foreach ($companies as $company)
                                <option value="{{ $company->id }}">{{ $company->company_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group in_banksingle">
                        <label for="exampleInputEmail1">Bank Name</label>
                        <select class="form-control select2 bank_name" name="paid_bank" id="in_bank_name" required>
                            <option value="">Select Bank</option>
                            {{-- @foreach ($banks as $bank)
                                                <option value="{{ $bank->id }}">{{ $bank->bank_name }} </option>
                                            @endforeach --}}
                        </select>
                    </div>

                    <div class="form-group in_cheque">
                        <label for="exampleInputEmail1">Cheque No</label>
                        <input type="text" class="form-control cheque_no" id="in_cheque_no" name="paid_cheque_number"
                            placeholder="Cheque No" required>
                    </div>

                    <div class="form-group in_modechange">
                        <label for="exampleInputEmail1">Remarks</label>
                        <textarea name="in_payment_remarks" id="in_payment_remarks" cols="10" rows="5" class="form-control"></textarea>
                    </div>

                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" id="in_submitBtn" class="btn btn-success">
                        <i class="fa fa-check mr-1"></i> Confirm & Clear
                    </button>
                </div>
            </div>
        </div>
    </div>
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
        $(function() {
            $("#example1").DataTable({
                "responsive": true,
                "lengthChange": false,
                "autoWidth": false,
                "buttons": ["excel"]
            }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

        });

        $('#investmentdate').datetimepicker({
            format: 'DD-MM-YYYY'
        });

        $('#in_clearingDate').datetimepicker({
            format: 'DD-MM-YYYY'
        });

        let selected = {};
        let allFilteredCount = null;

        const SELECT_ALL_URL = "{{ route('payout.pending.select_all') }}";

        function currentFilters() {
            return {
                month: $('#month').val(),
                year: $('#year').val(),
                batch_id: $('#batch_id').val(),
                investor_id: $('#investor_id').val(),
                company_id: $('#company_id').val()
            };
        }

        function selectedIds() {
            return Object.keys(selected);
        }

        function selectedCompanyIds() {
            return [...new Set(Object.values(selected).map(s => String(s.company_id)))];
        }

        function getSelectedTotal() {
            let cents = 0;
            Object.values(selected).forEach(s => {
                cents += Math.round((parseFloat(s.amount) || 0) * 100);
            });
            return cents / 100;
        }

        // function updateSelectionInfo() {
        //     const n = selectedIds().length;
        //     $('#selectionInfo').text(n ? n + ' selected | Total: ' + getSelectedTotal().toFixed(2) : '');
        //     $('#selectAll').prop('checked', allFilteredCount !== null && n === allFilteredCount && n > 0);
        // }
        function updateSelectionInfo() {
            const count = selectedIds().length;
            const total = getSelectedTotal();

            if (count > 0) {

                $('#selectionInfo').removeClass('d-none');

                $('#selectedCount').text(count);

                $('#selectedTotal').text(
                    total.toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    })
                );
            } else {
                $('#selectionInfo').addClass('d-none');
                $('#selectedCount').text(0);
                $('#selectedTotal').text('0.00');
            }
            $('#selectAll').prop(
                'checked',
                allFilteredCount !== null &&
                count === allFilteredCount &&
                count > 0
            );
        }

        function clearSelection() {
            selected = {};
            allFilteredCount = null;
            $('.groupCheckbox').prop('checked', false);
            updateSelectionInfo();
        }

        $(document).on('change', '.groupCheckbox', function() {
            const id = String($(this).val());
            const companyId = String($(this).data('company-id'));

            if (this.checked) {
                selected[id] = {
                    amount: parseFloat($(this).data('amount')) || 0,
                    company_id: companyId,
                    investor_id: String($(this).data('investor-id')),
                    payout_type: parseInt($(this).data('payout-type'))
                };
            } else {
                delete selected[id];
            }
            updateSelectionInfo();
        });


        $(document).on('change', '#selectAll', function() {
            const el = this;

            if (!el.checked) {
                clearSelection();
                return;
            }

            showLoader();
            $.ajax({
                url: SELECT_ALL_URL,
                type: 'GET',
                data: currentFilters(),
                dataType: 'json'
            }).done(function(res) {
                hideLoader();

                if (!res.count) {
                    el.checked = false;
                    toastr.error('No records found for the selected filters.');
                    return;
                }



                selected = {};
                res.items.forEach(function(i) {
                    selected[String(i.id)] = {
                        amount: parseFloat(i.amount) || 0,
                        company_id: String(i.company_id),
                        investor_id: String(i.investor_id),
                        payout_type: parseInt(i.payout_type)
                    };
                });
                allFilteredCount = res.count;
                $('.groupCheckbox').prop('checked', true);
                updateSelectionInfo();
            }).fail(function(xhr) {
                hideLoader();
                el.checked = false;
                toastr.error(xhr.responseJSON?.message || 'Unable to select records.');
            });
        });


        function toggleAllCheckboxes() {
            document.getElementById('selectAll').addEventListener('change', function() {
                const itemCheckboxes = document.querySelectorAll('.groupCheckbox');
                itemCheckboxes.forEach(checkbox => {
                    checkbox.checked = this
                        .checked;
                });
            });
        }

        let allBanks = @json($banks);

        $(document).on('change', '#paid_company', function() {
            CompanyChange($(this));
        });

        function CompanyChange(ele) {
            const companyId = $(ele).val();
            const companyName = $(ele).find('option:selected').text().trim();

            let options = '<option value="">Select Bank</option>';
            allBanks
                .filter(b => b.company_id == companyId)
                .forEach(b => {
                    // const selected = (b.id == bendorVal) ? 'selected' : '';
                    options +=
                        `<option value="${b.id}">${b.bank_name}</option>`;
                });
            $('#bank_name').html(options).trigger('change');
        }

        $(document).ready(function() {
            // $('#PayableList').DataTable();
            hidelemnetsonload();
        });

        function hidelemnetsonload() {
            $('.banksingle, .companySingle, .cheque, .modechange').hide();
        }

        function applyPaymentMode(modeId) {
            $('.singleClear').not('[value="' + modeId + '"]').prop('checked', false);
            $('.singleClear[value="' + modeId + '"]').prop('checked', true).trigger('change');
        }

        $('.singleClear').on('change', function() {
            $('.singleClear').not(this).prop('checked', false);
            if ($(this).prop('checked')) {
                $('.modechange').show();
                if ($(this).val() == 2) {
                    $('.banksingle').show();
                    $('.companySingle').show();
                    $('.cheque').hide();
                } else if ($(this).val() == 3) {
                    $('.banksingle').hide();
                    $('.companySingle').hide();
                    $('.cheque').show();
                } else {
                    $('.banksingle').hide();
                    $('.companySingle').hide();
                    $('.cheque').hide();
                }
            } else {
                $('.banksingle').hide();
                $('.companySingle').hide();
                $('.cheque').hide();
                $('.modechange').hide();
            }
        });

        $('.bulktriggerbtn').click(function(e) {
            e.preventDefault();

            if (selectedIds().length === 0) {
                toastr.error('Please select one or more items to continue.');
                return false;
            }
            if (selectedCompanyIds().length > 1) {
                toastr.error('You cannot select payouts of different companies.');
                return false;
            }
        });
        $('.reinvestAllBtn').click(function(e) {
            if (selectedIds().length === 0) {
                toastr.error('Please select one or more items to continue.');
                return false;
            }
            const vals = Object.values(selected);
            if (new Set(vals.map(s => s.investor_id)).size > 1) {
                toastr.error('Reinvest All works only for payouts of the same investor.');
                return false;
            }
            if (vals.some(s => s.payout_type !== 1)) {
                toastr.error('Only Profit payouts can be reinvested.');
                return false;
            }
        });

        $('#modal-payout').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var clearType = button.data('clear-type');

            $('#PayoutSubmitForm')[0].reset();
            hidelemnetsonload();
            $(this).find('input, select, textarea').removeClass('is-invalid is-valid');
            $('#method').val(clearType);

            if (clearType === 'bulk') {
                const total = getSelectedTotal();
                const count = selectedIds().length;
                const isReinvest = button.data('reinvest') == 1;

                $('#paid_amount').off('input.payoutModal')
                    .val(total.toFixed(2))
                    .attr('readonly', true);
                $('#amountPending').text('');
                $('#submitBtn').text(isReinvest ? 'Re-Invest All' : 'Submit');
                $('#reinvest').val(isReinvest ? 1 : '');
                $('#paid_company').prop('disabled', false);

                $('.clrngamnt').show();
                $('.chq').hide().css('display', 'none');
            } else {
                $('#amountLabel').text('Payout Amount');
                $('.chq').css('display', '');
                $('#payoutId').val(button.data('det-id'));

                let reinvest = button.data('reinvest');
                $('#reinvest').val(reinvest);

                let totalAmount = parseFloat(button.data('amount')) || 0;
                $('#paid_amount').off('input.payoutModal').val(totalAmount.toFixed(2));

                if (reinvest) {
                    $('#submitBtn').text('Re-Invest');
                    $('#paid_amount').off('input.payoutModal').val(totalAmount).attr('readonly', true);
                    $('#amountPending').text('');
                } else {
                    // $('#paid_amount').off('input.payoutModal').on('input.payoutModal', function() {
                    //     let paid = parseFloat(this.value) || 0;
                    //     if (paid > totalAmount) {
                    //         paid = totalAmount;
                    //         this.value = totalAmount;
                    //     }
                    //     let remaining = totalAmount - paid;
                    //     $('#amountPending').text('Remaining Amount: ' + remaining.toFixed(2));
                    // });

                    $('#submitBtn').text('Submit');
                    $('#paid_amount').attr('readonly', false);
                    // $('#amountPending').text('Remaining Amount: ' + totalAmount);
                    $('#amountPending').text('Remaining Amount: 0.00');

                    $('#paid_amount').on('input.payoutModal', function() {
                        let paid = parseFloat(this.value) || 0;
                        if (paid > totalAmount) {
                            paid = totalAmount;
                            this.value = totalAmount.toFixed(2);
                        }
                        $('#amountPending').text('Remaining Amount: ' + (totalAmount - paid).toFixed(2));
                    });

                    const modeId = button.attr('data-mode-id');
                    const companyId = button.attr('data-company-id');
                    const bankId = button.attr('data-bank-id');
                    const chequeNo = button.attr('data-cheque-no');

                    if (modeId) {
                        applyPaymentMode(modeId);
                    }
                    if (companyId) {
                        $('#paid_company').val(companyId).trigger('change').prop('disabled', true);
                    }
                    if (bankId) {
                        $('#bank_name').val(bankId).trigger('change');
                    }
                    $('#cheque_no').val(chequeNo || '');
                }

                $('.clrngamnt').show();
            }
        });
    </script>

    <script>
        $(function() {
            let table = $('#payoutPendingTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,

                ajax: {
                    url: "{{ route('payout.pending.list') }}",
                    data: function(d) {
                        d.month = $('#month').val();
                        d.year = $('#year').val();
                        d.batch_id = $('#batch_id').val();
                        d.investor_id = $('#investor_id').val();
                        d.company_id = $('#company_id').val();
                    }
                },
                columns: [{
                        data: 'checkbox',
                        name: 'checkbox',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'investor_name',
                        name: 'investor_name'
                    },
                    {
                        data: 'company_name',
                        name: 'company_name'
                    },
                    {
                        data: 'investment_code',
                        name: 'investment_code'
                    },
                    {
                        data: 'payout_date',
                        name: 'payout_date',
                        searchable: false
                    },
                    {
                        data: 'payout_type',
                        name: 'payout_type'
                    },
                    {
                        data: 'payout_amount',
                        name: 'payout_amount'
                    },
                    {
                        data: 'payment_mode',
                        name: 'payment_mode',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
                rowCallback: function(row, data) {
                    if (data.has_returned === 1) {
                        $(row).css('background-color', '#ffe1e1');
                    }
                },
                drawCallback: function() {
                    $('.groupCheckbox').each(function() {
                        if (selected[String($(this).val())]) {
                            this.checked = true;
                        }
                    });
                    updateSelectionInfo();
                },
                order: [
                    [4, 'asc']
                ],
                dom: 'Bfrtip',
                buttons: [{
                    extend: 'excelHtml5',
                    text: 'Export Excel',
                    title: 'Contract Data',
                    action: function(e, dt) {
                        let params = dt.ajax.params();
                        params.month = $('#month').val();
                        params.year = $('#year').val();
                        params.batch_id = $('#batch_id').val();
                        params.investor_id = $('#investor_id').val();
                        params.company_id = $('#company_id').val();
                        params.search = dt.search();

                        window.location.href = "{{ route('payout.pending.export') }}?" + $
                            .param(params);
                    }
                }]
            });

            $('.searchbtnchq').on('click', function(e) {
                e.preventDefault();
                clearSelection(); // filters changed, so the old selection is no longer valid
                table.ajax.reload();
            });
        });
    </script>


    <script>
        $('#submitBtn').click(function(e) {
            e.preventDefault();

            let isValid = true;
            $(".error-text").remove(); // clear old errors

            // validate ALL required fields
            $("#PayoutSubmitForm").find("[required]:visible").each(function() {
                const value = $(this).val()?.trim();

                if (!value) {
                    isValid = false;
                    setInvalid(this, "This field is required");
                } else {
                    setValid(this);
                }
            });

            // Validate Select2 fields
            $("#PayoutSubmitForm").find('[required]select.select2').each(function() {
                const value = $(this).val();
                const container = $(this).next('.select2-container');

                // Skip validation if hidden (either the select or its container)
                if (!$(this).is(':visible') || container.css('display') === 'none') {
                    container.removeClass('is-invalid is-valid');
                    return; // skip hidden selects
                }


                if (!value || value.length === 0) {
                    container.addClass('is-invalid').removeClass('is-valid');
                    isValid = false;
                } else {
                    container.addClass('is-valid').removeClass('is-invalid');
                }
            });



            if (!isValid) return;

            submitForm(); // everything passed


        });

        // helper: invalid
        function setInvalid(input, message) {
            $(input).addClass("is-invalid").removeClass("is-valid");

            // // append error message
            // if ($(input).parent().next(".invalid-feedback").length === 0) {
            //     $(input).parent().after(
            //         `<div class="invalid-feedback" style="display:block;">${message}</div>`
            //     );
            // }
        }

        // helper: valid
        function setValid(input) {
            $(input).addClass("is-valid").removeClass("is-invalid");
        }

        function showError(input, message) {
            $(input).addClass("is-invalid").removeClass("is-valid");

            // append error message
            $(input).after(
                `<div class="invalid-feedback" style="display:block;">${message}</div>`
            );
        }

        function submitForm() {
            var form = document.getElementById('PayoutSubmitForm');
            var fdata = new FormData(form);

            fdata.append('_token', $('meta[name="csrf-token"]').attr('content'));

            var selectedValues = [];

            if ($('#method').val() == 'single') {
                selectedValues.push($('#payoutId').val());
            } else {
                selectedValues = selectedIds(); // all selected, including other pages
            }

            fdata.append('payout_ids', selectedValues);

            showLoader();

            $.ajax({
                type: "POST",
                url: "{{ route('payout.distribute.save') }}",
                data: fdata,
                dataType: "json",
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#modal-payout').modal('hide');
                    hideLoader();
                    toastr.success(response.message);

                    // if ($('#reinvest').val() == 1) {
                    //     let params = {
                    //         reinvestment: $('#reinvest').val(),
                    //         parent_id: response.data[0].investment_id,
                    //         investor_id: response.data[0].investor_id,
                    //         amount: response.data[0].amount_paid,
                    //         date: response.data[0].paid_date
                    //     };
                    //     window.location.href = "{{ route('investment.create') }}?" + $.param(params);
                    // }
                    if ($('#reinvest').val() == 1) {
                        let totalPaid = 0;
                        response.data.forEach(r => totalPaid += parseFloat(r.amount_paid) || 0);

                        let params = {
                            reinvestment: 1,
                            parent_id: response.data[0].investment_id,
                            investor_id: response.data[0].investor_id,
                            amount: totalPaid.toFixed(2),
                            date: response.data[0].paid_date
                        };
                        window.location.href = "{{ route('investment.create') }}?" + $.param(params);
                    } else {
                        clearSelection();
                        $('#payoutPendingTable').DataTable().ajax.reload();
                    }
                },
                error: function(errors) {
                    hideLoader();
                    toastr.error(errors.responseJSON?.message || 'Something went wrong.');
                }
            });
        }
    </script>
    @include('admin.investment.investor-payout-bulkjs');
@endsection
