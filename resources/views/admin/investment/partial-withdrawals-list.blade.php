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
                            <div class="card-body ">

                                <div class="mb-3 text-center">
                                    <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                        <label class="btn btn-outline-info active">
                                            <input type="radio" name="statusFilter" value="all" autocomplete="off"
                                                checked> All
                                        </label>
                                        <label class="btn btn-outline-info">
                                            <input type="radio" name="statusFilter" value="1" autocomplete="off">
                                            Requested
                                        </label>
                                        <label class="btn btn-outline-info">
                                            <input type="radio" name="statusFilter" value="2" autocomplete="off">
                                            Approved
                                        </label>
                                        <label class="btn btn-outline-info">
                                            <input type="radio" name="statusFilter" value="3" autocomplete="off">
                                            Withdawal Done
                                        </label>
                                    </div>
                                </div>
                                <div class="card-body table-responsive">
                                    <table id="partialWithdrawalsTable" class="table table-striped  nowrap"width="100%">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Action</th>
                                                <th>investor Details</th>
                                                <th>Company Name</th>
                                                <th>Status</th>
                                                <th>Transaction Type</th>
                                                <th>Transaction Amount</th>
                                                <th>Added By</th>
                                                <th>Requested Date</th>
                                                <th>Duration Days</th>
                                                <th>Withdrawal Date</th>

                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
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
            <!-- /.container-fluid -->







        </section>
        <!-- /.content -->

        <div class="modal fade" id="approvalModal" tabindex="-1">
            <div class="modal-dialog">
                <form id="approvalForm">
                    @csrf

                    <input type="hidden" id="approval_id" name="id">

                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Approve Withdrawal</h5>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>

                        <div class="modal-body">
                            <p>Are you sure you want to approve this withdrawal?</p>

                            <div class="form-group">
                                <label>Remarks (optional)</label>
                                <textarea name="approval_remarks" class="form-control"></textarea>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                Cancel
                            </button>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check"></i> Approve
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
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
                        d.status = $('input[name="statusFilter"]:checked').val();
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
                        name: 'investor.investor_name'
                    },
                    {
                        data: 'company_name',
                        name: 'company.company_name'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },

                    {
                        data: 'transaction_type',
                        name: 'transactionType.transaction_type'
                    },
                    {
                        data: 'transaction_amount',
                        name: 'investor_ledgers.transaction_amount'
                    },
                    {
                        data: 'added_by',
                        name: 'addedBy.first_name'
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
                        let url = "{{ route('investor.partial-withdrawal.export') }}" +
                            "?search=" +
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
        $('input[name="statusFilter"]').on('change', function() {
            table.ajax.reload();
            setTimeout(function() {
                table.columns.adjust().responsive.recalc();
            }, 200);
        });
    </script>
    <script>
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

        $(document).on('click', '.open-approval-modal', function() {
            let id = $(this).data('id');

            // set hidden input value
            $('#approval_id').val(id);

            // Laravel route with placeholder
            let url = "{{ route('investor.partial-withdrawals.approve', ':id') }}";
            url = url.replace(':id', id);

            $('#approvalForm').data('url', url);

            // OPEN MODAL USING JS
            $('#approvalModal').modal('show');
        });
        $(document).on('submit', '#approvalForm', function(e) {
            e.preventDefault();

            let form = $(this);
            let url = form.data('url');

            $.ajax({
                url: url,
                type: "POST",
                data: form.serialize(),
                beforeSend: function() {
                    form.find('button[type="submit"]').prop('disabled', true).text('Processing...');
                },
                success: function(response) {
                    $('#approvalModal').modal('hide');

                    // Optional: show success message
                    toastr.success(response.message || 'Approved successfully');

                    // Reload DataTable (important)
                    table.ajax.reload(null, false);
                },
                error: function(xhr) {
                    let error = xhr.responseJSON?.message || 'Something went wrong';
                    toastr.error(error);
                },
                complete: function() {
                    form.find('button[type="submit"]').prop('disabled', false).html(
                        '<i class="fas fa-check"></i> Approve');
                }
            });
        });
    </script>
@endsection
