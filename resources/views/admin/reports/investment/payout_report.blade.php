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
                                </div>
                            </div>
                            <div class="card-body">
                                <table id="payoutPendingTable" class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Investor Name</th>
                                            <th>Company Name</th>
                                            <th>Investment Code</th>
                                            <th>Payout Date</th>
                                            <th>Payout Type</th>
                                            <th>payout Amount</th>
                                            <th width="188">Payment Mode</th>
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
        $(function() {
            let table = $('#payoutPendingTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,

                ajax: {
                    url: "{{ route('investment-payout.list') }}",
                    data: function(d) {
                        d.month = $('#month').val();
                        d.batch_id = $('#batch_id').val();
                        d.investor_id = $('#investor_id').val();
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

                        let url = "{{ route('payout-report.export') }}?" + queryString;
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

                        let url = "{{ route('investment.create') }}?" + queryString;
                        window.location.href = url;
                        // window.location.href = "/investment/create?" + queryString;


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
