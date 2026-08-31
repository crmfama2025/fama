@extends('admin.layout.admin_master')

@section('custom_css')
    <link rel="stylesheet" href="{{ asset('assets/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <style>
        #projectReportTable th,
        #projectReportTable td {
            white-space: nowrap;
            vertical-align: middle;
        }

        #projectReportTable .amount-column {
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
                            <div class="col-md-3 mb-3"><label for="companyFilter">Company</label><select id="companyFilter"
                                    class="form-control select2">
                                    <option value="">All</option>
                                    @foreach ($companies as $item)
                                        <option value="{{ $item->id }}">{{ $item->company_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mb-3"><label for="vendorFilter">Vendor</label><select id="vendorFilter"
                                    class="form-control select2">
                                    <option value="">All</option>
                                    @foreach ($vendors as $item)
                                        <option value="{{ $item->id }}">{{ $item->vendor_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mb-3"><label for="propertyFilter">Property</label><select
                                    id="propertyFilter" class="form-control select2">
                                    <option value="">All</option>
                                    @foreach ($properties as $item)
                                        <option value="{{ $item->id }}">{{ $item->property_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mb-3"><label for="areaFilter">Area</label><select id="areaFilter"
                                    class="form-control select2">
                                    <option value="">All</option>
                                    @foreach ($areas as $item)
                                        <option value="{{ $item->id }}">{{ $item->area_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mb-3"><label for="localityFilter">Locality</label><select
                                    id="localityFilter" class="form-control select2">
                                    <option value="">All</option>
                                    @foreach ($localities as $item)
                                        <option value="{{ $item->id }}">{{ $item->locality_name }}</option>
                                    @endforeach
                                </select></div>
                            <div class="col-md-3 mb-3"><label for="statusFilter">Contract Status</label><select
                                    id="statusFilter" class="form-control select2">
                                    <option value="">All</option>
                                    <option value="0">Pending</option>
                                    <option value="1">Processing</option>
                                    <option value="2">Approved</option>
                                    <option value="3">Rejected</option>
                                    <option value="7">Signed</option>
                                    <option value="8">Expired</option>
                                    <option value="9">Terminated</option>
                                    <option value="10">Dropped</option>
                                </select></div>
                            <div class="col-md-2 mb-3"><label for="dateFrom">Date From</label><input type="date"
                                    id="dateFrom" class="form-control"></div>
                            <div class="col-md-2 mb-3"><label for="dateTo">Date To</label><input type="date"
                                    id="dateTo" class="form-control"></div>
                            <div class="col-md-5 mb-3 d-flex align-items-end justify-content-end"><button id="applyFilter"
                                    class="btn btn-primary mr-2"><i class="fa fa-filter"></i> Apply</button><button
                                    id="resetFilter" class="btn btn-secondary"><i class="fa fa-undo-alt"></i> Reset</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body table-responsive">
                        <table id="projectReportTable" class="table table-striped table-bordered nowrap" width="100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Project Number</th>
                                    <th>Project Code</th>
                                    <th>Project Type</th>
                                    <th>Renewal Count</th>
                                    <th>Parent Project Number</th>
                                    <th>Contract Status</th>
                                    <th>Company</th>
                                    <th>Vendor</th>
                                    <th>Contract Type</th>
                                    <th>Property</th>
                                    <th>Area</th>
                                    <th>Locality</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Duration Months</th>
                                    <th>Contract Fee</th>
                                    <th>Rent Payable / Annum</th>
                                    <th>Total Payment to Vendor</th>
                                    <th>Paid to Vendor</th>
                                    <th>Vendor Balance</th>
                                    <th>Rent Receivable / Annum</th>
                                    <th>Commission</th>
                                    <th>Deposit</th>
                                    <th>Development Cost</th>
                                    <th>Bed Cost</th>
                                    <th>Mattress Cost</th>
                                    <th>Appliances</th>
                                    <th>Decoration</th>
                                    <th>DEWA Deposit</th>
                                    <th>Cabinets Cost</th>
                                    <th>Total OTC</th>
                                    <th>Final Cost</th>
                                    <th>Initial Investment</th>
                                    <th>Expected Profit</th>
                                    <th>ROI %</th>
                                    <th>No. of Units</th>
                                    <th>No. of Floors</th>
                                    <th>Subunit Count</th>
                                    <th>Payment Pending</th>
                                    <th>Payment Received</th>
                                    <th>Installments</th>
                                    <th>Installment Payment Progress</th>
                                    <th>Terminated Date</th>
                                    <th>Balance Amount</th>
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
            const amount = (data, name) => ({
                data,
                name,
                className: 'amount-column',
                defaultContent: '0.00'
            });
            const params = () => ({
                company_id: $('#companyFilter').val(),
                vendor_id: $('#vendorFilter').val(),
                property_id: $('#propertyFilter').val(),
                area_id: $('#areaFilter').val(),
                locality_id: $('#localityFilter').val(),
                contract_status: $('#statusFilter').val(),
                date_from: $('#dateFrom').val(),
                date_to: $('#dateTo').val()
            });
            const table = $('#projectReportTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                scrollX: true,
                pageLength: 25,
                lengthMenu: [25, 50, 100, 250],
                ajax: {
                    url: "{{ route('project-report.data') }}",
                    type: 'POST',
                    data: data => {
                        data._token = "{{ csrf_token() }}";
                        data.keyword = data.search.value;
                        data.search.value = '';
                        Object.assign(data, params());
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'project_number',
                        name: 'c.project_number'
                    }, {
                        data: 'project_code',
                        name: 'c.project_code'
                    },
                    {
                        data: 'project_type',
                        orderable: false
                    }, {
                        data: 'renewal_count',
                        name: 'c.renewal_count'
                    },
                    {
                        data: 'parent_project_number',
                        name: 'pc.project_number',
                        defaultContent: '-'
                    }, {
                        data: 'contract_status_name',
                        name: 'c.contract_status'
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
                        data: 'contract_type',
                        name: 'ct.contract_type',
                        defaultContent: '-'
                    },
                    {
                        data: 'property_name',
                        name: 'p.property_name'
                    }, {
                        data: 'area_name',
                        name: 'a.area_name'
                    },
                    {
                        data: 'locality_name',
                        name: 'l.locality_name'
                    }, {
                        data: 'start_date',
                        name: 'cd.start_date'
                    },
                    {
                        data: 'end_date',
                        name: 'cd.end_date'
                    }, {
                        data: 'duration_in_months',
                        name: 'cd.duration_in_months'
                    },
                    amount('contract_fee', 'cd.contract_fee'),
                    amount('rent_per_annum_payable', 'cr.rent_per_annum_payable'),
                    amount('total_payment_to_vendor', 'cr.total_payment_to_vendor'),
                    amount('paid_amount', 'pay.paid_to_vendor'),
                    amount('vendor_balance', 'vendor_balance'),
                    amount('rent_receivable_per_annum', 'cr.rent_receivable_per_annum'),
                    amount('commission', 'cr.commission'),
                    amount('deposit', 'cr.deposit'),
                    amount('cost_of_development', 'cc.cost_of_development'),
                    amount('cost_of_bed', 'cc.cost_of_bed'),
                    amount('cost_of_matress', 'cc.cost_of_matress'),
                    amount('appliances', 'cc.appliances'),
                    amount('decoration', 'cc.decoration'),
                    amount('dewa_deposit', 'cc.dewa_deposit'),
                    amount('cost_of_cabinets', 'cc.cost_of_cabinets'),
                    amount('total_otc', 'cr.total_otc'),
                    amount('final_cost', 'cr.final_cost'),
                    amount('initial_investment', 'cr.initial_investment'),
                    amount('expected_profit', 'cr.expected_profit'),
                    {
                        data: 'roi_perc',
                        name: 'cr.roi_perc',
                        className: 'amount-column'
                    },
                    {
                        data: 'no_of_units',
                        name: 'cu.no_of_units'
                    },
                    {
                        data: 'no_of_floors',
                        name: 'cu.no_of_floors'
                    }, {
                        data: 'total_subunit_count_per_contract',
                        name: 'cu.total_subunit_count_per_contract'
                    },
                    amount('total_payment_pending', 'cu.total_payment_pending'),
                    amount('total_payment_received', 'cu.total_payment_received'),
                    {
                        data: 'installment_name',
                        orderable: false
                    },
                    {
                        data: 'installment_payment_progress',
                        orderable: true,
                        defaultContent: '0/0'
                    },

                    {
                        data: 'terminated_date',
                        name: 'c.terminated_date'
                    },
                    amount('balance_amount', 'c.balance_amount')

                ],
                order: [
                    [1, 'desc']
                ],
                dom: 'Bfrtip',
                buttons: [{
                    text: '<i class="fa fa-file-excel"></i> Export Excel',
                    className: 'btn btn-success',
                    action: () => window.location.href =
                        "{{ route('project-report.export') }}?" + $.param({
                            ...params(),
                            keyword: table.search()
                        })
                }]
            });
            $('#applyFilter').on('click', () => table.ajax.reload());
            $('#resetFilter').on('click', function() {
                $('#companyFilter, #vendorFilter, #propertyFilter, #areaFilter, #localityFilter, #statusFilter')
                    .val('').trigger('change');
                $('#dateFrom, #dateTo').val('');
                table.search('');
                table.ajax.reload();
            });
        });
    </script>
@endsection
