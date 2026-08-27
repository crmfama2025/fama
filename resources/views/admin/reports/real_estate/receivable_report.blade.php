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
@endsection

@section('content')
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
                            <li class="breadcrumb-item"><a href="../dashboard.php">Home</a></li>
                            <li class="breadcrumb-item active">{{ $title }}</li>
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
                            <!-- /.card-header -->
                            <div class="card-body">



                                <div class="card card-danger card-outline">
                                    <div class="card-header shadow-sm">
                                        <h5 class="card-title mb-0">Filter Receivables</h5>
                                    </div>
                                    <div class="d-flex justify-content-end mx-4 my-2">
                                        <button type="button" class="btn btn-secondary reset">
                                            <i class="fa fa-undo-alt"></i> Reset
                                        </button>
                                    </div>

                                    <div class="card-body">
                                        <form class="form-row align-items-end fileterform ">
                                            <!-- From Date -->
                                            <div class="form-group col-lg-3 col-md-4">
                                                <label for="dateFrom" class="asterisk">Payment Date From</label>
                                                <div class="input-group date" id="dateFrom" data-target-input="nearest">
                                                    <input type="text" class="form-control datetimepicker-input" required
                                                        data-target="#dateFrom" placeholder="dd-mm-YYYY" />
                                                    <div class="input-group-append" data-target="#dateFrom"
                                                        data-toggle="datetimepicker">
                                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- To Date -->
                                            <div class="form-group col-lg-3 col-md-4">
                                                <label for="dateTo" class="asterisk">Payment Date To</label>
                                                <div class="input-group date" id="dateTo" data-target-input="nearest">
                                                    <input type="text" class="form-control datetimepicker-input" required
                                                        data-target="#dateTo" placeholder="dd-mm-YYYY" />
                                                    <div class="input-group-append" data-target="#dateTo"
                                                        data-toggle="datetimepicker">
                                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group col-lg-3 col-md-4">
                                                <label for="propertySelect">Company</label>
                                                <select class="form-control select2" id="companySelect" name="company_id">
                                                    <option value="">Select Company</option>
                                                    @foreach ($companies as $comp)
                                                        <option value="{{ $comp->id }}">
                                                            {{ $comp->company_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <!-- Project Number -->
                                            <div class="form-group col-lg-3 col-md-4">
                                                <label for="projectSelect">Project Number</label>
                                                <select class="form-control select2" id="projectSelect" name="contract_id">
                                                    <option value="">Select Project Number</option>
                                                </select>
                                            </div>


                                            <!-- Property -->
                                            <div class="form-group col-lg-3 col-md-4">
                                                <label for="propertySelect">Property</label>
                                                <select class="form-control select2" id="propertySelect" name="property_id">
                                                    <option value="">Select Property</option>
                                                    @foreach ($properties as $property)
                                                        <option value="{{ $property->id }}">
                                                            {{ $property->property_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <!-- Unit -->
                                            <div class="form-group col-lg-3 col-md-4">
                                                <label for="unitSelect">Unit</label>
                                                <select class="form-control select2" id="unitSelect" name="unit_id">
                                                    <option value="">Select Unit</option>
                                                    {{-- @foreach ($units as $unit)
                                                        <option value="{{ $unit->id }}">{{ $unit->unit_number }}
                                                        </option>
                                                    @endforeach --}}
                                                </select>
                                            </div>
                                            <div class="form-group col-lg-3 col-md-4">
                                                <label for="unitSelect">Tenant</label>
                                                <select class="form-control select2" id="tenantSelect" name="tenant_id">
                                                    <option value="">Select Tenant</option>
                                                    @foreach ($tenants as $tenant)
                                                        <option value="{{ $tenant->id }}">{{ $tenant->tenant_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <!-- Unit -->
                                            <div class="form-group col-lg-3 col-md-4">
                                                <label for="unitSelect">Payment Mode</label>
                                                <select class="form-control select2" id="modeSelect" name="mode_id">
                                                    <option value="">Select PaymentMode</option>
                                                    @foreach ($payment_modes as $mode)
                                                        <option value="{{ $mode->id }}">
                                                            {{ $mode->payment_mode_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="form-group col-lg-3 col-md-4">
                                                <label for="dateFrom" class="asterisk">Paid Date From</label>
                                                <div class="input-group date" id="paiddateFrom"
                                                    data-target-input="nearest">
                                                    <input type="text" class="form-control datetimepicker-input"
                                                        required data-target="#paiddateFrom" placeholder="dd-mm-YYYY" />
                                                    <div class="input-group-append" data-target="#paiddateFrom"
                                                        data-toggle="datetimepicker">
                                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- To Date -->
                                            <div class="form-group col-lg-3 col-md-4">
                                                <label for="dateTo" class="asterisk">Paid Date To</label>
                                                <div class="input-group date" id="paiddateTo"
                                                    data-target-input="nearest">
                                                    <input type="text" class="form-control datetimepicker-input"
                                                        required data-target="#paiddateTo" placeholder="dd-mm-YYYY" />
                                                    <div class="input-group-append" data-target="#paiddateTo"
                                                        data-toggle="datetimepicker">
                                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group col-lg-3 col-md-4">
                                                <label for="paymentStatusSelect">Payment Status</label>
                                                <select class="form-control select2" id="paymentStatusSelect"
                                                    name="is_payment_received">
                                                    <option value="">All Status</option>
                                                    <option value="0">Pending</option>
                                                    <option value="1">Received</option>
                                                    <option value="2">Partially Received</option>
                                                    <option value="3">Bounced</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-lg-3 col-md-4">
                                                <label for="propertySelect">Paid Company</label>
                                                <select class="form-control select2" id="paidCompanySelect"
                                                    name="paid_company_id">
                                                    <option value="">Select Paid Company</option>
                                                    @foreach ($companies as $comp)
                                                        <option value="{{ $comp->id }}">
                                                            {{ $comp->company_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group col-lg-3 col-md-4">
                                                <label for="invoiceSelect">Invoice Status</label>
                                                <select class="form-control select2" id="invoiceSelect"
                                                    name="is_invoice_added">
                                                    <option value="">All Status</option>
                                                    <option value="0">Pending</option>
                                                    <option value="1">Added</option>
                                                </select>
                                            </div>

                                            <!-- Search Button -->
                                            <div class="form-group col-lg-3 col-md-4">
                                                <button type="button" class="btn btn-primary btn-block searchbtnchq">
                                                    <i class="fa fa-search"></i> Search
                                                </button>
                                            </div>

                                        </form>
                                    </div>
                                </div>

                                <!-- /.card -->




                                <!-- ===== AMOUNT INPUT + ALLOCATION PANEL ===== -->

                                <!-- ===== END ALLOCATION PANEL ===== -->


                                <div class="card searchCheque">
                                    <!-- /.card-header -->

                                    <div class="card-body">

                                        <!-- Company Filter Buttons -->
                                        {{-- <div class="card card-info mb-3">
                                            <div class="card-body text-center">
                                                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                                    <label class="btn btn-outline-info active">
                                                        <input type="radio" name="companyFileter" value="all"
                                                            autocomplete="off" checked> All
                                                    </label>
                                                    @foreach ($companies as $company)
                                                        <label class="btn btn-outline-info">
                                                            <input type="radio" name="companyFileter"
                                                                class="companyFilter" value="{{ $company->id }}"
                                                                autocomplete="off">
                                                            {{ $company->company_short_code }}
                                                        </label>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div> --}}

                                        <!-- Table -->
                                        <div class="card">
                                            <div class="card-body table-responsive">
                                                <table id="tenantChequeTable" class="table table-striped  nowrap"
                                                    style="width:100%">
                                                    <thead>
                                                        <tr>

                                                            <th>#</th>

                                                            <th>Project</th>
                                                            <th>Company</th>
                                                            <th>Tenant</th>
                                                            <th>Building</th>
                                                            <th>Unit</th>
                                                            <th>Subunit</th>
                                                            <th>Due Date</th>
                                                            <th>Payment Mode</th>
                                                            <th>Amount</th>
                                                            <th>Composition</th>
                                                            {{-- <th>Total Installments</th> --}}
                                                            <th>Paid Amount</th>
                                                            <th>Pending Amount</th>
                                                            <th>Paid Date</th>
                                                            <th>Paid Mode</th>
                                                            <th>Paid Bank</th>
                                                            <th>Paid Cheque Number</th>
                                                            <th>Paid Company Name</th>
                                                            <th>Has Bounced</th>
                                                            <th>Bounced Reason</th>
                                                            <th>Bounced Date</th>
                                                            <th>Status</th>
                                                            <th>Terminate Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <!-- Data populated by DataTables / Blade -->
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                    </div>

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
    <script src="{{ asset('assets/datatables-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('assets/datatables-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('assets/datatables-buttons/js/buttons.colVis.min.js') }}"></script>
    {{-- <script src="{{ asset('assets/icheck-bootstrap/icheck.min.js') }}"></script> --}}


    <script>
        $('#dateFrom').datetimepicker({
            format: 'DD-MM-YYYY'
        });

        $('#dateTo').datetimepicker({
            format: 'DD-MM-YYYY'
        });
        $('#paiddateFrom').datetimepicker({
            format: 'DD-MM-YYYY'
        });

        $('#paiddateTo').datetimepicker({
            format: 'DD-MM-YYYY'
        });


        $('#fromDateAllocationPicker').datetimepicker({
            format: 'DD-MM-YYYY'
        });

        $('#toDateAllocationPicker').datetimepicker({
            format: 'DD-MM-YYYY'
        });

        $(document).ready(function() {
            // $('.searchCheque').hide();

            // $('.propertyselect').hide();
            // $('.unitselect').hide();
        });

        $('.searchbtnchq').click(function() {
            $('.searchCheque').show();
            const searchformContainer = document.querySelector('.fileterform');
            // if (!validateformContainer(searchformContainer)) {

            //     Swal.fire({
            //         toast: true,
            //         position: 'top-end',
            //         icon: 'warning',
            //         title: "Please fill all required fields correctly!",
            //         showConfirmButton: false,
            //         timer: 3000,
            //         timerProgressBar: true
            //     });
            //     return;
            // }
            table.ajax.reload();
        });



        function toggleAllCheckboxes() {
            document.getElementById('selectAll').addEventListener('change', function() {
                const itemCheckboxes = document.querySelectorAll('.groupCheckbox');
                itemCheckboxes.forEach(checkbox => {
                    checkbox.checked = this
                        .checked;
                });
            });
        }

        $('#clearingdate').datetimepicker({
            format: 'DD-MM-YYYY'
        });
        $('#clearingdateSingle').datetimepicker({
            format: 'DD-MM-YYYY'
        });

        $('#bouncedDate').datetimepicker({
            format: 'DD-MM-YYYY'
        });
    </script>
    <script>
        let table = '';
        $(function() {
            table = $('#tenantChequeTable').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 10,
                responsive: true,
                searching: false,


                ajax: {
                    url: "{{ route('receivable-report.list') }}",
                    data: function(d) {
                        // let companyId = $('.companyFilter:checked').val() || 'all';
                        // if (companyId === 'all') {
                        //     companyId = null;
                        // }
                        d.company_id = $('#companySelect').val() || null;
                        d.date_from = $('#dateFrom input').val();
                        d.date_to = $('#dateTo input').val();
                        d.property_id = $('#propertySelect').val();
                        d.unit_id = $('#unitSelect').val();
                        d.mode_id = $('#modeSelect').val();
                        d.tenant_id = $('#tenantSelect').val();
                        // Paid Date
                        d.paid_date_from = $('#paiddateFrom input').val();
                        d.paid_date_to = $('#paiddateTo input').val();
                        d.contract_id = $('#projectSelect').val() || null;

                        // Payment Status
                        d.is_payment_received =
                            $('#paymentStatusSelect').val() || null;

                        // Paid Company
                        d.paid_company_id =
                            $('#paidCompanySelect').val() || null;

                        // Invoice Status
                        d.is_invoice_added =
                            $('#invoiceSelect').val() || null;

                    },
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'apd.id',
                        orderable: true,
                        searchable: false
                    },

                    {
                        data: 'project_number',
                        name: 'agreement.contract.project_number',
                        render: function(data, type, row) {
                            return data ? data : '';
                        },
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: 'company_name',
                        name: 'agreement.contract.company.company_name',
                    },
                    {
                        data: 'tenant_name',
                        name: 'agreement.tenant.tenant_name',
                        render: function(data, type, row) {
                            return data ? data : '';
                        },
                        orderable: false,
                        searchable: true
                    }, {
                        data: 'property_name',
                        name: 'agreement.contract.property.property_name',
                    },
                    {
                        data: 'unit_number',
                        name: 'agreement.agreement_units.contractUnitDetail.unit_number',

                    },
                    {
                        data: 'subunit_no',
                        name: 'agreement.agreement_units.contractSubunitDetail.subunit_no',
                        render: function(data, type, row) {
                            return data ? data : ' - ';
                        },
                        orderable: false,
                        searchable: true

                    },
                    {
                        data: 'payment_date',
                        name: 'agreement_payment_details.payment_date',

                    },
                    {
                        data: 'payment_mode_name',
                        name: 'paymentMode.payment_mode_name',
                    },
                    // {
                    //     data: 'payment_amount',
                    //     name: 'agreement_payment_details.payment_amount',
                    // },
                    // {
                    //     data: 'installment',
                    //     name: 'installment',
                    // },

                    // {
                    //     data: 'installment_name',
                    //     name: 'agreementPayment.installment.installment_name',
                    // },
                    {
                        data: 'composition',
                        name: 'composition.installment_position',
                        orderable: true,
                        searchable: false
                    },

                    {
                        data: 'installment_name',
                        name: 'installment.installment_name',
                    },
                    {
                        data: 'paid_amount',
                        name: 'paid_amount',
                        defaultContent: '0.00'
                    },
                    {
                        data: 'pending_amount',
                        name: 'pending_amount',
                        defaultContent: '0.00'
                    },
                    {
                        data: 'paid_date',
                        name: 'paid_date',
                        defaultContent: '-'
                    },
                    {
                        data: 'paid_mode',
                        name: 'paid_mode',
                        defaultContent: '-'
                    },
                    {
                        data: 'paid_bank',
                        name: 'paid_bank',
                        defaultContent: '-'
                    },
                    {
                        data: 'paid_cheque_number',
                        name: 'paid_cheque_number',
                        defaultContent: '-'
                    },
                    {
                        data: 'paid_company',
                        name: 'paid_company',
                        defaultContent: '-'
                    },

                    {
                        data: 'is_payment_received',
                        name: 'is_payment_received',
                        render: function(data, type, row) {
                            // Priority: Bounced
                            if (row.has_bounced) {
                                return '<span class="badge bg-danger">Bounced</span>';
                            }

                            switch (data) {
                                case 0:
                                    return '<span class="badge bg-warning">Pending</span>';
                                case 2:
                                    return '<span class="badge bg-info">Partially Paid</span>';
                                case 1:
                                    return '<span class="badge bg-success">Paid</span>';
                                default:
                                    return '<span class="badge bg-secondary">-</span>';
                            }
                        },
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'has_bounced',
                        name: 'has_bounced',
                        render: function(data, type, row) {
                            if (data == 1) {
                                return '<span class="badge bg-danger">Bounced</span>';
                            }

                            if (data == 0) {
                                return '<span class="badge bg-success">Not Bounced</span>';
                            }

                            return '<span class="badge bg-secondary">-</span>';
                        },
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'bounced_date',
                        name: 'bounced_date',
                        defaultContent: '-',
                        render: function(data, type, row) {
                            if (row.has_bounced != 1 || !data) {
                                return '-';
                            }

                            return data;
                        },
                        orderable: true,
                        searchable: false
                    },
                    {
                        data: 'bounced_reason',
                        name: 'bounced_reason',
                        defaultContent: '-',
                        render: function(data, type, row) {
                            if (row.has_bounced != 1 || !data) {
                                return '-';
                            }

                            return data;
                        },
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'terminate_status',
                        name: 'apd.terminate_status',
                        render: function(data, type, row) {

                            if (data == 1) {
                                return '<span class="badge bg-danger">Terminated</span>';
                            }

                            if (data == 0) {
                                return '<span class="badge bg-success">Not Terminated</span>';
                            }

                            return '<span class="badge bg-secondary">-</span>';
                        },
                        orderable: true,
                        searchable: true
                    }







                ],
                rowCallback: function(row, data, index) {
                    if (data.is_payment_received == 0 && data.has_bounced == 1) {
                        $(row).css('background-color', '#f8d7da');
                        $(row).css('color', '#721c24');
                    }
                },

                order: [
                    [0, 'desc']
                ],
                dom: 'Bfrtip',
                buttons: [{
                    extend: 'excelHtml5',
                    text: 'Export Excel',
                    title: 'Receivables Data',

                    action: function(e, dt, node, config) {

                        let companyId = $('.companyFilter:checked').val() || '';

                        // If "All" is selected, don't send a company ID
                        if (companyId === 'all') {
                            companyId = '';
                        }

                        let params = {

                            // DataTable search
                            search: dt.search(),

                            // Company radio filter
                            company_id: companyId,

                            // Payment Date
                            date_from: $('#dateFrom input').val(),
                            date_to: $('#dateTo input').val(),

                            // Property
                            property_id: $('#propertySelect').val() || '',

                            // Unit
                            unit_id: $('#unitSelect').val() || '',

                            // Tenant
                            tenant_id: $('#tenantSelect').val() || '',

                            // Payment Mode
                            mode_id: $('#modeSelect').val() || '',

                            // Paid Date
                            paid_date_from: $('#paiddateFrom input').val(),
                            paid_date_to: $('#paiddateTo input').val(),

                            // Payment Status
                            is_payment_received: $('#paymentStatusSelect').val() || '',

                            // Paid Company
                            paid_company_id: $('#paidCompanySelect').val() || '',

                            // Invoice Status
                            is_invoice_added: $('#invoiceSelect').val() || '',
                            contract_id: $('#projectSelect').val() || null
                        };

                        console.log('Receivables Export Filters:', params);

                        let url =
                            "{{ route('receivable-report.export') }}" +
                            '?' +
                            $.param(params);

                        window.location.href = url;
                    }
                }]
            });
        });
    </script>

    {{-- Filetr section --}}
    <script>
        let units = @json($units);
        // let tenants = @json($tenants);
        let agreements = @json($agreements);
        let contracts = @json($contracts);
        // let banks = @json($banks);
        // console.log('units', units)
        console.log(agreements);

        $(document).on('change', '#propertySelect', function() {
            propertyChange();
        });

        function propertyChange() {
            let property_id = $('#propertySelect').val();
            let unitSelect = $('#unitSelect');

            unitSelect.empty();
            unitSelect.append('<option value="">Select Unit</option>');

            if (property_id) {
                let filteredUnits = units.filter(u => u.contract.property_id == property_id);

                filteredUnits.forEach(u => {
                    unitSelect.append('<option value="' + u.id + '">' + u.unit_number + '</option>');
                });
            }

            unitSelect.trigger('change');
        }
        $(document).on('change', '#unitSelect', function() {
            unitChange();
        });

        function unitChange() {
            let unit_id = $('#unitSelect').val();
            let tenantSelect = $('#tenantSelect');

            tenantSelect.empty();
            tenantSelect.append('<option value="">Select Tenant</option>');

            if (!unit_id) return;

            let addedTenants = new Set();

            agreements.forEach(agreement => {
                // Check if this agreement has the selected unit
                let hasUnit = agreement.agreement_units?.some(
                    au => au.contract_unit_details_id == unit_id
                );

                if (hasUnit && agreement.tenant) {
                    if (!addedTenants.has(agreement.tenant.id)) {
                        addedTenants.add(agreement.tenant.id);

                        tenantSelect.append(
                            `<option value="${agreement.tenant.id}">
                        ${agreement.tenant.tenant_name}
                    </option>`
                        );
                    }
                }
            });

            tenantSelect.trigger('change');
        }



        $('input[name="companyFileter"]').on('change', function() {
            table.ajax.reload();
        });
        $(document).on('click', '.reset', function() {

            // Clear Company
            $('#companySelect')
                .val('')
                .trigger('change');

            // Clear Property
            $('#propertySelect')
                .val('')
                .trigger('change');

            // Clear Unit
            $('#unitSelect')
                .empty()
                .append('<option value="">Select Unit</option>')
                .val('')
                .trigger('change');

            // Clear Tenant
            $('#tenantSelect')
                .empty()
                .append('<option value="">Select Tenant</option>')
                .val('')
                .trigger('change');

            // Clear Payment Mode
            $('#modeSelect')
                .val('')
                .trigger('change');

            // Clear Payment Status
            $('#paymentStatusSelect')
                .val('')
                .trigger('change');

            // Clear Paid Company
            $('#paidCompanySelect')
                .val('')
                .trigger('change');

            // Clear Invoice Status
            $('#invoiceSelect')
                .val('')
                .trigger('change');

            // Clear dates
            $('#dateFrom input').val('');
            $('#dateTo input').val('');
            $('#paiddateFrom input').val('');
            $('#paiddateTo input').val('');

            // Reload DataTable
            table.ajax.reload(null, true);
        });
        $(document).on('change', '#companySelect', function() {

            let companyId = $(this).val();
            let projectSelect = $('#projectSelect');

            projectSelect.empty();
            projectSelect.append(
                '<option value="">Select Project Number</option>'
            );

            if (!companyId) {
                return;
            }

            contracts
                .filter(contract => contract.company_id == companyId)
                .forEach(contract => {

                    projectSelect.append(
                        `<option value="${contract.id}">
                    ${contract.project_number}
                </option>`
                    );
                });

            projectSelect.trigger('change');
        });
    </script>
@endsection
