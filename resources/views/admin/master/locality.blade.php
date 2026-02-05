@extends('admin.layout.admin_master')

@section('custom_css')
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
                        <h1>Locality</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
                            <li class="breadcrumb-item active">Locality</li>
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
                            @can('locality.add')
                                <div class="card-header">
                                    <!-- <h3 class="card-title">Locality Details</h3> -->
                                    <span class="float-right">
                                        <button class="btn btn-info float-right m-1" data-toggle="modal"
                                            data-target="#modal-locality">Add Locality</button>
                                        <button class="btn btn-secondary float-right m-1" data-toggle="modal"
                                            data-target="#modal-import">Import</button>
                                    </span>
                                </div>
                            @endcan
                            <!-- /.card-header -->
                            <div class="card-body table-responsive">
                                <table id="localityTable" class="table table-striped table-hover" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            {{-- <th>Company Name</th> --}}
                                            <th>Area Name</th>
                                            <th>Locality Name</th>
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


            {{-- <div class="modal fade" id="modal-locality">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Locality</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="" id="localityForm">
                            @csrf
                            <input type="hidden" name="id" id="locality_id" value="0">
                            <div class="modal-body">
                                <div class="card-body">
                                    @if (auth()->user()->company_id)
                                        <input type="hidden" name="company_id" id="company_id"
                                            value="{{ auth()->user()->company_id }}">
                                    @else
                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-4 col-form-label">Company</label>
                                            <select class="form-control select2 col-sm-8" name="company_id" id="company_id">
                                                <option value="">Select Company</option>
                                                @foreach ($companies as $company)
                                                    <option value="{{ $company->id }}">{{ $company->company_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif
                                    <div class="form-group row">
                                        <label for="inputEmail3" class="col-sm-4 col-form-label">Area</label>
                                        <select class="form-control select2 col-sm-8" name="area_id" id="area_select">
                                            <option value="">Select Area</option>
                                        </select>
                                    </div>

                                    <div class="form-group row">
                                        <label for="inputEmail3" class="col-sm-4 col-form-label">Locality Name</label>
                                        <input type="text" name="locality_name" id="locality_name"
                                            class="col-sm-8 form-control" id="inputEmail3" placeholder="Locality Name">
                                    </div>
                                </div>
                                <!-- /.card-body -->
                            </div>
                            <div class="modal-footer justify-content-between">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-info">Save changes</button>
                            </div>
                        </form>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div> --}}
            <!-- /.modal -->

            <div class="modal fade" id="modal-import">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Import</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="" id="LocalityImportForm" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="modal-body">
                                <div class="card-body">
                                    <div class="form-group row">
                                        <label for="inputEmail3" class="col-sm-3 col-form-label">Import excel</label>
                                        <input type="file" name="file" class="col-sm-9 form-control">
                                    </div>
                                </div>
                                <!-- /.card-body -->
                            </div>
                            <div class="modal-footer justify-content-between">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                <button type="button" id="importBtn" class="btn btn-info">Import</button>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.22.5/dist/sweetalert2.all.min.js"></script>

    @component('admin.modals.modal-locality', ['areas' => $areas])
        @slot('company_dropdown')
            @foreach ($companies as $company)
                <option value="{{ $company->id }}">{{ $company->company_name }}
                </option>
            @endforeach
        @endslot
        @slot('area_dropdown')
            @foreach ($areas as $area)
                <option value="{{ $area->id }}">{{ $area->area_name }}
                </option>
            @endforeach
        @endslot
    @endcomponent


    <script>
        $(function() {
            let table = $('#localityTable').DataTable({
                processing: true,
                serverSide: true,

                ajax: {
                    url: "{{ route('locality.list') }}",
                    data: function(d) {
                        // d.company_id = $('#companyFilter').val();
                    },
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'localities.id',
                        orderable: true,
                        searchable: false
                    },
                    // {
                    //     data: 'company_name',
                    //     name: 'companies.company_name',
                    // },
                    {
                        data: 'area_name',
                        name: 'areas.area_name'
                    },
                    {
                        data: 'locality_name',
                        name: 'localities.locality_name'
                    },
                    {
                        data: 'status',
                        name: 'areas.status',
                        render: function(data, type, row) {
                            if (data == 1) {
                                return '<span class="badge bg-success">Active</span>';
                            } else {
                                return '<span class="badge bg-secondary">Inactive</span>';
                            }
                        }
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
                order: [
                    [0, 'desc']
                ],
                dom: 'Bfrtip', // This is important for buttons
                buttons: [{
                    extend: 'excelHtml5',
                    text: 'Export Excel',
                    title: 'Area Data',
                    action: function(e, dt, node, config) {
                        // redirect to your Laravel export route
                        let searchValue = dt.search();
                        let url = "{{ route('locality.export') }}" + "?search=" +
                            encodeURIComponent(searchValue);
                        window.location.href = url;
                    }
                }]
            });

        });

        $('#importBtn').on('click', function() {
            showLoader();
            let formData = new FormData($('#LocalityImportForm')[0]);
            $.ajax({
                url: "{{ route('import.locality') }}",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    hideLoader();
                    toastr.success(response.message);
                    window.location.reload();
                },
                error: function(err) {
                    hideLoader();
                    toastr.error(err.responseJSON.message);
                }
            });
        });

        function deleteConf(id) {
            Swal.fire({
                title: "Are you sure?",
                // text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "DELETE",
                        url: '/locality/' + id,
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        dataType: "json",
                        success: function(response) {
                            toastr.success(response.message);
                            $('#localityTable').DataTable().ajax.reload();
                        }
                    });

                }
                //  else {
                //     toastr.error(errors.responseJSON.message);
                // }
            });
        }


        $("#modal-locality").on('shown.bs.modal', function(e) {
            document.activeElement.blur();
            $("#area_select").focus();

            var id = $(e.relatedTarget).data('id');
            var name = $(e.relatedTarget).data('name');
            var company_id = $(e.relatedTarget).data('company');
            var area_id = $(e.relatedTarget).data('area');
            var status = $(e.relatedTarget).data('status');
            // alert(status);

            if (id) {
                //     $(this).find('form')[0].reset();
                //     $('#company_id').prop('disabled', false);

                //     companyChange(null, null);
                // } else {
                // $('#company_id').val(company_id).trigger('change');
                // companyChange(company_id, $(e.relatedTarget).data('area'));

                // $('#company_id').prop('disabled', true);
                $('#area_select').val(area_id).trigger('change');
                $('#locality_id').val(id);
                $('#locality_name').val(name);
                $('#status').val(status).trigger('change');


            } else {
                $('#localityForm').trigger("reset");
            }

        });

        $('#modal-locality').on('hidden.bs.modal', function() {
            let form = $(this).find('form');

            form[0].reset();
            form.find('select').val(null).trigger('change');
            $('#company_id').prop('disabled', false);
        });
        enableEnterNavigation('#localityForm');
    </script>
@endsection
