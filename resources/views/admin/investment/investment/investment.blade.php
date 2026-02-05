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
                        <h1>Investment</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="../dashboard.php">Home</a></li>
                            <li class="breadcrumb-item active">Investment</li>
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
                                <!-- <h3 class="card-title">Property Details</h3> -->
                                <span class="float-right">
                                    <a class="btn btn-info float-right m-1" href="{{ route('investment.create') }}">Add
                                        Investment</a>
                                    {{-- <button class="btn btn-secondary float-right m-1" data-toggle="modal"
                                        data-target="#modal-import">Import</button> --}}
                                </span>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body table-responsive">
                                <table id="investmentsTable" class="table table-striped  nowrap"width="100%">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Action</th>
                                            <th>Status</th>
                                            <th>Company Name</th>
                                            <th>Investor Name</th>
                                            <th>Investment Amount</th>
                                            <th>Received Amount</th>
                                            <th>Investment Date</th>
                                            <th>Profit Interval</th>
                                            <th>Profit %</th>
                                            <th>Maturity date</th>
                                            <th>Profit Release Date</th>
                                            <th>Grace Period </th>
                                            <th>Payout Batch</th>
                                            <th>Nominee Details</th>
                                            <th>Commission Amount</th>
                                            <th>Commission %</th>
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




            <div class="modal fade" id="terminationModal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <form id="terminationForm" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="modal-content">
                            <div class="modal-header bg-danger">
                                <h5 class="modal-title text-white">
                                    <i class="fas fa-ban"></i> Terminate Investment
                                </h5>
                                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                            </div>

                            <div class="modal-body">

                                <input type="hidden" name="investment_id" id="termination_investment_id">
                                <div class=" d-none text-lg-left text-info" id="profit-div">
                                </div>

                                <!-- Requested Date -->
                                <div class="form-group">
                                    <label class="asterisk">Requested Date</label>
                                    <div class="input-group date" id="requesteddate" data-target-input="nearest">
                                        <input type="text" class="form-control datetimepicker-input"
                                            name="termination_requested_date" id="termination_requested_date"
                                            data-target="#requesteddate" placeholder="DD-MM-YYYY"
                                            value="{{ old('termination_requested_date', isset($investment->termination_requested_date) ? \Carbon\Carbon::parse($investment->termination_requested_date)->format('d-m-Y') : '') }}"
                                            required>
                                        <div class="input-group-append" data-target="#requesteddate"
                                            data-toggle="datetimepicker">
                                            <div class="input-group-text">
                                                <i class="fa fa-calendar"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Duration -->
                                <div class="form-group">
                                    <label class="asterisk">Duration (Days)</label>
                                    <input type="number" name="duration" id="termination_duration" class="form-control"
                                        min="1" required>
                                </div>

                                <!-- Termination Date -->
                                <div class="form-group">
                                    <label class="asterisk">Termination Date</label>
                                    {{-- <input type="date" name="termination_date" id="termination_date"
                                        class="form-control"> --}}
                                    <div class="input-group date" id="terminationdate" data-target-input="nearest">
                                        <input type="text" class="form-control datetimepicker-input"
                                            name="termination_date" id="termination_date" data-target="#terminationdate"
                                            placeholder="DD-MM-YYYY"
                                            value="{{ old('termination_date', isset($investment->termination_date) ? \Carbon\Carbon::parse($investment->termination_date)->format('d-m-Y') : '') }}"
                                            required>
                                        <div class="input-group-append" data-target="#terminationdate"
                                            data-toggle="datetimepicker">
                                            <div class="input-group-text">
                                                <i class="fa fa-calendar"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Investment Amount</label>
                                    <input type="number" name="investment_amount" id="investment_amount"
                                        class="form-control" disabled>
                                </div>

                                <div class="form-group">
                                    <label class="asterisk">Outstanding Till Termination</label>
                                    <input type="number" name="termination_outstanding" id="termination_outstanding"
                                        class="form-control" step="0.01" min="-999999999" required>
                                </div>




                                <!-- File Upload -->
                                <div class="form-group">
                                    <label>Upload Document</label>
                                    <input type="file" name="termination_file" class="form-control-file"
                                        accept=".pdf,.jpg,.jpeg,.png">
                                    <small class="text-muted">PDF / JPG / PNG allowed</small>
                                    <div id="existingFileContainer" style="margin-top:5px;"></div>
                                </div>

                            </div>

                            <div class="modal-footer">
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-check"></i> Confirm Termination
                                </button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                    Cancel
                                </button>
                            </div>

                        </div>
                    </form>
                </div>
            </div>

            <!-- /.modal -->

            <div class="modal fade" id="pendingInvestmentModal" tabindex="-1">
                <div class="modal-dialog">
                    <form id="pendingInvestmentForm">
                        @csrf
                        <input type="hidden" name="investment_id" id="investment_id">

                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Submit Pending Investment</h5>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>

                            <div class="modal-body">
                                <div class="form-group">
                                    <label>Pending Balance Amount</label>
                                    <input type="text" id="pending_balance"
                                        class="form-control font-weight-bold text-danger" readonly>
                                </div>
                                <div class="form-group">
                                    <label class="asterisk">Received Date</label>
                                    {{-- <input type="date" name="received_date" class="form-control" required>
                                    <label class="asterisk">Investment Date</label> --}}
                                    <div class="input-group date" id="receiveddate" data-target-input="nearest">
                                        <input type="text" class="form-control datetimepicker-input"
                                            name="received_date" data-target="#receiveddate" placeholder="DD-MM-YYYY"
                                            required>
                                        <div class="input-group-append" data-target="#receiveddate"
                                            data-toggle="datetimepicker">
                                            <div class="input-group-text">
                                                <i class="fa fa-calendar"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="asterisk">Received Amount</label>
                                    <input type="number" name="received_amount" id="received_amount"
                                        class="form-control" step="0.01" min="0" required>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-check-circle"></i> Submit
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>


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
                pageLength: 5,
                ajax: {
                    url: "{{ route('investment.list') }}",
                    data: function(d) {
                        // You can add filters here if needed
                    },
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
                        let url = "{{ route('investment.export') }}" + "?search=" +
                            encodeURIComponent(searchValue);
                        window.location.href = url;
                    }
                }]
            });

        });

        $('#receiveddate').datetimepicker({
            format: 'DD-MM-YYYY'
        });

        $('#requesteddate').datetimepicker({
            format: 'DD-MM-YYYY'
        });
        $('#terminationdate').datetimepicker({
            format: 'DD-MM-YYYY'
        });
    </script>
    <script>
        $(document).on('click', '.openPendingModal', function() {
            let investmentId = $(this).data('id');
            let pendingBalance = parseFloat($(this).data('balance')) || 0;


            $('#investment_id').val(investmentId);
            $('#pending_balance').val(pendingBalance.toFixed(2));
            $('#received_amount')
                .attr('max', pendingBalance.toFixed(2))
                .attr('min', 1)
                .val('');
            $('#pendingInvestmentModal').modal('show');
        });

        // function validateReceivedAmount() {
        //     let received = parseFloat($('#received_amount').val()) || 0;
        //     let pending = parseFloat($('#pending_balance').val()) || 0;

        //     if (received > pending) {
        //         Swal.fire({
        //             icon: 'warning',
        //             text: 'Received Amount cannot be greater than Investment Amount.',
        //             toast: true,
        //             position: 'top-end',
        //             showConfirmButton: false,
        //             timer: 2500,
        //         });
        //         $('#pendingInvestmentForm button[type="submit"]').attr('disabled', true);
        //     } else if (received == 0) {
        //         Swal.fire({
        //             icon: 'warning',
        //             text: 'Received Amount cannot be Zero.',
        //             toast: true,
        //             position: 'top-end',
        //             showConfirmButton: false,
        //             timer: 2500,
        //         });
        //         $('#pendingInvestmentForm button[type="submit"]').attr('disabled', true);
        //     } else {
        //         $('#pendingInvestmentForm button[type="submit"]').attr('disabled', false);
        //     }
        // }

        // $('#received_amount').on('input', function() {
        //     validateReceivedAmount();
        // });
        $('#pendingInvestmentForm').on('submit', function(e) {
            e.preventDefault();

            $.ajax({
                url: "{{ route('investment.submit.pending') }}",
                method: "POST",
                data: $(this).serialize(),
                beforeSend: function() {
                    $('#pendingInvestmentForm button[type="submit"]').attr('disabled', true);
                },
                success: function(res) {
                    $('#pendingInvestmentModal').modal('hide');
                    $('#investmentsTable').DataTable().ajax.reload(null, false);
                    toastr.success(res.message);
                    setTimeout(function() {
                        window.location.reload();
                    }, 2000);
                },
                error: function(xhr) {
                    // Handle error
                    let errMsg = 'Something went wrong!';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errMsg = xhr.responseJSON.message;
                    }
                    toastr.error(errMsg);
                },
                complete: function() {
                    $('#pendingInvestmentForm button[type="submit"]').attr('disabled', false);
                }
            });
        });

        function confirmDelete(id) {
            Swal.fire({
                title: "Are you sure?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "DELETE",
                        url: '/investment/' + id,
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        dataType: "json",
                        success: function(response) {
                            toastr.success(response.message);
                            $('#investmentsTable').DataTable().ajax.reload();
                        }
                    });

                }
                //  else {
                //     toastr.error(errors.responseJSON.message);
                // }
            });
        }
        $(document).on('click', '.openTerminationModal', function() {
            $('#terminationForm')[0].reset();
            $('#existingFileContainer').html('');
            let investmentId = $(this).data('id');


            $('#termination_investment_id').val(investmentId);
            $('#requested_date').val('');
            $('#termination_duration').val('');
            $('#termination_date').val('');
            let invest_amount = $(this).data('principal');
            $('#investment_amount').val(invest_amount);
            let outstanding = $(this).data('outstanding');
            $('#termination_outstanding').val(outstanding);
            // 👉 Outstanding profit
            let outstandingProfit = $(this).data('outstanding-profit');

            if (outstandingProfit !== null && outstandingProfit !== '' && outstandingProfit != 0) {
                $('#profit-div')
                    .removeClass('d-none')
                    .html(' Pending Payout Amount Generated: <strong>' + outstandingProfit +
                        '</strong>');
            } else {
                $('#profit-div')
                    .addClass('d-none')
                    .html('');
            }





            if ($(this).data('status')) {
                $status = $(this).data('status');
                if ($status == 1) {
                    let requestedDate = $(this).data('requested-date') || '';
                    let duration = $(this).data('duration') || '';
                    let terminationDate = $(this).data('termination-date') || '';
                    let filePath = $(this).data('file-path');

                    console.log(filePath);


                    $('#termination_investment_id').val(investmentId);
                    $('#termination_requested_date').val(requestedDate);
                    $('#termination_duration').val(duration);
                    $('#termination_date').val(terminationDate);

                    if (filePath) {
                        $('#existingFileContainer').html(
                            '<a style="text-decoration:underline;" class="text-blue" href="' + filePath +
                            '" target="_blank">Click here </a>to view Existing File'
                        );
                    } else {
                        $('#existingFileContainer').html('');
                    }
                }
            }

            $('#terminationModal').modal('show');
        });
        $('#requesteddate').on('change.datetimepicker', function() {
            calculateTerminationDate();
        });
        $('#termination_duration').on('change keyup', function() {
            calculateTerminationDate();
        });

        function calculateTerminationDate() {
            let requestedDate = $('#termination_requested_date').val();
            let duration = parseInt($('#termination_duration').val(), 10);

            if (!requestedDate || isNaN(duration) || duration <= 0) {
                $('#termination_date').val('');
                return;
            }

            // Convert DD-MM-YYYY to YYYY-MM-DD
            let parts = requestedDate.split('-');
            if (parts.length !== 3) return;

            let date = new Date(parts[2], parts[1] - 1, parts[0]); // year, monthIndex, day
            if (isNaN(date.getTime())) return;

            date.setDate(date.getDate() + duration);

            let day = String(date.getDate()).padStart(2, '0');
            let month = String(date.getMonth() + 1).padStart(2, '0');
            let year = date.getFullYear();

            $('#termination_date').val(`${day}-${month}-${year}`);
        }
        $('#terminationForm').on('submit', function(e) {
            e.preventDefault();

            let form = $(this)[0];
            let formData = new FormData(form);

            $.ajax({
                url: "{{ route('investment.submit.termination') }}",
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function() {
                    $('#terminationForm button[type="submit"]').attr('disabled', true);
                },
                success: function(res) {
                    $('#terminationModal').modal('hide');
                    $('#investmentsTable').DataTable().ajax.reload(null, false);
                    toastr.success(res.message);
                    setTimeout(function() {
                        window.location.reload();
                    }, 2000);
                },
                error: function(xhr) {
                    let errMsg = 'Something went wrong!';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errMsg = xhr.responseJSON.message;
                    }
                    toastr.error(errMsg);
                },
                complete: function() {
                    $('#terminationForm button[type="submit"]').attr('disabled', false);
                }
            });
        });
        $('#terminationModal').on('hidden.bs.modal', function() {
            // Reset the form fields
            $('#terminationForm')[0].reset();
        });
    </script>
@endsection
