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
                                <div class="card card-info">
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
                                                        <option value="{{ $investor->id }}">{{ $investor->investor_name }}
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

                                            <button class="btn btn-success float-right m-1 bulktriggerbtn"
                                                data-toggle="modal" data-target="#modal-payout"
                                                data-clear-type="bulk">Payout All</button>

                                            <a href="{{ route('distributed.report') }}"
                                                class="btn btn-outline-maroon m-1"><i class="fas fa-book"></i>
                                                View Report</a>
                                        </span>
                                    </div>
                                    <div class="card-body">
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
                                    <div class="form-group companySingle">
                                        <label for="exampleInputEmail1">Company Name</label>
                                        <select class="form-control select2" name="paid_company_id" id="paid_company"
                                            required>
                                            <option value="">Select Company</option>
                                            @foreach ($companies as $company)
                                                <option value="{{ $company->id }}">{{ $company->company_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group banksingle">
                                        <label for="exampleInputEmail1">Bank Name</label>
                                        <select class="form-control select2 bank_name" name="paid_bank" id="bank_name"
                                            required>
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


        function toggleAllCheckboxes() {
            document.getElementById('selectAll').addEventListener('change', function() {
                const itemCheckboxes = document.querySelectorAll('.groupCheckbox');
                itemCheckboxes.forEach(checkbox => {
                    checkbox.checked = this
                        .checked; // Set checked status based on the "Select All" checkbox
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

        $('.singleClear').on('change', function() {
            $('.singleClear').not(this).prop('checked', false);
            console.log($(this).val());
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

        // $('#bankBulk').click(function() {
        //     if ($(this).prop('checked')) {
        //         $('.banksingle').show();
        //     } else {
        //         $('.banksingle').hide();
        //     }
        // });

        $('.bulktriggerbtn').click(function(e) {
            e.preventDefault();
            if ($('.groupCheckbox:checked').length === 0) {
                toastr.error('Please select one or more items to continue.');
                return false;
            } else {
                $('#modal-clear-payable').show();
            }
        });



        $('#modal-payout').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget); // Button that triggered the modal
            var clearType = button.data('clear-type'); // Extract info from data-* attributes
            $('#PayoutSubmitForm')[0].reset();
            hidelemnetsonload();

            $(this).find('input, select, textarea').removeClass('is-invalid is-valid');

            $('#method').val(clearType);

            // $('.modechange').hide();
            if (clearType === 'bulk') {

                $('.clrngamnt').hide();
                $('.chq').hide().css('display', 'none', 'important');
            } else {
                $('#payoutId').val(button.data('det-id'));
                let reinvest = button.data('reinvest');
                $('#reinvest').val(reinvest);

                let totalAmount = button.data('amount');

                if (reinvest) {
                    $('#submitBtn').text('Re-Invest');
                    $('#paid_amount').val(totalAmount).attr('readonly', true);
                } else {
                    // document.getElementById('paid_amount').addEventListener('input', function() {
                    //     let paid = parseFloat(this.value) || 0;
                    //     // Prevent entering more than total amount
                    //     if (paid > totalAmount) {
                    //         paid = totalAmount;
                    //         this.value = totalAmount;
                    //     }

                    //     let remaining = totalAmount - paid;

                    //     document.getElementById('amountPending').innerText =
                    //         'Remaining Amount: ' + remaining;
                    // });
                    $('#paid_amount').off('input.payoutModal').on('input.payoutModal', function() {
                        let paid = parseFloat(this.value) || 0;
                        console.log('paid', paid);
                        if (paid > totalAmount) {
                            paid = totalAmount;
                            this.value = totalAmount;
                        }

                        let remaining = totalAmount - paid;

                        $('#amountPending').text('Remaining Amount: ' + remaining.toFixed(2));
                    });

                    $('#submitBtn').text('Submit');
                    $('#paid_amount').attr('readonly', false);
                    $('#amountPending').text('Remaining Amount: ' + button.data('amount'));
                }

                $('.clrngamnt').show();
                $('.chq').show();
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
                        d.batch_id = $('#batch_id').val();
                        d.investor_id = $('#investor_id').val();
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
                        data: 'payment_mode',
                        name: 'payment_mode',
                    },
                    // {
                    //     data: 'cheque_no',
                    //     name: 'contract_payment_details.cheque_no',
                    // },
                    // {
                    //     data: 'payment_amount',
                    //     name: 'payment_amount',
                    // },
                    // {
                    //     data: 'composition',
                    //     name: 'composition',
                    // },
                    // {
                    //     data: 'has_returned',
                    //     name: 'has_returned',
                    //     render: function(data, type, row) {
                    //         if (data == 1) {
                    //             return '<span class="badge bg-danger text-white">Returned</span><i class="far fa-comments loadReason pl-1" onclick="loadReason(this)" data-reason="' +
                    //                 row.returned_reason + '"></i>';



                    //         }
                    //         return '-';

                    //     },
                    // },

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

                        let params = dt.ajax.params();

                        // add your custom filters manually (important)
                        params.month = $('#month').val();
                        params.batch_id = $('#batch_id').val();
                        params.investor_id = $('#investor_id').val();
                        params.search = dt.search();

                        // build query string
                        let queryString = $.param(params);

                        let url = "{{ route('payout.pending.export') }}?" + queryString;
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

            showLoader();

            $.ajax({
                type: "POST",
                url: "{{ route('payout.distribute.save') }}",
                data: fdata,
                dataType: "json",
                processData: false,
                contentType: false,
                success: function(response) {
                    console.log(response);
                    $('#modal-payout').modal('hide');
                    hideLoader();
                    toastr.success(response.message);

                    if ($('#reinvest').val() == 1) {
                        let params = {};

                        // add your custom filters manually (important)
                        params.reinvestment = $('#reinvest').val();
                        params.parent_id = response.data[0].investment_id;
                        params.investor_id = response.data[0].investor_id;
                        params.amount = response.data[0].amount_paid;
                        params.date = response.data[0].paid_date;

                        // build query string
                        let queryString = $.param(params);

                        window.location.href = "/investment/create?" + queryString;
                    } else {
                        $('#payoutPendingTable').DataTable().ajax.reload();
                    }

                    // window.location.href = "{{ route('investorPayout.index') }}";
                },
                error: function(errors) {
                    hideLoader();
                    toastr.error(errors.responseJSON.message);
                }
            });
        }
    </script>
@endsection
