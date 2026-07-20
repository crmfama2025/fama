@extends('admin.layout.admin_master')

@section('custom_css')
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('assets/daterangepicker/daterangepicker.css') }}">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet" href="{{ asset('assets/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
    <!-- iCheck for checkboxes and radio inputs -->

    <link rel="stylesheet" href="{{ asset('assets/icheck-bootstrap/icheck-bootstrap.min.css') }}">


    <style>
        .contractTable tbody tr {
            background-color: #f6ffff;
        }

        .contractTable thead tr {
            background-color: #D6EEEE;
        }
    </style>
@endsection
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Contract Documents</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
                            <li class="breadcrumb-item active">Contract Documents</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <!-- <h3 class="card-title">Contract Documents list</h3> -->

                                <span class="float-right">
                                    <a href="{{ route('contract.show', $contract->id) }}"
                                        class="btn btn-info float-right m-1" target="_blank">View Contract</a>
                                    <button class="btn btn-info float-right m-1" data-toggle="modal"
                                        data-target="#modal-upload">Upload Files</button>
                                    <button class="btn btn-info float-right m-1" data-toggle="modal"
                                        data-target="#modal-payments">Update Payments</button>
                                </span>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <div class="card-body">
                                    {{-- <h4 class="text-bold">Contract Document List</h4> --}}
                                    <div>
                                        <table class="table contractTable">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Document name</th>
                                                    <th>view</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>1</td>
                                                    <td>Project Scope</td>
                                                    <td><a href="{{ url('/download-scope', $contract->contract_scope->id) }}"
                                                            class="btn btn-info"><i class="far fa-eye"></i></a></td>
                                                </tr>
                                                @foreach ($contractDocuments as $document)
                                                    <tr>
                                                        <td>{{ $loop->iteration + 1 }}</td>
                                                        <td>{{ $document->document_type->label_name }}
                                                            {{ $document->signed_status == 2 ? '(Signed)' : '' }}</td>
                                                        <td>
                                                            @if ($document->signed_document_path)
                                                                <a href="{{ asset('storage/' . $document->signed_document_path) }}"
                                                                    class="btn btn-info" target="_blank"
                                                                    rel="noopener noreferrer"><i class="far fa-eye"></i></a>
                                                                {{-- <a href="{{ $document->original_document_path }}">View</a> --}}
                                                            @elseif($document->original_document_path)
                                                                <a href="{{ asset('storage/' . $document->original_document_path) }}"
                                                                    class="btn btn-info" target="_blank"><i
                                                                        class="far fa-eye"></i></a></a>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                @if ($contract->is_aknowledgement_uploaded == 0 && $contract->contract_status == 7)
                                                    <tr>
                                                        <td>{{ count($contractDocuments) + 2 }}</td>
                                                        <td>Acknowledgement (CRM Generated)</td>
                                                        <td>
                                                            @if ($contract->is_acknowledgement_released)
                                                                <a href="{{ route('contracts.acknowledgement', $contract->id) }}"
                                                                    class="btn btn-info" target="_blank"><i
                                                                        class="far fa-eye"></i></a>
                                                            @else
                                                                <a href="{{ route('contracts.release', $contract->id) }}"
                                                                    class="btn btn-info">Generate</a>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <br>

                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </div><!-- /.container-fluid -->

            <div class="modal fade" id="modal-upload">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Upload Documents</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="" id="ContractUploadForm" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="contract_id" value="{{ $contract->id }}" id="contract_id_upload">
                            <div class="modal-body">
                                <div class="card-body">
                                    <small class="text-danger">* Max File size 10MB</small>
                                    @foreach ($documentTypes as $key => $documentType)
                                        {{-- @if (($documentType->id == 3 || $documentType->id == 2) && !$contract->is_acknowledgement_released)
                                            @continue
                                        @endif --}}

                                        <div class="form-group row">

                                            <input type="hidden" name="{{ $key }}[document_type]"
                                                value="{{ $documentType->id }}">
                                            <input type="hidden" name="{{ $key }}[status_change]"
                                                value="{{ $documentType->status_change_value }}">
                                            <label for="inputEmail3" class="col-form-label">{{ $documentType->label_name }}
                                            </label>
                                            <input type="{{ $documentType->field_type }}" name="{{ $key }}[file]"
                                                class="form-control" accept="{{ $documentType->accept_types }}">

                                        </div>
                                        @if ($documentType->id == 1)
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-form-label">Signed
                                                    {{ $documentType->label_name }} </label>
                                                <input type="file" id="signed"
                                                    name="{{ $key }}[signed_contract]" class="form-control">
                                                {{-- <label class="labelpermission" for="signed"> Signed </label> --}}
                                            </div>
                                        @endif
                                    @endforeach
                                </div>


                                {{-- <div class="card-body">
                                    <div class="form-group row">
                                        <label for="inputEmail3" class="col-form-label">Vendor Contract</label>
                                        <input type="file" name="file" class="form-control">
                                    </div>
                                </div>

                                <div class="card-body">
                                    <div class="form-group row">
                                        <label for="inputEmail3" class="col-form-label">Acknoledgement</label>
                                        <input type="file" name="file" class="form-control">
                                    </div>
                                </div> --}}
                                <!-- /.card-body -->
                                {{-- </div> --}}
                                <div class="modal-footer justify-content-between">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    <button type="submit" id="importBtn" class="btn btn-info">Upload</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
            <!-- /.modal -->

            {{-- Payables modal --}}
            <div class="modal fade" id="modal-payments" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Update Payments</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form id="formUpdatePayments">
                            @csrf
                            <input type="hidden" name="contract_id" value="{{ $contract->id }}">
                            <div class="modal-body">
                                <div class="form-row mb-3">
                                    <div class="form-group col-md-3">
                                        <label>Closing Date <span class="text-danger">*</span></label>
                                        <div class="input-group date" id="closing-date-picker"
                                            data-target-input="nearest">
                                            <input type="text" name="closing_date"
                                                class="form-control datetimepicker-input closing-date"
                                                data-target="#closing-date-picker"
                                                value="{{ $contract->contract_detail->closing_date ? \Carbon\Carbon::parse($contract->contract_detail->closing_date)->format('d-m-Y') : '' }}"
                                                required>
                                            <div class="input-group-append" data-target="#closing-date-picker"
                                                data-toggle="datetimepicker">
                                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group col-md-3">
                                        <div class="d-flex align-items-center">
                                            <label class="mb-0 mr-3 mt-31">Business Type <span
                                                    class="text-danger">*</span></label>
                                            <div class="icheck-primary d-inline-block mr-3">
                                                <input type="radio" id="business_type_b2b" name="business_type"
                                                    value="1"
                                                    {{ old('business_type', $contract->contract_unit->business_type) == '1' ? 'checked' : '' }}>
                                                <label for="business_type_b2b">B2B</label>
                                            </div>
                                            <div class="icheck-primary d-inline-block">
                                                <input type="radio" id="business_type_b2c" name="business_type"
                                                    value="2"
                                                    {{ old('business_type', $contract->contract_unit->business_type) == '2' ? 'checked' : '' }}>
                                                <label for="business_type_b2c">B2C</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="paymentRowsWrapper">
                                    @foreach ($contractPayables as $index => $detail)
                                        <div class="payment-row" data-index="{{ $index }}">
                                            <input type="hidden" name="payments[{{ $index }}][id]"
                                                value="{{ $detail->id }}">
                                            <div class="form-row">
                                                <div class="form-group col-md-3">
                                                    <label>Payment Mode <span class="text-danger">*</span></label>
                                                    <select name="payments[{{ $index }}][payment_mode_id]"
                                                        class="form-control select2 payment-mode-select" required>
                                                        @foreach ($dropdowns['paymentmodes'] as $mode)
                                                            <option value="{{ $mode->id }}"
                                                                {{ $detail->payment_mode_id == $mode->id ? 'selected' : '' }}>
                                                                {{ $mode->payment_mode_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="form-group col-md-3">
                                                    <label>Payment Date <span class="text-danger">*</span></label>
                                                    <div class="input-group date payment-date-picker"
                                                        id="payment-date-picker-{{ $index }}"
                                                        data-target-input="nearest">
                                                        <input type="text"
                                                            name="payments[{{ $index }}][payment_date]"
                                                            class="form-control datetimepicker-input payment-date"
                                                            data-target="#payment-date-picker-{{ $index }}"
                                                            value="{{ \Carbon\Carbon::parse($detail->payment_date)->format('d-m-Y') }}"
                                                            required>
                                                        <div class="input-group-append"
                                                            data-target="#payment-date-picker-{{ $index }}"
                                                            data-toggle="datetimepicker">
                                                            <div class="input-group-text"><i class="fa fa-calendar"></i>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group col-md-3">
                                                    <label>Payment Amount <span class="text-danger">*</span></label>
                                                    <input type="text"
                                                        name="payments[{{ $index }}][payment_amount]"
                                                        class="form-control payment-amount"
                                                        value="{{ $detail->payment_amount }}" required disabled>
                                                </div>
                                                <div class="form-group col-md-3">
                                                    <label>Beneficiary <span class="text-danger">*</span></label>
                                                    <select name="payments[{{ $index }}][beneficiary_id]"
                                                        class="form-control select2 beneficiary-select" required>
                                                        @foreach ($dropdowns['vendors'] as $vendor)
                                                            <option value="{{ $vendor->id }}"
                                                                {{ old("payments.$index.beneficiary_id", $detail->beneficiary_id ?? $contract->vendor_id) == $vendor->id ? 'selected' : '' }}>
                                                                {{ $vendor->vendor_name ?? $vendor->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-row bank-fields">
                                                <div class="form-group col-md-4 bank-name-field">
                                                    <label>Bank Name <span class="text-danger">*</span></label>
                                                    <select name="payments[{{ $index }}][bank_id]"
                                                        class="form-control select2 bank-select">
                                                        <option value="">Select Bank</option>
                                                        @foreach ($dropdowns['banks'] as $bank)
                                                            <option value="{{ $bank->id }}"
                                                                {{ $detail->bank_id == $bank->id ? 'selected' : '' }}>
                                                                {{ $bank->bank_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="form-group col-md-4 cheque-no-field">
                                                    <label>Cheque No <span class="text-danger">*</span></label>
                                                    <input type="text" name="payments[{{ $index }}][cheque_no]"
                                                        class="form-control cheque-no-input"
                                                        value="{{ $detail->cheque_no }}">
                                                </div>
                                            </div>
                                            <hr>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

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
    @include('admin.projects.contract.includes.contract_document_js')

    <script>
        $(function() {


            $('.payment-date-picker').each(function() {
                $(this).datetimepicker({
                    format: 'DD-MM-YYYY',
                    useCurrent: false
                });
            });

            $('#closing-date-picker').datetimepicker({
                format: 'DD-MM-YYYY',
                useCurrent: false
            });

            $('.select2').select2({
                dropdownParent: $('#modal-payments')
            });

            function toggleFieldsByMode(select) {
                const row = select.closest('.payment-row');
                const modeId = select.val();

                const bankField = row.find('.bank-name-field');
                const chequeField = row.find('.cheque-no-field');

                if (modeId == '3') { // Cheque
                    bankField.show();
                    chequeField.show();
                    row.find('.cheque-no-input').attr('required', true);
                } else if (modeId == '2') { // Bank Transfer
                    bankField.show();
                    chequeField.hide();
                    row.find('.cheque-no-input').removeAttr('required').val('');
                } else { // Cash / other
                    bankField.hide();
                    chequeField.hide();
                    row.find('.cheque-no-input').removeAttr('required').val('');
                }
            }

            $('#modal-payments').on('shown.bs.modal', function() {

                $(document).off('focusin.modal');
                $('.payment-mode-select').each(function() {
                    toggleFieldsByMode($(this));
                });
            });

            $(document).on('change', '.payment-mode-select', function() {
                toggleFieldsByMode($(this));
            });

            $('#formUpdatePayments').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    method: 'POST',
                    url: '{{ route('contracts.updatePayables', $contract->id) }}',
                    data: $(this).serialize(),
                    success: function(res) {
                        $('#modal-payments').modal('hide');

                        toastr.success(res.message ?? 'Payments updated successfully');
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            let msg = Object.values(errors).map(e => e[0]).join('<br>');
                            toastr.error(msg);
                        } else {
                            toastr.error('Something went wrong');
                        }
                    }
                });
            });
        });
    </script>
@endsection
