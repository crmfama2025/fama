@extends('admin.layout.admin_master')

@section('custom_css')
    <!-- daterange picker -->
    <link rel="stylesheet" href="{{ asset('assets/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet" href="{{ asset('assets/select2/css/select2.min.css') }}">
    <!-- iCheck for checkboxes and radio inputs -->
    <link rel="stylesheet" href="{{ asset('assets/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('assets/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <!-- DataTables -->
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
                            <li class="breadcrumb-item"><a href="../dashboard.php">Home</a></li>
                            <li class="breadcrumb-item active">Investment</li>
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
                        {{-- <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Filters</h5>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <select id="companyFilter" class="form-control select2">
                                            <option value="">All Companies</option>
                                            @foreach ($companies as $company)
                                                @if (!in_array($company->id, [3, 6]))
                                                    <option value="{{ $company->id }}">
                                                        {{ $company->company_name }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-2">
                                        <button id="applyFilter" class="btn btn-primary">Apply</button>
                                        <button id="resetFilter" class="btn btn-secondary">Reset</button>
                                    </div>
                                </div>
                            </div>
                        </div> --}}
                        <div class="card">
                            <div class="card-header">
                                {{-- <span class="float-right">
                                    <a class="btn btn-info float-right m-1" href="{{ route('investment.index') }}">Back to
                                        Investments</a>

                                </span> --}}
                            </div>

                            <!-- /.card-header -->
                            <div class="card-body table-responsive">
                                <div class="mb-3 text-center">
                                    <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                        <label class="btn btn-outline-primary active">
                                            <input type="radio" name="agreementFilter" value="all" autocomplete="off"
                                                checked> All
                                        </label>
                                        <label class="btn btn-outline-success">
                                            <input type="radio" name="agreementFilter" value="1" autocomplete="off">
                                            Pending
                                        </label>
                                        <label class="btn btn-outline-warning">
                                            <input type="radio" name="agreementFilter" value="2" autocomplete="off">
                                            Investor Signed
                                        </label>
                                        <label class="btn btn-outline-danger">
                                            <input type="radio" name="agreementFilter" value="3" autocomplete="off">
                                            Both Signed
                                        </label>
                                    </div>
                                </div>
                                <table id="investmentContractsTable" class="table table-striped  nowrap"width="100%">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Action</th>
                                            <th>Company Name</th>
                                            <th>Investor Name</th>
                                            <th>Status</th>
                                            <th>Signed Status</th>
                                            <th>Contract Type</th>
                                            <th>Version</th>
                                            <th>Document</th>
                                            <th>Additional Document </th>
                                            <th>Generated Date</th>
                                            {{-- <th>Version</th> --}}

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







        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
@endsection
@section('custom_js')
    <script src="{{ asset('assets/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('assets/moment/moment.min.js') }}"></script>
    <script src="{{ asset('assets/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>
    <script src="{{ asset('assets/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/daterangepicker/daterangepicker.js') }}"></script>
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
    <script>
        let table = '';
        $(function() {
            table = $('#investmentContractsTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                // pageLength: 5,

                ajax: {
                    url: "{{ route('investmentContracts.list') }}",
                    data: function(d) {
                        // d.investment_id = "{{ $investment->id ?? '' }}";
                        // d.company_id = $('#companyFilter').val(); // 👈 filter param
                        d.status = $('input[name="agreementFilter"]:checked').val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'id'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'company_name',
                        name: 'company.company_name'
                    },


                    {
                        data: 'investor_name',
                        name: 'investor.investor_name'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'signed_status',
                        name: 'signed_status'
                    },

                    {
                        data: 'investor_agreement_type',
                        name: 'investor_agreement_type'
                    },
                    {
                        data: 'investor_agreement_template',
                        name: 'investor_agreement_template'
                    },
                    //  Main Document
                    {
                        data: 'main_doc_view',
                        name: 'main_doc_view',
                        orderable: false,
                        searchable: false
                    },

                    //  Additional Document
                    {
                        data: 'additional_doc_view',
                        name: 'additional_doc_view',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'generated_date',
                        name: 'generated_date',
                        orderable: false,
                        searchable: false
                    },


                ],

                order: [
                    [0, 'desc']
                ],
                dom: 'Bfrtip',
                buttons: [{
                    extend: 'excelHtml5',
                    text: 'Export Excel',
                    title: 'Investments Data',
                    action: function(e, dt, node, config) {
                        let searchValue = dt.search();
                        let url = "{{ route('investment_contracts.export') }}" + "?search=" +
                            encodeURIComponent(searchValue);
                        window.location.href = url;
                    }
                }]
            });

        });
        $('input[name="agreementFilter"]').on('change', function() {
            table.ajax.reload();
            setTimeout(function() {
                table.columns.adjust().responsive.recalc();
            }, 200);
        });
        $('#receiveddate').datetimepicker({
            format: 'DD-MM-YYYY'
        });

        $('#requesteddate').datetimepicker({
            format: 'DD-MM-YYYY'
        });
        $('#terminationdate').datetimepicker({
            format: 'DD-MM-YYYY'
        });
        $('#applyFilter').click(function() {
            table.ajax.reload();
        });
        $('#resetFilter').click(function() {
            $('#companyFilter').val('').trigger('change');
            table.ajax.reload();
        });



        $(document).on('click', '.send-signed-pdf-btn', function(e) {
            e.preventDefault();

            const btn = $(this);
            const url = btn.data('url');
            const originalHtml = btn.html();

            // prevent double-clicks while sending
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

            $.ajax({
                url: url,
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    btn.prop('disabled', false).html(originalHtml);
                    toastr.success(response.message || 'Email dispatch initiated.');
                    // or: alert(response.message);
                },
                error: function(xhr) {
                    btn.prop('disabled', false).html(originalHtml);
                    const msg = xhr.responseJSON?.message || 'Something went wrong.';
                    toastr.error(msg);
                    // or: alert(msg);
                }
            });
        });
    </script>
@endsection
