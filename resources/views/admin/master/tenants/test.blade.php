@extends('admin.layout.admin_master')

@section('custom_css')
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
                                            <input type="text" name="documents[3][number]"
                                                class="form-control @error('documents.trade_license.number') is-invalid @enderror"
                                                value="{{ old('documents.trade_license.number', $tradeLicense->document_number ?? '') }}"
                                                placeholder="Enter trade license number">
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
                                            <input type="date" name="documents[3][issued]"
                                                class="form-control @error('documents.trade_license.issued') is-invalid @enderror"
                                                value="{{ old('documents.trade_license.issued', isset($tradeLicense) && $tradeLicense->issued_date ? \Illuminate\Support\Carbon::parse($tradeLicense->issued_date)->format('Y-m-d') : '') }}">
                                            @error('documents.trade_license.issued')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Trade License Expiry Date -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Trade License Expiry Date</label>
                                            <input type="date" name="documents[3][expiry]"
                                                class="form-control @error('documents.trade_license.expiry') is-invalid @enderror"
                                                value="{{ old('documents.trade_license.expiry', isset($tradeLicense) && $tradeLicense->expiry_date ? \Illuminate\Support\Carbon::parse($tradeLicense->expiry_date)->format('Y-m-d') : '') }}">
                                            @error('documents.trade_license.expiry')
                                                <span class="invalid-feedback">{{ $message }}</span>
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
                                <button type="submit" class="btn btn-info px-4">
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
        let existingOwners = @json($owners ?? []);
        console.log('Existing Owners Data:', existingOwners);

        function generateOwners(count) {
            let container = document.getElementById('owners_section');
            container.innerHTML = '';

            for (let i = 1; i <= count; i++) {
                // alert(i);
                let ownerData = existingOwners[i] || {};
                let passport = ownerData[1] || {};
                let emirates = ownerData[2] || {};

                container.innerHTML += `
                    <div class="card card-outline card-info owner-card mb-3" id="owner_${i}" data-owner-index="${i}">
                        <div class="card-header">
                            <i class="fas fa-user mr-1"></i> Owner ${i} Documents
                        </div>
                        <div class="card-body">
                            <div class="row">
                             <input type="hidden" name="owners[${i}][2][id]" value="${emirates.id ?? ''}">
                                <!-- Emirates ID -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Emirates ID Number</label>
                                        <input type="text" name="owners[${i}][2][emirates_id]" class="form-control"
                                            value="${emirates.number ?? ''}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Emirates ID Upload</label>
                                        <input type="file" name="owners[${i}][2][emirates_file]" class="form-control">
                                        ${emirates.file ? `<a href="${emirates.file}" target="_blank">View File</a>` : ''}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Emirates ID Issued Date</label>
                                        <input type="date" name="owners[${i}][2][emirates_issued]" class="form-control"
                                            value="${emirates.issued ?? ''}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Emirates ID Expiry Date</label>
                                        <input type="date" name="owners[${i}][2][emirates_expiry]" class="form-control"
                                            value="${emirates.expiry ?? ''}">
                                    </div>
                                </div>

                                <!-- Passport -->
                                <input type="hidden" name="owners[${i}][1][id]" value="${passport.id ?? ''}">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Passport Number</label>
                                        <input type="text" name="owners[${i}][1][passport_number]" class="form-control"
                                            value="${passport.number ?? ''}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Passport Upload</label>
                                        <input type="file" name="owners[${i}][1][passport_file]" class="form-control">
                                        ${passport.file ? `<a href="${passport.file}" target="_blank">View File</a>` : ''}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Passport Issued Date</label>
                                        <input type="date" name="owners[${i}][1][passport_issued]" class="form-control"
                                            value="${passport.issued ?? ''}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Passport Expiry Date</label>
                                        <input type="date" name="owners[${i}][1][passport_expiry]" class="form-control"
                                            value="${passport.expiry ?? ''}">
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                `;
            }
        }
        const existingOwnerCount = {{ count($owners ?? []) }};
        // Trigger on dropdown change
        document.getElementById('owners_count').addEventListener('change', function() {

            let newCount = parseInt(this.value);
            let tenantId = document.getElementById('tenantForm').dataset.tenantId;

            if (newCount < existingOwnerCount) {

                for (let i = newCount; i <= existingOwnerCount; i++) {

                    let ownerBlock = document.getElementById('owner_' + i);

                    if (ownerBlock && !ownerBlock.querySelector('.remove-owner-btn')) {

                        let wrapper = document.createElement('div');
                        wrapper.className = 'remove-owner-wrapper text-right mt-2';
                        // text-end = right align (Bootstrap 5)
                        // use 'text-right' if Bootstrap 4

                        // 🔹 Create button
                        let button = document.createElement('button');
                        button.type = 'button';
                        button.dataset.owner = i;
                        button.dataset.tenantId = tenantId;
                        button.className = 'btn btn-outline-danger btn-sm remove-owner-btn';
                        button.innerText = 'Remove Owner';

                        // 🔹 Append button inside wrapper
                        wrapper.appendChild(button);

                        // 🔹 Append wrapper inside card-body
                        ownerBlock.querySelector('.card-body').appendChild(wrapper);
                    }
                }

            } else {
                generateOwners(newCount);
            }

        });

        // Generate on page load with default or old() value
        window.onload = function() {
            generateOwners(document.getElementById('owners_count').value);
        };
    </script>
    <script>
        $(document).ready(function() {

            $('#tenantForm').on('submit', function(e) {
                e.preventDefault(); // prevent normal form submission

                let form = this;
                let formData = new FormData(form);

                // Clear previous errors
                $(form).find('.is-invalid').removeClass('is-invalid');
                $(form).find('.invalid-feedback').remove();

                // Detect if this is update (tenant ID present)
                let tenantId = $(form).data(
                    'tenant-id');
                alert(tenantId); // make sure your form has data-tenant-id="{{ $tenant->id ?? '' }}"
                let url = tenantId ?
                    `/tenants/update/${tenantId}` // RESTful route for update
                    :
                    "{{ route('tenant.store') }}";

                let method = tenantId ? 'POST' : 'POST';
                // // For Laravel, you still send POST but add _method=PUT for update
                // if (tenantId) {
                //     formData.append('_method', 'PUT'); // tells Laravel it's an update
                // }

                $.ajax({
                    url: url,
                    method: method,
                    data: formData,
                    processData: false, // required for FormData
                    contentType: false, // required for FormData
                    headers: {
                        'X-CSRF-TOKEN': $('input[name="_token"]').val()
                    },
                    beforeSend: function() {
                        $(form).find('button[type="submit"]').prop('disabled', true);
                    },
                    success: function(response) {
                        toastr.success('Tenant saved successfully!', 'Success');
                        // Redirect to tenant list or any other page
                        window.location.href = "{{ route('tenant.index') }}";
                        form.reset();
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            // Validation errors
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, messages) {
                                let input = $(form).find(`[name="${key}"]`);
                                if (input.length === 0) {
                                    input = $(form).find(
                                        `[name="${key.replace(/\./g, '_')}"]`);
                                }
                                input.addClass('is-invalid');
                                input.after('<span class="invalid-feedback">' +
                                    messages[0] + '</span>');
                            });
                            toastr.error('Please fix the errors in the form.',
                                'Validation Error');
                        } else {
                            toastr.error('Something went wrong. Please try again.', 'Error');
                        }
                    },
                    complete: function() {
                        $(form).find('button[type="submit"]').prop('disabled', false);
                    }
                });

            });

        });
    </script>
    <script>
        document.addEventListener('click', function(e) {

            if (e.target.classList.contains('remove-owner-btn')) {

                let button = e.target;
                let tenantId = button.dataset.tenantId;
                let ownerIndex = button.dataset.owner;

                let ownerBlock = document.getElementById('owner_' + ownerIndex);

                if (!ownerBlock) return;

                // 🔥 Collect all hidden document IDs inside this owner block
                let hiddenInputs = ownerBlock.querySelectorAll('input[type="hidden"][name*="[id]"]');

                let documentIds = [];

                hiddenInputs.forEach(function(input) {
                    if (input.value) {
                        documentIds.push(input.value);
                    }
                });


                console.log("Tenant:", tenantId);
                console.log("Document IDs:", documentIds);

                if (documentIds.length === 0) {
                    ownerBlock.remove();
                    return;
                }

                Swal.fire({
                    title: 'Are you sure?',
                    text: "This will permanently remove this owner and related documents.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, remove it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {

                    if (result.isConfirmed) {

                        fetch('/tenant/remove-owner-documents', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                                },
                                body: JSON.stringify({
                                    tenant_id: tenantId,
                                    document_ids: documentIds
                                })
                            })
                            .then(response => {

                                if (!response.ok) {
                                    return response.json().then(err => {
                                        throw new Error(err.message);
                                    });
                                }

                                return response.json();
                            })
                            .then(data => {

                                ownerBlock.remove();

                                Swal.fire(
                                    'Removed!',
                                    data.message,
                                    'success'
                                );

                            })
                            .catch(error => {

                                Swal.fire(
                                    'Error!',
                                    error.message,
                                    'error'
                                );

                            });
                    }
                });
            }
        });
    </script>
@endsection
