@extends('admin.layout.admin_master')

@section('custom_css')
    <!-- daterange picker -->

    <link rel="stylesheet" href="{{ asset('assets/daterangepicker/daterangepicker.css') }}">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet" href="{{ asset('assets/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <style>
        .section-title {
            font-size: 15px;
            font-weight: 700;
            color: #17a2b8;
            border-left: 4px solid #17a2b8;
            padding-left: 10px;
            margin-bottom: 20px;
        }

        .form-group label {
            font-weight: 600 !important;
            font-size: 13px;
            color: #444;
            margin-bottom: 4px;
        }

        .card-section {
            border: 1px solid #e3e6ea;
            border-radius: 6px;
            padding: 20px;
            margin-bottom: 20px;
            /* background: #fafbfc; */
        }

        .owner-card .card-header {
            background-color: #17a2b8;
            color: white;
            font-weight: 600;
        }
    </style>
@endsection

@section('content')
    <div class="content-wrapper">

        <!-- Content Header -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Add Tenant</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard.index') }}">Home</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('tenant.index') }}">Tenant</a>
                            </li>
                            <li class="breadcrumb-item active">Add Tenant</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <!-- Main Content -->
        <section class="content">
            <div class="container-fluid">
                <div class="card card-outline card-info">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-user-plus mr-2"></i>New Tenant Registration</h3>
                    </div>

                    <div class="card-body">

                        {{-- Validation Errors --}}
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show">
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                                <strong><i class="fas fa-exclamation-circle mr-1"></i> Please fix the following
                                    errors:</strong>
                                <ul class="mb-0 mt-2">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="#" method="POST" enctype="multipart/form-data" id="tenantForm"
                            data-tenant-id="{{ $tenant->id ?? '' }}">
                            @csrf

                            {{-- ===================== TENANT DETAILS ===================== --}}
                            <div class="card-section shadow  p-4">
                                <p class="section-title"><i class="fas fa-building mr-1"></i> Tenant Details</p>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="asterisk">Tenant Name <small class="text-muted">(As per Trade
                                                    License)</small></label>
                                            <input type="text" name="tenant_name"
                                                class="form-control @error('tenant_name') is-invalid @enderror"
                                                value="{{ old('tenant_name', $tenant->tenant_name ?? '') }}"
                                                placeholder="Enter tenant name" required>
                                            @error('tenant_name')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="asterisk">Email</label>
                                            <input type="email" name="tenant_email"
                                                class="form-control @error('tenant_email') is-invalid @enderror"
                                                value="{{ old('tenant_email', $tenant->tenant_email ?? '') }}"
                                                placeholder="Enter email address">
                                            @error('tenant_email')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="asterisk">Office landline/Mobile</label>
                                            <input type="text" name="tenant_mobile"
                                                class="form-control @error('tenant_mobile') is-invalid @enderror"
                                                value="{{ old('tenant_mobile', $tenant->tenant_mobile ?? '') }}"
                                                placeholder="Enter mobile number">
                                            @error('tenant_mobile')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Nationality</label>
                                            <select name="nationality_id" id="nationality"
                                                class="form-control select2 @error('nationality_id') is-invalid @enderror">
                                                <option value="">-- Select Nationality --</option>
                                                @foreach ($formData['nationalities'] as $nationality)
                                                    <option value="{{ $nationality->id }}"
                                                        {{ old('nationality_id', $tenant->nationality_id ?? '') == $nationality->id ? 'selected' : '' }}>
                                                        {{ $nationality->nationality_name }}</option>
                                                @endforeach
                                            </select>
                                            @error('nationality_id')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Contact Person Department</label>
                                            <input type="text" name="contact_person_department"
                                                class="form-control @error('contact_person_department') is-invalid @enderror"
                                                value="{{ old('contact_person_department') }}"
                                                placeholder="Enter department">
                                            @error('contact_persondepartment')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div> --}}

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Contact Person</label>
                                            <input type="text" name="contact_person"
                                                class="form-control @error('contact_person') is-invalid @enderror"
                                                value="{{ old('contact_person', $tenant->contact_person ?? '') }}"
                                                placeholder="Contact person name">
                                            @error('contact_person')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Contact Email</label>
                                            <input type="email" name="contact_email"
                                                class="form-control @error('contact_email') is-invalid @enderror"
                                                value="{{ old('contact_email', $tenant->contact_email ?? '') }}"
                                                placeholder="Contact person email">
                                            @error('contact_email')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Contact Mobile</label>
                                            <input type="text" name="contact_number"
                                                class="form-control @error('contact_number') is-invalid @enderror"
                                                value="{{ old('contact_number', $tenant->contact_number ?? '') }}"
                                                placeholder="Enter mobile number">
                                            @error('contact_number')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Contact Person Department</label>
                                            <input type="text" name="contact_person_department"
                                                class="form-control @error('contact_person_department') is-invalid @enderror"
                                                value="{{ old('contact_person_department', $tenant->contact_person_department ?? '') }}"
                                                placeholder="Contact person department">
                                            @error('contact_person_department')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Payment Method</label>
                                            <select name="payment_mode_id" id="payment_mode"
                                                class="form-control select2 @error('payment_mode_id') is-invalid @enderror">
                                                <option value="">-- Select Payment Mode --</option>
                                                @foreach ($formData['paymentmodes'] as $mode)
                                                    <option value="{{ $mode->id }}"
                                                        {{ old('payment_mode_id', $tenant->payment_mode_id ?? '') == $mode->id ? 'selected' : '' }}>
                                                        {{ $mode->payment_mode_name }}</option>
                                                @endforeach
                                            </select>
                                            @error('payment_mode_id')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Payment Frequency</label>
                                            <select name="payment_frequency_id" id="payment_frequency"
                                                class="form-control select2 @error('payment_frequency_id') is-invalid @enderror">
                                                <option value="">-- Select Payment Frequency --</option>
                                                @foreach ($formData['profitInterval'] as $interval)
                                                    <option value="{{ $interval->id }}"
                                                        {{ old('payment_frequency_id', $tenant->payment_frequency_id ?? '') == $interval->id ? 'selected' : '' }}>
                                                        {{ $interval->profit_interval_name }}</option>
                                                @endforeach
                                            </select>
                                            @error('payment_frequency_id')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Security Cheque</label>
                                            <div>
                                                <div class="form-check form-check-inline">
                                                    <input
                                                        class="form-check-input @error('security_cheque') is-invalid @enderror"
                                                        type="radio" name="security_cheque" id="security_cheque_yes"
                                                        value="1"
                                                        {{ old('security_cheque', $tenant->security_cheque_status ?? 0) == '1' ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="security_cheque_yes">Yes</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input
                                                        class="form-check-input @error('security_cheque') is-invalid @enderror"
                                                        type="radio" name="security_cheque" id="security_cheque_no"
                                                        value="0"
                                                        {{ old('security_cheque', $tenant->security_cheque_status ?? 0) == '0' ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="security_cheque_no">No</label>
                                                </div>
                                            </div>
                                            @error('security_cheque')
                                                <span class="invalid-feedback d-block">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Address line 1</label>
                                            <input type="text" name="tenant_address"
                                                class="form-control @error('tenant_address') is-invalid @enderror"
                                                value="{{ old('tenant_address', $tenant->tenant_address ?? '') }}"
                                                placeholder="Building / Flat No., Street">
                                            @error('tenant_address')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Address Line 2</label>
                                            <input type="text" name="tenant_street"
                                                class="form-control @error('tenant_street') is-invalid @enderror"
                                                value="{{ old('tenant_street', $tenant->tenant_street ?? '') }}"
                                                placeholder="Area / Community">
                                            @error('tenant_street')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>City</label>
                                            <input type="text" name="tenant_city"
                                                class="form-control @error('tenant_city') is-invalid @enderror"
                                                value="{{ old('tenant_city', $tenant->tenant_city ?? '') }}"
                                                placeholder="City / Emirates">
                                            @error('tenant_city')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- ===================== TRADE LICENSE ===================== --}}
                            @php
                                // Get Trade License document if exists
                                if (isset($tenant)) {
                                    $tradeLicense = $tenant->tenantDocuments->firstWhere('document_type', 3);
                                }
                            @endphp

                            <div class="card-section shadow  p-4">
                                <p class="section-title"><i class="fas fa-file-alt mr-1"></i> Trade License</p>

                                <div class="row">
                                    <!-- Trade License Number -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Trade License Number</label>
                                            <input type="text" name="documents[3][number]" id="trade_license_no"
                                                class="form-control @error('documents.trade_license.number') is-invalid @enderror"
                                                value="{{ old('documents.trade_license.number', $tradeLicense->document_number ?? '') }}"
                                                pattern="[A-Za-z0-9\/-]{5,20}"
                                                title="5–20 characters. Letters, numbers, / or - only">
                                            @error('documents.trade_license.number')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Trade License File Upload -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Trade License Upload</label>
                                            <input type="hidden" name="documents[3][id]"
                                                value="{{ $tradeLicense->id ?? '' }}">
                                            <input type="file" name="documents[3][file]"
                                                class="form-control @error('documents.trade_license.file') is-invalid @enderror"
                                                accept=".pdf,.jpg,.jpeg,.png">
                                            <small class="text-muted">Accepted: PDF, JPG, PNG</small>

                                            @if (isset($tradeLicense) && $tradeLicense->document_path)
                                                <div class="mt-1">
                                                    <a href="{{ asset('storage/' . $tradeLicense->document_path) }}"
                                                        target="_blank">View Current File</a>
                                                </div>
                                            @endif

                                            @error('documents.trade_license.file')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Trade License Issued Date -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Trade License Issued Date</label>
                                            <div class="input-group date" id="tradeLicenseIssuedDate"
                                                data-target-input="nearest">
                                                <input type="text" class="form-control datetimepicker-input"
                                                    name="documents[3][issued]" id="trade_license_issued"
                                                    data-target="#tradeLicenseIssuedDate" placeholder="dd-mm-YYYY"
                                                    value="{{ isset($tradeLicense) && $tradeLicense->issued_date ? \Carbon\Carbon::parse($tradeLicense->issued_date)->format('d-m-Y') : '' }}">
                                                <div class="input-group-append" data-target="#tradeLicenseIssuedDate"
                                                    data-toggle="datetimepicker">
                                                    <div class="input-group-text">
                                                        <i class="fa fa-calendar"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            @error('documents.trade_license.issued')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Trade License Expiry Date -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Trade License Expiry Date</label>
                                            <div class="input-group date" id="tradeLicenseExpiryDate"
                                                data-target-input="nearest">
                                                <input type="text" class="form-control datetimepicker-input"
                                                    name="documents[3][expiry]" id="trade_license_expiry"
                                                    data-target="#tradeLicenseExpiryDate" placeholder="dd-mm-YYYY"
                                                    value="{{ isset($tradeLicense) && $tradeLicense->expiry_date ? \Carbon\Carbon::parse($tradeLicense->expiry_date)->format('d-m-Y') : '' }}">
                                                <div class="input-group-append" data-target="#tradeLicenseExpiryDate"
                                                    data-toggle="datetimepicker">
                                                    <div class="input-group-text">
                                                        <i class="fa fa-calendar"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            @error('documents.trade_license.expiry')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- ===================== OWNERS SECTION ===================== --}}
                            <div class="card-section shadow  p-4">
                                <p class="section-title"><i class="fas fa-users mr-1"></i> Owner Documents</p>

                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            @php
                                                $selectedOwners = old('no_of_owners', $tenant->no_of_owners ?? 1);
                                            @endphp

                                            <label>No. of Owners <span class="text-danger">*</span></label>
                                            <select name="no_of_owners" id="owners_count" class="form-control">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <option value="{{ $i }}"
                                                        {{ $selectedOwners == $i ? 'selected' : '' }}>
                                                        {{ $i }}
                                                    </option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div id="owners_section"></div>
                            </div>

                            {{-- ===================== ACTION BUTTONS ===================== --}}
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <a href="{{ route('tenant.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left mr-1"></i> Back
                                </a>
                                <button type="submit" class="btn btn-info px-4" id="submitBtn">
                                    <i class="fas fa-save mr-1"></i> Save Tenant
                                </button>
                            </div>

                        </form>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
        </section>

    </div>
@endsection

@section('custom_js')
    <script src="{{ asset('assets/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/moment/moment.min.js') }}"></script>
    <!-- Tempusdominus Bootstrap 4 -->

    <script src="{{ asset('assets/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>
    <!-- date-range-picker -->

    <script src="{{ asset('assets/daterangepicker/daterangepicker.js') }}"></script>

    <script src="{{ asset('assets/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/jszip/jszip.min.js') }}"></script>
    <script src="{{ asset('assets/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('assets/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('assets/datatables-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('assets/datatables-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('assets/datatables-buttons/js/buttons.colVis.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.22.5/dist/sweetalert2.all.min.js"></script>
    <script>
        $('#tradeLicenseIssuedDate').datetimepicker({
            format: 'DD-MM-YYYY'
        });

        $('#tradeLicenseExpiryDate').datetimepicker({
            format: 'DD-MM-YYYY'
        });
    </script>

    <script>
        let existingOwners = @json($owners ?? []);
        let existingOwnerKeys = Object.keys(existingOwners).map(Number);
        const existingOwnerCount = existingOwnerKeys.length;

        function generateOwners(count) {
            let container = document.getElementById('owners_section');
            container.innerHTML = '';

            let ownerSlots = [];

            if (existingOwnerKeys.length > 0) {
                // EDIT MODE: use real DB indices
                ownerSlots = existingOwnerKeys.slice(0, count);

                // If user increased count beyond existing owners, add new slots
                if (count > existingOwnerKeys.length) {
                    let maxKey = Math.max(...existingOwnerKeys);
                    for (let j = 1; j <= count - existingOwnerKeys.length; j++) {
                        ownerSlots.push(maxKey + j);
                    }
                }
            } else {
                // CREATE MODE: just use 1, 2, 3...
                for (let j = 1; j <= count; j++) {
                    ownerSlots.push(j);
                }
            }

            ownerSlots.forEach(function(ownerIndex, displayIndex) {
                let ownerData = existingOwners[ownerIndex] || {};
                let passport = ownerData[1] || {};
                let emirates = ownerData[2] || {};
                let displayNumber = displayIndex + 1;

                let emiratesIssued = emirates.issued ? moment(emirates.issued, 'YYYY-MM-DD').format('DD-MM-YYYY') :
                    '';
                let emiratesExpiry = emirates.expiry ? moment(emirates.expiry, 'YYYY-MM-DD').format('DD-MM-YYYY') :
                    '';
                let passportIssued = passport.issued ? moment(passport.issued, 'YYYY-MM-DD').format('DD-MM-YYYY') :
                    '';
                let passportExpiry = passport.expiry ? moment(passport.expiry, 'YYYY-MM-DD').format('DD-MM-YYYY') :
                    '';

                container.innerHTML += `
                    <div class="card card-outline card-info owner-card mb-3" id="owner_${ownerIndex}" data-owner-index="${ownerIndex}">
                        <div class="card-header">
                            <i class="fas fa-user mr-1"></i> Owner ${displayNumber} Documents
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <input type="hidden" name="owners[${ownerIndex}][2][id]" value="${emirates.id ?? ''}">

                                <!-- Emirates ID -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Emirates ID Number</label>
                                        <input type="text" name="owners[${ownerIndex}][2][emirates_id]" class="form-control emirates-id"
                                            value="${emirates.number ?? ''}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Emirates ID Upload</label>
                                        <input type="file" name="owners[${ownerIndex}][2][emirates_file]" class="form-control">
                                        ${emirates.file ? `<a href="${emirates.file}" target="_blank">View File</a>` : ''}
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Emirates ID Issued Date</label>
                                        <div class="input-group date" id="emiratesIssued_${ownerIndex}" data-target-input="nearest">
                                            <input type="text"
                                                class="form-control datetimepicker-input"
                                                name="owners[${ownerIndex}][2][emirates_issued]"
                                                data-target="#emiratesIssued_${ownerIndex}"
                                                placeholder="dd-mm-YYYY"
                                                value="${emiratesIssued ?? ''}">
                                            <div class="input-group-append" data-target="#emiratesIssued_${ownerIndex}" data-toggle="datetimepicker">
                                                <div class="input-group-text">
                                                    <i class="fa fa-calendar"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Emirates ID Expiry Date -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Emirates ID Expiry Date</label>
                                        <div class="input-group date" id="emiratesExpiry_${ownerIndex}" data-target-input="nearest">
                                            <input type="text"
                                                class="form-control datetimepicker-input"
                                                name="owners[${ownerIndex}][2][emirates_expiry]"
                                                data-target="#emiratesExpiry_${ownerIndex}"
                                                placeholder="dd-mm-YYYY"
                                                value="${emiratesExpiry ?? ''}">
                                            <div class="input-group-append" data-target="#emiratesExpiry_${ownerIndex}" data-toggle="datetimepicker">
                                                <div class="input-group-text">
                                                    <i class="fa fa-calendar"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Passport -->
                                <input type="hidden" name="owners[${ownerIndex}][1][id]" value="${passport.id ?? ''}">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Passport Number</label>
                                        <input type="text" name="owners[${ownerIndex}][1][passport_number]" class="form-control passport-number"
                                            value="${passport.number ?? ''}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Passport Upload</label>
                                        <input type="file" name="owners[${ownerIndex}][1][passport_file]" class="form-control">
                                        ${passport.file ? `<a href="${passport.file}" target="_blank">View File</a>` : ''}
                                    </div>
                                </div>
                            <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Passport Issued Date</label>
                                        <div class="input-group date" id="passportIssued_${ownerIndex}" data-target-input="nearest">
                                            <input type="text"
                                                class="form-control datetimepicker-input"
                                                name="owners[${ownerIndex}][1][passport_issued]"
                                                data-target="#passportIssued_${ownerIndex}"
                                                placeholder="dd-mm-YYYY"
                                                value="${passportIssued ?? ''}">
                                            <div class="input-group-append" data-target="#passportIssued_${ownerIndex}" data-toggle="datetimepicker">
                                                <div class="input-group-text">
                                                    <i class="fa fa-calendar"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Passport Expiry Date</label>
                                        <div class="input-group date" id="passportExpiry_${ownerIndex}" data-target-input="nearest">
                                            <input type="text"
                                                class="form-control datetimepicker-input"
                                                name="owners[${ownerIndex}][1][passport_expiry]"
                                                data-target="#passportExpiry_${ownerIndex}"
                                                placeholder="dd-mm-YYYY"
                                                value="${passportExpiry ?? ''}">
                                            <div class="input-group-append" data-target="#passportExpiry_${ownerIndex}" data-toggle="datetimepicker">
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
                `;

            });
            ownerSlots.forEach(function(ownerIndex) {
                initOwnerDatePickers(ownerIndex);
            });
            validateAll();
        }

        // Trigger on dropdown change
        document.getElementById('owners_count').addEventListener('change', function() {
            let newCount = parseInt(this.value);
            let tenantId = document.getElementById('tenantForm').dataset.tenantId;

            if (tenantId && newCount < existingOwnerCount) {
                // EDIT MODE + reducing count: show remove buttons instead of wiping blocks
                generateOwners(existingOwnerCount); // keep all existing blocks visible

                for (let i = newCount; i < existingOwnerCount; i++) {
                    let ownerIndex = existingOwnerKeys[i];
                    let ownerBlock = document.getElementById('owner_' + ownerIndex);

                    if (ownerBlock && !ownerBlock.querySelector('.remove-owner-btn')) {
                        let wrapper = document.createElement('div');
                        wrapper.className = 'remove-owner-wrapper text-right mt-2';

                        let button = document.createElement('button');
                        button.type = 'button';
                        button.dataset.owner = ownerIndex;
                        button.dataset.tenantId = tenantId;
                        button.className = 'btn btn-outline-danger btn-sm remove-owner-btn';
                        button.innerText = 'Remove Owner';

                        wrapper.appendChild(button);
                        ownerBlock.querySelector('.card-body').appendChild(wrapper);
                    }
                }
            } else {
                // CREATE MODE or increasing count
                generateOwners(newCount);
            }
        });

        // Generate on page load
        window.onload = function() {
            generateOwners(document.getElementById('owners_count').value);
        };
    </script>
    <script>
        function initOwnerDatePickers(ownerIndex) {
            $('#emiratesIssued_' + ownerIndex).datetimepicker({
                format: 'DD-MM-YYYY'
            });
            $('#emiratesExpiry_' + ownerIndex).datetimepicker({
                format: 'DD-MM-YYYY'
            });
            $('#passportIssued_' + ownerIndex).datetimepicker({
                format: 'DD-MM-YYYY'
            });
            $('#passportExpiry_' + ownerIndex).datetimepicker({
                format: 'DD-MM-YYYY'
            });
        }
    </script>
    @include('admin.master.tenants.form-submit-js')
    {{-- <script>
        $(document).on('input', '#trade_license_no', function() {

            // Convert to uppercase
            this.value = this.value.toUpperCase();

            let value = this.value;
            let regex = /^[A-Z0-9\/-]{0,20}$/;

            if (!regex.test(value)) {
                this.value = value.slice(0, -1);
            }

            validateTradeLicense();
        });

        function validateTradeLicense() {
            // alert("test");

            let input = $('#trade_license_no');
            let value = input.val();
            let fullRegex = /^[A-Z0-9\/-]{5,20}$/;
            let submitBtn = $('#submitBtn');

            // Remove existing error
            input.removeClass('is-invalid');
            $('#tradeLicenseError').remove();

            if (value.length === 0) {
                showError(input, "Trade License Number is required");
                submitBtn.prop('disabled', true);
                return false;
            }

            if (!fullRegex.test(value)) {
                showError(input, "Trade License must be 5–20 characters (letters, numbers, / or - only)");
                submitBtn.prop('disabled', true);
                return false;
            }

            submitBtn.prop('disabled', false);
            return true;
        }

        function showError(input, message) {
            input.addClass('is-invalid');
            input.after('<div id="tradeLicenseError" class="invalid-feedback">' + message + '</div>');
        }
    </script>
    <script>
        $(document).on('input', '.emirates-id', function() {

            // Allow only numbers and dash
            this.value = this.value.replace(/[^0-9-]/g, '');

            validateEmiratesID($(this));
        });

        function validateEmiratesID(input) {
            alert("test");

            let value = input.val();
            let regex = /^\d{3}-\d{4}-\d{7}-\d{1}$/;

            input.removeClass('is-invalid');
            input.next('.invalid-feedback').remove();

            if (value.length === 0) {
                showError(input, "Emirates ID is required");
                return false;
            }

            if (!regex.test(value)) {
                showError(input, "Emirates ID must be in format: 784-XXXX-XXXXXXX-X");
                return false;
            }

            return true;
        }
    </script>
    <script>
        $(document).on('input', '.passport-number', function() {

            // Convert to uppercase
            this.value = this.value.toUpperCase();

            // Remove special characters
            this.value = this.value.replace(/[^A-Z0-9]/g, '');

            validatePassport($(this));
        });

        function validatePassport(input) {

            let value = input.val();
            let regex = /^[A-Z0-9]{6,9}$/;

            input.removeClass('is-invalid');
            input.next('.invalid-feedback').remove();

            if (value.length === 0) {
                showError(input, "Passport Number is required");
                return false;
            }

            if (!regex.test(value)) {
                showError(input, "Passport must be 6–9 characters (letters & numbers only)");
                return false;
            }

            return true;
        }
    </script>
    <script>
        $(document).ready(function() {

            // Validate all Emirates IDs on load
            $('.emirates-id').each(function() {
                // alert("test");
                if ($(this).val().length > 0) {
                    validateEmiratesID($(this));
                }
            });

            // Validate all Passport numbers on load
            $('.passport-number').each(function() {
                if ($(this).val().length > 0) {
                    validatePassport($(this));
                }
            });
            let tradeInput = $('#trade_license_no');
            if (tradeInput.length && tradeInput.val().length > 0) {
                validateTradeLicense();
            }

        });
    </script> --}}
    <script>
        function validateAll() {
            // alert("test");
            let submitBtn = $('#submitBtn'); // your submit button ID

            let allValid = true;

            // Validate Trade License
            let tradeInput = $('#trade_license_no');
            if (tradeInput.length) {
                let val = tradeInput.val();
                let regex = /^[A-Z0-9\/-]{5,20}$/;
                tradeInput.removeClass('is-invalid');
                $('#tradeLicenseError').remove();

                if (val.length === 0 || !regex.test(val)) {
                    showError(tradeInput,
                        "Trade License must be 5–20 characters (letters, numbers, / or - only)");
                    allValid = false;
                }
            }
            // alert("test");
            // Validate all Passports
            $('.passport-number').each(function() {
                // alert("test");

                let val = $(this).val();
                let regex = /^[A-Z0-9]{6,9}$/;
                $(this).removeClass('is-invalid');
                $(this).next('.invalid-feedback').remove();

                if (val.length === 0 || !regex.test(val)) {
                    showError($(this), "Passport must be 6–9 characters (letters & numbers only)");
                    allValid = false;
                }
            });

            // Validate all Emirates IDs
            $('.emirates-id').each(function() {
                let val = $(this).val();
                let regex = /^\d{3}-\d{4}-\d{7}-\d{1}$/;
                $(this).removeClass('is-invalid');
                $(this).next('.invalid-feedback').remove();

                if (val.length === 0 || !regex.test(val)) {
                    showError($(this), "Emirates ID must be in format: 784-XXXX-XXXXXXX-X");
                    allValid = false;
                }
            });

            // Enable/disable submit button
            submitBtn.prop('disabled', !allValid);
            return allValid;
        }

        function showError(input, message) {
            input.addClass('is-invalid');
            if (!input.next('.invalid-feedback').length) {
                input.after('<div class="invalid-feedback">' + message + '</div>');
            }
        }
    </script>
    <script>
        $(document).ready(function() {


            // Run validation on page load
            validateAll();

            // Event delegation for dynamic inputs
            $(document).on('input', '#trade_license_no', function() {
                this.value = this.value.toUpperCase();
                validateAll();
            });

            $(document).on('input', '.passport-number', function() {
                this.value = this.value.toUpperCase();
                this.value = this.value.replace(/[^A-Z0-9]/g, ''); // remove invalid chars
                validateAll();
            });

            $(document).on('input', '.emirates-id', function() {
                this.value = this.value.replace(/[^0-9-]/g, ''); // allow only digits and dash
                validateAll();
            });

            // Main function to validate everything


            // Helper to show error

        });
    </script>
@endsection
