@extends('admin.layout.admin_master')

@section('content')
    <div class="content-wrapper">

        {{-- Page Header --}}
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Bank Detail</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('bank.index') }}">Bank</a></li>
                            <li class="breadcrumb-item active">View</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>
        {{-- @dd($bank); --}}

        {{-- Main Content --}}
        <section class="content">
            <div class="card">

                {{-- Card Header --}}
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-university mr-1 text-blue"></i> {{ $bank->bank_name }}
                    </h3>

                    <div class="card-tools">
                        <a href="{{ route('bank.index') }}" class="btn btn-sm btn-warning">
                            <i class="fa-arrow-alt-circle-left fas"></i> Back
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
                                            <span class="info-box-text text-muted">Bank Code</span>
                                            <span class="info-box-number">{{ $bank->bank_code }}</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- <div class="col-md-4">
                                    <div class="info-box bg-light">
                                        <div class="info-box-content text-center">
                                            <span class="info-box-text text-muted">Phone</span>
                                            <span class="info-box-number">{{ $bank->bank_phone }}</span>
                                        </div>
                                    </div>
                                </div> --}}

                                <div class="col-md-4">
                                    <div class="info-box bg-light">
                                        <div class="info-box-content text-center">
                                            <span class="info-box-text text-muted">Status</span>
                                            <span class="badge badge-{{ $bank->status ? 'success' : 'secondary' }}">
                                                {{ $bank->status ? 'Active' : 'Inactive' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Bank Details --}}
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h3 class="card-title">Bank Information</h3>
                                </div>
                                <div class="card-body">
                                    <table class="table table-striped">
                                        <tr>
                                            <th width="30%">Bank Name</th>
                                            <td>{{ $bank->bank_name ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th width="30%">Bank Name in Arabic</th>
                                            <td>{{ $bank->bank_arabic_name ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th width="30%">Iban</th>
                                            <td>{{ $bank->iban ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th width="30%">Account Number</th>
                                            <td>{{ $bank->account_number ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th width="30%">Bank Short Code<Code></Code></th>
                                            <td>{{ $bank->bank_short_code ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th width="30%">Company Name</th>
                                            <td>{{ $bank->company->company_name ?? '-' }}</td>
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
                                    <b class="d-block">{{ $bank->addedBy->first_name ?? '-' }}
                                        {{ $bank->addedBy->last_name ?? '' }}</b>
                                </p>

                                <p class="text-sm">
                                    Updated By
                                    <b class="d-block">{{ $bank->updatedBy->first_name ?? '-' }}
                                        {{ $bank->updatedBy->last_name ?? '' }}</b>
                                </p>
                                {{-- @dd($bank->created_at) --}}

                                <p class="text-sm">
                                    Created On
                                    <b class="d-block">{{ \Carbon\Carbon::parse($bank->created_at)->format('d M Y') }}
                                    </b>
                                </p>
                                <p class="text-sm">
                                    Updated On
                                    <b class="d-block">{{ \Carbon\Carbon::parse($bank->updated_at)->format('d M Y') }}
                                    </b>
                                </p>



                            </div>

                            {{-- Action Buttons --}}
                            {{-- <div class="text-center mt-4">
                                <a href="{{ route('vendors.index') }}" class="btn btn-sm btn-secondary">Back</a>
                            </div> --}}
                        </div>

                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
