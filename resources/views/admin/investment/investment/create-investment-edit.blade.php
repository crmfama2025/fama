@extends('admin.layout.admin_master')

@section('custom_css')
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('assets/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">

    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet" href="{{ asset('assets/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">

    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('assets/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
@endsection

@section('content')
    <div class="content-wrapper">

        <!-- Page Header -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Investment</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Investment</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <!-- Main Content -->
        <section class="content">
            <div class="container-fluid">

                <div class="row">
                    <div class="col-12">
                        <div class="card">

                            <!-- Card Header -->
                            <div class="card-header">
                                <div class=" d-flex justify-content-between align-items-center">
                                    <!-- Title on the right -->
                                    <h3 class="card-title font-weight-bold m-0">Add Investment</h3>
                                    <!-- Back button on the left -->
                                    <a href="{{ route('investment.index') }}" class="btn btn-info">
                                        <i class="fas fa-arrow-left"></i> Back
                                    </a>
                                </div>
                            </div>


                            <!-- Card Body -->
                            <div class="card-body">
                                <form id="investmentForm" method="POST" enctype="multipart/form-data" novalidate>
                                    @csrf

                                    @if (isset($investment))
                                        @method('PUT')
                                        <input type="hidden" name="investment_id" id="investment_id"
                                            value="{{ $investment->id }}">
                                    @endif

                                    <!-- ================= Investor Information ================= -->
                                    <div class="card card-outline card-info ">
                                        <div class="card-header">
                                            <h3 class="card-title font-weight-bold">Investor Information</h3>
                                        </div>

                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="asterisk">Investor</label>
                                                        <select class="form-control select2" name="investor_id"
                                                            id="investor_id" required>
                                                            <option value="">Select Investor</option>
                                                            @foreach ($data['investors'] as $investor)
                                                                <option value="{{ $investor->id }}"
                                                                    data-reference-id="{{ $investor->referral_id }}"
                                                                    data-referror="{{ $investor->referral ? $investor->referral->investor_name . ' - ' . $investor->referral->investor_code : '' }}"
                                                                    data-payout-batch-id="{{ $investor->payout_batch_id }}"
                                                                    data-profit-release-date="{{ $investor->profit_release_date }}"
                                                                    data-banks='@json($investor->investorBanks)'
                                                                    data-investments='@json($investor->total_no_of_investments)'
                                                                    {{ (isset($investment) && $investment->investor_id) || $parent['investor_id'] == $investor->id ? 'selected' : '' }}>
                                                                    {{ $investor->investor_name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="asterisk">Investment Amount</label>
                                                        <input type="number" class="form-control" id="investment_amount"
                                                            name="investment_amount" placeholder="Enter Investment Amount"
                                                            value="{{ old('investment_amount', isset($investment) && $investment->investment_amount ? $investment->investment_amount : $parent['amount'] ?? '') }}"
                                                            required>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="asterisk">Received Amount</label>
                                                        <input type="number" class="form-control" name="received_amount"
                                                            id="received_amount" placeholder="Enter Received Amount"
                                                            value="{{ old('received_amount', isset($investment) && $investment->received_amount ? $investment->received_amount : $parent['amount'] ?? '') }}"
                                                            {{ $paymentsCount > 1 ? 'readonly' : '' }}>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="asterisk">Investment Date</label>
                                                        <div class="input-group date" id="investmentdate"
                                                            data-target-input="nearest">
                                                            <input type="text" class="form-control datetimepicker-input"
                                                                name="investment_date" id="investment_date"
                                                                data-target="#investmentdate" placeholder="DD-MM-YYYY"
                                                                value="{{ old('investment_date', isset($investment->investment_date) ? \Carbon\Carbon::parse($investment->investment_date)->format('d-m-Y') : $parent['date'] ?? '') }}"
                                                                required>
                                                            <div class="input-group-append" data-target="#investmentdate"
                                                                data-toggle="datetimepicker">
                                                                <div class="input-group-text">
                                                                    <i class="fa fa-calendar"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                    <!-- ================= Investment Terms ================= -->
                                    <div class="card card-outline card-info ">
                                        <div class="card-header">
                                            <h3 class="card-title font-weight-bold">Investment Terms</h3>
                                        </div>

                                        <div class="card-body">
                                            <div class="row">

                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="asterisk">Investment Tenure (Months)</label>
                                                        <input type="number" class="form-control"
                                                            name="investment_tenure"
                                                            value="{{ old('investment_tenure', $investment->investment_tenure ?? '') }}"
                                                            id="investment_tenure" placeholder="Invesmnet Tenure"
                                                            required>
                                                    </div>
                                                </div>



                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="asterisk">Grace Period (Days)</label>
                                                        <input type="number" class="form-control" name="grace_period"
                                                            value="{{ old('grace_period', $investment->grace_period ?? '') }}"
                                                            id="grace_period" placeholder="Grace Period" required>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="asterisk">Maturity Date</label>
                                                        <div class="input-group date" id="maturityDate"
                                                            data-target-input="nearest">
                                                            <input type="text"
                                                                class="form-control datetimepicker-input"
                                                                value="{{ old('maturity_date', isset($investment->maturity_date) ? \Carbon\Carbon::parse($investment->maturity_date)->format('d-m-Y') : '') }}"
                                                                name="maturity_date" id="maturity_date" required
                                                                data-target="#maturityDate" placeholder="DD-MM-YYYY">
                                                            <div class="input-group-append" data-target="#maturityDate"
                                                                data-toggle="datetimepicker">
                                                                <div class="input-group-text">
                                                                    <i class="fa fa-calendar"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                    <!-- ================= Profit ================= -->
                                    <div class="card card-outline card-info ">
                                        <div class="card-header">
                                            <h3 class="card-title font-weight-bold">Profit Details</h3>
                                        </div>

                                        <div class="card-body">
                                            <div class="row">

                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="asterisk">Profit %</label>
                                                        <input type="number" step="0.01" class="form-control"
                                                            name="profit_perc"
                                                            value="{{ old('profit_perc', $investment->profit_perc ?? '') }}"
                                                            id="profit_perc" placeholder="Profit Percentage" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Profit Amount</label>
                                                        <input type="text" class="form-control" name="profit_amount"
                                                            value="{{ old('profit_amount', $investment->profit_amount ?? '') }}"
                                                            id="profit_amount" placeholder="Profit Amount" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="asterisk">Profit Interval</label>
                                                        <select class="form-control select2" id="profit_interval_id"
                                                            name="profit_interval_id" required>
                                                            <option value="">Select Interval</option>
                                                            @foreach ($data['profitInterval'] as $interval)
                                                                <option value="{{ $interval->id }}"
                                                                    {{ old('profit_interval_id', isset($investment->profit_interval_id) ? $investment->profit_interval_id : '') == $interval->id ? 'selected' : '' }}>
                                                                    {{ $interval->profit_interval_name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>


                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Profit Amount per Interval</label>
                                                        <input type="text" class="form-control"
                                                            name="profit_amount_per_interval"
                                                            id="profit_amount_per_interval"
                                                            value="{{ old('profit_amount_per_interval', $investment->profit_amount_per_interval ?? '') }}"
                                                            placeholder="Profit Amount per Interval" readonly>
                                                    </div>
                                                </div>



                                                {{-- <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="asterisk">Profit Release Date</label>
                                                        <div class="input-group date" id="profitreleasedate"
                                                            data-target-input="nearest">
                                                            <input type="text"
                                                                class="form-control datetimepicker-input"
                                                                name="profit_release_date" id="profit_release_date"
                                                                value="{{ old('profit_release_date', isset($investment->profit_release_date) ? $investment->profit_release_date : '') }}"
                                                                data-target="#profitreleasedate" placeholder="DD-MM-YYYY">
                                                            <div class="input-group-append"
                                                                data-target="#profitreleasedate"
                                                                data-toggle="datetimepicker">
                                                                <div class="input-group-text">
                                                                    <i class="fa fa-calendar"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div> --}}
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Profit Release Date</label>
                                                        <select name="profit_release_date" id="profit_release_date"
                                                            class="form-control">
                                                            <option value="">Select Date</option>
                                                            @for ($i = 1; $i <= 31; $i++)
                                                                <option value="{{ $i }}"
                                                                    {{ old('profit_release_date', $investment->profit_release_date ?? '') == $i ? 'selected' : '' }}>
                                                                    {{ $i }}
                                                                </option>
                                                            @endfor
                                                        </select>
                                                    </div>
                                                </div>

                                                {{-- @dd($investment->payout_batch_id) --}}

                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Payout Batch</label>
                                                        <select class="form-control select2" id="payout_batch_id"
                                                            name="payout_batch_id" required>
                                                            <option value="">Select Payout Batch</option>
                                                            @foreach ($data['payoutBatches'] as $batch)
                                                                <option value="{{ $batch->id }}"
                                                                    {{ old('payout_batch_id', isset($investment->payout_batch_id) ? $investment->payout_batch_id : '') == $batch->id ? 'selected' : '' }}>
                                                                    {{ $batch->batch_name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="asterisk">First Profit Release Date</label>
                                                        <div class="input-group date" id="firstprofitreleasedate"
                                                            data-target-input="nearest">
                                                            <input type="text"
                                                                class="form-control datetimepicker-input"
                                                                name="next_profit_release_date"
                                                                id="first_profit_release_date"
                                                                value="{{ old('next_profit_release_date', isset($investment->next_profit_release_date) ? \Carbon\Carbon::parse($investment->next_profit_release_date)->format('d-m-Y') : '') }}"
                                                                data-target="#firstprofitreleasedate"
                                                                placeholder="DD-MM-YYYY">
                                                            <div class="input-group-append"
                                                                data-target="#firstprofitreleasedate"
                                                                data-toggle="datetimepicker">
                                                                <div class="input-group-text">
                                                                    <i class="fa fa-calendar"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>


                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="asterisk">Investor Bank</label>
                                                        <select class="form-control select2" name="investor_bank_id"
                                                            id="investor_bank_id" required>
                                                        </select>
                                                    </div>
                                                </div>





                                            </div>
                                        </div>
                                    </div>

                                    <!-- ================== Referral ================= -->
                                    @if (isset($investment) && $investment->investmentReferral)
                                        {{-- @dd($investment->investmentReferral); --}}
                                        <input type="hidden" name="investment_referral_id"
                                            value="{{ $investment->investmentReferral->id }}">
                                        <div class="card card-outline card-info ">
                                            <div class="card-header">
                                                <h3 class="card-title font-weight-bold">Referral Details :- <span
                                                        class="referror-det text-success">{{ $investment->investmentReferral->referrer ? $investment->investmentReferral->referrer->investor_name . ' - ' . $investment->investmentReferral->referrer->investor_code : '' }}</span>
                                                </h3>
                                            </div>

                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label class="asterisk">Referral Commission %</label>
                                                            <input type="text" class="form-control"
                                                                name="referral_commission_perc"
                                                                id="referral_commission_perc"
                                                                value="{{ old('referral_commission_perc', $investment->investmentReferral->referral_commission_perc ?? '') }}"
                                                                placeholder="Referral Commission Percentage">
                                                        </div>
                                                    </div>
                                                    <input type="hidden" name="referral_id" id="referral_id"
                                                        value="{{ $investment->investmentReferral->investor_referror_id }}">
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label class="asterisk">Referral Commission Amount</label>
                                                            <input type="text" class="form-control"
                                                                name="referral_commission_amount"
                                                                id="referral_commission_amount"
                                                                value="{{ old('referral_commission_amount', $investment->investmentReferral->referral_commission_amount ?? '') }}"
                                                                placeholder="Referral Commission Amount">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label class="asterisk">Referral Commission Frequency</label>
                                                            <select class="form-control select2"
                                                                name="referral_commission_frequency_id">
                                                                <option value="">Select Frequency</option>
                                                                @foreach ($data['frequency'] as $freq)
                                                                    <option value="{{ $freq->id }}"
                                                                        {{ old('referral_commission_frequency_id', isset($investment->investmentReferral->referral_commission_frequency_id) ? $investment->investmentReferral->referral_commission_frequency_id : '') == $freq->id ? 'selected' : '' }}>
                                                                        {{ $freq->commission_frequency_name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label class="asterisk">First Referral Commission Release
                                                                Date</label>
                                                            <div class="input-group date"
                                                                id="firstreferralcommissionreleasedate"
                                                                data-target-input="nearest">
                                                                <input type="text"
                                                                    class="form-control datetimepicker-input"
                                                                    name="next_referral_commission_release_date"
                                                                    id="first_referral_commission_release_date"
                                                                    value="{{ old('next_referral_commission_release_date', isset($investment->next_referral_commission_release_date) ? \Carbon\Carbon::parse($investment->next_referral_commission_release_date)->format('d-m-Y') : '') }}"
                                                                    data-target="#firstreferralcommissionreleasedate"
                                                                    placeholder="DD-MM-YYYY">
                                                                <div class="input-group-append"
                                                                    data-target="#firstreferralcommissionreleasedate"
                                                                    data-toggle="datetimepicker">
                                                                    <div class="input-group-text">
                                                                        <i class="fa fa-calendar"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="card card-outline card-info " id="referral-section">
                                            <div class="card-header">
                                                <h3 class="card-title font-weight-bold">Referral Details :- <span
                                                        class="referror-det text-success"></span></h3>
                                            </div>

                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label class="asterisk">Referral Commission %</label>
                                                            <input type="text" class="form-control"
                                                                name="referral_commission_perc"
                                                                id="referral_commission_perc"
                                                                placeholder="Referral Commission Percentage">
                                                        </div>
                                                    </div>
                                                    <input type="hidden" name="referral_id" id="referral_id">
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label class="asterisk">Referral Commission Amount</label>
                                                            <input type="text" class="form-control"
                                                                name="referral_commission_amount"
                                                                id="referral_commission_amount"
                                                                placeholder="Referral Commission Amount">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label class="asterisk">Referral Commission Frequency</label>
                                                            <select class="form-control select2"
                                                                name="referral_commission_frequency_id">
                                                                <option value="">Select Frequency</option>
                                                                @foreach ($data['frequency'] as $freq)
                                                                    <option value="{{ $freq->id }}">
                                                                        {{ $freq->commission_frequency_name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label class="asterisk">First Referral Commission Release
                                                                Date</label>
                                                            <div class="input-group date"
                                                                id="firstreferralcommissionreleasedate"
                                                                data-target-input="nearest">
                                                                <input type="text"
                                                                    class="form-control datetimepicker-input"
                                                                    name="next_referral_commission_release_date"
                                                                    id="first_referral_commission_release_date"
                                                                    data-target="#firstreferralcommissionreleasedate"
                                                                    placeholder="DD-MM-YYYY">
                                                                <div class="input-group-append"
                                                                    data-target="#firstreferralcommissionreleasedate"
                                                                    data-toggle="datetimepicker">
                                                                    <div class="input-group-text">
                                                                        <i class="fa fa-calendar"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif



                                    <!-- ================= Nominee & Documents ================= -->
                                    <div class="card card-outline card-info ">
                                        <div class="card-header">
                                            <h3 class="card-title font-weight-bold">Nominee</h3>
                                        </div>

                                        <div class="card-body">
                                            <div class="row">

                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Nominee Name</label>
                                                        <input type="text" class="form-control" name="nominee_name"
                                                            value="{{ old('nominee_name', $investment->nominee_name ?? '') }}"
                                                            placeholder="Nominee Name">
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Nominee Email</label>
                                                        <input type="email" class="form-control" name="nominee_email"
                                                            value="{{ old('nominee_email', $investment->nominee_email ?? '') }}"
                                                            placeholder="Nominee email">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Nominee Phone</label>
                                                        <input type="number" class="form-control" name="nominee_phone"
                                                            value="{{ old('nominee_phone', $investment->nominee_phone ?? '') }}"
                                                            placeholder="Nominee Phone">
                                                    </div>
                                                </div>



                                            </div>
                                        </div>
                                    </div>

                                    <!-- ================= Company & Bank ================= -->
                                    <div class="card card-outline card-info ">
                                        <div class="card-header">
                                            <h3 class="card-title font-weight-bold">Company & Bank Details</h3>
                                        </div>

                                        <div class="card-body">
                                            <div class="row">

                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="asterisk">Company</label>
                                                        <select class="form-control select2" name="company_id"
                                                            id="company_id" required>
                                                            <option value="">Select Company</option>
                                                            @foreach ($data['companyBanks'] as $company)
                                                                <option value="{{ $company->id }}"
                                                                    data-banks='@json($company->banks)'
                                                                    {{ old('company_id', isset($investment->company_id) ? $investment->company_id : '') == $company->id ? 'selected' : '' }}>
                                                                    {{ $company->company_name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>


                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="asterisk">Company Bank</label>
                                                        <select class="form-control select2" name="company_bank_id"
                                                            id="company_bank_id" required>
                                                            <option value="">Select Company Bank</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="asterisk">Invested Company</label>
                                                        <select class="form-control select2" name="invested_company_id"
                                                            id="invested_company_id" required>
                                                            <option value="">Select Company</option>
                                                            @foreach ($data['companyBanks'] as $company)
                                                                <option value="{{ $company->id }}"
                                                                    data-banks='@json($company->banks)'
                                                                    {{ old('invested_company_id', isset($investment->invested_company_id) ? $investment->invested_company_id : '') == $company->id ? 'selected' : '' }}>
                                                                    {{ $company->company_name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>


                                            </div>
                                        </div>
                                    </div>

                                    <!-- ================= Nominee & Documents ================= -->
                                    <div class="card card-outline card-info ">
                                        <div class="card-header">
                                            <h3 class="card-title font-weight-bold">Documents</h3>
                                        </div>

                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Upload Contract</label>
                                                        <input type="file" class="form-control" name="contract_file">
                                                        @if (isset($investment) && $investment->investmentDocument)
                                                            <input type="hidden" name="document_id"
                                                                value="{{ $investment->investmentDocument->id }}">
                                                            <div class="mt-2">
                                                                <strong>Existing File:</strong>
                                                                <a href="{{ asset('storage/' . $investment->investmentDocument->investment_contract_file_path) }}"
                                                                    target="_blank">
                                                                    {{ basename($investment->investmentDocument->investment_contract_file_name) }}
                                                                </a>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                    {{-- Reinvestment hidden Inputs --}}
                                    <input type="hidden" name="reinvestment_or_not" value="{{ $reinvestment }}">
                                    <input type="hidden" name="parent_investment_id"
                                        value="{{ $parent_investment_id }}">

                                    <!-- ================= Submit ================= -->
                                    <div class="text-right">
                                        <button type="submit" class="btn btn-info px-4">
                                            <i class="fa fa-save"></i> Save Investment
                                        </button>
                                    </div>

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
    <!-- Select2 -->
    <script src="{{ asset('assets/select2/js/select2.full.min.js') }}"></script>

    <!-- Moment & Date Picker -->
    <script src="{{ asset('assets/moment/moment.min.js') }}"></script>
    <script src="{{ asset('assets/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>

    <!-- DataTables -->
    <script src="{{ asset('assets/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>

    <script>
        $(function() {

            // Select2
            $('.select2').select2({
                theme: 'bootstrap4'
            });

            // Date Pickers
            $('#investmentdate').datetimepicker({
                format: 'DD-MM-YYYY'
            });
            $('#profitreleasedate').datetimepicker({
                format: 'DD-MM-YYYY'
            });
            $('#firstprofitreleasedate').datetimepicker({
                format: 'DD-MM-YYYY'
            });
            $('#maturityDate').datetimepicker({
                format: 'DD-MM-YYYY'
            });
            $('#firstreferralcommissionreleasedate').datetimepicker({
                format: 'DD-MM-YYYY'
            });


        });
    </script>

    <script>
        $(document).ready(function() {
            $('#referral-section').hide();
            @if (isset($investment) || $parent['investor_id'])
                $('#investor_id').on('select2:opening', function(e) {
                    e.preventDefault();
                });
            @endif
        });

        function calculateProfit() {
            let investmentAmount = parseFloat($('#investment_amount').val()) || 0;
            let profitPerc = parseFloat($('#profit_perc').val()) || 0;

            let profitAmount = (investmentAmount * profitPerc) / 100;

            $('#profit_amount').val(profitAmount.toFixed(2));
        }

        $('#investment_amount, #profit_perc').on('keyup change', function() {
            calculateProfit();
        });

        function calculateProfitPerInterval() {
            let profitAmount = parseFloat($('#profit_amount').val()) || 0;
            let interval = $('#profit_interval_id').val();
            let divisor = 0;
            switch (interval) {
                case '1':
                    divisor = 12;
                    break;
                case '2':
                    divisor = 4;
                    break;
                case '3':
                    divisor = 2;
                    break;
                case '4':
                    divisor = 1;
                    break;
                case '5':
                    divisor = 6;
                    break;
            }

            let profitPerInterval = profitAmount / divisor;
            $('#profit_amount_per_interval').val(profitPerInterval.toFixed(2));
        }

        $('#profit_amount, #profit_interval_id').on('keyup change', function() {
            calculateProfitPerInterval();
        });


        function calculateMaturityDate() {
            // alert("hi");
            let investmentDate = $('#investment_date').val();
            let tenure = parseInt($('#investment_tenure').val()) || 0;
            let grace = parseInt($('#grace_period').val()) || 0;

            if (investmentDate) {
                let parts = investmentDate.split('-');
                let dateObj = new Date(parts[2], parts[1] - 1, parts[0]);

                dateObj.setMonth(dateObj.getMonth() + tenure);

                dateObj.setDate(dateObj.getDate() + grace);

                let day = ("0" + dateObj.getDate()).slice(-2);
                let month = ("0" + (dateObj.getMonth() + 1)).slice(-2);
                let year = dateObj.getFullYear();

                $('#maturity_date').val(`${day}-${month}-${year}`);
            }
        }

        $('#investment_tenure, #grace_period').on('change keyup', function() {
            calculateMaturityDate();
        });
        $('#investmentdate').on('change.datetimepicker', function() {
            calculateMaturityDate();
            calculateFirstProfitReleaseDate();
        });

        function calculateReferralCommission() {
            let investmentAmount = parseFloat($('#investment_amount').val()) || 0;
            let referalprofitPerc = parseFloat($('#referral_commission_perc').val()) || 0;

            let commissionAmount = (investmentAmount * referalprofitPerc) / 100;

            $('#referral_commission_amount').val(commissionAmount.toFixed(2));
        }

        $('#referral_commission_perc,#investment_amount').on('keyup change', function() {
            calculateReferralCommission();
        });

        let editPayoutBatch = $('#payout_batch_id').val();
        let editProfitReleaseDate = $('#profit_release_date').val();

        function investorChange(selectedBankId = null) {
            let investorId = $('#investor_id').val();
            let selectedOption = $('#investor_id').find(':selected');
            let referenceId = selectedOption.data('reference-id');
            let payoutBatchId = selectedOption.data('payout-batch-id');
            let profitReleaseDate = selectedOption.data('profit-release-date');
            let banks = selectedOption.data('banks');
            let $bankSelect = $('#investor_bank_id');
            let investments = parseInt(selectedOption.data('investments')) || 0;
            console.log(referenceId, investments);

            if (editPayoutBatch) {
                $('#payout_batch_id').val(editPayoutBatch || '').trigger('change');
            } else {
                if (payoutBatchId) {
                    $('#payout_batch_id').val(payoutBatchId).trigger('change');
                } else {
                    $('#payout_batch_id').val('').trigger('change');
                }
            }


            if (editProfitReleaseDate) {

                $('#profit_release_date').val(editProfitReleaseDate)
            } else {
                if (profitReleaseDate) {
                    $('#profit_release_date').val(profitReleaseDate);

                } else {
                    $('#profit_release_date').val('');
                }
            }
            //
            if (referenceId && referenceId > 0 && investments === 0) {
                $('#referral-section').show();
                $('#referral_id').val(referenceId);
                $('.referror-det').text(selectedOption.data('referror'));
            } else {
                $('#referral-section').hide();
            }

            // Populate investor banks
            $bankSelect.empty().append('<option value="">Select Bank</option>');
            if (!banks || banks.length === 0) return;

            $.each(banks, function(index, bank) {
                $bankSelect.append(
                    `<option value="${bank.id}">${bank.investor_bank_name} - ${bank.investor_iban}</option>`
                );
            });

            // For edit: select saved bank, otherwise primary bank
            if (selectedBankId) {
                $bankSelect.val(selectedBankId).trigger('change');
            } else {
                let primaryBank = banks.find(b => b.is_primary == 1);
                if (primaryBank) {
                    $bankSelect.val(primaryBank.id).trigger('change');
                }
            }
        }

        // On change
        $('#investor_id').on('change', function() {
            investorChange();
        });
        $('#grace_period, #profit_interval_id').on('change keyup', function() {
            calculateFirstProfitReleaseDate();
        });

        function calculateFirstProfitReleaseDate() {

            let investmentDate = $('#investment_date').val();
            let gracePeriod = parseInt($('#grace_period').val()) || 0;
            let profitIntervalId = $('#profit_interval_id').val();
            // let releaseDay = parseInt($('#profit_release_day').val()) || null;

            if (!investmentDate || !profitIntervalId) {
                $('#first_profit_release_date').val('');
                return;
            }

            // Only calculate if the input is empty
            let firstProfitInput = $('#first_profit_release_date');
            if (firstProfitInput.val().trim() !== '') {
                return; // keep existing value
            }

            let parts = investmentDate.split('-');
            let date = new Date(parts[2], parts[1] - 1, parts[0]);

            if (isNaN(date.getTime())) {
                $('#first_profit_release_date').val('');
                return;
            }

            date.setDate(date.getDate() + gracePeriod);

            switch (profitIntervalId) {
                case '1':
                    date.setMonth(date.getMonth() + 1);
                    break;
                case '2':
                    date.setMonth(date.getMonth() + 3);
                    break;
                case '3':
                    date.setMonth(date.getMonth() + 6);
                    break;
                case '4':
                    date.setFullYear(date.getFullYear() + 1);
                    break;
                case '5':
                    date.setMonth(date.getMonth() + 2);
                    break;
            }


            let d = String(date.getDate()).padStart(2, '0');
            let m = String(date.getMonth() + 1).padStart(2, '0');
            let y = date.getFullYear();


            $('#first_profit_release_date').val(`${d}-${m}-${y}`);
            $('#first_referral_commission_release_date').val(`${d}-${m}-${y}`);
        }




        // On page load for edit
        let selectedInvestorBankId = "{{ old('investor_bank_id', $investment->investor_bank_id ?? '') }}";
        @php
            $investorId = old('investor_id', isset($investment) ? $investment->investor_id : $parent['investor_id'] ?? '');
        @endphp

        $('#investor_id').val("{{ old('investor_id', $investorId) }}").trigger('change');
        investorChange(selectedInvestorBankId);


        function companyChange(selectedBankId = null) {
            let companyId = $('#company_id').val();
            let selectedOption = $('#company_id option:selected');
            let banks = selectedOption.data('banks');
            let $bankSelect = $('#company_bank_id');

            $bankSelect.empty().append('<option value="">Select Bank</option>');

            if (!banks || banks.length === 0) {
                return;
            }

            $.each(banks, function(index, bank) {
                $bankSelect.append(
                    `<option value="${bank.id}">${bank.bank_name}</option>`
                );
            });

            // Select the bank if provided (for edit)
            if (selectedBankId) {
                $bankSelect.val(selectedBankId).trigger('change');
            }
        }

        // On page load for edit
        let selectedCompanyBankId = "{{ old('company_bank_id', $investment->company_bank_id ?? '') }}";
        $('#company_id').val("{{ old('company_id', $investment->company_id ?? '') }}").trigger('change');
        companyChange(selectedCompanyBankId);

        // When user changes company
        $('#company_id').on('change', function() {
            companyChange();
        });
    </script>
    <script>
        function validateReceivedAmount() {
            let investment = parseFloat($('#investment_amount').val()) || 0;
            let received = parseFloat($('#received_amount').val()) || 0;

            if (received > investment) {
                Swal.fire({
                    icon: 'warning',
                    text: 'Received Amount cannot be greater than Investment Amount.',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2500,
                });
                $('#investmentForm button[type="submit"]').attr('disabled', true);
            } else {
                $('#investmentForm button[type="submit"]').attr('disabled', false);
            }
        }

        $('#investment_amount, #received_amount').on('input', function() {
            validateReceivedAmount();
        });
    </script>

    <script>
        $('#investmentForm').on('submit', function(e) {
            e.preventDefault();

            // HTML5 validation
            // if (!this.checkValidity()) {
            //     // Trigger native browser validation UI
            //     this.reportValidity();
            //     return;
            // }
            const form = this;

            // Run custom validation
            if (!validateFormFields(form)) {
                // Optional: scroll to first error
                const firstError = form.querySelector('.is-invalid');
                if (firstError) {
                    firstError.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                    Swal.fire({
                        icon: 'warning',
                        title: 'Incomplete Step',
                        text: 'Please complete all required inputs.',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2500,
                    });
                }
                return;
            }
            validateReceivedAmount();


            let investmentId = $('#investment_id').val();
            let url = investmentId ? `/investment/${investmentId}` : "{{ route('investment.store') }}";

            let formData = new FormData(this);
            if (investmentId) {
                formData.append('_method', 'PUT');
            }
            showLoader();

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {
                    // Optional: disable submit button
                    $('#investmentForm button[type="submit"]').attr('disabled', true);
                },
                success: function(response) {
                    // Handle success
                    hideLoader();

                    toastr.success(response.message);

                    $('#investmentForm')[0].reset();
                    $('.select2').val(null).trigger('change');
                    $('#referral-section').hide();
                    window.location.href = "{{ route('investment.index') }}";
                },
                error: function(xhr) {
                    // Handle error
                    hideLoader();

                    let errMsg = 'Something went wrong!';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errMsg = xhr.responseJSON.message;
                    }
                    toastr.error(errMsg);
                },
                complete: function() {
                    $('#investmentForm button[type="submit"]').attr('disabled', false);
                }
            });

        });
    </script>
    <script>
        $('#firstprofitreleasedate').on('change.datetimepicker', function(e) {
            // e.date is a moment object
            let fp_date = e.date ? e.date.format('DD-MM-YYYY') : '';
            let $commission = $('#first_referral_commission_release_date');

            if (!$commission.val()) {
                $commission.val(fp_date);
            }
        });
    </script>
    <script>
        function validateFormFields(form) {
            let isValid = true;

            form.querySelectorAll('[required]:not([type="radio"])').forEach(field => {
                // Skip hidden fields
                if (field.offsetParent === null) return;

                if (!field.checkValidity()) {
                    field.classList.add('is-invalid');
                    field.classList.remove('is-valid');
                    isValid = false;
                } else {
                    field.classList.add('is-valid');
                    field.classList.remove('is-invalid');
                }
            });

            // Validate Select2 fields
            $(form).find('select.select2[required]').each(function() {
                if (!$(this).is(':visible')) return;

                const value = $(this).val();
                const container = $(this).next('.select2-container');

                if (!value || value.length === 0) {
                    container.addClass('is-invalid').removeClass('is-valid');
                    isValid = false;
                } else {
                    container.addClass('is-valid').removeClass('is-invalid');
                }
            });

            return isValid;
        }
    </script>
@endsection
