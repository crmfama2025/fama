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
                        <h1>Property</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
                            <li class="breadcrumb-item active">Property</li>
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
                            @can('property.add')
                                <div class="card-header">
                                    <!-- <h3 class="card-title">Property Details</h3> -->
                                    <span class="float-right">
                                        <button class="btn btn-info float-right m-1" data-toggle="modal"
                                            data-target="#modal-property">Add Property</button>
                                        <button class="btn btn-secondary float-right m-1" data-toggle="modal"
                                            data-target="#modal-import">Import</button>
                                    </span>
                                </div>
                            @endcan
                            <!-- /.card-header -->
                            <div class="card-body table-responsive">
                                <table id="propertyTable" class="table table-striped table-hover" style="width: 100%">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            {{-- <th>Company</th> --}}
                                            <th>Area</th>
                                            <th>Locality</th>
                                            {{-- <th>Property Type</th> --}}
                                            <th>Property Name</th>
                                            <th>Property size</th>
                                            <th>Plot no</th>
                                            <th>Makani Number</th>
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


            <div class="modal fade" id="modal-import">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Import</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="" id="PropertyImportForm" method="POST" enctype="multipart/form-data">
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

    {{-- @component('admin.modals.modal-property', [
    'areas' => $areas,
    'localities' => $localities,
    'property_types' => $property_types,
])
        @slot('company_dropdown')
            @foreach ($companies as $company)
                <option value="{{ $company->id }}">{{ $company->company_name }}
                </option>
            @endforeach
        @endslot

        @slot('propertySizeUnits_dropdown')
            @foreach ($propertySizeUnits as $unit)
                <option value="{{ $unit->id }}">{{ $unit->unit_name }}
                </option>
            @endforeach
        @endslot
    @endcomponent --}}
    @component('admin.modals.modal-property', [
        // 'areas' => $areas,
        'localities' => $localities,
        'property_types' => $property_types,
    ])
        @slot('area_dropdown')
            @foreach ($areas as $area)
                <option value="{{ $area->id }}">{{ $area->area_name }}
                </option>
            @endforeach
        @endslot

        @slot('propertySizeUnits_dropdown')
            @foreach ($propertySizeUnits as $unit)
                <option value="{{ $unit->id }}">{{ $unit->unit_name }}
                </option>
            @endforeach
        @endslot
    @endcomponent



    <script>
        $(function() {
            let table = $('#propertyTable').DataTable({
                processing: true,
                serverSide: true,

                ajax: {
                    url: "{{ route('property.list') }}",
                    data: function(d) {
                        // d.company_id = $('#companyFilter').val();
                    },
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'properties.id',
                        orderable: true,
                        searchable: false
                    },
                    // {
                    //     data: 'id',
                    //     name: 'areas.id',
                    //     visible: false
                    // },
                    // {
                    //     data: 'company_name',
                    //     name: 'companies.company_name',
                    // },
                    {
                        data: 'area_name',
                        name: 'areas.area_name',
                    },
                    {
                        data: 'locality_name',
                        name: 'localities.locality_name',
                    },
                    // {
                    //     data: 'property_type',
                    //     name: 'property_types.property_type',
                    // },
                    {
                        data: 'property_name',
                        name: 'properties.property_name'
                    },
                    {
                        data: 'property_size',
                        name: 'properties.property_size'
                    },
                    {
                        data: 'plot_no',
                        name: 'properties.plot_no'
                    },
                    {
                        data: 'makani_number',
                        name: 'properties.makani_number'
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
                    title: 'Property Data',
                    action: function(e, dt, node, config) {
                        // redirect to your Laravel export route
                        let searchValue = dt.search();
                        let url = "{{ route('property.export') }}" + "?search=" +
                            encodeURIComponent(searchValue);
                        window.location.href = url;
                    }
                }]
            });
        });

        $('#importBtn').on('click', function() {

            showLoader();

            let formData = new FormData($('#PropertyImportForm')[0]);
            $.ajax({
                url: "{{ route('import.property') }}",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    // console.log(response);

                    // $('#propertyTable').DataTable().ajax.reload();

                    // // Close modal
                    // $('#modal-import').modal('hide');

                    // // Reset form
                    // $('#PropertyImportForm')[0].reset();
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

        $("#modal-property").on('shown.bs.modal', function(e) {


            // $(this).find('.select2').select2({
            //     width: '100%',
            //     dropdownParent: $('#modal-property'),
            //     placeholder: 'Select'
            // });

            const $modal = $(this);

            $modal.find('.select2').each(function() {
                if ($(this).hasClass('select2-hidden-accessible')) {
                    $(this).select2('destroy');
                }

                $(this).select2({
                    width: '100%',
                    dropdownParent: $modal,
                    placeholder: $(this).data('placeholder'),
                });
            });
            $modal.on('click', '.select2-container', function(e) {
                e.stopPropagation();
            });
            document.activeElement.blur();
            $("#area_id").focus();
            var id = $(e.relatedTarget).data('id');
            var name = $(e.relatedTarget).data('name');
            // var company_id = $(e.relatedTarget).data('company');
            var area_id = $(e.relatedTarget).data('area');
            var locality_id = $(e.relatedTarget).data('locality');
            var lat = $(e.relatedTarget).data('lat');
            var long = $(e.relatedTarget).data('long');
            var address = $(e.relatedTarget).data('address');
            var location = $(e.relatedTarget).data('location');
            var remarks = $(e.relatedTarget).data('remarks');
            var status = $(e.relatedTarget).data('status');
            var makani = $(e.relatedTarget).data('makani');

            if (!id) {
                $(this).find('form')[0].reset();
                // $('#company_id').prop('disabled', false);
                // $('#company_id').val('').trigger('change');

                // companyChange(null, null);
            } else {
                // $('#company_id').val(company_id).trigger('change');
                // companyChange(company_id, area_id, $(e.relatedTarget).data(
                //     'property_type'), locality_id);
                $('#area_id').val(area_id).trigger('change');
                areaChange(area_id, locality_id);

                // $('#company_id').prop('disabled', true);
                $('#property_id').val(id);
                $('#property_name').val(name);
                $('#property_size').val($(e.relatedTarget).data('property_size'));
                $('#property_size_unit').val($(e.relatedTarget).data('property_size_unit')).trigger('change');
                $('#plot_no').val($(e.relatedTarget).data('plot_no'));
                $('#latitude').val(lat);
                $('#longitude').val(long);
                $('#address').val(address);
                $('#location').val(location);
                $('#remarks').val(remarks);
                $('#makani_number').val(makani);
                $('#status').val(status).trigger('change');

            }
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
                        url: '/property/' + id,
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        dataType: "json",
                        success: function(response) {
                            toastr.success(response.message);
                            $('#propertyTable').DataTable().ajax.reload();
                        }
                    });

                }
                //  else {
                //     toastr.error(errors.responseJSON.message);
                // }
            });
        }
        enableEnterNavigation('#PropertyForm');
        $('#makani_number').on('input', function() {
            this.value = this.value.replace(/\D/g, '');
        });
    </script>
@endsection
