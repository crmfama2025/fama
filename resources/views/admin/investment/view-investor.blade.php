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
                        <h1>{{ $title }}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
                            <li class="breadcrumb-item active">{{ $title }}</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-3 col-sm-6 col-12">
                        <div class="info-box">
                            <span class="info-box-icon bg-info"><i class="fas fa-hand-holding-usd"></i></span>

                            <div class="info-box-content">
                                <span class="info-box-text">Investments</span>
                                <span class="info-box-number">{{ $investor->total_no_of_investments }}</span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                        <!-- /.info-box -->
                    </div>

                    <div class="col-md-3 col-sm-6 col-12">
                        <div class="info-box">
                            <span class="info-box-icon bg-gradient-maroon"><i class="fas fa-piggy-bank"></i></span>

                            <div class="info-box-content">
                                <span class="info-box-text">Tot. Investment</span>
                                <span class="info-box-number">{{ $investor->total_invested_amount }}</span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                        <!-- /.info-box -->
                    </div>

                    <div class="col-md-3 col-sm-6 col-12">
                        <div class="info-box">
                            <span class="info-box-icon bg-gradient-olive"><i class="fas fa-dollar-sign"></i></span>

                            <div class="info-box-content">
                                <span class="info-box-text">Profit Released</span>
                                <span class="info-box-number">{{ $investor->total_profit_received }}</span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                        <!-- /.info-box -->
                    </div>

                    <div class="col-md-3 col-sm-6 col-12">
                        <div class="info-box">
                            <span class="info-box-icon bg-danger"><i class="fas fa-funnel-dollar"></i></span>

                            <div class="info-box-content">
                                <span class="info-box-text">Tot. Commission Amount</span>
                                <span class="info-box-number">{{ $investor->total_referal_commission }}</span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                        <!-- /.info-box -->
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->

                <div class="card">
                    <div class="card-header p-2">
                        <ul class="nav nav-pills">
                            <li class="nav-item"><a class="nav-link active" href="#investor" data-toggle="tab">Investor
                                    Details</a>
                            </li>
                            <li class="nav-item"><a class="nav-link" href="#investment" data-toggle="tab">Investments</a>
                            </li>
                        </ul>
                    </div><!-- /.card-header -->
                    <div class="card-body">
                        <div class="tab-content mt-4">
                            <div class="active tab-pane" id="investor">
                                <!-- About Me Box -->
                                <div class="card card-outline card-info">
                                    <div class="card-header">
                                        <strong>
                                            <h3 class="card-title font-weight-bold text-lg text-maroon">
                                                {{ $investor->investor_name }} - {{ $investor->investor_name_arabic }}
                                            </h3>
                                        </strong>
                                    </div>
                                    <!-- /.card-header -->
                                    <div class="card-body">
                                        <strong><i class="fas fa-book mr-1"></i> Basic Details</strong>

                                        <p class="text-muted m-0">
                                            <i class="fas fa-phone-alt text-grey mr-1"></i>
                                            {{ $investor->investor_mobile }}

                                        </p>
                                        <p class="text-muted m-0">
                                            <i class="fas fa-envelope text-grey mr-1"></i>
                                            {{ $investor->investor_email }}
                                        </p>
                                        <p class="text-muted m-0">
                                            <i class="fas fa-flag text-grey mr-1"></i>
                                            {{ $investor->nationality->nationality_name }}
                                        </p>

                                        <hr>

                                        <strong><i class="fas fa-map-marker-alt mr-1"></i> Address</strong>

                                        <p class="text-muted m-0">{{ $investor->countryOfResidence->nationality_name }}</p>
                                        <p class="text-muted m-0">{{ $investor->investor_address }}</p>
                                        <p class="text-muted m-0">{{ $investor->adressline2 ?? ' - ' }}</p>
                                        <p class="text-muted m-0">{{ $investor->state }}</p>
                                        <p class="text-muted m-0">{{ $investor->city }}</p>
                                        <p class="text-muted m-0">{{ $investor->postal_code ?? ' - ' }}</p>

                                        <strong>Arabic Address</strong>

                                        <p class="text-muted m-0">{{ $investor->investor_address_arabic }}</p>
                                        <p class="text-muted m-0">{{ $investor->adress_line2_arabic ?? ' - ' }}</p>
                                        <p class="text-muted m-0">{{ $investor->state_arabic }}</p>
                                        <p class="text-muted m-0">{{ $investor->city_arabic }}</p>

                                        <hr>

                                        <strong><i class="fas fa-pencil-alt mr-1"></i> Identity Details</strong>

                                        <p class="text-muted m-0">Emirates ID / Other ID : {{ $investor->id_number }}
                                        </p>
                                        <p class="text-muted m-0">Passport : {{ $investor->passport_number }}</p>

                                        <hr>

                                        <strong><i class="far fa-file-alt mr-1"></i> Documents Received</strong>



                                        @foreach ($investorDocuments as $doc)
                                            @php
                                                $href = $doc->document_type_id
                                                    ? asset('storage/' . $doc->document_path)
                                                    : 'javascript:void(0)';
                                            @endphp

                                            <p class="text-muted m-0">{{ $doc->documentType->label_name }}
                                                :
                                                <a href="{{ $href }}"
                                                    @if ($href !== 'javascript:void(0)') target="_blank" @endif
                                                    class="text-bold">{{ $doc->document_name }}</a>
                                            </p>
                                        @endforeach

                                        <hr>


                                    </div>
                                    <!-- /.card-body -->
                                </div>

                                <div class="card card-outline card-info">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="fas fa-university mr-1"></i> Bank Details
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-striped text-muted">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Bank name</th>
                                                    <th>Bank Name Arabic</th>
                                                    <th>Beneficiary</th>
                                                    <th>Beneficiary Arabic</th>
                                                    <th>IBAN</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($investorBanks as $investorBank)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>{{ $investorBank->investor_bank_name }}</td>
                                                        <td>{{ $investorBank->investor_bank_name_arabic }}</td>
                                                        <td>{{ $investorBank->investor_beneficiary }}</td>
                                                        <td>{{ $investorBank->investor_beneficiary_arabic }}</td>
                                                        <td>{{ $investorBank->investor_iban }}</td>
                                                        <td>
                                                            <button class="btn btn-info btn-sm"
                                                                data-id="{{ $investorBank->id }}"
                                                                data-investor-id="{{ $investorBank->investor_id }}"
                                                                data-target="#modal-add-bank" data-toggle="modal"
                                                                title="Add Bank"><i
                                                                    class="fas fa-pencil-alt"></i></button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            </div>
                            <!-- /.tab-pane -->
                            <div class="tab-pane" id="investment">
                                <div class="card card-outline card-info">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="fas fa-coins mr-1"></i> Investment Details
                                        </h3>
                                    </div>
                                    <div class="card-body table-responsive">
                                        <table id="investmentsTable" class="table table-striped display nowrap"
                                            width="100%">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Action</th>
                                                    <th>Investment</th>
                                                    <th>Investment Amount</th>
                                                    <th>Date</th>
                                                    <th>Profit Interval</th>
                                                    <th>Profit %</th>
                                                    <th>Profit Release Date</th>
                                                    <th>Tot. Profit Released</th>
                                                    <th>Active Month Release</th>
                                                    <th>Outstanding Profit</th>
                                                    <th>Grace Period </th>
                                                    <th>Payout Batch</th>
                                                    <th>Nominee Details</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- /.card-body -->
                                </div>
                            </div>
                            <!-- /.tab-pane -->

                        </div>
                        <!-- /.tab-content -->
                    </div><!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div><!-- /.container-fluid -->
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

    @include('admin.investment.investor-bank-modal')

    <script>
        let table = '';
        $(function() {
            table = $('#investmentsTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                pageLength: 5,
                ajax: {
                    url: "{{ route('investment.list') }}",
                    data: function(d) {
                        console.log({{ $investor->id }});
                        d.investorid = "{{ $investor->id }}";
                        // You can add filters here if needed
                    },
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
                        data: 'investment_code',
                        name: 'investment_code'
                    },
                    {
                        data: 'investment_amount',
                        name: 'investment_amount'
                    },
                    {
                        data: 'investment_date',
                        name: 'investment_date'
                    },
                    {
                        data: 'profit_interval',
                        name: 'profitInterval.profit_interval_name'
                    },
                    {
                        data: 'profit_perc',
                        name: 'profit_perc'
                    },
                    {
                        data: 'maturity_date',
                        name: 'maturity_date'
                    },
                    {
                        data: 'total_profit_released',
                        name: 'total_profit_released'
                    },
                    {
                        data: 'current_month_released',
                        name: 'current_month_released'
                    },
                    {
                        data: 'outstanding_profit',
                        name: 'outstanding_profit'
                    },
                    {
                        data: 'grace_period',
                        name: 'grace_period'
                    },
                    {
                        data: 'batch_name',
                        name: 'payoutBatch.batch_name'
                    },
                    {
                        data: 'nominee_details',
                        name: 'nominee_name'
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
                        let form = $('<form>', {
                            action: "{{ route('tanantReceivables.export') }}",
                            method: 'POST'
                        });
                        form.append($('<input>', {
                            type: 'hidden',
                            name: '_token',
                            value: '{{ csrf_token() }}'
                        }));
                        form.append($('<input>', {
                            type: 'hidden',
                            name: 'search',
                            value: searchValue
                        }));
                        form.appendTo('body').submit();
                    }
                }]
            });

        });
    </script>
@endsection
