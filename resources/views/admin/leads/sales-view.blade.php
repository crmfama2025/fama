@extends('admin.layout.admin_master')

@section('custom_css')
    <style>
        .sales-lead-header {
            background: linear-gradient(135deg,
                    #FAF7F0 0%,
                    #FAF7F0 50%,
                    #FAF7F0 100%);

            border-radius: 8px 8px 0 0;
            padding: 28px 30px;
            color: #0f0e0e;
            position: relative;
            overflow: hidden;
            border-bottom: 1px solid #ECE4D3;
        }

        .sales-lead-header::before {
            content: "";
            position: absolute;
            width: 280px;
            height: 280px;
            border-radius: 50%;
            background: rgba(212, 169, 74, 0.06);
            right: -100px;
            top: -140px;
        }

        .sales-lead-header::after {
            content: "";
            position: absolute;
            width: 180px;
            height: 180px;
            border-radius: 50%;
            background: rgba(212, 169, 74, 0.05);
            right: 150px;
            bottom: -120px;
        }

        .sales-lead-header-inner {
            display: flex;
            align-items: center;
        }

        .sales-lead-icon {
            width: 58px;
            height: 58px;
            min-width: 58px;
            border-radius: 12px;
            background: #efe6d0;
            border: 1px solid #e0d3ac;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #8a7b4e;
            font-size: 24px;
            margin-right: 16px;
        }

        .sales-lead-title {
            font-size: 24px;
            font-weight: 600;
            color: #1f2937;
            margin: 0;
        }

        .sales-lead-company {
            color: #6b7280;
            font-size: 14px;
            margin-top: 5px;
        }

        .sales-lead-status {
            display: inline-flex;
            align-items: center;
            padding: 5px 11px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-left: 12px;
        }

        .sales-lead-status.processing {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .sales-lead-status.pending {
            background: #f3f4f6;
            color: #4b5563;
        }

        .quick-actions .btn-outline-light {
            border-color: #D1C7AE;
            color: #1F2937;
        }

        .quick-actions .btn {
            border-radius: 6px;
            padding: 7px 13px;
        }

        /* Quick Actions */
        .quick-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .quick-action {
            border-radius: 6px;
            padding: 8px 13px;
            font-size: 13px;
            font-weight: 500;
        }

        /* Information */
        .sales-info-item {
            margin-bottom: 18px;
        }

        .sales-info-item:last-child {
            margin-bottom: 0;
        }

        .sales-info-label {
            display: block;
            color: #6c757d;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .4px;
            margin-bottom: 5px;
        }

        .sales-info-value {
            color: #212529;
            font-size: 15px;
            font-weight: 500;
            word-break: break-word;
        }

        .sales-info-value a {
            color: #2f75b5;
            text-decoration: none;
        }

        .sales-info-value a:hover {
            text-decoration: underline;
        }

        /* Requirement */
        .sales-requirement {
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            border-radius: 7px;
            padding: 18px;
            min-height: 150px;
            line-height: 1.7;
            color: #495057;
            white-space: pre-line;
        }

        /* Summary Boxes */
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }

        .summary-box {
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            border-radius: 7px;
            padding: 13px;
        }

        .summary-box-label {
            display: block;
            font-size: 10px;
            text-transform: uppercase;
            font-weight: 700;
            color: #6c757d;
            margin-bottom: 5px;
        }

        .summary-box-value {
            font-size: 15px;
            font-weight: 600;
            color: #212529;
        }

        /* Assigned Info */
        .assigned-sales-box {
            display: flex;
            align-items: center;
            padding: 14px;
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
        }

        .sales-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: #efe6d0;
            color: #8a7b4e;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            font-weight: 600;
        }

        .sales-person-name {
            font-size: 14px;
            font-weight: 600;
            color: #212529;
        }

        .sales-person-role {
            font-size: 12px;
            color: #6c757d;
            margin-top: 2px;
        }

        /* Activity */
        .sales-activity {
            position: relative;
            padding-left: 30px;
            padding-bottom: 24px;
        }

        .sales-activity:last-child {
            padding-bottom: 0;
        }

        .sales-activity::before {
            content: '';
            position: absolute;
            left: 4px;
            top: 4px;
            width: 10px;
            height: 10px;
            background: #2f75b5;
            border-radius: 50%;
            z-index: 2;
        }

        .sales-activity::after {
            content: '';
            position: absolute;
            left: 8px;
            top: 14px;
            width: 2px;
            height: calc(100% - 4px);
            background: #dee2e6;
        }

        .sales-activity:last-child::after {
            display: none;
        }

        .sales-activity-title {
            font-size: 14px;
            font-weight: 600;
            color: #343a40;
        }

        .sales-activity-date {
            font-size: 12px;
            color: #6c757d;
            margin-top: 4px;
        }

        /* Follow Up */
        .follow-up-box {
            border: 1px dashed #d1d5db;
            border-radius: 8px;
            padding: 18px;
            background: #fafafa;
            text-align: center;
        }

        .follow-up-icon {
            width: 42px;
            height: 42px;
            margin: 0 auto 10px;
            border-radius: 50%;
            background: #eef2ff;
            color: #4338ca;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .follow-up-title {
            font-size: 14px;
            font-weight: 600;
            color: #343a40;
        }

        .follow-up-text {
            font-size: 12px;
            color: #6c757d;
            margin-top: 4px;
        }

        /* Responsive */
        @media (max-width: 767px) {
            .sales-lead-header-inner {
                align-items: flex-start;
                flex-wrap: wrap;
            }

            .sales-lead-title {
                font-size: 20px;
            }

            .sales-lead-status {
                margin-left: 0;
                margin-top: 7px;
            }

            .quick-actions {
                margin-top: 18px;
                width: 100%;
            }

            .quick-action {
                flex: 1;
            }

            .summary-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endsection

@section('content')
    <div class="content-wrapper">
        {{-- Page Header --}}
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>{{ $title }}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard.index') }}">Home</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('lead.index') }}">My Leads</a>
                            </li>
                            <li class="breadcrumb-item active">{{ $title }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <div class="card shadow-sm">
                    {{-- Lead Header --}}
                    <div class="sales-lead-header">
                        <div class="sales-lead-header-inner">
                            <div class="sales-lead-icon">
                                <i class="fas fa-user-tie"></i>
                            </div>

                            <div>
                                <div class="d-flex align-items-center flex-wrap">
                                    <h2 class="sales-lead-title">
                                        {{ !empty($lead->contact_person_name) ? ucfirst($lead->contact_person_name) : 'Lead' }}
                                    </h2>
                                    @include('admin.leads.status-badge', ['status' => $lead->status])
                                </div>

                                <div class="sales-lead-company">
                                    <i class="fas fa-building mr-1"></i>
                                    {{ !empty($lead->company_name) ? ucfirst($lead->company_name) : '-' }}
                                </div>
                            </div>

                            {{-- Quick Actions --}}
                            <div class="quick-actions ml-auto">
                                <a href="{{ route('lead.index') }}" class="btn btn-outline-light btn-sm">
                                    <i class="fas fa-arrow-left mr-1"></i>
                                    Back To Leads
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        {{-- Contact Information --}}
                        <div class="sales-card mb-4">
                            <div class="sales-card-header">
                                <h5>
                                    <i class="fas fa-address-card mr-2 text-primary"></i>
                                    Contact Information
                                </h5>
                            </div>

                            <div class="sales-card-body">
                                <div class="row">
                                    <div class="col-md-6 col-lg-3">
                                        <div class="sales-info-item">
                                            <span class="sales-info-label">Company</span>
                                            <div class="sales-info-value">
                                                {{ !empty($lead->company_name) ? ucfirst($lead->company_name) : 'Individual Lead' }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-lg-3">
                                        <div class="sales-info-item">
                                            <span class="sales-info-label">Contact Person</span>
                                            <div class="sales-info-value">
                                                {{ !empty($lead->contact_person_name) ? ucfirst($lead->contact_person_name) : '-' }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-lg-3">
                                        <div class="sales-info-item">
                                            <span class="sales-info-label">Phone</span>
                                            <div class="sales-info-value">
                                                @if ($lead->phone_number)
                                                    {{-- <a href="tel:{{ $lead->phone_number }}"> --}}
                                                    <i class="fas fa-phone mr-1"></i>
                                                    {{ $lead->phone_number }}
                                                    {{-- </a> --}}
                                                @else
                                                    <span class="empty-value">Not provided</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-lg-3">
                                        <div class="sales-info-item">
                                            <span class="sales-info-label">Email</span>
                                            <div class="sales-info-value">
                                                @if ($lead->email)
                                                    {{-- <a href="mailto:{{ $lead->email }}"> --}}
                                                    <i class="fas fa-envelope mr-1"></i>
                                                    {{ $lead->email }}
                                                    {{-- </a> --}}
                                                @else
                                                    <span class="empty-value">Not provided</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Requirement + Summary --}}
                        <div class="row">
                            {{-- Requirement --}}
                            <div class="col-lg-8 mb-4">
                                <div class="sales-card">
                                    <div class="sales-card-header">
                                        <h5>
                                            <i class="fas fa-clipboard-list mr-2 text-primary"></i>
                                            Customer Requirement
                                        </h5>
                                    </div>

                                    <div class="sales-card-body">
                                        <div class="sales-requirement">
                                            @if ($lead->requirement)
                                                {{ $lead->requirement }}
                                            @else
                                                <span class="empty-value">
                                                    No requirement details provided.
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Summary --}}
                            <div class="col-lg-4 mb-4">
                                <div class="sales-card">
                                    <div class="sales-card-header">
                                        <h5>
                                            <i class="fas fa-chart-pie mr-2 text-primary"></i>
                                            Lead Information
                                        </h5>
                                    </div>

                                    <div class="sales-card-body">
                                        <div class="summary-grid">
                                            <div class="summary-box">
                                                <span class="summary-box-label">Lead Source</span>
                                                <div class="summary-box-value">
                                                    {{ $lead->lead_source ?: '-' }}
                                                </div>
                                            </div>

                                            <div class="summary-box">
                                                <span class="summary-box-label">Total Staff</span>
                                                <div class="summary-box-value">
                                                    {{ $lead->total_staff !== null ? number_format($lead->total_staff) : '-' }}
                                                </div>
                                            </div>

                                            <div class="summary-box">
                                                <span class="summary-box-label">Required Location</span>
                                                <div class="summary-box-value">
                                                    {{ !empty($lead->required_location) ? ucfirst($lead->required_location) : '-' }}
                                                </div>
                                            </div>

                                            <div class="summary-box">
                                                <span class="summary-box-label">Created On</span>
                                                <div class="summary-box-value">
                                                    {{ $lead->created_at ? $lead->created_at->format('d M Y') : '-' }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>



                        {{-- Follow Up --}}
                        @include('admin.leads.follow-up-list', ['followUps' => $lead->followUps])


                    </div>
                </div>
            </div>
        </section>
    </div>
    @include('admin.leads.follw-up-modal', [
        'lead' => $lead,
    ])
@endsection

@section('custom_js')
    <script>
        // Status update can be connected to AJAX later.
        $(document).on('click', '#updateLeadStatus', function() {

            // AJAX status update here

        });


        $('#followUpModal').on('shown.bs.modal', function() {

            // Initialize Select2
            $('#followUpOutcome').select2({
                allowClear: true,
                dropdownParent: $('#followUpModal'),
                width: '100%'
            });

            $('#follow_up_type').select2({
                allowClear: true,
                dropdownParent: $('#followUpModal'),
                width: '100%'
            });



        });

        $(document).on('click', '.addFollowUpBtn', function() {

            // Clear form
            $('#followUpForm')[0].reset();

            // IMPORTANT: clear previous edit ID
            $('#followUpId').val('');

            // Clear Select2
            $('#followUpOutcome').val('').trigger('change');
            $('#follow_up_type').val('').trigger('change');

            // Hide conditional sections
            $('#notInterestedReasonWrapper').addClass('d-none');
            $('#meetingDetailsWrapper').addClass('d-none');
            $('#otherOutcomeWrapper').addClass('d-none');

            // Show normal sections
            $('#notesWrapper').removeClass('d-none');
            $('#nextFollowUpWrapper').removeClass('d-none');

            // Reset required
            $('#notInterestedReason').prop('required', false);
            $('#meetingDate').prop('required', false);
            $('#meetingTime').prop('required', false);
            $('#otherOutcome').prop('required', false);

            // Clear validation
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();

            // Add mode
            $('#followUpModalTitle').text('Add Follow-up');

            $('#saveFollowUpBtn').html(
                '<i class="fas fa-save mr-1"></i> Save Follow-up'
            );
        });



        $('#followUpOutcome').on('change', function() {
            let outcome = $(this).val();

            // Hide everything first
            $('#notInterestedReasonWrapper').addClass('d-none');
            $('#meetingDetailsWrapper').addClass('d-none');
            $('#otherOutcomeWrapper').addClass('d-none');
            $('#notesWrapper').removeClass('d-none');
            $('#nextFollowUpWrapper').removeClass('d-none');

            // Reset required fields
            $('#notInterestedReason').prop('required', false);
            $('#meetingDate').prop('required', false);
            $('#meetingTime').prop('required', false);
            $('#otherOutcome').prop('required', false);

            // Not Interested
            if (outcome === '5') {
                $('#notInterestedReasonWrapper').removeClass('d-none');
                $('#notInterestedReason').prop('required', true);
                $('#notesWrapper').addClass('d-none');
            }

            // Meeting Scheduled
            if (outcome === '6') {
                $('#meetingDetailsWrapper').removeClass('d-none');
                $('#meetingDate').prop('required', true);
                $('#meetingTime').prop('required', true);
                $('#nextFollowUpWrapper').addClass('d-none');
                $('#nextFollowUpDate').val('');
                $('#nextFollowUpTime').val('');
            }

            // Others
            if (outcome === '11') {
                $('#otherOutcomeWrapper').removeClass('d-none');
                $('#otherOutcome').prop('required', true);
            }

            // Converted / Lost
            if (outcome === '9' || outcome === '10') {
                $('#nextFollowUpWrapper').addClass('d-none');
                $('#nextFollowUpDate').val('');
                $('#nextFollowUpTime').val('');
            } else {
                $('#nextFollowUpWrapper').removeClass('d-none');
            }
        });

        $('#followUpForm').on('submit', function(e) {
            e.preventDefault();

            let form = $(this);
            let button = $('#saveFollowUpBtn');

            button.prop('disabled', true);

            // Clear previous errors
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();

            let url;
            let followUpId = $('#followUpId').val();

            if (followUpId) {
                // Edit
                url = "{{ route('lead.follow-up.update', [$lead->id, ':followUp']) }}"
                    .replace(':followUp', followUpId);
            } else {
                // Add
                url = "{{ route('lead.follow-up.store', $lead->id) }}";
            }

            showLoader();

            $.ajax({
                url: url,
                type: "POST",
                data: form.serialize(),
                success: function(response) {
                    if (response.success) {
                        $('#followUpModal').modal('hide');
                        form[0].reset();

                        // Reset Select2
                        $('#followUpType').val('').trigger('change');
                        $('#followUpOutcome').val('').trigger('change');

                        toastr.success(response.message);

                        // Reload page to update status/activity/follow-up information
                        setTimeout(function() {
                            location.reload();
                        }, 500);
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;

                        $.each(errors, function(field, messages) {
                            let input = $('[name="' + field + '"]');

                            input.addClass('is-invalid');
                            input.after(
                                '<span class="invalid-feedback d-block">' +
                                messages[0] +
                                '</span>'
                            );
                        });

                        toastr.error('Please check the form and correct the errors.');
                    } else {
                        toastr.error('Unable to save the follow-up. Please try again.');
                    }
                },
                complete: function() {
                    hideLoader();
                    button.prop('disabled', false);
                }
            });
        });

        $(document).on('click', '.editFollowUpBtn', function() {
            let button = $(this);

            // Get values
            let id = button.data('id');
            let status = button.data('status');
            let type = button.data('type');
            let date = button.data('date');
            let notInterestedReason = button.data('not-interested-reason') || '';
            let meetingDate = button.data('meeting-date') || '';
            let meetingTime = button.data('meeting-time') || '';
            let meetingLocation = button.data('meeting-location') || '';
            let notes = button.data('notes') || '';
            let nextDate = button.data('next-date') || '';
            let nextTime = button.data('next-time') || '';

            // Set hidden ID
            $('#followUpId').val(id);

            // Set basic fields
            $('#followUpOutcome').val(status).trigger('change');
            $('#follow_up_type').val(type).trigger('change');
            $('#followUpDate').val(date);
            $('#notInterestedReason').val(notInterestedReason);
            $('#meetingDate').val(meetingDate);
            $('#meetingTime').val(meetingTime);
            $('#meetingLocation').val(meetingLocation);
            $('#followUpNotes').val(notes);
            $('#nextFollowUpDate').val(nextDate);
            $('#nextFollowUpTime').val(nextTime);

            // Change modal title and button
            $('#followUpModalTitle').text('Edit Follow-up');
            $('#saveFollowUpBtn').html('<i class="fas fa-save mr-1"></i> Update Follow-up');

            // Open modal
            $('#followUpModal').modal('show');
        });
        document.getElementById('clearFollowUpTime').addEventListener('click', function() {
            document.getElementById('nextFollowUpTime').value = '';
        });
        $(document).on('click', '.delete-follow-up', function() {

            let followUpId = $(this).data('id');

            Swal.fire({
                title: 'Delete Follow Up?',
                text: 'Are you sure you want to delete this follow-up?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Delete',
                cancelButtonText: 'Cancel'
            }).then((result) => {

                if (result.isConfirmed) {

                    $.ajax({
                        url: "{{ route('lead-follow-up.destroy', ':id') }}".replace(':id',
                            followUpId),
                        type: 'DELETE',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },

                        success: function(response) {

                            if (response.success) {

                                Swal.fire({
                                    title: 'Deleted!',
                                    text: 'Follow-up deleted successfully.',
                                    icon: 'success',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload();
                                });

                            } else {

                                Swal.fire({
                                    title: 'Error',
                                    text: response.message,
                                    icon: 'error'
                                });

                            }
                        },

                        error: function(xhr) {

                            let message = 'Unable to delete follow-up.';

                            if (xhr.responseJSON?.message) {
                                message = xhr.responseJSON.message;
                            }

                            Swal.fire({
                                title: 'Error',
                                text: message,
                                icon: 'error'
                            });
                        }
                    });
                }
            });
        });
    </script>
@endsection
