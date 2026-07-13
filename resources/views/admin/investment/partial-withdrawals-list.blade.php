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

                            <!-- /.card-header -->
                            <div class="card-body table-responsive">
                                <table id="partialWithdrawalsTable" class="table table-striped  nowrap"width="100%">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Action</th>
                                            <th>investor Details</th>
                                            <th>Status</th>
                                            <th>Transaction Amount</th>
                                            <th>Requested Date</th>
                                            <th>Duration Days</th>
                                            <th>Withdrawal Date</th>

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
            table = $('#partialWithdrawalsTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                // pageLength: 5,
                ajax: {
                    url: "{{ route('investor.partial-withdrawals') }}",
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
                        data: 'investor_name',
                        name: 'investor_name'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'transaction_amount',
                        name: 'transaction_amount'
                    },
                    {
                        data: 'requested_date',
                        name: 'requested_date'
                    },
                    {
                        data: 'duration_days',
                        name: 'duration_days'
                    },
                    {
                        data: 'withdrawal_date',
                        name: 'withdrawal_date'
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
                        // url: '/investment/' + id,
                        url: "{{ route('investment.destroy', ':id') }}".replace(':id', id),
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
            let comm_outstanding = $(this).data('outstanding');
            $('#termination_outstanding').val(outstanding);
            $('#termination_referral_commission_outstanding').val(comm_outstanding);
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





            // if ($(this).data('status')) {
            $status = $(this).data('status');
            if ($status == 1) {
                // alert("test");

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
                $('.pending_div')
                    .removeClass('d-none')
            } else if ($status == 0) {

                // alert("test");
                $('#profit-div')
                    .addClass('d-none')
                    .html('');
                $('.pending_div')
                    .addClass('d-none')
            }
            // }

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
            $('#terminationForm')[0].reset();

        });
    </script>
@endsection
