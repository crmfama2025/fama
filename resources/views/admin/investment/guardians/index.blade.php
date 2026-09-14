@extends('admin.layout.admin_master')

@section('custom_css')
    <link rel="stylesheet" href="{{ asset('assets/daterangepicker/daterangepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
@endsection

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>{{ $title }}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"> <a href="{{ route('dashboard.index') }}">Home</a> </li>
                            <li class="breadcrumb-item active">{{ $title }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>


        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <span class="float-right">
                                    @if (auth()->user()->hasAnyPermission(['investor-guardian.add']))
                                        <button type="button" class="btn btn-info float-right m-1" id="addGuardianBtn"
                                            data-toggle="modal" data-target="#addGuardianModal">
                                            Add Guardian
                                        </button>
                                    @endif
                                </span>
                            </div>
                            <div class="card-body">
                                <table id="InvestorGuardianList" class="table table-striped table-hover display nowrap">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Action</th>
                                            <th>Guardian Code</th>
                                            <th>Guardian Details</th>
                                            <th>Emirates ID</th>
                                            <th>EID Expiry</th>
                                            <th>Passport No</th>
                                            <th>Passport Expiry</th>
                                            <th>Added By</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
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
    <script src="{{ asset('assets/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('assets/datatables-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('assets/datatables-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('assets/datatables-buttons/js/buttons.colVis.min.js') }}"></script>

    <script>
        $(function() {
            let table = $('#InvestorGuardianList').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,

                ajax: {
                    url: "{{ route('investor-guardian.list') }}",
                    data: function(d) {
                        // Add filters here if required
                    }
                },

                columns: [{
                        data: 'DT_RowIndex',
                        name: 'id',
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
                        data: 'investor_guardian_code',
                        name: 'investor_guardian_code'
                    },
                    {
                        data: 'guardian_name',
                        name: 'guardian_name'
                    },
                    {
                        data: 'emirates_id_number',
                        name: 'emirates_id_number'
                    },
                    {
                        data: 'eid_expiry_date',
                        name: 'eid_expiry_date'
                    },
                    {
                        data: 'passport_number',
                        name: 'passport_number'
                    },
                    {
                        data: 'passport_expiry_date',
                        name: 'passport_expiry_date'
                    },
                    {
                        data: 'added_by',
                        name: 'added_by'
                    }
                ],

                order: [
                    [0, 'desc']
                ],


            });
        });

        function deleteConf(id) {
            Swal.fire({
                title: "Are you sure?",
                text: "This guardian will be deleted.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "DELETE",
                        url: "{{ route('investor-guardian.destroy', ':id') }}".replace(':id', id),
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        dataType: "json",
                        success: function(response) {
                            toastr.success(response.message);
                            $('#InvestorGuardianList').DataTable().ajax.reload();
                        },
                        error: function(xhr) {
                            toastr.error(
                                xhr.responseJSON?.message ?? 'Something went wrong.'
                            );
                        }
                    });
                }
            });
        }

        $(document).on('click', '.editGuardian', function() {
            let id = $(this).data('id');

            $('#addGuardianForm')[0].reset();

            $('#modal_guardian_id').val(id);

            $('#guardianModalTitle').html(`
                <i class="fas fa-user-edit mr-1"></i>
                Edit Guardian
            `);

            $('#guardianSubmitBtn').html(`
                <i class="fas fa-save mr-1"></i>
                Update Guardian
            `);

            // Files are optional during edit
            $('#emirates_id_copy').prop('required', false);
            $('#passport_copy').prop('required', false);
            $('#emiratesIdCopyLabel').removeClass('asterisk');
            $('#passportCopyLabel').removeClass('asterisk');

            $('#currentEmiratesIdCopy').html('');
            $('#currentPassportCopy').html('');

            $.ajax({
                url: "{{ route('investor-guardian.edit', ':id') }}".replace(':id', id),
                type: "GET",

                beforeSend: function() {
                    $('#guardianSubmitBtn').prop('disabled', true);
                },

                success: function(response) {

                    let guardian = response.guardian;

                    $('#guardian_name').val(guardian.guardian_name);
                    $('#guardian_name_arabic').val(guardian.guardian_name_arabic);
                    $('#guardian_mobile').val(guardian.guardian_mobile);
                    $('#guardian_email').val(guardian.guardian_email);

                    $('#emirates_id_number').val(guardian.emirates_id_number);
                    $('#eid_expiry_date').val(guardian.eid_expiry_date);

                    $('#passport_number').val(guardian.passport_number);
                    $('#passport_expiry_date').val(guardian.passport_expiry_date);

                    if (guardian.emirates_id_copy) {
                        $('#currentEmiratesIdCopy').html(`
                    Current file:
                    <a href="${guardian.emirates_id_copy}" target="_blank">
                        View Emirates ID
                    </a>
                `);
                    }

                    if (guardian.passport_copy) {
                        $('#currentPassportCopy').html(`
                    Current file:
                    <a href="${guardian.passport_copy}" target="_blank">
                        View Passport
                    </a>
                `);
                    }

                },

                error: function() {
                    toastr.error('Unable to load guardian details.');
                    $('#addGuardianModal').modal('hide');
                },

                complete: function() {
                    $('#guardianSubmitBtn').prop('disabled', false);
                }
            });
        });
    </script>
    @include('admin.investment.add-guardian-modal')
@endsection
