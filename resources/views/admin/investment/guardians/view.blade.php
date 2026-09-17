@extends('admin.layout.admin_master')

@section('content')
    <div class="content-wrapper">


        {{-- Page Header --}}
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Guardian Detail</h1>
                    </div>

                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard.index') }}">Home</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('investor-guardian.index') }}">Guardians</a>
                            </li>
                            <li class="breadcrumb-item active">View</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        {{-- Main Content --}}
        <section class="content">
            <div class="card">

                {{-- Card Header --}}
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user-shield mr-1 text-blue"></i>
                        {{ $investorGuardian->guardian_name ?? 'Guardian' }}
                    </h3>

                    <div class="card-tools">
                        <a href="{{ route('investor-guardian.index') }}" class="btn btn-sm btn-warning">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>

                {{-- Card Body --}}
                <div class="card-body">
                    <div class="row">

                        {{-- LEFT SIDE --}}
                        <div class="col-lg-8">

                            {{-- Info Boxes --}}
                            <div class="row">

                                <div class="col-md-6">
                                    <div class="info-box bg-light">
                                        <div class="info-box-content text-center">
                                            <span class="info-box-text text-muted">
                                                Guardian Code
                                            </span>

                                            <span class="info-box-number">
                                                {{ $investorGuardian->investor_guardian_code ?? '-' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="info-box bg-light">
                                        <div class="info-box-content text-center">
                                            <span class="info-box-text text-muted">
                                                Mobile
                                            </span>

                                            <span class="info-box-number">
                                                {{ $investorGuardian->guardian_mobile ?? '-' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>



                            </div>

                            {{-- Guardian Information --}}
                            <div class="card mt-3">

                                <div class="card-header">
                                    <h3 class="card-title">
                                        Guardian Information
                                    </h3>
                                </div>

                                <div class="card-body">

                                    <table class="table table-striped">

                                        <tr>
                                            <th width="35%">Guardian Code</th>
                                            <td>
                                                {{ $investorGuardian->investor_guardian_code ?? '-' }}
                                            </td>
                                        </tr>

                                        <tr>
                                            <th>Guardian Name</th>
                                            <td>
                                                {{ $investorGuardian->guardian_name ?? '-' }}
                                            </td>
                                        </tr>

                                        <tr>
                                            <th>Guardian Name in Arabic</th>
                                            <td>
                                                {{ $investorGuardian->guardian_name_arabic ?? '-' }}
                                            </td>
                                        </tr>

                                        <tr>
                                            <th>Mobile</th>
                                            <td>
                                                {{ $investorGuardian->guardian_mobile ?? '-' }}
                                            </td>
                                        </tr>

                                        <tr>
                                            <th>Email</th>
                                            <td>
                                                @if (!empty($investorGuardian->guardian_email))
                                                    <a href="mailto:{{ $investorGuardian->guardian_email }}">
                                                        {{ $investorGuardian->guardian_email }}
                                                    </a>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>

                                        <tr>
                                            <th>Address</th>
                                            <td>
                                                {{ $investorGuardian->guardian_address ?? '-' }}
                                            </td>
                                        </tr>

                                        <tr>
                                            <th>Address(Arabic)</th>
                                            <td>
                                                {{ $investorGuardian->guardian_address_ar ?? '-' }}
                                            </td>
                                        </tr>



                                    </table>

                                </div>
                            </div>

                            {{-- Identification Details --}}
                            <div class="card mt-3">

                                <div class="card-header">
                                    <h3 class="card-title">
                                        Identification Details
                                    </h3>
                                </div>

                                <div class="card-body">

                                    <table class="table table-striped">

                                        <tr>
                                            <th width="35%">Emirates ID Number</th>
                                            <td>
                                                {{ $investorGuardian->emirates_id_number ?? '-' }}
                                            </td>
                                        </tr>

                                        <tr>
                                            <th>Emirates ID Expiry Date</th>
                                            <td>
                                                @if (!empty($investorGuardian->eid_expiry_date))
                                                    {{ \Carbon\Carbon::parse($investorGuardian->eid_expiry_date)->format('d M Y') }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>

                                        <tr>
                                            <th>Emirates ID Copy</th>
                                            <td>
                                                @if (!empty($investorGuardian->emirates_id_copy))
                                                    <a href="{{ asset('storage/' . $investorGuardian->emirates_id_copy) }}"
                                                        target="_blank" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-file-alt"></i>
                                                        View Emirates ID
                                                    </a>
                                                @else
                                                    <span class="text-muted">
                                                        No file
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>

                                        <tr>
                                            <th>Passport Number</th>
                                            <td>
                                                {{ $investorGuardian->passport_number ?? '-' }}
                                            </td>
                                        </tr>

                                        <tr>
                                            <th>Passport Expiry Date</th>
                                            <td>
                                                @if (!empty($investorGuardian->passport_expiry_date))
                                                    {{ \Carbon\Carbon::parse($investorGuardian->passport_expiry_date)->format('d M Y') }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>

                                        <tr>
                                            <th>Passport Copy</th>
                                            <td>
                                                @if (!empty($investorGuardian->passport_copy))
                                                    <a href="{{ asset('storage/' . $investorGuardian->passport_copy) }}"
                                                        target="_blank" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-passport"></i>
                                                        View Passport
                                                    </a>
                                                @else
                                                    <span class="text-muted">
                                                        No file
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>

                                    </table>

                                </div>
                            </div>

                        </div>

                        {{-- RIGHT SIDE --}}
                        <div class="col-lg-4">

                            <h4 class="text-primary">
                                <i class="fas fa-info-circle"></i>
                                Meta Details
                            </h4>

                            <div class="text-muted">

                                <p class="text-sm">
                                    Added By

                                    <b class="d-block">
                                        {{ $investorGuardian->addedBy->first_name ?? '-' }}
                                        {{ $investorGuardian->addedBy->last_name ?? '' }}
                                    </b>
                                </p>

                                <p class="text-sm">
                                    Updated By

                                    <b class="d-block">
                                        {{ $investorGuardian->updatedBy->first_name ?? '-' }}
                                        {{ $investorGuardian->updatedBy->last_name ?? '' }}
                                    </b>
                                </p>

                                <p class="text-sm">
                                    Created At

                                    <b class="d-block">
                                        {{ $investorGuardian->created_at?->format('d M Y H:i') ?? '-' }}
                                    </b>
                                </p>

                                <p class="text-sm">
                                    Updated At

                                    <b class="d-block">
                                        {{ $investorGuardian->updated_at?->format('d M Y H:i') ?? '-' }}
                                    </b>
                                </p>

                            </div>

                            {{-- Quick Actions --}}
                            <div class="card mt-4">

                                <div class="card-header">
                                    <h3 class="card-title">
                                        Quick Actions
                                    </h3>
                                </div>

                                <div class="card-body">

                                    <a href="{{ route('investor-guardian.index') }}" class="btn btn-secondary btn-block">
                                        <i class="fas fa-list mr-1"></i>
                                        Guardian List
                                    </a>

                                </div>

                            </div>


                            {{-- Investors Linked to Guardian --}}

                            <div class="card mt-4">

                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-users mr-1 text-primary"></i>
                                        Investors
                                        <span class="badge badge-primary ml-1">
                                            {{ $investorGuardian->investors->count() }}
                                        </span>
                                    </h3>
                                </div>

                                <div class="card-body p-0">

                                    @if ($investorGuardian->investors->count())
                                        <div class="list-group list-group-flush">

                                            @foreach ($investorGuardian->investors as $investor)
                                                <div class="list-group-item">

                                                    <div class="d-flex justify-content-between align-items-center">

                                                        <div>
                                                            <strong class="d-block">
                                                                {{ $investor->investor_name ?? '-' }}
                                                            </strong>

                                                            <small class="text-muted">
                                                                {{ $investor->investor_code ?? '-' }}
                                                            </small>
                                                        </div>

                                                        @if (auth()->user()->hasAnyPermission(['investor.view'], $investor->company_id))
                                                            <a href="{{ route('investor.show', $investor->id) }}"
                                                                class="btn btn-sm btn-primary" title="View Investor">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                        @endif

                                                    </div>

                                                </div>
                                            @endforeach

                                        </div>
                                    @else
                                        <div class="text-center text-muted p-3">
                                            <i class="fas fa-user-slash mb-2"></i>
                                            <p class="mb-0">
                                                No investors linked to this guardian.
                                            </p>
                                        </div>
                                    @endif


                                </div>

                            </div>
                        </div>

                    </div>
        </section>
    </div>
@endsection
