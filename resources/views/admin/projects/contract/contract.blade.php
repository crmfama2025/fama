@extends('admin.layout.admin_master')

@section('custom_css')
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('assets/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('assets/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/datatables-buttons/css/buttons.bootstrap4.min.css') }}">

    <link rel="stylesheet" href="{{ asset('assets/icheck-bootstrap/icheck-bootstrap.min.css') }}">
@endsection

@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Contract</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
                            <li class="breadcrumb-item active">Contract</li>
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
                                <!-- <h3 class="card-title">Contract Details</h3> -->
                                <span class="float-right">
                                    @if (auth()->user()->hasAnyPermission(['contract.add']))
                                        <a href="{{ route('contract.create') }}" class="btn btn-info float-right m-1">
                                            Add Contract
                                        </a>
                                    @endif

                                    @if (auth()->user()->hasAnyPermission(['contract.renew']))
                                        <a href="{{ route('contract.renewal_pending_list') }}"
                                            class="btn btn-secondary float-right m-1">
                                            Renew Contract
                                        </a>
                                    @endif
                                    {{-- <button class="btn btn-secondary float-right m-1" data-toggle="modal"
                                        data-target="#modal-import">Import</button> --}}
                                </span>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <div class="table-responsive">
                                    <div class="d-flex justify-content-center">
                                        <div class="mb-2" id="statusFilters">
                                            @php
                                                $permission = auth()
                                                    ->user()
                                                    ->permissions()
                                                    ->whereIn('permission_id', [57, 59, 60, 63, 64, 92])
                                                    ->exists();
                                            @endphp
                                            @if ($permission)
                                                <button class="btn my-1 btn-primary filter-btn" add-class="btn-primary"
                                                    data-filter="">All</button>
                                                <button class="btn my-1 btn-outline-warning filter-btn pending"
                                                    add-class="btn-warning" data-filter="0">Pending</button>
                                                <button class="btn my-1 btn-outline-info filter-btn processing"
                                                    add-class="btn-info" data-filter="1">Processing</button>
                                            @endif
                                            <button class="btn my-1 btn-outline-df filter-btn approvalPending"
                                                add-class="btn-df" data-filter="4">Approval Pending</button>
                                            <button class="btn my-1 btn-outline-secondary filter-btn approval_on_hold"
                                                add-class="btn-secondary" data-filter="5">Approval On Hold</button>
                                            <button class="btn my-1 btn-outline-success filter-btn approved"
                                                add-class="btn-success" data-filter="2">Approved</button>
                                            {{-- <button class="btn my-1 btn-outline-maroon filter-btn" add-class="btn-maroon"
                                                data-filter="6">Partially Signed</button> --}}
                                            <button class="btn my-1 btn-outline-lightblue filter-btn signed"
                                                add-class="btn-lightblue" data-filter="7">Signed</button>
                                            <button class="btn my-1 btn-outline-dark filter-btn expired"
                                                add-class="btn-dark" data-filter="8">Expired</button>
                                            <button class="btn my-1 btn-outline-danger filter-btn rejected"
                                                add-class="btn-danger" data-filter="3">Rejected</button>
                                        </div>
                                    </div>


                                    <table id="contractTable" class="table table-striped projects  display nowrap">
                                        <thead>
                                            <tr>
                                                <th style="width: 1%">#</th>
                                                <th style="width:112px;">Actions</th>
                                                <th>Project</th>
                                                <th>Business type</th>
                                                <th>Status</th>
                                                <th>Company Name</th>
                                                <th>Total Units</th>
                                                <th>ROI %</th>
                                                <th>Profit</th>
                                                <th>Start date</th>
                                                <th>End date</th>
                                                <th>Vendor Name</th>
                                                <th>Property Name</th>
                                                <th>Area</th>
                                                <th>Locality</th>
                                                <th>Indirect Details</th>

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




            <div class="modal fade" id="modal-send-approval">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Approval comments</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="" id="ContractCommentsForm" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="contract_id" id="approval_contract_id">
                            <input type="hidden" name="_token" id="approval_token">
                            <input type="hidden" name="contract_status" id="approval_status">
                            <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
                            <div class="modal-body">
                                <div class="card-body">
                                    <div class="form-group row">
                                        <label for="inputEmail3" class="col-sm-3 col-form-label asterisk">Comments</label>
                                        <textarea name="comment" class="form-control" id="comment" cols="10" rows="5" required></textarea>
                                    </div>
                                </div>
                                <!-- /.card-body -->
                            </div>
                            <div class="modal-footer justify-content-between">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-info" onclick="sendForApproval()">Send</button>
                            </div>
                        </form>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
            <!-- /.modal -->


            <div class="modal fade" id="commentsModal">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Approval comments</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body" style="max-height:400px; overflow-y:auto;">
                            <ul id="commentsList" class="list-group"></ul>

                            {{-- @if ($comments->isNotEmpty())
                                @foreach ($comments as $comment)
                                    <!-- Post -->
                                    <div class="post clearfix" style="width: 100%">
                                        <div class="user-block">
                                            <img class="img-circle img-bordered-sm"
                                                src="{{ $comment->user->profile_path ? asset('storage/' . $comment->user->profile_path) : asset('img copy/avatar.png') }}"
                                                alt="User Image">
                                            <span class="username">
                                                {{ $comment->user->first_name }}
                                                {{ $comment->user->last_name }}
                                            </span>
                                            <span class="description">Date -
                                                {{ $comment->created_at }}</span>
                                        </div>
                                        <!-- /.user-block -->
                                        <p>
                                            {{ $comment->comment }}
                                        </p>

                                    </div>
                                    <!-- /.post -->
                                @endforeach
                            @else
                                <div class="post clearfix"> No Comments ... </div>
                            @endif --}}
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

            <div class="modal fade" id="modal-upload">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Upload Documents</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="javascript:void(0)" id="ContractUploadForm" method="POST"
                            enctype="multipart/form-data">
                            <input type="hidden" name="contract_id" id="contract_id_upload">
                            <div class="modal-body">
                                <div class="card-body">
                                    <div class="form-group row">
                                        <div class="col-9 pr-1">
                                            <input type="hidden" name="0[document_type]" value="1">
                                            <input type="hidden" name="0[status_change]"
                                                value="is_vendor_contract_uploaded">
                                            <label for="inputEmail3" class="col-form-label">Vendor Contract</label>
                                            <input type="file" name="0[file]" class="form-control"
                                                accept=".pdf,image/*">
                                        </div>
                                        <div class="col-3">
                                            <span class="float-right mt-31">
                                                <div class="icheck-success d-inline">
                                                    <input type="checkbox" id="signed" name="0[signed_contract]"
                                                        class="signedContract" value="1">
                                                    <label class="labelpermission" for="signed"> Signed </label>
                                                </div>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="modal-footer justify-content-between">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                <button type="submit" id="importBtn" class="btn btn-info">Upload</button>
                            </div>
                        </form>
                    </div>
                    <!-- /.modal-content -->
                </div>
            </div>

            <div class="modal fade" id="modal-terminate-contract">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Terminate Contract</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="" id="ContractTerminateForm" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="contract_id" id="terminating_contract_id">
                            <input type="hidden" name="_token" id="terminate_token">
                            <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
                            <div class="modal-body">
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Terminated Date</label>
                                        <div class="input-group date" id="terminatedate" data-target-input="nearest">
                                            <input type="text" class="form-control datetimepicker-input"
                                                name="terminated_date" data-target="#terminatedate"
                                                placeholder="dd-mm-YYYY" required />
                                            <div class="input-group-append" data-target="#terminatedate"
                                                data-toggle="datetimepicker">
                                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Reason</label>
                                        <textarea name="terminated_reason" id="" cols="10" rows="5" class="form-control" required></textarea>
                                    </div>
                                    <div class="form-group clrngamnt">
                                        <label for="exampleInputEmail1">Balance Amount</label>
                                        <input type="number" class="form-control" name="balance_amount"
                                            id="balance_amount" placeholder="Balance Amount" required min="0"
                                            step="1">
                                        {{-- </div>
                                    <div class="form-group row"> --}}
                                        <div class="icheck-primary">
                                            <input type="checkbox" id="received" value="1"
                                                name="balance_received">
                                            <label for="received">Received</label>
                                        </div>
                                    </div>

                                    <div class="form-group row paymentModeChecks">
                                        @foreach ($paymentmodes as $paymentmode)
                                            <div
                                                class="icheck-primary mx-2 {{ $paymentmode->id == 2 ? 'bank' : ($paymentmode->id == 3 ? 'chq' : '') }}">
                                                <input type="checkbox" id="mode{{ $paymentmode->id }}"
                                                    class="modeChange" value="{{ $paymentmode->id }}" name="paid_mode"
                                                    required>
                                                <label for="mode{{ $paymentmode->id }}">
                                                    {{ $paymentmode->payment_mode_name }}
                                                </label>
                                            </div>
                                        @endforeach
                                        {{-- <div class="icheck-primary bank">
                                            <input type="checkbox" id="bankcheque" class="modeChange"
                                                value="{{ $paymentmodes->where('id', 2)->first()->id ?? '' }}"
                                                name="paid_mode">
                                            <label
                                                for="bankcheque">{{ $paymentmodes->where('id', 2)->first()->payment_mode_name ?? '' }}
                                            </label>
                                        </div>

                                        <div class="icheck-primary chq">
                                            <input type="checkbox" id="radioPrimary2" class="modeChange"
                                                value="{{ $paymentmodes->where('id', 3)->first()->id ?? '' }}"
                                                name="paid_mode">
                                            <label
                                                for="radioPrimary2">{{ $paymentmodes->where('id', 3)->first()->payment_mode_name ?? '' }}
                                            </label>
                                        </div> --}}
                                    </div>
                                    <div class="form-group companyTerminate">
                                        <label for="exampleInputEmail1">Company Name</label>
                                        <select class="form-control select2" name="company_id" id="paid_company"
                                            required>
                                            <option value="">Select Company</option>
                                            @foreach ($companies as $company)
                                                <option value="{{ $company->id }}">{{ $company->company_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group bankTerminate">
                                        <label for="exampleInputEmail1">Bank Name</label>
                                        <select class="form-control select2 bank_name" name="paid_bank" id="bank_name"
                                            required>
                                            <option value="">Select Bank</option>

                                        </select>
                                    </div>

                                    <div class="form-group chequeTerminate">
                                        <label for="exampleInputEmail1">Cheque No</label>
                                        <input type="text" class="form-control cheque_no" id="cheque_no"
                                            name="paid_cheque_number" placeholder="Cheque No" required>
                                    </div>
                                </div>
                                <!-- /.card-body -->
                            </div>
                            <div class="modal-footer justify-content-between">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-info"
                                    onclick="terminateContract()">Terminate</button>
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
    <!-- /.content-wrapper -->
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
    <script src="{{ asset('assets/bs-stepper/js/bs-stepper.min.js') }}"></script>

    <script>
        $('#terminatedate').datetimepicker({
            format: 'DD-MM-YYYY',
            allowInputToggle: true
        });

        $(function() {
            let table = $('#contractTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,

                ajax: {
                    url: "{{ route('contract.list') }}",
                    data: function(d) {
                        filre: 'require'
                        // d.company_id = $('#companyFilter').val();
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
                    {
                        data: 'project_number',
                        name: 'contracts.project_number',
                    },
                    {
                        data: 'business_type',
                        name: 'contract_units.business_type',
                    },
                    {
                        data: 'status',
                        name: 'contracts.contract_status',
                        // render: function(data, type, row) {
                        //     let badgeClass = '';
                        //     let text = '';

                        //     switch (data) {
                        //         case 0:
                        //             badgeClass = 'badge badge-warning';
                        //             text = 'Pending';
                        //             break;
                        //         case 1:
                        //             badgeClass = 'badge badge-info text-white';
                        //             text = 'Processing';
                        //             break;
                        //         case 2:
                        //             badgeClass = 'badge badge-success text-white';
                        //             text = 'Approved';
                        //             break;
                        //         case 3:
                        //             badgeClass = 'badge badge-danger text-white';
                        //             text = 'Terminated';
                        //             break;
                        //     }

                        //     return '<span class="' + badgeClass + '">' + text + '</span>';
                        // },
                    },
                    {
                        data: 'company_name',
                        name: 'companies.company_name',
                    },
                    {
                        data: 'no_of_units',
                        name: 'contract_units.no_of_units',
                    },
                    {
                        data: 'roi_perc',
                        name: 'contract_rentals.roi_perc',
                    },
                    {
                        data: 'expected_profit',
                        name: 'contract_rentals.expected_profit',
                    },
                    {
                        data: 'start_date',
                        name: 'contract_details.start_date',
                    },
                    {
                        data: 'end_date',
                        name: 'contract_details.end_date',
                    },
                    {
                        data: 'vendor_name',
                        name: 'vendors.vendor_name',
                    },
                    {
                        data: 'property_name',
                        name: 'properties.property_name',
                    },
                    {
                        data: 'area_name',
                        name: 'areas.area_name',
                    },
                    {
                        data: 'locality_name',
                        name: 'localities.locality_name',
                    },
                    {
                        data: 'indirect_project',
                        name: 'indirect_project',

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
                        let url = "{{ route('contract.export') }}" + "?search=" +
                            encodeURIComponent(searchValue);
                        window.location.href = url;
                    }
                }]
            });

            @if ($permission == false)
                $(document).ready(function() {
                    $('.approvalPending').click();
                });
            @endif

            $(document).ready(function() {

                // Get the filter value from the URL
                const urlParams = new URLSearchParams(window.location.search);
                const filterValue = urlParams.get('filter');

                if (filterValue) {
                    // Trigger the corresponding filter button
                    const targetButton = $('.' + filterValue);
                    console.log(targetButton);
                    if (targetButton.length) {
                        targetButton.click();
                    }
                }

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
                table.column(4).search(filterValue).draw();
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
                        url: '/contract/' + id,
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        dataType: "json",
                        success: function(response) {
                            toastr.success(response.message);
                            $('#contractTable').DataTable().ajax.reload();
                        }
                    });

                } else {
                    toastr.error(errors.responseJSON.message);
                }
            });
        }

        function sendForApproval() {
            const form = document.getElementById("ContractCommentsForm");
            var fdata = new FormData(form);



            let isValid = true;
            $(".error-text").remove(); // clear old errors
            $(form).find("[required]:visible").each(function() {
                const value = $(this).val()?.trim();

                if (!value) {
                    isValid = false;
                    setInvalid(this);
                } else {
                    setValid(this);
                }
            });


            if (!isValid) {
                toastr.error('Please fill all required fields before submitting.');
                return;
            }

            Swal.fire({
                title: "Are you sure?",
                text: "The contract is ready to approve!",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, send!"
            }).then((result) => {
                if (result.isConfirmed) {
                    showLoader();
                    $.ajax({
                        type: "POST",
                        url: '/contract-send-for-approval',
                        data: fdata,
                        dataType: "json",
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            toastr.success(response.message);
                            $('#modal-send-approval').modal('hide');
                            $('#modal-send-approval').find('input, textarea, select').val('');

                            $('#contractTable').DataTable().ajax.reload();
                            hideLoader();
                        }
                    });
                }
            });
        }

        $('#modal-send-approval').on('show.bs.modal', function(event) {
            var button = event.relatedTarget;

            // values from button
            var id = button.getAttribute('data-id');

            // set to modal fields
            $('#approval_contract_id').val(id);
            $('#approval_token').val($('meta[name="csrf-token"]').attr('content'));
            $('#approval_status').val(4);
        });
    </script>

    <script>
        $(document).on('click', '.loadComments', function(e) {
            e.preventDefault();

            let contractId = $(this).data('id');
            $('#commentsList').html('<li class="list-group-item">Loading...</li>');

            $.get('/contracts/' + contractId + '/comments', function(response) {

                $('#commentsList').empty();

                if (response.comments.length === 0) {
                    $('#commentsList').append('<li class="list-group-item">No comments found.</li>');
                } else {
                    response.comments.forEach(function(comment) {
                        $('#commentsList').append(`
                            <li class="list-group-item">
                                <div class="post clearfix" style="width: 100%">
                                    <div class="user-block">
                                        <img class="img-circle img-bordered-sm"
                                            src="${ comment.user?.profile_path ? '/storage/'+comment.user?.profile_path : '/img/avatar.png' }"
                                            alt="User Image">
                                        <span class="username">
                                            ${ comment.user?.first_name }
                                            ${ comment.user?.last_name ?? '' }
                                        </span>
                                        <span class="description">Date -
                                            ${ comment.created_at }</span>
                                    </div>
                                    <!-- /.user-block -->
                                    <p>
                                        ${ comment.comment }
                                    </p>
                                </div>
                            </li>
                        `);

                        // <li class="list-group-item">
                        //         <strong>${comment.user}</strong>
                        //         <br>
                        //         ${comment.comment}
                        //         <br>
                        //         <small class="text-muted">${comment.created_at}</small>
                        //     </li>
                    });
                }

                $('#commentsModal').modal('show');
            });
        });


        $('#modal-upload').on('show.bs.modal', function(event) {
            var button = event.relatedTarget;

            // values from button
            var id = button.getAttribute('data-id');

            // set to modal fields
            $('#contract_id_upload').val(id);
        });
    </script>

    <script>
        $(document).ready(function() {
            hidelemnetsonload();
        });

        $('#received').on('change', function() {
            if ($(this).prop('checked')) {
                $('.paymentModeChecks').show();
            } else {
                $('.paymentModeChecks').hide();
                $('.bankTerminate').hide();
                $('.companyTerminate').hide();
                $('.chequeTerminate').hide();
            }
        });

        $('.modeChange').on('change', function() {

            $('.modeChange').not(this).prop('checked', false);

            let mode = $('.modeChange:checked').val();

            if (mode == 2 || mode == 3) {
                $('.companyTerminate').show();
                $('.bankTerminate').show();
                if (mode == 3) {
                    $('.chequeTerminate').show();
                } else {
                    $('.chequeTerminate').hide();
                }
            } else {
                $('.companyTerminate').hide();
                $('.bankTerminate').hide();
                $('.chequeTerminate').hide();
            }
        });

        function hidelemnetsonload() {
            $('.paymentModeChecks, .companyTerminate, .bankTerminate, .chequeTerminate').hide();
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

        $('#modal-terminate-contract').on('show.bs.modal', function(event) {
            var button = event.relatedTarget;

            // values from button
            var id = button.getAttribute('data-id');

            // set to modal fields
            $('#terminating_contract_id').val(id);
            $('#terminate_token').val($('meta[name="csrf-token"]').attr('content'));
        });


        function terminateContract() {

            const form = document.getElementById("ContractTerminateForm");
            var fdata = new FormData(form);

            let isValid = true;
            $(".error-text").remove(); // clear old errors
            $(form).find("[required]:visible").each(function() {
                const value = $(this).val()?.trim();

                if (!value) {
                    isValid = false;
                    setInvalid(this);
                } else {
                    setValid(this);
                }
            });

            // Validate Select2 fields
            $(form).find('[required]select.select2').each(function() {
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

            // Validate payment mode (iCheck checkboxes)
            if ($('.modeChange:checked').length === 0) {
                isValid = false;
                toastr.error('Please select at least one payment mode.');
            }


            if (!isValid) {
                toastr.error('Please fill all required fields before submitting.');
                return;
            }

            Swal.fire({
                title: "Are you sure?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, Terminate!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/contracts/terminate',
                        type: 'POST',
                        data: fdata,
                        processData: false,
                        contentType: false,
                        success: function(res) {
                            toastr.success(res.message);
                            location.reload();
                        },
                        error: function(err) {
                            toastr.error('Something went wrong');
                        }
                    });

                } else {
                    toastr.error(errors.responseJSON.message);
                }
            });
        }
    </script>
    @include('admin.projects.contract.includes.contract_document_js')
@endsection
