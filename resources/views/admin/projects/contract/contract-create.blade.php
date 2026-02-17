@extends('admin.layout.admin_master')

@section('custom_css')
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('assets/daterangepicker/daterangepicker.css') }}">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet" href="{{ asset('assets/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
    <!-- iCheck for checkboxes and radio inputs -->

    <link rel="stylesheet" href="{{ asset('assets/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/bs-stepper/css/bs-stepper.min.css') }}">
    <style>
        .enddate {
            background-color: #e9ecef;
        }
    </style>
@endsection

@section('content')
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
                            <li class="breadcrumb-item active">{{ $title }}</li>
                        </ol>
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
                            {{-- <div class="card-header">
                                <!-- <h3 class="card-title">Contract Details</h3> -->
                                <span class="float-right">
                                    <button class="btn btn-info float-right m-1" data-toggle="modal"
                                        data-target="#modal-Contract">Add Contract</button>
                                    <button class="btn btn-secondary float-right m-1" data-toggle="modal"
                                        data-target="#modal-import">Import</button>
                                </span>
                            </div> --}}
                            <!-- /.card-header -->
                            <div class="card-body">
                                <div class="bs-stepper">
                                    <form action="" id="contractForm">
                                        @csrf
                                        <input type="hidden" name="contract[id]" id="contract_id"
                                            value="{{ old('id', $contract->id ?? '') }}">
                                        <input type="hidden" name="detail[id]"
                                            value="{{ old('detail_id', $contract->contract_detail->id ?? '') }}">
                                        <input type="hidden" name="unit[id]"
                                            value="{{ old('unit_id', $contract->contract_unit->id ?? '') }}">
                                        <input type="hidden" name="rentals[id]"
                                            value="{{ old('rentals_id', $contract->contract_rentals->id ?? '') }}">
                                        <input type="hidden" name="otc[id]"
                                            value="{{ old('otc_id', $contract->contract_otc->id ?? '') }}">
                                        <input type="hidden" name="payment[id]"
                                            value="{{ old('payment_id', $contract->contract_payments->id ?? '') }}">


                                        @if ($renew)
                                            <input type="hidden" name="contract[renewal_count]"
                                                value="{{ $contract->renewal_count + 1 }}">
                                            <input type="hidden" name="contract[renewal]" value="{{ $renew }}">
                                        @endif


                                        <div class="bs-stepper-header" role="tablist">
                                            <!-- your steps here -->
                                            <div class="step" data-target="#lead-step">
                                                <button type="button" class="step-trigger" role="tab"
                                                    aria-controls="lead-step" id="lead-step-trigger">
                                                    <span class="bs-stepper-circle"><i class="far fa-user"></i></span>
                                                    <span class="bs-stepper-label">Lead</span>
                                                </button>
                                            </div>
                                            <div class="line"></div>
                                            <div class="step" data-target="#property-step">
                                                <button type="button" class="step-trigger" role="tab"
                                                    aria-controls="property-step" id="property-step-trigger">
                                                    <span class="bs-stepper-circle"><i class="far fa-building"></i></span>
                                                    <span class="bs-stepper-label">Property</span>
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
                                            <div class="step" data-target="#contract-step">
                                                <button type="button" class="step-trigger" role="tab"
                                                    aria-controls="contract-step" id="contract-step-trigger">
                                                    <span class="bs-stepper-circle"><i
                                                            class="fas fa-file-contract"></i></span>
                                                    <span class="bs-stepper-label">Contract</span>
                                                </button>
                                            </div>
                                            <div class="line"></div>
                                            <div class="step" data-target="#rental-step">
                                                <button type="button" class="step-trigger" role="tab"
                                                    aria-controls="rental-step" id="rental-step-trigger">
                                                    <span class="bs-stepper-circle"><i
                                                            class="fas fa-house-user"></i></span>
                                                    <span class="bs-stepper-label">Rental Payment</span>
                                                </button>
                                            </div>
                                            <div class="line"></div>
                                            <div class="step" data-target="#otc-step">
                                                <button type="button" class="step-trigger" role="tab"
                                                    aria-controls="otc-step" id="otc-step-trigger">
                                                    <span class="bs-stepper-circle"><i
                                                            class="fas fa-file-invoice-dollar"></i></span>
                                                    <span class="bs-stepper-label">OTC</span>
                                                </button>
                                            </div>
                                            <div class="line"></div>
                                            <div class="step" data-target="#payment-step">
                                                <button type="button" class="step-trigger" role="tab"
                                                    aria-controls="payment-step" id="payment-step-trigger">
                                                    <span class="bs-stepper-circle"><i
                                                            class="fas fa-dollar-sign"></i></span>
                                                    <span class="bs-stepper-label">Payment</span>
                                                </button>
                                            </div>
                                            <div class="line"></div>
                                            <div class="step" data-target="#rentalreceivable-step">
                                                <button type="button" class="step-trigger" role="tab"
                                                    aria-controls="rentalreceivable-step"
                                                    id="rentalreceivable-step-trigger">
                                                    <span class="bs-stepper-circle"><i
                                                            class="fas fa-dollar-sign"></i></span>
                                                    <span class="bs-stepper-label">Rental Receivable</span>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="bs-stepper-content card p-3">
                                            <!-- your steps content here -->
                                            <div id="lead-step" class="content step-content" data-step="0"
                                                role="tabpanel" aria-labelledby="lead-step-trigger">
                                                <div class="form-group row">
                                                    <div class="col-md-4">
                                                        <label for="exampleInputEmail1" class="asterisk">Project
                                                            No</label>
                                                        <input type="number" class="form-control"
                                                            name="contract[project_number]"
                                                            value="{{ old('project_number', $contract->project_number ?? '') }}"
                                                            id="project_no" placeholder="Project No" required>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="exampleInputEmail1" class="asterisk">Company</label>
                                                        {{-- <div class="input-group "> --}}
                                                        <select class="form-control select2" name="contract[company_id]"
                                                            id="vc_company_id" required>
                                                            <option value="">Select Company</option>
                                                            @foreach ($companies as $company)
                                                                <option value="{{ $company->id }}"
                                                                    {{ (string) old('company_id', $contract->company_id ?? '') === (string) $company->id ? 'selected' : '' }}>
                                                                    {{ $company->company_name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        {{-- <span class="input-group-append">
                                                            <button type="button" class="btn btn-info btn-flat"
                                                                data-toggle="modal" data-target="#modal-company"><i
                                                                    class="fa fa-plus"></i></button>
                                                        </span> --}}
                                                        {{-- </div> --}}
                                                    </div>
                                                    {{-- @dump($vendors) --}}

                                                    <div class="col-md-4">
                                                        <label class="asterisk">Vendor</label>
                                                        {{-- <div class="input-group"> --}}
                                                        <select class="form-control select2" name="contract[vendor_id]"
                                                            id="vc_vendor_id"
                                                            data-selected="{{ old('vendor_id', $contract->vendor_id ?? '') }}"
                                                            required>
                                                            <option value="">Select Vendor</option>
                                                            @foreach ($vendors as $vendor)
                                                                <option value="{{ $vendor->id }}"
                                                                    {{ (string) old('vendor_id', $contract->vendor_id ?? '') === (string) $vendor->id ? 'selected' : '' }}>
                                                                    {{ $vendor->vendor_name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        {{-- <span class="input-group-append">
                                                            <button type="button" class="btn btn-info btn-flat"
                                                                id="addVendorButton"><i class="fa fa-plus"></i></button>
                                                        </span>
                                                    </div> --}}
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-md-4">
                                                        <label for="exampleInputEmail1" class="asterisk">Contract
                                                            Type</label>
                                                        <select class="form-control select2"
                                                            name="contract[contract_type_id]" id="contract_type"
                                                            data-selected="{{ old('contract_type_id', $contract->contract_type_id ?? '') }}"
                                                            required>
                                                            @foreach ($contractTypes as $contractType)
                                                                <option value="{{ $contractType->id }}"
                                                                    {{ old('contract_type_id', $contract->contract_type_id ?? '') === $contractType->id ? 'selected' : '' }}>
                                                                    {{ $contractType->contract_type }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="exampleInputEmail1" class="asterisk">Contact
                                                            Person</label>
                                                        <input type="text" class="form-control"
                                                            value="{{ old('contact_person', $contract->contact_person ?? 'Adil') }}"
                                                            id="contact_person" name="contract[contact_person]"
                                                            placeholder="Contact Person" required>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="exampleInputEmail1" class="asterisk">Contact
                                                            No</label>
                                                        <input type="text" class="form-control"
                                                            value="{{ old('contact_number', $contract->contact_number ?? '971568856995') }}"
                                                            id="contact_no" name="contract[contact_number]"
                                                            placeholder="Contact No" required>
                                                    </div>
                                                    <!-- <div class="col-md-4">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <label for="exampleInputEmail1">Agent</label>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <select class="form-control select2" name="agent" id="agent">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <option value="">Select Agent</option>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <option value="1">Agent 1</option>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            </select>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        </div> -->

                                                </div>
                                                <div class="form-group row indirect_div d-none">
                                                    <div class="col-md-4">
                                                        <label for="exampleInputEmail1" class="">Indirect
                                                            Company</label>
                                                        <select class="form-control select2"
                                                            name="contract[indirect_company_id]" id="indirect_company_id">
                                                            {{-- <option value="">Select Company</option> --}}
                                                            @foreach ($companies->whereIn('id', [3, 6]) as $company)
                                                                <option value="{{ $company->id }}"
                                                                    {{ (string) old('contract.indirect_company_id', $contract->indirect_company_id ?? '') === (string) $company->id ? 'selected' : '' }}>
                                                                    {{ $company->company_name }}
                                                                </option>
                                                            @endforeach

                                                        </select>

                                                    </div>
                                                    {{-- @dump($indirectct); --}}
                                                    <div class="col-md-4">
                                                        <label for="exampleInputEmail1" class="">Indirect Project
                                                            No</label>
                                                        <select class="form-control select2"
                                                            name="contract[indirect_contract_id]"
                                                            id="indirect_contract_id"
                                                            data-selected="{{ $contract->indirect_contract_id ?? '' }}">
                                                            {{-- @dump($indirectct); --}}
                                                            <option value="">Select Indirect Prject</option>
                                                            @foreach ($indirect as $ct)
                                                                <option value="{{ $ct->id }}"
                                                                    {{ (string) old('contract.indirect_contract_id', $contract->indirect_contract_id ?? '') === (string) $ct->id ? 'selected' : '' }}>
                                                                    Project - {{ $ct->project_number }}
                                                                </option>
                                                            @endforeach

                                                        </select>
                                                    </div>
                                                    <input type="hidden" name="contract[indirect_status]"
                                                        id="indirect_status" value="">

                                                </div>
                                                <button type="button" class="btn btn-info nextBtn">Next</button>
                                            </div>
                                            <div id="property-step" class="content step-content" data-step="1"
                                                role="tabpanel" aria-labelledby="property-step-trigger">
                                                <div class="form-group row">
                                                    <div class="col-md-4">
                                                        <label for="exampleInputEmail1" class="asterisk">Area</label>

                                                        {{-- <div class="input-group"> --}}
                                                        <select class="form-control select2" name="contract[area_id]"
                                                            id="vc_area_id"
                                                            data-selected="{{ old('area_id', $contract->area_id ?? '') }}"
                                                            required>
                                                            <option value="">Select Area</option>
                                                            @foreach ($areas as $area)
                                                                <option value="{{ $area->id }}"
                                                                    {{ (string) old('area_id', $contract->area_id ?? '') === (string) $area->id ? 'selected' : '' }}>
                                                                    {{ $area->area_name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        {{-- <span class="input-group-append">
                                                            <button type="button" class="btn btn-info btn-flat"
                                                                id="addAreaButton"><i class="fa fa-plus"></i></button>
                                                        </span>
                                                    </div> --}}
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="exampleInputEmail1" class="asterisk">Locality</label>

                                                        {{-- <div class="input-group"> --}}
                                                        <select class="form-control select2" name="contract[locality_id]"
                                                            id="vc_locality_id"
                                                            data-selected="{{ old('locality_id', $contract->locality_id ?? '') }}"
                                                            required>
                                                            <option value="">Select Locality</option>
                                                        </select>
                                                        {{-- <span class="input-group-append">
                                                            <button type="button" class="btn btn-info btn-flat"
                                                                id="addLocalityButton"><i class="fa fa-plus"></i></button>
                                                        </span>
                                                    </div> --}}

                                                    </div>

                                                    {{-- <div class="col-md-4">
                                                    <label for="exampleInputEmail1">Property type</label>

                                                    <div class="input-group">
                                                        <select class="form-control select2" name="property_type"
                                                            id="vc_property_type_id">

                                                        </select>
                                                        <span class="input-group-append">
                                                            <button type="button" class="btn btn-info btn-flat"
                                                                id="addPropertyTypeButton"><i
                                                                    class="fa fa-plus"></i></button>
                                                        </span>
                                                    </div>
                                                </div> --}}

                                                    <div class="col-md-4">
                                                        <label for="exampleInputEmail1" class="asterisk">Property</label>

                                                        {{-- <div class="input-group"> --}}
                                                        <select class="form-control select2" name="contract[property_id]"
                                                            id="vc_property_id"
                                                            data-selected="{{ old('property_id', $contract->property_id ?? '') }}"
                                                            required>
                                                            <option value="">Select Property</option>
                                                        </select>
                                                        {{-- <span class="input-group-append">
                                                            <button type="button" class="btn btn-info btn-flat"
                                                                id="addPropertyButton"><i class="fa fa-plus"></i></button>
                                                        </span>
                                                    </div> --}}
                                                    </div>
                                                </div>
                                                <div class="form-group row">



                                                    <div class="col-md-4">
                                                        <label for="exampleInputEmail1" class="asterisk">Grace period (in
                                                            months)</label>
                                                        <input type="number" class="form-control"
                                                            name="detail[grace_period]" id="exampleInputEmail1"
                                                            placeholder="Grace period"
                                                            value="{{ old('grace_period', $contract->contract_detail->grace_period ?? 1) }}"
                                                            required>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="exampleInputEmail1" class="asterisk">Closing
                                                            Date</label>
                                                        <div class="input-group date" id="closingdate"
                                                            data-target-input="nearest">
                                                            <input type="text"
                                                                class="form-control datetimepicker-input"
                                                                data-target="#closingdate" placeholder="dd-mm-YYYY"
                                                                name="detail[closing_date]"
                                                                value="{{ old('closing_date', $contract->contract_detail->closing_date ?? '') }}"
                                                                required />
                                                            <div class="input-group-append" data-target="#closingdate"
                                                                data-toggle="datetimepicker">
                                                                <div class="input-group-text"><i
                                                                        class="fa fa-calendar"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                {{-- <div class="form-group row">
                                                </div> --}}

                                                <button class="btn btn-info prevBtn" type="button">Previous</button>
                                                <button type="button" class="btn btn-info nextBtn">Next</button>
                                            </div>
                                            <div id="unit-step" class="content step-content" data-step="2"
                                                role="tabpanel" aria-labelledby="unit-step-trigger">
                                                <div class="form-group row">
                                                    <div class="col-md-4">
                                                        <label for="exampleInputEmail1" class="asterisk">No. of
                                                            Units</label>
                                                        <input type="number" name="unit[no_of_units]"
                                                            class="form-control" id="no_of_units"
                                                            placeholder="No. of Units"
                                                            value="{{ old('no_of_units', $contract->contract_unit->no_of_units ?? null) }}"
                                                            required>
                                                    </div>
                                                    <div class="col-md-4 mt-4">
                                                        <div class="icheck-success d-inline">
                                                            <input type="checkbox" id="fullBuilding"
                                                                name="unit[building_type]" class="fullBuildCheck"
                                                                value="1"
                                                                {{ old('unit.building_type', $contract->contract_unit->building_type ?? '') == 1 ? 'checked' : '' }}>
                                                            <label class="labelpermission" for="fullBuilding"> Full
                                                                Building
                                                            </label>
                                                        </div>

                                                        <div class="icheck-success d-inline">
                                                            <input type="checkbox" id="watchmanrm"
                                                                class="watchmanrmcheck" value="1"
                                                                name="unit[watchman_room]"
                                                                {{ old('unit.watchman_room', $contract->contract_unit->watchman_room ?? '') == 1 ? 'checked' : '' }}>
                                                            <label class="labelpermission" for="watchmanrm"> Watchman Room
                                                            </label>
                                                        </div>

                                                        <div class="icheck-success d-inline">
                                                            <input type="radio" id="btob" class="btobcheck"
                                                                value="1" name="unit[business_type]"
                                                                {{ old('unit.business_type', $contract->contract_unit->business_type ?? '') == 1 ? 'checked' : '' }}>
                                                            <label class="labelpermission" for="btob"> B2B </label>
                                                        </div>


                                                        <div class="icheck-success d-inline">
                                                            <input type="radio" id="btoc" class="btoccheck"
                                                                value="2" name="unit[business_type]"
                                                                {{ old('unit.business_type', $contract->contract_unit->business_type ?? null) == 2 ? 'checked' : (!isset($contract) ? 'checked' : '') }}>
                                                            <label class="labelpermission" for="btoc"> B2C </label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr>
                                                <div class="normalBuilding">
                                                    <div id="append-div">
                                                        @if (isset($contract) && isset($contract->contract_unit_details))
                                                            @include(
                                                                'admin.projects.contract.includes.edit_unitdet',
                                                                [
                                                                    'contract_units_details' =>
                                                                        $contract->contract_unit_details ?? [],
                                                                    'unittypedp' => $UnitTypes,
                                                                    'propertytypedp' => $property_types,
                                                                    'unitstatusdp' => $UnitStatus,
                                                                    'unitsizeunitdp' => $UnitSizeUnit,
                                                                ]
                                                            )
                                                        @endif

                                                    </div>
                                                </div>
                                                <div class="fullBuilding">
                                                    <div id="append-div-fullb">

                                                    </div>
                                                </div>

                                                <button class="btn btn-info prevBtn" type="button">Previous</button>
                                                <button class="btn btn-info nextBtn" type="button">Next</button>
                                            </div>
                                            <div id="contract-step" class="content step-content" data-step="3"
                                                role="tabpanel" aria-labelledby="contract-step-trigger">
                                                <div class="form-group row">
                                                    <div class="col-md-2">
                                                        <label for="exampleInputEmail1">Contract fee</label>
                                                        <input type="number" class="form-control"
                                                            name="detail[contract_fee]" id="contract_fee"
                                                            placeholder="Contract fee"
                                                            value="{{ old('contract_fee', $contract->contract_detail->contract_fee ?? '') }}">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label for="exampleInputEmail1">Ejari</label>
                                                        <input type="number" class="form-control" id="ejari"
                                                            name="detail[ejari]"
                                                            value="{{ old('deposit', $contract->contract_detail->ejari ?? '') }}"
                                                            placeholder="Ejari">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label for="exampleInputEmail1" class="asterisk">Start
                                                            Date</label>
                                                        <div class="input-group date" id="startdate"
                                                            data-target-input="nearest">
                                                            <input type="text"
                                                                class="form-control datetimepicker-input startdate"
                                                                data-target="#startdate" placeholder="dd-mm-YYYY"
                                                                name="detail[start_date]" required
                                                                value="{{ old('start_date', $contract->contract_detail->start_date ?? '') }}" />
                                                            <div class="input-group-append" data-target="#startdate"
                                                                data-toggle="datetimepicker">
                                                                <div class="input-group-text"><i
                                                                        class="fa fa-calendar"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label for="exampleInputEmail1" class="asterisk">Duration in
                                                            Months</label>
                                                        <input type="number" class="form-control" id="duration_months"
                                                            name="detail[duration_in_months]"
                                                            placeholder="Duration in Months"
                                                            value="{{ old('duration_in_months', $contract->contract_detail->duration_in_months ?? '13') }}"
                                                            required>
                                                    </div>
                                                    {{-- <div class="col-md-2">
                                                        <label for="exampleInputEmail1">Duration in Days</label>
                                                        <input type="number" class="form-control" id="duration_days"
                                                            name="detail[duration_in_days]" placeholder="Duration in Days"
                                                            value="0">
                                                    </div> --}}
                                                    <div class="col-md-3">
                                                        <label for="exampleInputEmail1" class="asterisk">End Date</label>
                                                        <div class="input-group date" id="enddate"
                                                            data-target-input="nearest">
                                                            <input type="text"
                                                                class="form-control datetimepicker-input enddate"
                                                                name="detail[end_date]" placeholder="dd-mm-YYYY" readonly
                                                                onfocus="this.blur()"
                                                                value="{{ old('end_date', $contract->contract_detail->end_date ?? '') }}"
                                                                required />
                                                            <div class="input-group-append" data-toggle="datetimepicker">
                                                                <div class="input-group-text"><i
                                                                        class="fa fa-calendar"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <button class="btn btn-info prevBtn" type="button">Previous</button>
                                                <button class="btn btn-info nextBtn" type="button">Next</button>
                                            </div>
                                            <div id="rental-step" class="content step-content" data-step="4"
                                                role="tabpanel" aria-labelledby="rental-step-trigger">
                                                <div class="form-group row">
                                                    <div class="col-md-4">
                                                        <label for="exampleInputEmail1" class="asterisk">Rent per
                                                            annum</label>
                                                        <input type="number" class="form-control" id="rent_per_annum"
                                                            name="rentals[rent_per_annum_payable]"
                                                            placeholder="Rent per annum"
                                                            value="{{ old('rent_per_annum_payable', toNumeric($contract->contract_rentals->rent_per_annum_payable ?? '')) }}"
                                                            required readonly>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label for="exampleInputEmail1" class="asterisk">Commission
                                                            %</label>
                                                        <input type="number" class="form-control" id="commission_perc"
                                                            name="rentals[commission_percentage]"
                                                            placeholder="Commission %"
                                                            value="{{ old('commission_percentage', $contract->contract_rentals->commission_percentage ?? '5') }}"
                                                            required>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label for="exampleInputEmail1"
                                                            class="asterisk">Commission</label>
                                                        <input type="number" class="form-control" id="commission"
                                                            name="rentals[commission]" placeholder="Commission"
                                                            value="{{ old('commission', $contract->contract_rentals->commission ?? '') }}"
                                                            required>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label for="exampleInputEmail1" class="asterisk">Deposit %</label>
                                                        <input type="number" class="form-control" id="deposit_perc"
                                                            name="rentals[deposit_percentage]" placeholder="Deposit %"
                                                            value="{{ old('deposit_percentage', $contract->contract_rentals->deposit_percentage ?? '5') }}"
                                                            required>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label for="exampleInputEmail1" class="asterisk">Deposit</label>
                                                        <input type="number" class="form-control" id="deposit"
                                                            name="rentals[deposit]" placeholder="Deposit"
                                                            value="{{ old('deposit', $contract->contract_rentals->deposit ?? '') }}"
                                                            required>
                                                    </div>
                                                </div>
                                                <button class="btn btn-info prevBtn" type="button">Previous</button>
                                                <button class="btn btn-info nextBtn" type="button">Next</button>
                                            </div>
                                            <div id="otc-step" class="content step-content" data-step="5"
                                                role="tabpanel" aria-labelledby="otc-step-trigger">
                                                <div class="form-group row">
                                                    <div class="col-md-3">
                                                        <label for="exampleInputEmail1">Cost of Development</label>
                                                        <input type="number" class="form-control"
                                                            id="cost_of_development" name="otc[cost_of_development]"
                                                            value="{{ old('deposit', $contract->contract_otc->cost_of_development ?? '') }}"
                                                            placeholder="Cost of Development">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label for="exampleInputEmail1">Cost of Beds</label>
                                                        <input type="number" class="form-control" id="cost_of_beds"
                                                            name="otc[cost_of_bed]"
                                                            value="{{ old('deposit', $contract->contract_otc->cost_of_bed ?? '') }}"
                                                            placeholder="Cost of Beds">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label for="exampleInputEmail1">Cost of Mattress</label>
                                                        <input type="number" class="form-control" id="cost_of_mattress"
                                                            name="otc[cost_of_matress]"
                                                            value="{{ old('deposit', $contract->contract_otc->cost_of_matress ?? '') }}"
                                                            placeholder="Cost of Mattress">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label for="exampleInputEmail1">Appliances</label>
                                                        <input type="number" class="form-control" id="appliances"
                                                            name="otc[appliances]"
                                                            value="{{ old('deposit', $contract->contract_otc->appliances ?? '') }}"
                                                            placeholder="Appliances">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-md-3">
                                                        <label for="exampleInputEmail1">Decoration</label>
                                                        <input type="number" class="form-control" id="decoration"
                                                            name="otc[decoration]"
                                                            value="{{ old('deposit', $contract->contract_otc->decoration ?? '') }}"
                                                            placeholder="Decoration">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label for="exampleInputEmail1">Dewa Deposit</label>
                                                        <input type="number" class="form-control" id="dewa_deposit"
                                                            name="otc[dewa_deposit]"
                                                            value="{{ old('deposit', $contract->contract_otc->dewa_deposit ?? '') }}"
                                                            placeholder="Dewa Deposit">
                                                    </div>

                                                    <div class="col-md-3">
                                                        <label for="exampleInputEmail1">Cost of Cabinets</label>
                                                        <input type="number" class="form-control" id="cost_of_cabinets"
                                                            name="otc[cost_of_cabinets]"
                                                            value="{{ old('deposit', $contract->contract_otc->cost_of_cabinets ?? '') }}"
                                                            placeholder="Cost of Cabinets">
                                                    </div>
                                                </div>
                                                <button class="btn btn-info prevBtn" type="button">Previous</button>
                                                <button class="btn btn-info nextBtn" type="button">Next</button>
                                            </div>
                                            <div id="payment-step" class="content step-content" data-step="6"
                                                role="tabpanel" aria-labelledby="payment-step-trigger">
                                                <div class="form-group row">
                                                    <div class="col-md-4">
                                                        <label for="exampleInputEmail1" class="asterisk">No. of
                                                            Installments</label>
                                                        {{-- <div class="input-group"> --}}
                                                        <select class="form-control select2"
                                                            name="payment[installment_id]" id="no_of_installments"
                                                            required>
                                                            <option value="">Select</option>
                                                            @foreach ($installments as $installment)
                                                                <option value="{{ $installment->id }}"
                                                                    data-interval="{{ $installment->interval }}"
                                                                    {{ ($contract->contract_payments->installment_id ?? '') == $installment->id ? 'selected' : '' }}>
                                                                    {{ $installment->installment_name }}</option>
                                                            @endforeach
                                                        </select>
                                                        {{-- <span class="input-group-append">
                                                            <button type="button" class="btn btn-info btn-flat"
                                                                id="addInstallmentButton"><i
                                                                    class="fa fa-plus"></i></button>
                                                        </span>
                                                    </div> --}}
                                                        {{-- <div class="input-group">
                                                        <select class="form-control select2" name="no_of_installments"
                                                            id="no_of_installments">

                                                        </select>
                                                        <span class="input-group-append">
                                                            <button type="button" class="btn btn-info btn-flat"
                                                                id="addInstallmentButton"><i
                                                                    class="fa fa-plus"></i></button>
                                                        </span>
                                                    </div> --}}
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="exampleInputEmail1">Interval</label>
                                                        <input type="text" class="form-control" id="interval"
                                                            name="payment[interval]"
                                                            value="{{ $contract->contract_payments->interval ?? '' }}"
                                                            placeholder="Interval" readonly>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="exampleInputEmail1">Beneficiary</label>
                                                        <input type="text" class="form-control" id="beneficiary"
                                                            name="payment[beneficiary]"
                                                            value="{{ $contract->contract_payments->beneficiary ?? '' }}"
                                                            placeholder="Beneficiary">
                                                    </div>
                                                </div>
                                                <hr>
                                                <div class="payment_details">
                                                    @if (isset($contract) && isset($contract->contract_payment_details))
                                                        @include(
                                                            'admin.projects.contract.includes.edit_paymentdet',
                                                            [
                                                                'contract_payment_details' =>
                                                                    $contract->contract_payment_details ?? [],
                                                                'payment_modes' => $paymentmodes,
                                                                'banks' => $banks,
                                                            ]
                                                        )
                                                    @endif
                                                </div>
                                                <button class="btn btn-info prevBtn" type="button">Previous</button>
                                                <button class="btn btn-info nextBtn" type="button">Next</button>
                                            </div>
                                            <div id="rentalreceivable-step" class="content step-content" data-step="7"
                                                role="tabpanel" aria-labelledby="rentalreceivable-step-trigger">
                                                <div class=" p-3 mb-3">
                                                    <!-- info row -->
                                                    <div class="row invoice-info">
                                                        <div class="col-sm-6 float-left">
                                                            <strong>Payable Details</strong>
                                                            <address style="white-space: nowrap;">
                                                                Total Contract Amt - <input class="total_contract_amount"
                                                                    readonly="" value="0.00"
                                                                    style="border:none;"><br>
                                                                Commission - <input class="commssion_final" readonly=""
                                                                    value="0.00" style="border:none;"><br>
                                                                Refundable Deposit - <input class="deposit_final"
                                                                    readonly="" value="0.00"
                                                                    style="border:none;"><br>
                                                                Contract Fee - <input class="contract_final"
                                                                    readonly="" value="0.00"
                                                                    style="border:none;"><br>
                                                                Total Payment to Vendor - <input class="payment_to_vendor"
                                                                    name="rentals[total_payment_to_vendor]" readonly=""
                                                                    value="0.00" style="border:none;"><br>
                                                                Total OTC - <input class="total_otc_payable"
                                                                    name="rentals[total_otc]" readonly=""
                                                                    value="0.00" style="border:none;"><br>
                                                                Final Cost - <input class="final_cost" readonly=""
                                                                    name="rentals[final_cost]" value="0.00"
                                                                    style="border:none;"><br>
                                                                Initial Investment - <input class="initial_inv"
                                                                    name="rentals[initial_investment]" readonly=""
                                                                    value="0.00" style="border:none;"><br>
                                                            </address>
                                                        </div>

                                                        <div class="col-sm-6 float-right">
                                                            <span class="float-lg-right"><strong>Receivable
                                                                    Details</strong>
                                                                <address style="white-space: nowrap;">
                                                                    Expected Rental PM - <input
                                                                        class="total_rent_receivable"
                                                                        name="rentals[rent_receivable_per_month]"
                                                                        readonly="" value="0.00"
                                                                        style="border:none;">
                                                                    <br>
                                                                    No. Of Months - <input class="no_of_months_final"
                                                                        readonly="" value="0.00"
                                                                        style="border:none;">
                                                                    <br>
                                                                    Total Rental - <input class="total_rental"
                                                                        name="rentals[rent_receivable_per_annum]"
                                                                        readonly="" value="0.00"
                                                                        style="border:none;">
                                                                    <br>
                                                                </address>
                                                            </span>
                                                        </div>
                                                        <!-- /.col -->

                                                    </div>
                                                </div>
                                                <!-- /.row -->
                                                <div class="form-group row rentPerUnitFF">
                                                    @if (isset($contract) && isset($contract->contract_unit_details) && ($contract->contract_type_id ?? null) == '2')
                                                        @include(
                                                            'admin.projects.contract.includes.edit_profitdet',
                                                            [
                                                                'contract_units_details' =>
                                                                    $contract->contract_unit_details ?? [],
                                                            ]
                                                        )
                                                    @endif
                                                </div>
                                                <input type="hidden" name="unit[total_subunit_count_per_contract]"
                                                    id="subunit_count_per_contract"
                                                    value="{{ isset($contract) ? $contract->contract_unit->total_subunit_count_per_contract : 0 }}">
                                                <div class="form-group row">
                                                    <div class="col-md-2 rentPartition">
                                                        <label for="exampleInputEmail1" class="asterisk">Rent per
                                                            Partition</label>
                                                        {{-- @dd($contract); --}}
                                                        <input type="number" class="form-control"
                                                            name="unit_detail[rent_per_partition]" id="rent_per_part"
                                                            placeholder="Rent per Partition"
                                                            value="{{ isset($contract) ? toNumeric($contract->totals['prj_rent_per_partition']) : '' }}"
                                                            required>
                                                    </div>
                                                    <div class="col-md-2 rentBedspace">
                                                        <label for="exampleInputEmail1" class="asterisk">Rent per
                                                            Bedspace</label>
                                                        <input type="number" class="form-control"
                                                            name="unit_detail[rent_per_bedspace]" id="rent_per_bs"
                                                            placeholder="Rent per Bedspace"
                                                            value="{{ isset($contract) ? toNumeric($contract->totals['prj_rent_per_bedspace']) : '' }}"
                                                            required>
                                                    </div>
                                                    <div class="col-md-2 rentRoom">
                                                        <label for="exampleInputEmail1" class="asterisk">Rent per
                                                            Room</label>
                                                        <input type="number" class="form-control"
                                                            name="unit_detail[rent_per_room]" id="rent_per_room"
                                                            placeholder="Rent per Room"
                                                            value="{{ isset($contract) ? toNumeric($contract->totals['prj_rent_per_room']) : '' }}"
                                                            required>
                                                    </div>
                                                    <div class="col-md-2 rentFlat">
                                                        <label for="exampleInputEmail1" class="asterisk">Rent per
                                                            Flat</label>
                                                        <input type="number" class="form-control"
                                                            name="unit_detail[rent_per_flat]" id="rent_per_flat"
                                                            placeholder="Rent per Flat"
                                                            value="{{ isset($contract) ? toNumeric($contract->totals['prj_rent_per_flat']) : '' }}"
                                                            required>
                                                    </div>
                                                    {{-- <div class="col-md-3">
                                                        <label for="exampleInputEmail1">Receivable Start Date</label>
                                                        <div class="input-group date" id="receivable_start_date"
                                                            data-target-input="nearest">
                                                            <input type="text"
                                                                class="form-control datetimepicker-input receivable_start_date"
                                                                data-target="#receivable_start_date"
                                                                name="rentals[receivable_start_date]"
                                                                placeholder="dd-mm-YYYY" required />
                                                            <div class="input-group-append"
                                                                data-target="#receivable_start_date"
                                                                data-toggle="datetimepicker">
                                                                <div class="input-group-text"><i
                                                                        class="fa fa-calendar"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div> --}}
                                                    <div class="col-md-2">
                                                        <label for="exampleInputEmail1" class="asterisk">Rent
                                                            installments</label>
                                                        <select class="form-control select2"
                                                            name="rentals[receivable_installments]" id="rent_installments"
                                                            required>
                                                            <option value="">Select</option>
                                                            @foreach ($installments as $installment)
                                                                <option value="{{ $installment->id }}"
                                                                    data-interval="{{ $installment->interval }}"
                                                                    {{ ($contract->contract_rentals->receivable_installments ?? '') == $installment->id ? 'selected' : '' }}>
                                                                    {{ $installment->installment_name }}</option>
                                                            @endforeach
                                                        </select>
                                                        {{-- <input type="number" class="form-control"
                                                            name="rentals[receivable_installments]" max="14"
                                                            id="rent_installments" placeholder="Rent installments"
                                                            oninput="validateMax(this)"
                                                            value="{{ $contract->contract_rentals->receivable_installments }}"
                                                            required> --}}
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-md-3">
                                                        <label for="exampleInputEmail1">ROI%</label>
                                                        <input type="text" class="form-control" id="roi"
                                                            name="rentals[roi_perc]" placeholder="ROI" readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label for="exampleInputEmail1">Expected Profit</label>
                                                        <input type="text" class="form-control" id="expected_profit"
                                                            name="rentals[expected_profit]" placeholder="Expected Profit"
                                                            readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label for="exampleInputEmail1">Profit%</label>
                                                        <input type="text" class="form-control" id="profit"
                                                            name="rentals[profit_percentage]" placeholder="Profit"
                                                            readonly>
                                                    </div>
                                                </div>

                                                <div class="receivable_maindiv card col-md-12">
                                                    <div class="receivable_details p-4">
                                                        <h4>Rent Receivables</h4>
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <label class="asterisk">Payment Date</label>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <label class="asterisk">Payment Amount</label>
                                                            </div>
                                                        </div>

                                                        @if (isset($contract) && isset($contract->contract_payment_receivables))
                                                            @include(
                                                                'admin.projects.contract.includes.edit_paymentRec',
                                                                [
                                                                    'contract_payment_receivables' =>
                                                                        $contract->contract_payment_receivables ??
                                                                        [],
                                                                ]
                                                            )
                                                        @endif

                                                    </div>

                                                    <span class="text-danger error-text-installment p-4">
                                                        Please adjust the amount total rent is
                                                        <input class="total_rental text-bold"readonly="" value="0.00"
                                                            style="border:none;width: 8% !important;">
                                                        but we are getting through installments :
                                                        <input class="total_rental_inst text-bold"readonly=""
                                                            value="0.00" style="border:none;">
                                                    </span>
                                                </div>


                                                <br>
                                                <br>
                                                <button class="btn btn-info prevBtn" type="button">Previous</button>
                                                <button type="submit"
                                                    class="btn btn-info contractFormSubmit">Submit</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <!-- /.card-body -->
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
    <!-- /.content-wrapper -->
@endsection
@section('custom_js')
    <script src="{{ asset('assets/moment/moment.min.js') }}"></script>
    <!-- Tempusdominus Bootstrap 4 -->
    <script src="{{ asset('assets/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>
    <!-- date-range-picker -->
    <script src="{{ asset('assets/daterangepicker/daterangepicker.js') }}"></script>
    <!-- BS-Stepper -->
    <script src="{{ asset('assets/bs-stepper/js/bs-stepper.min.js') }}"></script>

    @include('admin.projects.contract.includes.stepper-validation')
    @include('admin.projects.contract.includes.form-js')
    @include('admin.projects.contract.includes.form-submit')


    {{-- Companywise dropdown change --}}
    <script>
        let allVendors = @json($vendors);
        let allProperties = @json($properties);
        let allAreas = @json($areas);
        let allLocalities = @json($localities);

        $(document).ready(function() {
            const companyId = "{{ $contract->company_id ?? '' }}";
            const vendorVal = "{{ $contract->vendor_id ?? '' }}";
            const areaVal = "{{ $contract->area_id ?? '' }}";
            const localityVal = "{{ $contract->locality_id ?? '' }}";
            const propertyVal = "{{ $contract->property_id ?? '' }}";

            // contractCompanyChange(companyId, vendorVal, areaVal, null, localityVal);
            contractCompanyChange(companyId);
            contractAreaChange(areaVal, localityVal, propertyVal);
        });

        $(document).on('change', '#vc_company_id', function() {
            CompanyChange($(this));
        });
        $(document).on('change', '#indirect_company_id', function() {
            fileterCompanies();
        });

        function CompanyChange(ele) {
            const companyId = $(ele).val();
            const companyName = $(ele).find('option:selected').text().trim();

            // // store clean globals
            lastAddedCompanyId = companyId;
            lastAddedCompanyName = companyName;

            contractCompanyChange(companyId, null);

        }

        function contractCompanyChange(companyId) {
            let options = '<option value="">Select Vendor</option>';
            let option1 = '<option value="">Select Area</option>';
            filterIndirectCompany();


            bankOptionByCompany();
            // allVendors
            //     .filter(v => v.company_id == companyId)
            //     .forEach(v => {
            //         const selected = (v.id == vendorVal) ? 'selected' : '';
            //         options +=
            //             `<option value="${v.id}" ${selected}>${v.vendor_name}</option>`;
            //     });
            // $('#vc_vendor_id').html(options).trigger('change');

            // allAreas
            //     .filter(a => a.company_id == companyId)
            //     .forEach(a => {
            //         const selected = (a.id == areaVal) ? 'selected' : '';
            //         option1 += `<option value="${a.id}" ${selected}>${a.area_name}</option>`;
            //     });
            // $('#vc_area_id').html(option1).trigger('change');
            // contractAreaChange(areaVal, localityVal);

        }
        $('#vc_area_id').on('change', function() {
            let areaId = $(this).val();
            let areaName = $(this).find('option:selected').text().trim();

            lastAddedAreaName = areaName;
            lastAddedAreaId = areaId;

            contractAreaChange(areaId, null); // reset areaVal when adding

        });

        function contractAreaChange(areaId, localityVal, propertyVal) {
            let option3 = '<option value="">Select Locality</option>';

            allLocalities
                .filter(l => l.area_id == areaId)
                .forEach(l => {
                    const selected = (l.id == localityVal) ? 'selected' : '';
                    option3 +=
                        `<option value="${l.id}" ${selected}>${l.locality_name}</option>`;
                });
            $('#vc_locality_id').html(option3).trigger('change');
            contractLocalityChange(localityVal, propertyVal);
        }

        $('#vc_locality_id').on('change', function() {
            let localityId = $(this).val();
            let locaityName = $(this).find('option:selected').text()
                .trim();

            lastAddedLocalityName = locaityName;
            lastAddedLocalityId = localityId;
            contractLocalityChange(localityId, null); // reset areaVal when adding

        });


        function contractLocalityChange(localityId, propertyVal = null) {
            let option4 = '<option value="">Select Property</option>';

            allProperties
                .filter(pt => pt.locality_id == localityId)
                .forEach(pt => {
                    const selected = (pt.id == propertyVal) ? 'selected' : '';
                    option4 +=
                        `<option value="${pt.id}" ${selected}>${pt.property_name}</option>`;
                });
            $('#vc_property_id').html(option4).trigger('change');
        }



        $('#vc_vendor_id').on('change', function() {
            let vendorName = $(this).find('option:selected').text().trim();

            $('#beneficiary').val(vendorName);
        });
    </script>
    {{-- Indirect company filetr --}}
    <script>
        function filterIndirectCompany() {
            // alert("called");
            let ind_cts = @json($indirect);
            let com_id = $("#vc_company_id").val();

            let indirectCompanyId;


            if (com_id == 2 || com_id == 4) {

                $(".indirect_div").removeClass("d-none");

                // REMOVE existing placeholder first
                $("#indirect_company_id option[value='']").remove();

                // ADD only once
                $("#indirect_company_id")
                    .prepend('<option value="">Select Indirect Company</option>')
                    .val('')
                    .trigger('change');

                fileterCompanies();

            } else {

                $(".indirect_div").addClass("d-none");

                $("#indirect_company_id").val("0").trigger('change');

                $("#indirect_contract_id")
                    .html('<option value="">Select Indirect Project</option>')
                    .val("0")
                    .trigger('change');

                $("#indirect_status").val(0);
            }



        }

        function fileterCompanies() {
            let ind_cts = @json($indirect);
            let indirectCompanyId = $("#indirect_company_id").val();
            // $("#indirect_status").val(1);
            let existingIndirectContractId = $("#indirect_contract_id").data('selected');


            let filteredContracts = ind_cts.filter(ct => ct.company_id == indirectCompanyId);
            console.log(filteredContracts);

            let options = '<option value="">Select Indirect Project</option>';
            filteredContracts.forEach(ct => {
                let selected = (ct.id == existingIndirectContractId) ? 'selected' : '';
                options += `<option value="${ct.id}" ${selected}>
                Project - ${ct.project_number}
            </option>`;
            });

            $("#indirect_contract_id").html(options).trigger('change');
        }
    </script>
@endsection
