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
                            <div class="card-header">
                                <a class="btn btn-info float-right mr-1" href="{{ route('investorPayout.index') }}"><i
                                        class="fas fa-arrow-left mr-2"></i> Back</a>
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

                                <div class="card">
                                    <div class="card-body">
                                        <table id="payoutPendingTable" class="table table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Investor Name</th>
                                                    <th>Company Name</th>
                                                    {{-- <th style="width: 5%">Investment Amount</th> --}}
                                                    <th>Payout Date</th>
                                                    <th>Payout Type</th>
                                                    <th>payout Amount</th>
                                                    <th>Payment Mode</th>
                                                    {{-- <th>Action</th> --}}
                                                </tr>
                                            </thead>
                                            <tbody>

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
                            <div class="modal-body">
                                <div class="card-body">
                                    <div class="form-group row">
                                        <label for="exampleInputEmail1">Payout Date</label>
                                        <div class="input-group date" id="investmentdate" data-target-input="nearest">
                                            <input type="text" class="form-control datetimepicker-input"
                                                name="paid_date" data-target="#investmentdate" placeholder="dd-mm-YYYY"
                                                required />
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
                                                    class="singleClear" value="{{ $paymentmode->id ?? '' }}"
                                                    name="paid_mode">
                                                <label
                                                    for="{{ $paymentmode->payment_mode_code }}">{{ $paymentmode->payment_mode_name ?? '' }}
                                                </label>
                                            </div>
                                        @endforeach
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
    </script>

    <script>
        $(function() {
            let table = $('#payoutPendingTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,

                ajax: {
                    url: "{{ route('distributed.list') }}",
                    data: function(d) {
                        d.date_From = $('#date_From').val();
                        d.date_To = $('#date_To').val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'id',
                        orderable: true,
                        searchable: false
                    },
                    {
                        data: 'investor_name',
                        name: 'investor_name',
                    },
                    {
                        data: 'company_name',
                        name: 'company_name',
                    },
                    {
                        data: 'paid_date',
                        name: 'paid_date',
                    },
                    {
                        data: 'payout_type',
                        name: 'payout_type',
                    },
                    {
                        data: 'amount_paid',
                        name: 'amount_paid',
                    },
                    {
                        data: 'payment_mode',
                        name: 'payment_mode',
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

                        let params = dt.ajax.params();

                        // add your custom filters manually (important)
                        params.date_From = $('#date_From').val();
                        params.date_To = $('#date_To').val();
                        params.search = dt.search();

                        // build query string
                        let queryString = $.param(params);

                        let url = "{{ route('payout.report.export') }}?" + queryString;

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
                $('.groupCheckbox:checked').each(function() {
                    selectedValues.push($(this).val());
                });
            }

            fdata.append('payout_ids', selectedValues);

            $.ajax({
                type: "POST",
                url: "{{ route('payout.distribute.save') }}",
                data: fdata,
                dataType: "json",
                processData: false,
                contentType: false,
                success: function(response) {
                    // console.log(response);
                    toastr.success(response.message);
                    window.location.href = "{{ route('investorPayout.index') }}";
                },
                error: function(errors) {
                    toastr.error(errors.responseJSON.message);
                }
            });
        }
    </script>
@endsection
