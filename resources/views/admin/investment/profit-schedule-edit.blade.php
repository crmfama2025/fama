@extends('admin.layout.admin_master')

@section('custom_css')
    <style>
        .profit-summary {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: .25rem;
            padding: 1rem;
            height: 100%;
        }

        .profit-summary strong {
            display: block;
            font-size: 1.5rem;
            margin-top: .25rem;
        }
    </style>
@endsection

@section('content')
    @php
        $pageTitle = $title ?? 'Edit Profit Records';
        // Saved values, never old submitted values, define the reference total.
        $savedAmounts = $profitRecords->map(fn($record) => (string) $record->profit_amount)->values()->all();
    @endphp
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>{{ $pageTitle }}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('investment.index') }}">Investments</a></li>
                            <li class="breadcrumb-item active">{{ $pageTitle }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h3 class="card-title font-weight-bold m-0">{{ $pageTitle }}</h3>
                                    <a href="{{ route('investment.index') }}" class="btn btn-info">
                                        <i class="fas fa-arrow-left"></i> Back
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                @if (session('success'))
                                    <div class="alert alert-success" role="status">{{ session('success') }}</div>
                                @endif
                                @if ($errors->any())
                                    <div class="alert alert-danger" role="alert">
                                        <ul class="mb-0">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <p class="text-muted">Redistribute the amounts below. The edited total must exactly match
                                    the original total before submitting.</p>
                                <div class="row mb-4">
                                    <div class="col-md-4 mb-2">
                                        <div class="profit-summary">Original Total<strong
                                                id="profitOriginalTotal">—</strong></div>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <div class="profit-summary">Edited Total<strong id="profitScheduleTotal">—</strong>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <div class="profit-summary">Difference<strong id="profitDifference">—</strong></div>
                                    </div>
                                </div>

                                <form id="profitEditForm" method="POST"
                                    action="{{ route('investments.profit-schedule.update', $investment->id) }}">
                                    @csrf
                                    @method('PUT')
                                    @isset($snapshot)
                                        <input type="hidden" name="snapshot" value="{{ old('snapshot', $snapshot) }}">
                                    @endisset
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th style="width:60px">#</th>
                                                    <th>Profit Date</th>
                                                    <th>Profit Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody id="profitScheduleBody">
                                                @forelse ($profitRecords as $index => $profitRecord)
                                                    <tr>
                                                        <td>
                                                            {{ $loop->iteration }}
                                                            <input type="hidden"
                                                                name="profit_records[{{ $index }}][id]"
                                                                value="{{ $profitRecord->id }}">
                                                        </td>
                                                        <td>
                                                            <input type="date"
                                                                name="profit_records[{{ $index }}][profit_release_month]"
                                                                class="form-control"
                                                                aria-label="Profit date for row {{ $loop->iteration }}"
                                                                value="{{ old("profit_records.{$index}.profit_release_month", \Carbon\Carbon::parse($profitRecord->profit_release_month)->toDateString()) }}"
                                                                min="{{ today()->toDateString() }}"
                                                                max="{{ \Carbon\Carbon::parse($investment->maturity_date)->toDateString() }}"
                                                                required>
                                                        </td>
                                                        <td>
                                                            <input type="text" inputmode="decimal"
                                                                name="profit_records[{{ $index }}][profit_amount]"
                                                                class="form-control profit-row-amount"
                                                                aria-label="Profit amount for row {{ $loop->iteration }}"
                                                                value="{{ old("profit_records.{$index}.profit_amount", $profitRecord->profit_amount) }}"
                                                                pattern="[0-9]{1,9}([.][0-9]{1,2})?" required>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="3" class="text-center">No editable future profit
                                                            records.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                    <div id="profitScheduleMessage" class="alert alert-info" role="status"
                                        aria-live="polite">Checking totals…</div>
                                    <noscript>
                                        <div class="alert alert-danger">Enable JavaScript to validate and submit changes.
                                        </div>
                                    </noscript>
                                    @if ($profitRecords->isNotEmpty())
                                        <div class="text-right">
                                            <button id="profitSubmitButton" type="submit" class="btn btn-info" disabled>
                                                <i class="fa fa-save"></i> Update Profit Records
                                            </button>
                                        </div>
                                    @endif
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('custom_js')
    <script>
        (() => {
            function initializeProfitEdit() {
                const form = document.getElementById('profitEditForm');
                const button = document.getElementById('profitSubmitButton');
                const message = document.getElementById('profitScheduleMessage');
                const inputs = [...form.querySelectorAll('.profit-row-amount')];
                const savedAmounts = @json($savedAmounts);
                const staleSession = @json($errors->has('snapshot'));
                let saving = false;

                function toCents(value) {
                    if (!/^\d{1,9}(?:\.\d{1,2})?$/.test(value)) return null;
                    const [whole, fraction = ''] = value.split('.');
                    return BigInt(whole) * 100n + BigInt(fraction.padEnd(2, '0'));
                }

                function money(value) {
                    const absolute = value < 0n ? -value : value;
                    return (value < 0n ? '-' : '') + (absolute / 100n).toString() +
                        '.' + (absolute % 100n).toString().padStart(2, '0');
                }

                const savedCents = savedAmounts.map(toCents);
                const baselineValid = savedCents.length > 0 && savedCents.every(value => value !== null);
                const original = baselineValid ? savedCents.reduce((sum, value) => sum + value, 0n) : 0n;
                document.getElementById('profitOriginalTotal').textContent = baselineValid ? money(original) : '—';

                function validate(showMessage = false) {
                    message.hidden = !showMessage;
                    let total = 0n;
                    let amountsValid = inputs.length > 0;
                    inputs.forEach(input => {
                        const cents = toCents(input.value);
                        input.setCustomValidity(cents === null ?
                            'Enter a non-negative amount, up to 9 whole digits and 2 decimal places.' : '');
                        if (cents === null) amountsValid = false;
                        else total += cents;
                    });
                    const difference = total - original;
                    const fieldsValid = [...form.querySelectorAll('input')].every(input => input.validity.valid);
                    const valid = baselineValid && amountsValid && fieldsValid && difference === 0n && !staleSession;
                    document.getElementById('profitScheduleTotal').textContent = amountsValid ? money(total) : '—';
                    document.getElementById('profitDifference').textContent = baselineValid && amountsValid ? money(
                        difference) : '—';
                    if (button) button.disabled = !valid || saving;
                    message.className = 'alert ' + (valid ? 'alert-success' : 'alert-danger');
                    if (!inputs.length) message.textContent = 'No editable future profit records.';
                    else if (staleSession) message.textContent = 'Reload the latest records before editing again.';
                    else if (!baselineValid) message.textContent =
                        'The saved amounts have an unsupported format. Correct the saved data before editing.';
                    else if (!amountsValid) message.textContent = 'Enter a valid amount in every row.';
                    else if (difference < 0n) message.textContent = 'Total is ' + money(-difference) +
                        ' below the original total. Increase the amounts to match.';
                    else if (difference > 0n) message.textContent = 'Total is ' + money(difference) +
                        ' above the original total. Reduce the amounts to match.';
                    else if (!fieldsValid) message.textContent =
                        'Totals match. Enter valid dates within the allowed range before submitting.';
                    else message.textContent = 'Totals match exactly. You can submit the changes.';
                    return valid;
                }

                form.addEventListener('input', () => validate(true));
                form.addEventListener('change', () => validate(true));
                form.addEventListener('submit', event => {
                    if (saving || !validate(true) || !form.reportValidity()) {
                        event.preventDefault();
                        return;
                    }
                    saving = true;
                    button.disabled = true;
                    button.textContent = 'Saving…';
                });
                window.addEventListener('pageshow', () => {
                    saving = false;
                    if (button) button.textContent = 'Update Profit Records';
                    validate();
                });
                validate();
            }
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initializeProfitEdit);
            } else initializeProfitEdit();
        })();
    </script>
@endsection
