@extends('admin.layout.admin_master')

@section('custom_css')
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
                        
                        {{-- Filter --}}
                        <div class="card card-df card-outline">
                            <div class="card-header shadow-sm">
                                <h5 class="card-title mb-0">Filter</h5>
                            </div>

                            <div class="d-flex justify-content-end mx-4 my-2">
                                <button type="button" class="btn btn-secondary reset">
                                    <i class="fa fa-undo-alt"></i> Reset
                                </button>
                            </div>

                            <form class="filterform">
                                <div class="card-body">
                                    <div class="row align-items-end">

                                        {{-- Company --}}
                                        <div class="col-lg-3 col-md-4">
                                            <label for="companySelect">Select Company</label>
                                            <select class="form-control select2"
                                                    id="companySelect"
                                                    name="company_id">
                                                <option value="">All Companies</option>

                                                @foreach ($companies->where('industry_id', 1) as $com)
                                                    <option value="{{ $com->id }}">
                                                        {{ $com->company_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        {{-- From Date --}}
                                        <div class="col-md-2">
                                            <label for="date_From">From</label>
                                            <div class="input-group date"
                                                id="dateFrom"
                                                data-target-input="nearest">
                                                <input type="text"
                                                    class="form-control datetimepicker-input"
                                                    data-target="#dateFrom"
                                                    id="date_From"
                                                    placeholder="dd-mm-YYYY"
                                                    autocomplete="off">
                                                <div class="input-group-append"
                                                    data-target="#dateFrom"
                                                    data-toggle="datetimepicker">
                                                    <div class="input-group-text">
                                                        <i class="fa fa-calendar"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- To Date --}}
                                        <div class="col-md-2">
                                            <label for="date_To">To</label>
                                            <div class="input-group date"
                                                id="dateTo"
                                                data-target-input="nearest">
                                                <input type="text"
                                                    class="form-control datetimepicker-input"
                                                    data-target="#dateTo"
                                                    id="date_To"
                                                    placeholder="dd-mm-YYYY"
                                                    autocomplete="off">
                                                <div class="input-group-append"
                                                    data-target="#dateTo"
                                                    data-toggle="datetimepicker">
                                                    <div class="input-group-text">
                                                        <i class="fa fa-calendar"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Project --}}
                                        <div class="col-lg-3 col-md-4">
                                            <label for="projectSelect">Select Project</label>
                                            <select class="form-control select2"
                                                    id="projectSelect"
                                                    name="contract_id">
                                                <option value="">All Projects</option>
                                            </select>
                                        </div>

                                        {{-- Search --}}
                                        <div class="mt-3 col-lg-3 col-md-4 ml-auto">
                                            <button type="button"
                                                    class="btn btn-primary btn-block searchbtn">
                                                <i class="fa fa-search"></i>
                                                Search
                                            </button>
                                        </div>

                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <!-- <h3 class="card-title">Contract Details</h3> -->
                                <span class="float-right">
                                    @if (auth()->user()->hasPermissionInRange(56, 64))
                                        <a href="{{ route('contract.index') }}" class="btn btn-info float-right m-1">
                                            Contract list
                                        </a>
                                    @endif
                                    {{-- <button class="btn btn-secondary float-right m-1" data-toggle="modal"
                                        data-target="#modal-import">Import</button> --}}
                                </span>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <table id="contractTable" class="table table-striped projects ">
                                    <thead>
                                        <tr>
                                            <th style="width: 1%">#</th>
                                            <th>Project No</th>
                                            <th>Contract Type</th>
                                            <th>Company Name</th>
                                            <th>Total No of units</th>
                                            <th>ROI</th>
                                            <th>Profit</th>
                                            <th>Expiry date</th>
                                            <th></th>
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




            <div class="modal fade" id="modal-reject">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Reject Renewal</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="" id="RenewalrejectionForm" method="POST" enctype="multipart/form-data">
                            <div class="modal-body">
                                <div class="card-body">
                                    <input type="hidden" id="contract_id" name="contract_id">
                                    <input type="hidden" id="reject_url" name="reject_url">
                                    <div class="form-group row">
                                        <label for="inputEmail3" class="form-label">Rejection Reason</label>
                                        <textarea name="renew_reject_reason" class="form-control" id="renew_reject_reason" cols="20" rows="5"
                                            required></textarea>
                                    </div>
                                </div>
                                <!-- /.card-body -->
                            </div>
                            <div class="modal-footer justify-content-between">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                <button type="button" id="rejectSubmitBtn" class="btn btn-info">Submit</button>
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
        // $('#dateFrom').datetimepicker({
        //     format: 'DD-MM-YYYY'
        // });

        // $('#dateTo').datetimepicker({
        //     format: 'DD-MM-YYYY'
        // });
        // $(function() {
        //     let table = $('#contractTable').DataTable({
        //         processing: true,
        //         serverSide: true,

        //         ajax: {
        //             url: "{{ route('contract.renewal_list') }}",
        //             data: function (d) {
        //                 d.companyId = $('#companySelect').val();
        //                 d.contractId = $('#projectSelect').val();
        //                 d.startDate = $('#date_From').val();
        //                 d.endDate = $('#date_To').val();
        //             }
        //         },
        //         columns: [{
        //                 data: 'DT_RowIndex',
        //                 name: 'contracts.id',
        //                 orderable: true,
        //                 searchable: false
        //             },
        //             {
        //                 data: 'project_number',
        //                 name: 'contracts.project_number',
        //             },
        //             {
        //                 data: 'contract_type',
        //                 name: 'contract_types.contract_type',
        //             },
        //             {
        //                 data: 'company_name',
        //                 name: 'companies.company_name',
        //             },
        //             {
        //                 data: 'no_of_units',
        //                 name: 'contract_units.no_of_units',
        //             },
        //             {
        //                 data: 'roi_perc',
        //                 name: 'contract_rentals.roi_perc',
        //             },
        //             {
        //                 data: 'expected_profit',
        //                 name: 'contract_rentals.expected_profit',
        //             },
        //             {
        //                 data: 'end_date',
        //                 name: 'contract_details.end_date',
        //             },
        //             // {
        //             //     data: 'status',
        //             //     name: 'contracts.contract_status',
        //             //     render: function(data, type, row) {
        //             //         let badgeClass = '';
        //             //         let text = '';

        //             //         switch (data) {
        //             //             case 0:
        //             //                 badgeClass = 'badge badge-warning';
        //             //                 text = 'Pending';
        //             //                 break;
        //             //             case 1:
        //             //                 badgeClass = 'badge badge-info text-white';
        //             //                 text = 'Processing';
        //             //                 break;
        //             //             case 2:
        //             //                 badgeClass = 'badge badge-success text-white';
        //             //                 text = 'Approved';
        //             //                 break;
        //             //             case 3:
        //             //                 badgeClass = 'badge badge-danger text-white';
        //             //                 text = 'Terminated';
        //             //                 break;
        //             //         }

        //             //         return '<span class="' + badgeClass + '">' + text + '</span>';
        //             //     },
        //             // },

        //             {
        //                 data: 'action',
        //                 name: 'action',
        //                 orderable: false,
        //                 searchable: false
        //             },
        //         ],
        //         order: [
        //             [0, 'desc']
        //         ],
        //         dom: 'Bfrtip', // This is important for buttons
        //         buttons: [{
        //             extend: 'excelHtml5',
        //             text: 'Export Excel',
        //             title: 'Contract Data',
        //             action: function(e, dt, node, config) {
        //                 // redirect to your Laravel export route
        //                 let searchValue = dt.search();
        //                 let url = "{{ route('contract.export') }}" + "?search=" +
        //                     encodeURIComponent(searchValue);
        //                 window.location.href = url;
        //             }
        //         }]
        //     });
        // });

        // $(document).on('click', '.openRejectModalBtn', function(e) {
        //     e.preventDefault();

        //     // Get data from button
        //     const id = $(this).data('id');

        //     // (Optional) Load modal content dynamically
        //     $('#contract_id').val(id);
        //     $('#reject_url').val($(this).data('url'));

        //     // Manually show the modal
        //     const myModal = new bootstrap.Modal(document.getElementById('modal-reject'));
        //     myModal.show();
        // });


        // $('#rejectSubmitBtn').click(function(e) {
        //     e.preventDefault();

        //     if ($('#renew_reject_reason').val() == '') {
        //         toastr.error('Please provide reason for Renewal');
        //         return;
        //     }

        //     const url = $('#reject_url').val();
        //     var form = document.getElementById('RenewalrejectionForm');
        //     var fdata = new FormData(form);
        //     fdata.append('_token', $('meta[name="csrf-token"]').attr('content'));

        //     $.ajax({
        //         type: "POST",
        //         url: url,
        //         data: fdata,
        //         dataType: "json",
        //         processData: false,
        //         contentType: false,
        //         success: function(response) {
        //             // console.log(response);
        //             toastr.success(response.message);
        //             window.location.href = "{{ route('contract.renewal_pending_list') }}";
        //         },
        //         error: function(errors) {
        //             toastr.error(errors.responseJSON.message);
        //         }
        //     });
        // });

        // const contracts = @json($contracts);

        // $(function () {

        //     $('#companySelect, #projectSelect').select2({
        //         theme: 'bootstrap4',
        //         width: '100%'
        //     });

        //     $('#companySelect').on('change', function () {

        //         const companyId = $(this).val();
        //         const projectSelect = $('#projectSelect');

        //         // Reset project dropdown
        //         projectSelect.empty();

        //         if (!companyId) {
        //             projectSelect.append(
        //                 '<option value="">All Projects</option>'
        //             );
        //         } else {
        //             projectSelect.append(
        //                 '<option value="">Select Project</option>'
        //             );

        //             const companyContracts = contracts.filter(function (contract) {
        //                 return String(contract.company_id) === String(companyId);
        //             });

        //             companyContracts.forEach(function (contract) {
        //                 projectSelect.append(
        //                     $('<option>', {
        //                         value: contract.id,
        //                         text: contract.project_number
        //                     })
        //                 );
        //             });
        //         }

        //         // Refresh Select2 display
        //         projectSelect.val('').trigger('change.select2');
        //     });

        // });
        // $('.searchbtn').on('click', function (e) {
        //     e.preventDefault();
        //     table.ajax.reload();
        // });

        // // Reset button
        // $('.reset').on('click', function (e) {
        //     e.preventDefault();

        //     $('#companySelect').val('').trigger('change');
        //     $('#projectSelect').html('<option value="">All Projects</option>')
        //         .trigger('change');

        //     $('#date_From').val('');
        //     $('#date_To').val('');

        //     table.ajax.reload();
        // });

        // function deleteConf(id) {
        //     Swal.fire({
        //         title: "Are you sure?",
        //         icon: "warning",
        //         showCancelButton: true,
        //         confirmButtonColor: "#3085d6",
        //         cancelButtonColor: "#d33",
        //         confirmButtonText: "Yes, delete it!"
        //     }).then((result) => {
        //         if (result.isConfirmed) {
        //             $.ajax({
        //                 type: "DELETE",
        //                 url: '/contract/' + id,
        //                 data: {
        //                     _token: $('meta[name="csrf-token"]').attr('content')
        //                 },
        //                 dataType: "json",
        //                 success: function(response) {
        //                     toastr.success(response.message);
        //                     $('#contractTable').DataTable().ajax.reload();
        //                 }
        //             });

        //         } else {
        //             toastr.error(errors.responseJSON.message);
        //         }
        //     });
        // }

        
$(function () {
    // Initialize date pickers
    $('#dateFrom, #dateTo').datetimepicker({
        format: 'DD-MM-YYYY'
    });

    // Initialize Select2
    $('#companySelect, #projectSelect').select2({
        theme: 'bootstrap4',
        width: '100%'
    });

    const contracts = @json($contracts);

    // Populate projects based on selected company
    $('#companySelect').on('change', function () {
        const companyId = $(this).val();
        const projectSelect = $('#projectSelect');

        projectSelect.empty();

        projectSelect.append(
            $('<option>', {
                value: '',
                text: companyId ? 'Select Project' : 'All Projects'
            })
        );

        if (companyId) {
            const companyContracts = contracts.filter(function (contract) {
                return String(contract.company_id) === String(companyId);
            });

            companyContracts.forEach(function (contract) {
                projectSelect.append(
                    $('<option>', {
                        value: contract.id,
                        text: contract.project_number
                    })
                );
            });
        }

        projectSelect.val('').trigger('change.select2');
    });

    // Initialize DataTable
    const table = $('#contractTable').DataTable({
        processing: true,
        serverSide: true,

        ajax: {
            url: "{{ route('contract.renewal_list') }}",
            data: function (d) {
                d.companyId = $('#companySelect').val();
                d.contractId = $('#projectSelect').val();
                d.startDate = $('#date_From').val();
                d.endDate = $('#date_To').val();
            }
        },

        columns: [
            {
                data: 'DT_RowIndex',
                name: 'contracts.id',
                orderable: false,
                searchable: false
            },
            {
                data: 'project_number',
                name: 'contracts.project_number'
            },
            {
                data: 'contract_type',
                name: 'contract_types.contract_type'
            },
            {
                data: 'company_name',
                name: 'companies.company_name'
            },
            {
                data: 'no_of_units',
                name: 'contract_units.no_of_units',
                orderable: false
            },
            {
                data: 'roi_perc',
                name: 'contract_rentals.roi_perc',
                orderable: false
            },
            {
                data: 'expected_profit',
                name: 'contract_rentals.expected_profit',
                orderable: false
            },
            {
                data: 'end_date',
                name: 'contract_details.end_date'
            },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            }
        ],

        order: [[0, 'desc']],
        dom: 'Bfrtip',

        buttons: [
            {
                extend: 'excelHtml5',
                text: 'Export Excel',
                title: 'Contract Data',
                action: function (e, dt) {
                    const searchValue = dt.search();

                    const url = "{{ route('contract.export') }}" +
                        "?search=" + encodeURIComponent(searchValue);

                    window.location.href = url;
                }
            }
        ]
    });

    // Search button: reload table with selected filters
    $('.searchbtn').on('click', function (e) {
        e.preventDefault();
        table.ajax.reload();
    });

    // Reset filters
    $('.reset').on('click', function (e) {
        e.preventDefault();

        $('#companySelect').val('').trigger('change');

        $('#projectSelect')
            .empty()
            .append('<option value="">All Projects</option>')
            .val('')
            .trigger('change.select2');

        $('#date_From').val('');
        $('#date_To').val('');

        // Clear DataTables' built-in search box too
        table.search('');

        table.ajax.reload();
    });
});

    </script>
@endsection
