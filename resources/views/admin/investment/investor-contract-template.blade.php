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
                            <div class="card-header">
                                <!-- <h3 class="card-title">Property Details</h3> -->
                                <span class="float-right">
                                    <a class="btn btn-info float-right m-1"
                                        href="{{ route('inv-contract-template.printview', 1) }}">Add
                                        New Contract</a>
                                </span>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <div class="row d-flex justify-content-center">
                                    <div class="col-md-6">
                                        <div class="card card-widget  shadow-sm ">
                                            <div
                                                class="bg-gradient-olive d-flex justify-content-between pl-4 pr-4 py-2 widget-user-header">
                                                <div>
                                                    <h5 class="widget-user-username"><i
                                                            class="fa fa-solid mx-1 fa-file-contract"></i>
                                                        {{ 'Mudarabah' }}
                                                    </h5>
                                                </div>

                                                <h5><span class="badge badge-olive">Version 1</span>

                                                </h5>
                                            </div>
                                            <div class="card-footer p-0">
                                                <ul class="nav flex-column">
                                                    <li class="nav-item">
                                                    <li class="nav-item">
                                                        <div class="nav-link">
                                                            Phone Number
                                                            <span
                                                                class="float-right text-bold">{{ 'tenant_mobile' }}</span>
                                                        </div>
                                                    </li>
                                                    </li>
                                                    <li class="nav-item">
                                                        <div class="nav-link">
                                                            Contact person <span
                                                                class="float-right text-bold text-capitalize ">{{ 'contact_person' }}</span>
                                                        </div>
                                                    </li>
                                                    <li class="nav-item">
                                                        <div class="nav-link">
                                                            Contact Number <span
                                                                class="float-right text-bold">{{ 'contact_number' }}</span>
                                                        </div>
                                                    </li>
                                                    <li class="nav-item">
                                                        <div class="nav-link">
                                                            Contact Email <span
                                                                class="float-right text-bold">{{ 'contact_email' }}</span>
                                                        </div>
                                                    </li>
                                                    <li class="nav-item px-3 py-2">
                                                        <a href="{{ route('tenant-registration.show', 1) }}"
                                                            class="btn btn-sm btn-info" target="_blank">
                                                            <i class="fa fa-eye"></i> View More
                                                        </a>
                                                    </li>

                                                </ul>
                                            </div>
                                        </div>
                                        <!-- /.widget-user -->
                                    </div>
                                </div>

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
    <script src="https://cdn.tiny.cloud/1/r2m02yla43z92hhl0kmzi68mw5s755pgapfp5ckbzmohshs6/tinymce/8/tinymce.min.js"
        referrerpolicy="origin" crossorigin="anonymous"></script>
    <script>
        tinymce.init({
            selector: 'textarea',
            plugins: [
                // Core editing features
                'anchor', 'autolink', 'charmap', 'codesample', 'emoticons', 'link', 'lists', 'media',
                'searchreplace', 'table', 'visualblocks', 'wordcount',
                // Premium features
                'checklist', 'mediaembed', 'casechange', 'formatpainter', 'pageembed', 'a11ychecker',
                'tinymcespellchecker', 'permanentpen', 'powerpaste', 'advtable', 'advcode', 'advtemplate',
                'tinymceai', 'uploadcare', 'mentions', 'tinycomments', 'tableofcontents', 'footnotes',
                'mergetags', 'autocorrect', 'typography', 'inlinecss', 'markdown', 'importword', 'exportword',
                'exportpdf'
            ],
            toolbar: 'undo redo | tinymceai-chat tinymceai-quickactions tinymceai-review | blocks fontfamily fontsize | bold italic underline strikethrough | link media table mergetags | addcomment showcomments | spellcheckdialog a11ycheck typography uploadcare | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
            tinycomments_mode: 'embedded',
            tinycomments_author: 'Author name',
            mergetags_list: [{
                    value: 'First.Name',
                    title: 'First Name'
                },
                {
                    value: 'Email',
                    title: 'Email'
                },
            ],
            tinymceai_token_provider: async () => {
                await fetch(
                    `https://demo.api.tiny.cloud/1/r2m02yla43z92hhl0kmzi68mw5s755pgapfp5ckbzmohshs6/auth/random`, {
                        method: "POST",
                        credentials: "include"
                    });
                return {
                    token: await fetch(
                        `https://demo.api.tiny.cloud/1/r2m02yla43z92hhl0kmzi68mw5s755pgapfp5ckbzmohshs6/jwt/tinymceai`, {
                            credentials: "include"
                        }).then(r => r.text())
                };
            },
            uploadcare_public_key: '57cd19fc18e3ccfb60c5',
        });
    </script>
@endsection
