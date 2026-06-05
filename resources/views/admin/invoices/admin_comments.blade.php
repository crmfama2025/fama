@extends('admin.layout.admin_master')

@section('custom_css')
    <!-- daterange picker -->
    <link rel="stylesheet" href="{{ asset('assets/daterangepicker/daterangepicker.css') }}">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet" href="{{ asset('assets/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
    <!-- iCheck for checkboxes and radio inputs -->
    <link rel="stylesheet" href="{{ asset('assets/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('assets/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('assets/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/datatables-buttons/css/buttons.bootstrap4.min.css') }}">

    <style>
        .chat-box {
            max-height: 400px;
            overflow-y: auto;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .chat-message {
            max-width: 70%;
            padding: 10px;
            border-radius: 10px;
            margin-bottom: 10px;
        }

        .chat-user {
            background: #e9ecef;
        }

        .chat-admin {
            background: #3a9e57;
            color: #fff;
            margin-left: auto;
        }
    </style>
@endsection

@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Invoice Approval Comments</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Invoice Approval Comments</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <section class="content">
            <div class="container-fluid">

                <!-- Invoice Info -->
                <div class="card">
                    <div class="card-header ">
                        <h3 class="card-title">Invoice Details</h3>

                        <div class="card-tools">
                            <a href="{{ route('invoices.show', $invoice->id) }}" class="btn btn-primary btn-sm"
                                target="_blank">
                                <i class="fas fa-eye"></i> View Full Invoice
                            </a>
                        </div>


                    </div>

                    <div class="card-body">
                        <p><strong>Invoice No:</strong> {{ $invoice->invoice_no }}</p>
                        <p><strong>Tenant:</strong> {{ $invoice->tenant->tenant_name ?? '' }}</p>

                        <p>
                            <strong>Status:</strong>

                            @php
                                $statusLabel = match ($invoice->status) {
                                    2 => 'Approved',
                                    3 => 'On Hold',
                                    default => 'Unknown',
                                };

                                $statusClass = match ($invoice->status) {
                                    2 => 'badge-success',
                                    3 => 'badge-warning',
                                    default => 'badge-secondary',
                                };
                            @endphp

                            <span class="badge {{ $statusClass }}">
                                {{ $statusLabel }}
                            </span>
                        </p>
                    </div>
                </div>

                <!-- Conversation -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="mb-0">Comments</h5>
                    </div>

                    <div class="card-body p-0">

                        <!-- Scrollable chat container -->
                        <div class="chat-box p-3" id="chatBox" style="max-height: 450px; overflow-y: auto;">

                            @forelse($invoice->comments as $comment)
                                <div class="d-flex mb-3">
                                    {{-- @dump($comment->user) --}}

                                    <!-- Avatar -->
                                    <div class="mr-3">
                                        <img src="{{ $comment->user->profile_path
                                            ? asset('storage/' . $comment->user->profile_path)
                                            : asset('assets/img/user.png') }}"
                                            class="rounded-circle" width="45" height="45">
                                    </div>

                                    <!-- Content -->
                                    <div class="flex-grow-1">

                                        <div class="d-flex justify-content-between">
                                            <strong>{{ $comment->user->name }}</strong>

                                            <small class="text-muted">
                                                {{ $comment->created_at->format('d-m-Y') }}
                                            </small>
                                        </div>

                                        <p class="mb-1 text-dark">
                                            {{ $comment->comment }}
                                        </p>
                                    </div>
                                </div>
                                <hr>
                            @empty
                                <p class="text-center text-muted p-3">No comments yet</p>
                            @endforelse

                        </div>
                    </div>
                </div>

                <!-- Reply Box -->
                <div class="card mt-3">
                    <div class="card-body">

                        <form id="commentForm">
                            @csrf

                            <div class="form-group">
                                <label>Add Comment</label>
                                <textarea name="comment" class="form-control" rows="3" required></textarea>
                            </div>

                            <button type="submit" class="btn btn-success" id="sendBtn">
                                Send Comment
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Actions -->
                {{-- <div class="card mt-3">
                    <div class="card-body text-right">

                        <form method="POST" action="{{ route('invoices.approve', $invoice->id) }}">
                            @csrf

                            <button name="status" value="approved" class="btn btn-success">
                                Approve
                            </button>

                            <button name="status" value="rejected" class="btn btn-danger ml-2">
                                Reject
                            </button>

                        </form>

                    </div>
                </div> --}}

            </div>
        </section>
    </div>
@endsection

@section('custom_js')
    <script>
        // Auto scroll to bottom
        let chatBox = document.getElementById('chatBox');
        if (chatBox) {
            chatBox.scrollTop = chatBox.scrollHeight;
        }
    </script>
    <script>
        $(document).ready(function() {

            $('#commentForm').on('submit', function(e) {
                e.preventDefault();

                let form = $(this);
                let formData = form.serialize();
                let url = "{{ route('invoices.comment', $invoice->id) }}";

                $('#sendBtn').prop('disabled', true);
                showLoader();

                $.ajax({
                    url: url,
                    type: "POST",
                    data: formData,
                    success: function(response) {
                        hideLoader();

                        toastr.success(response.message);

                        // clear textarea
                        form.find('textarea[name="comment"]').val('');

                        // OPTIONAL: append comment to chat box instantly
                        window.location.href = "{{ route('invoices.index') }}";

                        // scroll to bottom
                        // $('#chatBox').scrollTop($('#chatBox')[0].scrollHeight);

                    },
                    error: function(xhr) {
                        hideLoader();
                        toastr.error(xhr.responseJSON?.message || 'Something went wrong');
                    },
                    complete: function() {
                        $('#sendBtn').prop('disabled', false);
                        hideLoader();
                    }
                });

            });

        });
    </script>
@endsection
