@extends('admin.layout.admin_master')

@section('custom_css')
    <link rel="stylesheet" href="{{ asset('assets/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    <style>
        .select2-container--bootstrap4 .select2-selection--single {
            height: calc(2.25rem + 2px) !important;
            position: relative;
        }

        .select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow {
            height: 100%;
            top: 0;
        }

        .select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered {
            padding-left: 0 !important;
            padding-right: 2rem;
            line-height: 30px !important;
        }
    </style>
@endsection

@section('content')
    <div class="content-wrapper">

        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>{{ $title }}</h1>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">

                <div class="card">
                    <div class="card-body">

                        <form id="documentForm" method="POST" enctype="multipart/form-data"
                            action="{{ route('investment.contracts.upload', $document->id) }}">

                            @csrf

                            {{-- ACTION TYPE --}}
                            <div class="form-row mb-3">
                                <div class="col-md-12">

                                    <label class="mr-3">
                                        <input type="radio" name="action_type" value="0">
                                        Upload
                                    </label>

                                    <label class="mr-3">
                                        <input type="radio" name="action_type" value="1" checked>
                                        Generate
                                    </label>

                                </div>
                            </div>

                            {{-- DOCUMENT GROUPS --}}
                            <div id="document_rows">

                                {{-- MAIN DOCUMENT --}}
                                <div class="document-group border rounded p-3 mb-4">

                                    {{-- MAIN DOCUMENT FIELDS --}}
                                    <div class="form-row align-items-end">
                                        {{-- VERSION --}}
                                        <div class="col-md-2">
                                            <label class="asterisk">
                                                Version
                                            </label>

                                            <select name="documents[0][version]" class="form-control select2" required>

                                                <option value="">
                                                    Select Version No
                                                </option>

                                                @foreach (InvestorDocVersion() as $key => $item)
                                                    <option value="{{ $key }}">
                                                        {{ $item }}
                                                    </option>
                                                @endforeach

                                            </select>
                                        </div>

                                        {{-- DOCUMENT TYPE --}}
                                        <div class="col-md-3">
                                            <label class="asterisk">
                                                Document Type
                                            </label>

                                            <select name="documents[0][contract_type]" class="form-control select2"
                                                required>

                                                <option value="">
                                                    Select Type
                                                </option>

                                                @foreach ($formData['doc_types'] as $type)
                                                    <option value="{{ $type->id }}"
                                                        {{ $document->investor_agreement_type_id == $type->id ? 'selected' : '' }}>

                                                        {{ $type->investor_agreement_type }}
                                                    </option>
                                                @endforeach

                                            </select>
                                        </div>

                                        {{-- MAIN FILE --}}
                                        <div class="col-md-3 main-file-field">
                                            <label class="asterisk main-file-label">
                                                Upload Main File
                                            </label>

                                            <input type="file" name="documents[0][document]"
                                                class="form-control document-input" required>
                                        </div>

                                        {{-- DATE --}}
                                        <div class="col-md-2 main-date-field">

                                            <label class="asterisk main-date-label">
                                                Date
                                            </label>

                                            <div class="input-group date generateddate" id="generateddate-0"
                                                data-target-input="nearest">

                                                <input type="text"
                                                    class="form-control datetimepicker-input generated-date"
                                                    name="documents[0][generated_date]" data-target="#generateddate-0"
                                                    placeholder="DD-MM-YYYY" required>

                                                <div class="input-group-append" data-target="#generateddate-0"
                                                    data-toggle="datetimepicker">
                                                    <div class="input-group-text">
                                                        <i class="fa fa-calendar"></i>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>

                                        {{-- ADD MAIN DOCUMENT --}}
                                        <div class="col-md-2 addButton">

                                            <button type="button" class="btn btn-success add-document">

                                                <i class="fas fa-plus"></i>
                                                Add More

                                            </button>

                                        </div>

                                    </div>

                                    {{-- ADDITIONAL DOCUMENTS --}}
                                    <div class="additional-documents mt-4 ">

                                        <div class="d-flex justify-content-between align-items-center mb-2">

                                            <label class="font-weight-bold mb-0">
                                                Additional Documents
                                            </label>

                                        </div>

                                        <div class="additional-document-list">

                                            {{-- FIRST ADDITIONAL DOCUMENT --}}
                                            <div class="form-row additional-document-row mb-2">

                                                {{-- TYPE --}}
                                                <div class="col-md-5">
                                                    <label>Additional Document Type</label>
                                                    <select name="documents[0][additional_documents][0][type]"
                                                        class="form-control select2 additional-type">
                                                        <option value="">Select Type</option>
                                                        <option value="Exit Plan Document">Exit Plan Document</option>
                                                        <option value="Authorisation Document">Authorisation Document
                                                        </option>
                                                        <option value="Bank Confirmation Letter">Bank Confirmation Letter
                                                        </option>
                                                        <option value="Signanture Confirmation Letter">Signanture
                                                            Confirmation
                                                            Letter
                                                        </option>
                                                        <option value="Investment Transfer Confirmation Letter">Investment
                                                            Transfer Confirmation Letter
                                                        </option>
                                                    </select>
                                                </div>

                                                {{-- FILE --}}
                                                <div class="col-md-5">

                                                    <label>
                                                        Upload File
                                                    </label>

                                                    <input type="file" name="documents[0][additional_documents][0][file]"
                                                        class="form-control additional-file">

                                                </div>

                                                {{-- ADD MORE --}}
                                                <div class="col-md-2 d-flex align-items-end">

                                                    <button type="button" class="btn btn-success add-additional-document">

                                                        <i class="fas fa-plus"></i>
                                                        Add More

                                                    </button>

                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                            {{-- SUBMIT --}}
                            <div class="form-row mt-4">

                                <div class="col-md-12 text-right">

                                    <button type="submit" class="btn btn-primary">

                                        Submit

                                    </button>

                                </div>

                            </div>

                        </form>


                        {{-- @dump($documents) --}}

                        {{-- Documents list --}}
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5 class="mb-0">Document List</h5>
                            </div>

                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped" id="documentsListTable">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Version</th>
                                                <th>Document Type</th>
                                                <th>Main File</th>
                                                <th>Date</th>
                                                <th>Additional Documents</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            @foreach ($documents as $doc)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>V{{ $doc->version_number }}</td>
                                                    <td>{{ $doc->agreementType->investor_agreement_type }}</td>
                                                    <td>
                                                        @if ($doc->investment_contract_file_path)
                                                            <a href="{{ Storage::url($doc->investment_contract_file_path) }}"
                                                                target="_blank" class="btn btn-sm btn-primary">
                                                                <i class="fas fa-eye"></i> View
                                                            </a>
                                                        @else
                                                            -
                                                        @endif
                                                    </td>

                                                    <td>
                                                        {{ $doc->document_date ? \Carbon\Carbon::parse($doc->document_date)->format('d-m-Y') : '-' }}
                                                    </td>
                                                    <td>
                                                        @if ($doc->additionalDocuments->count())
                                                            @foreach ($doc->additionalDocuments as $additional)
                                                                <div class="mb-1">
                                                                    <a href="{{ Storage::url($additional->document_path) }}"
                                                                        target="_blank"
                                                                        class="btn btn-sm btn-outline-primary">
                                                                        <i class="fas fa-eye"></i>
                                                                        {{ $additional->document_name }}
                                                                    </a>
                                                                </div>
                                                            @endforeach
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        {{-- <button type="button" class="btn btn-sm btn-info edit-document"
                                                            data-id="{{ $doc->id }}">
                                                            <i class="fas fa-edit"></i>Edit
                                                        </button> --}}
                                                        <button type="button"
                                                            class="btn btn-sm btn-danger delete-document"
                                                            data-id="{{ $doc->id }}">
                                                            <i class="fas fa-trash"></i> Delete
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>


                    </div>
                </div>

            </div>
        </section>

    </div>
@endsection

@section('custom_js')
    {{-- Select2 --}}
    <script src="{{ asset('assets/select2/js/select2.full.min.js') }}"></script>

    {{-- Moment --}}
    <script src="{{ asset('assets/moment/moment.min.js') }}"></script>

    {{-- Tempus Dominus --}}
    <script src="{{ asset('assets/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>

    {{-- DataTables --}}
    <script src="{{ asset('assets/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>

    <script>
        $(document).ready(function() {

            // ---------- Helpers ----------

            // Initialize (or re-initialize) Select2 on all <select class="select2">
            // inside a given container. Always scoped to `select` tags only, so we
            // never accidentally match Select2's own generated wrapper spans
            // (which also carry the .select2 class after init).
            function initializeSelect2(container) {
                container.find('select.select2').each(function() {
                    let $el = $(this);

                    if ($el.hasClass('select2-hidden-accessible')) {
                        $el.select2('destroy');
                    }

                    $el.select2({
                        theme: 'bootstrap4',
                        width: '100%'
                    });
                });
            }

            // Initialize (or re-initialize) the date picker on all matching
            // inputs inside a given container. Guarded so we never double-init
            // the same input (which is what breaks tempusdominus positioning).
            function initializeDatePicker(container) {
                container.find('.generateddate').each(function() {
                    let $el = $(this);

                    if ($el.data('datetimepicker')) {
                        return;
                    }

                    $el.datetimepicker({
                        format: 'DD-MM-YYYY',
                        useCurrent: false
                    });
                });
            }

            // ---------- Initial page load ----------

            initializeSelect2($(document));
            initializeDatePicker($(document));

            // ---------- Action Type ----------

            function toggleActionFields() {
                let type = $('input[name="action_type"]:checked').val();

                // Clear dynamically added document groups
                $('#document_rows .document-group:not(:first)').remove();

                // Clear first document values
                let $firstGroup = $('#document_rows .document-group').first();

                $firstGroup.find('input[type="text"]').val('');
                $firstGroup.find('input[type="file"]').val('');

                $firstGroup.find('select').val('').trigger('change');

                // Remove all additional rows except the first one
                $firstGroup.find('.additional-document-row:not(:first)').remove();

                // Clear first additional document
                $firstGroup.find('.additional-document-row:first select').val('').trigger('change');
                $firstGroup.find('.additional-document-row:first input[type="file"]').val('');


                if (type == 0) {
                    $('.main-file-field').show();
                    $('.main-date-field').show();
                    $('.addButton').show();

                    $('.document-input').prop('required', true);
                    $('.generated-date').prop('required', true);

                    $('.main-file-label').addClass('asterisk');
                    $('.main-date-label').addClass('asterisk');
                } else {
                    $('.main-file-field').hide();
                    $('.main-date-field').hide();
                    $('.addButton').hide();

                    $('.document-input').prop('required', false);
                    $('.generated-date').prop('required', false);

                    $('.main-file-label').removeClass('asterisk');
                    $('.main-date-label').removeClass('asterisk');
                }
            }

            toggleActionFields();

            $('input[name="action_type"]').on('change', function() {
                toggleActionFields();
            });

            // ---------- Add Main Document ----------

            $(document).on('click', '.add-document', function() {
                let documentIndex = $('.document-group').length;

                let row = `
                    <div class="document-group border rounded p-3 mb-4">

                        <div class="form-row align-items-end">

                            <div class="col-md-2">
                                <label class="asterisk">Version</label>

                                <select name="documents[${documentIndex}][version]"
                                    class="form-control select2"
                                    required>

                                    <option value="">Select Version No</option>

                                    @foreach (InvestorDocVersion() as $key => $item)
                                        <option value="{{ $key }}">
                                            {{ $item }}
                                        </option>
                                    @endforeach

                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="asterisk">Document Type</label>

                                <select name="documents[${documentIndex}][contract_type]"
                                    class="form-control select2"
                                    required>

                                    <option value="">Select Type</option>

                                    @foreach ($formData['doc_types'] as $type)
                                        <option value="{{ $type->id }}">
                                            {{ $type->investor_agreement_type }}
                                        </option>
                                    @endforeach

                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="asterisk">Upload Main File</label>

                                <input type="file"
                                    name="documents[${documentIndex}][document]"
                                    class="form-control document-input"
                                    required>
                            </div>

                            <div class="col-md-2">
                                <label class="asterisk">Date</label>

                               <div class="input-group date generateddate"
                                    id="generateddate-${documentIndex}"
                                    data-target-input="nearest">

                                    <input type="text"
                                        class="form-control datetimepicker-input generated-date"
                                        name="documents[${documentIndex}][generated_date]"
                                        data-target="#generateddate-${documentIndex}"
                                        placeholder="DD-MM-YYYY"
                                        required>

                                    <div class="input-group-append"
                                        data-target="#generateddate-${documentIndex}"
                                        data-toggle="datetimepicker">
                                        <div class="input-group-text">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <button type="button"
                                    class="btn btn-danger remove-document">

                                    <i class="fas fa-minus"></i>
                                    Remove

                                </button>
                            </div>

                        </div>

                        <div class="additional-documents mt-4 ">

                            <label class="font-weight-bold">
                                Additional Documents
                            </label>

                            <div class="additional-document-list">

                                <div class="form-row additional-document-row mb-2">

                                    <div class="col-md-5">
                                        <label>Additional Document Type</label>

                                        <select name="documents[${documentIndex}][additional_documents][0][type]"
                                            class="form-control select2 additional-type">

                                            <option value="">Select Type</option>

                                            <option value="Exit Plan Document">
                                                Exit Plan Document
                                            </option>

                                            <option value="Authorisation Document">
                                                Authorisation Document
                                            </option>

                                            <option value="Bank Confirmation Letter">
                                                Bank Confirmation Letter
                                            </option>
                                            <option value="Signanture Confirmation Letter">Signanture
                                                Confirmation
                                                Letter
                                            </option>
                                            <option value="Investment Transfer Confirmation Letter">Investment
                                                Transfer Confirmation Letter
                                            </option>

                                        </select>
                                    </div>

                                    <div class="col-md-5">
                                        <label>Upload File</label>

                                        <input type="file"
                                            name="documents[${documentIndex}][additional_documents][0][file]"
                                            class="form-control additional-file">
                                    </div>

                                    <div class="col-md-2 d-flex align-items-end">
                                        <button type="button"
                                            class="btn btn-success add-additional-document">

                                            <i class="fas fa-plus"></i>
                                            Add More

                                        </button>
                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>
                `;

                $('#document_rows').append(row);

                let newGroup = $('#document_rows .document-group').last();

                initializeSelect2(newGroup);
                initializeDatePicker(newGroup);
            });

            // ---------- Remove Main Document ----------

            $(document).on('click', '.remove-document', function() {
                const $group = $(this).closest('.document-group');

                $group.find('select.select2').each(function() {
                    if ($(this).hasClass('select2-hidden-accessible')) {
                        $(this).select2('destroy');
                    }
                });

                $group.remove();
            });

            // ---------- Add Additional Document ----------

            $(document).on('click', '.add-additional-document', function() {
                let documentGroup = $(this).closest('.document-group');
                let documentIndex = $('.document-group').index(documentGroup);
                let additionalList = documentGroup.find('.additional-document-list');

                let additionalIndex = additionalList.find('.additional-document-row').length;

                let row = `
                        <div class="form-row additional-document-row mb-2">
                            <div class="col-md-5">
                                <select name="documents[${documentIndex}][additional_documents][${additionalIndex}][type]"
                                    class="form-control select2 additional-type">
                                    <option value="">Select Type</option>
                                    <option value="Exit Plan Document">Exit Plan Document</option>
                                    <option value="Authorisation Document">Authorisation Document</option>
                                    <option value="Bank Confirmation Letter">Bank Confirmation Letter</option>
                                    <option value="Signanture Confirmation Letter">Signanture
                                        Confirmation
                                        Letter
                                    </option>
                                    <option value="Investment Transfer Confirmation Letter">Investment
                                        Transfer Confirmation Letter
                                    </option>
                                </select>
                            </div>

                            <div class="col-md-5">
                                <input type="file"
                                    name="documents[${documentIndex}][additional_documents][${additionalIndex}][file]"
                                    class="form-control additional-file">
                            </div>

                            <div class="col-md-2 d-flex align-items-end">
                                <button type="button"
                                    class="btn btn-danger remove-additional-document">
                                    <i class="fas fa-minus"></i>
                                    Remove
                                </button>
                            </div>
                        </div>
                    `;

                additionalList.append(row);

                let $newRow = additionalList.find('.additional-document-row').last();

                initializeSelect2($newRow);
            });

            $(document).on('click', '.remove-additional-document', function() {
                const $row = $(this).closest('.additional-document-row');

                $row.find('select.select2').each(function() {
                    if ($(this).hasClass('select2-hidden-accessible')) {
                        $(this).select2('destroy');
                    }
                });

                $row.remove();
            });
            // ---------- Form Submit ----------

            $('#documentForm').on('submit', function(e) {
                e.preventDefault();

                let form = this;
                let formData = new FormData(form);
                let documentId = "{{ $document->id }}";

                $.ajax({
                    url: "{{ route('investment.contracts.upload', $document->id) }}",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,

                    beforeSend: function() {
                        $('button[type="submit"]')
                            .prop('disabled', true)
                            .text('Processing...');
                    },

                    success: function(response) {
                        $('button[type="submit"]')
                            .prop('disabled', false)
                            .text('Submit');

                        if (response.success) {
                            toastr.success(response.message);
                            window.location.href = redirectUrl;
                        } else {
                            toastr.error(response.message);
                        }
                    },

                    error: function(xhr) {
                        $('button[type="submit"]')
                            .prop('disabled', false)
                            .text('Submit');

                        let errors = xhr.responseJSON?.errors;

                        if (errors) {
                            let msg = '';

                            $.each(errors, function(key, value) {
                                msg += value[0] + '\n';
                            });

                            toastr.error(msg);
                        } else {
                            toastr.error('Server error');
                        }
                    }
                });
            });

        });
    </script>

    <script>
        const redirectUrl =
            "{{ $document->investment_id ? route('investment.contracts.list', $document->investment_id) : route('investmentContracts') }}";
    </script>

    <script>
        $(document).on('click', '.delete-document', function() {
            let id = $(this).data('id');

            Swal.fire({
                title: 'Are you sure?',
                text: 'This document will be deleted.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    showLoader();
                    $.ajax({
                        url: "{{ route('investment.documents.delete', '') }}/" + id,
                        type: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            if (response.success) {
                                hideLoader();
                                toastr.success(response.message);
                                setTimeout(function() {
                                    location.reload();
                                }, 1000);
                            }
                        },
                        error: function(xhr) {
                            hideloader();
                            toastr.success('Something went wrong while deleting the document.');
                        }
                    });
                }
            });
        });
    </script>
@endsection
