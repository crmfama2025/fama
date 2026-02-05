@extends('admin.layout.admin_master')

@section('custom_css')
    <!-- iCheck for checkboxes and radio inputs -->
    <link rel="stylesheet" href="{{ asset('assets/icheck-bootstrap/icheck-bootstrap.min.css') }}">
@endsection
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Contract Approval</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Contract Approval</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3 col-sm-6 col-12">
                                        <div class="info-box bg-info">
                                            <span class="info-box-icon"><i class="fas fa-file-contract"></i></span>

                                            <div class="info-box-content">
                                                <span class="info-box-text">Project Number</span>
                                                <span class="info-box-number">P - {{ $contract->project_number }}</span>
                                            </div>
                                            <!-- /.info-box-content -->
                                        </div>
                                        <!-- /.info-box -->
                                    </div>
                                    <!-- /.col -->
                                    <div class="col-md-3 col-sm-6 col-12">
                                        <div class="info-box bg-success">
                                            <span class="info-box-icon"><i class="fas fa-hand-holding-usd"></i></span>

                                            <div class="info-box-content">
                                                <span class="info-box-text">ROI</span>
                                                <span
                                                    class="info-box-number">{{ $contract->contract_rentals->roi_perc }}%</span>
                                            </div>
                                            <!-- /.info-box-content -->
                                        </div>
                                        <!-- /.info-box -->
                                    </div>
                                    <!-- /.col -->
                                    <div class="col-md-3 col-sm-6 col-12">
                                        <div class="info-box bg-warning">
                                            <span class="info-box-icon"><i class="fas fa-chart-line"></i></span>

                                            <div class="info-box-content">
                                                <span class="info-box-text">Profit</span>
                                                <span
                                                    class="info-box-number">{{ toNumeric($contract->contract_rentals->profit_percentage) }}%</span>
                                            </div>
                                            <!-- /.info-box-content -->
                                        </div>
                                        <!-- /.info-box -->
                                    </div>
                                    <!-- /.col -->
                                    <div class="col-md-3 col-sm-6 col-12">
                                        <div class="info-box bg-danger">
                                            <span class="info-box-icon"><i class="fas fa-home"></i></span>

                                            <div class="info-box-content">
                                                <span class="info-box-text">Houses</span>
                                                <span
                                                    class="info-box-number">{{ $contract->contract_unit->unit_type_count }}</span>
                                            </div>
                                            <!-- /.info-box-content -->
                                        </div>
                                        <!-- /.info-box -->
                                    </div>
                                    <!-- /.col -->
                                </div>
                                <!-- /.row -->
                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="card card-primary card-outline">
                                            <div class="card-header">
                                                <h4>Comments</h4>
                                            </div>
                                            <!-- /.card-header -->
                                            <div class="card-body row scrollery">
                                                @if ($comments->isNotEmpty())
                                                    @foreach ($comments as $comment)
                                                        <!-- Post -->
                                                        <div class="post clearfix" style="width: 100%">
                                                            <div class="user-block">
                                                                <img class="img-circle img-bordered-sm"
                                                                    src="{{ $comment->user->profile_path ? asset('storage/' . $comment->user->profile_path) : asset('img copy/avatar.png') }}"
                                                                    alt="User Image">
                                                                <span class="username">
                                                                    {{ $comment->user->first_name }}
                                                                    {{ $comment->user->last_name }}
                                                                </span>
                                                                <span class="description">Date -
                                                                    {{ $comment->created_at }}</span>
                                                            </div>
                                                            <!-- /.user-block -->
                                                            <p>
                                                                {{ $comment->comment }}
                                                            </p>

                                                        </div>
                                                        <!-- /.post -->
                                                    @endforeach
                                                @else
                                                    <div class="post clearfix"> No Comments ... </div>
                                                @endif


                                            </div>
                                            <div class="form-group m-3">
                                                <form class="form-horizontal" id="CommentForm">
                                                    @csrf
                                                    <input type="hidden" name="contract_id" id="contract_id"
                                                        value="{{ $contract->id }}">
                                                    <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">

                                                    <textarea class="form-control form-control-sm" name="comment" placeholder="Response" required></textarea>
                                                    {{-- <div class="input-group-append"> --}}
                                                    <button type="button" class="btn btn-success mt-2"
                                                        onclick="SendComments()">Send</button>
                                                    {{-- </div> --}}

                                                </form>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="col-sm-9">
                                        <div class="card card-primary card-outline">
                                            <!-- /.card-header -->
                                            <div class="card-body row">
                                                <div class="col-sm-3">
                                                    <strong><i class="fas fa-building mr-1"></i> Property</strong>

                                                    <p class="text-muted">
                                                        {{ $contract->property->property_name }}
                                                    </p>

                                                    <strong><i class="fas fa-user mr-1"></i> Vendor</strong>

                                                    <p class="text-muted">
                                                        {{ $contract->vendor->vendor_name }}
                                                    </p>
                                                </div>

                                                <div class="col-sm-3">
                                                    <strong><i class="fas fa-map-marker-alt mr-1"></i> Location</strong>

                                                    <p class="text-muted">{{ $contract->locality->locality_name }},
                                                        {{ $contract->area->area_name }}</p>
                                                </div>

                                                <div class="col-sm-3">
                                                    <strong><i class="fas fa-pencil-alt mr-1"></i> Details</strong>

                                                    <p class="text-muted">
                                                        <span
                                                            class="tag tag-danger">{{ $contract->contract_type->contract_type }}</span>
                                                        <br>
                                                        @php
                                                            $subtype = [];
                                                        @endphp
                                                        @foreach ($contract->contract_unit_details as $unitdetail)
                                                            @php
                                                                // subunittypeName();
                                                                $subtype[] = $unitdetail->subunittype;
                                                            @endphp
                                                        @endforeach
                                                        <span class="tag tag-success">Partition Type :
                                                            {{ subunittypeName(implode(', ', array_unique($subtype))) }}
                                                        </span> <br>
                                                    </p>
                                                </div>

                                                <div class="col-sm-3">
                                                    <span class="tag tag-info">

                                                        <p><a href="{{ url('/download-scope', $contract->contract_scope->id) }}"
                                                                class="tag tag-info">View Scope</a></p>
                                                        <p><a href="{{ route('contract.show', $contract->id) }}"
                                                                class="tag tag-info" target="_blank">View Contract</a></p>

                                                        @php
                                                            $doc = $contract->contract_documents
                                                                ->where('document_type_id', '1')
                                                                ->first();

                                                            $href =
                                                                $doc?->document_type_id == '1'
                                                                    ? asset('storage/' . $doc->original_document_path)
                                                                    : 'javascript:void(0)';
                                                        @endphp

                                                        <a href="{{ $href }}"
                                                            @if ($href !== 'javascript:void(0)') target="_blank" @endif>
                                                            <div class="icheck-success d-inline vcDoc">
                                                                <input type="radio" name="r2" id="radioSuccess1"
                                                                    {{ $doc?->document_type_id == '1' ? 'checked' : '' }}
                                                                    disabled>
                                                                <label for="radioSuccess1" class="mt-0">Vendor
                                                                    Contract</label>
                                                            </div>
                                                        </a>
                                                    </span>

                                                </div>

                                            </div>
                                            <!-- /.card-body -->
                                        </div>
                                    </div>

                                    {{-- </div>

                                <div class="row"> --}}

                                </div>

                                <button type="button" class="btn btn-danger float-right" style="margin-right: 5px;"
                                    data-toggle="modal" data-target="#modal-rejectreason">
                                    <i class="fas fa-window-close"></i> Reject
                                </button>

                                <button type="button" class="btn btn-info float-right" style="margin-right: 5px;"
                                    onclick="approveContract(2)">
                                    <i class="fas fa-thumbs-up"></i> Approve
                                </button>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modal-rejectreason">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Reject Reason</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="" id="" method="POST" enctype="multipart/form-data">
                            <div class="modal-body">
                                <div class="card-body">
                                    <div class="form-group row">
                                        <label for="inputEmail3" class="col-sm-3 col-form-label">Reason</label>
                                        <textarea name="comment" class="form-control" id="reject_reason" cols="10" rows="5"></textarea>
                                    </div>
                                </div>
                                <!-- /.card-body -->
                            </div>
                            <div class="modal-footer justify-content-between">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-info" onclick="approveContract(3)">Send</button>
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
        function approveContract(status) {
            let message = reason = ''
            if (status == 3) {
                message = "You want to reject!";
                reason = $('#reject_reason').val();
            } else {
                message = "You want to approve!";
            }


            Swal.fire({
                title: "Are you sure?",
                text: message,
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes!"
            }).then((result) => {
                if (result.isConfirmed) {

                    $.ajax({
                        type: "POST",
                        url: "{{ route('approve') }}",
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            contract_id: '{{ $contract->id }}',
                            status: status,
                            reason: reason
                        },
                        dataType: "json",
                        success: function(response) {
                            window.location.href = "{{ route('contract.index') }}";
                        }
                    });
                }
            });
        }


        function SendComments() {

            const form = document.getElementById("CommentForm");
            if (!form) return false;

            let isValid = true;

            // Select all required fields inside the form
            const requiredFields = form.querySelectorAll("[required]");

            requiredFields.forEach(field => {
                // Remove previous error style/message
                field.style.borderColor = "";

                if (!field.value.trim()) {
                    isValid = false;
                    field.style.borderColor = "red"; // highlight empty field
                }
            });

            if (isValid) {
                var fdata = new FormData(form);

                Swal.fire({
                    title: "Are you sure?",
                    text: "You want to hold approval!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        fdata.append('contract_status', 5);
                    }

                    $.ajax({
                        type: "POST",
                        url: "{{ route('contract.sendComment') }}",
                        data: fdata,
                        dataType: "json",
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            window.location.href = "{{ route('contract.index') }}";
                        }
                    });
                });
            } else {
                toastr.error("Please fill all required fields.");
            }

            // var form = document.getElementById('CommentForm');
        }
    </script>
@endsection
