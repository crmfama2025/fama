@extends('admin.layout.admin_master')
@section('custom_css')
    <!-- daterange picker -->

    <link rel="stylesheet" href="{{ asset('assets/daterangepicker/daterangepicker.css') }}">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet" href="{{ asset('assets/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('assets/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    <!-- iCheck for checkboxes and radio inputs -->
    <link rel="stylesheet" href="{{ asset('assets/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/bs-stepper/css/bs-stepper.min.css') }}">
@endsection
@section('content')
    {{-- {{ dd($tenant) }} --}}
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Agreement</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
                            <li class="breadcrumb-item active">Create Agreement
                            </li>
                        </ol>
                        <!-- Back to List Button -->
                        <a href="{{ route('agreement.index') }}" class="btn btn-info float-sm-right mx-2 btn-sm ml-2">
                            <i class="fa fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="bs-stepper">
                                    <div class="bs-stepper-header" role="tablist">
                                        <!-- your steps here -->
                                        <div class="step" data-target="#tenant-step">
                                            <button type="button" class="step-trigger" role="tab"
                                                aria-controls="tenant-step" id="tenant-step-trigger">
                                                <span class="bs-stepper-circle"><i class="far fa-user"></i></span>
                                                <span class="bs-stepper-label">Tenant</span>
                                            </button>
                                        </div>
                                        <div class="line"></div>
                                        <div class="step" data-target="#document-step">
                                            <button type="button" class="step-trigger" role="tab"
                                                aria-controls="document-step" id="documemnt-step-trigger">
                                                <span class="bs-stepper-circle"><i class="fas fa-file-upload"></i></span>
                                                <span class="bs-stepper-label">Documents</span>
                                            </button>
                                        </div>
                                        <div class="line"></div>
                                        <div class="step" data-target="#unit-step">
                                            <button type="button" class="step-trigger" role="tab"
                                                aria-controls="unit-step" id="unit-step-trigger">
                                                <span class="bs-stepper-circle"><i class="fas fa-door-open"></i></span>
                                                <span class="bs-stepper-label">Unit</span>
                                            </button>
                                        </div>

                                        <div class="line"></div>
                                        <div class="step" data-target="#agreement-step">
                                            <button type="button" class="step-trigger" role="tab"
                                                aria-controls="agreement-step" id="agreement-step-trigger">
                                                <span class="bs-stepper-circle"><i class="fas fa-file-contract"></i></span>
                                                <span class="bs-stepper-label">Agreement</span>
                                            </button>
                                        </div>
                                        <div class="line"></div>
                                        <div class="step" data-target="#payment-step">
                                            <button type="button" class="step-trigger" role="tab"
                                                aria-controls="payment-step" id="payment-step-trigger">
                                                <span class="bs-stepper-circle"><i class="fas fa-dollar-sign"></i></span>
                                                <span class="bs-stepper-label">Payment</span>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="bs-stepper-content card p-3">

                                        <form action="{{ route('agreement.store') }}" method="post" id="agreementForm"
                                            enctype="multipart/form-data">
                                            @csrf
                                            {{-- {{ dd($agreement) }} --}}

                                            {{-- Edit case --}}
                                            @isset($agreement)
                                                <input type="hidden" name="agreement_id" value={{ $agreement->id }}>
                                                <input type="hidden" name="tenant_id" value={{ $tenant->id }}>
                                                <input type="hidden" name="payment_id"
                                                    value={{ $agreement->agreement_payment->id }}>
                                            @endisset
                                            {{-- Edit case --}}
                                            <!-- your steps content here -->
                                            <div id="tenant-step" class="content step-content" role="tabpanel"
                                                aria-labelledby="tenant-step-trigger" data-ste="0">
                                                <div class="form-group row">
                                                    <div class="col-md-4">
                                                        <label for="exampleInputEmail1 " class="asterisk">Company</label>
                                                        <select class="form-control select2" name="company_id"
                                                            id="company_id" required>
                                                            <option value="">Select Company</option>
                                                            {{-- @foreach ($companies as $company)
                                                                <option value="{{ $company->id }}">
                                                                    {{ $company->company_name }}
                                                                </option>
                                                            @endforeach --}}
                                                            @foreach ($companies as $company)
                                                                {{-- <option value="{{ $company->id }}"
                                                                    {{ isset($agreement) && $agreement->company_id == $company->id ? 'selected' : '' }}>
                                                                    {{ $company->company_name }}
                                                                </option> --}}
                                                                <option value="{{ $company->id }}"
                                                                    @if (
                                                                        (isset($agreement) && $agreement->company_id == $company->id) ||
                                                                            (isset($company_id) && $company_id == $company->id)) selected @endif>
                                                                    {{ $company->company_name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        {{-- Edit case --}}
                                                        @isset($agreememnt)
                                                            <input type="hidden" name="company_id" id="edited_company"
                                                                value={{ $agreement->company_id }}>
                                                        @endisset
                                                        {{-- Edit case --}}

                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="asterisk">Contract</label>
                                                        <select class="form-control select2" name="contract_id"
                                                            id="contract_id" required>
                                                            {{-- <option value="">Select Project</option>
                                                        <option value="1">Project 1</option> --}}
                                                        </select>
                                                    </div>
                                                    {{--  Edit case --}}
                                                    @isset($agreememnt)
                                                        <input type="hidden" name="contract_id" id="edited_contract"
                                                            value="">
                                                    @endisset
                                                    {{-- Edit case --}}
                                                    <input type="hidden" name="contract_type" id="selctedcontractType">


                                                    <div class="col-md-4">
                                                        <label for="exampleInputEmail1 " class="asterisk">Tenant
                                                            Name</label>
                                                        <input type="text" class="form-control" id="tenant_name"
                                                            name="tenant_name" placeholder="Tenant Name"
                                                            value="{{ old('tenant_name', $tenant->tenant_name ?? '') }}"
                                                            required>
                                                    </div>
                                                </div>
                                                <div class="form-group row">


                                                    <div class="col-md-4">
                                                        <label for="exampleInputEmail1 " class="asterisk">Tenant mobile
                                                            <small class="text-muted font-weight-lighter">(e.g.,
                                                                +971501234567
                                                                or 971501234567)</small></label>
                                                        <input type="text" class="form-control" id="tenant_mobile"
                                                            name="tenant_mobile" placeholder="Tenant mobile"
                                                            value="{{ old('tenant_mobile', $tenant->tenant_mobile ?? '') }}"
                                                            required pattern="^\+?[1-9]\d{9,14}$">
                                                        <div class="invalid-feedback">
                                                            Enter valid mobile with country code.
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="exampleInputEmail1 " class="asterisk">Tenant
                                                            email</label>
                                                        <input type="email" class="form-control" id="tenant_email"
                                                            name="tenant_email" placeholder="Tenant email"
                                                            value="{{ old('tenant_email', $tenant->tenant_email ?? '') }}"
                                                            required pattern="^[^\s@]+@[^\s@]+\.[^\s@]+$">
                                                        <div class="invalid-feedback">
                                                            Please provide a valid email.
                                                        </div>
                                                    </div>
                                                    {{-- @dump($tenant->nationality_id); --}}
                                                    <div class="col-md-4">
                                                        <label for="exampleInputEmail1"
                                                            class="asterisk">Nationality</label>
                                                        <select class="form-control select2" name="nationality_id"
                                                            id="nationality_id" required>
                                                            <option value="">Select Nationality</option>
                                                            @foreach ($nationalities as $nationality)
                                                                <option
                                                                    value="{{ $nationality->id }}"{{ (isset($agreement) || isset($tenant)) && $tenant->nationality_id == $nationality->id ? 'selected' : '' }}>
                                                                    {{ $nationality->nationality_name }} </option>
                                                            @endforeach

                                                        </select>

                                                    </div>

                                                </div>
                                                <div class="form-group row">


                                                    <div class="col-md-4">
                                                        <label for="exampleInputEmail1 " class="asterisk">Contact
                                                            person</label>
                                                        <input type="text" class="form-control" id="contact_person"
                                                            name="contact_person" placeholder="Contact Person"
                                                            value="{{ old('contact_person', $tenant->contact_person ?? '') }}"
                                                            required>

                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="exampleInputEmail1 " class="asterisk">Contact
                                                            email</label>
                                                        <input type="email" class="form-control " id="contact_email"
                                                            name="contact_email" placeholder="Contact email"
                                                            value="{{ old('contact_email', $tenant->contact_email ?? '') }}"
                                                            required pattern="^[^\s@]+@[^\s@]+\.[^\s@]+$">
                                                        <div class="invalid-feedback">
                                                            Please provide a valid email.
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="exampleInputEmail1 " class="asterisk">Contact Number
                                                            <small class="text-muted font-weight-lighter">(e.g.,
                                                                +971501234567
                                                                or 971501234567)</small> </label>
                                                        <input type="text" class="form-control" id="contact_number"
                                                            name="contact_number" placeholder="Contact number"
                                                            value="{{ old('contact_number', $tenant->contact_number ?? '') }}"
                                                            required pattern="^\+?[1-9]\d{9,14}$">
                                                        <div class="invalid-feedback">
                                                            Enter valid mobile with country code.
                                                        </div>
                                                    </div>

                                                </div>
                                                <div class="form-group row">


                                                    <div class="col-md-6">
                                                        <label class="asterisk">Address Line 1<small
                                                                class="text-muted font-weight-lighter">(Flat
                                                                No,Building)</small></label>
                                                        <input type="text" class="form-control" id="tenant_address"
                                                            name="tenant_address" placeholder="Flat No,Building etc"
                                                            value="{{ old('tenant_address', $tenant->tenant_address ?? '') }}"
                                                            required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="">Address Line 2<small
                                                                class="text-muted font-weight-lighter">(Street)</small></label>
                                                        <input type="text" class="form-control" id="tenant_street"
                                                            name="tenant_street" placeholder="Street"
                                                            value="{{ old('tenant_street', $tenant->tenant_street ?? '') }}">
                                                    </div>


                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-md-4">
                                                        <label for="exampleInputEmail1 ">City</label>
                                                        <input type="text" class="form-control" id="tenant_city"
                                                            name="tenant_city" placeholder="Enter City"
                                                            value="{{ old('tenant_city', $tenant->tenant_city ?? '') }}">

                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="exampleInputEmail1">Emirate</label>
                                                        <select class="form-control select2" name="emirate_id"
                                                            id="emirate_id">
                                                            <option value="">Select Emirate</option>
                                                            @foreach ($emirates as $emirate)
                                                                <option
                                                                    value="{{ $emirate->id }}"{{ (isset($agreement) || isset($tenant)) && $tenant->emirate_id == $emirate->id ? 'selected' : '' }}>
                                                                    {{ $emirate->name }} </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                </div>

                                                <button type="button" class="btn btn-info nextBtn">Next</button>
                                            </div>
                                            <div id="document-step" class="content step-content" role="tabpanel"
                                                aria-labelledby="document-step-trigger" data-step="1">
                                                <div class="form-group p-3">
                                                    @foreach ($tenantIdentities as $index => $identity)
                                                        <h6 class="font-weight-bold text-cyan mb-3">
                                                            {{ $identity->identity_type }}
                                                        </h6>

                                                        @php
                                                            // Find matching document (if exists)
                                                            $document = isset($agreement)
                                                                ? $agreement->agreement_documents->firstWhere(
                                                                    'document_type',
                                                                    $identity->id,
                                                                )
                                                                : null;
                                                        @endphp
                                                        {{-- {{ dd($document) }} --}}

                                                        <div class="form-row">
                                                            {{-- First Field --}}
                                                            <div class="form-group col-md-6">
                                                                <label for="document_number_{{ $index }}">
                                                                    {{ $identity->first_field_label }}
                                                                </label>
                                                                <input type="{{ $identity->first_field_type }}"
                                                                    name="documents[{{ $index }}][document_number]"
                                                                    id="document_number_{{ $index }}"
                                                                    value="{{ $document ? $document->document_number : '' }}"
                                                                    class="form-control"
                                                                    placeholder="{{ $identity->first_field_label }}">
                                                                <input type="hidden"
                                                                    name="documents[{{ $index }}][document_type]"
                                                                    value="{{ $identity->id }}">
                                                            </div>

                                                            {{-- Second Field --}}
                                                            <div class="form-group col-md-6">
                                                                <label for="document_path_{{ $index }}">
                                                                    {{ $identity->second_field_label }}
                                                                </label>
                                                                <input type="{{ $identity->second_field_type }}"
                                                                    name="documents[{{ $index }}][document_path]"
                                                                    id="document_path_{{ $index }}"
                                                                    class="form-control"
                                                                    @if ($identity->second_field_type == 'file') accept="image/*,.pdf" @endif
                                                                    placeholder="{{ $identity->second_field_label }}">
                                                                @if ($document && $document->original_document_path)
                                                                    <input type="hidden"
                                                                        name="documents[{{ $index }}][id]"
                                                                        value="{{ $document->id }}">
                                                                    <div class="mt-2">
                                                                        @php
                                                                            $filePath = asset(
                                                                                'storage/' .
                                                                                    $document->original_document_path,
                                                                            );
                                                                            $isPdf = \Illuminate\Support\Str::endsWith(
                                                                                strtolower(
                                                                                    $document->original_document_path,
                                                                                ),
                                                                                '.pdf',
                                                                            );
                                                                        @endphp
                                                                        @if ($document->document_type != 3)
                                                                            @if ($isPdf)
                                                                                <a href="{{ $filePath }}"
                                                                                    target="_blank"
                                                                                    class="btn btn-outline-primary btn-sm">
                                                                                    <i class="fas fa-file-pdf"></i> View
                                                                                    PDF
                                                                                </a>
                                                                            @else
                                                                                <a href="{{ $filePath }}"
                                                                                    target="_blank">
                                                                                    <img src="{{ $filePath }}"
                                                                                        class="documentpreview"
                                                                                        alt="Document">
                                                                                </a>
                                                                            @endif
                                                                        @endif


                                                                        <p class="small text-muted mt-1">
                                                                            {{ $document->original_document_name }}</p>


                                                                    </div>
                                                                @endif
                                                            </div>
                                                            {{-- Existing File Preview --}}

                                                        </div>
                                                    @endforeach
                                                </div>

                                                <button class="btn btn-info prevBtn" type="button">Previous</button>
                                                <button type="button" class="btn btn-info nextBtn">Next</button>
                                            </div>
                                            <div id="unit-step" class="content step-content" role="tabpanel"
                                                aria-labelledby="unit-step-trigger" data-step="3">
                                                {{-- {{ dd($agreement) }} --}}
                                                @if (isset($agreement) && isset($agreement->agreement_units) && $agreement->contract->contract_type_id == 1)
                                                    @include('admin.projects.agreement.edit_unit', [
                                                        'agreement_units' => $agreement->agreement_units ?? [],
                                                        'contract_id' => $agreement->contract_id,
                                                        'businessType' =>
                                                            $agreement->contract->contract_unit->business_type,
                                                        'count' =>
                                                            $agreement->agreement_payment->installment->installment_name,
                                                    ])
                                                @else
                                                    <div id="unit_details_container">
                                                        <div id="unit_details_div_df">

                                                            <!-- Add More Button at the Top -->
                                                            <div class="d-flex  mb-3">
                                                                <button type="button" class="btn btn-success"
                                                                    id="add_more_unit">
                                                                    + Add More Unit
                                                                </button>
                                                            </div>

                                                            <!-- First Unit Card -->
                                                            <div class="card mb-3 p-3 unit-row">
                                                                <div class="card-body">

                                                                    <div class="row g-3 align-items-end">

                                                                        <div class="col-sm-3">
                                                                            <label class="form-label asterisk">Unit
                                                                                Type</label>
                                                                            <input type="hidden"
                                                                                name="unit_detail[0][unit_id]"
                                                                                value="">
                                                                            <select class="form-control  unit_type_id"
                                                                                name="unit_detail[0][unit_type_id]"
                                                                                required></select>
                                                                        </div>

                                                                        <div class="col-sm-3">
                                                                            <label class="form-label asterisk">Select Unit
                                                                                No</label>
                                                                            <select class="form-control  unit_type0"
                                                                                name="unit_detail[0][contract_unit_details_id]"
                                                                                required></select>
                                                                        </div>

                                                                        <div class="col-sm-3 subunit_number_div">
                                                                            <label class="form-label ">Sub
                                                                                Unit</label>
                                                                            <select class="form-control sub_unit_type"
                                                                                name="unit_detail[0][contract_subunit_details_id]"></select>
                                                                        </div>

                                                                        <div class="col-sm-3">
                                                                            <label class="form-label asterisk">Rent per
                                                                                Month</label>
                                                                            <input type="text"
                                                                                class="form-control rent_per_month"
                                                                                name="unit_detail[0][rent_per_month]"
                                                                                placeholder="Rent per month">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <!-- Container for dynamically added unit rows -->
                                                            <div id="additional_unit_rows"></div>
                                                        </div>


                                                    </div>
                                                @endif
                                                <div class="form-group  d-none" id="unit_details_div_ff">
                                                    <div class="card mt-3" id="unit_summary_div">
                                                        <div class="card-body">
                                                            <h5 class="mb-3"><i class="fas fa-door-open text-info"></i>
                                                                Unit Details</h5>
                                                            <div class="row">
                                                                <div class="col-md-3">
                                                                    <strong>Unit Type:</strong>
                                                                    <p id="unit_type_display" class="text-muted mb-0">-
                                                                    </p>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <strong>Unit Number:</strong>
                                                                    <p id="unit_number_display" class="text-muted mb-0">-
                                                                    </p>
                                                                </div>

                                                                <div class="col-md-3">
                                                                    <strong>Total Subunits:</strong>
                                                                    <p id="total_subunits_display"
                                                                        class="text-muted mb-0">-</p>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <strong>Rent per Annum:</strong>
                                                                    <p id="rent_per_annum_display"
                                                                        class="text-muted mb-0">-</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>

                                                <button class="btn btn-info prevBtn" type="button">Previous</button>
                                                <button class="btn btn-info nextBtn" type="button">Next</button>
                                            </div>
                                            <div id="agreement-step" class="content step-content" role="tabpanel"
                                                aria-labelledby="agreement-step-trigger" data-step="2">
                                                @if (isset($agreement))
                                                    @include(
                                                        'admin.projects.agreement.edit-start_and_end',
                                                        [
                                                            'agreement' => $agreement ?? [],
                                                        ]
                                                    )
                                                @else
                                                    <div class="form-group row">
                                                        <div class="col-md-4">
                                                            <label for="exampleInputEmail1" class="asterisk">Start
                                                                Date</label>
                                                            <div class="input-group date" id="startdate"
                                                                data-target-input="nearest">
                                                                <input type="text"
                                                                    class="form-control datetimepicker-input startdate"
                                                                    name="start_date" id="start_date"
                                                                    data-target="#startdate" placeholder="dd-mm-YYYY" />
                                                                <div class="input-group-append" data-target="#startdate"
                                                                    data-toggle="datetimepicker">
                                                                    <div class="input-group-text">
                                                                        <i class="fa fa-calendar"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label for="exampleInputEmail1" class="asterisk">Duration in
                                                                Months</label>
                                                            <input type="number" class="form-control"
                                                                id="duration_months" name="duration_in_months"
                                                                placeholder="Duration in Months" value="">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label for="exampleInputEmail1" class="asterisk">End
                                                                Date</label>
                                                            <div class="input-group date" id="enddate"
                                                                data-target-input="nearest">
                                                                <input type="text"
                                                                    class="form-control datetimepicker-input enddate"
                                                                    id="end_date" name="end_date" data-target="#enddate"
                                                                    placeholder="dd-mm-YYYY" readonly />
                                                                <div class="input-group-append" data-target="#enddate"
                                                                    data-toggle="datetimepicker">
                                                                    <div class="input-group-text"><i
                                                                            class="fa fa-calendar"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                                <button class="btn btn-info prevBtn" type="button">Previous</button>
                                                <button class="btn btn-info nextBtn" type="button">Next</button>
                                            </div>

                                            <div id="payment-step" class="content step-content" role="tabpanel"
                                                aria-labelledby="payment-step-trigger" data-step='4'>
                                                <div class="form-group row">
                                                    <div class="col-md-3">
                                                        <label for="exampleInputEmail1" class="asterisk">No. of
                                                            Installments</label>
                                                        <select class="form-control select2" name="installment_id"
                                                            id="no_of_installments">
                                                            <option value="">Select</option>
                                                            @foreach ($installments as $installment)
                                                                <option value="{{ $installment->id }}"
                                                                    data-interval="{{ $installment->interval }}">
                                                                    {{ $installment->installment_name }}</option>
                                                            @endforeach

                                                        </select>
                                                        {{-- <input type="text" name="installment_id" class="form-control"
                                                            id="no_of_installments"> --}}
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label for="exampleInputEmail1" class="asterisk">Interval</label>
                                                        <input type="text" class="form-control" id="interval"
                                                            name="interval" placeholder="Interval">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label for="exampleInputEmail1"
                                                            class="asterisk">Beneficiary</label>
                                                        <input type="text" class="form-control" id="beneficiary"
                                                            name="beneficiary" placeholder="Beneficiary">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label for="exampleInputEmail1" class="asterisk">Total Rent Per
                                                            Annum</label>
                                                        <input type="text" class="form-control" id="total_rent_annum"
                                                            name="total_rent_per_annum" placeholder=""
                                                            value="{{ isset($agreement) ? $agreement->agreement_payment->total_rent_annum : '' }}"
                                                            readonly>
                                                    </div>
                                                </div>
                                                <hr>
                                                <div class="payment_details">
                                                    <div
                                                        class="form-group row font-weight-bold text-secondary mb-2 justify-content-end header-row d-none">
                                                        <div class="col-auto text-end">
                                                            <label class="me-2">Total Rent per Annum:</label>
                                                            <span id="total_rent_per_annum"
                                                                class="text-info font-weight-bold">
                                                                {{ isset($agreement) ? $agreement->agreement_payment->total_rent_annum : 0 }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div id="paymentError"
                                                        class="text-danger font-weight-bold mb-2 d-none"></div>
                                                </div>

                                                <button class="btn btn-info prevBtn" type="button">Previous</button>
                                                <button type="submit" class="btn btn-info agreementFormSubmit"
                                                    id="submitBtn">Submit</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.card -->
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </div>
            <!-- /.container-fluid -->

        </section>
        <!-- /.content -->
    </div>
@endsection
@section('custom_js')
    <!-- Select2 -->

    <script src="{{ asset('assets/select2/js/select2.full.min.js') }}"></script>

    <script src="{{ asset('assets/moment/moment.min.js') }}"></script>
    <!-- Tempusdominus Bootstrap 4 -->

    <script src="{{ asset('assets/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>
    <!-- date-range-picker -->

    <script src="{{ asset('assets/daterangepicker/daterangepicker.js') }}"></script>

    <!-- BS-Stepper -->

    <script src="{{ asset('assets/bs-stepper/js/bs-stepper.min.js') }}"></script>

    @include('admin.projects.agreement.stepper-validation-js')
    @include('admin.projects.agreement.form-submit')
    @include('admin.projects.agreement.edit-agreement')


    <script>
        $('#startdate').datetimepicker({
            format: 'DD-MM-YYYY'
        });

        $('#enddate').datetimepicker({
            format: 'DD-MM-YYYY'
        });

        $('#terminationdate').datetimepicker({
            format: 'DD-MM-YYYY'
        });

        // BS-Stepper Init
        document.addEventListener('DOMContentLoaded', function() {
            window.stepper = new Stepper(document.querySelector('.bs-stepper'))
        })
    </script>

    <!-- end date calc from start date -->
    <script>
        // $('.startdate, #duration_months, #duration_days').on('input change', function() {
        //     calculateEndDate();
        // });

        function calculateEndDate() {
            var startDateVal = $('#startdate').find("input").val();
            var durationMonths = parseInt($('#duration_months').val()) || 0;
            var durationDays = parseInt($('#duration_days').val()) || 0;

            const startDate = parseDateCustom(startDateVal);

            if (!startDate || isNaN(startDate.getTime())) {
                $('.enddate').val('');
                return;
            }

            // Add months
            startDate.setMonth(startDate.getMonth() + durationMonths);

            // Add days
            startDate.setDate(startDate.getDate() + durationDays - 1);

            // Format as YYYY-MM-DD
            const year = startDate.getFullYear();
            const month = String(startDate.getMonth() + 1).padStart(2, '0');
            const day = String(startDate.getDate()).padStart(2, '0');

            const formattedDate = `${day}-${month}-${year}`;

            $('.enddate').val(formattedDate);
        }

        // modifying the date format to Y-m-d
        function parseDateCustom(dateStr) {
            if (!dateStr) return null;
            const parts = dateStr.split('-');

            if (parts.length !== 3) return null;
            const day = parseInt(parts[0], 10);
            const month = parseInt(parts[1], 10) - 1;
            const year = parseInt(parts[2], 10);

            formattedDate = `${year}-${month}-${day}`;

            return new Date(year, month, day);
        }
    </script>
    <!-- end date calc from start date -->


    <!-- payment multiple -->
    <script>
        $(document).ready(function() {
            // $('.payment_details').hide();


        });
    </script>

    <!-- payment mode scripts -->
    <script>
        $(document).ready(function() {
            hidePayments();
            CompanyChange();
            // $('.subrnt0').hide();
        });

        function hidePayments() {
            // alert("paymenthidecalled");
            $('.bank').hide();
            $('.chq').hide();
            $('.chqot').hide();
            $('.part0').hide();
            $('.bs0').hide();
            $('.chqiss').hide();
            $('.chqotiss').hide();
        }

        $('#no_of_installments, #interval').on('input change', function() {
            // alert("secondcall");
            calculatePaymentDates();
        });


        let updatingDates = false;

        function calculatePaymentDates() {
            if (updatingDates) return; // prevent recursive calls
            updatingDates = true;

            var startDateVal = $('#otherPaymentDate0').find("input").val();
            var noOfInstallments = parseInt($('#no_of_installments').find('option:selected').text().trim()) || 0;
            var interval = parseInt($('#interval').val()) || 0;

            const startDate = parseDateCustom(startDateVal);

            for (let i = 1; i < noOfInstallments; i++) {

                if (!startDate || isNaN(startDate.getTime())) {
                    $('#payment_date' + i).val('');
                    continue;
                }

                startDate.setMonth(startDate.getMonth() + interval);

                const year = startDate.getFullYear();
                const month = String(startDate.getMonth() + 1).padStart(2, '0');
                const day = String(startDate.getDate()).padStart(2, '0');

                const formattedDate = `${day}-${month}-${year}`;

                $('#otherPaymentDate' + i).datetimepicker('date', moment(formattedDate, 'DD-MM-YYYY'));
            }

            updatingDates = false;
        }


        function calculatepaymentamount(rent_per_month = 0, payment_count = 0) {
            // clearing the div
            const errorDiv = $('#paymentError');
            errorDiv.html('');
            errorDiv.addClass('d-none').removeClass('d-flex');
            $('#submitBtn').prop('disabled', false);

            var rentmonth = rent_per_month || 0;

            for (let i = 0; i < payment_count; i++) {
                $('#payment_amount_' + i).val((rentmonth));
            }
            let total_rent_per_annum = rentmonth * payment_count;
            $('#total_rent_per_annum').text(total_rent_per_annum);
            $('#total_rent_annum').val(total_rent_per_annum);
        }

        function paymentModeChange(i) {
            // alert("modechange");

            var payment_mode = $('#payment_mode' + i).val();

            if (payment_mode == '3') { // Cheque
                // alert('hicheque');
                $('#chq' + i).show().find('input, select').prop('disabled', false).prop('required', true);
                $('#bank' + i).show().find('input, select').prop('disabled', false).prop('required', true);
                $('#chqot' + i).hide().find('input, select').prop('disabled', true);
                $('#chqot' + i + ', #chqotiss' + i + ', #chqiss' + i)
                    .hide()
                    .find('input, select')
                    .prop('disabled', true);
                addClassAsterisk('#bank' + i);
                addClassAsterisk('#chq' + i);
            } else if (payment_mode == '2') { // Bank Transfer
                $('#bank' + i).show().find('input, select').prop('disabled', false).prop('required', true);
                addClassAsterisk('#bank' + i);
                $('#chq' + i).hide().find('input, select').prop('disabled', true);
                $('#chqot' + i).hide().find('input, select').prop('disabled', true);
                $('#chqot' + i + ', #chqotiss' + i + ', #chqiss' + i)
                    .hide()
                    .find('input, select')
                    .prop('disabled', true);
            } else { // Cash or others
                $('#bank' + i).hide().find('input, select').prop('disabled', true).prop('required', false);
                $('#chq' + i).hide().find('input, select').prop('disabled', true).prop('required', false);
                $('#chqot' + i).hide().find('input, select').prop('disabled', true);
                $('#chqot' + i + ', #chqotiss' + i + ', #chqiss' + i)
                    .hide()
                    .find('input, select')
                    .prop('disabled', true);
            }
        }


        function checkIssView(i) {
            var cheque_issuer = $('#cheque_issuer' + i).val();
            if (cheque_issuer == 'other') {
                $('#chqot' + i).show();
                $('#chqotiss' + i).show();
            } else {
                $('#chqot' + i).hide();
                $('#chqotiss' + i).hide();
            }
        }
    </script>
    <!-- payment mode scripts -->

    {{-- companywise contractwise units and subunits --}}

    <script>
        let allContracts = @json($contracts);
        let allunittypes = @json($unitTypes);
        let editedContract = @json($agreement->contract ?? null);
        let editedUnit = @json($agreement->agreement_units ?? null);

        let fullContracts = @json($fullContracts ?? []);
        window.allBanks = @json($banks);
        // console.log(fullContracts);

        // let editedUnit = window.editedUnit || [];
        //console.log("edited Unit :" + JSON.stringify(editedUnit))

        $(document).on('change', '#company_id', function() {
            CompanyChange();
        });

        function CompanyChange(contractId = null) {
            const companyId = $('#company_id').val();
            let renewalContractId = "{{ $renewalContractId ?? '' }}";
            // console.log('test', renewalContractId);

            if (renewalContractId) {
                contractId = renewalContractId;
            }
            if (editedUnit) {
                $('#company_id').on('select2:opening', function(e) {
                    e.preventDefault();
                });
            }

            //console.log("ids3" + companyId, contractId);
            let beneficiary = $('#company_id option:selected').text().trim();
            $('#beneficiary').val(beneficiary).prop('readonly', true);
            let options = '<option value="">Select Contract</option>';
            //console.log("Edited Contract:", JSON.stringify(editedContract));


            // Edit
            if (editedContract && editedContract.company_id == companyId) {
                options += `
            <option value="${editedContract.id}" selected>
                ${editedContract.project_code} - ${editedContract.project_number}
            </option>`;
                $('#edited_contract').val(editedContract.id);
            }
            // Edit

            allContracts
                .filter(c => c.company_id == companyId)
                .forEach(c => {
                    options +=
                        `<option value="${c.id}" ${(c.id == contractId) ? 'selected' : ''}>${c.project_code} - ${c.project_number}</option>`;
                });

            $('#contract_id').html(options).trigger('change');


        }

        $(document).on('change', '#contract_id', function() {

            contractChange();

        });
        let defaultStart = null;
        let defaultEnd = null;
        let defaultDuration = 0;


        function contractChange() {
            const contractId = $('#contract_id').val();

            // alert("called");
            if (editedUnit) {
                // $(this).prop('readonly', true);
                $('#contract_id').on('select2:opening', function(e) {
                    e.preventDefault();
                });

            } else {
                removeTenant();
            }


            const errorDiv = $('#paymentError');
            errorDiv.html('');
            errorDiv.addClass('d-none').removeClass('d-flex');
            $('#submitBtn').prop('disabled', false);
            $("#additional_unit_rows").empty().removeClass("d-block").addClass("d-none");

            let options = '<option value="">Select Unit Type</option>';
            let contract = null;
            if (editedUnit) {
                contract = fullContracts.find(c => c.id == contractId);

            }
            if (!contract) {
                contract = allContracts.find(c => c.id == contractId);

            }
            // console.log("contract", contract);
            selectedContract = contract;
            if (contract?.contract_unit?.business_type == 1) {
                $('#add_more_unit').removeClass('d-none').addClass('d-flex');
            } else {
                $('#add_more_unit').removeClass('d-flex').addClass('d-none');
            }

            $('#selctedcontractType').val(contract?.contract_type_id || 0);
            let receivable_sum = contract?.contract_payment_receivables_sum_receivable_amount || 0;
            let payment_count = contract?.contract_payment_receivables_count || 0;
            let number_of_units = contract?.contract_unit?.no_of_units;
            let monthrent = parseFloat(receivable_sum) / (payment_count * number_of_units);


            if (!contract || !contract.contract_unit || !contract.contract_unit.contract_unit_details) {
                $('#unit_type_id').html(options).trigger('change');
                return;
            }
            let unitTypeIds = contract.contract_unit.contract_unit_details
                .filter(d => d.is_vacant == 0)
                .map(d => d.unit_type_id);
            unitTypeIds = [...new Set(unitTypeIds)];
            // console.log('Unit Type Ids:', unitTypeIds);


            let selectedUnitIds = [];

            if (editedUnit && Array.isArray(editedUnit) && editedUnit.length > 0) {
                // alert('edited unit found');
                selectedUnitIds = editedUnit.map(u => u.unit_type_id);
                selectedUnitIds.forEach(id => {
                    if (!unitTypeIds.includes(id)) {
                        unitTypeIds.push(id);
                    }
                });
            }
            // console.log('Selected Unit Ids:', selectedUnitIds);


            allunittypes
                .filter(ut => unitTypeIds.includes(ut.id))
                .forEach(ut => {
                    // const isSelected = selectedUnitIds.includes(ut.id) ? 'selected' : '';
                    options += `<option value="${ut.id}">${ut.unit_type}</option>`;
                });


            $('.unit_type_id').html(options);
            const selectedUnitTypeId = selectedUnitIds.length ? selectedUnitIds[0] : '';
            // console.log('Selected Unit Type ID:', selectedUnitTypeId);
            $('.unit_type_id')
                .val(selectedUnitIds.length ? selectedUnitIds[0] : '')
                .trigger('change.select2');
            let agreement = @json($agreement ?? null);


            let contractTypes = @json($contractTypes);
            let selectedContractType = contractTypes.find(ct => ct.id === contract.contract_type_id);
            if ((selectedContractType && selectedContractType.contract_type === 'Direct Fama')) {
                unitTypeChange(selectedUnitTypeId, editedUnit, row = null);
                unitDetails = contract?.contract_unit?.contract_unit_details || [];
                unit_details = unitDetails;
            }
            if (
                (selectedContractType && selectedContractType.contract_type === 'Fama Faateh')
            ) {
                fillFFTenant();
                let uniDetails = [];
                // console.log('Agreemant :', agreement);
                if (editedUnit && Array.isArray(editedUnit) && editedUnit.length > 0) {
                    unitDetails = editedUnit
                        .filter(u => u.contract_unit_detail)
                        .map(u => u.contract_unit_detail);
                } else {
                    // alert('ff');
                    unitDetails = contract?.contract_unit?.contract_unit_details || [];
                    // console.log('unit', unitDetails);

                }
                unit_details = unitDetails;
                let html = `
                                <div class="card-body">
                                    <h5 class="mb-3">
                                        <i class="fas fa-door-open text-info"></i> Unit Details - ${selectedContractType.contract_type}
                                    </h5>
                            `;
                // console.log('unitdetails', unitDetails);
                // console.log("editedUnit", editedUnit);

                unitDetails.forEach((u, index) => {
                    const type = allunittypes?.find(t => t.id == u.unit_type_id)?.unit_type || 'Unknown';
                    const subunitCount = u.subunitcount_per_unit || 0;
                    const rent = parseFloat(u.total_rent_per_unit_per_month);


                    html += `
                                <div class=" mb-3 ">
                                            <div class="row unit-row" data-row-index=${index}>


                                                <div class="col-md-3">
                                                    <label class="form-label">Unit Number</label>
                                                    <select class="form-control select2 unit_type0  unit_no_select" name="" id="unit_number_${index}" disabled>
                                                        <option value="${u.id}">${u.unit_number || ''}</option>
                                                    </select>
                                                    <input type="hidden" name="unit_detail[${index}][contract_unit_details_id]" value="${u.id}">
                                                </div>

                                                <!-- Unit Type -->
                                                <div class="col-md-3">
                                                    <label class="form-label">Unit Type</label>
                                                    <select class="form-control select2 " name="unit_detail[${index}][unit_type_id]" id="unit_type_${index}" disabled>
                                                        <option value="${u.unit_type_id}">${type}</option>
                                                    </select>
                                                    <input type="hidden" name="unit_detail[${index}][unit_type_id]" value="${u.unit_type_id}">

                                                </div>

                                                <div class="col-md-3">
                                                    <label class="form-label">Total Subunits</label>
                                                    <input type="text" class="form-control" name="total_subunits_${index}"
                                                        value="${subunitCount}" readonly>
                                                </div>

                                                <div class="col-md-3">
                                                    <label class="form-label">Rent per month</label>
                                                    <input type="text" class="form-control" name="unit_detail[${index}][rent_per_month]"
                                                        value="${rent.toFixed(2)}" readonly>
                                                </div>
                                            </div>
                                        </div>
                            `;
                });

                html += `</div>`;
                $('#unit_details_div_ff')
                    .removeClass('d-none')
                    .html(html);

                $('#unit_details_div_df').addClass('d-none');



            } else if (selectedContractType && selectedContractType.contract_type === 'Direct Fama') {
                if (agreement && agreement.agreement_units && agreement.agreement_units.length > 0) {
                    // console.log('Agreemant :', agreement);

                    const agreementUnitId = agreement.agreement_units[0].id;

                    $('#unit_id').val(agreementUnitId);
                }
                $('#unit_details_div_ff').addClass('d-none');
                if (contract?.contract_unit
                    ?.business_type == 1) {
                    // alert('disabling subunit');
                    $('.subunit_number_div select').prop('disabled', true);
                } else {
                    $('.subunit_number_div select').prop('disabled', false);
                }
                $('#unit_details_div_df').removeClass('d-none');

            }



            const contract_start = contract?.contract_detail?.start_date ?? '';
            const contract_end = contract?.contract_detail?.end_date ?? '';
            const ct_duration_months = contract?.contract_detail?.duration_in_months ?? 0;
            if (!agreement) {
                if (contract_start) {
                    const startDateObj = parseDateCustom(contract_start);
                    const formattedStart =
                        `${String(startDateObj.getDate()).padStart(2, '0')}-${String(startDateObj.getMonth() + 1).padStart(2, '0')}-${startDateObj.getFullYear()}`;
                    $('#start_date').val(formattedStart).prop('readonly', true);
                } else {
                    $('#start_date').val('');
                }
                if (contract_end) {
                    const endDateObj = parseDateCustom(contract_end);
                    const formattedEnd =
                        `${String(endDateObj.getDate()).padStart(2, '0')}-${String(endDateObj.getMonth() + 1).padStart(2, '0')}-${endDateObj.getFullYear()}`;
                    $('#end_date').val(formattedEnd).prop('readonly', true);
                } else {
                    $('#end_date').val('');
                }
                $('#duration_months').val(ct_duration_months).prop('readonly', true);
                defaultStart = moment($('#start_date').val(), "DD-MM-YYYY");
                defaultEnd = moment($('#end_date').val(), "DD-MM-YYYY");
                defaultDuration = $("#duration_months").val();
                // console.log('Default values set:', defaultStart, defaultEnd, defaultDuration);
            }


            defaultStart = moment($('#start_date').val(), "DD-MM-YYYY");
            defaultEnd = moment($('#end_date').val(), "DD-MM-YYYY");
            defaultDuration = $("#duration_months").val();

            // 15/01/2026
            $('#start_date').prop('readonly', true);
            $('#end_date').prop('readonly', true);


            // console.log('Default values set:', defaultStart, defaultEnd, defaultDuration);




            payment_count = contract?.contract_payment_receivables_count ?? 0;
            if (agreement && agreement.agreement_payment) {
                const installmentId = agreement.agreement_payment.installment_id;
                $('#no_of_installments option').each(function() {
                    const optionValue = $(this).val();
                    if (parseInt(optionValue) === parseInt(installmentId)) {
                        $(this).prop('selected', true);
                        $(this).next('.select2-container')
                            .find('.select2-selection')
                            .addClass('readonly');

                        $('#no_of_installments').trigger('change');
                    }
                });
            } else {
                $('#no_of_installments option').each(function() {
                    const optionText = $(this).text().trim();
                    const countText = payment_count.toString().trim();
                    if (optionText === countText) {
                        $(this).prop('selected', true);
                        $('#no_of_installments').trigger('change');
                    }
                });
            }
            // console.log('contracttype', contract.contract_type_id);
            // console.log('business_type', contract.contract_unit.business_type);
            // console.log("contrat", contract);
            if (contract.contract_type_id === 2 ||
                (contract.contract_type_id === 1 && contract.contract_unit.business_type === 1)) {
                // alert("test");
                checkTerminatedAgreement(contractId);
            }



        }



        $(document).on('change', '.unit_type_id', function() {
            const unitTypeId = $(this).val();
            const row = $(this).closest('.unit-row');
            unitTypeChange(unitTypeId, editedUnit, row);
        });

        function unitTypeChange(unitTypeId, editedUnit, row) {
            let options = '<option value="">Select Unit No</option>';
            let selectedUnitnumbers = [];
            let contractId = $('#contract_id').val();
            // console.log("contracts:", allContracts);
            let contract = allContracts.find(c => c.id == contractId);

            // if (editedUnit && Array.isArray(editedUnit) && editedUnit.length > 0) {
            //     contract = fullContracts.find(c => c.id == contractId);
            //     console.log("acrualcontracts :", contract)
            //     console.log("editedUnit :", editedUnit)

            //     selectedUnitnumbers = editedUnit.map(u => u.contract_unit_details_id);

            //     editedUnit.forEach(u => {
            //         if (!unitTypeId || u.unit_type_id == unitTypeId) {
            //             const unitNumber = u.contract_unit_detail?.unit_number || '';
            //             options += `<option value="${u.contract_unit_details_id}" selected>${unitNumber}</option>`;
            //         }
            //     });

            //     let filteredUnits = contract.contract_unit.contract_unit_details
            //         .filter(u => u.is_vacant == 0);
            //     console.log("filetred", filteredUnits);
            //     if (unitTypeId) {
            //         filteredUnits = filteredUnits.filter(d => d.unit_type_id == unitTypeId);
            //     }

            //     filteredUnits.forEach(ut => {
            //         if (!selectedUnitnumbers.includes(ut.id)) {
            //             options += `<option value="${ut.id}">${ut.unit_number}</option>`;
            //         }
            //     });
            //     if (row) {
            //         row.find('.unit_type0').html(options)
            //             .val(selectedUnitnumbers.length ? selectedUnitnumbers[0] : '')
            //             .trigger('change.select2');
            //     }
            //     updateUnitOptions();

            //     unitNumberChange(selectedUnitnumbers, editedUnit);
            // }
            // else {


            if (!contract || !contract.contract_unit || !contract.contract_unit.contract_unit_details) {
                $('#unit_type0').html(options).trigger('change.select2');
                return;
            }

            let filteredUnits = contract.contract_unit.contract_unit_details;
            if (unitTypeId) {
                filteredUnits = filteredUnits.filter(d => d.unit_type_id == unitTypeId);
            }


            filteredUnits.forEach(ut => {
                options += `<option value="${ut.id}">${ut.unit_number}</option>`;
            });

            if (row) {
                row.find('.unit_type0').html(options);
                updateUnitOptions('.unit_type0');
                const safeVal = selectedUnitnumbers.length ? selectedUnitnumbers[0] : '';
                // console.log("safeval", safeVal);
                row.find('.unit_type0').val(safeVal).trigger('change.select2');

            } else {
                $('.unit-row:first .unit_type0').html(options)
                    .val(selectedUnitnumbers.length ? selectedUnitnumbers[0] : '')
                    .trigger('change.select2');
            }
            // $('#unit_type0').html(options)
            //     .val(selectedUnitnumbers.length ? selectedUnitnumbers[0] : '')
            //     .trigger('change.select2');
            // }
            unitNumberChange();
            // }

        }

        $(document).on('change', '.unit_type0', function() {
            const unitId = $(this).val();
            const row = $(this).closest('.unit-row');
            unitNumberChange(unitId, row);
        });



        function unitNumberChange(unitId, row) {
            // alert("unitnumber changes")

            let options = '<option value="">Select SubUnit</option>';
            let contractId = $('#contract_id').val();
            let contract = allContracts.find(c => c.id == contractId);
            let count = contract?.contract_payment_receivables_count || 0;
            let selectedUnit = contract?.contract_unit?.contract_unit_details?.find(u => u.id == unitId);

            let selectedSubunit = [];
            let subunitId = 0;
            // console.log("editedUNITunitchage", selectedUnit);
            // console.log("editedUNIT", editedUnit);

            // if (editedUnit && Array.isArray(editedUnit) && editedUnit.length > 0) {

            //     if (!editedUnit[0].contract_subunit_detail) {
            //         $('#sub_unit_type').html('<option value="">No Subunits Available</option>').trigger('change');
            //         $('#sub_unit_type').prop('required', false);
            //         $('#rent_per_month')
            //             .val(editedUnit[0].rent_per_month ?? '')
            //             .prop('required', true)
            //             .prop('readonly', true);
            //         calculatepaymentamount(editedUnit[0].rent_per_month, count);
            //         return;

            //     } else {
            //         editedUnit.forEach(u => {
            //             subunitId = u.contract_subunit_details_id;
            //             const subunitNo = u.contract_subunit_detail?.subunit_no || '';
            //             const unitDetailId = u.contract_subunit_detail?.contract_unit_detail_id;
            //             if (unitDetailId == unitId) {
            //                 options += `<option value="${subunitId}" selected>${subunitNo}</option>`;
            //                 selectedSubunit.push(subunitId);
            //             }
            //             calculatepaymentamount(editedUnit[0].rent_per_month, count);

            //         });
            //     }
            // } else {
            if (editedUnit && Array.isArray(editedUnit) && editedUnit.length > 0) {
                contract = fullContracts.find(c => c.id == contractId);
                selectedUnit = contract?.contract_unit?.contract_unit_details?.find(u => u.id == unitId);
                editedUnit.forEach(u => {
                    subunitId = u.contract_subunit_details_id;
                    const subunitNo = u.contract_subunit_detail?.subunit_no || '';
                    const unitDetailId = u.contract_subunit_detail?.contract_unit_detail_id;
                    if (unitDetailId == unitId) {
                        options += `<option value="${subunitId}" selected>${subunitNo}</option>`;
                        selectedSubunit.push(subunitId);
                    }
                });
            }
            // console.log("selectedUnit", selectedUnit);

            if (contract?.contract_unit
                ?.business_type == 1) {
                // alert("directfama rentpermonth");
                // alert(selectedUnit.total_rent_per_unit_per_month);
                if (row) {
                    // console.log("selectedUnit", row);


                    row.find('.rent_per_month').val(selectedUnit.total_rent_per_unit_per_month);

                }
                // alert(selectedUnit.total_rent_per_unit_per_month);
            } else {
                if (!contract || !contract.contract_unit || !contract.contract_unit.contract_unit_details) {
                    options += `<option value="">No Subunits Available</option>`;
                    $('#sub_unit_type').html(options).trigger('change');
                    return;
                }
                if (!selectedUnit) {
                    $('#sub_unit_type').html('<option value="">No Unit Selected</option>').trigger('change');
                    $('#sub_unit_type').prop('required', false);
                    $('#rent_per_month').val('').prop('required', false);
                    return;
                }
                let subunits = selectedUnit.contract_sub_unit_details || selectedUnit
                    .contract_subunit_details || [];
                // console.log("subunits", subunits);
                if (!subunits || subunits.length === 0) {
                    $('#sub_unit_type').html('<option value="">No Subunits Available</option>').trigger(
                        'change');
                    $('#sub_unit_type').prop('required', false);

                    $('#rent_per_month')
                        .val(selectedUnit.rent_per_room ?? '')
                        .prop('required', true)
                        .prop('readonly', true);
                    calculatepaymentamount(selectedUnit.rent_per_room, count);

                    return;
                }
                subunits = subunits.filter(sub => sub.is_vacant == 0 || selectedSubunit.includes(sub.id));

                if (selectedUnit && Array.isArray(subunits)) {
                    // console.log("selectedUnit.subunits", subunits);
                    subunits.forEach(sub => {
                        if (!selectedSubunit.includes(sub.id)) {
                            options += `<option value="${sub.id}">${sub.subunit_no}</option>`;
                        }
                    });
                }
                $('.sub_unit_type').html(options).trigger('change');
                $('.sub_unit_type').prop('required', true);
                const $label = $('.sub_unit_type').siblings('label');
                $label.addClass('asterisk');


                // subUnitChange(subunitId, editedUnit);

            }


            // }

        }


        $(document).on('change', '.sub_unit_type', function() {
            const subunitId = $(this).val();
            const row = $(this).closest('.unit-row');
            subUnitChange(subunitId, editedUnit, row);
        });

        function subUnitChange(subunitId, editedUnit, row) {
            // alert("subunitchangecalled");
            let contractId = $('#contract_id').val();
            let contract = allContracts.find(c => c.id == contractId);
            if (editedUnit) {
                contract = fullContracts.find(c => c.id == contractId);
            }
            let count = contract?.contract_payment_receivables_count || 0;
            // console.log("contract in subunit change", contract);

            let unitId = row.find('.unit_no_select').val();
            if (!unitId) unitId = row.find('.unit_type0').val();

            let selectedUnit = contract.contract_unit.contract_unit_details.find(u => u.id == unitId);

            let selectedSubUnit = selectedUnit?.contract_subunit_details?.find(su => su.id == subunitId);
            // console.log("selectedSubUnit", selectedSubUnit);


            $('.rent_per_month')
                .val(selectedUnit.rent_per_unit_per_month ?? '')
                .prop('required', true)
                .data('count', count);
            // .prop('readonly', true);


            calculatepaymentamount(selectedUnit.rent_per_unit_per_month, count);

            if (subunitId) {
                checkTermination(subunitId, unitId, contractId);

            }

        }
        $(document).on('input', '.rent_per_month', function() {
            const rent_val = $(this).val();
            const count = $(this).data('count') || 0;
            calculatepaymentamount(rent_val, count);


        });
    </script>
    {{-- end  --}}
    @include('admin.projects.agreement.terminate-js')

    <script>
        $('#no_of_installments').on('change', function() {
            let editedPayment = @json($agreement->agreement_payment ?? null);
            let agreementPaymentDetails = editedPayment?.agreement_payment_details || [];
            let contractReceivables = selectedContract.contract_payment_receivables || [];
            let deletedAgreementUnitIds = [];
            // console.log("editedPayment", editedPayment);
            // console.log("editedUnit", editedUnit);
            companyId = $("#company_id").val();

            if (editedPayment) {
                //console.log('editedpayment', editedPayment);
            }
            $('.payment_details').show();
            $('.payment_details').removeClass('d-none');
            const installments = $(this).find('option:selected').text().trim();
            let interval = $(this).find(':selected').data('interval');
            let totalRevenue = 0;

            $('#interval').val(interval);
            const containerPayment = document.getElementsByClassName('payment_details')[0];
            //console.log(containerPayment);
            const oldValues = [];
            containerPayment.querySelectorAll('.payment_mode_div').forEach((block, i) => {
                const amountInput = block.querySelector(`#payment_amount_${i}`);
                oldValues[i] = amountInput ? amountInput.value : '';
            });
            const prevFbBlocks = containerPayment.querySelectorAll('.payment_mode_div');
            prevFbBlocks.forEach(block => block.remove());
            //console.log(selectedContract);
            if (window.selectedContract && (selectedContract.contract_type_id == 2 || (
                    selectedContract.contract_type_id === 1 && selectedContract?.contract_unit
                    ?.business_type == 1))) {
                // alert("ff inst");
                const containerPayment = document.querySelector('.payment_details');

                $(containerPayment).find('.fama-table, #accordion').remove();
                $('.header-row').removeClass('d-none').addClass('d-flex');


                const total_units = selectedContract.contract_unit.no_of_units;
                // alert(total_units);
                const famaTable = document.createElement('div');
                famaTable.classList.add('fama-table', 'mt-3', 'd-flex', 'justify-content-center', 'mt-3',
                    'row');

                let tableHTML = `
                        <table class="table table-bordered table-sm table-info col-md-6">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Due Date</th>
                                    <th>Amount</th>
                                    <th>Adjusted Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                    `;
                // console.log("remaining_receivables", remainingReceivables)
                let receivablesToUse = remainingReceivables.length > 0 ?
                    remainingReceivables :
                    selectedContract.contract_payment_receivables;


                if (receivablesToUse &&
                    receivablesToUse.length > 0) {
                    // alert("createtable");

                    let totalMonthlyRent = 0;
                    let selectedUnits = [];

                    // Condition check once BEFORE looping
                    if (selectedContract.contract_type_id == 1 &&
                        selectedContract.contract_unit?.business_type == 1) {
                        if (editedUnit) {
                            selectedUnits = $('.unit-row').map(function() {
                                return $(this).find('select.unit_no_select').val();
                            }).get();
                        } else {
                            selectedUnits = $('.unit-row').map(function() {
                                return $(this).find('select.unit_type0').val();
                            }).get();
                        }



                        const unitsFiltered = window.unit_details.filter(u =>
                            selectedUnits.includes(String(u.id))
                        );
                        // console.log("unitsFiltered", unitsFiltered);

                        unitsFiltered.forEach(unit => {
                            totalMonthlyRent += parseFloat(unit.total_rent_per_unit_per_month || 0);
                        });
                    }

                    // Now loop receivables
                    receivablesToUse.forEach((r, index) => {

                        let amount = r.receivable_amount ?? 0;

                        // Apply condition inside loop
                        if (selectedContract.contract_type_id == 1 &&
                            selectedContract.contract_unit?.business_type == 1) {
                            amount = totalMonthlyRent;
                        }

                        tableHTML += `
                            <tr>
                                <td>${index + 1}</td>
                                <td>${r.receivable_date ?? '-'}</td>
                                <td class="receivable_amount${index}">${amount}</td>
                                <td class="amountchange" data-installment="${index}">${r.receivable_amount ?? 0}</td>
                            </tr>
                        `;
                    });
                } else {
                    tableHTML += `
                        <tr>
                            <td colspan="4" class="text-center text-muted">No receivables found for this contract.</td>
                        </tr>
                        `;
                }

                tableHTML += `
                            </tbody>
                        </table>
                    `;

                famaTable.innerHTML = tableHTML;
                containerPayment.appendChild(famaTable);
                //console.log(unit_details)

                // ====== Unit-wise Accordion Section ======
                if (editedPayment && editedUnit) {


                    const containerPayment = document.querySelector('.payment_details');
                    const accordion = document.createElement('div');
                    accordion.id = 'accordion';

                    // Extract payment details
                    const paymentDetails = editedPayment.agreement_payment_details || [];
                    // console.log("Payment Details :", paymentDetails);



                    if (
                        selectedContract.contract_type_id === 1 && selectedContract?.contract_unit
                        ?.business_type == 1) {
                        let total_revenue = 0;
                        $('.unit-row').each(function(index, row) {
                            const unitId = $(row).find('.unit_no_select').val();
                            const agunitId = $(row).find('.agreement_unit_id').val();
                            const unitObj = window.unit_details.find(u => u.id == unitId);
                            // console.log("Processing unitId:", unitId, unitObj);
                            // console.log("editedunit :", editedUnit);
                            // console.log("agunitId :", agunitId);
                            let isEditedUnit = false;
                            if (remainingReceivables.length > 0) {
                                b2b_per_month = parseFloat(unitObj.total_rent_per_unit_per_month)
                                    .toFixed(
                                        2);
                                total_revenue += b2b_per_month * (remainingReceivables.length);
                            } else {
                                if (unitObj && unitObj.unit_revenue) {
                                    total_revenue += parseFloat(unitObj.unit_revenue);
                                }
                            }



                            // Get payments: either from agreement or fallback to contract
                            let unitPayments = agreementPaymentDetails.filter(p => p.agreement_unit_id ==
                                agunitId);
                            editedUnit.forEach(eu => {
                                if (parseInt(eu.id) === parseInt(agunitId) &&
                                    parseInt(eu.contract_unit_details_id) === parseInt(unitId)) {
                                    isEditedUnit = true;
                                    editedData = eu;
                                }
                            });
                            if (!isEditedUnit && unitPayments.length > 0) {
                                unitPayments.forEach(p => {
                                    p.payment_amount = (unitObj.unit_revenue / installments)
                                        .toFixed(2);
                                });
                            }
                            // console.log("isEditedUnit :", isEditedUnit);
                            // console.log("agreementPaymentDetails :", agreementPaymentDetails);
                            // console.log("agreementPaymentDetails :", unitPayments);
                            // console.log("receivables :", contractReceivables);
                            // console.log("deleted", deletedAgreementUnitIds);
                            // console.log("terminatedReceivables", remainingReceivables);
                            if (remainingReceivables.length > 0) {
                                df_b2b_per_month = parseFloat(unitObj.total_rent_per_unit_per_month)
                                    .toFixed(
                                        2);
                                // total_revenue += df_b2b_per_month * (remainingReceivables.length);
                            } else {
                                df_b2b_per_month = parseFloat(unitObj.unit_revenue / installments)
                                    .toFixed(
                                        2);
                            }
                            if (unitPayments.length === 0) {
                                // alert("hi");
                                unitPayments = contractReceivables.map(r => ({
                                    agreement_unit_id: unitId,
                                    receivable_amount: r.receivable_amount,
                                    receivable_date: r.receivable_date,
                                    payment_mode_id: 2,
                                    payment_amount: df_b2b_per_month,
                                    bank_id: null,
                                    cheque_number: null,
                                }));
                                const termMoment = moment(termDate, 'YYYY-MM-DD');

                                unitPayments = unitPayments.filter(pay => {
                                    if (!pay.receivable_date) return true;

                                    const payMoment = moment(pay.receivable_date, 'DD-MM-YYYY');

                                    return !payMoment.isBefore(
                                        termMoment);
                                });
                            }

                            // console.log("unitPayments for unitId " + unitId + ":", unitPayments);

                            // Build accordion per unit
                            buildUnitAccordion(unitObj, unitPayments, index);

                            // Initialize datetimepickers and event handlers after HTML is inserted
                            unitPayments.forEach((pay, payIndex) => {
                                const uniqueId = `${unitId}_${payIndex}`;
                                const dateContainer = $(`#otherPaymentDate_${uniqueId}`);

                                if (dateContainer.length) {
                                    dateContainer.datetimepicker({
                                        format: 'DD-MM-YYYY'
                                    });


                                    dateContainer.find('input').attr('readonly', true).off('focus');
                                    dateContainer.find('.input-group-append').removeAttr(
                                        'data-toggle');

                                    // Event handlers
                                    $(`#payment_mode${uniqueId}`).off('change').on('change',
                                        function() {
                                            paymentModeChangeFF(unitId, payIndex);
                                        });

                                    dateContainer.off('input change').on('input change',
                                        function() {
                                            calculatePaymentDatesFF(unitId, payIndex);
                                        });

                                    paymentModeChangeFF(unitId, payIndex);

                                    const banks = getBanksByCompany(companyId);
                                    // console.log("Banks :", banks);
                                    const bankSelect = $(`#bank_name_${uniqueId}`);

                                    bankSelect.empty().append(
                                        `<option value="">Select Bank Name</option>`);

                                    banks.forEach(bank => {
                                        bankSelect.append(`
                                <option value="${bank.id}" ${bank.id == pay.bank_id ? 'selected' : ''}>
                                    ${bank.bank_name}
                                </option>
                            `);
                                    });
                                }
                            });
                        });
                        $('#total_rent_per_annum').text(total_revenue.toFixed(2));
                        $('#total_rent_annum').val(total_revenue.toFixed(2));
                    } else if (selectedContract.contract_type_id == 2) {
                        // Loop through units
                        editedUnit.forEach((unitObj, unitIndex) => {
                            const unit = unitObj;
                            // console.log("unit :", unit);
                            // console.log("unitinedx", unitIndex);
                            // console.log('unitobj', unitObj);
                            const row = document.querySelector(`.unit-row[data-row-index='${unitIndex}']`);
                            let unitName = `Unit ${unitIndex + 1}`;
                            if (row) {
                                const unitNoSelect = row.querySelector('.unit_no_select');
                                if (unitNoSelect) {
                                    // Use the text of the selected option
                                    unitName = unitNoSelect.options[unitNoSelect.selectedIndex].text;
                                }
                            }
                            const collapseId = `collapse_${unitObj.id}`;
                            const unitPayments = paymentDetails.filter(
                                pay => pay.agreement_unit_id === unit.id
                            );

                            let installmentBlocks = `
                                <div class="row font-weight-bold mb-2">
                                    <div class="col-md-4 asterisk">Payment Mode</div>
                                    <div class="col-md-4 asterisk">Payment Date</div>
                                    <div class="col-md-4 asterisk">Payment Amount</div>
                                </div>
                            `;

                            // console.log("unitPayments", unitPayments);

                            // Loop through payments
                            unitPayments.forEach((pay, payIndex) => {
                                const uniqueId = `${unit.id}_${payIndex}`;
                                const formattedDate = pay.payment_date ?
                                    moment(pay.payment_date, 'YYYY-MM-DD').format('DD-MM-YYYY') :
                                    '';
                                installmentBlocks += `
                                    <div class="form-group row mb-2">
                                        <input type="hidden" name="payment_detail[${unit.id}][${payIndex}][detail_id]" value="${pay.id}">
                                        <div class="col-md-4">
                                            <select class="form-control " name="payment_detail[${unit.id}][${payIndex}][payment_mode_id]" id="payment_mode${uniqueId}">
                                                <option value="">Select</option>
                                                @foreach ($paymentmodes as $paymentmode)
                                                    <option value="{{ $paymentmode->id }}" ${pay.payment_mode_id == {{ $paymentmode->id }} ? 'selected' : ''}>
                                                        {{ $paymentmode->payment_mode_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="input-group date" id="otherPaymentDate_${uniqueId}" data-target-input="nearest">
                                                <input type="text" class="form-control datetimepicker-input otherPaymentDate"
                                                    name="payment_detail[${unit.id}][${payIndex}][payment_date]"
                                                    id="payment_date_${uniqueId}"
                                                    value="${formattedDate}"
                                                    data-target="#otherPaymentDate_${uniqueId}" placeholder="dd-mm-YYYY"  />
                                                <div class="input-group-append" data-target="#otherPaymentDate_${uniqueId}" data-toggle="datetimepicker">
                                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <input type="text" class="form-control"
                                                id="payment_amount_${uniqueId}"
                                                name="payment_detail[${unit.id}][${payIndex}][payment_amount]"
                                                value="${pay.payment_amount ?? ''}"
                                                placeholder="Payment Amount" />
                                        </div>
                                    </div>

                                    <div class="form-group row extra-fields" id="extra_fields_${uniqueId}">
                                        <div class="col-md-4 bank" id="bank_${uniqueId}">
                                            <label>Bank Name</label>
                                            <select class="form-control" name="payment_detail[${unit.id}][${payIndex}][bank_id]" id="bank_name_${uniqueId}">
                                                <option value="" disabled>Select bank</option>

                                            </select>
                                        </div>

                                        <div class="col-md-3 chq" id="chq_${uniqueId}">
                                            <label>Cheque No</label>
                                            <input type="number" min="0" pattern="\d{6,10}"
                                            maxlength="10"
                                            title="Cheque number must be 6–10 digits" class="form-control" id="cheque_no_${uniqueId}"
                                                name="payment_detail[${unit.id}][${payIndex}][cheque_number]"
                                                value="${pay.cheque_number ?? ''}"
                                                placeholder="Cheque No">
                                                @error('cheque_number')
                                                    <span class="invalid-feedback d-block">
                                                        {{ $message }}
                                                    </span>
                                                @enderror
                                        </div>
                                    </div>
                                `;
                            });

                            accordion.innerHTML += `
                                <div class="card card-info">
                                    <div class="card-header d-flex justify-content-between">
                                        <div class="card-title">
                                            <a class="w-100 text-white" data-toggle="collapse" href="#${collapseId}" aria-expanded="true">
                                                Unit No : ${unitName}
                                            </a>
                                        </div>
                                    </div>
                                    <div id="${collapseId}" class="collapse show" data-parent="#accordion">
                                        <div class="card-body bg-light">
                                            ${installmentBlocks}
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                        containerPayment.appendChild(accordion);
                        $(accordion).find('.select2').select2();
                        // Initialize dates and hide/show extras
                        setTimeout(() => {
                            editedUnit.forEach(unitObj => {
                                const unit = unitObj;
                                const unitPayments = paymentDetails.filter(p => p
                                    .agreement_unit_id ===
                                    unit.id);
                                unitPayments.forEach((_, payIndex) => {
                                    const uniqueId = `${unit.id}_${payIndex}`;
                                    $(`#otherPaymentDate_${uniqueId}`).datetimepicker({
                                        format: 'DD-MM-YYYY'
                                    });
                                    // console.log(unit.id, payIndex);
                                    paymentModeChangeFF(unit.id, payIndex);
                                });
                                unitPayments.forEach((pay, payIndex) => {
                                    const uniqueId = `${unit.id}_${payIndex}`;
                                    const bankSelect = $(`#bank_name_${uniqueId}`);

                                    if (!bankSelect.length) return;


                                    bankSelect.prop('disabled', false);
                                    bankSelect.empty().append(
                                        `<option value="">Select Bank Name</option>`);

                                    const banks = getBanksByCompany(companyId);

                                    banks.forEach(bank => {
                                        bankSelect.append(`
                                            <option value="${bank.id}" ${bank.id == pay.bank_id ? 'selected' : ''}>
                                                ${bank.bank_name}
                                            </option>
                                        `);
                                    });

                                });

                            });
                        }, 200);

                        editedUnit.forEach((unit) => {
                            for (let i = 0; i < installments; i++) {
                                const uniqueId = `${unit.id}_${i}`;
                                // console.log("test");

                                $(`#otherPaymentDate_${uniqueId}`).datetimepicker({
                                    format: 'DD-MM-YYYY'
                                });

                                $(`#otherPaymentDate_${uniqueId} input`).attr('readonly', true).off(
                                    'focus');
                                $(`#otherPaymentDate_${uniqueId} .input-group-append`).removeAttr(
                                    'data-toggle');
                                hidePayments(i);

                                $('#payment_mode' + uniqueId).change(function() {
                                    // console.log("paymentmodechange");
                                    paymentModeChangeFF(unit.id, i);
                                });

                                $('#otherPaymentDate_' + uniqueId).on('input change', function() {
                                    calculatePaymentDatesFF(unit.id, i);
                                });
                                // console.log(unit.id, i);

                                paymentModeChangeFF(unit.id, i);
                            }
                        });
                    }







                    initPaymentValidation(selectedContract.contract_type_id, selectedContract.contract_unit
                        .business_type);
                    $('input[name^="payment_detail"][name$="[payment_amount]"]').each(function() {
                        $(this).trigger('input.paymentValidation');
                    });
                } else {
                    const selectedUnits = $('.unit-row').map(function() {
                        return $(this).find('select.unit_type0').val();
                    }).get();
                    // console.log('selected units', selectedUnits)
                    const filteredUnits = window.unit_details.filter(u => selectedUnits.includes(String(u.id)));
                    // const filteredUnits = selectedUnits.map(id =>
                    //     window.unit_details.find(u => String(u.id) === id)
                    // ).filter(Boolean);
                    // console.log("selected units row:", selectedUnits);
                    // console.log("filtered units row:", filteredUnits);
                    // alert("hi");
                    if (filteredUnits.length > 0) {
                        // alert("new agreement");
                        // console.log("unit details:", window.unit_details);

                        const containerPayment = document.querySelector('.payment_details');
                        const accordion = document.createElement('div');
                        accordion.id = 'accordion';
                        // console.log('rent receivable per annum:' + selectedContract);
                        const installmentCount = $(this).find('option:selected').text().trim();

                        filteredUnits.forEach((unit, unitIndex) => {
                            const reven = parseFloat(unit.unit_revenue) || 0;
                            // console.log('reven', reven);
                            totalRevenue += reven;
                            // console.log('totalRevenue', totalRevenue);

                            // console.log('unit', unit);

                            const collapseId = `collapse_${unit.id}`;
                            const unitName = unit.unit_number || `Unit ${unitIndex + 1}`;

                            // const unitRev = parseFloat(selectedContract.contract_rentals
                            //     .rent_receivable_per_annum);
                            // const amount_per_month = (unitRev / (installmentCount * total_units)).toFixed(2);
                            const rev = parseFloat(unit.unit_revenue);

                            // alert(rev);
                            let monthlyrent = 0;
                            if (selectedContract.contract_type_id == 2) {
                                // alert("dfb2b");
                                // monthlyrent = (rev / installmentCount).toFixed(2);
                                // alert(monthlyrent);
                                monthlyrent = parseFloat(unit.total_rent_per_unit_per_month || 0).toFixed(
                                    2);
                                // monthlyrent = 100;



                            } else if (selectedContract.contract_type_id === 1 && selectedContract
                                ?.contract_unit
                                ?.business_type == 1) {

                                monthlyrent = parseFloat(unit.total_rent_per_unit_per_month || 0).toFixed(
                                    2);



                            }


                            let installmentBlocks = `
                                        <div class="row font-weight-bold mb-2">
                                            <div class="col-md-4 asterisk">Payment Mode</div>
                                            <div class="col-md-4 asterisk">Payment Date</div>
                                            <div class="col-md-4 asterisk">Payment Amount</div>
                                        </div>
                                    `;

                            // Loop installments for each unit


                            for (let i = 0; i < installmentCount; i++) {
                                const uniqueId = `${unit.id}_${i}`;


                                // 🗓️ Get the receivable date safely
                                let receivableDate = '';

                                if (remainingReceivables.length > 0) {
                                    // console.log('remaining', remainingReceivables);
                                    const rawDate = remainingReceivables[i]?.receivable_date;
                                    receivableDate = rawDate ? moment(rawDate, 'DD-MM-YYYY').format(
                                        'DD-MM-YYYY') : '';
                                } else {
                                    const rawDate = selectedContract.contract_payment_receivables?.[i]
                                        ?.receivable_date;
                                    receivableDate = rawDate ? moment(rawDate, 'DD-MM-YYYY').format(
                                        'DD-MM-YYYY') : '';
                                }
                                installmentBlocks += `
                                <div class="form-group row mb-2">
                                    <div class="col-md-4">
                                        <select class="form-control " name="payment_detail[${unit.id}][${i}][payment_mode_id]" id="payment_mode${uniqueId}" required>
                                            <option value="">Select</option>
                                            @foreach ($paymentmodes as $paymentmode)
                                                 <option value="{{ $paymentmode->id }}"
                                                    {{ $paymentmode->id == 2 ? 'selected' : '' }}>
                                                    {{ $paymentmode->payment_mode_name }}
                                                </option>

                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="input-group date" id="otherPaymentDate_${uniqueId}" data-target-input="nearest">
                                            <input type="text" class="form-control datetimepicker-input otherPaymentDate"
                                                name="payment_detail[${unit.id}][${i}][payment_date]" id="payment_date_${uniqueId}" value="${receivableDate}"
                                                data-target="#otherPaymentDate_${uniqueId}" placeholder="dd-mm-YYYY"  />
                                            <div class="input-group-append" data-target="#otherPaymentDate_${uniqueId}" data-toggle="datetimepicker">
                                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <input type="text" class="form-control"
                                            id="payment_amount_${uniqueId}"
                                            name="payment_detail[${unit.id}][${i}][payment_amount]"
                                            value="${monthlyrent}"
                                            placeholder="Payment Amount" />
                                    </div>
                                </div>

                                <div class="form-group row extra-fields" id="extra_fields_${uniqueId}">
                                    <div class="col-md-4 bank" id="bank_${uniqueId}">
                                        <label>Bank Name</label>
                                        <select class="form-control is-invalid" name="payment_detail[${unit.id}][${i}][bank_id]" id="bank_name_${uniqueId}">
                                            <option value="" >Select bank</option>

                                        </select>
                                    </div>

                                    <div class="col-md-3 chq" id="chq_${uniqueId}">
                                        <label>Cheque No</label>
                                        <input type="number" pattern="\d{6,10}" min="0"
                                                maxlength="10"
                                                title="Cheque number must be 6–10 digits" class="form-control" id="cheque_no_${uniqueId}" name="payment_detail[${unit.id}][${i}][cheque_number]" placeholder="Cheque No">
                                                @error('cheque_number')
                                                    <span class="invalid-feedback d-block">
                                                        {{ $message }}
                                                    </span>
                                                @enderror
                                    </div>
                                </div>
                            `;
                            }

                            let revenueHtml = '';
                            if (selectedContract.contract_type_id == 2) {
                                revenueHtml = `<div class="px-3">Revenue: AED ${rev ?? 0}</div>`;
                            }


                            // Accordion block
                            accordion.innerHTML += `
                            <div class="card card-info">
                                <div class="card-header d-flex justify-content-between">
                                    <div class="card-title">
                                        <a class="w-100 text-white" data-toggle="collapse" href="#${collapseId}" aria-expanded="true">
                                            Unit No : ${unitName}
                                        </a>
                                    </div>
                                    ${revenueHtml}
                                </div>
                                <div id="${collapseId}" class="collapse show" data-parent="#accordion">
                                    <div class="card-body bg-light">
                                        ${installmentBlocks}
                                    </div>
                                </div>
                            </div>
                        `;
                        });

                        containerPayment.appendChild(accordion);
                        $(accordion).find('.select2').select2();

                        setTimeout(() => {
                            window.unit_details.forEach((unit) => {
                                for (let i = 0; i < installmentCount; i++) {
                                    const uniqueId = `${unit.id}_${i}`;

                                    paymentModeChangeFF(unit.id, i);
                                }
                            });
                        }, 300);
                        // Initialize dynamic elements
                        window.unit_details.forEach((unit) => {
                            for (let i = 0; i < installmentCount; i++) {
                                const uniqueId = `${unit.id}_${i}`;

                                $(`#otherPaymentDate_${uniqueId}`).datetimepicker({
                                    format: 'DD-MM-YYYY'
                                });

                                $(`#otherPaymentDate_${uniqueId} input`).attr('readonly', true).off(
                                    'focus');
                                $(`#otherPaymentDate_${uniqueId} .input-group-append`).removeAttr(
                                    'data-toggle');
                                hidePayments(i);

                                $('#payment_mode' + uniqueId).change(function() {
                                    paymentModeChangeFF(unit.id, i);
                                });

                                $('#otherPaymentDate_' + uniqueId).on('input change', function() {
                                    calculatePaymentDatesFF(unit.id, i);
                                });

                                paymentModeChangeFF(unit.id, i);

                                const bankSelect = $(`#bank_name_${uniqueId}`);

                                if (!bankSelect.length) return;


                                bankSelect.prop('disabled', false);
                                bankSelect.empty().append(
                                    `<option value="">Select Bank Name</option>`);

                                const banks = getBanksByCompany(companyId);

                                banks.forEach(bank => {
                                    bankSelect.append(`
                                            <option value="${bank.id}" >
                                                ${bank.bank_name}
                                            </option>
                                        `);
                                });
                            }
                        });

                        initPaymentValidation(selectedContract.contract_type_id, selectedContract.contract_unit
                            .business_type);
                        // After populating amounts dynamically, trigger validation for all inputs
                        $('input[name^="payment_detail"][name$="[payment_amount]"]').each(function() {
                            $(this).trigger('input.paymentValidation');
                        });



                    }
                }
                if (
                    selectedContract.contract_type_id === 1 && selectedContract?.contract_unit
                    ?.business_type == 1) {
                    // console.log('totalRevenue', totalRevenue);
                    // totalRevenue = 1000;
                    if (totalRevenue) {

                        $('#total_rent_per_annum').text(totalRevenue);
                        $('#total_rent_annum').val(totalRevenue);
                    }


                } else {
                    if (remainingTotal) {
                        $('#total_rent_per_annum').text(remainingTotal);
                        $('#total_rent_annum').val(remainingTotal);
                    } else {
                        $('#total_rent_per_annum').text(selectedContract.contract_rentals
                            .rent_receivable_per_annum);
                        $('#total_rent_annum').val(selectedContract.contract_rentals
                            .rent_receivable_per_annum);
                    }

                }
                return;
            } else {

                //console.log('agree', editedPayment);
                const containerPayment = document.querySelector('.payment_details');

                $(containerPayment).find('.fama-table, #accordion').remove();
                $('.header-row').removeClass('d-none').addClass('d-flex');


                const newInstallments = $(this).find('option:selected').text().trim();
                if (editedPayment && Array.isArray(editedPayment.agreement_payment_details)) {
                    // console.log("EditedUnit", editedUnit)
                    // Grab existing payment details if any
                    let existingPayments = [];
                    if (editedPayment && Array.isArray(editedPayment.agreement_payment_details)) {
                        existingPayments = editedPayment.agreement_payment_details;
                    }

                    const oldInstallments = existingPayments.length;

                    // Remove extra blocks if newInstallments < oldInstallments
                    if (newInstallments < oldInstallments) {
                        const blocks = containerPayment.querySelectorAll('.payment_mode_div');
                        for (let i = blocks.length - 1; i >= newInstallments; i--) {
                            blocks[i].remove();
                        }
                    }

                    // Loop through installments and create/update payment blocks
                    for (let i = 0; i < newInstallments; i++) {
                        const pay = existingPayments[i] || {};
                        const formattedDate = pay.payment_date ? moment(pay.payment_date, 'YYYY-MM-DD').format(
                            'DD-MM-YYYY') : '';

                        let paymentBlock = containerPayment.querySelector(`#payment_block_${i}`);
                        if (!paymentBlock) {
                            paymentBlock = document.createElement('div');
                            paymentBlock.classList.add('payment_mode_div');
                            paymentBlock.id = `payment_block_${i}`;
                            containerPayment.appendChild(paymentBlock);
                        }

                        paymentBlock.innerHTML = `
                            <input type="hidden" name="payment_detail[${i}][id]" value="${pay.id || ''}">
                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label class="asterisk">Payment Mode</label>
                                    <select class="form-control " name="payment_detail[${i}][payment_mode_id]" id="payment_mode${i}" required>
                                        <option value="">Select</option>
                                        @foreach ($paymentmodes as $paymentmode)
                                            <option value="{{ $paymentmode->id }}" ${pay.payment_mode_id == {{ $paymentmode->id }} ? 'selected' : ''}>
                                                {{ $paymentmode->payment_mode_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="asterisk">Payment Date</label>
                                    <div class="input-group date" id="otherPaymentDate${i}" data-target-input="nearest">
                                        <input type="text" class="form-control datetimepicker-input otherPaymentDate"
                                            name="payment_detail[${i}][payment_date]" id="payment_date${i}"
                                            value="${formattedDate}" data-target="#otherPaymentDate${i}" placeholder="dd-mm-YYYY" />
                                        <div class="input-group-append" data-target="#otherPaymentDate${i}" data-toggle="datetimepicker">
                                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label class="asterisk">Payment Amount</label>
                                    <input type="text" class="form-control" id="payment_amount_${i}" name="payment_detail[${i}][payment_amount]" value="${pay.payment_amount || ''}" placeholder="Payment Amount" />
                                </div>
                            </div>
                            <div class="form-group row" id="extra_fields_${i}">
                                <div class="col-md-4 bank" id="bank${i}">
                                <label>Bank Name</label>
                                <select class="form-control" name="payment_detail[${i}][bank_id]" id="bank_name${i}">
                                    <option value="" >Select Bank Name</option>
                                    @foreach ($banks as $bank)
                                        <option value="{{ $bank->id }}" ${pay.bank_id == {{ $bank->id }} ? 'selected' : ''}>{{ $bank->bank_name }}</option>
                                    @endforeach
                                </select>
                                </div>

                                <div class="col-md-3 chq" id="chq${i}">
                                    <label>Cheque No</label>
                                    <input type="number" pattern="\d{6,10}" min="0"
                                        maxlength="10"
                                        title="Cheque number must be 6–10 digits" class="form-control" value="${pay.cheque_number || ''}" name="payment_detail[${i}][cheque_number]" id="cheque_no${i}" placeholder="Cheque No">
                                        @error('cheque_number')
                                            <span class="invalid-feedback d-block">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                </div>


                            </div>
                        `;

                        // Initialize Select2 and Datepicker AFTER appending to DOM
                        $(paymentBlock).find('.select2').select2();

                        initPaymentValidation(selectedContract.contract_type_id, selectedContract.contract_unit
                            .business_type);

                        // Trigger validation immediately for loaded amounts
                        $('input[name^="payment_detail"][name$="[payment_amount]"]').each(function() {
                            $(this).trigger('input');
                        });

                        $(`#otherPaymentDate${i}`).datetimepicker({
                            format: 'DD-MM-YYYY'
                        });

                        // // Bind change event to payment mode
                        // $(paymentBlock).find(`#payment_mode${i}`).off('change').on('change', function() {
                        //     paymentModeChange(i);
                        // });

                        // // Initial visibility setup
                        // paymentModeChange(i);

                        hidePayments(i);


                        // Then apply the right visibility logic
                        setTimeout(() => {
                            paymentModeChange(i);
                        }, 50);

                        // Attach change event for user interaction
                        $('#payment_mode' + i).change(function() {
                            paymentModeChange(i);
                        });
                        const banks = getBanksByCompany(companyId);
                        // console.log("Banks :", banks);
                        const bankSelect = $(`#bank_name${i}`);

                        bankSelect.empty().append(`<option value="">Select Bank Name</option>`);

                        banks.forEach(bank => {
                            bankSelect.append(`
                                <option value="${bank.id}" ${bank.id == pay.bank_id ? 'selected' : ''}>
                                    ${bank.bank_name}
                                </option>
                            `);
                        });

                    }
                    let rentval = parseFloat($('.rent_per_month').val()) || 0;
                    // console.log('rentval', rentval, newInstallments)
                    let editedRent = parseFloat(editedUnit?.[0]?.rent_per_month) || 0;
                    // console.log("ren", rentval, editedRent);

                    if (rentval !== editedRent) {
                        calculatepaymentamount(rentval, newInstallments);
                    }




                } else {
                    // alert("DFFFFFF");
                    for (let i = 0; i < installments; i++) {
                        // alert(installments);

                        const paymentBlock = document.createElement('div');
                        paymentBlock.classList.add('payment_mode_div');
                        let receivableDate = '';

                        if (remainingReceivables) {
                            // console.log('remaining', remainingReceivables);
                            const rawDate = remainingReceivables[i]?.receivable_date;
                            receivableDate = rawDate ? moment(rawDate, 'DD-MM-YYYY').format('DD-MM-YYYY') : '';
                        } else {
                            const rawDate = selectedContract.contract_payment_receivables?.[i]?.receivable_date;
                            receivableDate = rawDate ? moment(rawDate, 'DD-MM-YYYY').format('DD-MM-YYYY') : '';
                        }

                        const existingValue = oldValues[i] || '';

                        paymentBlock.innerHTML = `

                                <div class="form-group row">
                                    <div class="col-md-4">
                                        <label class="asterisk">Payment Mode</label>
                                        <select class="form-control " name="payment_detail[${i}][payment_mode_id]" id="payment_mode${i}" required>
                                            <option value="">Select</option>
                                            @foreach ($paymentmodes as $paymentmode)
                                            <option value="{{ $paymentmode->id }}">
                                            {{ $paymentmode->payment_mode_name }} </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="asterisk">Payment Date</label>
                                        <div class="input-group date" id="otherPaymentDate${i}" data-target-input="nearest">
                                            <input type="text" class="form-control datetimepicker-input otherPaymentDate"
                                                name="payment_detail[${i}][payment_date]" id="payment_date${i}" value="${receivableDate}"
                                                data-target="#otherPaymentDate${i}" placeholder="dd-mm-YYYY" />
                                            <div class="input-group-append" data-target="#otherPaymentDate${i}" data-toggle="datetimepicker">
                                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="asterisk">Payment Amount</label>
                                        <input type="text" class="form-control" id="payment_amount_${i}" name="payment_detail[${i}][payment_amount]" value="${existingValue}" placeholder="Payment Amount">
                                    </div>
                                </div>
                                <div class="form-group row" id="extra_fields_${i}">
                                        <div class="col-md-4 bank" id="bank${i}">
                                            <label for="exampleInputEmail1">Bank Name</label>
                                            <select class="form-control select2 is-invalid" name="payment_detail[${i}][bank_id]" id="bank_name${i}">
                                                <option value="" disabled>Select Bank Name</option>

                                            </select>
                                        </div>

                                        <div class="col-md-3 chq" id="chq${i}">
                                            <label for="exampleInputEmail1">Cheque No</label>
                                            <input type="number" class="form-control" pattern="\d{6,10}" min="0"
                                                maxlength="10"
                                                title="Cheque number must be 6–10 digits" id="cheque_no${i}" name="payment_detail[${i}][cheque_number]" placeholder="Cheque No">
                                                @error('cheque_number')
                                                        <span class="invalid-feedback d-block">
                                                            {{ $message }}
                                                        </span>
                                                    @enderror
                                                                                    </div>

                                        <div class="col-md-3 chq" id="chqiss${i}">
                                            <label for="exampleInputEmail1">Cheque Issuer</label>
                                            <select class="form-control select2" name="cheque_issuer[]" id="cheque_issuer${i}">
                                                <option value="">Select</option>
                                                <option value="self">Self</option>
                                                <option value="other">Other</option>
                                            </select>
                                        </div>

                                        <div class="col-md-3 chqot" id="chqotiss${i}">
                                            <label for="exampleInputEmail1">Cheque Issuer Name</label>
                                            <input type="text" class="form-control" id="cheque_issuer_name${i}" name="cheque_issuer_name[]" placeholder="Cheque Issuer Name">
                                        </div>

                                        <div class="col-md-3 chqot" id="chqot${i}">
                                            <label for="exampleInputEmail1">Issuer ID</label>
                                            <input type="text" class="form-control" id="issuer_id${i}" name="issuer_id[]" placeholder="Issuer ID">
                                        </div>
                                    </div>
                            `;

                        // Append first
                        containerPayment.appendChild(paymentBlock);
                        initPaymentValidation(selectedContract.contract_type_id, selectedContract.contract_unit
                            .business_type);
                        // $('input[name^="payment_detail"][name$="[payment_amount]"]').each(function() {
                        //     $(this).trigger('input.paymentValidation');
                        // });


                        $('#otherPaymentDate' + i).datetimepicker({
                            format: 'DD-MM-YYYY'
                        });

                        // attachEventsPayment(paymentBlock, i);
                        hidePayments(i);

                        $('#payment_mode' + i).change(function() {
                            paymentModeChange(i);
                        });



                        $('#otherPaymentDate0').on('input change', function() {
                            calculatePaymentDates();
                        });
                        const banks = getBanksByCompany(companyId);
                        // console.log("Banks :", banks);
                        const bankSelect = $(`#bank_name${i}`);

                        bankSelect.empty().append(`<option value="">Select Bank Name</option>`);

                        banks.forEach(bank => {
                            bankSelect.append(`
                                <option value="${bank.id}">
                                    ${bank.bank_name}
                                </option>
                            `);
                        });



                    }

                }

            }



        });
    </script>
    <script>
        function initPaymentValidation(type, business_type) {
            //console.log(type, business_type);
            $(document).off('input.paymentValidation');

            $(document).on('input.paymentValidation', 'input[name^="payment_detail"][name$="[payment_amount]"]',
                function() {
                    if (type === 2 || (type === 1 && business_type === 1)) {
                        const name = $(this).attr('name');
                        const match = name.match(/payment_detail\[\d+\]\[(\d+)\]\[payment_amount\]/);
                        if (!match) return;
                        const changedIndex = parseInt(match[1]);
                        validateTotalPaymentFF(changedIndex);
                    } else if (type === 1) {
                        validateTotalPayment();
                    }

                });
        }


        function validateTotalPayment() {
            let totalRent = parseFloat($('#total_rent_per_annum').text()) || 0;
            // alert(totalRent);
            let totalPayment = 0;

            // Sum all payment amounts
            $('input[name^="payment_detail"][name$="[payment_amount]"]').each(function() {
                let val = parseFloat($(this).val()) || 0;
                totalPayment += val;
            });
            // console.log(totalRent, totalPayment);

            // // Enable or disable submit button
            // if (totalPayment === totalRent && totalRent > 0) {
            //     $('#submitBtn').prop('disabled', false);
            // } else {
            //     $('#submitBtn').prop('disabled', true);
            //     toastr.error(` Total payment amount ${totalPayment} does not match total rent per annum ${totalRent}.`,
            //         `Check Payments`);
            // }
            const errorDiv = $('#paymentError');
            // errorDiv.html('');
            errorDiv.attr('tabindex', '-1');

            // Enable or disable submit button
            if (totalPayment === totalRent && totalRent > 0) {
                $('#submitBtn').prop('disabled', false);
                errorDiv.addClass('d-none').removeClass('d-flex');
            } else {
                $('#submitBtn').prop('disabled', true);
                errorDiv.html(
                    `Total payment amount <span class="mx-1 text-dark">${totalPayment}</span> does not match total rent per annum <span class="mx-1 text-dark">${totalRent}</span>.`
                );
                errorDiv.removeClass('d-none').addClass('d-flex');
                // errorDiv.focus();
                // errorDiv[0].scrollIntoView({
                //     behavior: 'smooth',
                //     block: 'center'
                // });
            }
        }


        function validateTotalPaymentFF(changedIndex = null) {
            const totalsByInstallment = {};
            const errorDiv = $('#paymentError');
            let errorMessages = [];

            $('input[name^="payment_detail"][name$="[payment_amount]"]').each(function() {
                const name = $(this).attr('name');
                const match = name.match(/payment_detail\[\d+\]\[(\d+)\]/);
                if (!match) return;

                const installmentIndex = parseInt(match[1]);
                const val = parseFloat($(this).val()) || 0;

                if (!totalsByInstallment[installmentIndex]) {
                    totalsByInstallment[installmentIndex] = 0;
                }
                totalsByInstallment[installmentIndex] += val;
            });

            // const keysToCheck = changedIndex !== null ? [changedIndex] : Object.keys(totalsByInstallment);
            const keysToCheck = Object.keys(totalsByInstallment);

            keysToCheck.forEach(index => {
                const total = totalsByInstallment[index]?.toFixed(2) || 0;
                const $amountCell = $(`.amountchange[data-installment="${index}"]`);
                const receivable = parseFloat($(`.receivable_amount${index}`).text()) || 0;

                $amountCell.text(total);

                if (receivable == total) {
                    $amountCell.css({
                        backgroundColor: '#d4edda',
                        color: '#155724'
                    });
                } else {
                    $amountCell.css({
                        backgroundColor: '#f8d7da',
                        color: '#721c24'
                    });

                    // Add to error list
                    // errorMessages.push(
                    //     `Installment ${parseInt(index) + 1}: Total (${total}) is not equal to Receivable (${receivable})`
                    // );
                    errorMessages.push(
                        `Installment <span class="text-dark">${parseInt(index) + 1}</span>: Total <span class="text-dark">(${total})</span> is not equal to Receivable <span class="text-dark">(${receivable})</span>`
                    );
                }
            });

            if (errorMessages.length > 0) {
                $('#submitBtn').prop('disabled', true);
                errorDiv.html(errorMessages.join('<br>')) // show all errors
                    .removeClass('d-none')
                    .addClass('d-block');
            } else {
                $('#submitBtn').prop('disabled', false);
                errorDiv.addClass('d-none').removeClass('d-block').empty();
            }
        }




        function paymentModeChangeFF(unitId, i) {
            // alert("called");
            const uniqueId = `${unitId}_${i}`;
            const payment_mode = $(`#payment_mode${uniqueId}`).val();

            if (payment_mode == '3') { // Cheque
                $(`#chq_${uniqueId}`).show().find('input, select').prop('disabled', false).prop('required', true);
                $(`#bank_${uniqueId}`).show().find('input, select').prop('disabled', false).prop('required', true);
                addClassAsterisk(`#chq_${uniqueId}`);
                addClassAsterisk(`#bank_${uniqueId}`);
            } else if (payment_mode == '2') { // Bank Transfer
                // alert("called");
                $(`#bank_${uniqueId}`).show().find('input, select').prop('disabled', false).prop('required', true);
                $(`#chq_${uniqueId}`).hide().find('input, select').prop('disabled', true);
                addClassAsterisk(`#bank_${uniqueId}`);
            } else { // Cash or others
                $(`#bank_${uniqueId}`).hide().find('input, select').prop('disabled', true).prop('required', false);
                $(`#chq_${uniqueId}`).hide().find('input, select').prop('disabled', true).prop('required', false);
            }
        }

        function calculatePaymentDatesFF(unitId, startIndex) {
            const uniqueIdStart = `${unitId}_${startIndex}`;
            const startDateVal = $(`#otherPaymentDate_${uniqueIdStart}`).find("input").val();

            const noOfInstallments = parseInt($('#no_of_installments').find('option:selected').text().trim()) || 0;
            const interval = parseInt($('#interval').val()) || 1;

            const startDate = parseDateCustom(startDateVal);
            if (!startDate || isNaN(startDate.getTime())) return;

            $(document).off('change.datetimepicker.autoCalc');

            for (let i = startIndex + 1; i < noOfInstallments; i++) {
                const nextId = `${unitId}_${i}`;
                const nextPicker = $(`#otherPaymentDate_${nextId}`);

                if (nextPicker.length === 0) continue;

                const nextDate = new Date(startDate);
                nextDate.setMonth(startDate.getMonth() + (interval * (i - startIndex)));

                const year = nextDate.getFullYear();
                const month = String(nextDate.getMonth() + 1).padStart(2, '0');
                const day = String(nextDate.getDate()).padStart(2, '0');
                const formattedDate = `${day}-${month}-${year}`;

                nextPicker.datetimepicker('date', moment(formattedDate, 'DD-MM-YYYY'));
            }

            // rebind after done
            $(document).on('change.datetimepicker.autoCalc', '.otherPaymentDate', function() {
                const idParts = this.id.split('_');
                const unitId = idParts[1];
                const index = parseInt(idParts[2]);
                calculatePaymentDatesFF(unitId, index);
            });
        }
    </script>
    {{-- Df B2B unit selection add more --}}
    <script>
        let counter = 1;
        let deletedUnits = [];

        $("#add_more_unit").on("click", function() {
            let addedUnits = getUnitCount();
            const totalUnitsAvailable = window.unit_details.length;

            if (addedUnits >= totalUnitsAvailable) {
                $("#add_more_unit")
                    .prop("disabled", true)
                    .addClass("shake");

                Swal.fire({
                    icon: "warning",
                    title: "Unit Limit Reached",
                    text: "No more units can be added.",
                });
                return;
            }


            let clone = `
                    <div class="card mb-3 unit-row p-3" data-row-index="${counter}">
                        <div class="card-body">
                        <div class="row g-3 align-items-end">

                            <div class="col-sm-3">
                                <label class="form-label">Unit Type</label>
                                <input type="hidden" name="unit_detail[${counter}][unit_id]" value="">
                                <select class="form-control select2 unit_type_id"
                                        name="unit_detail[${counter}][unit_type_id]" required>
                                </select>
                            </div>

                            <div class="col-sm-3">
                                <label class="form-label">Select Unit No</label>
                                <select class="form-control select2 unit_type0"
                                        name="unit_detail[${counter}][contract_unit_details_id]" required>
                                    <option value="">Select Unit Number</option>
                                </select>
                            </div>

                            <div class="col-sm-3 subunit_number_div">
                                <label class="form-label">Sub Unit</label>
                                <select class="form-control select2 sub_unit_type"
                                        name="unit_detail[${counter}][contract_subunit_details_id]" disabled></select>
                            </div>

                            <div class="col-sm-2">
                                <label class="form-label">Rent per Month</label>
                                <input type="text" class="form-control rent_per_month"
                                    name="unit_detail[${counter}][rent_per_month]" placeholder="Rent per month">
                            </div>
                            <div class="col-sm-1 text-end">
                                <button type="button" class="btn btn-danger delete-row"> <i class="fa fa-trash"></i></button>
                                </div>

                        </div>
                        </div>


                    </div>
                    `;




            $("#additional_unit_rows").append(clone);
            $("#additional_unit_rows").removeClass("d-none").addClass("d-block");

            populateUnitTypesForNewRow(counter);

            // $(".select2").select2();


            counter++;
            updateUnitOptions('.unit_type0');
        });



        // Delete row
        $(document).on("click", ".delete-row", function() {
            $(this).closest(".unit-row").remove();
        });




        function getUnitCount() {
            return $(".unit-row").length;
        }

        function updateUnitOptions(selecter) {
            const selectedUnitIds = $(selecter).map(function() {
                return $(this).val();
            }).get();
            // console.log("Selected Unit IDs:", selectedUnitIds);
            // console.log("Selector:", selecter);

            $(selecter).each(function() {
                const select = $(this);
                const currentVal = select.val();
                // console.log("Current Value:", currentVal);

                select.find("option").prop("disabled", false);

                selectedUnitIds.forEach(function(id) {
                    if (id && id !== currentVal) {
                        // alert(currentVal);
                        const opt = select.find(`option[value='${id}']`);
                        opt.prop("disabled", true);
                        opt.text(opt.text() + " (Already selected)");
                    }
                });
                // select.trigger('change');

                // select.select2();
            });
        }


        $(document).on("click", ".delete-row", function() {
            $("#add_more_unit")
                .prop("disabled", false);
            $(this).closest(".unit-row").remove();
            updateUnitOptions();
        });

        $(document).on("change", ".unit_type0", function() {
            updateUnitOptions();
        });

        function populateUnitTypesForNewRow(index) {
            let contract = selectedContract;
            let unitTypeIds = contract.contract_unit.contract_unit_details
                .filter(d => d.is_vacant == 0)
                .map(d => d.unit_type_id);

            unitTypeIds = [...new Set(unitTypeIds)];

            let options = `<option value="">Select Unit Types</option>`;

            allunittypes
                .filter(t => unitTypeIds.includes(t.id))
                .forEach(t => {
                    options += `<option value="${t.id}">${t.unit_type}</option>`;
                });
            deletedUnits.forEach(du => {
                if (!unitTypeIds.includes(du.unit_type_id)) {
                    options += `<option value="${du.unit_type_id}">${du.unit_type_name}</option>`;
                }
            });

            $(`select[name="unit_detail[${index}][unit_type_id]"]`).html(options);
        }
    </script>

    <script>
        $(document).ready(function() {
            if (editedUnit) {
                updateUnitOptions('.unit_no_select');
            }
            $(document).on('change', '.unit_type_select', function() {
                // alert('unit type changed');
                var typeId = $(this).val();
                var $unitNoSelect = $(this).closest('.unit-row').find('.unit_no_select');
                // console.log('Selected Unit Type ID:', typeId);
                // console.log('unitsbytype', mergedUnitsByType[typeId]);
                // var $unitNoSelect = $(this).closest('.unit-row');
                // unitTypeChange(typeId, null, $unitNoSelect);


                $unitNoSelect.empty().append('<option value="">Select Unit Number</option>');

                if (mergedUnitsByType[typeId]) {
                    mergedUnitsByType[typeId].forEach(function(unit) {
                        $unitNoSelect.append('<option value="' + unit.id + '">' + unit.unit_number +
                            '</option>');
                    });
                }
                updateUnitOptions('.unit_no_select');
            });
        });
        $(document).on('change', '.unit_no_select', function() {
            // alert('unit no changed');
            const unitId = $(this).val();
            const row = $(this).closest('.unit-row');
            unitNumberChange(unitId, row);
            updateUnitOptions('.unit_no_select');
        });
    </script>
    <script>
        function getBanksByCompany(companyId) {
            return window.allBanks.filter(bank => bank.company_id == companyId);
        }
    </script>
    <script>
        function fillFFTenant() {
            $('#tenant_name').val('Faateh Real Estate');
            $('#tenant_mobile').val('+971568856995');
            $('#tenant_email').val('adil@faateh.ae');
            $('#contact_person').val('Adil Faridi');
            $('#contact_number').val('+971568856995');
            $('#contact_email').val('adil@faateh.ae');
            $('#tenant_address').val('Dubai');

        }

        function removeTenant() {
            $('#tenant_name').val('');
            $('#tenant_mobile').val('');
            $('#tenant_email').val('');
            $('#contact_person').val('');
            $('#contact_number').val('');
            $('#contact_email').val('');
            $('#tenant_address').val('');
        }
    </script>
    <script>
        function addClassAsterisk(inputSelector) {
            const label = $(inputSelector).find('label');
            label.addClass('asterisk');
        }
    </script>
@endsection
