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
                        <h1>{{ $title }}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
                            <li class="breadcrumb-item active">Vendor</li>
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
                            @can('vendor.add')
                                <div class="card-header">
                                    <!-- <h3 class="card-title">Vendor Details</h3> -->
                                    <span class="float-right">
                                        <button class="btn btn-info float-right m-1" data-toggle="modal"
                                            data-target="#modal-vendor">Add Vendor</button>
                                        <button class="btn btn-secondary float-right m-1" data-toggle="modal"
                                            data-target="#modal-import">Import</button>
                                    </span>
                                </div>
                            @endcan
                            <!-- /.card-header -->
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="vendorTable" class="table table-striped table-hover w-100">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                {{-- <th>Company Name</th> --}}
                                                <th>Vendor Code</th>
                                                <th>Vendor Name</th>
                                                <th>Vendor Phone</th>
                                                <th>Vendor Email</th>
                                                {{-- <th>Contact Person</th>
                                                <th>Contact Number</th> --}}
                                                <th>Action</th>
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


            <div class="modal fade" id="modal-import">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Import</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="" id="VendorImportForm" method="POST" enctype="multipart/form-data">
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
    <!-- Select2 -->
    <script src="{{ asset('assets/select2/js/select2.full.min.js') }}"></script>
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

    @component('admin.modals.modal-vendor')
        @slot('company_dropdown')
            @foreach ($companies as $company)
                <option value="{{ $company->id }}">{{ $company->company_name }}
                </option>
            @endforeach
        @endslot
        @slot('contract_templates_dropdown')
            @foreach ($contractTemplates as $temp)
                <option value="{{ $temp->id }}">{{ $temp->template_name }}
                </option>
            @endforeach
        @endslot
    @endcomponent

    <script>
        $(function() {
            let table = $('#vendorTable').DataTable({
                processing: true,
                serverSide: true,
                scrollX: true,

                ajax: {
                    url: "{{ route('vendor.list') }}",
                    data: function(d) {
                        // d.company_id = $('#companyFilter').val();
                    },
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'vendors.id',
                        orderable: true,
                        searchable: false
                    },
                    // {
                    //     data: 'company_name',
                    //     name: 'companies.company_name',
                    // },
                    {
                        data: 'vendor_code',
                        name: 'vendors.vendor_code',
                    },
                    {
                        data: 'vendor_name',
                        name: 'vendors.vendor_name',
                    },
                    {
                        data: 'vendor_phone',
                        name: 'vendors.vendor_phone',
                    },
                    {
                        data: 'vendor_email',
                        name: 'vendors.vendor_email',
                    },
                    // {
                    //     data: 'contact_person',
                    //     name: 'vendors.contact_person',
                    // },
                    // {
                    //     data: 'contact_person_phone',
                    //     name: 'vendors.contact_person_phone',
                    // },
                    // {
                    //     data: 'accountant_name',
                    //     name: 'vendors.accountant_name',
                    // },
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
                    title: 'Vendor Data',
                    action: function(e, dt, node, config) {
                        // redirect to your Laravel export route
                        let searchValue = dt.search();
                        let url = "{{ route('vendor.export') }}" + "?search=" +
                            encodeURIComponent(searchValue);
                        window.location.href = url;
                    }
                }]
            });
        });

        $('#modal-vendor').on('shown.bs.modal', function(event) {
            document.activeElement.blur();
            $("#vendor_name").trigger('focus');

            let rowData = $(event.relatedTarget).data('row');

            if (rowData) {
                $('#vendor_id').val(rowData.id);
                $.each(rowData, function(key, value) {
                    // If an element with same id exists, set its value/text
                    let $el = $('#' + key);

                    if ($el.is('input, textarea')) {
                        $el.val(value);
                    } else if ($el.is('select')) {
                        $el.val(value).trigger('change');;
                    } else {
                        $el.text(value);
                    }
                });
            } else {
                // Clear all inputs and selects
                $(this).find('select').val('').trigger('change');
                $(this).find('form')[0].reset();
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
                        url: '/vendors/' + id,
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        dataType: "json",
                        success: function(response) {
                            toastr.success(response.message);
                            $('#vendorTable').DataTable().ajax.reload();
                        }
                    });

                }
                //  else {
                //     toastr.error(errors.responseJSON.message);
                // }
            });
        }

        $('#importBtn').on('click', function() {
            let formData = new FormData($('#VendorImportForm')[0]);
            $.ajax({
                url: "{{ route('import.vendor') }}",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    toastr.success(response.message);
                    window.location.reload();
                },
                error: function(err) {
                    toastr.error(err.responseJSON.message);
                }
            });
        });
        $('#vendor_phone').on('blur', function() {
            phoneValidation('#vendor_phone', 'vendor_phone');
        });
        $('#contact_person_phone').on('blur', function() {
            phoneValidation('#contact_person_phone', 'contact_person_phone');
        });
        $('#accountant_phone').on('blur', function() {
            phoneValidation('#accountant_phone', 'accountant_phone');
        });
        $('#landline_number').on('blur', function() {
            validateLandline('#landline_number');
        });

        // enableEnterNavigation('#VendorForm');
    </script>
@endsection
