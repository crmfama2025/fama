@extends('admin.layout.admin_master')

@section('custom_css')
    <link rel="stylesheet" href="{{ asset('assets/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/datatables-buttons/css/buttons.bootstrap4.min.css') }}">

    <style>
        #contractInventoryTable th,
        #contractInventoryTable td {
            white-space: nowrap;
            vertical-align: middle;
        }

        #contractInventoryTable .amount-column {
            text-align: right;
        }
    </style>
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
                            <li class="breadcrumb-item">
                                <a href="{{ url('/dashboard') }}">Home</a>
                            </li>
                            <li class="breadcrumb-item active">{{ $title }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <div class="card card-info card-outline">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Filter</h5>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="companyFilter">Company</label>
                                <select id="companyFilter" class="form-control select2">
                                    <option value="">All</option>
                                    @foreach ($companies as $company)
                                        <option value="{{ $company->id }}">
                                            {{ $company->company_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="vendorFilter">Vendor</label>
                                <select id="vendorFilter" class="form-control select2">
                                    <option value="">All</option>
                                    @foreach ($vendors as $vendor)
                                        <option value="{{ $vendor->id }}">
                                            {{ $vendor->vendor_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="propertyFilter">Property</label>
                                <select id="propertyFilter" class="form-control select2">
                                    <option value="">All</option>
                                    @foreach ($properties as $property)
                                        <option value="{{ $property->id }}">
                                            {{ $property->property_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="areaFilter">Area</label>
                                <select id="areaFilter" class="form-control select2">
                                    <option value="">All</option>
                                    @foreach ($areas as $area)
                                        <option value="{{ $area->id }}">
                                            {{ $area->area_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="localityFilter">Locality</label>
                                <select id="localityFilter" class="form-control select2">
                                    <option value="">All</option>
                                    @foreach ($localities as $locality)
                                        <option value="{{ $locality->id }}">
                                            {{ $locality->locality_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2 mb-3">
                                <label for="dateFrom">Contract Date From</label>
                                <input type="date" id="dateFrom" class="form-control">
                            </div>

                            <div class="col-md-2 mb-3">
                                <label for="dateTo">Contract Date To</label>
                                <input type="date" id="dateTo" class="form-control">
                            </div>

                            <div class="col-md-5 mb-3 d-flex align-items-end justify-content-end">
                                <button type="button" id="applyFilter" class="btn btn-primary mr-2">
                                    <i class="fa fa-filter"></i> Apply
                                </button>
                                <button type="button" id="resetFilter" class="btn btn-secondary">
                                    <i class="fa fa-undo-alt"></i> Reset
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body table-responsive">
                        <table id="contractInventoryTable" class="table table-striped table-bordered nowrap" width="100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Project Number</th>
                                    <th>Unit Number</th>
                                    <th>Unit Type</th>
                                    <th>Maid Room</th>
                                    <th>Property Type</th>
                                    <th>Floor Number</th>
                                    <th>Unit Status</th>
                                    <th>Unit Rent Per Annum</th>
                                    <th>Unit Rent Per Month</th>
                                    <th>Partition / Bedspace / Room</th>
                                    <th>No. of Partition / Bedspace / Room</th>
                                    <th>Rent per Partition / Bedspace / Room</th>
                                    <th>Rent per Flat</th>
                                    <th>Unit Profit %</th>
                                    <th>Unit Profit</th>
                                    <th>Unit Revenue</th>
                                    <th>Project Code</th>
                                    <th>Renewal Status</th>
                                    <th>Company</th>
                                    <th>Vendor</th>
                                    <th>Property</th>
                                    <th>Area</th>
                                    <th>Locality</th>
                                    <th>Contract Start Date</th>
                                    <th>Contract End Date</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('custom_js')
    <script src="{{ asset('assets/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('assets/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>

    <script>
        $(function() {
            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%'
            });

            const table = $('#contractInventoryTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: false,
                scrollX: true,
                pageLength: 25,
                lengthMenu: [25, 50, 100, 250],
                ajax: {
                    url: "{{ route('inventory-report.data') }}",
                    data: function(data) {
                        data.keyword = data.search.value;
                        data.search.value = '';
                        data.company_id = $('#companyFilter').val();
                        data.vendor_id = $('#vendorFilter').val();
                        data.property_id = $('#propertyFilter').val();
                        data.area_id = $('#areaFilter').val();
                        data.locality_id = $('#localityFilter').val();
                        data.date_from = $('#dateFrom').val();
                        data.date_to = $('#dateTo').val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'project_number',
                        name: 'c.project_number'
                    },
                    {
                        data: 'unit_number',
                        name: 'cu.unit_number'
                    },
                    {
                        data: 'unit_type',
                        name: 'cu.unit_type',
                        defaultContent: '-'
                    },
                    {
                        data: 'maid_room',
                        name: 'cu.maid_room',
                        defaultContent: '-'
                    },
                    {
                        data: 'property_type',
                        name: 'cu.property_type',
                        defaultContent: '-'
                    },
                    {
                        data: 'floor_number',
                        name: 'cu.floor_number',
                        defaultContent: '-'
                    },
                    {
                        data: 'unit_status',
                        name: 'cu.unit_status',
                        defaultContent: '-'
                    },
                    {
                        data: 'unit_rent_per_annum',
                        name: 'cu.unit_rent_per_annum',
                        className: 'amount-column'
                    },
                    {
                        data: 'unit_rent_per_month',
                        name: 'cu.unit_rent_per_month',
                        className: 'amount-column'
                    },
                    {
                        data: 'partition_bedspace_room',
                        name: 'cu.partition_bedspace_room',
                        defaultContent: '-'
                    },
                    {
                        data: 'no_of_partition_bedspace_room',
                        name: 'cu.no_of_partition_bedspace_room',
                        defaultContent: '0'
                    },
                    {
                        data: 'rent_per_partition_bedspace_room',
                        name: 'rent_per_partition_bedspace_room',
                        searchable: false,
                        // orderable: false,
                        defaultContent: '-'
                    },
                    {
                        data: 'rent_per_flat',
                        name: 'cu.rent_per_flat',
                        className: 'amount-column'
                    },
                    {
                        data: 'unit_profit_percentage',
                        name: 'cu.unit_profit_percentage',
                        className: 'amount-column'
                    },
                    {
                        data: 'unit_profit',
                        name: 'cu.unit_profit',
                        className: 'amount-column'
                    },
                    {
                        data: 'unit_revenue',
                        name: 'cu.unit_revenue',
                        className: 'amount-column'
                    },
                    {
                        data: 'project_code',
                        name: 'c.project_code',
                        defaultContent: '-'
                    },
                    {
                        data: 'renewal_status',
                        name: 'c.parent_contract_id',
                    },
                    {
                        data: 'company_name',
                        name: 'co.company_name'
                    },
                    {
                        data: 'vendor_name',
                        name: 'v.vendor_name',
                        defaultContent: '-'
                    },
                    {
                        data: 'property_name',
                        name: 'p.property_name',
                        defaultContent: '-'
                    },
                    {
                        data: 'area_name',
                        name: 'a.area_name',
                        defaultContent: '-'
                    },
                    {
                        data: 'locality_name',
                        name: 'l.locality_name',
                        defaultContent: '-'
                    },
                    {
                        data: 'contract_start_date',
                        name: 'cd.start_date'
                    },
                    {
                        data: 'contract_end_date',
                        name: 'cd.end_date'
                    },
                ],
                order: [
                    [1, 'desc']
                ],
                dom: 'Bfrtip',
                buttons: [{
                    text: '<i class="fa fa-file-excel"></i> Export Excel',
                    className: 'btn btn-success',
                    action: function() {
                        const params = $.param({
                            company_id: $('#companyFilter').val(),
                            vendor_id: $('#vendorFilter').val(),
                            property_id: $('#propertyFilter').val(),
                            area_id: $('#areaFilter').val(),
                            locality_id: $('#localityFilter').val(),
                            date_from: $('#dateFrom').val(),
                            date_to: $('#dateTo').val(),
                            keyword: table.search()
                        });

                        window.location.href =
                            "{{ route('inventory-report.export') }}?" + params;
                    }
                }]
            });

            $('#applyFilter').on('click', function() {
                table.ajax.reload();
            });

            $('#resetFilter').on('click', function() {
                $('#companyFilter, #vendorFilter, #propertyFilter, #areaFilter, #localityFilter')
                    .val('')
                    .trigger('change');

                $('#dateFrom, #dateTo').val('');
                table.search('');
                table.ajax.reload();
            });
        });
    </script>
@endsection
