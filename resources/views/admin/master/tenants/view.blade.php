@extends('admin.layout.admin_master')

@section('content')
    <div class="content-wrapper">

        {{-- Page Header --}}
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Tenant Detail</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('tenant.index') }}">Tenant</a></li>
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
                        <i class="fas fa-building mr-1 text-info"></i> {{ $tenant->tenant_name }}
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('tenant.index') }}" class="btn signinbtn">
                            <i class="fas fa-arrow-left mr-1"></i> Back
                        </a>
                    </div>
                </div>

                {{-- Card Body --}}
                <div class="card-body">
                    <div class="row">

                        {{-- =================== LEFT SIDE =================== --}}
                        <div class="col-lg-8">

                            {{-- Info Boxes --}}
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="info-box bg-light">
                                        <div class="info-box-content text-center">
                                            <span class="info-box-text text-muted">Tenant Code</span>
                                            <span class="info-box-number">{{ $tenant->tenant_code }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-box bg-light">
                                        <div class="info-box-content text-center">
                                            <span class="info-box-text text-muted">Email</span>
                                            <span class="info-box-number" style="font-size:13px;">
                                                {{ $tenant->tenant_email ?? '-' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-box bg-light">
                                        <div class="info-box-content text-center">
                                            <span class="info-box-text text-muted">Mobile</span>
                                            <span class="info-box-number" style="font-size:13px;">
                                                {{ $tenant->tenant_mobile ?? '-' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Tenant Information --}}
                            <div class="card mt-3">
                                <div class="card-header signinbtn">
                                    <h3 class="card-title text-white">
                                        <i class="fas fa-building mr-1"></i> Tenant Information
                                    </h3>
                                </div>
                                <div class="card-body p-0">
                                    <table class="table table-striped mb-0">
                                        <tr>
                                            <th width="35%">Tenant Type</th>
                                            <td>
                                                {{ isset($tenant->tenant_type)
                                                    ? ($tenant->tenant_type == 1
                                                        ? 'B2B'
                                                        : ($tenant->tenant_type == 2
                                                            ? 'B2C'
                                                            : '-'))
                                                    : '-' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th width="35%">Tenant Name</th>
                                            <td>{{ $tenant->tenant_name ?? '-' }}</td>
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
                                            <th>Payment Method</th>
                                            <td>{{ $tenant->paymentMode->payment_mode_name ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Payment Frequency</th>
                                            <td>{{ $tenant->paymentFrequency->profit_interval_name ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Security Cheque</th>
                                            <td>
                                                <span
                                                    class="badge badge-{{ $tenant->security_cheque_status ? 'success' : 'secondary' }}">
                                                    {{ $tenant->security_cheque_status ? 'Yes' : 'No' }}
                                                </span>
                                            </td>
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

                            {{-- Contact Person --}}
                            <div class="card mt-3">
                                <div class="card-header bg-light">
                                    <h3 class="card-title">
                                        <i class="fas fa-user mr-1 text-info"></i> Contact Person
                                    </h3>
                                </div>
                                <div class="card-body p-0">
                                    <table class="table table-striped mb-0">
                                        <tr>
                                            <th width="35%">Name</th>
                                            <td>{{ $tenant->contact_person ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Email</th>
                                            <td>{{ $tenant->contact_email ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Mobile</th>
                                            <td>{{ $tenant->contact_number ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Department</th>
                                            <td>{{ $tenant->contact_person_department ?? '-' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            {{-- Trade License --}}
                            @php
                                $tradeLicense = $tenant->tenantDocuments->firstWhere('document_type', 3);
                            @endphp
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
                                            <td>
                                                {{ $tradeLicense?->issued_date ? \Carbon\Carbon::parse($tradeLicense->issued_date)->format('d M Y') : '-' }}
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

                            {{-- Owner Documents --}}
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
                                                <div class="col-md-6">
                                                    <h6 class="text-muted font-weight-bold mb-2">
                                                        <i class="fas fa-id-card mr-1"></i> Emirates ID
                                                    </h6>
                                                    <table class="table table-sm table-bordered mb-0">
                                                        <tr>
                                                            <th width="45%">Number</th>
                                                            <td>{{ $emirates->document_number ?? '-' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Issued Date</th>
                                                            <td>
                                                                {{ $emirates?->issued_date ? \Carbon\Carbon::parse($emirates->issued_date)->format('d M Y') : '-' }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>Expiry Date</th>
                                                            <td>
                                                                @if ($emirates?->expiry_date)
                                                                    @php $expired = \Carbon\Carbon::parse($emirates->expiry_date)->isPast(); @endphp
                                                                    <span
                                                                        class="badge badge-{{ $expired ? 'danger' : 'success' }} mr-1">
                                                                        {{ $expired ? 'Expired' : 'Valid' }}
                                                                    </span>
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
                                                                        target="_blank" class="btn btn-sm btn-outline-info">
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
                                                            <th>Issued Date</th>
                                                            <td>
                                                                {{ $passport?->issued_date ? \Carbon\Carbon::parse($passport->issued_date)->format('d M Y') : '-' }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>Expiry Date</th>
                                                            <td>
                                                                @if ($passport?->expiry_date)
                                                                    @php $expired = \Carbon\Carbon::parse($passport->expiry_date)->isPast(); @endphp
                                                                    <span
                                                                        class="badge badge-{{ $expired ? 'danger' : 'success' }} mr-1">
                                                                        {{ $expired ? 'Expired' : 'Valid' }}
                                                                    </span>
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
                                                                        target="_blank" class="btn btn-sm btn-outline-info">
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

                        </div>
                        {{-- END LEFT SIDE --}}

                        {{-- =================== RIGHT SIDE =================== --}}
                        <div class="col-lg-4">

                            {{-- Meta Details --}}
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h3 class="card-title">
                                        <i class="fas fa-info-circle mr-1 text-info"></i> Meta Details
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <p class="text-sm">
                                        Added By
                                        <b class="d-block">
                                            {{ $tenant->addedBy->first_name ?? '-' }}
                                            {{ $tenant->addedBy->last_name ?? '' }}
                                        </b>
                                    </p>
                                    <p class="text-sm">
                                        Updated By
                                        <b class="d-block">
                                            {{ $tenant->updatedBy->first_name ?? '-' }}
                                            {{ $tenant->updatedBy->last_name ?? '' }}
                                        </b>
                                    </p>
                                    <p class="text-sm">
                                        Created On
                                        <b class="d-block">
                                            {{ \Carbon\Carbon::parse($tenant->created_at)->format('d M Y') }}
                                        </b>
                                    </p>
                                    <p class="text-sm mb-0">
                                        Updated On
                                        <b class="d-block">
                                            {{ \Carbon\Carbon::parse($tenant->updated_at)->format('d M Y') }}
                                        </b>
                                    </p>
                                </div>
                            </div>

                            {{-- Document Summary --}}
                            <div class="card mt-3">
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
                                            <td>
                                                <span class="badge badge-info">
                                                    {{ $ownerDocs->count() }}
                                                </span>
                                            </td>
                                        </tr>
                                        @foreach ($ownerDocs as $ownerIndex => $docs)
                                            @php
                                                $p = $docs->firstWhere('document_type', 1);
                                                $e = $docs->firstWhere('document_type', 2);
                                            @endphp
                                            <tr>
                                                <th>Owner {{ $loop->iteration }} Passport</th>
                                                <td>
                                                    <span class="badge badge-{{ $p ? 'success' : 'secondary' }}">
                                                        {{ $p ? 'Uploaded' : 'Missing' }}
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Owner {{ $loop->iteration }} Emirates ID</th>
                                                <td>
                                                    <span class="badge badge-{{ $e ? 'success' : 'secondary' }}">
                                                        {{ $e ? 'Uploaded' : 'Missing' }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </div>
                            </div>

                            {{-- Quick Actions --}}
                            <div class="card mt-3">
                                <div class="card-header bg-light">
                                    <h3 class="card-title">
                                        <i class="fas fa-bolt mr-1 text-info"></i> Quick Actions
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <a href="{{ route('tenant.edit', $tenant->id) }}"
                                        class="btn signinbtn btn-block mb-2">
                                        <i class="fas fa-edit mr-1"></i> Edit Tenant
                                    </a>
                                    <a href="{{ route('tenant.index') }}" class="btn btn-secondary btn-block">
                                        <i class="fas fa-list mr-1"></i> All Tenants
                                    </a>
                                </div>
                            </div>

                        </div>
                        {{-- END RIGHT SIDE --}}

                    </div>
                </div>
                {{-- END Card Body --}}

            </div>
        </section>

    </div>
@endsection
