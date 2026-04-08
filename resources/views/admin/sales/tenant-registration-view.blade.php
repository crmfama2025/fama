@extends('admin.layout.admin_master')

@section('content')
    <div class="content-wrapper">

        {{-- ── Page Header ── --}}
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Tenant Registration Detail</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('tenant-registration.index') }}">Tenant
                                    Registration</a></li>
                            <li class="breadcrumb-item active">View</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">

                {{-- ── Top Action Bar ── --}}
                <div class="card card-outline card-primary mb-3">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-file-contract mr-1 text-info"></i>
                            {{ $tenant->tenant_name }}
                            <small class="text-muted ml-2">{{ $agreement->sales_agreement_code }}</small>
                        </h3>
                        <div class="card-tools">
                            {{-- Status Badge --}}
                            @if ($agreement->is_approved == 1)
                                <span class="badge badge-success mr-2"><i
                                        class="fas fa-check-circle mr-1"></i>Approved</span>
                            @elseif($agreement->is_approved == 2)
                                <span class="badge badge-danger mr-2"><i
                                        class="fas fa-times-circle mr-1"></i>Rejected</span>
                            @else
                                <span class="badge badge-warning mr-2"><i class="fas fa-clock mr-1"></i>Pending</span>
                            @endif

                            {{-- Business Type --}}
                            @if ($agreement->business_type == 1)
                                <span class="badge badge-info mr-2"><i class="fas fa-building mr-1"></i>B2B</span>
                            @else
                                <span class="badge badge-secondary mr-2"><i class="fas fa-user mr-1"></i>B2C</span>
                            @endif

                            @if ($agreement->is_approved == 0)
                                @if (auth()->user()->hasAnyPermission(['tenant-registration.approve']))
                                    <a data-action="{{ route('tenant-registration.approve', $agreement->id) }}"
                                        class="btn btn-success btn-sm mr-1 approval-btn">
                                        <i class="fas fa-clipboard-check mr-1"></i> Approve
                                    </a>
                                @endif
                                @if (auth()->user()->hasAnyPermission(['tenant-registration.reject']))
                                    <a data-action="{{ route('tenant-registration.reject', $agreement->id) }}"
                                        class="btn btn-danger btn-sm mr-1 rejection-btn">
                                        <i class="fas fa-times mr-1"></i> Reject
                                    </a>
                                @endif
                                {{-- @elseif ($agreement->is_approved == 2)
                                @if (auth()->user()->hasAnyPermission(['tenant-registration.approve']))
                                    <a data-action="{{ route('tenant-registration.approve', $agreement->id) }}"
                                        class="btn btn-success btn-sm mr-1 approval-btn">
                                        <i class="fas fa-clipboard-check mr-1"></i> Re-Approve
                                    </a>
                                @endif --}}
                            @endif
                            <a href="{{ route('tenant-registration.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left mr-1"></i> Back
                            </a>
                        </div>
                    </div>
                </div>

                {{-- ── Info Boxes ── --}}
                <div class="row">
                    <div class="col-6 col-md-3">
                        <div class="info-box bg-light">
                            <span class="info-box-icon"><i class="fas fa-hashtag text-info"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text text-muted">Agreement Code</span>
                                <span class="info-box-number"
                                    style="font-size:13px;">{{ $agreement->sales_agreement_code ?? '-' }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="info-box bg-light">
                            <span class="info-box-icon"><i class="fas fa-calendar-alt text-success"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text text-muted">Start Date</span>
                                <span class="info-box-number" style="font-size:13px;">
                                    {{ $agreement->start_date ? \Carbon\Carbon::parse($agreement->start_date)->format('d M Y') : '-' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="info-box bg-light">
                            <span class="info-box-icon"><i class="fas fa-calendar-check text-warning"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text text-muted">End Date</span>
                                <span class="info-box-number" style="font-size:13px;">
                                    {{ $agreement->end_date ? \Carbon\Carbon::parse($agreement->end_date)->format('d M Y') : '-' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="info-box bg-light">
                            <span class="info-box-icon"><i class="fas fa-clock text-danger"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text text-muted">Duration</span>
                                <span class="info-box-number" style="font-size:13px;">
                                    @if ($agreement->start_date && $agreement->end_date)
                                        {{ \Carbon\Carbon::parse($agreement->start_date)->diffInMonths($agreement->end_date) }}
                                        Months
                                    @else
                                        -
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── Approval Details Banner ── --}}
                {{-- @if ($agreement->is_approved == 1)
                    <div class="alert alert-default-success alert-dismissible mb-3">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <i class="fas fa-check-circle mr-1"></i>
                        <strong>Approved</strong> by
                        <strong>{{ $agreement->approvedBy->first_name ?? '-' }}
                            {{ $agreement->approvedBy->last_name ?? '' }}</strong>
                        on
                        <strong>{{ $agreement->approved_date ? \Carbon\Carbon::parse($agreement->approved_date)->format('d M Y, h:i A') : '-' }}</strong>
                        @if ($agreement->approved_comments)
                            — <em>"{{ $agreement->approved_comments }}"</em>
                        @endif
                    </div>
                @elseif ($agreement->is_approved == 2)
                    <div class="alert alert-default-danger alert-dismissible mb-3">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <i class="fas fa-times-circle mr-1"></i>
                        <strong>Rejected</strong> by
                        <strong>{{ $agreement->approvedBy->first_name ?? '-' }}
                            {{ $agreement->approvedBy->last_name ?? '' }}</strong>
                        on
                        <strong>{{ $agreement->approved_date ? \Carbon\Carbon::parse($agreement->approved_date)->format('d M Y, h:i A') : '-' }}</strong>
                        @if ($agreement->approved_comments)
                            — <em>"{{ $agreement->approved_comments }}"</em>
                        @endif
                    </div>
                @endif --}}

                <div class="row">

                    {{-- ══════════════ LEFT COLUMN ══════════════ --}}
                    <div class="col-lg-8">

                        {{-- ── Tenant Information ── --}}
                        <div class="card">
                            <div class="card-header signinbtn">
                                <h3 class="card-title text-white">
                                    <i class="fas fa-user-tie mr-1"></i> Tenant Information
                                </h3>
                            </div>
                            <div class="card-body p-0">
                                <table class="table table-striped mb-0">
                                    <tr>
                                        <th width="35%">Tenant Type</th>
                                        <td>
                                            @if ($tenant->tenant_type == 1)
                                                <span class="badge badge-info">B2B</span>
                                            @elseif($tenant->tenant_type == 2)
                                                <span class="badge badge-secondary">B2C</span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Tenant Name</th>
                                        <td>{{ $tenant->tenant_name . ' - ' . $tenant->tenant_code ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td>{{ $tenant->tenant_email ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Mobile</th>
                                        <td>{{ $tenant->tenant_mobile ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Nationality</th>
                                        <td>{{ $tenant->nationality->nationality_name ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Address</th>
                                        <td>
                                            {{ $tenant->tenant_address ?? '' }}
                                            {{ $tenant->tenant_street ? ', ' . $tenant->tenant_street : '' }}
                                            {{ $tenant->tenant_city ? ', ' . $tenant->tenant_city : '' }}
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        {{-- ── Property Info ── --}}
                        <div class="card mt-3">
                            <div class="card-header bg-light">
                                <h3 class="card-title">
                                    <i class="fas fa-building mr-1 text-info"></i> Property Details
                                </h3>
                            </div>
                            <div class="card-body p-0">
                                <table class="table table-striped mb-0">
                                    <tr>
                                        <th width="35%">Property</th>
                                        <td>{{ $agreement->property->property_name ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Area</th>
                                        <td>{{ $agreement->area->area_name ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Locality</th>
                                        <td>{{ $agreement->locality->locality_name ?? '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        {{-- ── B2B Contact Person ── --}}
                        @if ($agreement->business_type == 1)
                            <div class="card mt-3">
                                <div class="card-header bg-light">
                                    <h3 class="card-title">
                                        <i class="fas fa-address-book mr-1 text-info"></i> Contact Person
                                    </h3>
                                </div>
                                <div class="card-body p-0">
                                    <table class="table table-striped mb-0">
                                        <tr>
                                            <th width="35%">Name</th>
                                            <td>{{ $tenant->contact_person ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Department</th>
                                            <td>{{ $tenant->contact_person_department ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Office Landline / Mobile</th>
                                            <td>{{ $tenant->tenant_mobile ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Contact Number</th>
                                            <td>{{ $tenant->contact_number ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Contact Email</th>
                                            <td>{{ $tenant->contact_email ?? '-' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        @endif

                        {{-- ── Rented Units ── --}}
                        <div class="card mt-3">
                            <div class="card-header signinbtn">
                                <h3 class="card-title text-white">
                                    <i class="fas fa-door-open mr-1"></i> Rented Units
                                </h3>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Unit No</th>
                                                <th>Floor</th>
                                                <th>Type</th>
                                                {{-- <th>Subunits</th> --}}
                                                <th>Monthly Rent</th>
                                                <th>Annual Rent</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {{-- @dump($agreement->salesTenantUnits) --}}
                                            @forelse($agreement->agreementUnits  ?? [] as $i => $unit)
                                                <tr>
                                                    <td>{{ $i + 1 }}</td>
                                                    <td>
                                                        <span class="badge badge-info">
                                                            {{ $unit->contractUnitDetail->unit_number ?? '-' }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $unit->floor_number ?? '-' }}</td>
                                                    <td>{{ $unit->unitType->unit_type ?? '-' }}</td>
                                                    {{-- <td>
                                                        @php
                                                            $subIds = $unit->subunit_ids
                                                                ? json_decode($unit->subunit_ids)
                                                                : [];
                                                        @endphp
                                                        @if (!empty($subIds))
                                                            @foreach ($unit->contractUnitDetail->contractSubUnitDetails->whereIn('id', $subIds) as $sub)
                                                                <span
                                                                    class="badge badge-secondary mr-1">{{ $sub->subunit_no }}</span>
                                                            @endforeach
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td> --}}
                                                    <td>
                                                        @if ($unit->monthly_rent)
                                                            <strong>AED
                                                                {{ number_format($unit->monthly_rent, 2) }}</strong>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($unit->annual_rent)
                                                            <strong>AED {{ number_format($unit->annual_rent, 2) }}</strong>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7" class="text-center text-muted">No units found.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        @if ($agreement->business_type == 1)
                            @php
                                $allSubRents = $agreement->agreementUnits->flatMap(
                                    fn($u) => $u->salesTenantSubunitRents ?? collect(),
                                );
                            @endphp

                            @if ($allSubRents->count() > 0)
                                <div class="card mt-3">
                                    <div class="card-header signinbtn">
                                        <h3 class="card-title text-white">
                                            <i class="fas fa-money-bill-wave mr-1"></i> Subunit Rents
                                        </h3>
                                    </div>
                                    <div class="card-body p-0">
                                        <div id="subunitAccordion">
                                            @foreach ($agreement->agreementUnits as $unitIdx => $unit)
                                                @if (($unit->salesTenantSubunitRents ?? collect())->count())
                                                    <div class="card mb-0 border-0 border-bottom">
                                                        {{-- Accordion Header --}}
                                                        <div class="card-header bg-light p-0">
                                                            <button
                                                                class="btn btn-link btn-block text-left px-3 py-2 text-dark"
                                                                data-toggle="collapse"
                                                                data-target="#subunit_{{ $unitIdx }}"
                                                                aria-expanded="{{ $unitIdx === 0 ? 'true' : 'false' }}">
                                                                <span class="badge badge-info mr-2">
                                                                    {{ $unit->contractUnitDetail->unit_number ?? '-' }}
                                                                </span>
                                                                <strong>{{ $unit->unitType->unit_type ?? '-' }}</strong>
                                                                <span class="text-muted ml-2" style="font-size:12px;">
                                                                    Floor {{ $unit->floor_number ?? '-' }}
                                                                </span>
                                                                <span class="badge badge-secondary ml-2">
                                                                    {{ $unit->salesTenantSubunitRents->count() }} subunits
                                                                </span>
                                                                <i class="fas fa-chevron-down float-right mt-1"></i>
                                                            </button>
                                                        </div>

                                                        {{-- Accordion Body --}}
                                                        <div id="subunit_{{ $unitIdx }}"
                                                            class="collapse {{ $unitIdx === 0 ? 'show' : '' }}"
                                                            data-parent="#subunitAccordion">
                                                            <table class="table table-sm mb-0">
                                                                <thead>
                                                                    <tr class="bg-light">
                                                                        <th>#</th>
                                                                        <th>Subunit No</th>
                                                                        <th>Monthly Rent</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach ($unit->salesTenantSubunitRents as $j => $subRent)
                                                                        <tr>
                                                                            <td>{{ $j + 1 }}</td>
                                                                            <td>
                                                                                <span class="badge badge-secondary">
                                                                                    {{ $subRent->contractSubUnitDetail->subunit_no ?? '-' }}
                                                                                </span>
                                                                            </td>
                                                                            <td>
                                                                                @if ($subRent->rent_per_month)
                                                                                    <strong>AED
                                                                                        {{ number_format($subRent->rent_per_month, 2) }}</strong>
                                                                                @else
                                                                                    <span class="text-muted">-</span>
                                                                                @endif
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                                <tfoot>
                                                                    <tr class="bg-light">
                                                                        <th colspan="2" class="text-right">Total</th>
                                                                        <th>
                                                                            AED
                                                                            {{ number_format($unit->salesTenantSubunitRents->sum('rent_per_month'), 2) }}
                                                                        </th>
                                                                    </tr>
                                                                </tfoot>
                                                            </table>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endif

                        {{-- ── Trade License (B2B) ── --}}
                        @if ($agreement->business_type == 1)
                            @php $tradeLicense = $tenant->tenantDocuments->firstWhere('document_type', 3); @endphp
                            <div class="card mt-3">
                                <div class="card-header bg-light">
                                    <h3 class="card-title">
                                        <i class="fas fa-file-alt mr-1 text-info"></i> Trade License
                                    </h3>
                                </div>
                                <div class="card-body p-0">
                                    <table class="table table-striped mb-0">
                                        <tr>
                                            <th width="35%">License Number</th>
                                            <td>{{ $tradeLicense->document_number ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Issued Date</th>
                                            <td>{{ $tradeLicense?->issued_date ? \Carbon\Carbon::parse($tradeLicense->issued_date)->format('d M Y') : '-' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Expiry Date</th>
                                            <td>
                                                @if ($tradeLicense?->expiry_date)
                                                    @php $expired = \Carbon\Carbon::parse($tradeLicense->expiry_date)->isPast(); @endphp
                                                    <span class="badge badge-{{ $expired ? 'danger' : 'success' }} mr-1">
                                                        {{ $expired ? 'Expired' : 'Valid' }}
                                                    </span>
                                                    {{ \Carbon\Carbon::parse($tradeLicense->expiry_date)->format('d M Y') }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Document</th>
                                            <td>
                                                @if ($tradeLicense?->original_document_path)
                                                    <a href="{{ asset('storage/' . $tradeLicense->original_document_path) }}"
                                                        target="_blank" class="btn btn-sm btn-outline-info">
                                                        <i class="fas fa-eye mr-1"></i> View File
                                                    </a>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            {{-- ── Owner Documents ── --}}
                            @php
                                $ownerDocs = $tenant->tenantDocuments
                                    ->where('document_type', '!=', 3)
                                    ->groupBy('owner_index');
                            @endphp
                            @if ($ownerDocs->count() > 0)
                                @foreach ($ownerDocs as $ownerIndex => $docs)
                                    @php
                                        $passport = $docs->firstWhere('document_type', 1);
                                        $emirates = $docs->firstWhere('document_type', 2);
                                    @endphp
                                    <div class="card mt-3">
                                        <div class="card-header signinbtn">
                                            <h3 class="card-title text-white">
                                                <i class="fas fa-user mr-1"></i> Owner {{ $loop->iteration }} Documents
                                            </h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                {{-- Emirates ID --}}
                                                <div class="col-md-6 mb-3 mb-md-0">
                                                    <h6 class="text-muted font-weight-bold mb-2">
                                                        <i class="fas fa-id-card mr-1"></i> Emirates ID
                                                    </h6>
                                                    <table class="table table-sm table-bordered mb-0">
                                                        <tr>
                                                            <th width="45%">Number</th>
                                                            <td>{{ $emirates->document_number ?? '-' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Issued</th>
                                                            <td>{{ $emirates?->issued_date ? \Carbon\Carbon::parse($emirates->issued_date)->format('d M Y') : '-' }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>Expiry</th>
                                                            <td>
                                                                @if ($emirates?->expiry_date)
                                                                    @php $exp = \Carbon\Carbon::parse($emirates->expiry_date)->isPast(); @endphp
                                                                    <span
                                                                        class="badge badge-{{ $exp ? 'danger' : 'success' }} mr-1">{{ $exp ? 'Expired' : 'Valid' }}</span>
                                                                    {{ \Carbon\Carbon::parse($emirates->expiry_date)->format('d M Y') }}
                                                                @else
                                                                    -
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>File</th>
                                                            <td>
                                                                @if ($emirates?->original_document_path)
                                                                    <a href="{{ asset('storage/' . $emirates->original_document_path) }}"
                                                                        target="_blank"
                                                                        class="btn btn-sm btn-outline-info">
                                                                        <i class="fas fa-eye mr-1"></i> View
                                                                    </a>
                                                                @else
                                                                    <span class="text-muted">-</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div>
                                                {{-- Passport --}}
                                                <div class="col-md-6">
                                                    <h6 class="text-muted font-weight-bold mb-2">
                                                        <i class="fas fa-passport mr-1"></i> Passport
                                                    </h6>
                                                    <table class="table table-sm table-bordered mb-0">
                                                        <tr>
                                                            <th width="45%">Number</th>
                                                            <td>{{ $passport->document_number ?? '-' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Issued</th>
                                                            <td>{{ $passport?->issued_date ? \Carbon\Carbon::parse($passport->issued_date)->format('d M Y') : '-' }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>Expiry</th>
                                                            <td>
                                                                @if ($passport?->expiry_date)
                                                                    @php $exp = \Carbon\Carbon::parse($passport->expiry_date)->isPast(); @endphp
                                                                    <span
                                                                        class="badge badge-{{ $exp ? 'danger' : 'success' }} mr-1">{{ $exp ? 'Expired' : 'Valid' }}</span>
                                                                    {{ \Carbon\Carbon::parse($passport->expiry_date)->format('d M Y') }}
                                                                @else
                                                                    -
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>File</th>
                                                            <td>
                                                                @if ($passport?->original_document_path)
                                                                    <a href="{{ asset('storage/' . $passport->original_document_path) }}"
                                                                        target="_blank"
                                                                        class="btn btn-sm btn-outline-info">
                                                                        <i class="fas fa-eye mr-1"></i> View
                                                                    </a>
                                                                @else
                                                                    <span class="text-muted">-</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="alert alert-info mt-3">
                                    <i class="fas fa-info-circle mr-1"></i> No owner documents found.
                                </div>
                            @endif
                        @endif
                        {{-- ── B2C Documents ── --}}
                        @if ($agreement->business_type == 2)
                            @php
                                $emiratesDoc = $tenant->tenantDocuments->firstWhere('document_type', 2);
                                $passportDoc = $tenant->tenantDocuments->firstWhere('document_type', 1);
                            @endphp

                            <div class="card mt-3">
                                <div class="card-header signinbtn">
                                    <h3 class="card-title text-white">
                                        <i class="fas fa-file-alt mr-1"></i> Tenant Documents
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">

                                        {{-- Emirates ID --}}
                                        <div class="col-md-6 mb-3 mb-md-0">
                                            <h6 class="text-muted font-weight-bold mb-2">
                                                <i class="fas fa-id-card mr-1"></i> Emirates ID
                                            </h6>
                                            <table class="table table-sm table-bordered mb-0">
                                                <tr>
                                                    <th width="45%">Number</th>
                                                    <td>{{ $emiratesDoc->document_number ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Issued</th>
                                                    <td>
                                                        {{ $emiratesDoc?->issued_date ? \Carbon\Carbon::parse($emiratesDoc->issued_date)->format('d M Y') : '-' }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Expiry</th>
                                                    <td>
                                                        @if ($emiratesDoc?->expiry_date)
                                                            @php $exp = \Carbon\Carbon::parse($emiratesDoc->expiry_date)->isPast(); @endphp
                                                            <span
                                                                class="badge badge-{{ $exp ? 'danger' : 'success' }} mr-1">
                                                                {{ $exp ? 'Expired' : 'Valid' }}
                                                            </span>
                                                            {{ \Carbon\Carbon::parse($emiratesDoc->expiry_date)->format('d M Y') }}
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>File</th>
                                                    <td>
                                                        @if ($emiratesDoc?->original_document_path)
                                                            <a href="{{ asset('storage/' . $emiratesDoc->original_document_path) }}"
                                                                target="_blank" class="btn btn-sm btn-outline-info">
                                                                <i class="fas fa-eye mr-1"></i> View
                                                            </a>
                                                        @else
                                                            <span class="text-muted">Not uploaded</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>

                                        {{-- Passport --}}
                                        <div class="col-md-6">
                                            <h6 class="text-muted font-weight-bold mb-2">
                                                <i class="fas fa-passport mr-1"></i> Passport
                                            </h6>
                                            <table class="table table-sm table-bordered mb-0">
                                                <tr>
                                                    <th width="45%">Number</th>
                                                    <td>{{ $passportDoc->document_number ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Issued</th>
                                                    <td>
                                                        {{ $passportDoc?->issued_date ? \Carbon\Carbon::parse($passportDoc->issued_date)->format('d M Y') : '-' }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Expiry</th>
                                                    <td>
                                                        @if ($passportDoc?->expiry_date)
                                                            @php $exp = \Carbon\Carbon::parse($passportDoc->expiry_date)->isPast(); @endphp
                                                            <span
                                                                class="badge badge-{{ $exp ? 'danger' : 'success' }} mr-1">
                                                                {{ $exp ? 'Expired' : 'Valid' }}
                                                            </span>
                                                            {{ \Carbon\Carbon::parse($passportDoc->expiry_date)->format('d M Y') }}
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>File</th>
                                                    <td>
                                                        @if ($passportDoc?->original_document_path)
                                                            <a href="{{ asset('storage/' . $passportDoc->original_document_path) }}"
                                                                target="_blank" class="btn btn-sm btn-outline-info">
                                                                <i class="fas fa-eye mr-1"></i> View
                                                            </a>
                                                        @else
                                                            <span class="text-muted">Not uploaded</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            {{-- ── B2C Document Summary (right column equivalent) ── --}}
                            {{-- Also add this in the right column Document Summary section --}}
                        @endif

                        {{-- END B2B ONLY --}}
                        {{-- END B2B ONLY --}}

                    </div>
                    {{-- END LEFT COLUMN --}}

                    {{-- ══════════════ RIGHT COLUMN ══════════════ --}}
                    <div class="col-lg-4">

                        {{-- ── Document Summary ── --}}
                        @if ($agreement->business_type == 1)
                            @php
                                $tradeLicense =
                                    $tradeLicense ?? $tenant->tenantDocuments->firstWhere('document_type', 3);
                                $ownerDocs =
                                    $ownerDocs ??
                                    $tenant->tenantDocuments->where('document_type', '!=', 3)->groupBy('owner_index');
                            @endphp
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h3 class="card-title">
                                        <i class="fas fa-file-alt mr-1 text-info"></i> Document Summary
                                    </h3>
                                </div>
                                <div class="card-body p-0">
                                    <table class="table table-sm mb-0">
                                        <tr>
                                            <th>Trade License</th>
                                            <td>
                                                <span class="badge badge-{{ $tradeLicense ? 'success' : 'secondary' }}">
                                                    {{ $tradeLicense ? 'Uploaded' : 'Missing' }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>No. of Owners</th>
                                            <td><span class="badge badge-info">{{ $ownerDocs->count() }}</span></td>
                                        </tr>
                                        @foreach ($ownerDocs as $ownerIndex => $docs)
                                            @php
                                                $p = $docs->firstWhere('document_type', 1);
                                                $e = $docs->firstWhere('document_type', 2);
                                            @endphp
                                            <tr>
                                                <th>Owner {{ $loop->iteration }} Passport</th>
                                                <td><span
                                                        class="badge badge-{{ $p ? 'success' : 'secondary' }}">{{ $p ? 'Uploaded' : 'Missing' }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Owner {{ $loop->iteration }} Emirates ID</th>
                                                <td><span
                                                        class="badge badge-{{ $e ? 'success' : 'secondary' }}">{{ $e ? 'Uploaded' : 'Missing' }}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </div>
                            </div>
                        @endif

                        {{-- ── Subunit Rents (B2B) ── --}}
                        {{-- ── Subunit Rents (B2B) ── --}}
                        @if ($agreement->business_type == 1)
                            @php
                                $allSubRents = $agreement->salesTenantUnits
                                    ? $agreement->salesTenantUnits->flatMap(
                                        fn($u) => $u->salesTenantSubunitRents ?? collect(),
                                    )
                                    : collect();
                            @endphp

                            @if ($allSubRents->count() > 0)
                                <div class="card mt-3">
                                    <div class="card-header bg-light">
                                        <h3 class="card-title">
                                            <i class="fas fa-money-bill-wave mr-1 text-info"></i> Subunit Rents
                                        </h3>
                                    </div>
                                    <div class="card-body p-0">
                                        <table class="table table-sm mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Unit</th>
                                                    <th>Subunit</th>
                                                    <th>Monthly</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($agreement->salesTenantUnits as $unit)
                                                    @foreach ($unit->salesTenantSubunitRents ?? [] as $subRent)
                                                        <tr>
                                                            <td><span
                                                                    class="badge badge-info">{{ $unit->contractUnitDetail->unit_number ?? '-' }}</span>
                                                            </td>
                                                            <td>{{ $subRent->contractSubUnitDetail->subunit_no ?? '-' }}
                                                            </td>
                                                            <td>
                                                                @if ($subRent->rent_per_month)
                                                                    <strong>AED
                                                                        {{ number_format($subRent->rent_per_month, 2) }}</strong>
                                                                @else
                                                                    <span class="text-muted">-</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif
                        @endif

                        {{-- ── Meta Details ── --}}
                        <div class="card mt-3">
                            <div class="card-header bg-light">
                                <h3 class="card-title">
                                    <i class="fas fa-info-circle mr-1 text-info"></i> Meta Details
                                </h3>
                            </div>
                            <div class="card-body">
                                <p class="text-sm">
                                    Added By
                                    <b class="d-block">{{ $agreement->addedBy->first_name ?? '-' }}
                                        {{ $agreement->addedBy->last_name ?? '' }}</b>
                                </p>
                                <p class="text-sm">
                                    Created On
                                    <b
                                        class="d-block">{{ \Carbon\Carbon::parse($agreement->created_at)->format('d M Y, h:i A') }}</b>
                                </p>
                                <p class="text-sm mb-0">
                                    Last Updated
                                    <b
                                        class="d-block">{{ \Carbon\Carbon::parse($agreement->updated_at)->format('d M Y, h:i A') }}</b>
                                </p>
                            </div>
                        </div>

                        {{-- ── Approval Details ── --}}
                        @if (in_array($agreement->is_approved, [1, 2]))
                            <div class="card mt-3">
                                <div class="card-header {{ $agreement->is_approved == 1 ? 'bg-olive' : 'bg-danger' }}">
                                    <h3 class="card-title text-white">
                                        <i
                                            class="fas fa-{{ $agreement->is_approved == 1 ? 'check-circle' : 'times-circle' }} mr-1"></i>
                                        {{ $agreement->is_approved == 1 ? 'Approval' : 'Rejection' }} Details
                                    </h3>
                                </div>
                                <div class="card-body p-0">
                                    <table class="table table-striped mb-0">
                                        <tr>
                                            <th width="45%">Status</th>
                                            <td>
                                                <span
                                                    class="badge badge-{{ $agreement->is_approved == 1 ? 'success' : 'danger' }}">
                                                    {{ $agreement->is_approved == 1 ? 'Approved' : 'Rejected' }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>{{ $agreement->is_approved == 1 ? 'Approved' : 'Rejected' }} By</th>
                                            <td>
                                                <strong>
                                                    {{ $agreement->approvedBy->first_name ?? '-' }}
                                                    {{ $agreement->approvedBy->last_name ?? '' }}
                                                </strong>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Date & Time</th>
                                            <td>
                                                {{ $agreement->approved_date ? \Carbon\Carbon::parse($agreement->approved_date)->format('d M Y, h:i A') : '-' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Remarks</th>
                                            <td>
                                                <div style="max-height: 100px; overflow-y: auto; word-break: break-word;"
                                                    data-toggle="tooltip" data-html="true"
                                                    title="{{ $agreement->is_approved == 1 ? $agreement->approved_comments ?? '-' : $agreement->rejection_reason ?? '-' }}">
                                                    {{ Str::limit($agreement->is_approved == 1 ? $agreement->approved_comments ?? '-' : $agreement->rejection_reason ?? '-', 100) }}
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        @endif

                        {{-- ── Quick Actions ── --}}
                        <div class="card mt-3">
                            <div class="card-header bg-light">
                                <h3 class="card-title">
                                    <i class="fas fa-bolt mr-1 text-info"></i> Quick Actions
                                </h3>
                            </div>
                            <div class="card-body">
                                @if ($agreement->is_approved == 0)
                                    @if (auth()->user()->hasAnyPermission(['tenant-registration.approve']))
                                        <button type="button" class="btn btn-success btn-block mb-2 approval-btn"
                                            data-action="{{ route('tenant-registration.approve', $agreement->id) }}"
                                            data-status="approved">
                                            <i class="fas fa-clipboard-check mr-1"></i> Approve
                                        </button>
                                    @endif
                                    @if (auth()->user()->hasAnyPermission(['tenant-registration.reject']))
                                        <button type="button" class="btn btn-danger btn-block mb-2 rejection-btn"
                                            data-action="{{ route('tenant-registration.reject', $agreement->id) }}">
                                            <i class="fas fa-times mr-1"></i> Reject
                                        </button>
                                    @endif
                                    {{-- @elseif ($agreement->is_approved == 2)
                                    @if (auth()->user()->hasAnyPermission(['tenant-registration.approve']))
                                        <button type="button" class="btn btn-success btn-block mb-2 approval-btn"
                                            data-action="{{ route('tenant-registration.approve', $agreement->id) }}">
                                            <i class="fas fa-redo mr-1"></i> Re-Approve
                                        </button>
                                    @endif --}}
                                @endif
                                @if (auth()->user()->hasAnyPermission(['tenant-registration.edit']) && $agreement->is_approved != 1)
                                    <a href="{{ route('tenant-registration.edit', $agreement->id) }}"
                                        class="btn btn-warning btn-block">
                                        <i class="fas fa-edit mr-1"></i> Edit
                                    </a>
                                @endif
                                <a href="{{ route('tenant-registration.index') }}" class="btn btn-secondary btn-block">
                                    <i class="fas fa-list mr-1"></i> All Registrations
                                </a>
                            </div>
                        </div>

                    </div>
                    {{-- END RIGHT COLUMN --}}

                </div>
            </div>
        </section>
    </div>
    <!-- Approval Modal -->
    <div class="modal fade" id="approvalModal" tabindex="-1" aria-labelledby="approvalModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="approvalForm" method="POST" action="{{ route('tenant-registration.approve', $agreement->id) }}">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="approvalModalLabel">Approval Tenant</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="approvalRemarks" class="asterisk">Remarks</label>
                            <textarea class="form-control" id="approvalRemarks" name="approved_comments" rows="3"
                                placeholder="Enter your remarks here..." required></textarea>
                        </div>
                        <input type="hidden" name="status" id="approvalStatus">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Rejection Modal -->
    <div class="modal fade" id="rejectionModal" tabindex="-1" aria-labelledby="rejectionModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form id="rejectionForm" method="POST" action="">
                @csrf
                <div class="modal-content">
                    <div class="modal-header bg-danger">
                        <h5 class="modal-title text-white" id="rejectionModalLabel">
                            <i class="fas fa-times-circle mr-1"></i> Reject Tenant Registration
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        {{-- <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            You are about to <strong>reject</strong> this tenant registration. This action cannot be undone.
                        </div> --}}
                        <div class="form-group">
                            <label for="rejectionRemarks" class="asterisk">Rejection Reason </label>
                            <textarea class="form-control" id="rejectionRemarks" name="approved_comments" rows="4"
                                placeholder="Enter the reason for rejection..." required></textarea>
                            {{-- <small class="text-muted">This reason will be visible to the concerned parties.</small> --}}
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-arrow-left mr-1"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-times mr-1"></i> Confirm Reject
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('custom_js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '{{ session('success') }}',
                timer: 3000,
                showConfirmButton: false
            });
            // OR using Toastr:
            // toastr.success('{{ session('success') }}');
        @endif

        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: '{{ session('error') }}',
                timer: 3000,
                showConfirmButton: false
            });
            // OR using Toastr:
            // toastr.error('{{ session('error') }}');
        @endif
    </script>
    <script>
        document.querySelectorAll('.approval-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const action = this.dataset.action;
                // const status = this.dataset.status;

                const form = document.getElementById('approvalForm');
                form.action = action;
                // document.getElementById('approvalStatus').value = status;

                // Show modal (Bootstrap 5)
                const modal = new bootstrap.Modal(document.getElementById('approvalModal'));
                modal.show();
            });
        });
    </script>
    <script>
        document.querySelectorAll('.rejection-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const action = this.dataset.action;

                const form = document.getElementById('rejectionForm');
                form.action = action;

                // Clear previous input
                document.getElementById('rejectionRemarks').value = '';

                const modal = new bootstrap.Modal(document.getElementById('rejectionModal'));
                modal.show();
            });
        });
    </script>
@endsection
