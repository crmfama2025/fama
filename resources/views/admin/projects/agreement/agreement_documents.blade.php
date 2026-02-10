@extends('admin.layout.admin_master')
@section('custom_css')
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


                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <!-- <h3 class="card-title">Agreement Documents list</h3> -->

                                <span class="float-right">
                                    <button class="btn btn-info float-right m-1" data-toggle="modal"
                                        data-target="#modal-upload">Upload Files</button>
                                </span>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <table class="table agreementTable">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Document name</th>
                                            <th>Document Number</th>
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
                                                <td colspan="4" class="text-center">No documents available</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                <br>

                                <a href="{{ route('agreement.index') }}" class="btn btn-info"><i
                                        class="fas mr-2 fa-arrow-left"></i>Back</a>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </div><!-- /.container-fluid -->
            {{-- {{ dd($documents) }} --}}

            <div class="modal fade" id="modal-upload">
                <div class="modal-dialog modal-lg">
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
                                    @endphp
                                    @foreach ($tenantIdentities as $index => $identity)
                                        @if ($identity->id == 6 && $business_type != 1)
                                            @continue
                                        @endif
                                        <h6 class="font-weight-bold text-cyan mb-3">
                                            {{ $identity->identity_type }}
                                        </h6>
                                        @php
                                            // Find matching document (if exists)
                                            $document = isset($documents)
                                                ? $documents->firstWhere('document_type', $identity->id)
                                                : null;
                                        @endphp

                                        <div class="form-row">
                                            {{-- First Field --}}
                                            {{-- <input type="hidden" name="agreement_id" value="{{ $identity->id }}"> --}}
                                            @if ($identity->id !== 6)
                                                <div class="form-group col-md-6">
                                                    <label for="document_number_{{ $index }}">
                                                        {{ $identity->first_field_label }}
                                                    </label>
                                                    <input type="{{ $identity->first_field_type }}"
                                                        name="documents[{{ $index }}][document_number]"
                                                        id="document_number_{{ $index }}"
                                                        value="{{ $document ? $document->document_number : '' }}"
                                                        class="form-control"
                                                        placeholder="{{ $identity->first_field_label }}">

                                                </div>
                                            @endif
                                            <input type="hidden" name="documents[{{ $index }}][document_type]"
                                                value="{{ $identity->id }}">

                                            {{-- Second Field --}}
                                            <div class="form-group col-md-6">
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
                                                            <a href="{{ $filePath }}" target="_blank" class="mr-1">
                                                                <img src="{{ $filePath }}" class="documentpreview"
                                                                    title="Click to View File" alt="Document">
                                                            </a>
                                                        @endif
                                                        <p class="small text-muted mt-1">
                                                            {{ $document->original_document_name }}</p>


                                                    </div>
                                                @endif
                                            </div>

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
    <script>
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
                    if (typefield == 6) {
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
                    return false;
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
    </script>
@endsection
