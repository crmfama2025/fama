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
                        <h1>Payable Clearing</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="../dashboard.php">Home</a></li>
                            <li class="breadcrumb-item active">Payable Clearing</li>
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

                                <div class="card card-info">
                                    <!-- /.card-header -->
                                    <!-- form start -->
                                    <form class="form-horizontal" id="filterForm">
                                        <div class="form-group row m-4">
                                            <div class="col-md-2">
                                                <label for="exampleInputEmail1">From</label>
                                                <div class="input-group date" id="dateFrom" data-target-input="nearest">
                                                    <input type="text" class="form-control datetimepicker-input"
                                                        data-target="#dateFrom" id="date_From" placeholder="dd-mm-YYYY" />
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
                                                        data-target="#dateTo" id="date_To" placeholder="dd-mm-YYYY" />
                                                    <div class="input-group-append" data-target="#dateTo"
                                                        data-toggle="datetimepicker">
                                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- <div class="col-md-2 vendorselect">
                                                <label for="inputPassword3">Company</label>
                                                <select class="form-control select2" name="area_id">
                                                    <option value="">Select Company</option>
                                                    <option value="1">Company 1</option>
                                                </select>
                                            </div> --}}

                                            <div class="col-md-2 vendorselect">
                                                <label for="inputPassword3">Vendor</label>
                                                <select class="form-control select2" id="vendor_id">
                                                    <option value="">Select Vendor</option>
                                                    @foreach ($vendors as $vendor)
                                                        <option value="{{ $vendor->id }}">{{ $vendor->vendor_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col-md-2 propertyselect">
                                                <label for="inputPassword3">Property</label>
                                                <select class="form-control select2" id="property_id">
                                                    <option value="">Select Property</option>
                                                    @foreach ($properties as $property)
                                                        <option value="{{ $property->id }}">{{ $property->property_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col-md-2 propertyselect">
                                                <label for="inputPassword3">Payment mode</label>
                                                <select class="form-control select2" id="payment_mode">
                                                    <option value="">Select Payment mode</option>
                                                    @foreach ($paymentmodes as $paymentmode)
                                                        <option value="{{ $paymentmode->id }}">
                                                            {{ $paymentmode->payment_mode_name }}</option>
                                                    @endforeach
                                                </select>
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
                                    <div class="card-header">
                                        <button type="button" class="btn btn-success float-right bulktriggerbtn"
                                            data-toggle="modal" data-target="#modal-clear-payable"
                                            data-clear-type="bulk">Clear All</button>

                                        <button class="btn btn-danger float-right mr-1" title="return" data-toggle="modal"
                                            data-target="#modal-return-cheque" id="returnBtn">Returns</button>

                                        <a href="{{ route('cleared.list') }}"
                                            class="btn btn-outline-maroon float-right mr-1"><i class="fas fa-book"></i>
                                            View
                                            Report</a>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex justify-content-center">
                                            <div class="mb-2" id="statusFilters">

                                                <button class="btn btn-info filter-btn" add-class="btn-info"
                                                    data-filter="">All</button>
                                                @foreach ($companies as $company)
                                                    <button class="btn btn-outline-info filter-btn companyFilterBtn"
                                                        add-class="btn-info"
                                                        data-filter="{{ $company->company_name }}">{{ $company->company_short_code }}</button>
                                                @endforeach
                                            </div>
                                        </div>
                                        <div class="table-responsive">
                                            <table id="PayableList" class="table table-striped projects">
                                                <thead>
                                                    <tr>
                                                        <th>
                                                            <div class="icheck-primary d-inline">
                                                                <input type="checkbox" name="selectall" id="selectAll"
                                                                    value="1" onclick="toggleAllCheckboxes()">
                                                                <label for="selectAll">All
                                                                </label>
                                                            </div>
                                                        </th>
                                                        <th>Project</th>
                                                        <th>Company Name</th>
                                                        <th>Vendor</th>
                                                        <th>Building</th>
                                                        <th>Due Date</th>
                                                        <th>Payment Mode</th>
                                                        {{-- <th>Ch.No</th> --}}
                                                        <th>Amount</th>
                                                        <th>Composition</th>
                                                        <th>Returned</th>
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
                                                    <td>PRJ00001</td>
                                                    <td>Vendor 1</td>
                                                    <td>Building name</td>
                                                    <td>01/08/2025</td>
                                                    <td>1234</td>
                                                    <td>100000.00</td>
                                                    <td>RENT 1/4</td>
                                                    <td>
                                                        <a class="btn btn-success  btn-sm" title="Clear cheque"
                                                            data-toggle="modal" data-target="#modal-clear-payable"
                                                            data-clear-type="single">Clear</a>
                                                        <a class="btn btn-danger btn-sm" title="return"
                                                            data-toggle="modal"
                                                            data-target="#modal-return-cheque">Return</a>
                                                    </td>
                                                </tr> --}}

                                                </tbody>
                                            </table>
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

            {{-- <div class="modal fade" id="modal-clear-payable">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">You Are Going To Pass This Cheque!</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="" id="ContractImportForm" method="POST" enctype="multipart/form-data">
                            <div class="modal-body">
                                <div class="card-body">
                                    <div class="form-group row">
                                        <label for="exampleInputEmail1">Clearing Date</label>
                                        <div class="input-group date" id="clearingdate" data-target-input="nearest">
                                            <input type="text" class="form-control datetimepicker-input"
                                                data-target="#clearingdate" placeholder="dd-mm-YYYY" />
                                            <div class="input-group-append" data-target="#clearingdate"
                                                data-toggle="datetimepicker">
                                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="icheck-primary d-inline">
                                            <input type="checkbox" id="bankBulk" class=""
                                                value="{{ $paymentmodes->where('id', 2)->first()->id ?? '' }}"
                                                name="payment_mode_id">
                                            <label
                                                for="bankBulk">{{ $paymentmodes->where('id', 2)->first()->payment_mode_name ?? '' }}
                                            </label>
                                        </div>
                                    </div>
                                    <div class="form-group bank">
                                        <label for="exampleInputEmail1">Bank Name</label>
                                        <select class="form-control select2 bank_name" name="bank_id" id="bank_name"
                                            required>
                                            <option value="">Select Bank</option>
                                            @foreach ($banks as $bank)
                                                <option value="{{ $bank->id }}">{{ $bank->bank_name }} </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <!-- /.card-body -->
                            </div>
                            <div class="modal-footer justify-content-between">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                <button type="button" id="clearBtn" class="btn btn-info">Clear</button>
                            </div>
                        </form>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div> --}}

            <div class="modal fade" id="modal-clear-payable">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-info">
                            <h4 class="modal-title">Payable Payments</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="" id="PaymentSubmitForm" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="method" id="method">
                            <input type="hidden" name="detId" id="detId">
                            <div class="modal-body">
                                <div class="card-body">
                                    <div class="form-group row">
                                        <label for="exampleInputEmail1">Clearing Date</label>
                                        <div class="input-group date" id="clearingdate" data-target-input="nearest">
                                            <input type="text" class="form-control datetimepicker-input"
                                                name="paid_date" data-target="#clearingdate" placeholder="dd-mm-YYYY"
                                                required />
                                            <div class="input-group-append" data-target="#clearingdate"
                                                data-toggle="datetimepicker">
                                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row clrngamnt">
                                        <label for="exampleInputEmail1">Clearing Amount</label>
                                        <input type="number" class="form-control" name="paid_amount" id="paid_amount"
                                            placeholder="Clearing Amount" required min="0" step="1">
                                        <span id="amountPending" class="text-danger text-sm"></span>
                                    </div>
                                    <div class="form-group row">
                                        <div class="icheck-primary bank">
                                            <input type="checkbox" id="bankcheque" class="singleClear"
                                                value="{{ $paymentmodes->where('id', 2)->first()->id ?? '' }}"
                                                name="paid_mode">
                                            <label
                                                for="bankcheque">{{ $paymentmodes->where('id', 2)->first()->payment_mode_name ?? '' }}
                                            </label>
                                        </div>

                                        <div class="icheck-primary chq">
                                            <input type="checkbox" id="radioPrimary2" class="singleClear"
                                                value="{{ $paymentmodes->where('id', 3)->first()->id ?? '' }}"
                                                name="paid_mode">
                                            <label
                                                for="radioPrimary2">{{ $paymentmodes->where('id', 3)->first()->payment_mode_name ?? '' }}
                                            </label>
                                        </div>
                                    </div>
                                    <div class="form-group banksingle">
                                        <label for="exampleInputEmail1">Bank Name</label>
                                        <select class="form-control select2 bank_name" name="paid_bank" id="bank_name"
                                            required>
                                            <option value="">Select Bank</option>
                                            @foreach ($banks as $bank)
                                                <option value="{{ $bank->id }}">{{ $bank->bank_name }} </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group row cheque">
                                        <label for="exampleInputEmail1">Cheque No</label>
                                        <input type="text" class="form-control cheque_no" id="cheque_no"
                                            name="paid_cheque_number" placeholder="Cheque No" required>
                                    </div>

                                    <div class="form-group row modechange">
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

            <div class="modal fade" id="modal-return-cheque">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">You Are Going To Return This Cheque!</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="" id="ReturnForm" method="POST" enctype="multipart/form-data">
                            <div class="modal-body">
                                <div class="card-body">
                                    <div class="form-group row">
                                        <label for="exampleInputEmail1">Returning Date</label>
                                        <div class="input-group date" id="returndate" data-target-input="nearest">
                                            <input type="text" class="form-control datetimepicker-input"
                                                name="returned_date" data-target="#returndate"
                                                placeholder="dd-mm-YYYY" />
                                            <div class="input-group-append" data-target="#returndate"
                                                data-toggle="datetimepicker">
                                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="exampleInputEmail1">Return Reason</label>
                                        <textarea name="returned_reason" id="" cols="10" rows="5" class="form-control"></textarea>
                                    </div>
                                </div>
                                <!-- /.card-body -->
                            </div>
                            <div class="modal-footer justify-content-between">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                <button type="button" id="clearBtn" class="btn btn-info"
                                    onclick="returnedCheques()">Return</button>
                            </div>
                        </form>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
            <!-- /.modal -->

            <div class="modal fade" id="reasonsModal">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Returned Reason</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body" style="max-height:400px; overflow-y:auto;">
                            <ul id="reason" class="list-group"></ul>

                        </div>
                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
            <!-- /.modal -->
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

        $(document).ready(function() {
            // $('#PayableList').DataTable();
            hidelemnetsonload();
        });

        function hidelemnetsonload() {
            $('.banksingle, .cheque, .modechange').hide();
        }

        function toggleAllCheckboxes() {
            document.getElementById('selectAll').addEventListener('change', function() {
                const itemCheckboxes = document.querySelectorAll('.groupCheckbox');
                itemCheckboxes.forEach(checkbox => {
                    checkbox.checked = this
                        .checked; // Set checked status based on the "Select All" checkbox
                });
            });
        }

        $('#clearingdate').datetimepicker({
            format: 'DD-MM-YYYY'
        });

        $('#returndate').datetimepicker({
            format: 'DD-MM-YYYY'
        });

        $('.singleClear').on('change', function() {
            $('.singleClear').not(this).prop('checked', false);

            if ($(this).prop('checked')) {
                $('.banksingle').show();
                $('.modechange').show();
                if ($(this).val() == 2) {
                    $('.cheque').hide();
                } else {
                    $('.cheque').show();
                }
            } else {
                $('.banksingle').hide();
                $('.cheque').hide();
                $('.modechange').hide();
            }
        });

        $('#bankBulk').click(function() {
            if ($(this).prop('checked')) {
                $('.banksingle').show();
            } else {
                $('.banksingle').hide();
            }
        });

        $('.bulktriggerbtn').click(function(e) {
            e.preventDefault();
            if ($('.groupCheckbox:checked').length === 0) {
                toastr.error('Please select one or more items to continue.');
                return false;
            } else {
                $('#modal-clear-payable').show();
            }
        });


        $('#modal-clear-payable').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget); // Button that triggered the modal
            var clearType = button.data('clear-type'); // Extract info from data-* attributes
            $('#PaymentSubmitForm')[0].reset();
            hidelemnetsonload();

            $(this).find('input, select, textarea').removeClass('is-invalid is-valid');

            $('#method').val(clearType);

            // $('.modechange').hide();
            if (clearType === 'bulk') {

                $('.clrngamnt').hide();
                $('.chq').hide().css('display', 'none', 'important');
            } else {
                $('#detId').val(button.data('det-id'));

                let totalAmount = button.data('amount');
                document.getElementById('paid_amount').addEventListener('input', function() {
                    let paid = parseFloat(this.value) || 0;
                    // Prevent entering more than total amount
                    if (paid > totalAmount) {
                        paid = totalAmount;
                        this.value = totalAmount;
                    }

                    let remaining = totalAmount - paid;

                    document.getElementById('amountPending').innerText =
                        'Remaining Amount: ' + remaining;
                });

                $('#amountPending').text('Remaining Amount: ' + button.data('amount'));
                $('.clrngamnt').show();
                $('.chq').show();
            }
        });
    </script>

    <script>
        $(function() {
            let table = $('#PayableList').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,

                ajax: {
                    url: "{{ route('payable.list') }}",
                    data: function(d) {
                        d.date_from = $('#date_From').val();
                        d.date_to = $('#date_To').val();
                        d.vendor_id = $('#vendor_id').val();
                        d.property_id = $('#property_id').val();
                        d.payment_mode = $('#payment_mode').val();
                    }
                },
                columns: [{
                        data: 'checkbox',
                        name: 'checkbox',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'project_number',
                        name: 'contract.project_number',
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
                        data: 'payment_mode',
                        name: 'payment_mode',
                    },
                    // {
                    //     data: 'cheque_no',
                    //     name: 'contract_payment_details.cheque_no',
                    // },
                    {
                        data: 'payment_amount',
                        name: 'payment_amount',
                    },
                    {
                        data: 'composition',
                        name: 'composition',
                    },
                    {
                        data: 'has_returned',
                        name: 'has_returned',
                        render: function(data, type, row) {
                            if (data == 1) {
                                return '<span class="badge bg-danger text-white">Returned</span><i class="far fa-comments loadReason pl-1" onclick="loadReason(this)" data-reason="' +
                                    row.returned_reason + '"></i>';



                            }
                            return '-';

                        },
                    },

                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
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
                        let url = "{{ route('payables.pending.export') }}" + "?search=" +
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


    <script>
        $('#submitBtn').click(function(e) {
            e.preventDefault();

            let isValid = true;
            $(".error-text").remove(); // clear old errors

            // validate ALL required fields
            $("#PaymentSubmitForm").find("[required]:visible").each(function() {
                const value = $(this).val()?.trim();

                if (!value) {
                    isValid = false;
                    setInvalid(this, "This field is required");
                } else {
                    setValid(this);
                }
            });

            // Validate Select2 fields
            $("#PaymentSubmitForm").find('[required]select.select2').each(function() {
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
            var form = document.getElementById('PaymentSubmitForm');
            var fdata = new FormData(form);

            fdata.append('_token', $('meta[name="csrf-token"]').attr('content'));

            var selectedValues = [];
            $('.groupCheckbox:checked').each(function() {
                selectedValues.push($(this).val());
            });

            if ($('#method').val() == 'single') {
                selectedValues.push($('#detId').val());
            }

            fdata.append('payment_detail_ids', selectedValues);

            $.ajax({
                type: "POST",
                url: "{{ route('payable.save') }}",
                data: fdata,
                dataType: "json",
                processData: false,
                contentType: false,
                success: function(response) {
                    // console.log(response);
                    toastr.success(response.message);
                    window.location.href = "{{ route('finance.payable.clearing') }}";
                },
                error: function(errors) {
                    toastr.error(errors.responseJSON.message);
                }
            });
        }
    </script>


    <script>
        $('#returnBtn').click(function(e) {
            e.preventDefault();
            if ($('.groupCheckbox:checked').length === 0) {
                toastr.error('Please select one or more items to continue.');
                return false;
            } else {
                $('#modal-return-cheque').show();
            }
        });

        function returnedCheques() {
            var form = document.getElementById('ReturnForm');
            var fdata = new FormData(form);

            fdata.append('_token', $('meta[name="csrf-token"]').attr('content'));

            var selectedValues = [];
            $('.groupCheckbox:checked').each(function() {
                selectedValues.push($(this).val());
            });

            if ($('#method').val() == 'single') {
                selectedValues.push($('#detId').val());
            }

            fdata.append('payment_detail_ids', selectedValues);

            $.ajax({
                type: "POST",
                url: "{{ route('return.save') }}",
                data: fdata,
                dataType: "json",
                processData: false,
                contentType: false,
                success: function(response) {
                    // console.log(response);
                    toastr.success(response.message);
                    window.location.href = "{{ route('finance.payable.clearing') }}";
                },
                error: function(errors) {
                    toastr.error(errors.responseJSON.message);
                }
            });
        }



        function loadReason(ele) {
            let reason = $(ele).data('reason');

            $('#reason').html(
                '<li class="list-group-item">Loading...</li>');

            $('#reason').empty();

            if (reason === '') {
                $('#reason').append(
                    '<li class="list-group-item">No Reason found.</li>'
                );
            } else {
                $('#reason').append(`
                                        <li class="list-group-item">
                                            <div class="post clearfix" style="width: 100%">
                                                <p>
                                                    ${ reason }
                                                </p>
                                            </div>
                                        </li>
                                    `);
            }

            $('#reasonsModal').modal('show');
        }

        // function loadReason() {
        //     let reason = $(this).data('reason');
        //     $('#reason').html(
        //         '<li class="list-group-item">Loading...</li>');

        //     $('#reason').empty();

        //     if (reason === '') {
        //         $('#reason').append(
        //             '<li class="list-group-item">No comments found.</li>'
        //         );
        //     } else {
        //         $('#reason').append(`
    //                                     <li class="list-group-item">
    //                                         <div class="post clearfix" style="width: 100%">
    //                                             <p>
    //                                                 ${ reason }
    //                                             </p>
    //                                         </div>
    //                                     </li>
    //                                 `);
        //     }

        //     $('#commentsModal').modal('show');
        // }
    </script>
@endsection
