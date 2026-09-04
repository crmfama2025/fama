@extends('admin.layout.admin_master')
@section('custom_css')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('assets/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
@endsection
@section('content')

    <div class="content-wrapper">

        <!-- Content Header -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>{{ $title }}</h1>
                    </div>

                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">
                                <a href="{{ route('lead.index') }}">Leads</a>
                            </li>
                            <li class="breadcrumb-item active">Create Lead</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <!-- Main Content -->
        <section class="content">
            <div class="container-fluid">

                <div class="card card-primary">

                    <div class="card-header bg-cyan">
                        <h3 class="card-title">
                            @if (isset($lead))
                                <i class="fas fa-edit mr-1"></i>
                                Edit Lead Information
                            @else
                                <i class="fas fa-user-plus mr-1"></i>
                                New Lead Information
                            @endif
                        </h3>
                    </div>

                    <form action="{{ route('lead.store') }}" method="POST" id="leadForm">
                        @csrf

                        @if (isset($lead))
                            <input type="hidden" name="id" value="{{ $lead->id }}">
                        @endif

                        <div class="card-body">

                            {{-- Validation Errors --}}
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <strong>Please fix the following errors:</strong>

                                    <ul class="mb-0 mt-2">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="row">

                                {{-- Company Name --}}
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="company_name">Company Name</label>

                                        <input type="text" name="company_name" id="company_name"
                                            class="form-control @error('company_name') is-invalid @enderror"
                                            value="{{ old('company_name', $lead->company_name ?? '') }}"
                                            placeholder="Enter company name">

                                        @error('company_name')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>


                                {{-- Contact Person --}}
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="contact_person_name" class="asterisk">
                                            Contact Person
                                        </label>

                                        <input type="text" name="contact_person_name" id="contact_person_name"
                                            class="form-control @error('contact_person_name') is-invalid @enderror"
                                            value="{{ old('contact_person_name', $lead->contact_person_name ?? '') }}"
                                            placeholder="Enter contact person name" required>

                                        @error('contact_person_name')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>


                                {{-- Phone Number --}}
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="phone_number" class="asterisk">
                                            Phone Number
                                        </label>

                                        <input type="text" name="phone_number" id="phone_number"
                                            class="form-control @error('phone_number') is-invalid @enderror"
                                            value="{{ old('phone_number', $lead->phone_number ?? '') }}"
                                            placeholder="Enter phone number" required>

                                        @error('phone_number')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>


                                {{-- Email --}}
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email">Email</label>

                                        <input type="email" name="email" id="email"
                                            class="form-control @error('email') is-invalid @enderror"
                                            value="{{ old('email', $lead->email ?? '') }}"
                                            placeholder="Enter email address">

                                        @error('email')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>


                                {{-- Lead Source --}}
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="lead_source" class="asterisk">
                                            Lead Source
                                        </label>

                                        <select name="lead_source" id="lead_source"
                                            class="form-control select2 @error('lead_source') is-invalid @enderror"
                                            required>

                                            <option value="">Select Lead Source</option>

                                            @foreach (['WhatsApp', 'Website', 'Referral', 'Email', 'Phone', 'Walk-in', 'Other'] as $source)
                                                <option value="{{ $source }}"
                                                    {{ old('lead_source', $lead->lead_source ?? '') == $source ? 'selected' : '' }}>
                                                    {{ $source }}
                                                </option>
                                            @endforeach

                                        </select>

                                        @error('lead_source')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>


                                {{-- Total Staff --}}
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="total_staff">Total Staff</label>

                                        <input type="number" name="total_staff" id="total_staff"
                                            class="form-control @error('total_staff') is-invalid @enderror"
                                            value="{{ old('total_staff', $lead->total_staff ?? '') }}" min="0"
                                            placeholder="Enter total staff">

                                        @error('total_staff')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>


                                {{-- Required Location --}}
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="required_location">
                                            Required Location
                                        </label>

                                        <input type="text" name="required_location" id="required_location"
                                            class="form-control @error('required_location') is-invalid @enderror"
                                            value="{{ old('required_location', $lead->required_location ?? '') }}"
                                            placeholder="e.g. Dubai">

                                        @error('required_location')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>


                                {{-- Requirement --}}
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="requirement" class="asterisk">
                                            Requirement
                                        </label>

                                        <textarea name="requirement" id="requirement" rows="4"
                                            class="form-control @error('requirement') is-invalid @enderror" placeholder="Enter lead requirement" required>{{ old('requirement', $lead->requirement ?? '') }}</textarea>

                                        @error('requirement')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                            </div>

                        </div>


                        <div class="card-footer">

                            <a href="{{ route('lead.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i>
                                Back
                            </a>

                            <button type="submit" class="btn btn-primary float-right">

                                <i class="fas fa-save"></i>

                                {{ isset($lead) ? 'Update Lead' : 'Save Lead' }}

                            </button>

                        </div>

                    </form>

                </div>

            </div>
        </section>

    </div>

@endsection
@section('custom_js')
    <!-- DataTables  & Plugins -->
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
        $('#leadForm').on('submit', function(e) {
            e.preventDefault();

            let form = $(this);
            let formData = new FormData(this);

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,

                beforeSend: function() {
                    showLoader();
                },

                success: function(response) {

                    hideLoader();

                    if (response.success) {

                        toastr.success(response.message);
                        form[0].reset();

                        setTimeout(function() {
                            window.location.href = "{{ route('lead.index') }}";
                        }, 500);
                    }
                },

                error: function(xhr) {

                    hideLoader();

                    if (xhr.status === 422) {

                        let errors = xhr.responseJSON.errors;

                        $.each(errors, function(field, messages) {

                            $('#' + field)
                                .addClass('is-invalid')
                                .after(
                                    '<span class="invalid-feedback d-block">' +
                                    messages[0] +
                                    '</span>'
                                );
                        });

                        toastr.error('Please fix the validation errors.');

                    } else {

                        toastr.error(
                            xhr.responseJSON?.message ??
                            'Something went wrong.'
                        );
                    }
                }
            });
        });
    </script>
@endsection
