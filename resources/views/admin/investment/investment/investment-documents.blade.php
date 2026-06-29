@extends('admin.layout.admin_master')

@section('custom_css')
    <link rel="stylesheet" href="{{ asset('assets/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
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

                        <form method="POST" enctype="multipart/form-data"
                            action="{{ route('investment.contracts.submit', $investment->id) }}">
                            @csrf

                            {{-- RADIO ACTION --}}
                            <div class="form-row mb-3">
                                <div class="col-md-12">

                                    <label class="mr-3">
                                        <input type="radio" name="action_type" value="upload" checked>
                                        Upload
                                    </label>

                                    <label class="mr-3">
                                        <input type="radio" name="action_type" value="generate">
                                        Generate
                                    </label>

                                </div>
                            </div>

                            {{-- MAIN FIELDS (UPLOAD MODE) --}}
                            <div id="upload_fields" class="form-row align-items-end">

                                {{-- Version --}}
                                <div class="col-md-3">
                                    <label class="asterisk">Version</label>
                                    <select name="version" class="form-control select2" required>
                                        <option value="">Select Version No</option>
                                        @foreach (InvestorDocVersion() as $key => $item)
                                            <option value="{{ $key }}">{{ $item }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Document Type --}}
                                <div class="col-md-3">
                                    <label class="asterisk">Document Type</label>
                                    <select name="contract_type" class="form-control select2" required>
                                        <option value="">Select Type</option>
                                        @foreach ($formData['doc_types'] as $type)
                                            <option value="{{ $type->id }}">
                                                {{ $type->investor_agreement_type }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- File --}}
                                <div class="col-md-3" id="file_upload">
                                    <label class="field-label" id="file_label">
                                        Upload File
                                    </label>
                                    <input type="file" name="document" class="form-control">
                                </div>

                                {{-- Date --}}
                                <div class="col-md-3" id="date">
                                    <label class="field-label" id="date_label">
                                        Date
                                    </label>
                                    <input type="date" name="generated_date" class="form-control">
                                </div>

                            </div>

                            {{-- ADDITIONAL DOC --}}
                            <div class="form-row mt-3">
                                <div class="col-md-12">
                                    <div class="form-check">
                                        <input type="checkbox" name="has_additional_doc" class="form-check-input"
                                            id="additional_doc_check" value="1">

                                        <label class="form-check-label" for="additional_doc_check">
                                            Add Additional Document
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div id="additional_doc_section" class="form-row mt-3 d-none">

                                <div class="col-md-4">
                                    <label id="add_doc_type_label">
                                        Additional Document Type
                                    </label>

                                    <select name="additional_contract_type" class="form-control select2">
                                        <option value="">Select Type</option>
                                        <option value="exit_plan_document">Exit Plan Document</option>
                                        <option value="authorisation_document">Authorisation Document</option>
                                        <option value="bank_confirmation_letter">Bank Confirmation Letter</option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label id="add_doc_file_label">
                                        Upload File
                                    </label>

                                    <input type="file" name="additional_document" class="form-control">
                                </div>

                            </div>

                            {{-- BUTTON --}}
                            <div class="form-row mt-4">
                                <div class="col-md-12 text-right">
                                    <button type="submit" class="btn btn-primary">
                                        Submit
                                    </button>
                                </div>
                            </div>

                        </form>

                    </div>
                </div>

            </div>
        </section>

    </div>
@endsection

@section('custom_js')
    <script src="{{ asset('assets/select2/js/select2.full.min.js') }}"></script>

    <script>
        $(document).ready(function() {

            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%'
            });

            function toggleActionFields() {
                let type = $('input[name="action_type"]:checked').val();

                if (type === 'upload') {
                    // $('#upload_fields').show();
                    $('#date').show();
                    $('#file_upload').show();
                    $('[name="document"]').prop('required', true);
                    $('[name="generated_date"]').prop('required', true);

                    // add asterisk class
                    $('#file_label').addClass('asterisk');
                    $('#date_label').addClass('asterisk');
                } else {
                    // $('#upload_fields').hide();
                    $('#date').hide();
                    $('#file_upload').hide();
                    $('[name="document"]').prop('required', false);
                    $('[name="generated_date"]').prop('required', false);

                    // remove asterisk class
                    $('#file_label').removeClass('asterisk');
                    $('#date_label').removeClass('asterisk');
                }
            }

            toggleActionFields();

            $('input[name="action_type"]').on('change', function() {
                toggleActionFields();
            });

            $('#additional_doc_check').on('change', function() {
                let checked = this.checked;

                // toggle section
                $('#additional_doc_section').toggleClass('d-none', !checked);

                // inputs
                $('[name="additional_contract_type"]').prop('required', checked);
                $('[name="additional_document"]').prop('required', checked);

                // asterisk labels
                $('#add_doc_type_label').toggleClass('asterisk', checked);
                $('#add_doc_file_label').toggleClass('asterisk', checked);
            });

        });
    </script>
@endsection
