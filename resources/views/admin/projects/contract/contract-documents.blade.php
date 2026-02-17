@extends('admin.layout.admin_master')

@section('custom_css')
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
                                                        <td>{{ $document->document_type->label_name }}</td>
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
                                                                    class="btn btn-info">Release</a>
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
                                    @foreach ($documentTypes as $key => $documentType)
                                        {{-- @if (($documentType->id == 3 || $documentType->id == 2) && !$contract->is_acknowledgement_released)
                                            @continue
                                        @endif --}}

                                        <div class="form-group row">

                                            <input type="hidden" name="{{ $key }}[document_type]"
                                                value="{{ $documentType->id }}">
                                            <input type="hidden" name="{{ $key }}[status_change]"
                                                value="{{ $documentType->status_change_value }}">
                                            <label for="inputEmail3"
                                                class="col-form-label">{{ $documentType->label_name }}</label>
                                            <input type="{{ $documentType->field_type }}" name="{{ $key }}[file]"
                                                class="form-control" accept="{{ $documentType->accept_types }}">

                                        </div>
                                        @if ($documentType->id == 1)
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-form-label">Signed
                                                    {{ $documentType->label_name }}</label>
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
    @include('admin.projects.contract.includes.contract_document_js')
@endsection
