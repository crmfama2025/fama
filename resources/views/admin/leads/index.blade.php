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
    <style>
        .requirement-text {
            max-width: 250px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: block;
        }
    </style>
@endsection

@section('content')
    <!-- Content Wrapper -->
    <div class="content-wrapper">

        <!-- Content Header -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">

                    <div class="col-sm-6">
                        <h1>{{ $title }}</h1>
                    </div>

                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard.index') }}">Home</a>
                            </li>
                            <li class="breadcrumb-item active">
                                Lead
                            </li>
                        </ol>
                    </div>

                </div>
            </div>
        </section>

        <!-- Main content -->
        <section class="content">

            <div class="container-fluid">

                <div class="card card-df card-outline">

                    <div class="card-header shadow-sm">
                        <h5 class="card-title mb-0">Filter</h5>
                    </div>

                    <div class="d-flex justify-content-end mx-4 my-2">
                        <button type="button" class="btn btn-secondary reset">
                            <i class="fa fa-undo-alt"></i> Reset
                        </button>
                    </div>

                    <div class="card-body">

                        <form class="form-row align-items-end filterform">

                            {{-- Follow Up Status --}}
                            <div class="form-group col-lg-3 col-md-4">
                                <label for="followUpStatusSelect">Follow Up Status</label>

                                <select class="form-control select2" id="followUpStatusSelect" name="follow_up_status">

                                    <option value="">All Status</option>

                                    {{-- Change these according to your actual statuses --}}
                                    <option value="0">Pending</option>
                                    <option value="1">Processing</option>
                                    <option value="2">Interested</option>
                                    <option value="3">Call Back</option>
                                    <option value="4">No Answer</option>
                                    <option value="5">Not Interested</option>
                                    <option value="6">Meeting Scheduled</option>
                                    <option value="7">Proposal Sent</option>
                                    <option value="8">Negotiation</option>
                                    <option value="9">Converted</option>
                                    <option value="10">Lost</option>
                                    <option value="11">Others</option>

                                </select>
                            </div>


                            {{-- Follow Up Type --}}
                            <div class="form-group col-lg-3 col-md-4">
                                <label for="leadSourceSelect">Lead Source</label>
                                <select class="form-control select2" id="leadSourceSelect" name="lead_source">
                                    <option value="">All Sources</option>
                                    <option value="WhatsApp">WhatsApp</option>
                                    <option value="Website">Website</option>
                                    <option value="Referral">Referral</option>
                                    <option value="Email">Email</option>
                                    <option value="Phone">Phone</option>
                                    <option value="Walk-in">Walk-in</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>


                            {{-- Follow Up Date From --}}
                            {{-- <div class="form-group col-lg-3 col-md-4">

                                <label for="followUpDateFrom">
                                    Follow Up Date From
                                </label>

                                <div class="input-group date" id="followUpDateFrom" data-target-input="nearest">

                                    <input type="text" class="form-control datetimepicker-input"
                                        data-target="#followUpDateFrom" placeholder="dd-mm-YYYY">

                                    <div class="input-group-append" data-target="#followUpDateFrom"
                                        data-toggle="datetimepicker">

                                        <div class="input-group-text">
                                            <i class="fa fa-calendar"></i>
                                        </div>

                                    </div>
                                </div>

                            </div> --}}


                            {{-- Follow Up Date To --}}
                            {{-- <div class="form-group col-lg-3 col-md-4">

                                <label for="followUpDateTo">
                                    Follow Up Date To
                                </label>

                                <div class="input-group date" id="followUpDateTo" data-target-input="nearest">

                                    <input type="text" class="form-control datetimepicker-input"
                                        data-target="#followUpDateTo" placeholder="dd-mm-YYYY">

                                    <div class="input-group-append" data-target="#followUpDateTo"
                                        data-toggle="datetimepicker">

                                        <div class="input-group-text">
                                            <i class="fa fa-calendar"></i>
                                        </div>

                                    </div>
                                </div>

                            </div> --}}


                            {{-- Next Follow Up From --}}
                            {{-- <div class="form-group col-lg-3 col-md-4">

                                <label for="nextFollowUpFrom">
                                    Next Follow Up From
                                </label>

                                <div class="input-group date" id="nextFollowUpFrom" data-target-input="nearest">

                                    <input type="text" class="form-control datetimepicker-input"
                                        data-target="#nextFollowUpFrom" placeholder="dd-mm-YYYY">

                                    <div class="input-group-append" data-target="#nextFollowUpFrom"
                                        data-toggle="datetimepicker">

                                        <div class="input-group-text">
                                            <i class="fa fa-calendar"></i>
                                        </div>

                                    </div>
                                </div>

                            </div> --}}


                            {{-- Next Follow Up To --}}
                            {{-- <div class="form-group col-lg-3 col-md-4">

                                <label for="nextFollowUpTo">
                                    Next Follow Up To
                                </label>

                                <div class="input-group date" id="nextFollowUpTo" data-target-input="nearest">

                                    <input type="text" class="form-control datetimepicker-input"
                                        data-target="#nextFollowUpTo" placeholder="dd-mm-YYYY">

                                    <div class="input-group-append" data-target="#nextFollowUpTo"
                                        data-toggle="datetimepicker">

                                        <div class="input-group-text">
                                            <i class="fa fa-calendar"></i>
                                        </div>

                                    </div>
                                </div>

                            </div> --}}


                            {{-- Followed Up By --}}
                            <div class="form-group col-lg-3 col-md-4">

                                <label for="followedUpBySelect">
                                    Assigned To
                                </label>

                                <select class="form-control select2" id="followedUpBySelect" name="followed_up_by">

                                    <option value="">All Salespersons</option>

                                    @foreach ($salesPersons as $salesPerson)
                                        <option value="{{ $salesPerson->id }}">
                                            {{ $salesPerson->first_name . ' ' . $salesPerson->last_name }}
                                        </option>
                                    @endforeach

                                </select>

                            </div>


                            {{-- Search --}}
                            <div class="form-group col-lg-2">

                                <button type="button" class="btn btn-primary btn-block searchbtn">

                                    <i class="fa fa-search"></i>
                                    Search

                                </button>

                            </div>

                        </form>

                    </div>

                </div>

                <div class="row">

                    <div class="col-12">

                        <div class="card">

                            <div class="card-header">

                                <span class="float-right">
                                    @if (auth()->user()->hasAnyPermission(['leads.add']))
                                        <a href="{{ route('lead.create') }}" class="btn btn-info float-right m-1">
                                            Add Lead
                                        </a>
                                    @endif

                                </span>

                            </div>
                            {{-- @endcan --}}

                            <!-- /.card-header -->
                            {{-- <div class="card-body border-bottom">
                                <div class="d-flex flex-wrap align-items-center">

                                    <span class="badge bg-info mr-2 mb-2 px-3 py-2">
                                        Processing <span id="countProcessing">0</span>
                                    </span>

                                    <span class="badge bg-success mr-2 mb-2 px-3 py-2">
                                        Interested <span id="countInterested">0</span>
                                    </span>

                                    <span class="badge bg-primary mr-2 mb-2 px-3 py-2">
                                        Call Back <span id="countCallBack">0</span>
                                    </span>

                                    <span class="badge bg-secondary mr-2 mb-2 px-3 py-2">
                                        No Answer <span id="countNoAnswer">0</span>
                                    </span>

                                    <span class="badge bg-danger mr-2 mb-2 px-3 py-2">
                                        Not Interested <span id="countNotInterested">0</span>
                                    </span>

                                    <span class="badge bg-purple mr-2 mb-2 px-3 py-2">
                                        Meeting Scheduled <span id="countMeeting">0</span>
                                    </span>

                                    <span class="badge bg-info mr-2 mb-2 px-3 py-2">
                                        Proposal Sent <span id="countProposal">0</span>
                                    </span>

                                    <span class="badge bg-warning text-dark mr-2 mb-2 px-3 py-2">
                                        Negotiation <span id="countNegotiation">0</span>
                                    </span>

                                    <span class="badge bg-success mr-2 mb-2 px-3 py-2">
                                        Converted <span id="countConverted">0</span>
                                    </span>

                                    <span class="badge bg-danger mr-2 mb-2 px-3 py-2">
                                        Lost <span id="countLost">0</span>
                                    </span>

                                    <span class="badge bg-dark mr-2 mb-2 px-3 py-2">
                                        Others <span id="countOthers">0</span>
                                    </span>

                                </div>
                            </div> --}}




                            <div class="card-body table-responsive">

                                <table id="leadsTable" class="table table-striped table-hover" style="width:100%">

                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Lead Code</th>
                                            <th>Company Name</th>
                                            <th>Contact Person</th>
                                            <th>Phone Number</th>
                                            <th>Email</th>
                                            <th>Lead Source</th>
                                            <th>Total Staff</th>
                                            <th>Required Location</th>
                                            {{-- <th>Requirement</th> --}}
                                            <th>Status</th>
                                            <th>Action</th>
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
    <!-- DataTables & Plugins -->
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

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.22.5/dist/sweetalert2.all.min.js"></script>


    <script>
        $('#followUpDateFrom').datetimepicker({
            format: 'DD-MM-YYYY'
        });

        $('#followUpDateTo').datetimepicker({
            format: 'DD-MM-YYYY'
        });

        $('#nextFollowUpFrom').datetimepicker({
            format: 'DD-MM-YYYY'
        });

        $('#nextFollowUpTo').datetimepicker({
            format: 'DD-MM-YYYY'
        });
        let table;
        $(function() {

            table = $('#leadsTable').DataTable({

                processing: true,
                serverSide: true,
                responsive: true,

                ajax: {
                    url: "{{ route('lead.list') }}",
                    // type: "GET",

                    data: function(d) {
                        // DataTables automatically sends search.value, start, length, order, etc.
                        d.follow_up_status = $('#followUpStatusSelect').val();
                        d.lead_source = $('#leadSourceSelect').val();
                        d.follow_up_date_from = $('#followUpDateFrom input').val();
                        d.follow_up_date_to = $('#followUpDateTo input').val();
                        d.next_follow_up_from = $('#nextFollowUpFrom input').val();
                        d.next_follow_up_to = $('#nextFollowUpTo input').val();
                        d.followed_up_by = $('#followedUpBySelect').val();
                    },

                },

                columns: [

                    {
                        data: 'DT_RowIndex',
                        name: 'leads.id',
                        orderable: true,
                        searchable: false
                    },
                    {
                        data: 'lead_code',
                        name: 'leads.lead_code'
                    },


                    {
                        data: 'company_name',
                        name: 'leads.company_name'
                    },

                    {
                        data: 'contact_person_name',
                        name: 'leads.contact_person_name'
                    },

                    {
                        data: 'phone_number',
                        name: 'leads.phone_number'
                    },

                    {
                        data: 'email',
                        name: 'leads.email'
                    },

                    {
                        data: 'lead_source',
                        name: 'leads.lead_source'
                    },

                    {
                        data: 'total_staff',
                        name: 'leads.total_staff'
                    },

                    {
                        data: 'required_location',
                        name: 'leads.required_location'
                    },

                    {
                        data: 'status',
                        name: 'leads.status',
                    },

                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }

                ],

                order: [
                    [0, 'desc']
                ],

                dom: 'Bfrtip',

                buttons: [

                    {
                        extend: 'excelHtml5',
                        text: '<i class="fas fa-file-excel"></i> Export Excel',
                        title: 'Lead Data',
                        className: 'btn btn-success',

                        action: function(e, dt, node, config) {
                            let params = {
                                search: dt.search(),

                                follow_up_status: $('#followUpStatusSelect').val(),
                                lead_source: $('#leadSourceSelect').val(),

                                follow_up_date_from: $('#followUpDateFrom input').val(),
                                follow_up_date_to: $('#followUpDateTo input').val(),

                                next_follow_up_from: $('#nextFollowUpFrom input').val(),
                                next_follow_up_to: $('#nextFollowUpTo input').val(),

                                followed_up_by: $('#followedUpBySelect').val()
                            };

                            let url = "{{ route('lead.export') }}" + '?' + $.param(params);

                            window.location.href = url;
                        }
                    }

                ]

            });
            $('.searchbtn').on('click', function() {

                table.ajax.reload(null, true);

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

                        url: "{{ route('lead.destroy', ':id') }}"
                            .replace(':id', id),

                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },

                        dataType: "json",

                        success: function(response) {

                            toastr.success(response.message);

                            $('#leadsTable')
                                .DataTable()
                                .ajax.reload(null, false);

                        },

                        error: function(error) {

                            toastr.error(
                                error.responseJSON?.message ??
                                'Something went wrong.'
                            );

                        }

                    });

                }

            });

        }

        $('.reset').click(function() {
            $('.filterform')[0].reset();

            $('#followUpStatusSelect').val(null).trigger('change');
            $('#leadSourceSelect').val(null).trigger('change');
            $('#followedUpBySelect').val(null).trigger('change');

            $('#followUpDateFrom input').val('');
            $('#followUpDateTo input').val('');
            $('#nextFollowUpFrom input').val('');
            $('#nextFollowUpTo input').val('');

            table.ajax.reload(null, true);
        });
    </script>
@endsection
