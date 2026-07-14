@extends('admin.layout.admin_master')

@section('custom_css')
    <!-- daterange picker -->
    <link rel="stylesheet" href="{{ asset('assets/daterangepicker/daterangepicker.css') }}">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet" href="{{ asset('assets/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('assets/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('assets/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/datatables-buttons/css/buttons.bootstrap4.min.css') }}">

    <style>
        .balance-positive {
            color: #28a745;
            font-weight: 600;
        }

        .balance-negative {
            color: #dc3545;
            font-weight: 600;
        }

        .investment-row.disabled-row {
            opacity: 0.5;
        }

        .withdrawal-amount-input:disabled {
            background-color: #e9ecef;
        }

        .amount-invalid {
            border-color: #dc3545 !important;
        }

        .summary-box {
            background: #f8f9fa;
            border-left: 4px solid #17a2b8;
            padding: 12px 15px;
            border-radius: 4px;
        }

        .nav-tabs .nav-link.active {
            font-weight: 600;
        }

        .ledger-table tbody tr {
            background-color: #f6ffff;
        }

        .ledger-table thead tr {
            background-color: #D6EEEE;
        }

        .bootstrap-datetimepicker-widget {
            z-index: 9999 !important;
        }
    </style>
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
                            <li class="breadcrumb-item"><a href="#">Investors</a></li>
                            <li class="breadcrumb-item active">{{ $title }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">

                {{-- ============ INVESTOR SUMMARY ============ --}}
                {{-- <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-user-tie mr-1"></i> {{ $investor->name }}
                            <small class="text-muted">({{ $investor->investor_code ?? 'INV-' . $investor->id }})</small>
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-3 col-6">
                                <div class="description-block border-right">
                                    <h5 class="description-header">{{ number_format($investor->total_invested ?? 0, 2) }}
                                    </h5>
                                    <span class="description-text text-muted">TOTAL INVESTED</span>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="description-block border-right">
                                    <h5 class="description-header text-danger">
                                        {{ number_format($investor->total_withdrawn ?? 0, 2) }}</h5>
                                    <span class="description-text text-muted">TOTAL WITHDRAWN</span>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="description-block border-right">
                                    <h5 class="description-header text-success">
                                        {{ number_format($investor->total_returns ?? 0, 2) }}</h5>
                                    <span class="description-text text-muted">TOTAL RETURNS</span>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="description-block">
                                    <h5 class="description-header">{{ number_format($investor->net_balance ?? 0, 2) }}</h5>
                                    <span class="description-text text-muted">NET BALANCE</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> --}}

                <div class="row">
                    {{-- ============ COMPANY-WISE LEDGER ============ --}}
                    <div class="col-md-12">
                        <div class="card card-outline card-info">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-book mr-1"></i> Company-wise Ledger</h3>
                            </div>
                            <div class="card-body">
                                <ul class="nav nav-tabs" id="companyLedgerTabs" role="tablist">
                                    @foreach ($companies as $index => $company)
                                        <li class="nav-item">
                                            <a class="nav-link {{ $index === 0 ? 'active' : '' }}"
                                                id="company-tab-{{ $company->id }}" data-toggle="tab"
                                                href="#company-pane-{{ $company->id }}" role="tab">
                                                {{ $company->company_name }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>

                                <div class="tab-content pt-3" id="companyLedgerTabContent">
                                    @foreach ($companies as $index => $company)
                                        <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}"
                                            id="company-pane-{{ $company->id }}" role="tabpanel">

                                            <div class="row mb-2">
                                                <div class="col-4"><small class="text-muted">Invested</small>
                                                    <div class="font-weight-bold">
                                                        {{ number_format($company->total_invested, 2) }}</div>
                                                </div>
                                                <div class="col-4"><small class="text-muted">Withdrawn</small>
                                                    <div class="font-weight-bold text-danger">
                                                        {{ number_format($company->total_withdrawn, 2) }}</div>
                                                </div>
                                                <div class="col-4"><small class="text-muted">Balance</small>
                                                    <div class="font-weight-bold text-success">
                                                        {{ number_format($company->balance, 2) }}</div>
                                                </div>
                                            </div>

                                            <table class="table table-sm  table-bordered ledger-table"
                                                data-company="{{ $company->id }}" style="width:100%">
                                                <thead>
                                                    <tr>
                                                        <th>Date</th>
                                                        <th>Type</th>
                                                        {{-- <th>Description</th> --}}
                                                        <th class="text-right">Debit</th>
                                                        <th class="text-right">Credit</th>
                                                        <th class="text-right">Balance</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse ($company->ledger as $entry)
                                                        <tr>
                                                            <td>{{ \Carbon\Carbon::parse($entry['date'])->format('d-m-Y') }}
                                                            </td>
                                                            <td>
                                                                @php
                                                                    $badgeClass = match (true) {
                                                                        str_contains(
                                                                            strtolower($entry['type']),
                                                                            'investment',
                                                                        )
                                                                            => 'badge-warning',
                                                                        str_contains(
                                                                            strtolower($entry['type']),
                                                                            'addendum',
                                                                        )
                                                                            => 'badge-success',
                                                                        str_contains(
                                                                            strtolower($entry['type']),
                                                                            'withdrawal',
                                                                        )
                                                                            => 'badge-danger',
                                                                        str_contains(
                                                                            strtolower($entry['type']),
                                                                            'return',
                                                                        )
                                                                            => 'badge-info',
                                                                        default => 'badge-secondary',
                                                                    };
                                                                @endphp
                                                                <span
                                                                    class="badge {{ $badgeClass }}">{{ $entry['type'] }}</span>
                                                            </td>
                                                            {{-- <td>{{ $entry['description'] }}</td> --}}
                                                            <td class="text-right">
                                                                {{ $entry['debit'] > 0 ? number_format($entry['debit'], 2) : '-' }}
                                                            </td>
                                                            <td class="text-right">
                                                                {{ $entry['credit'] > 0 ? number_format($entry['credit'], 2) : '-' }}
                                                            </td>
                                                            <td class="text-right font-weight-bold">
                                                                {{ number_format($entry['balance'], 2) }}</td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="6" class="text-center text-muted">No
                                                                transactions found.</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="tab-content pt-3" id="companyLedgerTabContent">

                                    {{-- ============ COMPANY 1: Faateh Properties LLC ============ --}}
                                    {{-- <div class="tab-pane fade show active" id="company-pane-1" role="tabpanel">

                                        <div class="row mb-2">
                                            <div class="col-4"><small class="text-muted">Invested</small>
                                                <div class="font-weight-bold">500,000.00</div>
                                            </div>
                                            <div class="col-4"><small class="text-muted">Withdrawn</small>
                                                <div class="font-weight-bold text-danger">75,000.00</div>
                                            </div>
                                            <div class="col-4"><small class="text-muted">Balance</small>
                                                <div class="font-weight-bold text-success">425,000.00</div>
                                            </div>
                                        </div>

                                        <table class="table table-sm table-bordered ledger-table" data-company="1"
                                            style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Type</th>
                                                    <th>Description</th>
                                                    <th class="text-right">Debit</th>
                                                    <th class="text-right">Credit</th>
                                                    <th class="text-right">Balance</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>2026-01-10</td>
                                                    <td><span class="badge badge-success">Investment</span></td>
                                                    <td>Initial investment - INV-0001</td>
                                                    <td class="text-right">-</td>
                                                    <td class="text-right">300,000.00</td>
                                                    <td class="text-right font-weight-bold">300,000.00</td>
                                                </tr>
                                                <tr>
                                                    <td>2026-02-15</td>
                                                    <td><span class="badge badge-success">Investment</span></td>
                                                    <td>Additional investment - INV-0002</td>
                                                    <td class="text-right">-</td>
                                                    <td class="text-right">200,000.00</td>
                                                    <td class="text-right font-weight-bold">500,000.00</td>
                                                </tr>
                                                <tr>
                                                    <td>2026-04-01</td>
                                                    <td><span class="badge badge-info">Return</span></td>
                                                    <td>Quarterly profit distribution</td>
                                                    <td class="text-right">-</td>
                                                    <td class="text-right">12,500.00</td>
                                                    <td class="text-right font-weight-bold">512,500.00</td>
                                                </tr>
                                                <tr>
                                                    <td>2026-05-20</td>
                                                    <td><span class="badge badge-warning">Withdrawal</span></td>
                                                    <td>Partial withdrawal from INV-0001</td>
                                                    <td class="text-right">75,000.00</td>
                                                    <td class="text-right">-</td>
                                                    <td class="text-right font-weight-bold">437,500.00</td>
                                                </tr>
                                                <tr>
                                                    <td>2026-06-30</td>
                                                    <td><span class="badge badge-info">Return</span></td>
                                                    <td>Quarterly profit distribution</td>
                                                    <td class="text-right">-</td>
                                                    <td class="text-right">10,500.00</td>
                                                    <td class="text-right font-weight-bold">448,000.00</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div> --}}

                                    {{-- ============ COMPANY 2: Faateh Al Barsha Development ============ --}}
                                    {{-- <div class="tab-pane fade" id="company-pane-2" role="tabpanel">

                                        <div class="row mb-2">
                                            <div class="col-4"><small class="text-muted">Invested</small>
                                                <div class="font-weight-bold">250,000.00</div>
                                            </div>
                                            <div class="col-4"><small class="text-muted">Withdrawn</small>
                                                <div class="font-weight-bold text-danger">0.00</div>
                                            </div>
                                            <div class="col-4"><small class="text-muted">Balance</small>
                                                <div class="font-weight-bold text-success">250,000.00</div>
                                            </div>
                                        </div>

                                        <table class="table table-sm table-bordered ledger-table" data-company="2"
                                            style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Type</th>
                                                    <th>Description</th>
                                                    <th class="text-right">Debit</th>
                                                    <th class="text-right">Credit</th>
                                                    <th class="text-right">Balance</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>2026-03-05</td>
                                                    <td><span class="badge badge-success">Investment</span></td>
                                                    <td>Initial investment - INV-0003</td>
                                                    <td class="text-right">-</td>
                                                    <td class="text-right">250,000.00</td>
                                                    <td class="text-right font-weight-bold">250,000.00</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div> --}}

                                    {{-- ============ COMPANY 3: Faateh Marina Towers ============ --}}
                                    {{-- <div class="tab-pane fade" id="company-pane-3" role="tabpanel">

                                        <div class="row mb-2">
                                            <div class="col-4"><small class="text-muted">Invested</small>
                                                <div class="font-weight-bold">150,000.00</div>
                                            </div>
                                            <div class="col-4"><small class="text-muted">Withdrawn</small>
                                                <div class="font-weight-bold text-danger">50,000.00</div>
                                            </div>
                                            <div class="col-4"><small class="text-muted">Balance</small>
                                                <div class="font-weight-bold text-success">100,000.00</div>
                                            </div>
                                        </div>

                                        <table class="table table-sm table-bordered ledger-table" data-company="3"
                                            style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Type</th>
                                                    <th>Description</th>
                                                    <th class="text-right">Debit</th>
                                                    <th class="text-right">Credit</th>
                                                    <th class="text-right">Balance</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>2025-11-12</td>
                                                    <td><span class="badge badge-success">Investment</span></td>
                                                    <td>Initial investment - INV-0004</td>
                                                    <td class="text-right">-</td>
                                                    <td class="text-right">150,000.00</td>
                                                    <td class="text-right font-weight-bold">150,000.00</td>
                                                </tr>
                                                <tr>
                                                    <td>2026-01-25</td>
                                                    <td><span class="badge badge-warning">Withdrawal</span></td>
                                                    <td>Partial withdrawal from INV-0004</td>
                                                    <td class="text-right">50,000.00</td>
                                                    <td class="text-right">-</td>
                                                    <td class="text-right font-weight-bold">100,000.00</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div> --}}

                                </div>
                            </div>
                        </div>
                    </div>


                </div>
                <div class="row">
                    {{-- ============ PARTIAL WITHDRAWAL FORM ============ --}}
                    <div class="col-md-12">
                        <div class="card card-outline card-warning">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-hand-holding-usd mr-1"></i> Partial Withdrawal
                                </h3>
                            </div>
                            <form id="partialWithdrawalForm" autocomplete="off">
                                @csrf
                                <input type="hidden" name="investor_id" value="{{ $investor->id }}">

                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="company_id" class="asterisk">Select Company </label>
                                                <select class="form-control select2" id="company_id" name="company_id"
                                                    style="width:100%" required>
                                                    <option value="">-- Select Company --</option>
                                                    @foreach ($companies as $company)
                                                        <option value="{{ $company->id }}">{{ $company->company_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="withdrawal_amount" class="asterisk">Withdrawal Amount </label>
                                                <input type="number" step="0.01" min="0" class="form-control"
                                                    id="withdrawal_amount" name="withdrawal_amount" placeholder="0.00"
                                                    required>
                                                <small class="form-text text-muted" id="withdrawalAmountHint">Split this
                                                    amount across the
                                                    investments below.</small>
                                            </div>
                                        </div>
                                    </div>



                                    <div id="investmentsWrapper" style="display:none;">
                                        <label class="d-block">
                                            Select Investment(s) to Withdraw From <span class="text-danger">*</span>
                                        </label>

                                        {{-- <div class="d-flex justify-content-end mb-2">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input"
                                                    id="selectAllInvestments">
                                                <label class="custom-control-label" for="selectAllInvestments">Select
                                                    All</label>
                                            </div>
                                        </div> --}}

                                        <div id="investmentsTableBody">
                                        </div>

                                        <div class="summary-box mb-3">
                                            <div class="d-flex justify-content-between">
                                                <span>Investments Selected:</span>
                                                <strong id="selectedCount">0</strong>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span>Total Withdrawal Amount:</span>
                                                <strong id="totalWithdrawalAmount">0.00</strong>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group ">
                                                    <label for="requested_date" class="asterisk">Requested Date</label>
                                                    <div class="input-group date" id="requestedDatePicker"
                                                        data-target-input="nearest">
                                                        <input type="text" class="form-control datetimepicker-input"
                                                            name="requested_date" id="requested_date"
                                                            data-target="#requestedDatePicker" placeholder="DD-MM-YYYY"
                                                            required>
                                                        <div class="input-group-append" data-target="#requestedDatePicker"
                                                            data-toggle="datetimepicker">
                                                            <div class="input-group-text">
                                                                <i class="fa fa-calendar"></i>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="durationDays_${inv.id}" class="asterisk">Days</label>
                                                    <input type="number" min="0" step="1"
                                                        class="form-control form-control duration-days-input"
                                                        name="duration_days" id="durationDays" required>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group ">
                                                    <label for="terminationDate_${inv.id}" class="asterisk">Withdrawal
                                                        Date</label>
                                                    <div class="input-group date termination-date-picker"
                                                        id="withdrawalDatePicker" data-target-input="nearest">
                                                        <input type="text"
                                                            class="form-control form-control datetimepicker-input termination-date-input"
                                                            name="withdrawal_date" id="withdrawal_date"
                                                            data-target="#withdrawalDatePicker" placeholder="DD-MM-YYYY"
                                                            required>
                                                        <div class="input-group-append"
                                                            data-target="#withdrawalDatePicker"
                                                            data-toggle="datetimepicker">
                                                            <div class="input-group-text"><i class="fa fa-calendar"></i>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    <div id="noInvestmentsMsg" class="alert alert-warning" style="display:none;">
                                        This investor has no investments in the selected company.
                                    </div>
                                </div>

                                <div class="card-footer">
                                    <button type="submit" class="btn btn-warning" id="submitWithdrawalBtn" disabled>
                                        <i class="fas fa-money-bill-wave mr-1"></i> Process Withdrawal
                                    </button>
                                </div>
                            </form>
                        </div>
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
    <script src="{{ asset('assets/moment/moment.min.js') }}"></script>
    <script src="{{ asset('assets/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>

    <script>
        $(function() {

            /* ---------- Select2 init ---------- */
            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%'
            });

            /* ---------- Date picker ---------- */
            // $('#requested_date').datetimepicker({
            //     format: 'DD-MM-YYYY',
            //     maxDate: moment()
            // });

            /* ---------- Date pickers (init ONCE, on the container divs) ---------- */
            $('#requestedDatePicker').datetimepicker({
                format: 'DD-MM-YYYY',
                maxDate: moment()
            });

            $('#withdrawalDatePicker').datetimepicker({
                format: 'DD-MM-YYYY'
            });

            /* ---------- Ledger DataTables, one per company tab ---------- */
            // NOTE: adjust the route name/params to match your controller.
            // Expected JSON response shape per row:
            // { date, type, description, debit, credit, balance }
            // $('.ledger-table').each(function() {
            //     const companyId = $(this).data('company');
            //     $(this).DataTable({
            //         processing: true,
            //         serverSide: true,
            //         ajax: {
            //             data: function(d) {
            //                 d.company_id = companyId;
            //             }
            //         },
            //         order: [
            //             [0, 'desc']
            //         ],
            //         columns: [{
            //                 data: 'date',
            //                 name: 'date'
            //             },
            //             {
            //                 data: 'type',
            //                 name: 'type'
            //             },
            //             // {
            //             //     data: 'description',
            //             //     name: 'description'
            //             // },
            //             {
            //                 data: 'debit',
            //                 name: 'debit',
            //                 className: 'text-right',
            //                 render: (d) => d > 0 ? parseFloat(d).toFixed(2) : '-'
            //             },
            //             {
            //                 data: 'credit',
            //                 name: 'credit',
            //                 className: 'text-right',
            //                 render: (d) => d > 0 ? parseFloat(d).toFixed(2) : '-'
            //             },
            //             {
            //                 data: 'balance',
            //                 name: 'balance',
            //                 className: 'text-right font-weight-bold',
            //                 render: (d) => parseFloat(d).toFixed(2)
            //             }
            //         ]
            //     });
            // });

            // Redraw DataTable when its tab becomes visible (fixes column width glitch)
            $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
                const target = $(e.target).attr('href');
                $(target).find('table.ledger-table').DataTable().columns.adjust();
            });

            let totalAvailableBalance = 0;
            let exceedAlertShown = false;

            /* ---------- Load investments when company changes ---------- */
            $('#company_id').on('change', function() {
                const companyId = $(this).val();
                const investorId = {{ $investor->id }};

                $('#investmentsTableBody').empty();
                $('#investmentsWrapper').hide();
                $('#noInvestmentsMsg').hide();
                $('#submitWithdrawalBtn').prop('disabled', true);

                updateSummary();

                if (!companyId) return;

                $.ajax({
                    url: "{{ url('investor') }}/" + investorId + "/company/" + companyId +
                        "/investments",
                    method: 'GET',
                    dataType: 'json'
                }).done(function(res) {
                    const investments = res.data ||
                        res; // support either {data: [...]} or [...]
                    console.log('Loaded investments:', investments);

                    if (!investments || investments.length === 0) {
                        $('#noInvestmentsMsg').show();
                        return;
                    }

                    //  sum up available balances across all investments in this company
                    totalAvailableBalance = investments.reduce(function(sum, inv) {
                        return sum + parseFloat(inv.available_balance || 0);
                    }, 0);

                    $('#withdrawal_amount')
                        .attr('max', totalAvailableBalance.toFixed(2))
                        .attr('title', 'Max available: ' + totalAvailableBalance.toFixed(2));

                    investments.forEach(function(inv) {
                        const row = `
                            <div class="card investment-row mb-2" data-investment-id="${inv.id}" data-available="${inv.available_balance}">
                                <div class="card-body py-2">
                                    <div class="row align-items-center">
                                        <div class="col-md-1 col-2">
                                            <input type="checkbox" class="investment-checkbox"
                                                name="investments[${inv.id}][selected]" value="1">
                                        </div>
                                        <div class="col-md-2 col-10">
                                            <strong>${inv.reference ?? ('INV-' + inv.id)}</strong>
                                            <br><small class="text-muted">${inv.invested_date ?? ''}</small>
                                        </div>
                                        <div class="col-md-2 col-6 text-md-right mt-2 mt-md-0">
                                            <small class="text-muted d-md-none">Available</small><br>
                                              <input type="number" step="0.01" min="0"
                                                class="form-control form-control-sm "
                                                name="investments[${inv.id}][available_amount]"
                                                value="${parseFloat(inv.available_balance).toFixed(2)}" readonly
                                                >
                                        </div>
                                        <div class="col-md-2 col-6 mt-2 mt-md-0">
                                            <small class="text-muted d-block">Withdraw Amount</small>
                                            <input type="number" step="0.01" min="0"
                                                class="form-control form-control-sm withdrawal-amount-input"
                                                name="investments[${inv.id}][amount]"
                                                max="${inv.available_balance}"
                                                disabled>
                                        </div>

                                    </div>
                                </div>
                            </div>`;

                        $('#investmentsTableBody').append(row);

                        // $(`#terminationRequestedDate_${inv.id}`).datetimepicker({
                        //     format: 'DD-MM-YYYY',
                        //     icons: {
                        //         time: 'fa fa-clock'
                        //     }
                        // });

                        // $(`#terminationDate_${inv.id}`).datetimepicker({
                        //     format: 'DD-MM-YYYY',
                        //     icons: {
                        //         time: 'fa fa-clock'
                        //     }
                        // });

                        // Re-init withdrawal_date AFTER the wrapper is visible
                        // if ($('#requestedDatePicker').data('DateTimePicker')) {
                        //     $('#requestedDatePicker').data('DateTimePicker').destroy();
                        // }
                        // $('#requestedDatePicker').datetimepicker({
                        //     format: 'DD-MM-YYYY',
                        //     // maxDate: moment()
                        // });
                        // if ($('#withdrawalDatePicker').data('DateTimePicker')) {
                        //     $('#withdrawalDatePicker').data('DateTimePicker').destroy();
                        // }
                        // $('#withdrawalDatePicker').datetimepicker({
                        //     format: 'DD-MM-YYYY',
                        //     // maxDate: moment()
                        // });
                    });


                    $('#investmentsWrapper').show();
                }).fail(function() {
                    toastr.error('Could not load investments for this company.');
                });
            });

            $('#withdrawal_amount').on('input', function() {
                const targetAmount = parseFloat($(this).val());

                if (!isNaN(targetAmount) && targetAmount > totalAvailableBalance && totalAvailableBalance >
                    0) {
                    if (!exceedAlertShown) {
                        toastr.error('Withdrawal amount cannot exceed available balance of ' +
                            totalAvailableBalance.toFixed(2));
                        exceedAlertShown = true;
                    }
                } else {
                    exceedAlertShown = false;
                }

                updateSummary();
            });

            /* ---------- Select all ---------- */
            $(document).on('change', '#selectAllInvestments', function() {
                const checked = $(this).is(':checked');
                $('.investment-checkbox').prop('checked', checked).trigger('change');
            });

            /* ---------- Enable/disable amount input per row ---------- */
            $(document).on('change', '.investment-checkbox', function() {
                const $row = $(this).closest('.investment-row');
                const $amountInput = $row.find('.withdrawal-amount-input');
                // const $reqDateInput = $row.find('.termination-requested-date-input');
                // const $durationInput = $row.find('.duration-days-input');
                // const $termDateInput = $row.find('.termination-date-input');

                if ($(this).is(':checked')) {
                    $amountInput.prop('disabled', false).focus();
                    // $reqDateInput.prop('disabled', false);
                    // $durationInput.prop('disabled', false);
                    // $termDateInput.prop('disabled', false);
                    $row.removeClass('disabled-row');
                } else {
                    $amountInput.prop('disabled', true).val('').removeClass('amount-invalid');
                    // $reqDateInput.prop('disabled', true).val('');
                    // $durationInput.prop('disabled', true).val('');
                    // $termDateInput.prop('disabled', true).val('');
                    $row.addClass('disabled-row');
                }
                updateSummary();
            });
            /* ---------- Validate amount against available balance ---------- */
            $(document).on('input', '.withdrawal-amount-input', function() {
                const $row = $(this).closest('.investment-row');
                const available = parseFloat($row.data('available'));
                const entered = parseFloat($(this).val());
                // const $reqDateInput = $row.find('.termination-requested-date-input');
                // const $durationInput = $row.find('.duration-days-input');
                // const $termDateInput = $row.find('.termination-date-input');

                if (isNaN(entered) || entered <= 0 || entered > available) {
                    // $reqDateInput.prop('required', false).prop('disabled', true);
                    // $durationInput.prop('required', false).prop('disabled', true);
                    // $termDateInput.prop('required', false).prop('disabled', true);
                    if (entered > available) {
                        toastr.error('Amount cannot exceed available balance of ' +
                            available.toFixed(2));
                    }

                    $(this).addClass('amount-invalid');
                } else if (entered === available) {
                    // $reqDateInput.prop('required', true).prop('disabled', false);
                    // $durationInput.prop('required', false).prop('disabled', false);
                    // $termDateInput.prop('required', false).prop('disabled', false);
                    $(this).removeClass('amount-invalid');
                } else {
                    //  $reqDateInput.prop('required', false).prop('disabled', true);
                    // $durationInput.prop('required', false).prop('disabled', true);
                    // $termDateInput.prop('required', false).prop('disabled', true);
                    $(this).removeClass('amount-invalid');
                }
                updateSummary();
            });
            /* ---------- Summary + submit button state ---------- */
            function updateSummary() {
                let total = 0;
                let count = 0;
                let hasInvalid = false;

                $('.investment-checkbox:checked').each(function() {
                    const $row = $(this).closest('.investment-row');
                    const $amountInput = $row.find('.withdrawal-amount-input');
                    const val = parseFloat($amountInput.val());

                    count++;
                    if (!isNaN(val) && val > 0) {
                        total += val;
                    } else {
                        hasInvalid = true;
                    }
                    if ($amountInput.hasClass('amount-invalid')) {
                        hasInvalid = true;
                    }
                });

                $('#selectedCount').text(count);
                $('#totalWithdrawalAmount').text(total.toFixed(2));

                const targetAmount = parseFloat($('#withdrawal_amount').val());
                const targetIsValid = !isNaN(targetAmount) && targetAmount > 0;
                const totalsMatch = targetIsValid && Math.abs(total - targetAmount) < 0.01;

                $('#withdrawal_amount').toggleClass('amount-invalid', targetIsValid && !totalsMatch);

                const diff = targetIsValid ? (targetAmount - total) : 0;
                if (targetIsValid && !totalsMatch) {
                    // toastr.warning('Total allocated (' + total.toFixed(2) + ') does not match withdrawal amount (' +
                    //     targetAmount.toFixed(2) + ')');
                    $('#withdrawalAmountHint').text(
                        diff > 0 ?
                        `Remaining to allocate: ${diff.toFixed(2)}` :
                        `You've allocated ${Math.abs(diff).toFixed(2)} more than the withdrawal amount`
                    ).addClass('text-danger').removeClass('text-muted');
                } else {
                    $('#withdrawalAmountHint').text('Split this amount across the investments below.')
                        .addClass('text-muted').removeClass('text-danger');
                }

                $('#submitWithdrawalBtn').prop('disabled', !(count > 0 && !hasInvalid && totalsMatch));
            }
            $('#withdrawal_amount').on('input', updateSummary);

            $('#requestedDatePicker').on('change.datetimepicker', function(e) {
                calculateTerminationDate();
            });
            $('#durationDays').on('change keyup', function() {
                calculateTerminationDate();
            });

            function calculateTerminationDate() {
                let requestedDate = $('#requested_date').val();
                let duration = parseInt($('#durationDays').val(), 10);

                if (!requestedDate || isNaN(duration) || duration <= 0) {
                    $('#withdrawal_date').val('');
                    return;
                }

                // Convert DD-MM-YYYY to YYYY-MM-DD
                let parts = requestedDate.split('-');
                if (parts.length !== 3) return;

                let date = new Date(parts[2], parts[1] - 1, parts[0]); // year, monthIndex, day
                if (isNaN(date.getTime())) return;

                date.setDate(date.getDate() + duration);

                let day = String(date.getDate()).padStart(2, '0');
                let month = String(date.getMonth() + 1).padStart(2, '0');
                let year = date.getFullYear();

                $('#withdrawal_date').val(`${day}-${month}-${year}`);
            }

            /* ---------- Submit ---------- */
            $('#partialWithdrawalForm').on('submit', function(e) {
                e.preventDefault();

                if (!$('#requested_date').val()) {
                    toastr.warning('Please select a requested date.');
                    return;
                }

                const formData = $(this).serialize();
                let url = "{{ route('investor.partial-withdrawal.store', ['id' => $investor->id]) }}";

                $.ajax({
                    // NOTE: adjust to your actual store route
                    url: url,
                    method: 'POST',
                    data: formData,
                    dataType: 'json'
                }).done(function(res) {
                    toastr.success(res.message ?? 'Withdrawal processed successfully.');
                    setTimeout(() => location.reload(), 1200);
                }).fail(function(xhr) {
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        let msg = '';
                        $.each(errors, function(key, val) {
                            msg += val[0] + '<br>';
                        });
                        toastr.error(msg);
                    } else {
                        toastr.error(
                            'Something went wrong while processing the withdrawal.');
                    }
                });
            });

        });
    </script>
@endsection
