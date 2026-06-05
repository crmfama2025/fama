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






                                <!-- /.card -->




                                <!-- ===== AMOUNT INPUT + ALLOCATION PANEL ===== -->

                                <!-- ===== END ALLOCATION PANEL ===== -->


                                <div class="card searchCheque">

                                    <div class="card-body">

                                        <div class="d-flex justify-content-between align-items-center mb-3">



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
                                                <a href="{{ route('invoices.index') }}" class="btn bg-gray">
                                                    <i class="fas fa-file-invoice"></i> Back to List
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
        let table = '';
        $(function() {
            table = $('#invoiceTable').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 10,
                responsive: true,


                ajax: {
                    url: "{{ route('invoices.generated-list') }}",
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
                        d.status = $('input[name="invoiceFilter"]:checked').val();

                    },
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'tenant_invoices.id',
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
                        name: 'agreementPaymentDetail.payment_date',

                    },

                    {
                        data: 'payment_amount',
                        name: 'agreementPaymentDetail.payment_amount',
                    },

                    {
                        data: 'installment_name',
                        name: 'agreementPaymentDetail.agreementPayment.installment.installment_name',
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
    </script>
@endsection
