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


                                <div class="card card-df card-outline">
                                    <div class="card-header shadow-sm">
                                        <h5 class="card-title mb-0">Filter</h5>
                                    </div>
                                    <div class="d-flex justify-content-end mx-4">
                                        <button type="button" class="btn btn-secondary reset">
                                            <i class="fa fa-undo-alt"></i> Reset
                                        </button>
                                    </div>

                                    <div class="card-body">
                                        <form class="form-row align-items-end fileterform justify-content-end">
                                            <!-- From Date -->
                                            <div class="form-group col-md-2">
                                                <label for="dateFrom">From</label>
                                                <div class="input-group date" id="dateFrom" data-target-input="nearest">
                                                    <input type="text" class="form-control datetimepicker-input"
                                                        data-target="#dateFrom" placeholder="dd-mm-YYYY" />
                                                    <div class="input-group-append" data-target="#dateFrom"
                                                        data-toggle="datetimepicker">
                                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- To Date -->
                                            <div class="form-group col-md-2">
                                                <label for="dateTo">To</label>
                                                <div class="input-group date" id="dateTo" data-target-input="nearest">
                                                    <input type="text" class="form-control datetimepicker-input"
                                                        data-target="#dateTo" placeholder="dd-mm-YYYY" />
                                                    <div class="input-group-append" data-target="#dateTo"
                                                        data-toggle="datetimepicker">
                                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Property -->
                                            <div class="form-group col-md-2">
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
                                            <div class="form-group col-md-2">
                                                <label for="projectSelect">Project</label>
                                                <select class="form-control select2" id="projectSelect" name="contract_id">
                                                    <option value="">Select Project</option>
                                                    @foreach ($contracts as $contract)
                                                        <option value="{{ $contract->id }}">
                                                            {{ $contract->project_number }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <!-- Unit -->
                                            <div class="form-group col-md-2">
                                                <label for="unitSelect">Unit</label>
                                                <select class="form-control select2" id="unitSelect" name="unit_id">
                                                    <option value="">Select Unit</option>
                                                    {{-- @foreach ($units as $unit)
                                                        <option value="{{ $unit->id }}">{{ $unit->unit_number }}
                                                        </option>
                                                    @endforeach --}}
                                                </select>
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label for="unitSelect">Tenant</label>
                                                <select class="form-control select2" id="tenantSelect" name="tenant_id">
                                                    <option value="">Select Tenant</option>
                                                    @foreach ($tenants as $tenant)
                                                        <option value="{{ $tenant->id }}">{{ $tenant->tenant_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>


                                            <!-- Search Button -->
                                            <div class="form-group col-md-2">
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

                                    <div class="card-body">
                                        {{-- <div class="mb-3 text-center">
                                            <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                                <label class="btn btn-outline-primary active">
                                                    <input type="radio" name="invoiceFilter" value="all"
                                                        autocomplete="off" checked> All
                                                </label>
                                                <label class="btn btn-outline-warning">
                                                    <input type="radio" name="invoiceFilter" value="0"
                                                        autocomplete="off">
                                                    Pending
                                                </label>
                                                <label class="btn btn-outline-maroon">
                                                    <input type="radio" name="invoiceFilter" value="1"
                                                        autocomplete="off">
                                                    Generated
                                                </label>
                                                <label class="btn btn-outline-success">
                                                    <input type="radio" name="invoiceFilter" value="2"
                                                        autocomplete="off">
                                                    Approved
                                                </label>
                                                <label class="btn btn-outline-lightblue">
                                                    <input type="radio" name="invoiceFilter" value="3"
                                                        autocomplete="off">
                                                    On Hold
                                                </label>
                                            </div>
                                        </div> --}}
                                        <div class="d-flex justify-content-between align-items-center mb-3">

                                            <!-- LEFT: Filters -->
                                            <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                                <label class="btn btn-outline-primary active">
                                                    <input type="radio" name="invoiceFilter" value="all"
                                                        autocomplete="off" checked> All
                                                </label>

                                                <label class="btn btn-outline-warning">
                                                    <input type="radio" name="invoiceFilter" value="0"
                                                        autocomplete="off"> Pending
                                                </label>

                                                <label class="btn btn-outline-maroon">
                                                    <input type="radio" name="invoiceFilter" value="1"
                                                        autocomplete="off"> Generated
                                                </label>

                                                {{-- <label class="btn btn-outline-success">
                                                    <input type="radio" name="invoiceFilter" value="2"
                                                        autocomplete="off"> Approved
                                                </label> --}}

                                                <label class="btn btn-outline-lightblue">
                                                    <input type="radio" name="invoiceFilter" value="3"
                                                        autocomplete="off"> On Hold
                                                </label>
                                            </div>

                                            <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                                <label class="btn btn-outline-info active">
                                                    <input type="radio" name="companyFileter" value="all"
                                                        autocomplete="off" checked> All
                                                </label>
                                                @foreach ($companies as $company)
                                                    <label class="btn btn-outline-info">
                                                        <input type="radio" name="companyFileter" class="companyFilter"
                                                            value="{{ $company->id }}" autocomplete="off">
                                                        {{ $company->company_short_code }}
                                                    </label>
                                                @endforeach
                                            </div>

                                            <!-- RIGHT: Extra Button -->
                                            <div>
                                                <a href="{{ route('invoices.generated') }}" target="_blank"
                                                    class="btn bg-gray">
                                                    <i class="fas fa-file-invoice"></i> Approved Invoices
                                                </a>
                                            </div>

                                        </div>



                                        <!-- Table -->
                                        <div class="card">
                                            <div class="card-body table-responsive">
                                                <table id="invoiceTable" class="table table-striped  nowrap projects"
                                                    style="width:100%">
                                                    <thead>
                                                        <tr>
                                                            {{-- <th class="text-center">
                                                                <input type="checkbox" id="selectAll"
                                                                    onclick="toggleAllCheckboxes()">Select All
                                                            </th> --}}

                                                            <th>#</th>
                                                            <th>Action</th>
                                                            <th>Status</th>

                                                            <th>Project</th>
                                                            <th>Company</th>
                                                            <th>Tenant</th>
                                                            <th>Building</th>
                                                            <th>Unit</th>
                                                            {{-- <th>Subunit</th> --}}
                                                            <th>Due Date</th>
                                                            {{-- <th>Payment Mode</th> --}}
                                                            <th>Amount</th>
                                                            <th>Composition</th>
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












            <!-- /.modal -->
        </section>
    </div>
    @include('admin.invoices.invoice_modals')
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

        $(document).ready(function() {
            // $('.searchCheque').hide();

            // $('.propertyselect').hide();
            // $('.unitselect').hide();
        });

        $(document).on('click', '.open-generate-modal', function() {

            let paymentDetailid = $(this).data('id');
            let tenant = $(this).data('tenant');
            let unit = $(this).data('unit');
            let trn = $(this).data('trn');
            let tenantId = $(this).data('tenant-id');
            let agreementId = $(this).data('agreement-id');
            let contractId = $(this).data('contract-id');
            let contractUnitId = $(this).data('contract-unit-id');
            let agreementUnitId = $(this).data('agreement-unit-id');
            let totalAmount = $(this).data('total-amount');
            let payment_date = $(this).data('payment-date'); // e.g. 2026-12-26


            // ✅ Use moment (BEST for your case)
            let date = moment(payment_date, ['YYYY-MM-DD', 'DD-MM-YYYY']);

            // Start & End of month
            let startDate = date.clone().startOf('month').format('DD-MM-YYYY');
            let endDate = date.clone().endOf('month').format('DD-MM-YYYY');

            // Set values
            $('#gMonthStart').val(startDate);
            $('#gMonthEnd').val(endDate);

            // Make readonly
            $('#gMonthStart').prop('readonly', true);
            $('#gMonthEnd').prop('readonly', true);

            // Update datetimepicker UI
            $('#monthStartPicker').datetimepicker('date', moment(startDate, 'DD-MM-YYYY'));
            $('#monthEndPicker').datetimepicker('date', moment(endDate, 'DD-MM-YYYY'));

            $('#gpaymentDetailId').val(paymentDetailid);
            $('#gTenantName').text(tenant);
            $('#gUnitNo').text(unit);
            $('#gTrnNumber').val(trn);
            $('#gTenantId').val(tenantId);
            $('#gAgreementId').val(agreementId);
            $('#gContractId').val(contractId);
            $('#gContractUnitId').val(contractUnitId);
            $('#gAgreementUnitId').val(agreementUnitId);
            $('#gPaymentDetailId').val(paymentDetailid);
            $('#gTotalAmount').val(totalAmount);


            $('#generateModal').modal('show');
        });
        $(document).on('click', '.open-approve-modal', function() {
            const invoiceNumber = $(this).data('invoice-number');
            const tenant = $(this).data('tenant');
            const approveUrl = $(this).data('approve-url');

            $('#approve-invoice-number').text('#' + invoiceNumber);
            $('#approve-tenant-name').text(tenant);
            $('#approve-invoice-form').attr('action', approveUrl);
            $('#approval_comment').val('');

            $('#approveInvoiceModal').modal('show');
        });
        $('#approve-invoice-form').on('submit', function(e) {
            e.preventDefault();

            const form = $(this);

            // 🔥 Get the actual button that triggered submit
            const submitter = e.originalEvent.submitter;
            const status = $(submitter).data('status');

            const submitBtn = $(submitter);

            // disable only clicked button
            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Processing...');

            // append status to form data
            let formData = form.serializeArray();
            formData.push({
                name: 'status',
                value: status
            });

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: $.param(formData),
                success: function(response) {
                    $('#approveInvoiceModal').modal('hide');
                    $('#invoiceTable').DataTable().ajax.reload();
                    toastr.success(response.message ?? 'Success');
                },
                error: function(xhr) {
                    const message = xhr.responseJSON?.message ?? 'Something went wrong.';
                    toastr.error(message);
                },
                complete: function() {
                    submitBtn.prop('disabled', false).html(
                        submitBtn.data('status') == 3 ?
                        '<i class="fas fa-pause mr-1"></i> On Hold' :
                        '<i class="fas fa-check mr-1"></i> Approve'
                    );
                }
            });
        });

        $('#monthStartPicker').datetimepicker({
            format: 'DD-MM-YYYY'
        });

        $('#monthEndPicker').datetimepicker({
            format: 'DD-MM-YYYY'
        });
        $('#invoiceDatePicker').datetimepicker({
            format: 'DD-MM-YYYY'
        });
        $('#editInvoiceDatePicker').datetimepicker({
            format: 'DD-MM-YYYY'
        });
        // Format to YYYY-MM-DD
        function formatDate(d) {
            let month = String(d.getMonth() + 1).padStart(2, '0');
            let day = String(d.getDate()).padStart(2, '0');
            return `${d.getFullYear()}-${month}-${day}`;
        }
        let units = @json($units);
        let tenants = @json($tenants);
        let agreements = @json($agreements);
        let banks = @json($banks);
        let contracts = @json($contracts);
        // console.log("units", units);

        $(document).on('change', '#propertySelect', function() {
            propertyChange();
        });

        function propertyChange() {
            let property_id = $('#propertySelect').val();
            // let unitSelect = $('#unitSelect');

            // unitSelect.empty();
            // unitSelect.append('<option value="">Select Unit</option>');

            // if (property_id) {
            //     let filteredUnits = units.filter(u => u.contract.property_id == property_id);
            //     console.log("filteredUnits", filteredUnits);

            //     filteredUnits.forEach(u => {
            //         unitSelect.append('<option value="' + u.id + '">' + u.unit_number + '</option>');
            //     });
            // }

            // unitSelect.trigger('change');

            let projectSelect = $('#projectSelect');
            projectSelect.empty();
            projectSelect.append('<option value="">Select Project</option>');
            let filteredContracts = contracts.filter(c => c.property_id == property_id);
            filteredContracts.forEach(c => {
                projectSelect.append(
                    `<option value="${c.id}">
                        ${c.project_number}
                    </option>`
                );
            });
            projectSelect.trigger('change');
        }
        $(document).on('change', '#projectSelect', function() {
            projectChange();
        });

        function projectChange() {
            let project_id = $('#projectSelect').val();
            let unitSelect = $('#unitSelect');

            unitSelect.empty();
            unitSelect.append('<option value="">Select Unit</option>');

            if (project_id) {
                let filteredUnits = units.filter(u => u.contract_id == project_id);
                console.log("filteredUnits", filteredUnits);

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
            // alert("test");
            let unit_id = $('#unitSelect').val();
            let tenantSelect = $('#tenantSelect');
            // alert(unit_id);

            tenantSelect.empty();
            tenantSelect.append('<option value="">Select Tenant</option>');

            if (!unit_id) return;

            let addedTenants = new Set();

            agreements.forEach(agreement => {
                // Check if this agreement has the selected unit
                // console.log("agreement", agreement)
                let hasUnit = agreement.agreement_units?.some(
                    au => au.contract_unit_details_id == unit_id
                );
                // console.log("Checking agreement: ", agreement.id, "hasUnit: ", hasUnit);

                if (hasUnit && agreement.tenant) {
                    // console.log("agreement tenant: ", agreement.tenant);
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
            // console.log("added tenants: ", addedTenants);

            tenantSelect.trigger('change');


        }
        $(document).on('click', '.reset', function() {
            $('.fileterform').trigger('reset');

            $('#propertySelect').val(null).trigger('change');
            $('#unitSelect').val(null).trigger('change');
            $('#modeSelect').val(null).trigger('change');

            $('#dateFrom input').val('');
            $('#dateTo input').val('');
            $('#tenantSelect').val(null).trigger('change');
            showLoader();

            setTimeout(() => {
                window.location.reload();
            }, 1000);
        });
    </script>
    <script>
        let table = '';
        $(function() {
            table = $('#invoiceTable').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 10,
                responsive: true,


                ajax: {
                    url: "{{ route('invoices.list') }}",
                    data: function(d) {
                        let companyId = $('.companyFilter:checked').val() || 'all';
                        if (companyId === 'all') {
                            companyId = null;
                        }
                        d.company_id = companyId;
                        d.date_from = $('#dateFrom input').val();
                        d.date_to = $('#dateTo input').val();
                        d.property_id = $('#propertySelect').val();
                        d.unit_id = $('#unitSelect').val();
                        d.mode_id = $('#modeSelect').val();
                        d.tenant_id = $('#tenantSelect').val();
                        d.contract_id = $('#projectSelect').val();
                        d.status = $('input[name="invoiceFilter"]:checked').val();

                    },
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'agreement_payment_details.id',
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
                        data: 'status',
                        name: 'status',

                        orderable: true,
                        searchable: true
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
                        data: 'payment_date',
                        name: 'agreement_payment_details.payment_date',

                    },

                    {
                        data: 'payment_amount',
                        name: 'agreement_payment_details.payment_amount',
                    },

                    {
                        data: 'installment_name',
                        name: 'agreementPayment.installment.installment_name',
                    },







                ],
                // rowCallback: function(row, data, index) {
                //     if (data.is_payment_received == 0 && data.has_bounced == 1) {
                //         $(row).css('background-color', '#f8d7da');
                //         $(row).css('color', '#721c24');
                //     }
                // },

                order: [
                    [0, 'desc']
                ],

            });
        });
        $('input[name="invoiceFilter"]').on('change', function() {
            table.ajax.reload();
            setTimeout(function() {
                table.columns.adjust().responsive.recalc();
            }, 200);
        });

        $('input[name="companyFileter"]').on('change', function() {
            // alert("test");
            table.ajax.reload();
        });

        function validateformContainer(formContainer) {
            let isValid = true;

            // Select all required fields except radio buttons
            formContainer.querySelectorAll('[required]:not([type="radio"])').forEach(field => {
                // Skip hidden fields
                if (field.offsetParent === null) return;

                // Use browser validation API
                if (!field.checkValidity()) {
                    field.classList.add('is-invalid');
                    field.classList.remove('is-valid');
                    isValid = false;
                } else {
                    field.classList.remove('is-invalid');
                    field.classList.add('is-valid');
                }
            });

            return isValid;
        }
        $('.searchbtnchq').click(function() {
            $('.searchCheque').show();
            const searchformContainer = document.querySelector('.fileterform');
            if (!validateformContainer(searchformContainer)) {

                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'warning',
                    title: "Please fill all required fields correctly!",
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
                return;
            }
            table.ajax.reload();
        });
    </script>
    <script>
        $('#generateInvoiceForm').on('submit', function(e) {
            e.preventDefault();

            let form = $(this);
            let btn = $('#generateInvoiceForm button[type="submit"]');
            showLoader();

            $.ajax({
                url: "{{ route('invoices.store') }}",
                type: "POST",
                data: form.serialize(),
                beforeSend: function() {
                    btn.prop('disabled', true).html(
                        '<i class="fa fa-spinner fa-spin"></i> Processing...');
                },
                success: function(res) {

                    toastr.success('Invoice generated successfully');

                    $('#generateModal').modal('hide');
                    form[0].reset();
                    hideLoader();

                    location.reload();
                },
                error: function(xhr) {
                    hideLoader();

                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            toastr.error(value[0]);
                        });
                    } else {
                        toastr.error('Something went wrong');
                    }

                },
                complete: function() {
                    btn.prop('disabled', false).html(
                        '<i class="fas fa-file-pdf mr-1"></i> Generate Invoice');
                    hideLoader();
                }
            });
        });
        $(document).on('click', '.open-edit-modal', function() {

            $('#eInvoiceId').val($(this).data('id'));
            $('#eTenantName').text($(this).data('tenant'));
            $('#eUnitNo').text($(this).data('unit'));

            $('#eInvoiceDate').val($(this).data('date'));
            $('#eTrnNumber').val($(this).data('trn'));
            $('#eMonthStart').val($(this).data('start'));
            $('#eMonthEnd').val($(this).data('end'));
            $('#eTotalAmount').val($(this).data('amount'));
            $('#eTenantId').val($(this).data('tenant-id'))

            $('#editModal').modal('show');
        });
        $('#editInvoiceForm').on('submit', function(e) {
            e.preventDefault();

            let form = $(this);
            let id = $('#eInvoiceId').val(); // hidden input
            let btn = $('#editInvoiceForm button[type="submit"]');
            showLoader();

            $.ajax({
                url: "{{ url('invoices') }}/" + id, // OR use route if you prefer
                type: "PUT", // or PUT (see note below)
                data: form.serialize(),
                beforeSend: function() {
                    btn.prop('disabled', true).html(
                        '<i class="fa fa-spinner fa-spin"></i> Updating...'
                    );
                },
                success: function(res) {


                    $('#editModal').modal('hide');
                    form[0].reset();
                    location.reload(); // or datatable reload
                    hideLoader();
                    toastr.success('Invoice updated successfully');


                },
                error: function(xhr) {
                    hideLoader();
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            toastr.error(value[0]);
                        });
                    } else {
                        toastr.error('Something went wrong');
                    }

                },
                complete: function() {
                    btn.prop('disabled', false).html(
                        '<i class="fas fa-save mr-1"></i> Update Invoice'
                    );
                    hideLoader();
                }
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
                        url: "{{ route('invoices.destroy', ':id') }}".replace(':id', id),
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        dataType: "json",
                        success: function(response) {
                            toastr.success(response.message);
                            $('#invoiceTable').DataTable().ajax.reload();
                        }
                    });

                }
            });
        }
    </script>
    <script>
        $(document).on('click', '.loadComments', function() {

            let invoiceId = $(this).data('id');

            let url = "{{ route('invoices.comments', ':id') }}";
            url = url.replace(':id', invoiceId);

            $('#commentModal').modal('show');
            $('#commentList').html('Loading...');

            $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {

                    $('#commentList').html(response.html);
                },
                error: function() {
                    $('#commentList').html('<p class="text-danger">Failed to load comments</p>');
                }
            });

        });
    </script>

    {{-- Filetr section --}}
@endsection
