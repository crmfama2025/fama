@extends('admin.layout.admin_master')
@section('custom_css')
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('assets/daterangepicker/daterangepicker.css') }}">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet" href="{{ asset('assets/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('assets/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    <!-- iCheck for checkboxes and radio inputs -->
    <link rel="stylesheet" href="{{ asset('assets/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('assets/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/bs-stepper/css/bs-stepper.min.css') }}">
@endsection
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Agreement</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
                            <li class="breadcrumb-item active">Agreement List</li>
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
                                <!-- <h3 class="card-title">Agreement Details</h3> -->
                                <span class="float-right">

                                    @can('agreement.add')
                                        <a href="{{ route('agreement.create') }}" class="btn btn-info float-right m-1">Add
                                            Agreement</a>
                                    @endcan
                                    @can('agreement.renew')
                                        <a href="{{ route('agreement.expiring-list') }}"
                                            class="btn btn-secondary float-right m-1">Renewal List
                                        </a>
                                    @endcan
                                    {{-- <button class="btn btn-secondary float-right m-1" data-toggle="modal"
                                        data-target="#modal-import">Import</button> --}}
                                </span>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <div class="mb-3 text-center">
                                    <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                        <label class="btn btn-outline-primary active">
                                            <input type="radio" name="agreementFilter" value="all" autocomplete="off"
                                                checked> All
                                        </label>
                                        <label class="btn btn-outline-success">
                                            <input type="radio" name="agreementFilter" value="0" autocomplete="off">
                                            Active
                                        </label>
                                        <label class="btn btn-outline-warning">
                                            <input type="radio" name="agreementFilter" value="1" autocomplete="off">
                                            Terminated
                                        </label>
                                        <label class="btn btn-outline-danger">
                                            <input type="radio" name="agreementFilter" value="2" autocomplete="off">
                                            Expired
                                        </label>
                                    </div>
                                </div>


                                <table id="agreementTable" class="table table-striped projects display nowrap">
                                    <thead>
                                        <tr>
                                            <th style="width: 1%">#</th>
                                            <th>Actions</th>
                                            {{-- <th>Agreement Code</th> --}}
                                            <th>Company Name</th>
                                            <th>Project Details</th>
                                            <th>Customer Type</th>
                                            <th>Tenant Details</th>
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                            <th>Agreement Status</th>
                                            <th>Signed Agreement Status</th>
                                            <th>Created At</th>
                                            <!-- <th>Status</th> -->
                                            {{-- <th></th> --}}
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




            <div class="modal fade" id="modal-terminate">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Terminate Agreement</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="" id="terminateForm">
                            @csrf
                            <div class="modal-body">
                                <div class="card-body">
                                    <div class="form-group row">
                                        <label for="exampleInputEmail1" class="asterisk">Date</label>
                                        <div class="input-group date" id="terminationdate" data-target-input="nearest">
                                            <input type="text" name="terminated_date"
                                                class="form-control datetimepicker-input" data-target="#terminationdate"
                                                placeholder="dd-mm-YYYY" />
                                            <div class="input-group-append" data-target="#terminationdate"
                                                data-toggle="datetimepicker">
                                                <div class="input-group-text"><i class="fa fa-calendar-alt"></i></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="inputEmail3" class="col-form-label asterisk">Reason</label>
                                        <textarea name="terminated_reason" id="" class="form-control"></textarea>
                                    </div>

                                    <div class="form-group row">
                                        <label for="inputEmail3" class="col-form-label asterisk">Amount</label>
                                        <input type="number" name="amount" id="" class="form-control">
                                    </div>
                                    <div class="form-group row mt-2">
                                        <label class="col-form-label">Transaction Type</label>

                                        <div class="d-flex gap-4 mt-1 mx-1">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="transaction_type"
                                                    id="receive" value="1" checked>
                                                <label class="form-check-label" for="receive">
                                                    Receive
                                                </label>
                                            </div>

                                            <div class="form-check ml-2">
                                                <input class="form-check-input" type="radio" name="transaction_type"
                                                    id="pay_back" value="2">
                                                <label class="form-check-label" for="pay_back">
                                                    Pay Back
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-form-label asterisk">Payment Mode</label>

                                        <select name="payment_mode_id" id="payment_mode_id" class="form-control select2"
                                            style="width: 100%;">
                                            <option value="">Select Payment Mode</option>

                                            @foreach ($paymentmodes as $mode)
                                                <option value="{{ $mode->id }}">
                                                    {{ $mode->payment_mode_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group row d-none" id="bank_group">
                                        <label class="col-form-label asterisk">Bank</label>
                                        <select name="bank_id" id="bank_id" class="form-control select2"
                                            style="width: 100%;">
                                            <option value="">Select Bank</option>

                                            @foreach ($banks as $bank)
                                                <option value="{{ $bank->id }}">{{ $bank->bank_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group row d-none" id="cheque_group">
                                        <label class="col-form-label asterisk">Cheque Number</label>
                                        <input type="text" name="cheque_number" id="cheque_number"
                                            class="form-control">
                                    </div>

                                    <input type="hidden" name="agreement_id" id="agreement_id">
                                </div>
                            </div>
                            <!-- /.card-body -->
                            <div class="modal-footer justify-content-between">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-info terminate-btn">Save changes</button>
                            </div>
                        </form>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
            <!-- /.modal -->
        </section>
        <!-- /.content -->
    </div>
@endsection
@section('custom_js')
    <!-- Select2 -->

    <script src="{{ asset('assets/select2/js/select2.full.min.js') }}"></script>

    <script src="{{ asset('assets/moment/moment.min.js') }}"></script>
    <!-- Tempusdominus Bootstrap 4 -->

    <script src="{{ asset('assets/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>
    <!-- date-range-picker -->

    <script src="{{ asset('assets/daterangepicker/daterangepicker.js') }}"></script>
    <!-- DataTables  & Plugins -->

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
    <!-- BS-Stepper -->

    <script src="{{ asset('assets/bs-stepper/js/bs-stepper.min.js') }}"></script>

    <script>
        let banks = @json($banks);
        $(document).on('click', '.open-terminate-modal', function(e) {
            e.preventDefault();
            const agreementId = $(this).data('id');
            const companyId = $(this).data('company-id');
            $('#agreement_id').val(agreementId);
            $('#bank_id').empty().append('<option value="">Select Bank</option>');

            // Filter banks by company_id
            let filteredBanks = banks.filter(bank => bank.company_id == companyId);

            // Append filtered banks
            filteredBanks.forEach(bank => {
                $('#bank_id').append(
                    `<option value="${bank.id}">${bank.bank_name}</option>`
                );
            });

            // Re-init select2 (important when options change)
            $('#bank_id').select2({
                dropdownParent: $('#modal-terminate'),
                width: '100%'
            });

            $('#terminationdate').datetimepicker({
                format: 'DD-MM-YYYY',
                useCurrent: false
            });
            $('#modal-terminate').modal('show');
        });
    </script>
    <script>
        let table;
        $(function() {
            table = $('#agreementTable').DataTable({
                processing: true,
                serverSide: true,
                // responsive: true,

                ajax: {
                    url: "{{ route('agreement.list') }}",
                    data: function(d) {
                        d.status = $('input[name="agreementFilter"]:checked').val();
                    },
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'contracts.id',
                        orderable: true,
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                    // {
                    //     data: 'agreement_code',
                    //     name: 'agreements.agreement_code',
                    // },
                    {
                        data: 'company_name',
                        name: 'companies.company_name',

                    },
                    {
                        data: 'project_number',
                        name: 'contracts.project_number',
                    },
                    {
                        data: 'business_type',
                        name: 'contract_units.business_type',
                    },
                    {
                        data: 'tenant_details',
                        name: 'agreement_tenants.tenant_name',
                        render: function(data, type, row) {
                            return data ? data : '';
                        },
                        orderable: false,
                        searchable: true
                    },

                    {
                        data: 'start_date',
                        name: 'agreements.start_date',
                    },
                    {
                        data: 'end_date',
                        name: 'agreements.end_date',
                    },
                    {
                        data: 'agreement_status',
                        name: 'agreements.agreement_status',
                        render: function(data, type, row) {
                            let badgeClass = '';
                            let text = '';

                            switch (data) {
                                case 0:
                                    badgeClass = 'badge badge-success text-white';
                                    text = 'Active';
                                    break;
                                case 1:
                                    badgeClass = 'badge badge-warning text-black';
                                    text = 'Terminated';
                                    break;
                                case 2:
                                    badgeClass = 'badge badge-danger text-white';
                                    text = 'Expired';
                                    break;

                            }

                            return '<span class="' + badgeClass + '">' + text + '</span>';
                        },
                    },
                    {
                        data: 'is_signed_agreement_uploaded',
                        name: 'agreements.is_signed_agreement_uploaded',
                        render: function(data, type, row) {
                            let badgeClass = '';
                            let text = '';

                            switch (data) {
                                case 0:
                                    badgeClass = 'badge badge-warning';
                                    text = 'Not Uploaded';
                                    break;
                                case 1:
                                    badgeClass = 'badge badge-success text-white';
                                    text = 'Uploaded';
                                    break;

                            }

                            return '<span class="' + badgeClass + '">' + text + '</span>';
                        },
                    },
                    {
                        data: 'created_at',
                        name: 'agreements.created_at',
                    },



                ],
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
                        let url = "{{ route('agreement.export') }}" + "?search=" +
                            encodeURIComponent(searchValue);
                        window.location.href = url;
                    }
                }],
                // <-- ADD THESE OPTIONS
                // scrollY: '400px',
                scrollX: true, // height of the table container
                scrollCollapse: true,
                paging: true, // keep pagination
                fixedHeader: true // optional: fixes header while scrolling
            });
        });

        function deleteConf(id) {
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
                        url: '/agreement/' + id,
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        dataType: "json",
                        success: function(response) {
                            toastr.success(response.message);
                            $('#agreementTable').DataTable().ajax.reload();
                        }
                    });

                }
            });
        }
        $('.terminate-btn').click(function(e) {
            e.preventDefault();

            const button = $(this);
            const url = "{{ url('agreement-terminate') }}";
            const method = 'POST';
            const form = document.getElementById('terminateForm');

            button.prop('disabled', true);

            const formData = new FormData(form);
            formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

            $.ajax({
                url: url,
                type: method,
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    toastr.success(response.message);
                    $('#modal-terminate').modal('hide');
                    window.location.href = "{{ route('agreement.index') }}";
                },
                error: function(xhr) {
                    button.prop('disabled', false);
                    const response = xhr.responseJSON;
                    if (xhr.status === 422 && response?.errors) {
                        $.each(response.errors, function(key, messages) {
                            toastr.error(messages[0]);
                        });
                    } else if (response.message) {
                        toastr.error(response.message);
                    }
                }
            });
        });
        $('input[name="agreementFilter"]').on('change', function() {
            table.ajax.reload();
        });
    </script>
    <script>
        $('#payment_mode_id').on('change', function() {

            let selected = $(this).val();
            console.log(selected);

            // Hide all first
            $('#bank_group').addClass('d-none');
            $('#cheque_group').addClass('d-none');

            // Clear values when hidden
            $('#bank_id').val(null).trigger('change');
            $('#cheque_number').val('');

            if (selected === '2') {
                // alert("test");
                // Bank transfer → show bank only
                $('#bank_group').removeClass('d-none');
            }

            if (selected === '3') {
                // Cheque → show bank + cheque number
                $('#bank_group').removeClass('d-none');
                $('#cheque_group').removeClass('d-none');
            }

            // cash / credit → nothing shown
        });
    </script>
@endsection
