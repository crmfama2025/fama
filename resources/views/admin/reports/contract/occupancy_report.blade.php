@extends('admin.layout.admin_master')

@section('custom_css')
    <link rel="stylesheet" href="{{ asset('assets/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <style>
        #occupancyTable th,
        #occupancyTable td {
            white-space: nowrap;
            vertical-align: middle;
        }

        #occupancyTable .amount-column {
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
                            <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Home</a></li>
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
                            @foreach ([
            'companyFilter' => ['Company', $companies, 'company_name'],
            'vendorFilter' => ['Vendor', $vendors, 'vendor_name'],
            'propertyFilter' => ['Property', $properties, 'property_name'],
            'areaFilter' => ['Area', $areas, 'area_name'],
            'localityFilter' => ['Locality', $localities, 'locality_name'],
        ] as $id => [$label, $items, $text])
                                <div class="col-md-3 mb-3"><label for="{{ $id }}">{{ $label }}</label>
                                    <select id="{{ $id }}" class="form-control select2">
                                        <option value="">All</option>
                                        @foreach ($items as $item)
                                            <option value="{{ $item->id }}">{{ $item->{$text} }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endforeach
                            <div class="col-md-3 mb-3"><label for="occupancyFilter">Occupancy Status</label>
                                <select id="occupancyFilter" class="form-control select2">
                                    <option value="">All</option>
                                    <option value="occupied">Occupied</option>
                                    <option value="vacant">Vacant</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3"><label for="subunitTypeFilter">Subunit Type</label>
                                <select id="subunitTypeFilter" class="form-control select2">
                                    <option value="">All</option>
                                    <option value="1">Partition</option>
                                    <option value="2">Bedspace</option>
                                    <option value="3">Room</option>
                                    <option value="4">Full Flat</option>
                                </select>
                            </div>
                            <div class="col-md-2 mb-3"><label for="dateFrom">Contract Date From</label><input type="date"
                                    id="dateFrom" class="form-control"></div>
                            <div class="col-md-2 mb-3"><label for="dateTo">Contract Date To</label><input type="date"
                                    id="dateTo" class="form-control"></div>
                            <div class="col-md-5 mb-3 d-flex align-items-end justify-content-end">
                                <button type="button" id="applyFilter" class="btn btn-primary mr-2"><i
                                        class="fa fa-filter"></i> Apply</button>
                                <button type="button" id="resetFilter" class="btn btn-secondary"><i
                                        class="fa fa-undo-alt"></i> Reset</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body table-responsive">
                        <table id="occupancyTable" class="table table-striped table-bordered nowrap" width="100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Project Number</th>
                                    <th>Unit Number</th>
                                    <th>Unit Type</th>
                                    <th>Subunit Number</th>
                                    <th>Subunit Code</th>
                                    <th>Subunit Type</th>
                                    <th>Occupancy Status</th>
                                    <th>Maid Room</th>
                                    <th>Property Type</th>
                                    <th>Floor Number</th>
                                    <th>Unit Status</th>
                                    <th>Unit Rent Per Annum</th>
                                    <th>Unit Rent Per Month</th>
                                    {{-- <th>Rent Per Flat</th> --}}
                                    <th>Unit Profit %</th>
                                    <th>Unit Profit</th>
                                    <th>Unit Revenue</th>
                                    <th>Project Code</th>
                                    <th>Company</th>
                                    <th>Vendor</th>
                                    <th>Property Code</th>
                                    <th>Property</th>
                                    <th>Area</th>
                                    <th>Locality</th>
                                    <th>Contract Start</th>
                                    <th>Contract End</th>
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
    <script src="{{ asset('assets/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
    <script>
        $(function() {
            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%'
            });
            const filterData = function(data) {
                data.keyword = data.search.value;
                data.search.value = '';
                data.company_id = $('#companyFilter').val();
                data.vendor_id = $('#vendorFilter').val();
                data.property_id = $('#propertyFilter').val();
                data.area_id = $('#areaFilter').val();
                data.locality_id = $('#localityFilter').val();
                data.occupancy_status = $('#occupancyFilter').val();
                data.subunit_type = $('#subunitTypeFilter').val();
                data.date_from = $('#dateFrom').val();
                data.date_to = $('#dateTo').val();
            };
            const amount = column => ({
                data: column[0],
                name: column[1],
                className: 'amount-column',
                defaultContent: '0.00'
            });
            const table = $('#occupancyTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: false,
                scrollX: true,
                pageLength: 25,
                lengthMenu: [25, 50, 100, 250],
                ajax: {
                    url: "{{ route('occupancy-report.data') }}",
                    data: filterData
                },
                columns: [{
                        data: 'DT_RowIndex',
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
                    }, {
                        data: 'unit_type',
                        name: 'ut.unit_type',
                        defaultContent: '-'
                    },
                    {
                        data: 'subunit_no',
                        name: 'su.subunit_no'
                    }, {
                        data: 'subunit_code',
                        name: 'su.subunit_code'
                    },
                    {
                        data: 'subunit_type_name',
                        name: 'su.subunit_type'
                    }, {
                        data: 'occupancy_status',
                        name: 'su.is_vacant'
                    },
                    {
                        data: 'maid_room',
                        name: 'cu.maid_room'
                    }, {
                        data: 'property_type',
                        name: 'pt.property_type',
                        defaultContent: '-'
                    },
                    {
                        data: 'floor_number',
                        name: 'cu.floor_no',
                        defaultContent: '-'
                    }, {
                        data: 'unit_status',
                        name: 'us.unit_status',
                        defaultContent: '-'
                    },
                    amount(['unit_rent_per_annum', 'cu.unit_rent_per_annum']),
                    amount([
                        'unit_rent_per_month', 'cu.rent_per_unit_per_month'
                    ]),
                    // amount(['rent_per_flat', 'cu.rent_per_flat']), 
                    amount(['unit_profit_percentage',
                        'cu.unit_profit_perc'
                    ]),
                    amount(['unit_profit', 'cu.unit_profit']),
                    amount(['unit_revenue',
                        'cu.unit_revenue'
                    ]),
                    {
                        data: 'project_code',
                        name: 'c.project_code',
                        defaultContent: '-'
                    },
                    {
                        data: 'company_name',
                        name: 'co.company_name'
                    }, {
                        data: 'vendor_name',
                        name: 'v.vendor_name',
                        defaultContent: '-'
                    },
                    {
                        data: 'property_code',
                        name: 'p.property_code',
                        defaultContent: '-'
                    }, {
                        data: 'property_name',
                        name: 'p.property_name',
                        defaultContent: '-'
                    },
                    {
                        data: 'area_name',
                        name: 'a.area_name',
                        defaultContent: '-'
                    }, {
                        data: 'locality_name',
                        name: 'l.locality_name',
                        defaultContent: '-'
                    },
                    {
                        data: 'contract_start_date',
                        name: 'cd.start_date'
                    }, {
                        data: 'contract_end_date',
                        name: 'cd.end_date'
                    }
                ],
                order: [
                    [1, 'desc']
                ],
                dom: 'Bfrtip',
                buttons: [{
                    text: '<i class="fa fa-file-excel"></i> Export Excel',
                    className: 'btn btn-success',
                    action: function() {
                        window.location.href = "{{ route('occupancy-report.export') }}?" + $
                            .param({
                                company_id: $('#companyFilter').val(),
                                vendor_id: $('#vendorFilter').val(),
                                property_id: $('#propertyFilter').val(),
                                area_id: $('#areaFilter').val(),
                                locality_id: $('#localityFilter').val(),
                                occupancy_status: $('#occupancyFilter').val(),
                                subunit_type: $('#subunitTypeFilter').val(),
                                date_from: $('#dateFrom').val(),
                                date_to: $('#dateTo').val(),
                                keyword: table.search()
                            });
                    }
                }]
            });
            $('#applyFilter').on('click', () => table.ajax.reload());
            $('#resetFilter').on('click', function() {
                $('#companyFilter, #vendorFilter, #propertyFilter, #areaFilter, #localityFilter, #occupancyFilter, #subunitTypeFilter')
                    .val('').trigger('change');
                $('#dateFrom, #dateTo').val('');
                table.search('');
                table.ajax.reload();
            });
        });
    </script>
@endsection
