@extends('admin.layout.admin_master')
@section('custom_css')
    <!-- daterange picker -->

    <link rel="stylesheet" href="{{ asset('assets/daterangepicker/daterangepicker.css') }}">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet" href="{{ asset('assets/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
    <style>
        .agreementTable tbody tr {
            background-color: #f6ffff;
        }

        .agreementTable thead tr {
            background-color: #D6EEEE;
        }
    </style>
@endsection
@section('content')
    <!-- Content Wrapper. Contains page content -->
    {{-- {{ dd($data) }} --}}
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Agreement Documents</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Agreement Documents</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header">
                        <span class="float-right">
                            <button class="btn btn-info float-right m-1" data-toggle="modal"
                                data-target="#modal-upload">Upload
                                Files</button>
                        </span>
                    </div>

                    <div class="row mx-2 mt-3">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <div class="d-flex justify-content-between">
                                        <h3 class="card-title">Agreement Documents list -
                                            {!! $agreement->contract->contract_type->contract_type .
                                                ' - ' .
                                                $agreement->contract->contract_unit->business_type() !!}</h3>


                                    </div>

                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <table class="table agreementTable">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Document name</th>
                                                <th>Document Number</th>
                                                <th>Issued Date</th>
                                                <th>Expiry Date</th>
                                                <th>Status</th>
                                                <th>view</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($documents as $doc)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $doc->TenantIdentity->identity_type }}</td>
                                                    <td>
                                                        @if ($doc->document_type == 6)
                                                            -
                                                        @else
                                                            {{ $doc->document_number }}
                                                        @endif
                                                    </td>
                                                    <td>{{ getFormattedDate($doc->issued_date) ?? ' - ' }}</td>
                                                    <td>{{ getFormattedDate($doc->expiry_date) ?? ' - ' }}</td>
                                                    <td>
                                                        @if ($doc->expiry_date && \Carbon\Carbon::parse($doc->expiry_date)->isPast())
                                                            <span class="badge badge-danger">Expired</span>
                                                        @else
                                                            <span class="badge badge-success">Active</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="{{ asset('storage/' . $doc->original_document_path) }}"
                                                            target="_blank" class="btn btn-sm btn-outline-info"
                                                            title="Click to View">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7" class="text-center">No documents available</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                    <br>


                                </div>
                                <!-- /.card-body -->
                            </div>
                            <!-- /.card -->
                        </div>
                        <!-- /.col -->
                    </div>
                    <div class="row mx-2">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <div class="d-flex justify-content-between">
                                        <h3 class="card-title">Tenant Documents</h3>
                                        @if ($agreement->contract->contract_unit->business_type == 1)
                                            <a href="{{ route('tenant.edit', $agreement->tenant->id) }}" target="_blank"
                                                class="btn btn-info"><i class="fas fa-edit mr-2"></i>Edit</a>
                                        @endif
                                    </div>

                                    {{-- <span class="float-right">
                                    <button class="btn btn-info float-right m-1" data-toggle="modal"
                                        data-target="#modal-upload">Upload Files</button>
                                </span> --}}
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <table class="table agreementTable">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Document name</th>
                                                <th>Document Number</th>
                                                <th>Issued Date</th>
                                                <th>Expiry Date</th>
                                                <th>Status</th>
                                                <th>view</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($tenant_documents as $doc)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $doc->TenantIdentity->identity_type }}</td>
                                                    <td>
                                                        @if ($doc->document_type == 6)
                                                            -
                                                        @else
                                                            {{ $doc->document_number }}
                                                        @endif
                                                    </td>
                                                    <td>{{ getFormattedDate($doc->issued_date) ?? ' - ' }}</td>
                                                    <td>{{ getFormattedDate($doc->expiry_date) ?? ' - ' }}</td>
                                                    <td>
                                                        @if ($doc->expiry_date && \Carbon\Carbon::parse($doc->expiry_date)->isPast())
                                                            <span class="badge badge-danger">Expired</span>
                                                        @else
                                                            <span class="badge badge-success">Active</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="{{ asset('storage/' . $doc->original_document_path) }}"
                                                            target="_blank" class="btn btn-sm btn-outline-info"
                                                            title="Click to View">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7" class="text-center">No documents available</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                    <br>


                                </div>
                                <!-- /.card-body -->
                            </div>
                            <!-- /.card -->
                        </div>
                        <!-- /.col -->
                    </div>
                    <div class="text-left">
                        <a href="{{ route('agreement.index') }}" class="btn btn-info mb-2 mx-3"><i
                                class="fas mr-2 fa-arrow-left"></i>Back</a>
                    </div>

                </div>
                <!-- /.row -->
            </div><!-- /.container-fluid -->
            {{-- {{ dd($documents) }} --}}

            <div class="modal fade" id="modal-upload">
                <div
                    class="modal-dialog {{ $agreement->contract->contract_unit->business_type == 2 ? 'modal-xl' : 'modal-md' }}">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Upload Documents</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="" id="agreementImportForm" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="modal-body">
                                <div class="card-body">
                                    <input type="hidden" name="agreement_id" value="{{ $agreementId }}">
                                    @php
                                        $business_type = $agreement->contract->contract_unit->business_type;
                                        $contract_type = $agreement->contract->contract_type_id;
                                    @endphp
                                    @foreach ($tenantIdentities as $index => $identity)
                                        {{-- @if ($identity->id == 6 && $business_type != 1) --}}
                                        {{-- @if (($identity->id == 6 || $identity->id == 5) && $business_type != 1)
                                            @continue
                                        @endif --}}
                                        {{-- Contract type 2 → allow only ID 5 --}}
                                        @if ($contract_type == 2 && $identity->id != 5)
                                            @continue
                                        @endif

                                        {{-- Contract type 1 & Business type 1 → hide 5 and 6 --}}
                                        @if ($contract_type == 1 && $business_type == 2 && in_array($identity->id, [5, 6, 3, 4]))
                                            @continue
                                        @endif

                                        @if ($contract_type == 1 && $business_type == 1 && $identity->id != 6)
                                            @continue
                                        @endif
                                        <h6 class="font-weight-bold text-cyan mb-3">
                                            {{ $identity->identity_type }}
                                        </h6>
                                        @php
                                            // Find matching document (if exists)
                                            if ($business_type == 1) {
                                                $document = isset($documents)
                                                    ? $documents->firstWhere('document_type', $identity->id)
                                                    : null;
                                            } else {
                                                $document = isset($tenant_documents)
                                                    ? $tenant_documents->firstWhere('document_type', $identity->id)
                                                    : null;
                                            }
                                            // $document = isset($documents)
                                            //     ? $documents->firstWhere('document_type', $identity->id)
                                            //     : null;
                                        @endphp

                                        <div class="form-row">
                                            {{-- First Field --}}
                                            {{-- <input type="hidden" name="agreement_id" value="{{ $identity->id }}"> --}}
                                            @if ($identity->id !== 6 && $identity->id !== 5)
                                                {{-- Hide document number for Trade License and UID/UDB --}}
                                                <div class="form-group col-md-3">
                                                    <label for="document_number_{{ $index }}">
                                                        {{ $identity->first_field_label }}
                                                    </label>
                                                    {{-- <input type="{{ $identity->first_field_type }}"
                                                    name="documents[{{ $index }}][document_number]"
                                                    id="document_number_{{ $index }}"
                                                    value="{{ $document ? $document->document_number : '' }}"
                                                    class="form-control"
                                                    placeholder="{{ $identity->first_field_label }}"> --}}
                                                    <input type="{{ $identity->first_field_type }}"
                                                        name="documents[{{ $index }}][document_number]"
                                                        id="document_number_{{ $index }}"
                                                        class="form-control document_number"
                                                        data-doc-type="{{ $identity->id }}"
                                                        value="{{ $document ? $document->document_number : '' }}"
                                                        placeholder="{{ $identity->first_field_label }}">
                                                    <small class="text-danger d-none"
                                                        id="doc_error_{{ $index }}"></small>

                                                </div>
                                            @endif
                                            <input type="hidden" name="documents[{{ $index }}][document_type]"
                                                value="{{ $identity->id }}">

                                            {{-- Second Field --}}
                                            <div class="form-group {{ $business_type == 2 ? 'col-md-3' : 'col-md-12' }}">
                                                <label for="document_path_{{ $index }}">
                                                    {{ $identity->second_field_label }}
                                                </label>
                                                <input type="{{ $identity->second_field_type }}"
                                                    name="documents[{{ $index }}][document_path]"
                                                    id="document_path_{{ $index }}" class="form-control"
                                                    @if ($identity->second_field_type == 'file') accept="image/*,.pdf" @endif
                                                    placeholder="{{ $identity->second_field_label }}">
                                                @if ($document && $document->original_document_path)
                                                    <input type="hidden" name="documents[{{ $index }}][id]"
                                                        value="{{ $document->id }}">
                                                    <div class="d-flex align-items-center mt-2">
                                                        @php
                                                            $filePath = asset(
                                                                'storage/' . $document->original_document_path,
                                                            );
                                                            $isPdf = \Illuminate\Support\Str::endsWith(
                                                                strtolower($document->original_document_path),
                                                                '.pdf',
                                                            );
                                                        @endphp

                                                        @if ($isPdf)
                                                            <a href="{{ $filePath }}" target="_blank"
                                                                class="btn btn-outline-primary btn-sm mr-1"
                                                                title="Click to View File">
                                                                <i class="fas fa-file-pdf"></i> View PDF
                                                            </a>
                                                        @else
                                                            <a href="{{ $filePath }}" target="_blank"
                                                                class="mr-1">
                                                                <img src="{{ $filePath }}" class="documentpreview"
                                                                    title="Click to View File" alt="Document">
                                                            </a>
                                                        @endif
                                                        <p class="small text-muted mt-1">
                                                            {{ $document->original_document_name }}
                                                        </p>


                                                    </div>
                                                @endif
                                            </div>
                                            @if ($identity->id != 6 && $identity->id != 5)
                                                <div class="form-group col-md-3">
                                                    <label class="">Issued Date</label>
                                                    <div class="input-group date" id="issuedDate_{{ $index }}"
                                                        data-target-input="nearest">
                                                        <input type="text"
                                                            class="form-control datetimepicker-input is_date"
                                                            name="documents[{{ $index }}][issued_date]"
                                                            id="issued_date_{{ $index }}"
                                                            data-target="#issuedDate_{{ $index }}"
                                                            placeholder="dd-mm-YYYY"
                                                            value="{{ $document && $document->issued_date ? \Carbon\Carbon::parse($document->issued_date)->format('d-m-Y') : '' }}" />
                                                        <div class="input-group-append"
                                                            data-target="#issuedDate_{{ $index }}"
                                                            data-toggle="datetimepicker">
                                                            <div class="input-group-text">
                                                                <i class="fa fa-calendar"></i>
                                                            </div>
                                                        </div>
                                                        <span class="text-danger" id="issued_error_{{ $index }}"
                                                            class="error"></span>
                                                    </div>
                                                </div>

                                                <div class="form-group col-md-3">
                                                    <label class="">Expiry Date</label>
                                                    <div class="input-group date" id="expiryDate_{{ $index }}"
                                                        data-target-input="nearest">
                                                        <input type="text"
                                                            class="form-control datetimepicker-input exp_date"
                                                            name="documents[{{ $index }}][expiry_date]"
                                                            id="expiry_date_{{ $index }}"
                                                            data-target="#expiryDate_{{ $index }}"
                                                            placeholder="dd-mm-YYYY"
                                                            value="{{ $document && $document->expiry_date ? \Carbon\Carbon::parse($document->expiry_date)->format('d-m-Y') : '' }}" />
                                                        <div class="input-group-append"
                                                            data-target="#expiryDate_{{ $index }}"
                                                            data-toggle="datetimepicker">
                                                            <div class="input-group-text">
                                                                <i class="fa fa-calendar"></i>
                                                            </div>
                                                        </div>
                                                        <span class="text-danger" id="expiry_error_{{ $index }}"
                                                            class="error"></span>
                                                    </div>
                                                </div>
                                            @endif


                                        </div>
                                        <hr class=" d-block d-sm-none">
                                    @endforeach

                                </div>
                                <!-- /.card-body -->
                            </div>
                            <div class="modal-footer justify-content-between">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                <button type="submit" id="importBtn" class="btn btn-info importBtn">Upload</button>
                            </div>
                        </form>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
            <!-- /.modal -->

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
    <script>
        $('.input-group.date').datetimepicker({
            format: 'DD-MM-YYYY'
        });
    </script>
    <script>
        function documentNumberValidation() {
            let isValid = true;
            // alert("test");

            document.querySelectorAll('.document_number').forEach(function(input) {

                const docType = input.dataset.docType;
                const value = input.value.trim();
                const index = input.id.split('_').pop();
                const errorElement = document.getElementById('doc_error_' + index);

                errorElement.classList.add('d-none');
                errorElement.innerText = '';

                if (!value) return; // Skip empty

                let regex = null;
                let message = '';

                // 🔹 Emirates ID (example: id = 1)
                if (docType == 2) {
                    regex = /^\d{3}-\d{4}-\d{7}-\d{1}$/;
                    message = "Emirates ID must be in format 784-1990-1234567-1";
                }

                // 🔹 Passport (example: id = 2)
                if (docType == 1) {
                    regex = /^[A-Z0-9]{6,9}$/;
                    message = "Passport must be 6–9 alphanumeric characters";
                }
                // 🏢 Trade License (example id = 6 — change if different)
                if (docType == 3) {
                    regex = /^[A-Z0-9\/-]{5,20}$/;
                    message = "Trade License must be 5–20 characters (letters, numbers, / or - only)";
                }
                if (docType == 4) {
                    regex = /^\d{9,15}$/;
                    message = "UID/UDB number must be 9–15 digits";
                }

                if (regex && !regex.test(value)) {
                    errorElement.innerText = message;
                    errorElement.classList.remove('d-none');
                    isValid = false;
                }

            });

            return isValid; // ✅
        }
        $('.importBtn').click(function(e) {

            e.preventDefault();
            const uploadBtn = $(this);
            const agreementId = $('input[name="agreement_id"]').val();
            const url = "{{ url('agreement-documents-upload') }}/" + agreementId;

            let method = 'POST';
            var form = document.getElementById('agreementImportForm');
            let formValid = true;
            let hasInput = false;

            $('#agreementImportForm .form-row').each(function() {
                // alert("test");
                const firstField = $(this).find('input[name*="[document_number]"]');
                const secondField = $(this).find('input[name*="[document_path]"]');
                const hiddenIdField = $(this).find('input[name*="[id]"]')
                const typefield = parseInt($(this).find('input[name*="[document_type]"]').val());

                const firstVal = firstField.val()?.trim();
                const secondVal = secondField.val()?.trim();
                const hasExistingFile = hiddenIdField.length > 0;

                firstField.removeClass('is-invalid');
                secondField.removeClass('is-invalid');
                $(this).find('.invalid-feedback').remove();

                if (firstVal && !secondVal && !hasExistingFile) {
                    secondField.addClass('is-invalid');
                    secondField.after(
                        '<span class="invalid-feedback d-block">This field is required.</span>');
                    formValid = false;
                }
                if ((secondVal || hasExistingFile) && !firstVal) {
                    // alert(typefield);
                    console.log("typefield: " + typefield);
                    if (typefield == 6 || typefield == 5) {
                        formValid = true;
                    } else {
                        firstField.addClass('is-invalid');
                        firstField.after(
                            '<span class="invalid-feedback d-block">This field is required.</span>');
                        formValid = false;
                    }

                }
                if (firstVal || secondVal || hasExistingFile) {
                    hasInput = true;
                    // return false;
                }
            });
            if (!hasInput) {
                toastr.error('Please add at least one document details.');
                return false;
            }

            if (!formValid) {
                toastr.error('Please fill all required fields.');
                return false;
            }
            let docValid = documentNumberValidation();

            if (!docValid) {
                toastr.error('Please correct document number format.');
                return false;
            }
            uploadBtn.prop('disabled', true);

            var fdata = new FormData(form);

            fdata.append('_token', $('meta[name="csrf-token"]').attr('content'));
            // alert("hi");
            showLoader();

            $.ajax({
                url: url,
                type: method,
                data: fdata,
                processData: false,
                contentType: false,
                success: function(response) {
                    hideLoader();
                    toastr.success(response.message);
                    $('#modal-upload').modal('hide');
                    //  window.location = "{{ route('agreement.index') }}"
                    location.reload();
                },
                error: function(xhr) {
                    hideLoader();
                    uploadBtn.prop('disabled', false);
                    const response = xhr.responseJSON;
                    if (xhr.status === 422 && response?.errors) {
                        $.each(response.errors, function(key, messages) {
                            toastr.error(messages[0]);
                        });
                    } else if (response.message) {
                        toastr.error(response.message);
                    }
                }
            });
        });
        $('.document_number').on('input', function() {
            this.value = this.value.toUpperCase();
        });
        $(document).on('input change dp.change', '.document_number, .is_date,  .exp_date, input[type="file"]',
            function() {
                // alert("test");
                let rowIndex = this.id.split('_').pop();
                validateDocumentRow(rowIndex);
            });
        $('#modal-upload').on('shown.bs.modal', function() {
            // Find all document number inputs inside this modal
            $(this).find('.document_number').each(function() {
                let rowIndex = this.id.split('_').pop();
                validateDocumentRow(rowIndex);
            });
        });
        // $('#modal-upload').on('shown.bs.modal', function(event) {
        function validateDocumentRow(rowIndex) {
            // alert("test");
            // alert(rowIndex);
            console.log("rowindex", rowIndex);

            let numberField = $('#document_number_' + rowIndex);
            let fileField = $('#document_path_' + rowIndex);
            let issuedField = $('#issued_date_' + rowIndex);
            let expiryField = $('#expiry_date_' + rowIndex);
            let errorElement = $('#doc_error_' + rowIndex);
            let issuedError = $('#issued_error_' + rowIndex);
            let expiryError = $('#expiry_error_' + rowIndex);
            console.log("error", expiryError);
            console.log(issuedField, expiryField);

            let value = numberField.val().toUpperCase();
            numberField.val(value);

            let documentType = numberField.data('doc-type');

            let emiratesRegex = /^784-\d{4}-\d{7}-\d{1}$/;
            let passportRegex = /^[A-Z0-9]{6,9}$/;
            let tradeRegex = /^[A-Z0-9\/-]{5,20}$/;
            let UidRegex = /^\d{9,15}$/;

            errorElement.text('');
            issuedError.text('');
            expiryError.text('');


            // -------------------
            // Document Validation
            // -------------------
            // alert(documentType);

            if (documentType == 2) {
                // alert("eid");
                if (value && !emiratesRegex.test(value)) {
                    errorElement.text('Invalid Emirates ID format');
                    errorElement.removeClass('d-none');
                    // errorElement.classList.remove('d-none');
                }
            } else if (documentType == 1) {
                if (value && !passportRegex.test(value)) {
                    errorElement.text('Passport must be 6-9 alphanumeric characters');
                    errorElement.removeClass('d-none');
                    // errorElement.classList.remove('d-none');
                }
            } else if (documentType == 3) {
                if (value && !tradeRegex.test(value)) {
                    errorElement.text('Trade License must be 5–20 characters');
                    errorElement.removeClass('d-none');
                    // errorElement.classList.remove('d-none');
                }
            } else if (documentType == 4) {
                if (value && !UidRegex.test(value)) {
                    errorElement.text('UID/UDB number must be 9–15 digits');
                    errorElement.removeClass('d-none');
                    // errorElement.classList.remove('d-none');
                }
            }

            // -------------------
            // Date Validation
            // -------------------
            let issuedLabel = issuedField.closest('.form-group').find('label');
            let expiryLabel = expiryField.closest('.form-group').find('label');
            if (issuedField.val() && expiryField.val()) {
                if (!value) {
                    numberField.addClass('is-invalid');
                    numberField.attr('required', true);
                } else {
                    numberField.addClass('is-valid');
                    numberField.attr('required', false);
                }

            }

            if (value) {
                // alert(value);

                issuedField.attr('required', true);
                expiryField.attr('required', true);


                if (!issuedField.val()) {
                    issuedField.addClass('is-invalid');
                    issuedLabel.addClass('asterisk');
                } else {
                    issuedField.removeClass('is-invalid');
                    issuedLabel.removeClass('asterisk');
                }

                if (!expiryField.val()) {
                    expiryField.addClass('is-invalid');
                    expiryLabel.addClass('asterisk');
                } else {
                    expiryField.removeClass('is-invalid');
                    expiryLabel.removeClass('asterisk');
                }

                if (issuedField.val() && expiryField.val()) {
                    // alert("test");

                    let issuedDate = moment(issuedField.val(), "DD-MM-YYYY", true);
                    let expiryDate = moment(expiryField.val(), "DD-MM-YYYY", true);

                    if (!issuedDate.isValid() || !expiryDate.isValid()) {
                        // Invalid format, do nothing or show generic error
                        return;
                    }

                    if (expiryDate.isBefore(issuedDate) || expiryDate.isSame(issuedDate)) {
                        // alert("test");
                        expiryField.addClass('is-invalid');
                        expiryError.text('Expiry date must be after Issued date');
                    } else {
                        expiryField.removeClass('is-invalid');
                        expiryError.text(''); // clear error if valid
                    }

                }

            } else {
                issuedField.removeAttr('required').removeClass('is-invalid');
                expiryField.removeAttr('required').removeClass('is-invalid');
                expiryLabel.removeClass('asterisk');
                issuedLabel.removeClass('asterisk');
            }
        }
        // });
    </script>
@endsection
