@extends('admin.layout.admin_master')

@section('custom_css')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('assets/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/datatables-buttons/css/buttons.bootstrap4.min.css') }}">

    <style>
        .lead-header {
            background: linear-gradient(135deg, #FAF7F0 0%, #FAF7F0 50%, #FAF7F0 100%);
            border-radius: 8px 8px 0 0;
            padding: 28px 30px;
            color: #0f0e0e;
            position: relative;
            overflow: hidden;
            border-bottom: 1px solid #ECE4D3;
        }

        .lead-header::before {
            content: "";
            position: absolute;
            width: 280px;
            height: 280px;
            border-radius: 50%;
            background: rgba(212, 169, 74, 0.06);
            right: -100px;
            top: -140px;
        }

        .lead-header::after {
            content: "";
            position: absolute;
            width: 180px;
            height: 180px;
            border-radius: 50%;
            background: rgba(212, 169, 74, 0.05);
            right: 150px;
            bottom: -120px;
        }

        .lead-header-content {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            width: 100%;
        }

        .lead-icon {
            width: 62px;
            height: 62px;
            min-width: 62px;
            border-radius: 12px;
            background: #EFE6D0;
            border: 1px solid #E0D3AC;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 18px;
            font-size: 27px;
            color: #8A7B4E;
        }

        .lead-header-info {
            min-width: 0;
        }

        .lead-title {
            font-size: 25px;
            font-weight: 600;
            letter-spacing: .2px;
            color: #1F2937;
        }

        .lead-subtitle {
            margin-top: 8px;
            font-size: 14px;
            color: #6B7280;
            display: flex;
            align-items: center;
            flex-wrap: wrap;
        }

        .lead-subtitle i {
            color: #8A7B4E;
        }

        .separator {
            margin: 0 12px;
            color: #D1C7AE;
        }

        /* .lead-status.active {
                            background: rgba(34, 197, 94, .12);
                            border: 1px solid rgba(34, 197, 94, .25);
                            color: #15803d;
                        }

                        .lead-status.pending {
                            background: rgba(217, 155, 27, .14);
                            border: 1px solid rgba(217, 155, 27, .3);
                            color: #92650b;
                        } */

        .lead-header-actions {
            white-space: nowrap;
        }

        .lead-header-actions .btn-light {
            background: #1F2937;
            color: #fff;
            font-weight: 500;
            border: none;
        }

        .lead-header-actions .btn-outline-light {
            border-color: #D1C7AE;
            color: #1F2937;
        }

        .lead-header-actions .btn {
            border-radius: 6px;
            padding: 7px 13px;
        }

        .info-card {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            height: 100%;
            background: #fff;
            overflow: hidden;
        }

        .info-card-header {
            padding: 15px 20px;
            border-bottom: 1px solid #e5e7eb;
            background: #f8fafc;
        }

        .info-card-header h5 {
            margin: 0;
            font-size: 16px;
            font-weight: 600;
            color: #343a40;
        }

        .info-card-body {
            padding: 20px;
        }

        .info-item {
            margin-bottom: 20px;
        }

        .info-item:last-child {
            margin-bottom: 0;
        }

        .info-label {
            display: block;
            font-size: 13px;
            color: #6c757d;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .3px;
            margin-bottom: 6px;
        }

        .info-value {
            font-size: 15px;
            color: #212529;
            font-weight: 500;
            word-break: break-word;
        }

        .info-value a {
            color: #2f75b5;
            text-decoration: none;
        }

        .info-value a:hover {
            text-decoration: underline;
        }

        .requirement-box {
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 18px;
            line-height: 1.7;
            color: #343a40;
            min-height: 120px;
            white-space: pre-line;
        }

        .status-badge {
            display: inline-block;
            padding: 7px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-processing {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .status-pending {
            background: #f3f4f6;
            color: #4b5563;
        }

        .source-badge {
            display: inline-block;
            background: #eef2ff;
            color: #4338ca;
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
        }

        .summary-item {
            display: flex;
            align-items: center;
            margin-bottom: 18px;
        }

        .summary-item:last-child {
            margin-bottom: 0;
        }

        .summary-icon {
            width: 38px;
            height: 38px;
            min-width: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f1f5f9;
            border-radius: 8px;
            margin-right: 12px;
            color: #2f75b5;
        }

        .summary-content {
            min-width: 0;
        }

        .summary-label {
            display: block;
            font-size: 11px;
            color: #6c757d;
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 2px;
        }

        .summary-value {
            font-size: 14px;
            color: #212529;
            font-weight: 500;
        }

        .activity-item {
            position: relative;
            padding-left: 32px;
            padding-bottom: 25px;
        }

        .activity-item:last-child {
            padding-bottom: 0;
        }

        .activity-item::before {
            content: '';
            position: absolute;
            left: 5px;
            top: 4px;
            width: 11px;
            height: 11px;
            background: #2f75b5;
            border-radius: 50%;
            z-index: 2;
        }

        .activity-item::after {
            content: '';
            position: absolute;
            left: 9px;
            top: 15px;
            width: 2px;
            height: calc(100% - 5px);
            background: #dee2e6;
        }

        .activity-item:last-child::after {
            display: none;
        }

        .activity-title {
            font-size: 14px;
            font-weight: 600;
            color: #343a40;
        }

        .activity-date {
            font-size: 12px;
            color: #6c757d;
            margin-top: 3px;
        }

        .activity-user {
            font-size: 12px;
            color: #6c757d;
            margin-top: 4px;
        }



        .action-buttons .btn {
            min-width: 110px;
        }

        .assign-lead-info {
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 15px;
        }

        .assign-lead-icon {
            width: 45px;
            height: 45px;
            border-radius: 8px;
            background: #eef2ff;
            color: #4338ca;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .assign-lead-name {
            font-size: 16px;
            font-weight: 600;
            color: #212529;
        }

        .assign-lead-company {
            font-size: 13px;
            color: #6c757d;
            margin-top: 2px;
        }

        .current-assignment {
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 12px 15px;
        }

        .current-assignment-title {
            font-size: 12px;
            font-weight: 700;
            color: #6c757d;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .current-assignment-details {
            font-size: 13px;
            color: #495057;
        }

        @media (max-width:768px) {
            .lead-header {
                padding: 22px 18px;
            }

            .lead-header-content {
                align-items: flex-start;
                flex-wrap: wrap;
            }

            .lead-icon {
                width: 52px;
                height: 52px;
                min-width: 52px;
                font-size: 22px;
                margin-right: 12px;
            }

            .lead-title {
                font-size: 20px;
            }

            .lead-header-actions {
                width: 100%;
                margin-top: 18px;
                margin-left: 0 !important;
            }

            .lead-subtitle {
                font-size: 13px;
            }

            .separator {
                display: none;
            }
        }

        @media (max-width:767px) {
            .lead-header {
                text-align: left;
            }

            .lead-header .text-md-right {
                text-align: left !important;
            }

            .action-buttons {
                flex-direction: column;
            }

            .action-buttons .btn {
                width: 100%;
                margin-bottom: 8px;
            }
        }
    </style>
@endsection


@section('content')
    <div class="content-wrapper">

        {{-- =========================================
             PAGE HEADER
        ========================================== --}}

        <section class="content-header">

            <div class="container-fluid">

                <div class="row mb-2">

                    <div class="col-sm-6">

                        <h1>
                            {{ $title }}
                        </h1>

                    </div>

                    <div class="col-sm-6">

                        <ol class="breadcrumb float-sm-right">

                            <li class="breadcrumb-item">

                                <a href="{{ route('dashboard.index') }}">
                                    Home
                                </a>

                            </li>

                            <li class="breadcrumb-item">

                                <a href="{{ route('lead.index') }}">
                                    Leads
                                </a>

                            </li>

                            <li class="breadcrumb-item active">
                                {{ $title }}
                            </li>

                        </ol>

                    </div>

                </div>

            </div>

        </section>


        {{-- =========================================
             MAIN CONTENT
        ========================================== --}}

        <section class="content">

            <div class="container-fluid">

                <div class="card shadow-sm">

                    {{-- =================================
                         LEAD HEADER
                    ================================== --}}

                    <div class="lead-header">
                        <div class="lead-header-content">

                            <div class="lead-icon">
                                <i class="fas fa-user-tie"></i>
                            </div>

                            <div class="lead-header-info">
                                <div class="d-flex align-items-center flex-wrap">
                                    <h2 class="lead-title mb-0">
                                        {{ ucfirst($lead->contact_person_name) ?? 'Lead' }}
                                    </h2>
                                    @include('admin.leads.status-badge', ['status' => $lead->status])
                                </div>

                                <div class="lead-subtitle">
                                    <span>
                                        <i class="fas fa-building mr-1"></i>
                                        {{ ucfirst($lead->company_name) ?: 'Individual Lead' }}
                                    </span>

                                    <span class="separator">|</span>

                                    <span>
                                        <i class="fas fa-bullseye mr-1"></i>
                                        {{ $lead->lead_source ?? '-' }}
                                    </span>
                                </div>
                            </div>

                            <div class="lead-header-actions ml-auto">
                                @if (auth()->user()->hasAnyPermission(['leads.assign']) && $lead->status == 0)
                                    @if (!$lead->assigned_to)
                                        <button type="button" class="btn btn-success btn-sm mr-2" data-toggle="modal"
                                            data-target="#assignLeadModal">

                                            <i class="fas fa-user-plus mr-1"></i>
                                            Assign Lead

                                        </button>
                                    @else
                                        <button type="button" class="btn btn-warning btn-sm mr-2" data-toggle="modal"
                                            data-target="#assignLeadModal">

                                            <i class="fas fa-user-edit mr-1"></i>
                                            Reassign

                                        </button>
                                    @endif
                                @endif

                                @if (auth()->user()->hasAnyPermission(['leads.edit']) && !$lead->assigned_to)
                                    <a href="{{ route('lead.edit', $lead->id) }}" class="btn btn-light btn-sm mr-2">

                                        <i class="fas fa-edit mr-1"></i>
                                        Edit Lead

                                    </a>
                                @endif



                                <a href="{{ route('lead.index') }}" class="btn btn-outline-light btn-sm">

                                    <i class="fas fa-arrow-left mr-1"></i>
                                    Back

                                </a>

                            </div>

                        </div>
                    </div>


                    {{-- =================================
                         CARD BODY
                    ================================== --}}

                    <div class="card-body">


                        {{-- =================================
                             CONTACT INFORMATION
                        ================================== --}}

                        <div class="info-card mb-4">

                            <div class="info-card-header">

                                <h5>

                                    <i class="fas fa-address-card mr-2 text-primary"></i>

                                    Contact Information

                                </h5>

                            </div>


                            <div class="info-card-body">

                                <div class="row">


                                    {{-- Company --}}
                                    <div class="col-md-6 col-lg-3">

                                        <div class="info-item">

                                            <span class="info-label">
                                                Company
                                            </span>

                                            <div class="info-value">

                                                {{ !empty($lead->company_name) ? ucfirst($lead->company_name) : '-' }}

                                            </div>

                                        </div>

                                    </div>


                                    {{-- Contact Person --}}
                                    <div class="col-md-6 col-lg-3">

                                        <div class="info-item">

                                            <span class="info-label">
                                                Contact Person
                                            </span>

                                            <div class="info-value">

                                                {{ !empty($lead->contact_person_name) ? ucfirst($lead->contact_person_name) : '-' }}

                                            </div>

                                        </div>

                                    </div>


                                    {{-- Phone --}}
                                    <div class="col-md-6 col-lg-3">

                                        <div class="info-item">

                                            <span class="info-label">
                                                Phone Number
                                            </span>

                                            <div class="info-value">

                                                @if (!empty($lead->phone_number))
                                                    {{-- <a href="tel:{{ $lead->phone_number }}"> --}}

                                                    <i class="fas fa-phone-alt mr-1"></i>

                                                    {{ $lead->phone_number }}

                                                    {{-- </a> --}}
                                                @else
                                                    <span class="empty-value">
                                                        Not provided
                                                    </span>
                                                @endif

                                            </div>

                                        </div>

                                    </div>


                                    {{-- Email --}}
                                    <div class="col-md-6 col-lg-3">

                                        <div class="info-item">

                                            <span class="info-label">
                                                Email
                                            </span>

                                            <div class="info-value">

                                                @if (!empty($lead->email))
                                                    {{-- <a href="mailto:{{ $lead->email }}"> --}}

                                                    <i class="fas fa-envelope mr-1"></i>

                                                    {{ $lead->email }}

                                                    {{-- </a> --}}
                                                @else
                                                    <span class="empty-value">
                                                        Not provided
                                                    </span>
                                                @endif

                                            </div>

                                        </div>

                                    </div>


                                </div>

                            </div>

                        </div>


                        {{-- =================================
                             REQUIREMENT + SUMMARY
                        ================================== --}}

                        <div class="row">


                            {{-- Requirement --}}
                            <div class="col-lg-8 mb-4">

                                <div class="info-card">

                                    <div class="info-card-header">

                                        <h5>

                                            <i class="fas fa-clipboard-list mr-2 text-primary"></i>

                                            Lead Requirement

                                        </h5>

                                    </div>


                                    <div class="info-card-body">

                                        <div class="requirement-box">

                                            @if (!empty($lead->requirement))
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


                            {{-- Lead Summary --}}
                            <div class="col-lg-4 mb-4">

                                <div class="info-card">

                                    <div class="info-card-header">

                                        <h5>

                                            <i class="fas fa-chart-pie mr-2 text-primary"></i>

                                            Lead Summary

                                        </h5>

                                    </div>


                                    <div class="info-card-body">


                                        {{-- Lead Source --}}
                                        <div class="summary-item">

                                            <div class="summary-icon">

                                                <i class="fas fa-bullhorn"></i>

                                            </div>

                                            <div class="summary-content">

                                                <span class="summary-label">
                                                    Lead Source
                                                </span>

                                                <div class="summary-value">

                                                    @if (!empty($lead->lead_source))
                                                        <span class="source-badge">

                                                            {{ $lead->lead_source }}

                                                        </span>
                                                    @else
                                                        -
                                                    @endif

                                                </div>

                                            </div>

                                        </div>


                                        {{-- Total Staff --}}
                                        <div class="summary-item">

                                            <div class="summary-icon">

                                                <i class="fas fa-users"></i>

                                            </div>

                                            <div class="summary-content">

                                                <span class="summary-label">
                                                    Total Staff
                                                </span>

                                                <div class="summary-value">

                                                    {{ $lead->total_staff !== null ? number_format($lead->total_staff) : '-' }}

                                                </div>

                                            </div>

                                        </div>


                                        {{-- Required Location --}}
                                        <div class="summary-item">

                                            <div class="summary-icon">

                                                <i class="fas fa-map-marker-alt"></i>

                                            </div>

                                            <div class="summary-content">

                                                <span class="summary-label">
                                                    Required Location
                                                </span>

                                                <div class="summary-value">

                                                    {{ !empty($lead->required_location) ? ucfirst($lead->required_location) : '-' }}

                                                </div>

                                            </div>

                                        </div>


                                        {{-- Created Date --}}
                                        <div class="summary-item">

                                            <div class="summary-icon">

                                                <i class="far fa-calendar-alt"></i>

                                            </div>

                                            <div class="summary-content">

                                                <span class="summary-label">
                                                    Created On
                                                </span>

                                                <div class="summary-value">

                                                    {{ $lead->created_at ? $lead->created_at->format('d M Y, h:i A') : '-' }}

                                                </div>

                                            </div>

                                        </div>

                                        {{-- Assigned Sales Person --}}
                                        <div class="summary-item">

                                            <div class="summary-icon">
                                                <i class="fas fa-user-tie"></i>
                                            </div>

                                            <div class="summary-content">

                                                <span class="summary-label">
                                                    Assigned Sales Person
                                                </span>

                                                <div class="summary-value">

                                                    @if ($lead->assignedTo)
                                                        {{ $lead->assignedTo->first_name }}
                                                        {{ $lead->assignedTo->last_name }}
                                                    @else
                                                        <span class="empty-value">
                                                            Not Assigned
                                                        </span>
                                                    @endif

                                                </div>

                                            </div>

                                        </div>


                                        {{-- Assigned By --}}
                                        @if ($lead->assignedBy)
                                            <div class="summary-item">

                                                <div class="summary-icon">
                                                    <i class="fas fa-user-check"></i>
                                                </div>

                                                <div class="summary-content">

                                                    <span class="summary-label">
                                                        Assigned By
                                                    </span>

                                                    <div class="summary-value">

                                                        {{ $lead->assignedBy->first_name }}
                                                        {{ $lead->assignedBy->last_name }}

                                                    </div>

                                                </div>

                                            </div>
                                        @endif


                                        {{-- Assigned Date --}}
                                        @if ($lead->assigned_at)
                                            <div class="summary-item">

                                                <div class="summary-icon">
                                                    <i class="far fa-calendar-check"></i>
                                                </div>

                                                <div class="summary-content">

                                                    <span class="summary-label">
                                                        Assigned Date
                                                    </span>

                                                    <div class="summary-value">

                                                        {{ $lead->assigned_at->format('d M Y, h:i A') }}

                                                    </div>

                                                </div>

                                            </div>
                                        @endif


                                    </div>

                                </div>

                            </div>

                        </div>


                        @include('admin.leads.follow-up-list', ['followUps' => $lead->followUps])

                        {{-- =================================
                             ACTIVITY / AUDIT
                        ================================== --}}

                        <div class="info-card mb-4">

                            <div class="info-card-header">

                                <h5>

                                    <i class="fas fa-history mr-2 text-primary"></i>

                                    Lead Activity

                                </h5>

                            </div>


                            <div class="info-card-body">

                                <div class="row">


                                    {{-- Created --}}
                                    <div class="col-md-4">

                                        <div class="activity-item">

                                            <div class="activity-title">

                                                Lead Created

                                            </div>


                                            <div class="activity-date">

                                                <i class="far fa-clock mr-1"></i>

                                                {{ $lead->created_at ? $lead->created_at->format('d M Y, h:i A') : '-' }}

                                            </div>


                                            @if ($lead->addedBy)
                                                <div class="activity-user">

                                                    <i class="fas fa-user mr-1"></i>

                                                    {{ $lead->addedBy->first_name ?? '' }}
                                                    {{ $lead->addedBy->last_name ?? '' }}

                                                </div>
                                            @endif

                                        </div>

                                    </div>


                                    {{-- Updated --}}
                                    <div class="col-md-4">

                                        <div class="activity-item">

                                            <div class="activity-title">

                                                Last Updated

                                            </div>


                                            <div class="activity-date">

                                                <i class="far fa-clock mr-1"></i>

                                                {{ $lead->updated_at ? $lead->updated_at->format('d M Y, h:i A') : '-' }}

                                            </div>


                                            @if ($lead->updatedBy)
                                                <div class="activity-user">

                                                    <i class="fas fa-user-edit mr-1"></i>

                                                    {{ $lead->updatedBy->first_name ?? '' }}
                                                    {{ $lead->updatedBy->last_name ?? '' }}

                                                </div>
                                            @endif

                                        </div>

                                    </div>


                                    {{-- Status --}}
                                    <div class="col-md-4">

                                        <div class="activity-item">

                                            <div class="activity-title">

                                                Current Status

                                            </div>


                                            <div class="activity-date">

                                                @if ((int) $lead->status === 1)
                                                    <span class="text-primary">
                                                        <i class="fas fa-spinner mr-1"></i>
                                                        Processing
                                                    </span>
                                                @else
                                                    <span class="text-secondary">
                                                        <i class="fas fa-clock mr-1"></i>
                                                        Pending
                                                    </span>
                                                @endif

                                            </div>

                                        </div>

                                    </div>


                                </div>

                            </div>

                        </div>


                    </div>


                    {{-- =================================
                         FOOTER ACTIONS
                    ================================== --}}

                    <div class="card-footer">

                        <div class="action-buttons d-flex justify-content-between">


                            {{-- Back --}}
                            <a href="{{ route('lead.index') }}" class="btn btn-secondary">

                                <i class="fas fa-arrow-left mr-1"></i>

                                Back to Leads

                            </a>


                            {{-- Edit --}}
                            <a href="{{ route('lead.edit', $lead->id) }}" class="btn btn-primary">

                                <i class="fas fa-edit mr-1"></i>

                                Edit Lead

                            </a>


                        </div>

                    </div>

                </div>

            </div>

        </section>

    </div>


    @include('admin.leads.assign-modal', [
        'lead' => $lead,
        'salesPersons' => $salesPerson,
    ])
@endsection

@section('custom_js')
    <!-- DataTables  & Plugins -->
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.22.5/dist/sweetalert2.all.min.js"></script>
    <script>
        $('#assignLeadModal').on('shown.bs.modal', function() {

            $('#assigned_to').select2({
                placeholder: 'Select Sales Person',
                allowClear: true,
                dropdownParent: $('#assignLeadModal'),
                width: '100%'
            });

        });
        $('#assignLeadForm').on('submit', function(e) {

            e.preventDefault();

            let form = $(this);
            let button = $('#assignLeadBtn');

            button.prop('disabled', true);

            $.ajax({
                url: "{{ route('lead.assign', $lead->id) }}",
                type: "POST",
                data: form.serialize(),

                success: function(response) {

                    if (response.success) {

                        $('#assignLeadModal').modal('hide');

                        toastr.success(response.message);

                        setTimeout(function() {
                            location.reload();
                        }, 500);
                    }
                },

                error: function(xhr) {

                    if (xhr.status === 422) {

                        $.each(xhr.responseJSON.errors, function(key, value) {
                            toastr.error(value[0]);
                        });

                    } else {

                        toastr.error(
                            'Unable to assign the lead. Please try again.'
                        );
                    }
                },

                complete: function() {
                    button.prop('disabled', false);
                }
            });

        });
    </script>
@endsection
