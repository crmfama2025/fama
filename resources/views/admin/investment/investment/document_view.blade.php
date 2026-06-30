@extends('admin.layout.admin_master')

@section('custom_css')
    <!-- daterange picker -->
    <link rel="stylesheet" href="{{ asset('assets/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet" href="{{ asset('assets/select2/css/select2.min.css') }}">
    <!-- iCheck for checkboxes and radio inputs -->
    <link rel="stylesheet" href="{{ asset('assets/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('assets/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <!-- DataTables -->
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
                            <li class="breadcrumb-item active">Investment</li>
                        </ol>
                        <a href="{{ route('investment.index') }}" class="btn btn-info float-sm-right mx-2 btn-sm ml-2">
                            <i class="fa fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <section class="content">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Documents</h5>
                    </div>

                    <div class="card-body">

                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Document Type</th>
                                    <th>Version</th>
                                    <th>Generated Date</th>
                                    <th>Additional</th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                            <tbody>

                                {{-- MAIN DOCUMENT --}}
                                @if ($document)
                                    <tr>
                                        <td>1</td>
                                        <td>
                                            {{ $document->agreementType->investor_agreement_type ?? '-' }}
                                        </td>
                                        <td>V{{ $document->investor_agreement_template_id }}</td>
                                        <td>{{ $document->created_at->format('d M Y h:i A') }}</td>

                                        <td>
                                            {{ $document->has_additional_doc ?? 0 }}
                                        </td>

                                        <td>
                                            <a href="{{ asset('storage/' . $document->contract_file_path) }}"
                                                target="_blank" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endif


                                {{-- ADDITIONAL DOCUMENTS --}}
                                @if ($document && $document->has_additional_doc)
                                    {{-- @foreach ($document->additionalDocuments as $key => $doc) --}}
                                    <tr>
                                        <td>2</td>

                                        <td>Additional Document</td>

                                        <td>-</td>

                                        <td>{{ $document->created_at->format('d M Y h:i A') }}</td>

                                        <td>-</td>

                                        <td>
                                            <a href="{{ asset('storage/' . $document->additional_file_path) }}"
                                                target="_blank" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    {{-- @endforeach --}}
                                @endif

                            </tbody>
                        </table>

                        @if (!$document)
                            <p class="text-muted">No documents found</p>
                        @endif

                    </div>
                </div>

            </div>
        </section>

        <!-- /.content-wrapper -->
    </div>
@endsection
@section('custom_js')
    <script src="{{ asset('assets/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('assets/moment/moment.min.js') }}"></script>
    <script src="{{ asset('assets/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>
    <script src="{{ asset('assets/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/daterangepicker/daterangepicker.js') }}"></script>
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
@endsection
