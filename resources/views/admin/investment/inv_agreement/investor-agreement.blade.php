@extends('admin.layout.admin_master')

@section('custom_css')
    <!-- daterange picker -->
    <link rel="stylesheet" href="{{ asset('assets/daterangepicker/daterangepicker.css') }}">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet" href="{{ asset('assets/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('assets/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('assets/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
@endsection

@section('content')
    <!-- Content Wrapper. Contains page content -->
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
                            <!-- /.card-header -->
                            <div class="card-body">
                                <form action="{{ route('legal_template.store') }}" method="POST" id="agreementVersioning"
                                    enctype="multipart/form-data">
                                    <input type="hidden" id="template_id" name="template_id"
                                        value="{{ $investorTemplate?->id }}">
                                    <div class="modal-body">
                                        <div class="card card-outline card-info p-4">
                                            <h4>New Document Version</h4>
                                            <hr>
                                            <div class="form-group row">
                                                <div class="col-sm-6">
                                                    <label for="inputEmail3" class="asterisk">Agreement Type</label>
                                                    <select name="investor_agreement_type_id" class="form-control select2"
                                                        required>
                                                        <option value="">Select Agreement Type</option>
                                                        @foreach ($invAgreements as $invAgreement)
                                                            <option value="{{ $invAgreement->id }}"
                                                                {{ $invAgreement->id == $investorTemplate?->investor_agreement_type_id ? 'selected' : '' }}>
                                                                {{ $invAgreement->investor_agreement_type }} </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-sm-6">
                                                    <label class="asterisk">Version No</label>
                                                    <select name="version_no" class="form-control select2" required>
                                                        <option value="">Select Version No</option>
                                                        @for ($i = 1; $i <= 10; $i++)
                                                            <option value="{{ $i }}"
                                                                {{ $i == $investorTemplate?->version_no ? 'selected' : '' }}>
                                                                {{ $i }} </option>
                                                        @endfor
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-sm-6">
                                                    <label for="inputEmail3" class="asterisk">Effective From</label>
                                                    <div class="input-group date" id="EffectiveFrom"
                                                        data-target-input="nearest">
                                                        <input type="text" name="effective_from"
                                                            class="form-control datetimepicker-input"
                                                            data-target="#EffectiveFrom"
                                                            value="{{ $investorTemplate?->effective_from }}"
                                                            placeholder="dd-mm-YYYY" />
                                                        <div class="input-group-append" data-target="#EffectiveFrom"
                                                            data-toggle="datetimepicker">
                                                            <div class="input-group-text">
                                                                <i class="fa fa-calendar"></i>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-sm-6">
                                                    <label class="asterisk">Is Active</label>
                                                    <select name="is_active" class="form-control select2" required>
                                                        <option value="1"
                                                            {{ $investorTemplate?->is_active == 1 ? 'selected' : '' }}>Yes
                                                        </option>
                                                        <option value="0"
                                                            {{ $investorTemplate?->is_active == 0 ? 'selected' : '' }}>No
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-sm-12">
                                                    <label class="asterisk">Template (Full HTML)</label>
                                                    <textarea id="template" name="template" rows="30" class="form-control"
                                                        style="width:100%; font-family: monospace; font-size: 14px;">{{ $investorTemplate?->template }}</textarea>
                                                </div>
                                            </div>


                                        </div>

                                        <div class="form-group">
                                            <button type="button" id="submitbutton"
                                                class="btn btn-info float-right">Submit</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
@endsection


@section('custom_js')
    <script src="{{ asset('assets/select2/js/select2.full.min.js') }}"></script>

    <!-- Moment & Date Picker -->
    <script src="{{ asset('assets/moment/moment.min.js') }}"></script>
    <script src="{{ asset('assets/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>

    <script>
        $('#EffectiveFrom').datetimepicker({
            format: 'DD-MM-YYYY'
        });

        $('#submitbutton').click(function(e) {
            e.preventDefault();

            let isValid = true;

            $(".error-text").remove();

            $('#agreementVersioning')
                .find('[required]')
                .each(function() {

                    let value = $(this).val();

                    if ($(this).is('textarea') || $(this).is('input')) {
                        value = value ? value.trim() : '';
                    }

                    if (!value) {
                        isValid = false;
                        setInvalid(this, "This field is required");
                    } else {
                        setValid(this);
                    }
                });

            // Validate Select2 dropdowns
            $('#agreementVersioning')
                .find('select.select2[required]')
                .each(function() {

                    const value = $(this).val();
                    const container = $(this).next('.select2-container');

                    if (!value || value.length === 0) {
                        container.addClass('is-invalid').removeClass('is-valid');
                        isValid = false;
                    } else {
                        container.addClass('is-valid').removeClass('is-invalid');
                    }
                });

            if (!isValid) {
                toastr.error('Please fill all required fields before submitting.');
                return;
            }

            submitForm();
        });

        function submitForm() {

            showLoader();

            let id = $('#template_id').val(); // hidden field for edit
            console.log(id);
            let url = id ?
                `/legal_template/${id}` :
                `/legal_template`;

            let method = id ? 'PUT' : 'POST';

            if (typeof CKEDITOR !== 'undefined') {
                for (let instance in CKEDITOR.instances) {
                    CKEDITOR.instances[instance].updateElement();
                }
            }

            let form = document.getElementById('agreementVersioning');
            let fdata = new FormData(form);

            fdata.append('_token', $('meta[name="csrf-token"]').attr('content'));

            if (id) {
                fdata.append('_method', 'PUT');
            }

            $.ajax({
                type: "POST",
                url: url,
                data: fdata,
                dataType: "json",
                processData: false,
                contentType: false,

                success: function(response) {

                    hideLoader();

                    toastr.success(response.message || 'Document version saved successfully.');

                    window.location.href = "{{ route('legal_template.index') }}";
                },

                error: function(xhr) {

                    hideLoader();

                    if (xhr.status === 422) {

                        let errors = xhr.responseJSON.errors;

                        $.each(errors, function(field, messages) {

                            let element = $('[name="' + field + '"]');

                            if (element.length) {
                                setInvalid(element[0], messages[0]);
                            }
                        });

                        toastr.error('Please correct the highlighted fields.');
                    } else {
                        toastr.error(xhr.responseJSON?.message || 'Something went wrong.');
                    }
                }
            });
        }
    </script>
@endsection
