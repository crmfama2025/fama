@extends('admin.layout.admin_master')

@section('content')
    <div class="content-wrapper">

        {{-- Page Header --}}
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Company Detail</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard.index') }}">Home</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('company.index') }}">Companies</a>
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
                        <i class="fas fa-briefcase mr-1 text-blue"></i> {{ $company->company_name }}
                    </h3>

                    <div class="card-tools">
                        <a href="{{ route('company.index') }}" class="btn btn-sm btn-warning">
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
                                <div class="col-md-4">
                                    <div class="info-box bg-light">
                                        <div class="info-box-content text-center">
                                            <span class="info-box-text text-muted">Company Code</span>
                                            <span class="info-box-number">{{ $company->company_code ?? '-' }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="info-box bg-light">
                                        <div class="info-box-content text-center">
                                            <span class="info-box-text text-muted">Short Code</span>
                                            <span class="info-box-number">{{ $company->company_short_code ?? '-' }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="info-box bg-light">
                                        <div class="info-box-content text-center">
                                            <span class="info-box-text text-muted">Status</span>
                                            <span class="badge badge-{{ $company->status ? 'success' : 'secondary' }}">
                                                {{ $company->status ? 'Active' : 'Inactive' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Company Details --}}
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h3 class="card-title">Company Information</h3>
                                </div>
                                <div class="card-body">
                                    <table class="table table-striped">
                                        <tr>
                                            <th width="30%">Company Name</th>
                                            <td>{{ $company->company_name ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th width="30%">Company Name in Arabic</th>
                                            <td>{{ $company->company_arabic_name ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th width="30%">Trade License Number</th>
                                            <td>{{ $company->trade_license_number ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th width="30%">Registration Number</th>
                                            <td>{{ $company->registration_no ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Industry</th>
                                            <td>{{ $company->industry->name ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Phone</th>
                                            <td>{{ $company->phone ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Email</th>
                                            <td>{{ $company->email ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Website</th>
                                            <td>
                                                @if ($company->website)
                                                    <a href="{{ $company->website }}" target="_blank">
                                                        {{ $company->website }}
                                                    </a>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Address</th>
                                            <td>{{ $company->address ?? '-' }}</td>
                                        </tr>
                                        {{-- {{ dump($company) }} --}}
                                        <tr>
                                            <th>Lettter Head</th>
                                            <td>
                                                @if (!empty($company->letter_head_path))
                                                    <a href="{{ asset('storage/' . $company->letter_head_path) }}"
                                                        target="_blank" class="btn btn-sm btn-primary">
                                                        View
                                                    </a>
                                                @else
                                                    <span class="text-muted">No file</span>
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
                                <i class="fas fa-info-circle"></i> Meta Details
                            </h4>

                            <div class="text-muted">
                                <p class="text-sm">
                                    Added By
                                    <b class="d-block">
                                        {{ $company->addedBy->first_name ?? '-' }}
                                        {{ $company->addedBy->last_name ?? '' }}
                                    </b>
                                </p>

                                <p class="text-sm">
                                    Updated By
                                    <b class="d-block">
                                        {{ $company->updatedBy->first_name ?? '-' }}
                                        {{ $company->updatedBy->last_name ?? '' }}
                                    </b>
                                </p>

                                <p class="text-sm">
                                    Created At
                                    <b class="d-block">
                                        {{ $company->created_at?->format('d M Y') ?? '-' }}
                                    </b>
                                </p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
