@extends('admin.layout.admin_master')

@section('custom_css')
    <link rel="stylesheet" href="{{ asset('assets/daterangepicker/daterangepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
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

        .bootstrap-datetimepicker-widget {
            z-index: 9999 !important;
        }

        .ledger-table tbody tr {
            background-color: #f6ffff;
        }

        .ledger-table thead tr {
            background-color: #D6EEEE;
        }
    </style>
@endsection

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>{{ $title ?? 'Edit Partial Withdrawal' }}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="../dashboard.php">Home</a></li>
                            <li class="breadcrumb-item"><a href="#">Investors</a></li>
                            <li class="breadcrumb-item active">Edit Partial Withdrawal</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">

                {{-- ============ COMPANY-WISE LEDGER ============ --}}
                <div class="row">
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

                                            <table class="table table-sm table-bordered ledger-table"
                                                data-company="{{ $company->id }}" style="width:100%">
                                                <thead>
                                                    <tr>
                                                        <th>Date</th>
                                                        <th>Type</th>
                                                        <th class="text-right">Debit</th>
                                                        <th class="text-right">Credit</th>
                                                        <th class="text-right">Balance</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse ($company->ledger as $entry)
                                                        <tr>
                                                            <td>{{ \Carbon\Carbon::parse($entry['date'])->format('Y-m-d') }}
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
                                                            <td colspan="5" class="text-center text-muted">No
                                                                transactions found.</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="card card-outline card-warning">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-hand-holding-usd mr-1"></i>
                                    Edit Partial Withdrawal
                                    <small class="text-muted">
                                        &mdash;
                                        {{ $data['investor']->investor_name ?? ($data['investor']->name ?? 'Investor #' . $data['investor']->id) }}
                                    </small>
                                </h3>
                            </div>

                            <form id="partialWithdrawalEditForm" autocomplete="off">
                                @csrf
                                <input type="hidden" name="investor_id" value="{{ $data['investor']->id }}">
                                <input type="hidden" name="ledger_id" id="ledger_id" value="{{ $data['ledger']->id }}">

                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="company_id" class="asterisk">Select Company</label>
                                                <select class="form-control select2" id="company_id" name="company_id"
                                                    style="width:100%" required>
                                                    <option value="">-- Select Company --</option>
                                                    @foreach ($companies as $company)
                                                        <option value="{{ $company->id }}"
                                                            {{ (int) $data['ledger']->company_id === (int) $company->id ? 'selected' : '' }}>
                                                            {{ $company->company_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="withdrawal_amount" class="asterisk">Withdrawal Amount</label>
                                                <input type="number" step="0.01" min="0" class="form-control"
                                                    id="withdrawal_amount" name="withdrawal_amount" placeholder="0.00"
                                                    value="{{ $data['ledger']->transaction_amount }}" required>
                                                <small class="form-text text-muted" id="withdrawalAmountHint">Split
                                                    this amount across the investments below.</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="investmentsWrapper" style="display:none;">
                                        <label class="d-block">
                                            Select Investment(s) to Withdraw From <span class="text-danger">*</span>
                                        </label>

                                        <div id="investmentsTableBody"></div>

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
                                        {{-- @dump($data['ledger']->requested_date); --}}

                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="requested_date" class="asterisk">Requested Date</label>
                                                    <div class="input-group date" id="requestedDatePicker"
                                                        data-target-input="nearest">
                                                        <input type="text" class="form-control datetimepicker-input"
                                                            name="requested_date" id="requested_date"
                                                            data-target="#requestedDatePicker" placeholder="DD-MM-YYYY"
                                                            value="{{ isset($data['ledger']->requested_date) ? \Carbon\Carbon::parse($data['ledger']->requested_date)->format('d-m-Y') : '' }}"
                                                            required>
                                                        <div class="input-group-append" data-target="#requestedDatePicker"
                                                            data-toggle="datetimepicker">
                                                            <div class="input-group-text"><i class="fa fa-calendar"></i>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="durationDays" class="asterisk">Days</label>
                                                    <input type="number" min="0" step="1"
                                                        class="form-control duration-days-input" name="duration_days"
                                                        id="durationDays" value="{{ $data['ledger']->duration_days }}"
                                                        required>
                                                </div>
                                            </div>
                                            {{-- @dump($data['ledger']->withdrawal_date); --}}
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="withdrawal_date" class="asterisk">Withdrawal Date</label>
                                                    <div class="input-group date termination-date-picker"
                                                        id="withdrawalDatePicker" data-target-input="nearest">
                                                        <input type="text"
                                                            class="form-control datetimepicker-input termination-date-input"
                                                            name="withdrawal_date" id="withdrawal_date"
                                                            data-target="#withdrawalDatePicker" placeholder="DD-MM-YYYY"
                                                            value="{{ isset($data['ledger']->withdrawal_date) ? \Carbon\Carbon::parse($data['ledger']->withdrawal_date)->format('d-m-Y') : '' }}"
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
                                        <i class="fas fa-save mr-1"></i> Update Withdrawal
                                    </button>
                                    <a href="{{ route('investor.partial-withdrawal-list') }}" class="btn btn-default">
                                        Cancel
                                    </a>
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
    <script src="{{ asset('assets/moment/moment.min.js') }}"></script>
    <script src="{{ asset('assets/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>

    <script>
        // Existing bifurcation rows for this ledger, keyed by investment_id, e.g.:
        // { "12": { withdrawal_amount: 5000, previous_amount: 20000 }, ... }
        const existingBifurcations = {!! $data['bifurcations']->keyBy('investment_id')->map(function ($b) {
                return [
                    'withdrawal_amount' => (float) $b->withdrawal_amount,
                    'previous_amount' => (float) $b->previous_amount,
                    'withdrawal_month_profit' => (float) $b->withdrawal_month_profit,
                ];
            })->toJson() !!};
        console.log(existingBifurcations);

        const editLedgerId = {{ $data['ledger']->id }};
        const investorId = {{ $data['investor']->id }};

        $(function() {

            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%'
            });

            // Fixes column-width glitch on tables inside initially-hidden tabs
            $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
                const target = $(e.target).attr('href');
                const $table = $(target).find('table.ledger-table');
                if ($table.length && $.fn.DataTable.isDataTable($table)) {
                    $table.DataTable().columns.adjust();
                }
            });

            $('#requestedDatePicker').datetimepicker({
                format: 'DD-MM-YYYY',
                // maxDate: moment(),

            });

            let totalAvailableBalance = 0;
            let exceedAlertShown = false;

            // Force the picker to reflect the server-rendered value
            const requestedVal = $('#requested_date').val();
            if (requestedVal) {
                $('#requestedDatePicker').datetimepicker('date', moment(requestedVal, 'DD-MM-YYYY'));
            }
            $('#withdrawalDatePicker').datetimepicker({
                format: 'DD-MM-YYYY'
            });
            const withdrawalVal = $('#withdrawal_date').val();
            if (withdrawalVal) {
                $('#withdrawalDatePicker').datetimepicker('date', moment(withdrawalVal, 'DD-MM-YYYY'));
            }

            /* ---------- Load investments for a company, prefilling from existingBifurcations ---------- */
            function loadInvestments(companyId) {
                $('#investmentsTableBody').empty();
                $('#investmentsWrapper').hide();
                $('#noInvestmentsMsg').hide();
                $('#submitWithdrawalBtn').prop('disabled', true);

                if (!companyId) return;

                $.ajax({
                    url: "{{ url('investor') }}/" + investorId + "/company/" + companyId + "/investments",
                    method: 'GET',
                    data: {
                        edit_ledger_id: editLedgerId
                    },
                    dataType: 'json'
                }).done(function(res) {
                    const investments = res.data || res;

                    if (!investments || investments.length === 0) {
                        $('#noInvestmentsMsg').show();
                        return;
                    }

                    totalAvailableBalance = investments.reduce(function(sum, inv) {
                        const existing = existingBifurcations[inv.id];
                        const availableForDisplay = existing ? existing.previous_amount : inv
                            .available_balance;
                        return sum + parseFloat(availableForDisplay || 0);
                    }, 0);

                    $('#withdrawal_amount')
                        .attr('max', totalAvailableBalance.toFixed(2))
                        .attr('title', 'Max available: ' + totalAvailableBalance.toFixed(2));

                    investments.forEach(function(inv) {
                        const existing = existingBifurcations[inv.id];
                        // console.log('Investment ID:', inv.id, 'Existing bifurcation:', existing);

                        // If this investment was part of the withdrawal being edited,
                        // show the balance as it was BEFORE this withdrawal, not the
                        // already-reduced current balance.
                        const availableForDisplay = existing ? existing.previous_amount : inv
                            .available_balance;
                        const prefillAmount = existing ? existing.withdrawal_amount : '';
                        const isChecked = !!existing;
                        const profitPrefill = existing ? existing.withdrawal_month_profit : '';
                        console.log(existing, prefillAmount, profitPrefill, isChecked);

                        const row = `
                            <div class="card investment-row mb-2 ${isChecked ? '' : 'disabled-row'}"
                                data-investment-id="${inv.id}" data-available="${availableForDisplay}">
                                <div class="card-body py-2">
                                    <div class="row align-items-center">
                                        <div class="col-md-1 col-2">
                                            <input type="checkbox" class="investment-checkbox"
                                                name="investments[${inv.id}][selected]" value="1"
                                                ${isChecked ? 'checked' : ''}>
                                        </div>
                                        <div class="col-md-2 col-10">
                                            <strong>${inv.reference ?? ('INV-' + inv.id)}</strong>
                                            <br><small class="text-muted">${inv.invested_date ?? ''}</small>
                                        </div>
                                        <div class="col-md-2 col-6 text-md-right mt-2 mt-md-0">
                                            <small class="text-muted d-md-none">Available</small><br>
                                            <input type="number" step="0.01" min="0"
                                                class="form-control form-control-sm"
                                                name="investments[${inv.id}][available_amount]"
                                                value="${parseFloat(availableForDisplay).toFixed(2)}" readonly>
                                        </div>
                                        <div class="col-md-2 col-6 mt-2 mt-md-0">
                                            <small class="text-muted d-block">Withdraw Amount</small>
                                            <input type="number" step="0.01" min="0"
                                                class="form-control form-control-sm withdrawal-amount-input"
                                                name="investments[${inv.id}][amount]"
                                                max="${availableForDisplay}"
                                                value="${prefillAmount}"
                                                ${isChecked ? '' : 'disabled'}>

                                        </div>
                                         <div class="col-md-2 col-6 mt-2 mt-md-0">
                                            <small class="text-muted d-block">Withdrawal Month Profit</small>
                                            <input type="number" step="0.01" min="0"
                                                class="form-control form-control-sm profit-input"
                                                name="investments[${inv.id}][profit]"

                                                value="${profitPrefill}"
                                                 ${isChecked ? '' : 'disabled'}>
                                        </div>
                                    </div>
                                </div>
                            </div>`;

                        $('#investmentsTableBody').append(row);
                    });

                    $('#investmentsWrapper').show();
                    updateSummary();
                }).fail(function() {
                    toastr.error('Could not load investments for this company.');
                });
            }

            // Prefill on page load using the ledger's existing company
            const initialCompanyId = $('#company_id').val();
            if (initialCompanyId) {
                loadInvestments(initialCompanyId);
            }

            $('#company_id').on('change', function() {
                loadInvestments($(this).val());
            });

            /* ---------- Enable/disable amount input per row ---------- */
            $(document).on('change', '.investment-checkbox', function() {
                const $row = $(this).closest('.investment-row');
                const $amountInput = $row.find('.withdrawal-amount-input');

                if ($(this).is(':checked')) {
                    $amountInput.prop('disabled', false).focus();
                    // $availableInput.prop('disabled', false);
                    $row.removeClass('disabled-row');
                } else {
                    $amountInput.prop('disabled', true).val('').removeClass('amount-invalid');
                    // $availableInput.prop('disabled', true);
                    $row.addClass('disabled-row');
                }
                updateSummary();
            });

            $(document).on('input', '.withdrawal-amount-input', function() {
                const $row = $(this).closest('.investment-row');
                const available = parseFloat($row.data('available'));
                const entered = parseFloat($(this).val());

                if (isNaN(entered) || entered <= 0 || entered > available) {
                    if (entered > available) {
                        toastr.error('Amount cannot exceed available balance of ' + available.toFixed(2));
                    }
                    $(this).addClass('amount-invalid');
                } else {
                    $(this).removeClass('amount-invalid');
                }
                updateSummary();
            });

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
                    $('#withdrawalAmountHint').text(
                        diff > 0 ?
                        `Remaining to allocate: ${diff.toFixed(2)}` :
                        `Over-allocated by: ${Math.abs(diff).toFixed(2)}`
                    ).addClass('text-danger').removeClass('text-muted');
                } else {
                    $('#withdrawalAmountHint').text('Split this amount across the investments below.')
                        .addClass('text-muted').removeClass('text-danger');
                }

                $('#submitWithdrawalBtn').prop('disabled', !(count > 0 && !hasInvalid && totalsMatch));
            }
            $('#withdrawal_amount').on('input', updateSummary);

            $('#requestedDatePicker').on('change.datetimepicker', calculateWithdrawalDate);
            $('#durationDays').on('change keyup', calculateWithdrawalDate);

            function calculateWithdrawalDate() {
                let requestedDate = $('#requested_date').val();
                let duration = parseInt($('#durationDays').val(), 10);

                if (!requestedDate || isNaN(duration) || duration <= 0) return;

                let parts = requestedDate.split('-');
                if (parts.length !== 3) return;

                let date = new Date(parts[2], parts[1] - 1, parts[0]);
                if (isNaN(date.getTime())) return;

                date.setDate(date.getDate() + duration);

                let day = String(date.getDate()).padStart(2, '0');
                let month = String(date.getMonth() + 1).padStart(2, '0');
                let year = date.getFullYear();

                $('#withdrawal_date').val(`${day}-${month}-${year}`);
            }

            $('#withdrawal_amount').on('input', function() {
                const targetAmount = parseFloat($(this).val());

                if (totalAvailableBalance <= 0) {
                    if (targetAmount > 0 && !exceedAlertShown) {
                        toastr.error('No available balance to withdraw from for this company.');
                        exceedAlertShown = true;
                    }
                    $(this).addClass('amount-invalid');
                    updateSummary();
                    return;
                }

                if (!isNaN(targetAmount) && targetAmount > totalAvailableBalance) {
                    if (!exceedAlertShown) {
                        toastr.error('Withdrawal amount cannot exceed available balance of ' +
                            totalAvailableBalance.toFixed(2));
                        exceedAlertShown = true;
                    }
                } else {
                    exceedAlertShown = false;
                    $(this).removeClass('amount-invalid');
                }

                updateSummary();
            });

            /* ---------- Submit (PUT to update route) ---------- */
            $('#partialWithdrawalEditForm').on('submit', function(e) {
                e.preventDefault();

                if (!$('#requested_date').val()) {
                    toastr.warning('Please select a requested date.');
                    return;
                }

                const formData = $(this).serialize();
                const url =
                    "{{ route('investor.partial-withdrawal.update', ['id' => $data['ledger']->id]) }}";

                $.ajax({
                    url: url,
                    method: 'PUT',
                    data: formData,
                    dataType: 'json'
                }).done(function(res) {
                    toastr.success(res.message ?? 'Withdrawal updated successfully.');
                    setTimeout(() => {
                        window.location.href =
                            "{{ route('investor.partial-withdrawal-list') }}";
                    }, 1200);
                }).fail(function(xhr) {
                    if (xhr.status === 422 && xhr.responseJSON.errors) {
                        const errors = xhr.responseJSON.errors;
                        let msg = '';
                        $.each(errors, function(key, val) {
                            msg += val[0] + '<br>';
                        });
                        toastr.error(msg);
                    } else {
                        toastr.error(xhr.responseJSON?.message ??
                            'Something went wrong while updating the withdrawal.');
                    }
                });
            });

        });
    </script>
@endsection
