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
                                <form action="" id="investorForm" enctype="multipart/form-data">
                                    <input type="hidden" id="investor_id" name="investor[investor_id]"
                                        value="{{ $investor->id ?? '' }}">
                                    <div class="modal-body">
                                        <div class="card card-outline card-info p-4">
                                            <h4>Basic Details</h4>
                                            <hr>
                                            <div class="form-group row">
                                                <div class="col-sm-4">
                                                    <label class="asterisk">Investor Name</label>
                                                    <input type="text" name="investor[investor_name]"
                                                        class="form-control " placeholder="Investor Name"
                                                        value="{{ $investor->investor_name ?? '' }}" required>
                                                </div>
                                                <div class="col-sm-4">
                                                    <label class="asterisk">Investor Name In Arabic</label>
                                                    <input type="text" name="investor[investor_name_arabic]"
                                                        pattern="[‌\u0600-\u06FF\s]+" class="form-control arabic-input"
                                                        placeholder="Investor Name in Arabic"
                                                        value="{{ $investor->investor_name_arabic ?? '' }}" required>
                                                </div>

                                                <div class="col-sm-4">
                                                    <label for="inputEmail3" class="asterisk">Investor
                                                        Mobile</label>
                                                    <input type="number" pattern="[1-9][0-9]{9,14}"
                                                        name="investor[investor_mobile]" id="investor_mobile"
                                                        class="form-control" id="inputEmail3"
                                                        placeholder="CountryCodeMobileNumber (e.g. 971501234567)"
                                                        value="{{ $investor->investor_mobile ?? '' }}" required>
                                                </div>

                                            </div>

                                            <div class="form-group row">
                                                <div class="col-sm-4">
                                                    <label for="inputEmail3" class="asterisk">Investor
                                                        Email</label>
                                                    <input type="text" class="form-control"
                                                        name="investor[investor_email]" placeholder="Investor Email"
                                                        value="{{ $investor->investor_email ?? '' }}" required>
                                                </div>
                                                <div class="col-sm-4">
                                                    <label class="asterisk">Nationality</label>
                                                    <select name="investor[nationality_id]" class="form-control select2"
                                                        required>
                                                        <option value="">Select Nationality</option>
                                                        @foreach ($nationalities as $nationality)
                                                            <option value="{{ $nationality->id }}"
                                                                {{ $nationality->id == $investor?->nationality_id ? 'selected' : '' }}>
                                                                {{ $nationality->nationality_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-sm-4">
                                                    <label class="asterisk">Country of Residence</label>
                                                    <select name="investor[country_of_residence]"
                                                        class="form-control select2" required>
                                                        <option value="">Select Country</option>
                                                        @foreach ($nationalities as $nationality)
                                                            <option value="{{ $nationality->id }}"
                                                                {{ $nationality->id == $investor?->country_of_residence ? 'selected' : '' }}>
                                                                {{ $nationality->nationality_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                            </div>
                                            <div class="form-group row">
                                                <div class="col-md-4">
                                                    <label for="inputEmail3" class="asterisk">Preferred
                                                        Payment
                                                        Method</label>
                                                    <select class="form-control select2" name="investor[payment_mode_id]"
                                                        required>
                                                        <option value="">Select Payment Method</option>
                                                        @foreach ($paymentModes as $paymentMode)
                                                            <option value="{{ $paymentMode->id }}"
                                                                {{ $paymentMode->id == $investor?->payment_mode_id ? 'selected' : '' }}>
                                                                {{ $paymentMode->payment_mode_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-sm-4">
                                                    <label for="inputEmail3" class="asterisk">Emirates ID/
                                                        Other
                                                        ID</label>
                                                    <input type="text" name="investor[id_number]" id="id_number"
                                                        class="form-control" placeholder="Emirates ID/ Other ID"
                                                        value="{{ $investor->id_number ?? '' }}" required>
                                                </div>
                                                <div class="col-sm-4">
                                                    <label for="inputEmail3" class="">Passport Number</label>
                                                    <input type="text" name="investor[passport_number]"
                                                        id="passport_number" class="form-control"
                                                        placeholder="Passport Number"
                                                        value="{{ $investor->passport_number ?? '' }}">
                                                </div>

                                            </div>
                                            <div class="form-group row">
                                                <div class="col-sm-4">
                                                    <label for="inputEmail3" class="">Referral</label>
                                                    <select class="form-control select2" name="investor[referral_id]">
                                                        <option value="">Select Referral</option>
                                                        @foreach ($investorsLists as $investorsList)
                                                            <option value="{{ $investorsList->id }}"
                                                                {{ $investorsList->id == $investor?->referral_id ? 'selected' : '' }}>
                                                                {{ $investorsList->investor_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-sm-4">
                                                    <label for="inputEmail3" class="asterisk">Investor Relation</label>
                                                    <select class="form-control select2"
                                                        name="investor[investor_relation_id]" required>
                                                        <option value="">Select Relation</option>
                                                        @foreach ($relations as $relation)
                                                            <option value="{{ $relation->id }}"
                                                                {{ $investor?->investor_relation_id == $relation->id ? 'selected' : '' }}>
                                                                {{ $relation->relation_name }}</option>
                                                        @endforeach

                                                    </select>
                                                </div>

                                                <div class="col-sm-4">
                                                    <label for="inputEmail3" class="asterisk">Payout
                                                        Batch</label>
                                                    <select class="form-control select2" name="investor[payout_batch_id]"
                                                        required>
                                                        <option value="">Select Batch</option>
                                                        @foreach ($payoutbatches as $payoutbatch)
                                                            <option value="{{ $payoutbatch->id }}"
                                                                {{ $payoutbatch->id == $investor?->payout_batch_id ? 'selected' : '' }}>
                                                                {{ $payoutbatch->batch_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>


                                            </div>
                                            <div class="form-group row">
                                                <div class="col-sm-4">
                                                    <label for="inputEmail3" class="col-form-label">Profit Release
                                                        Date </label>
                                                    <select name="investor[profit_release_date]" id="profitReleaseDate"
                                                        class="form-control select2">
                                                        <option value="">Select Day</option>
                                                        @for ($i = 1; $i < 32; $i++)
                                                            <option value="{{ $i }}"
                                                                {{ $i == $investor?->profit_release_date ? 'selected' : '' }}>
                                                                {{ $i }}
                                                            </option>
                                                        @endfor
                                                    </select>
                                                    {{-- <div class="input-group date" id="profitReleaseDate"
                                                        data-target-input="nearest">
                                                        <input type="text" name="investor[profit_release_date]"
                                                            class="form-control datetimepicker-input"
                                                            data-target="#profitReleaseDate"
                                                            value="{{ $investor->profit_release_date ?? '' }}"
                                                            placeholder="dd-mm-YYYY" />

                                                        <div class="input-group-append" data-target="#profitReleaseDate"
                                                            data-toggle="datetimepicker">
                                                            <div class="input-group-text"><i class="fa fa-calendar"></i>
                                                            </div>
                                                        </div>
                                                    </div> --}}
                                                </div>
                                            </div>


                                        </div>

                                        <div class="card card-outline card-info p-4">
                                            <h4>Investor Address</h4>
                                            <hr>

                                            <div class="form-group row">
                                                <div class="col-sm-4">
                                                    <label for="inputEmail3" class="asterisk">Address Line 1</label>
                                                    <input type="text" class="form-control"
                                                        name="investor[investor_address]"
                                                        placeholder="Flat no, Buiding name"
                                                        value="{{ $investor->investor_address ?? '' }}" id=""
                                                        required>
                                                </div>

                                                <div class="col-sm-4">
                                                    <label for="inputEmail3" class="">Address Line 2</label>
                                                    <input type="text" class="form-control"
                                                        name="investor[address_line2]" placeholder="Street"
                                                        value="{{ $investor->address_line2 ?? '' }}" id="">
                                                </div>

                                                <div class="col-sm-4">
                                                    <label for="inputEmail3" class="asterisk">City</label>
                                                    <input type="text" class="form-control" name="investor[city]"
                                                        value="{{ $investor->city ?? '' }}" placeholder="City"
                                                        id="" required>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <div class="col-sm-4">
                                                    <label for="inputEmail3"
                                                        class="asterisk">State/Province/Region</label>
                                                    <input type="text" class="form-control" name="investor[state]"
                                                        value="{{ $investor->state ?? '' }}" placeholder="State"
                                                        id="" required>
                                                </div>

                                                <div class="col-sm-4">
                                                    <label for="inputEmail3">Postal Code/ZIP Code</label>
                                                    <input type="text" class="form-control"
                                                        name="investor[postal_code]" placeholder="Postal Code/ZIP Code"
                                                        value="{{ $investor->postal_code ?? '' }}" id="">
                                                </div>

                                                <div class="col-sm-4">
                                                    <label for="inputEmail3" class="asterisk">Country</label>
                                                    <select class="form-control select2" name="investor[country_id]"
                                                        required>
                                                        <option value="">Select Country</option>
                                                        @foreach ($nationalities as $nationality)
                                                            <option value="{{ $nationality->id }}"
                                                                {{ $nationality->id == $investor?->country_id ? 'selected' : '' }}>
                                                                {{ $nationality->nationality_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            {{-- Arabic Address section --}}
                                            <h5>Address In Arabic</h5>
                                            <hr>
                                            <div class="form-group row">
                                                <div class="col-sm-4">
                                                    <label for="inputEmail3" class="asterisk">Address Line 1</label>
                                                    <input type="text" class="form-control arabic-input"
                                                        name="investor[investor_address_arabic]"
                                                        placeholder="Flat no, Buiding name"
                                                        value="{{ $investor->investor_address_arabic ?? '' }}"
                                                        id="" required>
                                                </div>

                                                <div class="col-sm-4">
                                                    <label for="inputEmail3" class="">Address Line 2</label>
                                                    <input type="text" class="form-control arabic-input"
                                                        name="investor[address_line2_arabic]" placeholder="Street"
                                                        value="{{ $investor->address_line2_arabic ?? '' }}"
                                                        id="">
                                                </div>

                                                <div class="col-sm-4">
                                                    <label for="inputEmail3" class="asterisk">City</label>
                                                    <input type="text" class="form-control arabic-input"
                                                        name="investor[city_arabic]"
                                                        value="{{ $investor->city_arabic ?? '' }}" placeholder="City"
                                                        id="" required>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <div class="col-sm-4">
                                                    <label for="inputEmail3"
                                                        class="asterisk">State/Province/Region</label>
                                                    <input type="text" class="form-control arabic-input"
                                                        name="investor[state_arabic]"
                                                        value="{{ $investor->state_arabic ?? '' }}" placeholder="State"
                                                        id="" required>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="card card-outline card-info p-4">
                                            <h4>Investor Bank details</h4>
                                            <hr>

                                            <input type="hidden" name="investor_bank[bank_id]"
                                                value="{{ $investor->primaryBank->id ?? '' }}">

                                            <div class="form-group row">
                                                <div class="col-sm-4">
                                                    <label for="inputEmail3" class="asterisk">Banking Region</label>
                                                    <select class="form-control select2"
                                                        name="investor_bank[banking_region]" required>
                                                        <option value="">Select Region</option>
                                                        <option value="1"
                                                            {{ $investor?->primaryBank->banking_region == 1 ? 'selected' : '' }}>
                                                            Local</option>
                                                        <option value="2"
                                                            {{ $investor?->primaryBank->banking_region == 2 ? 'selected' : '' }}>
                                                            International</option>
                                                    </select>
                                                </div>

                                                <div class="col-md-4">
                                                    <label for="inputEmail3" class="asterisk">Benenficiary
                                                        Name</label>
                                                    <input type="text" name="investor_bank[investor_beneficiary]"
                                                        id="investor_beneficiary" class="form-control"
                                                        placeholder="Benenficiary Name"
                                                        value="{{ $investor->primaryBank->investor_beneficiary ?? '' }}"
                                                        required>
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="inputEmail3" class="asterisk">Benenficiary
                                                        Name In Arabic</label>
                                                    <input type="text"
                                                        name="investor_bank[investor_beneficiary_arabic]"
                                                        id="investor_beneficiary_arabic" class="form-control arabic-input"
                                                        placeholder="Benenficiary Name"
                                                        value="{{ $investor->primaryBank->investor_beneficiary_arabic ?? '' }}"
                                                        required>
                                                </div>
                                            </div>
                                            <div class="form-group row">

                                                <div class="col-md-4">
                                                    <label for="inputEmail3" class="asterisk">Benenficiary Bank
                                                        Name</label>
                                                    <input type="text" name="investor_bank[investor_bank_name]"
                                                        id="investor_bank_name" class="form-control "
                                                        placeholder="Bank Name"
                                                        value="{{ $investor->primaryBank->investor_bank_name ?? '' }}"
                                                        required>
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="inputEmail3" class="asterisk">Benenficiary Bank
                                                        Name Arabic</label>
                                                    <input type="text" name="investor_bank[investor_bank_name_arabic]"
                                                        id="investor_bank_name_arabic" class="form-control arabic-input"
                                                        placeholder="Bank Name"
                                                        value="{{ $investor->primaryBank->investor_bank_name_arabic ?? '' }}"
                                                        required>
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="inputEmail3" class="asterisk">IBAN</label>
                                                    <input type="text" name="investor_bank[investor_iban]"
                                                        id="investor_iban" class="form-control" id="inputEmail3"
                                                        placeholder="IBAN"
                                                        value="{{ $investor->primaryBank->investor_iban ?? '' }}"
                                                        required>
                                                </div>
                                                <input type="hidden" name="investor_bank[is_primary]" value="1">
                                            </div>
                                        </div>

                                        <div class="card card-outline card-info p-4">
                                            <h4>Investor Documents</h4>
                                            <hr>
                                            <div class="form-group row">
                                                @foreach ($documentTypes as $key => $documentType)
                                                    @php
                                                        $class = $req = '';
                                                        if ($documentType->id == 4) {
                                                            $class = 'asterisk';
                                                            $req = 'required';
                                                        }
                                                    @endphp
                                                    <div class="col-md-6">
                                                        <label for="inputEmail3"
                                                            class="col-form-label {{ $class }}">{{ $documentType->label_name }}</label>

                                                        {{-- hidden fields --}}
                                                        <input type="hidden"
                                                            name="inv_doc[{{ $key }}][document_type_id]"
                                                            value="{{ $documentType->id }}">
                                                        <input type="hidden"
                                                            name="inv_doc[{{ $key }}][status_change]"
                                                            value="{{ $documentType->status_change_value }}">
                                                        <input type="hidden"
                                                            name="inv_doc[{{ $key }}][field_name]"
                                                            value="{{ $documentType->field_name }}">
                                                        <input type="{{ $documentType->field_type }}"
                                                            name="inv_doc[{{ $key }}][file]"
                                                            class="form-control"
                                                            accept="{{ $documentType->accept_types }}"
                                                            {{ $req }}>

                                                        {{-- <input type="file"
                                                            name="investor_doc[{{ $documentType->id }}][{{ $documentType->field_name }}]"
                                                            id="{{ $documentType->field_name }}" class="form-control"
                                                            placeholder="Identity File"> --}}
                                                    </div>
                                                @endforeach
                                                {{-- <div class="col-md-6">
                                                    <label for="inputEmail3" class="asterisk">Identity
                                                        File</label>
                                                    <input type="file" name="investor_doc[document_name]"
                                                        id="investor_doc" class="form-control"
                                                        placeholder="Identity File" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="inputEmail3" class="col-form-label">Passport</label>
                                                    <input type="file" name="Property_name" id="Property_name"
                                                        class="form-control" placeholder="Bank Details">
                                                </div> --}}
                                                {{-- </div>
                                            <div class="form-group row"> --}}
                                                {{-- <div class="col-md-6">
                                                    <label for="inputEmail3" class="col-form-label">Supporting
                                                        Document</label>
                                                    <input type="file" name="Property_name" id="Property_name"
                                                        class="form-control" id="inputEmail3" placeholder="IBAN">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="inputEmail3" class="col-form-label">Referral Comm.
                                                        Contract</label>
                                                    <input type="file" name="Property_name" id="Property_name"
                                                        class="form-control" id="inputEmail3"
                                                        placeholder="Referral Comm. Contract">
                                                </div> --}}
                                            </div>
                                        </div>
                                        <!-- /.card-body -->
                                    </div>
                                    <button type="button" id="investorsubmitbutton"
                                        class="btn btn-info float-right">Submit</button>
                                </form>
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
    <!-- Select2 -->
    <script src="{{ asset('assets/select2/js/select2.full.min.js') }}"></script>

    <script src="{{ asset('assets/moment/moment.min.js') }}"></script>
    <script src="{{ asset('assets/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>
    <script src="{{ asset('assets/daterangepicker/daterangepicker.js') }}"></script>
    <!-- DataTables & Plugins -->
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


    <script>
        $('#profitReleaseDate').datetimepicker({
            format: 'DD-MM-YYYY'
        });

        $('#investor_mobile').on('blur', function() {
            phoneValidation(this, 'phone');
        });

        $('#investorsubmitbutton').click(function(e) {
            e.preventDefault();

            let isValid = true;
            $(".error-text").remove(); // clear old errors
            let investorId = $('#investor_id').val();


            // console.log($("#investorForm").find('.is-invalid').length);

            // if ($("#investorForm").find('.is-invalid').length > 0) {
            //     // $('#investorsubmitbutton').prop('disabled', true).addClass('disabled');
            //     toastr.error('Please fill all required fields before submitting.');
            //     return;
            // }
            //  else {
            // $('#investorsubmitbutton').prop('disabled', false).removeClass('disabled');
            // }
            // iF(!checkFormValidity('investorForm', 'investorsubmitbutton')) {

            // };


            // validate ALL required fields
            $("#investorForm").find("[required]:visible").each(function() {
                const value = $(this).val()?.trim();


                // 🔹 Skip file validation during edit
                if (
                    investorId &&
                    $(this).attr('type') === 'file'
                ) {
                    return; // skip this field
                }


                if (!value) {
                    isValid = false;
                    setInvalid(this, "This field is required");
                } else {

                    setValid(this);
                }
            });

            // Validate Select2 fields
            $("#investorForm").find('[required]select.select2').each(function() {
                const value = $(this).val();
                const container = $(this).next('.select2-container');

                // Skip validation if hidden (either the select or its container)
                if (!$(this).is(':visible') || container.css('display') === 'none') {
                    container.removeClass('is-invalid is-valid');
                    return; // skip hidden selects
                }


                if (!value || value.length === 0) {
                    container.addClass('is-invalid').removeClass('is-valid');
                    isValid = false;
                } else {
                    container.addClass('is-valid').removeClass('is-invalid');
                }
            });

            // ✅ Re-validate arabic fields on submit
            $("#investorForm").find('.arabic-input:visible').each(function() {
                validateArabicInput(this); // uses the global function from master layout
                if ($(this).hasClass('is-invalid')) {
                    isValid = false;
                }
            });


            if (!isValid) {
                toastr.error('Please fill all required fields before submitting.');
                return;
            }
            // if ($("#investorForm").find('.is-invalid').length > 0) {
            //     // $('#investorsubmitbutton').prop('disabled', true).addClass('disabled');
            //     toastr.error('Please fill all required fields before submitting.');
            //     return;
            // }

            submitForm(); // everything passed


        });

        // function validateMobile() {
        //     const mobile = document.getElementById('mobile').value.trim();
        //     const error = document.getElementById('mobileError');

        //     const regex = /^\+[1-9]\d{7,14}$/;

        //     if (!regex.test(mobile)) {
        //         error.textContent = 'Enter a valid international mobile number (e.g. +971501234567)';
        //         return false;
        //     }

        //     error.textContent = '';
        //     return true;
        // }

        // helper: invalid


        function submitForm(e) {
            // SHOW loader
            // $('#global-loader').show();
            showLoader();
            let investorId = $('#investor_id').val();


            let url = investorId ?
                `/investor/${investorId}` :
                `/investor`;

            let method = investorId ? 'PUT' : 'POST';



            var form = document.getElementById('investorForm');
            var fdata = new FormData(form);

            fdata.append('_token', $('meta[name="csrf-token"]').attr('content'));
            fdata.append('_method', method);


            $.ajax({
                type: "POST",
                url: url,
                data: fdata,
                dataType: "json",
                processData: false,
                contentType: false,
                success: function(response) {
                    hideLoader();
                    // console.log(response);
                    toastr.success(response.message);
                    window.location.href = "{{ route('investor.index') }}";
                },
                error: function(errors) {
                    hideLoader();
                    toastr.error(errors.responseJSON.message);
                }
            });
        }
    </script>
@endsection
