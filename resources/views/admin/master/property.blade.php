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
                            @if(auth()->user()->can('property.add') || auth()->user()->can('property.makani_verification'))
                                <div class="card-header">
                                    <span class="float-right">

                                        @can('property.add')
                                            <button class="btn btn-info m-1"
                                                    data-toggle="modal"
                                                    data-target="#modal-property">
                                                Add Property
                                            </button>

                                            <button class="btn btn-secondary m-1"
                                                    data-toggle="modal"
                                                    data-target="#modal-import">
                                                Import
                                            </button>
                                        @endcan

                                        @can('property.makani_verification')
                                            <button class="btn btn-success m-1"
                                                    data-toggle="modal"
                                                    data-target="#modal-makani-verification">
                                                Verify Makani
                                            </button>
                                        @endcan

                                    </span>
                                </div>
                            @endif

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

            <div class="modal fade" id="modal-makani-verification">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">

                        <div class="modal-header">
                            <h4 class="modal-title">Makani Verification</h4>
                            <button type="button" class="close" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>

                        <form id="MakaniVerificationForm" autocomplete="off">
                            @csrf

                            <div class="modal-body">

                                {{-- Row 1: Area | Locality --}}
                                <div class="row">
                                    <div class="col-md-6 form-group">
                                        <label>Area</label>
                                        <select name="area_id" id="makani_area_id" class="form-control select2" required>
                                            <option value="">Select Area</option>
                                            @foreach ($areas as $area)
                                                <option value="{{ $area->id }}">{{ $area->area_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-6 form-group">
                                        <label>Locality</label>
                                        <select name="locality_id" id="makani_locality_id" class="form-control select2" required>
                                            <option value="">Select Locality</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- Row 2: Building Name | Makani Number --}}
                                <div class="row">
                                    <div class="col-md-6 form-group">
                                        <label>Building Name</label>
                                        <input type="text" name="building_name" class="form-control" required>
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label>Plot No</label>
                                        <input type="text" name="plot_no" class="form-control" maxlength="50">
                                    </div>

                                    <div class="col-md-3 form-group">
                                        <label>Makani Number</label>
                                       
                                        <input type="text" name="makani_number" id="makani_verify_number" class="form-control" placeholder="12345 67890" maxlength="11" required>
                                    </div>
                                </div>

                                {{-- Row 3: Link (full width, since URLs are long) --}}
                                <div class="row">
                                    <div class="col-12 form-group">
                                        <label>Google Maps Location Link</label>
                                        <input type="url" name="location_link" id="makani_location_link" class="form-control" placeholder="Paste Google Maps link">
                                    </div>
                                </div>

                                <div id="makani-map" style="height:350px; width:100%;"></div>
                                <div id="makani-verification-result" class="mt-2"></div>

                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                {{-- <button type="submit" class="btn btn-success">Verify Makani</button> --}}
                                <button type="submit" id="makaniSubmitBtn" class="btn btn-success" disabled>Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

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
        $('#makani_area_id').on('change', function () {
            makaniAreaChange($(this).val(), null);
        });

        function makaniAreaChange(areaId, localityVal) {
            let options = '<option value="">Select Locality</option>';

            allLocalities
                .filter(l => l.area_id == areaId)
                .sort((a, b) => a.locality_name.localeCompare(b.locality_name, undefined, { sensitivity: 'base' }))
                .forEach(l => {
                    options += `<option value="${l.id}" ${l.id == localityVal ? 'selected' : ''}>${l.locality_name}</option>`;
                });

            $('#makani_locality_id').html(options).trigger('change');
        }

        let makaniMap, makaniOverlays = [], makaniTimer, makaniReq = 0;

        function initMakaniMap() {
            if (!makaniMap) {
                makaniMap = new google.maps.Map(document.getElementById('makani-map'), {
                    center: { lat: 25.2048, lng: 55.2708 }, zoom: 11
                });
            } else {
                google.maps.event.trigger(makaniMap, 'resize');
            }
        }

        function clearMakaniMap() {
            makaniOverlays.forEach(o => o.setMap(null));
            makaniOverlays = [];
        }

        function showMakaniResult(msg, type) {
            $('#makani-verification-result').html(`<div class="alert alert-${type} py-2 mb-0">${msg}</div>`);
        }

        function drawMakaniMap(res) {
            clearMakaniMap();
            const m = { lat: res.makani.lat, lng: res.makani.lng };
            const l = { lat: res.link.lat,   lng: res.link.lng };
            const color = res.within ? '#28a745' : '#dc3545';

            makaniOverlays.push(
                new google.maps.Marker({ position: m, map: makaniMap, label: 'M', title: 'Makani location' }),
                new google.maps.Marker({ position: l, map: makaniMap, label: 'L', title: 'Link location' }),
                new google.maps.Polyline({ path: [m, l], map: makaniMap, strokeColor: color, strokeWeight: 3 }),
                new google.maps.Circle({
                    center: m, radius: 100, map: makaniMap,
                    strokeColor: color, fillColor: color, fillOpacity: 0.12
                })
            );

            const bounds = new google.maps.LatLngBounds();
            bounds.extend(m); bounds.extend(l);
            makaniMap.fitBounds(bounds, 60);
            google.maps.event.addListenerOnce(makaniMap, 'idle', () => {
                if (makaniMap.getZoom() > 19) makaniMap.setZoom(19);
            });
        }

        function runMakaniCheck() {
            const digits = $('#makani_verify_number').val().replace(/\D/g, '');
            const link   = $('#makani_location_link').val().trim();
            const $btn   = $('#makaniSubmitBtn');

            $btn.prop('disabled', true);
            if (digits.length !== 10 || !link) return;

            const reqId = ++makaniReq;
            showMakaniResult('Checking locations...', 'info');

            $.ajax({
                url: "{{ route('property.makani.locate') }}",
                type: 'POST',
                data: { _token: $('meta[name="csrf-token"]').attr('content'), makani_number: digits, location_link: link },
                success: function (res) {
                    if (reqId !== makaniReq) return;          // ignore stale responses
                    drawMakaniMap(res);
                    if (res.within) {
                        showMakaniResult(`Within range: ${res.distance} m apart. You can submit.`, 'success');
                        $btn.prop('disabled', false);
                    } else {
                        showMakaniResult(`Too far: ${res.distance} m apart (max 100 m).`, 'danger');
                    }
                },
                error: function (xhr) {
                    if (reqId !== makaniReq) return;
                    clearMakaniMap();
                    const errs = xhr.responseJSON?.errors;
                    const msg = errs ? Object.values(errs).flat().join('<br>') : (xhr.responseJSON?.message || 'Something went wrong.');
                    showMakaniResult(msg, 'danger');
                }
            });
        }

        // format "12345 67890" and trigger the check (debounced)
        $('#makani_verify_number').on('input', function () {
            const d = this.value.replace(/\D/g, '').slice(0, 10);
            this.value = d.length > 5 ? d.slice(0, 5) + ' ' + d.slice(5) : d;
        });
        $('#makani_verify_number, #makani_location_link').on('input change', function () {
            clearTimeout(makaniTimer);
            makaniTimer = setTimeout(runMakaniCheck, 600);
        });

        $('#modal-makani-verification').on('shown.bs.modal', function () {
            const $modal = $(this);
            $modal.find('.select2').each(function () {
                if ($(this).hasClass('select2-hidden-accessible')) $(this).select2('destroy');
                $(this).select2({ width: '100%', dropdownParent: $modal, placeholder: 'Select' });
            });
            initMakaniMap();
        });

        $('#modal-makani-verification').on('hidden.bs.modal', function () {
            $('#MakaniVerificationForm')[0].reset();
            $('#makani_area_id').val('').trigger('change');
            $('#makani-verification-result').empty();
            $('#makaniSubmitBtn').prop('disabled', true);
            clearMakaniMap();
        });


        function esc(v) {
            return $('<div>').text(v ?? '-').html();
        }

        function submitMakani(confirmUpdate = false) {
            const $btn = $('#makaniSubmitBtn').prop('disabled', true);
            let payload = $('#MakaniVerificationForm').serialize();
            if (confirmUpdate) payload += '&confirm_update=1';

            $.ajax({
                url: "{{ route('property.makani.verify') }}",
                type: 'POST',
                data: payload,   // includes _token
                success: function (res) {
                    toastr.success(res.message);
                    $('#modal-makani-verification').modal('hide');
                    $('#propertyTable').DataTable().ajax.reload();
                },
                error: function (xhr) {
                    // existing building without Makani -> ask the user
                    if (xhr.status === 409 && xhr.responseJSON?.needs_confirmation) {
                        const p = xhr.responseJSON.existing;
                        Swal.fire({
                            title: 'Building already exists',
                            icon: 'question',
                            html: `
                                <p class="mb-2">This building is already in the system <b>without a Makani number</b>:</p>
                                <table class="table table-sm table-bordered text-left mb-2">
                                    <tr><th>Building</th><td>${esc(p.property_name)}</td></tr>
                                    <tr><th>Area</th><td>${esc(p.area)}</td></tr>
                                    <tr><th>Locality</th><td>${esc(p.locality)}</td></tr>
                                    <tr><th>Plot No</th><td>${esc(p.plot_no)}</td></tr>
                                    <tr><th>Size</th><td>${esc(p.property_size)}</td></tr>
                                    <tr><th>Makani</th><td>${esc(p.makani_number || 'Not set')}</td></tr>
                                </table>
                                <p class="mb-0">Do you want to continue and add the Makani number to this property?</p>`,
                            showCancelButton: true,
                            confirmButtonText: 'Yes, continue',
                            cancelButtonText: 'Cancel',
                            confirmButtonColor: '#28a745',
                            cancelButtonColor: '#d33',
                            // the Verify Makani modal is open, so keep the popup above it
                            target: document.getElementById('modal-makani-verification')
                        }).then(result => {
                            if (result.isConfirmed) {
                                submitMakani(true);
                            } else {
                                $btn.prop('disabled', false);
                            }
                        });
                        return;
                    }

                    const errs = xhr.responseJSON?.errors;
                    const msg = errs ? Object.values(errs).flat().join('<br>') : (xhr.responseJSON?.message || 'Verification failed.');
                    showMakaniResult(msg, 'danger');
                    $btn.prop('disabled', false);
                }
            });
        }

        $('#MakaniVerificationForm').on('submit', function (e) {
            e.preventDefault();
            submitMakani(false);
        });
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
                        // url: '/property/' + id,
                        url: "{{ route('property.destroy', ':id') }}".replace(':id', id),
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
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_key') }}"></script>
@endsection
