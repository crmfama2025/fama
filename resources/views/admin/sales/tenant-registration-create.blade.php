@extends('admin.layout.admin_master')

@section('custom_css')
    <link rel="stylesheet" href="{{ asset('assets/daterangepicker/daterangepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <style>
        /* ── Section Title (matches existing blade style) ── */
        .section-title {
            font-size: 15px;
            font-weight: 700;
            color: #17a2b8;
            border-left: 4px solid #17a2b8;
            padding-left: 10px;
            margin-bottom: 20px;
        }

        .form-group label {
            font-weight: 600 !important;
            font-size: 13px;
            color: #444;
            margin-bottom: 4px;
        }

        .card-section {
            border: 1px solid #e3e6ea;
            border-radius: 6px;
            padding: 20px;
            margin-bottom: 20px;
        }

        /* ── Business Type Toggle ── */
        .btype-toggle {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        .btype-option {
            position: relative;
        }

        .btype-option input[type="radio"] {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }

        .btype-option label {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 22px 14px;
            background: #f8f9fa;
            border: 2px solid #dee2e6;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
            font-weight: 600 !important;
            font-size: 14px !important;
            color: #6c757d !important;
        }

        .btype-option label .btype-icon {
            font-size: 28px;
            line-height: 1;
        }

        .btype-option label .btype-tag {
            font-size: 11px !important;
            font-weight: 700 !important;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: #adb5bd !important;
        }

        .btype-option input:checked+label {
            border-color: #17a2b8;
            background: rgba(23, 162, 184, 0.07);
            color: #17a2b8 !important;
            box-shadow: 0 0 0 1px #17a2b8;
        }

        .btype-option input:checked+label .btype-tag {
            color: #17a2b8 !important;
        }

        .btype-option label:hover {
            border-color: #adb5bd;
            color: #343a40 !important;
        }

        /* ── Conditional sections ── */
        .conditional-section {
            display: none;
        }

        .conditional-section.visible {
            display: block;
        }

        /* ── File upload ── */
        .file-upload-wrap {
            position: relative;
        }

        .file-upload-wrap input[type="file"] {
            position: absolute;
            inset: 0;
            opacity: 0;
            cursor: pointer;
            width: 100%;
            height: 100%;
            z-index: 2;
        }

        .file-upload-face {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #f8f9fa;
            border: 1px dashed #ced4da;
            border-radius: 4px;
            height: 38px;
            padding: 0 12px;
            font-size: 13px;
            color: #6c757d;
            cursor: pointer;
            transition: border-color 0.2s, background 0.2s;
        }

        .file-upload-face:hover,
        .file-upload-wrap:focus-within .file-upload-face {
            border-color: #17a2b8;
            background: rgba(23, 162, 184, 0.04);
            color: #343a40;
        }

        .file-upload-face.has-file {
            border-color: #28a745;
            background: rgba(40, 167, 69, 0.05);
            color: #28a745;
        }

        /* ── Expiry warning ── */
        .expiry-warn {
            font-size: 11px;
            margin-top: 3px;
            display: none;
        }

        .expiry-warn.show {
            display: block;
        }

        .expiry-warn.expired {
            color: #dc3545;
        }

        .expiry-warn.soon {
            color: #856404;
        }

        /* ── Owner blocks ── */
        .owner-block {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 16px 18px;
            margin-bottom: 14px;
        }

        .owner-block-title {
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            color: #17a2b8;
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .owner-num-badge {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #17a2b8;
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            font-weight: 700;
            flex-shrink: 0;
        }

        .trade-license-block {
            background: rgba(23, 162, 184, 0.05);
            border: 1px solid rgba(23, 162, 184, 0.25);
            border-radius: 6px;
            padding: 16px 18px;
            margin-top: 6px;
        }

        .trade-license-block .owner-block-title {
            color: #0d6efd;
        }

        /* ── Mandatory badge ── */
        .badge-mandatory {
            display: inline-flex;
            align-items: center;
            font-size: 10px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 20px;
            background: rgba(220, 53, 69, 0.1);
            color: #dc3545;
            border: 1px solid rgba(220, 53, 69, 0.2);
            margin-left: 6px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        /* ── B2C doc rows ── */
        .doc-row {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 14px 16px;
            margin-bottom: 10px;
        }

        .doc-row-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }

        .doc-row-title {
            font-size: 12px;
            font-weight: 700;
            color: #17a2b8;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .btn-remove-doc {
            background: rgba(220, 53, 69, 0.08);
            border: 1px solid rgba(220, 53, 69, 0.25);
            border-radius: 4px;
            color: #dc3545;
            cursor: pointer;
            font-size: 11px;
            font-weight: 500;
            padding: 3px 10px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            transition: all 0.2s;
        }

        .btn-remove-doc:hover {
            background: rgba(220, 53, 69, 0.15);
        }

        .btn-add-doc {
            background: transparent;
            border: 1px dashed #ced4da;
            border-radius: 6px;
            color: #6c757d;
            cursor: pointer;
            font-size: 13px;
            font-weight: 500;
            width: 100%;
            height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            margin-top: 4px;
            transition: all 0.2s;
        }

        .btn-add-doc:hover {
            border-color: #17a2b8;
            color: #17a2b8;
            background: rgba(23, 162, 184, 0.04);
        }

        .btn-remove-doc:disabled {
            opacity: 0.4;
            cursor: not-allowed;
            pointer-events: none;
        }

        /* ── Units table ── */
        .units-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .units-table th {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 9px 12px;
            text-align: left;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            color: #6c757d;
            white-space: nowrap;
        }

        .units-table td {
            border: 1px solid #dee2e6;
            padding: 8px 12px;
            vertical-align: middle;
        }

        .units-table tr:hover td {
            background: rgba(23, 162, 184, 0.03);
        }

        .units-table input[type="number"] {
            height: 32px;
            font-size: 13px;
            padding: 0 8px;
            width: 130px;
            border-radius: 4px;
            border: 1px solid #ced4da;
        }

        .units-table input[type="number"]:focus {
            border-color: #17a2b8;
            outline: none;
        }

        .floor-label {
            font-size: 11px;
            font-weight: 700;
            color: #17a2b8;
            background: rgba(23, 162, 184, 0.1);
            border-radius: 4px;
            padding: 2px 8px;
            white-space: nowrap;
        }

        .type-badge {
            font-size: 11px;
            background: #f0f2f5;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 2px 8px;
            color: #6c757d;
        }

        .subunit-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
        }

        .subunit-chip {
            font-size: 10px;
            background: #f0f2f5;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 2px 7px;
            color: #6c757d;
        }

        .subunit-chip.has-rent {
            background: rgba(40, 167, 69, 0.1);
            border-color: rgba(40, 167, 69, 0.3);
            color: #28a745;
            font-weight: 600;
        }

        .btn-subunit {
            background: transparent;
            border: 1px solid #17a2b8;
            border-radius: 5px;
            color: #17a2b8;
            font-size: 12px;
            font-weight: 600;
            padding: 4px 10px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            transition: all 0.2s;
            white-space: nowrap;
        }

        .btn-subunit:hover {
            background: rgba(23, 162, 184, 0.08);
        }

        .expand-row td {
            padding: 0 !important;
            border-top: none !important;
        }

        .expand-inner {
            background: #f0f8fb;
            border-top: 2px solid rgba(23, 162, 184, 0.2);
            overflow: hidden;
            max-height: 0;
            transition: max-height 0.3s ease, padding 0.3s ease;
        }

        .expand-inner.open {
            max-height: 400px;
            padding: 14px 16px 16px;
            overflow-y: auto;
            /* ← add this */

        }

        .subunit-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 10px;
            min-width: max-content;
            /* ← add this so grid doesn't compress */
            width: 100%;
        }

        .expand-inner-title {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            color: #17a2b8;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 6px;
        }



        .subunit-rent-card {
            background: #fff;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 10px 12px;
        }

        .subunit-rent-card-label {
            font-size: 11px;
            font-weight: 700;
            color: #17a2b8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .subunit-rent-card input {
            height: 34px;
            font-size: 12px;
            width: 100% !important;
        }

        /* ── Rent summary display ── */
        .rent-summary-box {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-top: 16px;
        }

        .rent-box {
            background: #f0f8fb;
            border: 1px solid rgba(23, 162, 184, 0.2);
            border-radius: 6px;
            padding: 14px 16px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .rent-box-icon {
            width: 36px;
            height: 36px;
            background: #17a2b8;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            flex-shrink: 0;
        }

        .rent-box-label {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: #6c757d;
            margin-bottom: 2px;
        }

        .rent-box-value {
            font-size: 20px;
            font-weight: 700;
            color: #212529;
        }

        .rent-box-value span {
            font-size: 12px;
            color: #6c757d;
            font-weight: 400;
            margin-left: 4px;
        }

        /* ── Duration pill ── */
        .duration-pill {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #f0f8fb;
            border: 1px solid rgba(23, 162, 184, 0.2);
            border-radius: 6px;
            padding: 10px 14px;
            font-size: 13px;
            color: #6c757d;
            margin-top: 14px;
        }

        /* ── Existing customer ── */
        .existing-customer-toggle {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            background: rgba(23, 162, 184, 0.05);
            border: 1px solid rgba(23, 162, 184, 0.2);
            border-radius: 6px;
            margin-bottom: 16px;
            cursor: pointer;
            transition: background 0.2s;
        }

        .existing-customer-toggle:hover {
            background: rgba(23, 162, 184, 0.1);
        }

        .existing-customer-toggle input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: #17a2b8;
            cursor: pointer;
            flex-shrink: 0;
        }

        .existing-customer-toggle .toggle-label {
            font-size: 14px;
            font-weight: 600 !important;
            color: #212529 !important;
            text-transform: none !important;
        }

        .existing-customer-toggle .toggle-sub {
            font-size: 12px;
            color: #6c757d;
            margin-left: auto;
        }

        .existing-customer-panel {
            display: none;
            margin-bottom: 16px;
        }

        .existing-customer-panel.visible {
            display: block;
        }

        .customer-search-wrap {
            position: relative;
            margin-bottom: 10px;
        }

        .customer-search-wrap input {
            padding-left: 36px !important;
        }

        .customer-search-wrap .search-icon {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            pointer-events: none;
        }

        .customer-list {
            border: 1px solid #dee2e6;
            border-radius: 6px;
            overflow: hidden;
            max-height: 280px;
            overflow-y: auto;
        }

        .customer-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 14px;
            border-bottom: 1px solid #f0f0f0;
            cursor: pointer;
            transition: background 0.15s;
        }

        .customer-item:last-child {
            border-bottom: none;
        }

        .customer-item:hover {
            background: rgba(23, 162, 184, 0.05);
        }

        .customer-item.selected {
            background: rgba(23, 162, 184, 0.08);
            border-left: 3px solid #17a2b8;
        }

        .customer-avatar {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            background: #17a2b8;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 700;
            color: #fff;
            flex-shrink: 0;
        }

        .customer-item-name {
            font-size: 14px;
            font-weight: 600;
            color: #212529;
        }

        .customer-item-meta {
            font-size: 11px;
            color: #6c757d;
            margin-top: 1px;
        }

        .customer-item-badge {
            font-size: 10px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 20px;
            background: rgba(40, 167, 69, 0.1);
            color: #28a745;
            border: 1px solid rgba(40, 167, 69, 0.25);
            white-space: nowrap;
            flex-shrink: 0;
        }

        /* ── Selected customer data ── */
        .selected-customer-data {
            display: none;
            margin-top: 14px;
        }

        .selected-customer-data.visible {
            display: block;
        }

        .selected-customer-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }

        .selected-customer-title {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            color: #17a2b8;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .btn-change-customer {
            background: transparent;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            color: #6c757d;
            font-size: 11px;
            font-weight: 600;
            padding: 3px 10px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-change-customer:hover {
            border-color: #17a2b8;
            color: #17a2b8;
        }

        .customer-data-grid-inner {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 8px;
        }

        .customer-data-field {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 8px 12px;
        }

        .customer-data-field-label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #6c757d;
            margin-bottom: 3px;
        }

        .customer-data-field-value {
            font-size: 13px;
            font-weight: 600;
            color: #212529;
        }

        .customer-doc-list {
            margin-top: 10px;
        }

        .customer-doc-list-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #6c757d;
            margin-bottom: 8px;
        }

        .customer-doc-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 10px;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            margin-bottom: 5px;
            font-size: 12px;
        }

        .customer-doc-name {
            font-weight: 600;
            color: #212529;
            flex: 1;
        }

        .customer-doc-meta {
            color: #6c757d;
        }

        .customer-doc-status {
            font-size: 10px;
            padding: 2px 8px;
            border-radius: 20px;
            font-weight: 700;
        }

        .customer-doc-status.valid {
            background: rgba(40, 167, 69, 0.1);
            color: #28a745;
            border: 1px solid rgba(40, 167, 69, 0.25);
        }

        .customer-doc-status.expiring {
            background: rgba(255, 193, 7, 0.1);
            color: #856404;
            border: 1px solid rgba(255, 193, 7, 0.25);
        }

        /* ── Unit summary ── */
        .unit-summary-bar {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #f0f8fb;
            border: 1px solid rgba(23, 162, 184, 0.2);
            border-radius: 6px;
            padding: 8px 14px;
            font-size: 13px;
            color: #6c757d;
            margin-top: 12px;
        }

        /* ── Info notice ── */
        .doc-notice {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            background: rgba(23, 162, 184, 0.06);
            border: 1px solid rgba(23, 162, 184, 0.2);
            border-radius: 6px;
            padding: 10px 14px;
            margin-bottom: 14px;
            font-size: 13px;
            color: #6c757d;
        }

        .doc-error {
            display: none;
            align-items: center;
            gap: 10px;
            background: rgba(220, 53, 69, 0.06);
            border: 1px solid rgba(220, 53, 69, 0.25);
            border-radius: 6px;
            padding: 10px 14px;
            margin-bottom: 12px;
            font-size: 13px;
            color: #dc3545;
        }

        .doc-error.visible {
            display: flex;
        }

        .btn-delete-unit {
            background: rgba(220, 53, 69, 0.08);
            border: 1px solid rgba(220, 53, 69, 0.3);
            border-radius: 5px;
            color: #dc3545;
            cursor: pointer;
            font-size: 13px;
            padding: 5px 10px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            transition: all 0.2s;
        }

        .btn-delete-unit:hover {
            background: rgba(220, 53, 69, 0.18);
        }

        .btn-delete-unit:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .btype-readonly {
            pointer-events: none;
            opacity: 0.7;
        }
    </style>
@endsection

@section('content')
    <div class="content-wrapper">

        {{-- Content Header --}}
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>{{ $title }}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('tenant.index') }}">Tenants</a></li>
                            <li class="breadcrumb-item active">{{ $title }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <div class="card card-df card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-file-contract mr-2"></i>New Tenancy Agreement
                        </h3>
                    </div>

                    <div class="card-body">

                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show">
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                                <strong><i class="fas fa-exclamation-circle mr-1"></i> Please fix the following
                                    errors:</strong>
                                <ul class="mb-0 mt-2">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="#" method="POST" enctype="multipart/form-data" id="tenancyForm"
                            data-agreement-id="{{ $agreement->id ?? '' }}">
                            @csrf

                            {{-- ═══════════════════════════════════════════════
                             SECTION 1 — Property Location
                        ═══════════════════════════════════════════════ --}}
                            <div class="card-section shadow-sm p-4">
                                <p class="section-title"><i class="fas fa-map-marker-alt mr-1"></i> Property Location</p>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label> Property <span class="text-danger">*</span></label>
                                            <select name="property_id" id="makaniNumber"
                                                class="form-control select2 @error('property_id') is-invalid @enderror"
                                                onchange="onMakaniChange(this)" required>
                                                <option value="">— Select Building —</option>
                                                @foreach ($formData['properties'] as $property)
                                                    <option value="{{ $property->id }}"
                                                        data-area="{{ $property->area->area_name }}"
                                                        data-area-id="{{ $property->area->id }}"
                                                        data-locality-id="{{ $property->locality->id }}"
                                                        data-locality="{{ $property->locality->locality_name }}"
                                                        {{ (isset($agreement) ? $agreement->property_id : old('property_id')) == $property->id ? 'selected' : '' }}>
                                                        {{ $property->property_name }} - {{ $property->plot_no }} —
                                                        {{ $property->makani_number }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('property_id')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Area</label>
                                            <input type="text" id="areaField" class="form-control" readonly
                                                placeholder="Auto-filled from Makani" value="{{ old('area') }}">
                                            <input type="hidden" name="area_id" id="areaName">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Locality</label>
                                            <input type="text" id="localityField" class="form-control" readonly
                                                placeholder="Auto-filled from Makani" value="{{ old('locality') }}">
                                            <input type="hidden" name="locality_id" id="localityName">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- ═══════════════════════════════════════════════
                             SECTION 2 — Business Type
                        ═══════════════════════════════════════════════ --}}
                            <div class="card-section shadow-sm p-4">
                                <p class="section-title"><i class="fas fa-briefcase mr-1"></i> Business Type</p>

                                <div class="btype-toggle {{ isset($agreement) ? 'btype-readonly' : '' }}">
                                    <div class="btype-option">
                                        <input type="radio" name="business_type" id="typeB2B" value="1"
                                            onchange="onBusinessTypeChange()"
                                            {{ (isset($agreement) ? $agreement->business_type : old('business_type')) == '1' ? 'checked' : '' }}>
                                        <label for="typeB2B">
                                            <span class="btype-icon">🏢</span>
                                            <span>Business to Business</span>
                                            <span class="btype-tag">B2B</span>
                                        </label>
                                    </div>
                                    <div class="btype-option">
                                        <input type="radio" name="business_type" id="typeB2C" value="2"
                                            onchange="onBusinessTypeChange()"
                                            {{ (isset($agreement) ? $agreement->business_type : old('business_type')) == '2' ? 'checked' : '' }}>
                                        <label for="typeB2C">
                                            <span class="btype-icon">🏠</span>
                                            <span>Business to Consumer</span>
                                            <span class="btype-tag">B2C</span>
                                        </label>
                                    </div>
                                </div>
                                @error('business_type')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- ═══════════════════════════════════════════════
                             SECTION 3A — Unit Details (B2C)
                        ═══════════════════════════════════════════════ --}}
                            {{-- @dump($agreement) --}}
                            <div class="card-section shadow-sm p-4 conditional-section" id="unitSection">
                                <p class="section-title"><i class="fas fa-door-open mr-1"></i> Unit Details</p>
                                <input type="hidden" name="agreement_unit_id"
                                    value="{{ isset($agreement) ? $agreement->agreementUnits[0]->id : '' }}">

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Floor <span class="text-danger">*</span></label>
                                            <select id="floorSelect" name="floor_number" class="form-control"
                                                onchange="onFloorChange(this)">
                                                <option value="">— Select Floor —</option>
                                                {{-- @foreach ($formData['floors'] ?? [] as $floor)
                                                    <option value="{{ $floor->id }}"
                                                        {{ old('floor') == $floor->id ? 'selected' : '' }}>
                                                        {{ $floor->floor_name }}
                                                    </option>
                                                @endforeach --}}
                                            </select>
                                            <small class="text-muted" id="floorHelper"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Unit Type <span class="text-danger">*</span></label>
                                            <select id="unitTypeSelect" name="unit_type_id" class="form-control" disabled
                                                onchange="onUnitTypeChange(this)">
                                                <option value="">— Select Floor First —</option>
                                            </select>
                                            <small class="text-muted" id="unitTypeHelper"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Unit Number <span class="text-danger">*</span></label>
                                            <select id="unitNumberSelect" name="contract_unit_details_id"
                                                class="form-control" disabled onchange="onUnitNumberChange(this)">
                                                <option value="">— Select Unit Type First —</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Subunit / Partition</label>
                                            <select id="subunitSelect" name="contract_subunit_details_id"
                                                class="form-control" disabled>
                                                <option value="">— Select Unit First —</option>
                                            </select>
                                            <small class="text-muted">e.g. P1, P2 </small>
                                        </div>
                                    </div>
                                </div>

                                <div id="unitSummary" style="display:none;">
                                    <div class="unit-summary-bar">
                                        <i class="fas fa-home text-info"></i>
                                        Selected: <strong id="unitSummaryText" class="text-dark ml-1"></strong>
                                    </div>
                                </div>
                            </div>

                            {{-- ═══════════════════════════════════════════════
                             SECTION 3B — Building Units & Rent (B2B)
                        ═══════════════════════════════════════════════ --}}
                            <div class="card-section shadow-sm p-4 conditional-section" id="b2bUnitsSection">
                                <p class="section-title">
                                    <i class="fas fa-building mr-1"></i> Building Units &amp; Rent
                                    <small class="text-muted font-weight-normal ml-2">All units in building</small>
                                </p>

                                <div class="table-responsive">
                                    <table class="units-table" id="b2bUnitsTable">
                                        <thead>
                                            <tr>
                                                <th>Select</th>
                                                <th>Floor</th>
                                                <th>Unit Type</th>
                                                <th>Unit No.</th>
                                                {{-- <th>Subunits</th> --}}
                                                <th class="asterisk">Annual Rent (AED)</th>
                                                <th class="asterisk">Monthly Rent (AED)</th>
                                                <th>Subunit Rents</th>
                                                <th id="unit_delete" style="display: none;">Delete</th>
                                            </tr>
                                        </thead>
                                        <tbody id="b2bUnitsBody">
                                            {{-- Populated by JS --}}
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- ═══════════════════════════════════════════════
                             SECTION 4 — Tenant Details
                        ═══════════════════════════════════════════════ --}}
                            <div class="card-section shadow-sm p-4 conditional-section" id="tenantSection">
                                <p class="section-title"><i class="fas fa-user mr-1"></i> Tenant Details</p>
                                <input type="hidden" name="agreement_tenant_id" id="agreement_tenant_id"
                                    value="{{ isset($agreement) ? $agreement->tenant->id : '' }}">

                                {{-- Existing customer toggle (B2B only) --}}
                                {{-- Existing customer toggle (B2B only) --}}
                                <div id="existingCustomerToggleWrap" style="display:none;">
                                    <label class="existing-customer-toggle" for="existingCustomerCheck">
                                        <input type="checkbox" id="existingCustomerCheck"
                                            onchange="onExistingCustomerToggle()">
                                        <span class="toggle-label">
                                            <i class="fas fa-search mr-1"></i>
                                            <strong>Existing Customer</strong> — Select from registered companies
                                        </span>
                                        <span class="toggle-sub">Skip re-entry of known data</span>
                                    </label>

                                    {{-- Dropdown Panel --}}
                                    <div class="existing-customer-panel" id="existingCustomerPanel"
                                        style="display:none;">
                                        <div class="form-group mb-0">
                                            <label style="font-size:13px; font-weight:600; color:#495057;">
                                                <i class="fas fa-building mr-1 text-muted"></i> Select B2B Tenant
                                            </label>
                                            <select id="existingCustomerSelect" class="form-control select2"
                                                onchange="onExistingCustomerSelected(this)" style="width:100%;">
                                                <option value="">— Search or select a tenant —</option>
                                                {{-- Options populated via JS / Ajax --}}
                                            </select>
                                        </div>

                                        {{-- Clear selection --}}
                                        <div id="clearExistingWrap" style="display:none; margin-top:8px;">
                                            <button type="button" class="btn-change-customer"
                                                onclick="clearSelectedCustomer()">
                                                <i class="fas fa-times mr-1"></i> Clear Selection
                                            </button>
                                        </div>
                                    </div>

                                    <div id="existingDivider"
                                        style="display:none; border-top:1px solid #dee2e6; margin:16px 0;"></div>
                                </div>

                                {{-- New customer fields --}}
                                <div id="newCustomerForm" class="">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label id="tenantNameLabel">
                                                    Name as per Emirates ID / Passport
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" name="tenant_name" id="tenantPassportName"
                                                    class="form-control @error('tenant_name') is-invalid @enderror"
                                                    value="{{ isset($tenant) ? $tenant->tenant_name : old('tenant_name') }}"
                                                    placeholder="Exactly as shown on document" required>
                                                @error('tenant_name')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Mobile Number <span class="text-danger">*</span></label>
                                                <input type="number" name="tenant_mobile" id="tenantMobile"
                                                    class="form-control @error('tenant_mobile') is-invalid @enderror"
                                                    value="{{ isset($tenant) ? $tenant->tenant_mobile : old('tenant_mobile') }}"
                                                    placeholder="+971 50 000 0000" required>
                                                @error('tenant_mobile')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Email Address <span class="text-danger">*</span></label>
                                                <input type="email" name="tenant_email"
                                                    class="form-control @error('tenant_email') is-invalid @enderror"
                                                    value="{{ isset($tenant) ? $tenant->tenant_email : old('tenant_email') }}"
                                                    placeholder="tenant@email.com" required>
                                                @error('tenant_email')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Nationality <span class="text-danger">*</span></label>
                                                <select name="nationality_id"
                                                    class="form-control select2 @error('nationality_id') is-invalid @enderror"
                                                    required>
                                                    <option value="">— Select Nationality —</option>
                                                    @foreach ($formData['nationalities'] as $nationality)
                                                        <option value="{{ $nationality->id }}"
                                                            {{ (isset($tenant) ? $tenant->nationality_id : old('nationality_id')) == $nationality->id ? 'selected' : '' }}>
                                                            {{ $nationality->nationality_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('nationality_id')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- B2B Address --}}
                                        <div id="addressFields" style="" class="col-md-12">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="asterisk">Address Line 1</label>
                                                        <input type="text" name="tenant_address" class="form-control"
                                                            value="{{ isset($tenant) ? $tenant->tenant_address : old('tenant_address') }}"
                                                            placeholder="Building / Flat No., Street" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Address Line 2</label>
                                                        <input type="text" name="tenant_street" class="form-control"
                                                            value="{{ isset($tenant) ? $tenant->tenant_street : old('tenant_street') }}"
                                                            placeholder="Area / Community">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>City</label>
                                                        <input type="text" name="tenant_city" class="form-control"
                                                            value="{{ isset($tenant) ? $tenant->tenant_city : old('tenant_city') }}"
                                                            placeholder="City / Emirates">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- B2B Contact --}}
                                        <div id="b2bContactFields" style="display:none;" class="col-md-12">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Contact Person <span class="text-danger">*</span></label>
                                                        <input type="text" name="contact_person" id="contactPerson"
                                                            class="form-control"
                                                            value="{{ isset($tenant) ? $tenant->contact_person : old('contact_person') }}"
                                                            placeholder="Contact person name">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Contact Person Department</label>
                                                        <input type="text" name="contact_person_department"
                                                            id="contactDepartment" class="form-control"
                                                            value="{{ isset($tenant) ? $tenant->contact_person_department : old('contact_person_department') }}"
                                                            placeholder="e.g. Finance, Operations">
                                                    </div>
                                                </div>
                                                {{-- <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Office Landline / Mobile <span
                                                                class="text-danger">*</span></label>
                                                        <input type="text" name="office_landline" id="officeLandline"
                                                            class="form-control"
                                                            value="{{ isset($tenant) ? $tenant->office_landline : old('office_landline') }}"
                                                            placeholder="+971 4 000 0000">
                                                    </div>
                                                </div> --}}
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Contact Number <span class="text-danger">*</span></label>
                                                        <input type="text" name="contact_number" id="contactNumber"
                                                            class="form-control"
                                                            value="{{ isset($tenant) ? $tenant->contact_number : old('contact_number') }}"
                                                            placeholder="+971 50 000 0000">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Contact Email <span class="text-danger">*</span></label>
                                                        <input type="email" name="contact_email" id="contactEmail"
                                                            class="form-control"
                                                            value="{{ isset($tenant) ? $tenant->contact_email : old('contact_email') }}"
                                                            placeholder="contact@email.com">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- B2C Emergency Contact --}}
                                        <div id="b2cContactFields" style="display:none;" class="col-md-12">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Emergency Contact Person <span
                                                                class="text-danger">*</span></label>
                                                        <input type="text" name="contact_person" id="contactPersonB2C"
                                                            class="form-control"
                                                            value="{{ isset($tenant) ? $tenant->contact_person : old('contact_person') }}"
                                                            placeholder="Contact person name">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Emergency Contact Number <span
                                                                class="text-danger">*</span></label>
                                                        <input type="number" name="contact_number" id="contactNumberB2C"
                                                            class="form-control"
                                                            value="{{ isset($tenant) ? $tenant->contact_number : old('contact_number') }}"
                                                            placeholder="+971 50 000 0000">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="asterisk">Emergency Contact Email</label>
                                                        <input type="email" name="contact_email" id="contactEmailB2C"
                                                            class="form-control" required
                                                            value="{{ isset($tenant) ? $tenant->contact_email : old('contact_email') }}"
                                                            placeholder="contact@email.com">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- ─── Documents sub-section ─── --}}
                                <hr class="mt-3 mb-3">
                                <p class="section-title tenant-documents" id="tenant-documents-head"><i
                                        class="fas fa-file-alt mr-1"></i> Tenant
                                    Documents</p>

                                {{-- B2C notice --}}
                                <div id="b2cDocNotice" class="doc-notice" style="display:none;">
                                    <i class="fas fa-info-circle text-info mt-1" style="flex-shrink:0;"></i>
                                    <span>
                                        <strong class="text-dark">B2C Requirement:</strong>
                                        At least one <strong class="text-info">Emirates ID</strong> or
                                        <strong class="text-info">Passport</strong> must be uploaded.
                                    </span>
                                </div>

                                {{-- B2C doc validation error --}}
                                <div id="b2cDocError" class="doc-error">
                                    <i class="fas fa-exclamation-circle"></i>
                                    Please upload at least one Emirates ID or Passport before submitting.
                                </div>

                                {{-- B2B Documents --}}
                                <div id="b2bDocSection" style="display:none;">

                                    <div class="doc-notice">
                                        <i class="fas fa-info-circle text-info mt-1" style="flex-shrink:0;"></i>
                                        <span>
                                            <strong class="text-dark">B2B Requirement:</strong>
                                            Emirates ID and Passport are mandatory for each owner.
                                            One Trade License is required for the company.
                                        </span>
                                    </div>

                                    <div id="b2bDocError" class="doc-error">
                                        <i class="fas fa-exclamation-circle"></i>
                                        Please upload all mandatory documents (Emirates ID, Passport per owner and Trade
                                        License).
                                    </div>

                                    {{-- Owner count selector --}}
                                    <div class="d-flex align-items-center gap-3 mb-3 p-3 bg-light rounded border">
                                        <i class="fas fa-users text-muted"></i>
                                        <label class="mb-0 font-weight-600" style="font-size:14px;font-weight:600;">
                                            Number of Owners
                                        </label>
                                        <select id="ownerCountSelect" name="no_of_owners" class="form-control"
                                            style="width:90px;" onchange="buildOwnerDocBlocks()">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <option value="{{ $i }}"
                                                    {{ isset($tenant) && $tenant->no_of_owners == $i ? 'selected' : '' }}>
                                                    {{ $i }}
                                                </option>
                                                {{ $i }}
                                                </option>
                                            @endfor
                                        </select>
                                    </div>

                                    {{-- Owner blocks rendered by JS --}}
                                    <div id="ownerDocBlocks"></div>

                                    {{-- Trade License --}}
                                    <div class="trade-license-block">
                                        <div class="owner-block-title">
                                            <i class="fas fa-id-card"></i>
                                            Company Trade License
                                            <span class="badge-mandatory">Mandatory</span>
                                        </div>
                                        {{-- @dump($tradeLicense) --}}
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Trade License Number <span class="text-danger">*</span></label>
                                                    <input type="hidden" name="tl_id"
                                                        value="{{ isset($tradeLicense) ? $tradeLicense->id : '' }}">
                                                    <input type="text" name="tl_number" id="tlNumber"
                                                        class="form-control" placeholder="e.g. CN-1234567"
                                                        value="{{ isset($tradeLicense) ? $tradeLicense->document_number : old('tl_number') }}">

                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Upload Trade License <span class="text-danger">*</span></label>
                                                    <div class="file-upload-wrap">
                                                        <input type="file" name="tl_file" id="tlFile"
                                                            accept="image/*,.pdf"
                                                            onchange="onFileChange(this,'tlFileFace','tlFileLabel')">
                                                        <div class="file-upload-face" id="tlFileFace">
                                                            <i class="fas fa-upload"></i>
                                                            <span id="tlFileLabel">Click to upload or drag &amp;
                                                                drop</span>
                                                        </div>
                                                    </div>
                                                    <small class="text-muted">PDF, JPG, PNG accepted</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Issued Date <span class="text-danger">*</span></label>
                                                    {{-- <input type="date" name="tl_issued" id="tlIssued"
                                                        class="form-control"
                                                        onchange="checkExpiry('tlExpiry','tlExpiryWarn')"
                                                        value="{{ old('tl_issued') }}"> --}}

                                                    <div class="input-group date" id="tradeLicenseIssuedDate"
                                                        data-target-input="nearest">
                                                        <input type="text" class="form-control datetimepicker-input"
                                                            name="tl_issued" id="trade_license_issued"
                                                            onchange="checkExpiry('trade_license_issued', 'tlExpiryWarn')"
                                                            data-target="#tradeLicenseIssuedDate" placeholder="dd-mm-YYYY"
                                                            value="{{ isset($tradeLicense) && $tradeLicense->issued_date ? \Carbon\Carbon::parse($tradeLicense->issued_date)->format('d-m-Y') : '' }}">
                                                        <div class="input-group-append"
                                                            data-target="#tradeLicenseIssuedDate"
                                                            data-toggle="datetimepicker">
                                                            <div class="input-group-text">
                                                                <i class="fa fa-calendar"></i>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Expiry Date <span class="text-danger">*</span></label>
                                                    {{-- <input type="date" name="tl_expiry" id="tlExpiry"
                                                        class="form-control"
                                                        onchange="checkExpiry('tlExpiry','tlExpiryWarn')"
                                                        value="{{ old('tl_expiry') }}"> --}}
                                                    <div class="input-group date" id="tradeLicenseExpiryDate"
                                                        data-target-input="nearest">
                                                        <input type="text" class="form-control datetimepicker-input"
                                                            name="tl_expiry" id="trade_license_expiry"
                                                            data-target="#tradeLicenseExpiryDate" placeholder="dd-mm-YYYY"
                                                            onchange="checkExpiry('trade_license_expiry', 'tlExpiryWarn')"
                                                            value="{{ isset($tradeLicense) && $tradeLicense->expiry_date ? \Carbon\Carbon::parse($tradeLicense->expiry_date)->format('d-m-Y') : '' }}">
                                                        <div class="input-group-append"
                                                            data-target="#tradeLicenseExpiryDate"
                                                            data-toggle="datetimepicker">
                                                            <div class="input-group-text">
                                                                <i class="fa fa-calendar"></i>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <span class="expiry-warn" id="tlExpiryWarn"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- B2C Documents --}}
                                {{-- <div id="b2cDocSection" style="display:none;">
                                    <div id="docRowsB2C"></div>
                                    <button type="button" class="btn-add-doc" onclick="addDocRowB2C()">
                                        <i class="fas fa-plus"></i> Add Another Document
                                    </button>
                                </div> --}}
                                {{-- B2C Documents --}}
                                <div id="b2cDocSection" style="display:none;">

                                    {{-- ── Emirates ID Row ── --}}
                                    <div class="doc-row" id="docRowEID">
                                        <div class="doc-row-header">
                                            <span class="doc-row-title">Emirates ID</span>
                                        </div>
                                        @php
                                            $eidDoc = isset($existingB2CDocs)
                                                ? collect($existingB2CDocs)->firstWhere('type', 2)
                                                : null;
                                        @endphp
                                        <input type="hidden" name="docsB2C[1][id]" id="b2cDocId_1"
                                            value="{{ $eidDoc['id'] ?? '' }}">
                                        <input type="hidden" name="docsB2C[1][type]" value="2">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Document Number</label>
                                                    <input type="text" name="docsB2C[1][number]" id="b2cDocNumber_1"
                                                        class="form-control" placeholder="784-XXXX-XXXXXXX-X"
                                                        value="{{ $eidDoc['number'] ?? '' }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Upload Emirates ID <small class="text-muted">(JPG, PNG,
                                                            PDF)</small></label>
                                                    <div class="file-upload-wrap">
                                                        <input type="file" name="docsB2C[1][file]"
                                                            accept="image/*,.pdf"
                                                            onchange="onFileChange(this,'b2cDocFace_1','b2cDocLabel_1')">
                                                        <div class="file-upload-face" id="b2cDocFace_1">
                                                            <i class="fas fa-upload"></i>
                                                            <span id="b2cDocLabel_1">Click to upload or drag &amp;
                                                                drop</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Issued Date</label>
                                                    <div class="input-group date" id="b2cIssued_1"
                                                        data-target-input="nearest">
                                                        <input type="text" class="form-control datetimepicker-input"
                                                            name="docsB2C[1][issued_date]" data-target="#b2cIssued_1"
                                                            placeholder="DD-MM-YYYY"
                                                            value="{{ $eidDoc['issued_date'] ?? '' }}">
                                                        <div class="input-group-append" data-target="#b2cIssued_1"
                                                            data-toggle="datetimepicker">
                                                            <div class="input-group-text"><i class="fa fa-calendar"></i>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Expiry Date</label>
                                                    <div class="input-group date" id="b2cExpiry_1"
                                                        data-target-input="nearest">
                                                        <input type="text" class="form-control datetimepicker-input"
                                                            name="docsB2C[1][expiry_date]" id="b2cDocExpiry_1"
                                                            data-target="#b2cExpiry_1" placeholder="DD-MM-YYYY"
                                                            onchange="checkExpiry('b2cDocExpiry_1','b2cDocWarn_1')"
                                                            value="{{ $eidDoc['expiry_date'] ?? '' }}">
                                                        <div class="input-group-append" data-target="#b2cExpiry_1"
                                                            data-toggle="datetimepicker">
                                                            <div class="input-group-text"><i class="fa fa-calendar"></i>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <span class="expiry-warn" id="b2cDocWarn_1"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- ── Passport Row (Required) ── --}}
                                    <div class="doc-row" id="docRowPassport">
                                        <div class="doc-row-header">
                                            <span class="doc-row-title">Passport <span
                                                    class="badge-mandatory">Required</span></span>
                                        </div>
                                        @php
                                            $passportDoc = isset($existingB2CDocs)
                                                ? collect($existingB2CDocs)->firstWhere('type', 1)
                                                : null;
                                        @endphp
                                        {{-- @dump($existingB2CDocs); --}}
                                        <input type="hidden" name="docsB2C[2][id]" id="b2cDocId_2"
                                            value="{{ $passportDoc['id'] ?? '' }}">
                                        <input type="hidden" name="docsB2C[2][type]" value="1">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Passport Number <span class="text-danger">*</span></label>
                                                    <input type="text" name="docsB2C[2][number]" id="b2cDocNumber_2"
                                                        class="form-control" placeholder="e.g. A12345678"
                                                        value="{{ $passportDoc['number'] ?? '' }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Upload Passport <span class="text-danger">*</span>
                                                        <small class="text-muted">(JPG, PNG, PDF)</small></label>
                                                    <div class="file-upload-wrap">
                                                        <input type="file" name="docsB2C[2][file]" id="docFile_2"
                                                            accept="image/*,.pdf"
                                                            onchange="onFileChange(this,'b2cDocFace_2','b2cDocLabel_2')">
                                                        <div class="file-upload-face" id="b2cDocFace_2">
                                                            <i class="fas fa-upload"></i>
                                                            <span id="b2cDocLabel_2">Click to upload or drag &amp;
                                                                drop</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Issued Date</label>
                                                    <div class="input-group date" id="b2cIssued_2"
                                                        data-target-input="nearest">
                                                        <input type="text" class="form-control datetimepicker-input"
                                                            name="docsB2C[2][issued_date]" data-target="#b2cIssued_2"
                                                            placeholder="DD-MM-YYYY" id="b2cDocissued_2"
                                                            value="{{ $passportDoc['issued_date'] ?? '' }}">
                                                        <div class="input-group-append" data-target="#b2cIssued_2"
                                                            data-toggle="datetimepicker">
                                                            <div class="input-group-text"><i class="fa fa-calendar"></i>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Expiry Date <span class="text-danger">*</span></label>
                                                    <div class="input-group date" id="b2cExpiry_2"
                                                        data-target-input="nearest">
                                                        <input type="text" class="form-control datetimepicker-input"
                                                            name="docsB2C[2][expiry_date]" id="b2cDocExpiry_2"
                                                            data-target="#b2cExpiry_2" placeholder="DD-MM-YYYY"
                                                            onchange="checkExpiry('b2cDocExpiry_2','b2cDocWarn_2')"
                                                            value="{{ $passportDoc['expiry_date'] ?? '' }}">
                                                        <div class="input-group-append" data-target="#b2cExpiry_2"
                                                            data-toggle="datetimepicker">
                                                            <div class="input-group-text"><i class="fa fa-calendar"></i>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <span class="expiry-warn" id="b2cDocWarn_2"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            {{-- ═══════════════════════════════════════════════
                             SECTION 5 — Agreement Duration
                        ═══════════════════════════════════════════════ --}}
                            <div class="card-section shadow-sm p-4 conditional-section" id="datesSection">
                                <p class="section-title"><i class="fas fa-calendar-alt mr-1"></i> Agreement Duration</p>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Start Date <span class="text-danger">*</span></label>
                                            <div class="input-group date" id="start_date" data-target-input="nearest">
                                                <input type="text" class="form-control datetimepicker-input"
                                                    name="start_date" id="startDate" data-target="#start_date"
                                                    placeholder="dd-mm-YYYY"
                                                    value="{{ isset($agreement) && $agreement->start_date ? \Carbon\Carbon::parse($agreement->start_date)->format('d-m-Y') : old('start_date') }}"
                                                    required>
                                                <div class="input-group-append" data-target="#start_date"
                                                    data-toggle="datetimepicker">
                                                    <div class="input-group-text">
                                                        <i class="fa fa-calendar"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- <input type="date" name="start_date" id="startDate"
                                                class="form-control @error('start_date') is-invalid @enderror"
                                                value="{{ old('start_date') }}" onchange="onDateChange()" required> --}}
                                            @error('start_date')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6" id="endDateField">
                                        <div class="form-group">
                                            <label>End Date <span class="text-danger">*</span></label>
                                            <div class="input-group date" id="end_date" data-target-input="nearest">
                                                <input type="text" class="form-control datetimepicker-input"
                                                    name="end_date" id="endDate" data-target="#end_date"
                                                    placeholder="dd-mm-YYYY"
                                                    value="{{ isset($agreement) && $agreement->end_date ? \Carbon\Carbon::parse($agreement->end_date)->format('d-m-Y') : old('end_date') }}">
                                                <div class="input-group-append" data-target="#end_date"
                                                    data-toggle="datetimepicker">
                                                    <div class="input-group-text">
                                                        <i class="fa fa-calendar"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- <input type="date" name="end_date" id="endDate"
                                                class="form-control @error('end_date') is-invalid @enderror"
                                                value="{{ old('end_date') }}" onchange="onDateChange()"> --}}
                                            @error('end_date')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div id="durationPill" style="display:none;">
                                    <div class="duration-pill">
                                        <i class="fas fa-calendar-check text-info"></i>
                                        <span id="durationText"></span>
                                    </div>
                                </div>
                            </div>

                            {{-- ═══════════════════════════════════════════════
                             SECTION 6 — Rent Details (B2C)
                        ═══════════════════════════════════════════════ --}}
                            <div class="card-section shadow-sm p-4 conditional-section" id="rentSection">
                                <p class="section-title"><i class="fas fa-money-bill-wave mr-1"></i> Rent Details</p>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>
                                                Rent Per Month <span class="text-danger">*</span>
                                                <small class="text-muted font-weight-normal">(AED)</small>
                                            </label>
                                            <input type="number" name="rent_per_month" id="rentMonth"
                                                class="form-control @error('rent_per_month') is-invalid @enderror"
                                                value="{{ old('rent_per_month', isset($agreement) ? $agreement->agreementUnits->first()->monthly_rent ?? '' : '') }}"
                                                placeholder="0.00" step="0.01" oninput="calculateAnnual()">
                                            @error('rent_per_month')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>
                                                Rent Per Annum <span class="text-danger">*</span>
                                                <small class="text-muted font-weight-normal">(AED)</small>
                                            </label>
                                            <input type="number" name="rent_per_annum" id="rentAnnum"
                                                class="form-control @error('rent_per_annum') is-invalid @enderror"
                                                value="{{ old('rent_per_annum', isset($agreement) ? $agreement->agreementUnits->first()->annual_rent ?? '' : '') }}"
                                                placeholder="0.00" step="0.01" oninput="calculateMonthly()">
                                            @error('rent_per_annum')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <div class="rent-summary-box" id="rentDisplay">
                                    <div class="rent-box">
                                        <div class="rent-box-icon">📅</div>
                                        <div>
                                            <div class="rent-box-label">Monthly</div>
                                            <div class="rent-box-value" id="displayMonth">—<span>AED</span></div>
                                        </div>
                                    </div>
                                    <div class="rent-box">
                                        <div class="rent-box-icon">📊</div>
                                        <div>
                                            <div class="rent-box-label">Annual</div>
                                            <div class="rent-box-value" id="displayAnnum">—<span>AED</span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- ═══════════════════════════════════════════════
                             ACTION BUTTONS
                        ═══════════════════════════════════════════════ --}}
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <a href="{{ route('tenant-registration.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left mr-1"></i> Back
                                </a>
                                <button type="submit" class="btn signinbtn px-4" id="submitBtn">
                                    <i class="fas fa-save mr-1"></i> Save Agreement
                                </button>
                            </div>

                        </form>
                    </div>{{-- /.card-body --}}
                </div>{{-- /.card --}}
            </div>
        </section>
    </div>
@endsection


@section('custom_js')
    <script src="{{ asset('assets/moment/moment.min.js') }}"></script>
    <!-- Tempusdominus Bootstrap 4 -->

    <script src="{{ asset('assets/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>
    <!-- date-range-picker -->

    <script src="{{ asset('assets/daterangepicker/daterangepicker.js') }}"></script>
    {{-- <script src="{{ asset('assets/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script> --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script>
        $('#start_date').datetimepicker({
            format: 'DD-MM-YYYY'
        });

        $('#end_date').datetimepicker({
            format: 'DD-MM-YYYY'
        });
        $('#tradeLicenseIssuedDate').datetimepicker({
            format: 'DD-MM-YYYY'
        });

        $('#tradeLicenseExpiryDate').datetimepicker({
            format: 'DD-MM-YYYY'
        });
        $('#b2cIssued_1').datetimepicker({
            format: 'DD-MM-YYYY'
        });
        $('#b2cExpiry_1').datetimepicker({
            format: 'DD-MM-YYYY'
        });
        $('#b2cIssued_2').datetimepicker({
            format: 'DD-MM-YYYY'
        });
        $('#b2cExpiry_2').datetimepicker({
            format: 'DD-MM-YYYY'
        });
        // Pre-fill uploaded file indicators for existing B2C docs
        @if (isset($existingB2CDocs) && count($existingB2CDocs))
            @foreach ($existingB2CDocs as $doc)
                @php $idx = $doc['type'] == 2 ? 1 : 2; @endphp
                @if (!empty($doc['view_url']))
                    (function() {
                        const face = document.getElementById('b2cDocFace_{{ $idx }}');
                        const label = document.getElementById('b2cDocLabel_{{ $idx }}');
                        if (face) {
                            face.classList.add('has-file');
                            face.style.backgroundImage = "url('{{ $doc['view_url'] }}')";
                            face.style.backgroundSize = 'contain';
                            face.style.backgroundRepeat = 'no-repeat';
                            face.style.backgroundPosition = 'center';
                        }
                        if (label) label.textContent = '✓ Previously uploaded';
                    })();
                @endif
            @endforeach
            checkExpiry('b2cDocExpiry_1', 'b2cDocWarn_1');
            checkExpiry('b2cDocExpiry_2', 'b2cDocWarn_2');
        @endif
        // // ── Listen to datetimepicker change events ──
        // $('#tradeLicenseIssuedDate').on('change.datetimepicker', function() {
        //     checkExpiry('trade_license_issued', 'tlExpiryWarn');
        // });
        // $('#tradeLicenseExpiryDate').on('change.datetimepicker', function() {
        //     checkExpiry('trade_license_expiry', 'tlExpiryWarn');
        // });
    </script>
    <script>
        const ownerDocs = @json($ownerDocs ?? []);
        const existingOwnerDocsJson = @json($existingOwnerDocsJson ?? []);
        console.log('owner', ownerDocs);
        const b2cDocs = @json($existingB2CDocs ?? []);
        const agreement = @json($agreement ?? null);
        console.log('agree', agreement);
        if (agreement) {
            $('#makaniNumber').on('select2:opening select2:selecting', function(e) {
                // alert("test");
                e.preventDefault();
            });
        }
        @if (isset($agreement) && $agreement->business_type == 2)
            @php
                $firstUnit = $agreement->agreementUnits->first();

            @endphp
            let editData = {
                floor: "{{ $firstUnit->floor_number ?? '' }}",
                unit_type_id: {{ $firstUnit->unit_type_id ?? 'null' }},
                unit_id: {{ $firstUnit->contract_unit_details_id ?? 'null' }},
                subunit_id: {{ $firstUnit->contract_subunit_details_id ?? 'null' }},
                unit_number: "{{ $firstUnit->contractUnitDetail->unit_number ?? '' }}",
                subunit_number: "{{ $firstUnit->contractSubunitDetail->subunit_no ?? '' }}",
            };
            // console.log($firstUnit);
            // $(document).ready(function() {
            // $('#makaniNumber').on('select2:opening select2:selecting', function(e) {
            //     alert("test");
            //     e.preventDefault();
            // });
            // });
        @else
            let editData = null;
        @endif
    </script>

    <script>
        const existingCustomers = @json($formData['existingCustomers'] ?? []);
        console.log('Existing Customers:', existingCustomers);
        let selectedCustomerId = null;
        // Injected from PHP — keyed by property_id → floor → unitType → [ {id, unit_number, subunits} ]
        const propertyUnitMap = @json($formData['propertyUnitMap'] ?? []);
        console.log('Property Unit Map:', propertyUnitMap);

        let activeUnitData = {}; // set when a property is selected

        // ─── Makani / Property autofill ───────────────────────────────────────────
        // ─── Property / Makani change ─────────────────────────────────────────────────
        // function onMakaniChange(select) {
        //     const opt = select.options[select.selectedIndex];
        //     const propertyId = select.value;

        //     // Auto-fill read-only fields
        //     document.getElementById('areaField').value = opt.dataset.area || '';
        //     document.getElementById('localityField').value = opt.dataset.locality || '';

        //     // Reset cascade
        //     resetUnitCascade();

        //     if (!propertyId) {
        //         activeUnitData = {};
        //         return;
        //     }
        //     // alert('Property selected: ' + propertyId);

        //     activeUnitData = propertyUnitMap[propertyId] || {};

        //     // Only populate floors if B2C is already selected
        //     if (document.getElementById('typeB2C').checked) {
        //         populateFloors();
        //     }
        // }
        function onMakaniChange(select) {
            // alert("test");
            const opt = select.options[select.selectedIndex];
            const propertyId = select.value;

            document.getElementById('areaField').value = opt.dataset.area || '';
            document.getElementById('localityField').value = opt.dataset.locality || '';
            document.getElementById('areaName').value = opt.dataset.areaId || '';
            document.getElementById('localityName').value = opt.dataset.localityId || '';


            resetUnitCascade();
            activeUnitData = {};
            window.activePropertyData = null;

            if (!propertyId) return;

            const rawData = propertyUnitMap[propertyId];
            console.log('rowData', rawData);
            if (!rawData || Array.isArray(rawData)) return;

            window.activePropertyData = rawData; // store both b2c and b2b

            const isB2C = document.getElementById('typeB2C').checked;
            const isB2B = document.getElementById('typeB2B').checked;

            if (isB2C) {
                activeUnitData = rawData.b2c || {};
                populateFloors();
            } else if (isB2B) {
                activeUnitData = rawData.b2b || {};
                if (typeof existingUnits !== 'undefined') {
                    mergeExistingUnitsIntoActiveData(existingUnits);
                }
                buildB2BTable();
            }
        }

        // ─── Populate floor dropdown ───────────────────────────────────────────────────
        function populateFloors() {
            // alert('populateFloors called');
            const floorSelect = document.getElementById('floorSelect');
            const helper = document.getElementById('floorHelper');

            resetSelect(floorSelect, '— Select Floor —');
            floorSelect.disabled = true;
            // const helper = document.getElementById('floorHelper');
            helper.textContent = '';
            console.log('Active unit data for floors:', activeUnitData);

            const floors = Object.keys(activeUnitData);
            console.log('Floors extracted:', floors);

            if (floors.length === 0) {
                floorSelect.disabled = true;
                helper.textContent = 'No vacant units available for this property.';
                return;
            }
            // alert('Floors available: ' + floors.join(', '));

            // Sort: G first, then numerically
            floors.sort((a, b) => {
                if (a === 'G') return -1;
                if (b === 'G') return 1;
                return parseInt(a) - parseInt(b);
            });
            // alert('Sorted floors: ' + floors.join(', '));

            // const floorLabels = {
            //     G: 'Ground Floor'
            // };
            floors.forEach(f => {
                const label = `Floor ${f}`;
                floorSelect.add(new Option(label, f));
            });

            floorSelect.disabled = false;
            helper.textContent = `${floors.length} floor${floors.length > 1 ? 's' : ''} available`;
        }

        // ─── Floor → Unit Type ─────────────────────────────────────────────────────────
        // function onFloorChange(select) {
        //     const floor = select.value;
        //     const typeSelect = document.getElementById('unitTypeSelect');
        //     const unitSelect = document.getElementById('unitNumberSelect');
        //     const subSelect = document.getElementById('subunitSelect');

        //     resetSelect(typeSelect, '— Select Unit Type —');
        //     resetSelect(unitSelect, '— Select Unit Type First —');
        //     resetSelect(subSelect, '— Select Unit First —');
        //     typeSelect.disabled = true;
        //     unitSelect.disabled = true;
        //     subSelect.disabled = true;
        //     hideSummary();

        //     if (!floor || !activeUnitData[floor]) return;

        //     const types = Object.keys(activeUnitData[floor]);
        //     types.forEach(t => typeSelect.add(new Option(t, t)));
        //     typeSelect.disabled = false;

        //     document.getElementById('unitTypeHelper').textContent =
        //         `${types.length} unit type${types.length > 1 ? 's' : ''} on this floor`;
        // }

        function onFloorChange(select) {
            const floor = select.value;
            const typeSelect = document.getElementById('unitTypeSelect');
            const unitSelect = document.getElementById('unitNumberSelect');
            const subSelect = document.getElementById('subunitSelect');

            resetSelect(typeSelect, '— Select Unit Type —');
            resetSelect(unitSelect, '— Select Unit Type First —');
            resetSelect(subSelect, '— Select Unit First —');
            typeSelect.disabled = true;
            unitSelect.disabled = true;
            subSelect.disabled = true;
            hideSummary();

            if (!floor || !activeUnitData[floor]) return;

            const types = activeUnitData[floor];
            console.log('Unit types for selected floor:', types);
            Object.entries(types).forEach(([typeId, typeData]) => {
                typeSelect.add(new Option(typeData.label, typeId)); // text="2BHK", value=3
            });
            typeSelect.disabled = false;

            const count = Object.keys(types).length;
            document.getElementById('unitTypeHelper').textContent =
                `${count} unit type${count > 1 ? 's' : ''} on this floor`;
        }


        function onUnitTypeChange(select) {
            // alert("test1");
            const floor = document.getElementById('floorSelect').value;
            const typeId = select.value;
            const unitSelect = document.getElementById('unitNumberSelect');
            const subSelect = document.getElementById('subunitSelect');

            resetSelect(unitSelect, '— Select Unit Number —');
            resetSelect(subSelect, '— Select Unit First —');
            unitSelect.disabled = true;
            subSelect.disabled = true;
            hideSummary();

            if (!typeId || !activeUnitData[floor]?.[typeId]) return;

            // Populate unit options
            activeUnitData[floor][typeId].units.forEach(unit => {
                unitSelect.add(new Option(unit.unit_number, unit.id));
            });
            console.log("unitSelect", unitSelect);
            console.log("editdata", editData);
            console.log("typeId", typeId);
            unitSelect.disabled = false;

            // Prefill if editData is provided
            if (editData?.unit_id && editData?.unit_type_id == typeId) {
                // Find the option and select it
                const optionToSelect = Array.from(unitSelect.options).find(
                    opt => opt.value == editData.unit_id
                );
                if (!optionToSelect) {
                    const label = ed.unit_number || 'Unknown Unit';
                    unitSelect.add(new Option(label, ed.unit_id));
                    optionToSelect = Array.from(unitSelect.options).find(
                        opt => opt.value == ed.unit_id
                    );
                }

                if (optionToSelect) {
                    optionToSelect.selected = true;
                    onUnitNumberChange(unitSelect);

                    // Prefill subunit if exists
                    if (editData.subunit_id) {
                        const subOption = Array.from(subSelect.options).find(
                            opt => opt.value == editData.subunit_id
                        );
                        if (subOption) subOption.selected = true;
                    }

                    updateUnitSummary();
                }
            }
        }

        // ─── Unit Number → Subunit ─────────────────────────────────────────────────────
        function onUnitNumberChange(select) {
            console.log("select", select);
            // alert("test");
            const floor = document.getElementById('floorSelect').value;
            const typeId = document.getElementById('unitTypeSelect').value;
            const unitId = parseInt(select.value);
            const subSelect = document.getElementById('subunitSelect');

            resetSelect(subSelect, '— Select Subunit —');
            subSelect.disabled = true;
            hideSummary();

            if (!unitId || !activeUnitData[floor]?.[typeId]?.units) return;

            // Find the selected unit object by its ID
            const unitObj = activeUnitData[floor][typeId].units.find(u => u.id === unitId);
            console.log('Selected unit object:', unitObj);
            if (!unitObj) return;

            if (unitObj.subunits.length === 0) {
                subSelect.add(new Option('Full Unit', 'full'));
                subSelect.value = 'full';
                subSelect.disabled = false;
            } else {
                unitObj.subunits.forEach(sub => {
                    subSelect.add(new Option(sub.label, sub.id));
                });
                subSelect.disabled = false;

                // Determine which subunit to select
                let selectedSubId = null;

                if (editData?.subunit_id && editData?.unit_id === unitId) {
                    selectedSubId = editData.subunit_id;

                    // Check if option already exists
                    const exists = Array.from(subSelect.options).some(
                        opt => opt.value == selectedSubId
                    );

                    // If it doesn't exist, add it dynamically
                    if (!exists) {
                        // You can use editData.subunit_label or fallback to "Unknown Subunit"
                        const label = editData.subunit_number || 'Unknown Subunit';
                        subSelect.add(new Option(label, selectedSubId));
                    }

                    // Now set the value
                    subSelect.value = selectedSubId;
                } else if (unitObj.subunits.length === 1) {
                    subSelect.value = unitObj.subunits[0].id;
                }
            }

            updateUnitSummary();
        }

        // ─── Business Type ────────────────────────────────────────────────────────
        function onBusinessTypeChange() {
            const isB2B = document.getElementById('typeB2B').checked;
            const isB2C = document.getElementById('typeB2C').checked;

            // Load correct unit data slice based on business type
            if (window.activePropertyData) {
                activeUnitData = isB2C ?
                    (window.activePropertyData.b2c || {}) :
                    (window.activePropertyData.b2b || {});
            } else {
                activeUnitData = {};
            }

            // Sections
            toggleVisible('unitSection', isB2C);
            toggleVisible('b2bUnitsSection', isB2B);
            toggleVisible('tenantSection', true);
            toggleVisible('datesSection', true);
            toggleVisible('rentSection', isB2C);

            // Existing customer toggle
            document.getElementById('existingCustomerToggleWrap').style.display = isB2B ? '' : 'none';
            document.getElementById('existingCustomerCheck').checked = false;
            document.getElementById('existingCustomerPanel').style.display = 'none';
            document.getElementById('existingDivider').style.display = 'none';
            clearSelectedCustomer();
            document.getElementById('newCustomerForm').style.display = '';

            // Doc sections
            document.getElementById('b2cDocNotice').style.display = isB2C ? 'flex' : 'none';
            document.getElementById('b2cDocError').classList.remove('visible');
            document.getElementById('b2bDocSection').style.display = isB2B ? '' : 'none';
            document.getElementById('b2cDocSection').style.display = isB2C ? '' : 'none';

            // Contact fields
            // document.getElementById('addressFields').style.display = isB2B ? '' : 'none';
            document.getElementById('b2bContactFields').style.display = isB2B ? '' : 'none';
            document.getElementById('b2cContactFields').style.display = isB2C ? '' : 'none';
            // ── Toggle required on B2C contact fields ──
            const b2cContactInputs = document.querySelectorAll('#b2cContactFields input');
            console.log(b2cContactInputs);
            b2cContactInputs.forEach(input => {
                input.required = isB2C;
                input.disabled = !isB2C;
            });

            // ── Toggle required on B2B contact fields ──
            const b2bContactInputs = document.querySelectorAll('#b2bContactFields input');
            b2bContactInputs.forEach(input => {
                input.required = isB2B;
                input.disabled = !isB2B;
            });

            // Name label
            const lbl = document.getElementById('tenantNameLabel');
            if (isB2B) {
                lbl.innerHTML = 'Name as per Trade License <span class="text-danger">*</span>';
                document.getElementById('tenantPassportName').placeholder = 'Exactly as shown on trade license';
            } else {
                lbl.innerHTML = 'Name as per Emirates ID / Passport <span class="text-danger">*</span>';
                document.getElementById('tenantPassportName').placeholder = 'Exactly as shown on document';
            }

            // End date (hidden for B2C)
            document.getElementById('endDateField').style.display = isB2C ? 'none' : '';
            if (isB2C) {
                document.getElementById('endDate').value = '';
                document.getElementById('durationPill').style.display = 'none';

                // ── B2C doc fields: add required ──
                document.getElementById('b2cDocNumber_2').required = true;
                document.getElementById('b2cDocExpiry_2').required = true;
                document.getElementById('b2cDocissued_2').required = true;

                // document.getElementById('docFile_2').required = true;

            } else {
                // ── B2C doc fields: remove required when switching away ──
                document.getElementById('b2cDocNumber_2').required = false;
                document.getElementById('b2cDocExpiry_2').required = false;
                document.getElementById('b2cDocissued_2').required = false;
            }

            // Init docs
            if (isB2B) {
                document.getElementById('ownerDocBlocks').innerHTML = '';
                buildOwnerDocBlocks();
            }
            // if (isB2C) {
            //     document.getElementById('docRowsB2C').innerHTML = '';
            //     docCountB2C = 0;

            //     const docs = (typeof b2cDocs !== 'undefined' && b2cDocs && b2cDocs.length > 0) ?
            //         b2cDocs : [null];

            //     docs.forEach(doc => addDocRowB2C(doc));

            // Always render Emirates ID and Passport rows
            // const emiratesDoc = b2cDocs?.find(d => d.type == 2) ?? null;
            // const passportDoc = b2cDocs?.find(d => d.type == 1) ?? null;

            // addDocRowB2C(emiratesDoc, 'Emirates ID', 2);
            // addDocRowB2C(passportDoc, 'Passport', 1);
            // }
            if (isB2B) {
                document.getElementById('endDate').required = true;
            } else {
                document.getElementById('endDate').required = false;
            }

            // Units
            if (isB2B) buildB2BTable();
            if (isB2C) {
                resetUnitCascade();
                populateFloors();
            }

            // ── Toggle required on B2C unit fields ──
            const b2cUnitSelects = ['floorSelect', 'unitTypeSelect', 'unitNumberSelect'];
            b2cUnitSelects.forEach(id => {
                const el = document.getElementById(id);
                if (el) el.required = isB2C;
            });
            document.getElementById('subunitSelect').required = false;
            // ── Toggle required on B2C rent fields ──
            const rentMonth = document.getElementById('rentMonth');
            const rentAnnum = document.getElementById('rentAnnum');
            if (rentMonth) rentMonth.required = isB2C;
            if (rentAnnum) rentAnnum.required = isB2C;
        }

        function toggleVisible(id, show) {
            const el = document.getElementById(id);
            if (show) el.classList.add('visible');
            else el.classList.remove('visible');
        }



        function updateUnitSummary() {
            const floor = document.getElementById('floorSelect');
            const type = document.getElementById('unitTypeSelect').text;
            const unit = document.getElementById('unitNumberSelect').text;
            if (!unit) return;
            document.getElementById('unitSummaryText').textContent =
                `${floor.options[floor.selectedIndex].text}  ·  ${type}  ·  ${unit}`;
            document.getElementById('unitSummary').style.display = 'block';
        }

        function resetUnitCascade() {
            [
                ['floorSelect', '— Select Property First —'],
                ['unitTypeSelect', '— Select Floor First —'],
                ['unitNumberSelect', '— Select Unit Type First —'],
                ['subunitSelect', '— Select Unit First —'],
            ].forEach(([id, placeholder]) => {
                const el = document.getElementById(id);
                if (!el) return;
                resetSelect(el, placeholder);
                el.disabled = true;
            });

            ['floorHelper', 'unitTypeHelper'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.textContent = '';
            });

            hideSummary();
        }

        // ─── Helper ───────────────────────────────────────────────────────────────────
        function resetSelect(el, placeholder) {
            el.innerHTML = '';
            el.add(new Option(placeholder, ''));
        }

        function hideSummary() {
            document.getElementById('unitSummary').style.display = 'none';
        }

        function resetUnitSection() {
            // alert('resetUnitSection called');
            ['floorSelect', 'unitTypeSelect', 'unitNumberSelect', 'subunitSelect'].forEach(id => {
                const el = document.getElementById(id);
                if (el) {
                    el.innerHTML = '';
                    el.add(new Option('—', ''));
                    el.disabled = (id !== 'floorSelect');
                }
            });
            hideSummary();
        }

        function resetSelect(el, placeholder) {
            el.innerHTML = '';
            el.add(new Option(placeholder, ''));
        }

        // ─── B2B Units Table ──────────────────────────────────────────────────────
        function buildB2BTable() {
            // alert("test");
            const tbody = document.getElementById('b2bUnitsBody');
            tbody.innerHTML = '';
            let rowIdx = 0;

            if (!activeUnitData || Object.keys(activeUnitData).length === 0) {
                tbody.innerHTML =
                    '<tr><td colspan="8" class="text-center text-muted p-3">No units available for this property.</td></tr>';
                return;
            }

            Object.entries(activeUnitData).forEach(([floor, typeMap]) => {
                const floorLabel = floor === 'G' ? 'Ground Floor' : `Floor ${floor}`;

                Object.entries(typeMap).forEach(([typeId, typeData]) => {
                    const typeName = typeData.label; // "2BHK"

                    typeData.units.forEach(unit => {
                        rowIdx++;
                        const key = `${floor}_${typeId}_${unit.id}`;
                        const subs = unit.subunits;
                        const hasMulti = subs.length > 0;
                        const agreementId = document.getElementById('tenancyForm').dataset
                            .agreementId;
                        const isEdit = !!agreementId;
                        // alert(isEdit);
                        const b2bRowCount = () => document.querySelectorAll(
                            '#b2bUnitsBody .unit-data-row').length;

                        const tr = document.createElement('tr');
                        tr.className = 'unit-data-row';
                        tr.id = `b2bRow_${rowIdx}`;
                        tr.innerHTML = `
                    <td><input type="checkbox" class="row-checkbox" id="selRow_${rowIdx}" value="${rowIdx}" onchange="onRowCheckboxChange(this, ${rowIdx})"></td>
                    <td><span class="floor-label">${floorLabel}</span></td>
                    <td><span class="type-badge">${typeName}</span></td>
                    <td style="font-weight:600;">${unit.unit_number}</td>

                    <input type="hidden" id="unitDbId_${rowIdx}" name="unit_rent[${key}][id]" value="">

                    <td><input type="number" placeholder="0.00"
                        id="annual_${rowIdx}" name="unit_rent[${key}][annual]"
                     ></td>
                    <td><input type="number" placeholder="0.00"
                        id="monthly_${rowIdx}" name="unit_rent[${key}][monthly]"
                        ></td>
                    <td>${hasMulti
                        ? `<button type="button" class="btn-subunit" id="subBtn_${rowIdx}" onclick="toggleExpandRow(${rowIdx})">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <i class="fas fa-chevron-down" id="subBtnIcon_${rowIdx}"></i> Subunit Rents
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </button>`
                        : '<span class="text-muted small">—</span>'}
                    </td>
                     <td>
                        ${isEdit
                            ? `<button type="button"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            class="btn-delete-unit"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            id="delBtn_${rowIdx}"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            data-row="${rowIdx}"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            data-unit-db-id=""
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            data-agreement-id="${agreementId}"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            onclick="deleteAgreementUnit(this)"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            style="display:none;">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <i class="fas fa-trash-alt"></i>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        </button>`
                            : '—'}
                    </td>`;
                        tbody.appendChild(tr);

                        if (hasMulti) {
                            const expandTr = document.createElement('tr');
                            expandTr.className = 'expand-row';
                            expandTr.id = `expandRow_${rowIdx}`;
                            const cards = subs.map(sub => {
                                const nk = `${key}_${sub.id}`;
                                return `<div class="subunit-rent-card" id="subCard_${rowIdx}_${sub.id}">
                            <div class="subunit-rent-card-label">${sub.label}</div>

                                <input type="hidden" id="subRentId_${rowIdx}_${sub.id}"
                                    name="subunit_rent[${nk}][id]" value="">

                            <div class="form-group mb-0">
                                <label style="font-size:11px;color:#6c757d;font-weight:600;" class="asterisk">Monthly Rent (AED)</label>
                                <input type="number" placeholder="0.00"
                                    id="smonthly_${rowIdx}_${sub.id}"
                                    name="subunit_rent[${nk}][monthly]"
                                    class="form-control form-control-sm"
                                    oninput="onSubInput(${rowIdx}, ${sub.id})">
                            </div>
                        </div>`;
                            }).join('');

                            expandTr.innerHTML = `<td colspan="8">
                        <div class="expand-inner" id="expandInner_${rowIdx}">
                            <div class="expand-inner-title">
                                <i class="fas fa-home"></i> ${unit.unit_number} — Subunit Rents
                            </div>
                            <div class="subunit-grid">${cards}</div>
                        </div></td>`;
                            tbody.appendChild(expandTr);
                        }
                    });
                });
            });
        }

        function toggleExpandRow(rowIdx) {
            const inner = document.getElementById(`expandInner_${rowIdx}`);
            const icon = document.getElementById(`subBtnIcon_${rowIdx}`);
            const btn = document.getElementById(`subBtn_${rowIdx}`);
            const isOpen = inner.classList.toggle('open');
            icon.className = isOpen ? 'fas fa-chevron-up' : 'fas fa-chevron-down';
            btn.classList.toggle('active', isOpen);
        }

        function onAnnualInput(rowIdx) {
            const v = parseFloat(document.getElementById(`annual_${rowIdx}`).value);
            document.getElementById(`monthly_${rowIdx}`).value = (!isNaN(v) && v > 0) ? (v / 12).toFixed(2) : '';
        }

        function onMonthlyInput(rowIdx) {
            const v = parseFloat(document.getElementById(`monthly_${rowIdx}`).value);
            document.getElementById(`annual_${rowIdx}`).value = (!isNaN(v) && v > 0) ? (v * 12).toFixed(2) : '';
        }

        function onSubInput(rowIdx, subId) {
            const val = parseFloat(document.getElementById(`smonthly_${rowIdx}_${subId}`)?.value);
            const chip = document.getElementById(`chip_${rowIdx}_${subId}`);
            if (chip) chip.classList.toggle('has-rent', !isNaN(val) && val > 0);
        }

        // ─── Owner doc blocks ─────────────────────────────────────────────────────
        // function buildOwnerDocBlocks() {
        //     const count = parseInt(document.getElementById('ownerCountSelect').value) || 1;
        //     const container = document.getElementById('ownerDocBlocks');
        //     container.innerHTML = '';
        //     for (let i = 1; i <= count; i++) {
        //         const block = document.createElement('div');
        //         block.className = 'owner-block';
        //         block.innerHTML = `
    //             <div class="owner-block-title">
    //                 <span class="owner-num-badge">${i}</span>
    //                 Owner ${i} Documents
    //                 // <span class="badge-mandatory">Mandatory</span>
    //             </div>
    //             <div class="row">
    //                 <div class="col-md-6">
    //                     <div class="form-group">
    //                         <label>Emirates ID Number</label>
    //                         <input type="hidden" name="owners[${i}][2][id]" id="eidDocId_${i}" value="">
    //                         <input type="text" name="owners[${i}][2][emirates_id]"
    //                             class="form-control emirates-id" placeholder="784-XXXX-XXXXXXX-X" required>
    //                     </div>
    //                 </div>
    //                 <div class="col-md-6">
    //                     <div class="form-group">
    //                         <label>Emirates ID Upload </label>
    //                         <div class="file-upload-wrap">
    //                             <input type="file" name="owners[${i}][2][emirates_file]" accept="image/*,.pdf"
    //                                 onchange="onFileChange(this,'eidFace_${i}','eidLabel_${i}')">
    //                             <div class="file-upload-face" id="eidFace_${i}">
    //                                 <i class="fas fa-upload"></i>
    //                                 <span id="eidLabel_${i}">Click to upload or drag &amp; drop</span>
    //                             </div>
    //                         </div>
    //                     </div>
    //                 </div>

    //                 <!-- Emirates Issued -->
    //                 <div class="col-md-6">
    //                     <div class="form-group">
    //                         <label>Emirates ID Issued Date</label>
    //                         <div class="input-group date" id="emiratesIssued_${i}" data-target-input="nearest">
    //                             <input type="text" class="form-control datetimepicker-input"
    //                                 name="owners[${i}][2][emirates_issued]"
    //                                 data-target="#emiratesIssued_${i}" placeholder="DD-MM-YYYY">
    //                             <div class="input-group-append" data-target="#emiratesIssued_${i}" data-toggle="datetimepicker">
    //                                 <div class="input-group-text"><i class="fa fa-calendar"></i></div>
    //                             </div>
    //                         </div>
    //                     </div>
    //                 </div>

    //                 <!-- Emirates Expiry -->
    //                 <div class="col-md-6">
    //                     <div class="form-group">
    //                         <label>Emirates ID Expiry Date</label>
    //                         <div class="input-group date" id="emiratesExpiry_${i}" data-target-input="nearest">
    //                             <input type="text" class="form-control datetimepicker-input"
    //                                 name="owners[${i}][2][emirates_expiry]"
    //                                 id="eidExpiry_${i}"
    //                                 data-target="#emiratesExpiry_${i}" placeholder="DD-MM-YYYY"
    //                                 onchange="checkExpiry('eidExpiry_${i}','eidWarn_${i}')">
    //                             <div class="input-group-append" data-target="#emiratesExpiry_${i}" data-toggle="datetimepicker">
    //                                 <div class="input-group-text"><i class="fa fa-calendar"></i></div>
    //                             </div>
    //                         </div>
    //                         <span class="expiry-warn" id="eidWarn_${i}"></span>
    //                     </div>
    //                 </div>

    //                 <div class="col-md-12"><hr class="my-2"></div>

    //                 <div class="col-md-6">
    //                     <div class="form-group">
    //                         <label>Passport Number </label>
    //                         <input type="hidden" name="owners[${i}][1][id]" id="ppDocId_${i}" value="">
    //                         <input type="text" name="owners[${i}][1][passport_number]"
    //                             class="form-control passport-number" placeholder="e.g. A12345678">
    //                     </div>
    //                 </div>
    //                 <div class="col-md-6">
    //                     <div class="form-group">
    //                         <label>Passport Upload </label>
    //                         <div class="file-upload-wrap">
    //                             <input type="file" name="owners[${i}][1][passport_file]" accept="image/*,.pdf"
    //                                 onchange="onFileChange(this,'ppFace_${i}','ppLabel_${i}')">
    //                             <div class="file-upload-face" id="ppFace_${i}">
    //                                 <i class="fas fa-upload"></i>
    //                                 <span id="ppLabel_${i}">Click to upload or drag &amp; drop</span>
    //                             </div>
    //                         </div>
    //                     </div>
    //                 </div>

    //                 <!-- Passport Issued -->
    //                 <div class="col-md-6">
    //                     <div class="form-group">
    //                         <label>Passport Issued Date</label>
    //                         <div class="input-group date" id="passportIssued_${i}" data-target-input="nearest">
    //                             <input type="text" class="form-control datetimepicker-input"
    //                                 name="owners[${i}][1][passport_issued]"
    //                                 data-target="#passportIssued_${i}" placeholder="DD-MM-YYYY">
    //                             <div class="input-group-append" data-target="#passportIssued_${i}" data-toggle="datetimepicker">
    //                                 <div class="input-group-text"><i class="fa fa-calendar"></i></div>
    //                             </div>
    //                         </div>
    //                     </div>
    //                 </div>

    //                 <!-- Passport Expiry -->
    //                 <div class="col-md-6">
    //                     <div class="form-group">
    //                         <label>Passport Expiry Date</label>
    //                         <div class="input-group date" id="passportExpiry_${i}" data-target-input="nearest">
    //                             <input type="text" class="form-control datetimepicker-input"
    //                                 name="owners[${i}][1][passport_expiry]"
    //                                 id="ppExpiry_${i}"
    //                                 data-target="#passportExpiry_${i}" placeholder="DD-MM-YYYY"
    //                                 onchange="checkExpiry('ppExpiry_${i}','ppWarn_${i}')">
    //                             <div class="input-group-append" data-target="#passportExpiry_${i}" data-toggle="datetimepicker">
    //                                 <div class="input-group-text"><i class="fa fa-calendar"></i></div>
    //                             </div>
    //                         </div>
    //                         <span class="expiry-warn" id="ppWarn_${i}"></span>
    //                     </div>
    //                 </div>

    //             </div>`;

        //         container.appendChild(block);

        //         // ── Init date pickers after DOM is ready ──
        //         initOwnerDatePickers(i);
        //         // ── Pre-fill existing owner documents if any ──
        //         console.log("testdrdrdtrd", existingOwnerDocsJson);
        //         if (existingOwnerDocsJson[i]) {
        //             existingOwnerDocsJson[i].forEach(doc => {
        //                 console.log("doc", doc);
        //                 const type = doc.document_type; // 1=Passport, 2=Emirates ID

        //                 const docIdInput = document.getElementById(type === 2 ? `eidDocId_${i}` : `ppDocId_${i}`);
        //                 if (docIdInput) docIdInput.value = doc.id ?? '';

        //                 // Set document number
        //                 const numberInput = document.querySelector(
        //                     `input[name="owners[${i}][${type}][${type===2?'emirates_id':'passport_number'}]"]`);
        //                 if (numberInput) numberInput.value = doc.document_number;

        //                 // Set issued date
        //                 const issuedInput = document.querySelector(
        //                     `input[name="owners[${i}][${type}][${type===2?'emirates_issued':'passport_issued'}]"]`
        //                 );
        //                 if (issuedInput) issuedInput.value = doc.issued_date;

        //                 // Set expiry date
        //                 const expiryInput = document.getElementById(`${type===2?'eidExpiry':'ppExpiry'}_${i}`);
        //                 if (expiryInput) {
        //                     expiryInput.value = doc.expiry_date;
        //                     checkExpiry(expiryInput.id, type === 2 ? `eidWarn_${i}` : `ppWarn_${i}`);
        //                 }

        //                 // Show file label as uploaded
        //                 const fileLabel = document.getElementById(`${type===2?'eidLabel':'ppLabel'}_${i}`);
        //                 if (fileLabel) fileLabel.textContent = 'File uploaded';

        //                 // Optionally: show file thumbnail
        //                 const fileFace = document.getElementById(`${type===2?'eidFace':'ppFace'}_${i}`);
        //                 if (fileFace && doc.view_url) {
        //                     fileFace.style.backgroundImage = `url('${doc.view_url}')`;
        //                     fileFace.style.backgroundSize = 'contain';
        //                     fileFace.style.backgroundRepeat = 'no-repeat';
        //                     fileFace.style.backgroundPosition = 'center';
        //                 }
        //             });
        //         }
        //     }
        // }
        let existingOwnerKeys = [];

        function buildOwnerDocBlocks() {
            const count = parseInt(document.getElementById('ownerCountSelect').value) || 1;
            const container = document.getElementById('ownerDocBlocks');
            container.innerHTML = '';

            // ── Build owner slots (same logic as generateOwners in page 2) ──
            let ownerSlots = [];
            console.log("b2bdocsJson", existingOwnerDocsJson);
            existingOwnerKeys = Object.keys(existingOwnerDocsJson).map(Number); // [2, 3] etc.
            let existingCount = existingOwnerKeys.length;

            if (existingOwnerKeys.length > 0) {
                if (count < existingCount) {
                    // keep all owners visible when reducing
                    ownerSlots = existingOwnerKeys;
                } else {
                    ownerSlots = existingOwnerKeys.slice(0, count);

                    if (count > existingOwnerKeys.length) {
                        let maxKey = Math.max(...existingOwnerKeys);
                        for (let j = 1; j <= count - existingOwnerKeys.length; j++) {
                            ownerSlots.push(maxKey + j);
                        }
                    }
                }
            } else {
                // CREATE MODE: just use 1, 2, 3...
                for (let j = 1; j <= count; j++) {
                    ownerSlots.push(j);
                }
            }

            ownerSlots.forEach(function(ownerIndex, displayIndex) {
                const displayNumber = displayIndex + 1;
                const ownerDocList = existingOwnerDocsJson[ownerIndex] || [];

                // Find passport (type 1) and emirates (type 2) from the flat array
                const emiratesDoc = ownerDocList.find(d => d.document_type === 2) || {};
                const passportDoc = ownerDocList.find(d => d.document_type === 1) || {};

                const block = document.createElement('div');
                block.className = 'owner-block';
                block.id = `owner_${ownerIndex}`;
                block.innerHTML = `
                    <div class="owner-block-title">
                        <span class="owner-num-badge">${displayNumber}</span>
                        Owner ${displayNumber} Documents
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Emirates ID Number</label>
                                <input type="hidden" name="owners[${ownerIndex}][2][id]" id="eidDocId_${ownerIndex}" value="${emiratesDoc.doc_id ?? ''}">
                                <input type="text" name="owners[${ownerIndex}][2][emirates_id]"
                                    class="form-control emirates-id" placeholder="784-XXXX-XXXXXXX-X"
                                    value="${emiratesDoc.document_number ?? ''}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Emirates ID Upload</label>
                                <div class="file-upload-wrap">
                                    <input type="file" name="owners[${ownerIndex}][2][emirates_file]" accept="image/*,.pdf"
                                        onchange="onFileChange(this,'eidFace_${ownerIndex}','eidLabel_${ownerIndex}')">
                                    <div class="file-upload-face ${emiratesDoc.view_url ? 'has-file' : ''}" id="eidFace_${ownerIndex}"
                                        ${emiratesDoc.view_url ? `style="background-image:url('${emiratesDoc.view_url}');background-size:contain;background-repeat:no-repeat;background-position:center;"` : ''}>
                                        <i class="fas fa-upload"></i>
                                        <span id="eidLabel_${ownerIndex}">${emiratesDoc.view_url ? 'File uploaded' : 'Click to upload or drag &amp; drop'}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Emirates ID Issued Date</label>
                                <div class="input-group date" id="emiratesIssued_${ownerIndex}" data-target-input="nearest">
                                    <input type="text" class="form-control datetimepicker-input"
                                        name="owners[${ownerIndex}][2][emirates_issued]"
                                        data-target="#emiratesIssued_${ownerIndex}" placeholder="DD-MM-YYYY"
                                        value="${emiratesDoc.issued_date ?? ''}">
                                    <div class="input-group-append" data-target="#emiratesIssued_${ownerIndex}" data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Emirates ID Expiry Date</label>
                                <div class="input-group date" id="emiratesExpiry_${ownerIndex}" data-target-input="nearest">
                                    <input type="text" class="form-control datetimepicker-input"
                                        name="owners[${ownerIndex}][2][emirates_expiry]"
                                        id="eidExpiry_${ownerIndex}"
                                        data-target="#emiratesExpiry_${ownerIndex}" placeholder="DD-MM-YYYY"
                                        onchange="checkExpiry('eidExpiry_${ownerIndex}','eidWarn_${ownerIndex}')"
                                        value="${emiratesDoc.expiry_date ?? ''}">
                                    <div class="input-group-append" data-target="#emiratesExpiry_${ownerIndex}" data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                    </div>
                                </div>
                                <span class="expiry-warn" id="eidWarn_${ownerIndex}"></span>
                            </div>
                        </div>

                        <div class="col-md-12"><hr class="my-2"></div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Passport Number</label>
                                <input type="hidden" name="owners[${ownerIndex}][1][id]" id="ppDocId_${ownerIndex}" value="${passportDoc.doc_id ?? ''}">
                                <input type="text" name="owners[${ownerIndex}][1][passport_number]"
                                    class="form-control passport-number" placeholder="e.g. A12345678"
                                    value="${passportDoc.document_number ?? ''}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Passport Upload</label>
                                <div class="file-upload-wrap">
                                    <input type="file" name="owners[${ownerIndex}][1][passport_file]" accept="image/*,.pdf"
                                        onchange="onFileChange(this,'ppFace_${ownerIndex}','ppLabel_${ownerIndex}')">
                                    <div class="file-upload-face ${passportDoc.view_url ? 'has-file' : ''}" id="ppFace_${ownerIndex}"
                                        ${passportDoc.view_url ? `style="background-image:url('${passportDoc.view_url}');background-size:contain;background-repeat:no-repeat;background-position:center;"` : ''}>
                                        <i class="fas fa-upload"></i>
                                        <span id="ppLabel_${ownerIndex}">${passportDoc.view_url ? 'File uploaded' : 'Click to upload or drag &amp; drop'}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Passport Issued Date</label>
                                <div class="input-group date" id="passportIssued_${ownerIndex}" data-target-input="nearest">
                                    <input type="text" class="form-control datetimepicker-input"
                                        name="owners[${ownerIndex}][1][passport_issued]"
                                        data-target="#passportIssued_${ownerIndex}" placeholder="DD-MM-YYYY"
                                        value="${passportDoc.issued_date ?? ''}">
                                    <div class="input-group-append" data-target="#passportIssued_${ownerIndex}" data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Passport Expiry Date</label>
                                <div class="input-group date" id="passportExpiry_${ownerIndex}" data-target-input="nearest">
                                    <input type="text" class="form-control datetimepicker-input"
                                        name="owners[${ownerIndex}][1][passport_expiry]"
                                        id="ppExpiry_${ownerIndex}"
                                        data-target="#passportExpiry_${ownerIndex}" placeholder="DD-MM-YYYY"
                                        onchange="checkExpiry('ppExpiry_${ownerIndex}','ppWarn_${ownerIndex}')"
                                        value="${passportDoc.expiry_date ?? ''}">
                                    <div class="input-group-append" data-target="#passportExpiry_${ownerIndex}" data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                    </div>
                                </div>
                                <span class="expiry-warn" id="ppWarn_${ownerIndex}"></span>
                            </div>
                        </div>
                    </div>`;

                container.appendChild(block);
                if (existingOwnerKeys.length > 0 && count < existingCount) {
                    const tenantId = document.getElementById('agreement_tenant_id').value;

                    const removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.dataset.owner = ownerIndex;
                    removeBtn.dataset.tenantId = tenantId;
                    removeBtn.className = 'btn btn-outline-danger btn-sm remove-owner-btn mt-2';
                    removeBtn.innerText = 'Remove Owner';

                    // removeBtn.onclick = function() {

                    //     block.remove();

                    //     const remaining = document.querySelectorAll('#ownerDocBlocks .owner-block').length;

                    //     if (remaining === count) {
                    //         document.querySelectorAll('.remove-owner-btn').forEach(btn => btn.remove());
                    //     }

                    // };

                    block.appendChild(removeBtn);
                }

                // ── Init date pickers ──
                initOwnerDatePickers(ownerIndex);

                // ── Trigger expiry checks if values exist ──
                if (emiratesDoc.expiry_date) checkExpiry(`eidExpiry_${ownerIndex}`, `eidWarn_${ownerIndex}`);
                if (passportDoc.expiry_date) checkExpiry(`ppExpiry_${ownerIndex}`, `ppWarn_${ownerIndex}`);
            });
        }

        // ─── B2C doc rows ─────────────────────────────────────────────────────────
        const docTypesB2C = [{
                value: '',
                label: '— Select Document Type —'
            },
            {
                value: '2',
                label: 'Emirates ID'
            },
            {
                value: '1',
                label: 'Passport'
            },
        ];
        const docTypesB2BExtra = [{
                value: '',
                label: '— Select Document Type —'
            },
            {
                value: '5',
                label: 'Tenancy Contract'
            },
            {
                value: '6',
                label: 'Other'
            },
        ];
        let docCountB2C = 0;

        // function addDocRowB2C(prefill = null) {
        //     docCountB2C++;
        //     const idx = docCountB2C;
        //     const container = document.getElementById('docRowsB2C');
        //     const row = document.createElement('div');
        //     row.className = 'doc-row';
        //     row.id = `docRowB2C_${idx}`;
        //     row.innerHTML = `
    //     <div class="doc-row-header">
    //         <span class="doc-row-title">Document ${idx}</span>
    //         ${idx > 1 ? `<button type="button" class="btn-remove-doc"
        //                                                             onclick="document.getElementById('docRowB2C_${idx}').remove()">
        //                                                             <i class="fas fa-times"></i> Remove</button>` : ''}
    //     </div>
    //     <div class="row">
    //         <div class="col-md-6">
    //             <div class="form-group">
    //                 <label>Document Type <span class="text-danger">*</span></label>
    //                 <select name="docsB2C[${idx}][type]" class="form-control">
    //                     ${docTypesB2C.map(d=>`<option value="${d.value}">${d.label}</option>`).join('')}
    //                 </select>
    //             </div>
    //         </div>
    //         <div class="col-md-6">
    //             <div class="form-group">
    //                 <label>Document Number</label>
    //                 <input type="text" name="docsB2C[${idx}][number]" class="form-control" id="b2cDocNumber_${idx}"
    //                     placeholder="e.g. reference number">
    //             </div>
    //         </div>
    //         <div class="col-md-12">
    //             <div class="form-group">
    //                 <label>Upload Document <small class="text-muted font-weight-normal">(JPG, PNG, PDF)</small></label>
    //                 <div class="file-upload-wrap">
    //                     <input type="file" name="docsB2C[${idx}][file]" accept="image/*,.pdf"
    //                         onchange="onFileChange(this,'b2cDocFace_${idx}','b2cDocLabel_${idx}')">
    //                     <div class="file-upload-face" id="b2cDocFace_${idx}">
    //                         <i class="fas fa-upload"></i>
    //                         <span id="b2cDocLabel_${idx}">Click to upload or drag &amp; drop</span>
    //                     </div>
    //                 </div>
    //             </div>
    //         </div>
    //         <div class="col-md-6">
    //             <div class="form-group">
    //                 <label>Issued Date</label>
    //                 <input type="date" name="docsB2C[${idx}][issued_date]" id="b2cDocIssued_${idx}" class="form-control">
    //             </div>
    //         </div>
    //         <div class="col-md-6">
    //             <div class="form-group">
    //                 <label>Expiry Date</label>
    //                 <input type="date" name="docsB2C[${idx}][expiry_date]"
    //                     id="b2cDocExpiry_${idx}" class="form-control"
    //                     onchange="checkExpiry('b2cDocExpiry_${idx}','b2cDocWarn_${idx}')">
    //                 <span class="expiry-warn" id="b2cDocWarn_${idx}"></span>
    //             </div>
    //         </div>
    //     </div>`;
        //     container.appendChild(row);
        //     if (prefill) {
        //         const typeSelect = row.querySelector(`select[name="docsB2C[${idx}][type]"]`);
        //         if (typeSelect) typeSelect.value = prefill.type ?? '';

        //         // ✅ use name selector since id was missing before
        //         const numberInput = row.querySelector(`input[name="docsB2C[${idx}][number]"]`);
        //         if (numberInput) numberInput.value = prefill.number ?? '';

        //         const issuedInput = row.querySelector(`input[name="docsB2C[${idx}][issued_date]"]`);
        //         if (issuedInput) issuedInput.value = prefill.issued_date ?? '';

        //         const expiryInput = document.getElementById(`b2cDocExpiry_${idx}`);
        //         if (expiryInput) {
        //             expiryInput.value = prefill.expiry_date ?? '';
        //             if (prefill.expiry_date) checkExpiry(`b2cDocExpiry_${idx}`, `b2cDocWarn_${idx}`);
        //         }

        //         const label = document.getElementById(`b2cDocLabel_${idx}`);
        //         const face = document.getElementById(`b2cDocFace_${idx}`);
        //         if (label) label.textContent = '✓ Previously uploaded';
        //         if (face) {
        //             face.classList.add('has-file');
        //             if (prefill.view_url) {
        //                 face.style.backgroundImage = `url('${prefill.view_url}')`;
        //                 face.style.backgroundSize = 'contain';
        //                 face.style.backgroundRepeat = 'no-repeat';
        //                 face.style.backgroundPosition = 'center';
        //             }
        //         }
        //     }
        // }
        function addDocRowB2C(prefill = null) {
            console.log("ownerDocs", ownerDocs);
            docCountB2C++;
            const idx = docCountB2C;
            const container = document.getElementById('docRowsB2C');
            const row = document.createElement('div');
            row.className = 'doc-row';
            row.id = `docRowB2C_${idx}`;

            row.innerHTML = `
            <div class="doc-row-header">
                <span class="doc-row-title">Document ${idx}</span>
                <div id="docRowActions_${idx}">
                ${idx > 1 ? `<button type="button" class="btn-remove-doc"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                onclick="document.getElementById('docRowB2C_${idx}').remove(); refreshB2CDocDeleteButtons();">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <i class="fas fa-times"></i> Remove</button>` : ''}
                                    </div>
            </div>
            <input type="hidden" name="docsB2C[${idx}][id]" id="b2cDocId_${idx}" value="">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Document Type <span class="text-danger">*</span></label>
                        <select name="docsB2C[${idx}][type]" class="form-control" required>
                            ${docTypesB2C.map(d=>`<option value="${d.value}">${d.label}</option>`).join('')}
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Document Number</label>
                        <input type="text" name="docsB2C[${idx}][number]" id="b2cDocNumber_${idx}" class="form-control" placeholder="e.g. reference number" required>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group">
                        <label>Upload Document <small class="text-muted font-weight-normal">(JPG, PNG, PDF)</small></label>
                        <div class="file-upload-wrap">
                            <input type="file" name="docsB2C[${idx}][file]" accept="image/*,.pdf"
                                onchange="onFileChange(this,'b2cDocFace_${idx}','b2cDocLabel_${idx}')" >
                            <div class="file-upload-face" id="b2cDocFace_${idx}">
                                <i class="fas fa-upload"></i>
                                <span id="b2cDocLabel_${idx}">Click to upload or drag & drop</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Issued Date -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Issued Date</label>
                        <div class="input-group date" id="b2cIssued_${idx}" data-target-input="nearest">
                            <input type="text" class="form-control datetimepicker-input"
                                name="docsB2C[${idx}][issued_date]" data-target="#b2cIssued_${idx}" placeholder="DD-MM-YYYY" required>
                            <div class="input-group-append" data-target="#b2cIssued_${idx}" data-toggle="datetimepicker">
                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Expiry Date -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Expiry Date</label>
                        <div class="input-group date" id="b2cExpiry_${idx}" data-target-input="nearest">
                            <input type="text" class="form-control datetimepicker-input"
                                name="docsB2C[${idx}][expiry_date]" id="b2cDocExpiry_${idx}"
                                data-target="#b2cExpiry_${idx}" placeholder="DD-MM-YYYY"
                                onchange="checkExpiry('b2cDocExpiry_${idx}','b2cDocWarn_${idx}')" required>
                            <div class="input-group-append" data-target="#b2cExpiry_${idx}" data-toggle="datetimepicker">
                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                            </div>
                        </div>
                        <span class="expiry-warn" id="b2cDocWarn_${idx}"></span>
                    </div>
                </div>
            </div>
            `;

            container.appendChild(row);
            // refreshB2CDocDeleteButtons();

            // ── Init datetimepickers ──
            $(`#b2cIssued_${idx}`).datetimepicker({
                format: 'DD-MM-YYYY'
            });
            $(`#b2cExpiry_${idx}`).datetimepicker({
                format: 'DD-MM-YYYY'
            });

            // ── Prefill if editing ──
            if (prefill) {
                console.log("prefill", prefill);
                const docIdInput = document.getElementById(`b2cDocId_${idx}`);
                if (docIdInput) docIdInput.value = prefill.id ?? '';
                const typeSelect = row.querySelector(`select[name="docsB2C[${idx}][type]"]`);
                if (typeSelect) typeSelect.value = prefill.type ?? '';

                const numberInput = row.querySelector(`input[name="docsB2C[${idx}][number]"]`);
                if (numberInput) numberInput.value = prefill.number ?? '';

                const issuedInput = row.querySelector(`input[name="docsB2C[${idx}][issued_date]"]`);
                if (issuedInput) issuedInput.value = prefill.issued_date ?? '';

                const expiryInput = document.getElementById(`b2cDocExpiry_${idx}`);
                if (expiryInput) {
                    expiryInput.value = prefill.expiry_date ?? '';
                    if (prefill.expiry_date) checkExpiry(`b2cDocExpiry_${idx}`, `b2cDocWarn_${idx}`);
                }

                const label = document.getElementById(`b2cDocLabel_${idx}`);
                const face = document.getElementById(`b2cDocFace_${idx}`);
                if (label) label.textContent = '✓ Previously uploaded';
                if (face && prefill.view_url) {
                    face.classList.add('has-file');
                    face.style.backgroundImage = `url('${prefill.view_url}')`;
                    face.style.backgroundSize = 'contain';
                    face.style.backgroundRepeat = 'no-repeat';
                    face.style.backgroundPosition = 'center';
                }
                if (prefill.id) {
                    const actionsWrap = document.getElementById(`docRowActions_${idx}`);
                    if (actionsWrap) {
                        actionsWrap.innerHTML = `
            <button type="button" class="btn-remove-doc"
                onclick="deleteB2CDoc(this, ${prefill.id}, 'docRowB2C_${idx}')">
                <i class="fas fa-trash-alt"></i> Delete
            </button>`;
                    }
                }
            }
            refreshB2CDocDeleteButtons();
        }

        // function addDocRowB2C(prefill = null, docLabel = null, fixedType = null) {
        //     docCountB2C++;
        //     const idx = docCountB2C;
        //     const container = document.getElementById('docRowsB2C');
        //     const row = document.createElement('div');
        //     row.className = 'doc-row';
        //     row.id = `docRowB2C_${idx}`;

        //     // If fixedType, show label instead of dropdown
        //     const typeField = fixedType ?
        //         `<input type="text" name="docsB2C[${idx}][type]" value="${docLabel}"
    //     class="form-control" readonly style="background:#f8f9fa; color:#495057; cursor:default;" disabled>
    //     <input type="hidden" name="docsB2C[${idx}][type]" value="${fixedType}">` :
        //         `<select name="docsB2C[${idx}][type]" class="form-control" required>
    //         ${docTypesB2C.map(d => `<option value="${d.value}">${d.label}</option>`).join('')}
    //     </select>`;
        //     row.innerHTML = `
    //     <div class="doc-row-header">
    //         <span class="doc-row-title">${docLabel ?? 'Document ' + idx}</span>
    //         <div id="docRowActions_${idx}"></div>
    //     </div>
    //     <input type="hidden" name="docsB2C[${idx}][id]" id="b2cDocId_${idx}" value="">
    //     <div class="row">
    //         <div class="col-md-6">
    //             <div class="form-group">
    //                 <label>Document Type <span class="text-danger">*</span></label>
    //                 ${typeField}
    //             </div>
    //         </div>
    //         <div class="col-md-6">
    //             <div class="form-group">
    //                 <label>Document Number <span class="text-danger">*</span></label>
    //                 <input type="text" name="docsB2C[${idx}][number]" id="b2cDocNumber_${idx}"
    //                     class="form-control" placeholder="e.g. reference number" required>
    //             </div>
    //         </div>
    //         <div class="col-md-12">
    //             <div class="form-group">
    //                 <label>Upload Document <small class="text-muted font-weight-normal">(JPG, PNG, PDF)</small></label>
    //                 <div class="file-upload-wrap">
    //                     <input type="file" name="docsB2C[${idx}][file]" accept="image/*,.pdf"
    //                         onchange="onFileChange(this,'b2cDocFace_${idx}','b2cDocLabel_${idx}')">
    //                     <div class="file-upload-face" id="b2cDocFace_${idx}">
    //                         <i class="fas fa-upload"></i>
    //                         <span id="b2cDocLabel_${idx}">Click to upload or drag & drop</span>
    //                     </div>
    //                 </div>
    //             </div>
    //         </div>
    //         <div class="col-md-6">
    //             <div class="form-group">
    //                 <label>Issued Date</label>
    //                 <div class="input-group date" id="b2cIssued_${idx}" data-target-input="nearest">
    //                     <input type="text" class="form-control datetimepicker-input"
    //                         name="docsB2C[${idx}][issued_date]" data-target="#b2cIssued_${idx}" placeholder="DD-MM-YYYY">
    //                     <div class="input-group-append" data-target="#b2cIssued_${idx}" data-toggle="datetimepicker">
    //                         <div class="input-group-text"><i class="fa fa-calendar"></i></div>
    //                     </div>
    //                 </div>
    //             </div>
    //         </div>
    //         <div class="col-md-6">
    //             <div class="form-group">
    //                 <label>Expiry Date</label>
    //                 <div class="input-group date" id="b2cExpiry_${idx}" data-target-input="nearest">
    //                     <input type="text" class="form-control datetimepicker-input"
    //                         name="docsB2C[${idx}][expiry_date]" id="b2cDocExpiry_${idx}"
    //                         data-target="#b2cExpiry_${idx}" placeholder="DD-MM-YYYY"
    //                         onchange="checkExpiry('b2cDocExpiry_${idx}','b2cDocWarn_${idx}')">
    //                     <div class="input-group-append" data-target="#b2cExpiry_${idx}" data-toggle="datetimepicker">
    //                         <div class="input-group-text"><i class="fa fa-calendar"></i></div>
    //                     </div>
    //                 </div>
    //                 <span class="expiry-warn" id="b2cDocWarn_${idx}"></span>
    //             </div>
    //         </div>
    //     </div>`;

        //     container.appendChild(row);

        //     $(`#b2cIssued_${idx}`).datetimepicker({
        //         format: 'DD-MM-YYYY'
        //     });
        //     $(`#b2cExpiry_${idx}`).datetimepicker({
        //         format: 'DD-MM-YYYY'
        //     });

        //     if (prefill) {
        //         const docIdInput = document.getElementById(`b2cDocId_${idx}`);
        //         if (docIdInput) docIdInput.value = prefill.id ?? '';

        //         const numberInput = row.querySelector(`input[name="docsB2C[${idx}][number]"]`);
        //         if (numberInput) numberInput.value = prefill.number ?? '';

        //         const issuedInput = row.querySelector(`input[name="docsB2C[${idx}][issued_date]"]`);
        //         if (issuedInput) issuedInput.value = prefill.issued_date ?? '';

        //         const expiryInput = document.getElementById(`b2cDocExpiry_${idx}`);
        //         if (expiryInput) {
        //             expiryInput.value = prefill.expiry_date ?? '';
        //             if (prefill.expiry_date) checkExpiry(`b2cDocExpiry_${idx}`, `b2cDocWarn_${idx}`);
        //         }

        //         const label = document.getElementById(`b2cDocLabel_${idx}`);
        //         const face = document.getElementById(`b2cDocFace_${idx}`);
        //         if (label) label.textContent = '✓ Previously uploaded';
        //         if (face && prefill.view_url) {
        //             face.classList.add('has-file');
        //             face.style.backgroundImage = `url('${prefill.view_url}')`;
        //             face.style.backgroundSize = 'contain';
        //             face.style.backgroundRepeat = 'no-repeat';
        //             face.style.backgroundPosition = 'center';
        //         }

        //         // DB delete button for saved docs
        //         if (prefill.id) {
        //             const actionsWrap = document.getElementById(`docRowActions_${idx}`);
        //             if (actionsWrap) {
        //                 actionsWrap.innerHTML = `
    //             <button type="button" class="btn-remove-doc"
    //                 onclick="deleteB2CDoc(this, ${prefill.id}, 'docRowB2C_${idx}')">
    //                 <i class="fas fa-trash-alt"></i> Delete
    //             </button>`;
        //             }
        //         }
        //     }
        // }

        // ─── Existing customer ────────────────────────────────────────────────────
        // function onExistingCustomerToggle() {
        //     const checked = document.getElementById('existingCustomerCheck').checked;
        //     const panel = document.getElementById('existingCustomerPanel');
        //     panel.classList.toggle('visible', checked);
        //     document.getElementById('newCustomerForm').style.display = checked ? 'none' : '';
        //     if (checked) renderCustomerList(existingCustomers);
        // }

        function renderCustomerList(customers) {
            console.log('Rendering customer list with customers:', customers, 'Selected ID:', selectedCustomerId);
            const list = document.getElementById('customerList');
            list.innerHTML = customers.length ? customers.map(c => `
                <div class="customer-item ${selectedCustomerId === c.id ? 'selected' : ''}"
                    onclick="selectCustomer(${c.id})">
                    <div class="customer-avatar">${c.name.charAt(0)}</div>
                    <div class="flex-fill">
                        <div class="customer-item-name">${c.name}</div>
                        <div class="customer-item-meta">TL: ${c.tradeLicense}</div>
                    </div>
                    <span class="customer-item-badge">${c.type}</span>
                </div>`).join('') :
                '<div class="p-3 text-muted text-center small">No customers found</div>';
        }

        function filterCustomerList() {
            const q = document.getElementById('customerSearchInput').value.toLowerCase();
            renderCustomerList(existingCustomers.filter(c =>
                c.name.toLowerCase().includes(q) || c.tradeLicense.toLowerCase().includes(q)
            ));
        }

        function selectCustomer(id) {
            selectedCustomerId = id;
            const c = existingCustomers.find(x => x.id === id);
            if (!c) return;

            document.getElementById('existingCustomerPanel').classList.remove('visible');
            const dataSection = document.getElementById('selectedCustomerData');
            dataSection.classList.add('visible');
            document.getElementById('existingDivider').style.display = '';

            document.getElementById('customerDataGrid').innerHTML = `
            <div class="customer-data-grid-inner">
                <div class="customer-data-field"><div class="customer-data-field-label">Company Name</div>
                    <div class="customer-data-field-value">${c.name}</div></div>
                <div class="customer-data-field"><div class="customer-data-field-label">Trade License</div>
                    <div class="customer-data-field-value">${c.tradeLicense}</div></div>
            </div>`;

            document.getElementById('customerDocList').innerHTML = c.docs.map(d => `
            <div class="customer-doc-item">
                <i class="fas fa-file-alt customer-doc-icon text-info"></i>
                <span class="customer-doc-name">${d.name}</span>
                 <a href="${d.url}" target="_blank" class="customer-doc-status valid">
                    View
                </a>
            </div>`).join('');
        }

        // function clearSelectedCustomer() {
        //     selectedCustomerId = null;
        //     document.getElementById('selectedCustomerData').classList.remove('visible');
        //     document.getElementById('existingCustomerPanel').classList.add('visible');
        //     document.getElementById('existingDivider').style.display = 'none';
        //     document.getElementById('customerSearchInput').value = '';
        //     renderCustomerList(existingCustomers);
        // }

        function onExistingCustomerToggle() {
            const checked = document.getElementById('existingCustomerCheck').checked;
            const panel = document.getElementById('existingCustomerPanel');
            const newForm = document.getElementById('newCustomerForm');

            if (checked) {
                // Populate the dropdown if not already done
                populateExistingCustomerSelect();
                panel.style.display = 'block';
                newForm.style.display = 'none';

                // ── Disable & remove required from all new customer form fields ──
                newForm.querySelectorAll('input, select, textarea').forEach(el => {
                    el.disabled = true;
                    el.removeAttribute('required');
                });
            } else {
                panel.style.display = 'none';
                clearSelectedCustomer();
                newForm.style.display = 'block';
                newForm.querySelectorAll('input, select, textarea').forEach(el => {
                    el.disabled = false;
                });
            }
        }

        function populateExistingCustomerSelect() {
            const select = document.getElementById('existingCustomerSelect');
            select.required = true;
            $('#existingCustomerSelect').siblings('label').addClass('asterisk');

            // Only populate once
            if (select.options.length > 1) return;

            existingCustomers.forEach(c => {
                const opt = document.createElement('option');
                opt.value = c.id;
                opt.textContent = `${c.code}  —   ${c.name}`;
                select.appendChild(opt);
            });

            // Re-init Select2 if available
            if (typeof $ !== 'undefined' && $.fn.select2) {
                $('#existingCustomerSelect').select2({
                    placeholder: '— Search or select a company —',
                    allowClear: true,
                    width: '100%'
                }).on('change', function() {
                    onExistingCustomerSelected(this);
                });
            }
        }

        function onExistingCustomerSelected(select) {
            const val = select.value;
            const newForm = document.getElementById('newCustomerForm');
            const clearWrap = document.getElementById('clearExistingWrap');
            const b2bDocs = document.getElementById('b2bDocSection');
            const documentshead = document.getElementById('tenant-documents-head');

            if (val) {
                newForm.style.display = 'none';
                clearWrap.style.display = 'block';
                b2bDocs.style.display = 'none';
                documentshead.style.display = 'none'; // ← hide docs for existing customer

                let hiddenInput = document.getElementById('hiddenExistingCustomerId');
                if (!hiddenInput) {
                    hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'existing_customer_id';
                    hiddenInput.id = 'hiddenExistingCustomerId';
                    document.getElementById('tenancyForm').appendChild(hiddenInput);
                }
                hiddenInput.value = val;

            } else {
                newForm.style.display = 'block';
                clearWrap.style.display = 'none';
                b2bDocs.style.display = '';
                documentshead.style.display = ''; // ← restore docs when cleared

                const hiddenInput = document.getElementById('hiddenExistingCustomerId');
                if (hiddenInput) hiddenInput.value = '';
            }
        }

        function clearSelectedCustomer() {
            const select = document.getElementById('existingCustomerSelect');
            const newForm = document.getElementById('newCustomerForm');
            const clearWrap = document.getElementById('clearExistingWrap');
            select.required = false;
            $('#existingCustomerSelect').siblings('label').removeClass('asterisk');

            // Clear Select2 properly
            if (typeof $ !== 'undefined' && $.fn.select2) {
                $('#existingCustomerSelect').val(null).trigger('change');
            } else if (select) {
                select.value = '';
            }

            newForm.style.display = 'block';
            clearWrap.style.display = 'none';

            const hiddenInput = document.getElementById('hiddenExistingCustomerId');
            if (hiddenInput) hiddenInput.value = '';
        }

        // function clearSelectedCustomer() {
        //     const select = document.getElementById('existingCustomerSelect');
        //     const newForm = document.getElementById('newCustomerForm');
        //     const clearWrap = document.getElementById('clearExistingWrap');

        //     if (select) select.value = '';
        //     newForm.style.display = 'block';
        //     clearWrap.style.display = 'none';

        //     const hiddenInput = document.getElementById('selectedCustomerId');
        //     if (hiddenInput) hiddenInput.value = '';
        // }

        // ─── Dates & duration ─────────────────────────────────────────────────────
        function onDateChange() {
            const s = document.getElementById('startDate').value;
            const e = document.getElementById('endDate').value;
            const pill = document.getElementById('durationPill');
            if (s && e) {
                const start = new Date(s),
                    end = new Date(e);
                if (end > start) {
                    const months = Math.round((end - start) / (1000 * 60 * 60 * 24 * 30.44));
                    const years = Math.floor(months / 12);
                    const rem = months % 12;
                    let txt = '';
                    if (years > 0) txt += `${years} year${years>1?'s':''}`;
                    if (rem > 0) txt += (txt ? ', ' : '') + `${rem} month${rem>1?'s':''}`;
                    document.getElementById('durationText').textContent =
                        `Agreement duration: ${txt || '< 1 month'}`;
                    pill.style.display = '';
                    return;
                }
            }
            pill.style.display = 'none';
        }

        // ─── Rent calculation ─────────────────────────────────────────────────────
        function calculateAnnual() {
            const v = parseFloat(document.getElementById('rentMonth').value);
            if (!isNaN(v) && v > 0) {
                document.getElementById('displayMonth').innerHTML = v;
            } else {
                document.getElementById('displayMonth').innerHTML = '—<span>AED</span>';
                // document.getElementById('displayAnnum').innerHTML = '—<span>AED</span>';
            }
        }

        function calculateMonthly() {
            const v = parseFloat(document.getElementById('rentAnnum').value);
            if (!isNaN(v) && v > 0) {
                document.getElementById('displayAnnum').innerHTML = v;
            } else {
                document.getElementById('displayAnnum').innerHTML = '—<span>AED</span>';
            }
        }

        // ─── File upload feedback ─────────────────────────────────────────────────
        function onFileChange(input, faceId, labelId) {
            const face = document.getElementById(faceId);
            const label = document.getElementById(labelId);
            if (input.files && input.files[0]) {
                face.classList.add('has-file');
                label.textContent = `✓  ${input.files[0].name}  (${(input.files[0].size/1024).toFixed(0)} KB)`;
            } else {
                face.classList.remove('has-file');
                label.textContent = 'Click to upload or drag & drop';
            }
        }

        // ─── Expiry check ─────────────────────────────────────────────────────────
        function checkExpiry(expiryId, warnId) {
            // alert("test");
            const input = document.getElementById(expiryId);
            const warn = document.getElementById(warnId);
            if (!input?.value) {
                warn.className = 'expiry-warn';
                return;
            }

            // ── Parse dd-mm-YYYY format ──
            const parts = input.value.split('-');
            if (parts.length !== 3) return;
            const expiryDate = new Date(`${parts[2]}-${parts[1]}-${parts[0]}`); // → YYYY-MM-DD
            if (isNaN(expiryDate)) return;

            const diff = Math.ceil((expiryDate - new Date().setHours(0, 0, 0, 0)) / 86400000);

            if (diff < 0) {
                // alert("test");
                warn.textContent = `⚠ Expired ${Math.abs(diff)} day${Math.abs(diff) > 1 ? 's' : ''} ago`;
                warn.className = 'expiry-warn show expired';
                input.classList.add('is-invalid');
            } else if (diff <= 30) {
                warn.textContent = `⚠ Expires in ${diff} day${diff > 1 ? 's' : ''}`;
                warn.className = 'expiry-warn show soon';
                input.style.borderColor = '#ffc107';
            } else {
                warn.textContent = '';
                warn.className = 'expiry-warn';
                input.classList.remove('is-invalid');
                input.style.borderColor = '';
            }
        }

        // ─── Auto-init if old() values exist ──────────────────────────────────────
        window.addEventListener('DOMContentLoaded', function() {
            @if (old('business_type'))
                onBusinessTypeChange();
            @endif
        });
    </script>
    <script>
        function initOwnerDatePickers(ownerIndex) {
            $('#emiratesIssued_' + ownerIndex).datetimepicker({
                format: 'DD-MM-YYYY'
            });
            $('#emiratesExpiry_' + ownerIndex).datetimepicker({
                format: 'DD-MM-YYYY'
            });
            $('#passportIssued_' + ownerIndex).datetimepicker({
                format: 'DD-MM-YYYY'
            });
            $('#passportExpiry_' + ownerIndex).datetimepicker({
                format: 'DD-MM-YYYY'
            });
            // ── Listen to datetimepicker change events ──
            // $('#emiratesExpiry_' + ownerIndex).on('change.datetimepicker', function() {
            //     checkExpiry('eidExpiry_' + ownerIndex, 'eidWarn_' + ownerIndex);
            // });
            // $('#passportExpiry_' + ownerIndex).on('change.datetimepicker', function() {
            //     checkExpiry('ppExpiry_' + ownerIndex, 'ppWarn_' + ownerIndex);
            // });
        }
    </script>
    <script>
        // window.addEventListener('DOMContentLoaded', function() {
        //     @if (isset($agreement))
        //         // alert("test");
        //         // ── Pre-select property ──
        //         const propertySelect = document.getElementById('makaniNumber');
        //         propertySelect.value = '{{ $agreement->property_id }}';
        //         propertySelect.dispatchEvent(new Event('change'));

        //         // ── Pre-select business type & trigger all sections ──
        //         const btypeInput = document.getElementById(
        //             '{{ $agreement->business_type == 1 ? 'typeB2B' : 'typeB2C' }}');
        //         if (btypeInput) {
        //             btypeInput.checked = true;
        //             onBusinessTypeChange();
        //         }
        //         const rentM = document.getElementById('rentMonth').value;
        //         // console.log("rentM", rentM);
        //         const rentA = document.getElementById('rentAnnum').value;
        //         if (rentM) {
        //             calculateAnnual();
        //         }
        //         if (rentA) {
        //             calculateMonthly();
        //         }


        //         // ── Pre-fill units after table is built ──
        //         setTimeout(function() {
        //             // alert("test");
        //             const existingUnits = @json($existingUnits ?? []);
        //             console.log('existingUn', existingUnits);
        //             existingUnits.forEach(unit => {
        //                 // Find the checkbox by matching the name attribute
        //                 const checkbox = document.querySelector(
        //                     `input[name="unit_rent[${unit.key}][annual]"]`);
        //                 if (!checkbox) return;

        //                 // Get the row index from the annual input id
        //                 const rowId = checkbox.id.replace('annual_', '');
        //                 const rowCheckbox = document.getElementById(`selRow_${rowId}`);
        //                 if (rowCheckbox) rowCheckbox.checked = true;

        //                 // Fill rents
        //                 checkbox.value = unit.annual ?? '';
        //                 const monthlyInput = document.getElementById(`monthly_${rowId}`);
        //                 if (monthlyInput) monthlyInput.value = unit.monthly ?? '';

        //                 // Fill subunit rents
        //                 unit.subunit_rents.forEach(sub => {
        //                     const subInput = document.querySelector(
        //                         `input[name="subunit_rent[${unit.key}_${sub.id}][monthly]"]`
        //                     );
        //                     if (subInput) {
        //                         subInput.value = sub.monthly ?? '';
        //                         // Open the expand row
        //                         const rowIdForSub = subInput.id.split('_')[1];
        //                         const expandInner = document.getElementById(
        //                             `expandInner_${rowIdForSub}`);
        //                         if (expandInner && !expandInner.classList.contains(
        //                                 'open')) {
        //                             toggleExpandRow(rowIdForSub);
        //                         }
        //                     }
        //                 });
        //             });
        //         }, 300); // slight delay to ensure buildB2BTable() has finished
        //     @elseif (old('business_type'))
        //         onBusinessTypeChange();
        //     @endif
        // });
        window.addEventListener('DOMContentLoaded', function() {
            @if (isset($agreement))
                console.log('agreement Units', @json($agreement->agreementUnits));

                const existingUnits = @json($existingUnits ?? []);
                console.log('existing', existingUnits);

                // Wrap FIRST — before anything calls buildB2BTable
                const _originalBuildB2BTable = buildB2BTable;
                window.buildB2BTable = function() {
                    _originalBuildB2BTable();

                    // Only prefill when the table actually has rows
                    // (skip the empty first call from onBusinessTypeChange)
                    const hasRows = document.querySelector('#b2bUnitsBody input[type="number"]');
                    console.log("hasrows", hasRows);
                    if (hasRows) {
                        // alert("test");
                        @if ($agreement->business_type == 1)
                            prefillUnits(existingUnits);
                        @endif
                    }
                };

                // Step 1: Set business type
                const btypeInput = document.getElementById(
                    '{{ $agreement->business_type == 1 ? 'typeB2B' : 'typeB2C' }}');
                if (btypeInput) {
                    btypeInput.checked = true;
                    onBusinessTypeChange();
                }

                // Step 2: Rent display
                const rentM = document.getElementById('rentMonth');
                const rentA = document.getElementById('rentAnnum');
                if (rentM && rentM.value) calculateAnnual();
                if (rentA && rentA.value) calculateMonthly();

                // Step 3: Manually call onMakaniChange instead of triggering Select2
                // This guarantees it runs synchronously with the correct radio state
                const propertySelect = document.getElementById('makaniNumber');
                propertySelect.value = '{{ $agreement->property_id }}';
                onMakaniChange(propertySelect);
                @if ($agreement->business_type == 1)
                    mergeExistingUnitsIntoActiveData(existingUnits);
                    buildB2BTable();
                @endif
                @if ($agreement->business_type == 2)
                    @php
                        $unit = $existingUnits[0] ?? [];
                    @endphp

                    // const editData = {
                    //     floor: {{ $unit['floor'] ?? 'null' }},
                    //     unit_type_id: {{ $unit['unit_type_id'] ?? 'null' }},
                    //     unit_id: {{ $unit['unit_id'] ?? 'null' }},
                    //     subunit_id: {{ $unit['subunit_id'] ?? 'null' }},
                    //     unit_number: '{{ $unit['unit_number'] ?? '' }}',
                    //     subunit_number: '{{ $unit['subunit_number'] ?? '' }}'
                    // };
                    prefillB2CUnit(editData);
                @endif
            @elseif (old('business_type'))
                onBusinessTypeChange();
            @endif
        });

        function prefillUnits(existingUnits) {
            // alert("test");
            if (!existingUnits || existingUnits.length === 0) return;

            existingUnits.forEach(unit => {
                const annualInput = document.querySelector(
                    `input[name="unit_rent[${unit.key}][annual]"]`
                );
                if (!annualInput) {
                    console.warn('prefillUnits: no row found for key:', unit.key);
                    return;
                }

                const rowId = annualInput.id.replace('annual_', '');

                const rowCheckbox = document.getElementById(`selRow_${rowId}`);
                if (rowCheckbox) {
                    rowCheckbox.checked = true;
                    rowCheckbox.disabled = true;
                    onRowCheckboxChange(rowCheckbox, rowId);
                }

                annualInput.value = unit.annual ?? '';

                const monthlyInput = document.getElementById(`monthly_${rowId}`);
                console.log("test", monthlyInput);
                if (monthlyInput) monthlyInput.value = unit.monthly ?? '';
                const unitDbIdInput = document.getElementById(`unitDbId_${rowId}`);
                if (unitDbIdInput) unitDbIdInput.value = unit.agreement_unit_id ?? '';
                const delBtn = document.getElementById(`delBtn_${rowId}`);
                if (delBtn && unit.unit_id) {
                    delBtn.dataset.unitDbId = unit.agreement_unit_id;
                    delBtn.setAttribute('onclick', `deleteAgreementUnit(this)`);
                }

                if (unit.subunit_rents && unit.subunit_rents.length > 0) {
                    unit.subunit_rents.forEach(sub => {
                        const subInput = document.querySelector(
                            `input[name="subunit_rent[${unit.key}_${sub.id}][monthly]"]`
                        );
                        if (!subInput) {
                            console.warn('prefillUnits: no subunit input for:', unit.key, sub.id);
                            return;
                        }
                        subInput.value = sub.monthly ?? '';
                        // ✅ Set hidden subunit rent id
                        const subRentIdInput = document.getElementById(`subRentId_${rowId}_${sub.id}`);
                        if (subRentIdInput) subRentIdInput.value = sub.subunit_rent_id ?? '';

                        const rowIdForSub = subInput.id.split('_')[1];
                        const expandInner = document.getElementById(`expandInner_${rowIdForSub}`);
                        if (expandInner && !expandInner.classList.contains('open')) {
                            toggleExpandRow(rowIdForSub);
                        }
                    });
                }
            });
            refreshDeleteButtonVisibility();
        }

        function mergeExistingUnitsIntoActiveData(existingUnits) {

            if (!existingUnits || !existingUnits.length) return;


            existingUnits.forEach(unit => {
                console.log("unit", unit);
                const floor = unit.floor;
                const type = unit.unit_type_id;

                if (!activeUnitData[floor]) {
                    activeUnitData[floor] = {};
                }

                if (!activeUnitData[floor][type]) {
                    activeUnitData[floor][type] = {
                        label: unit.unit_type,
                        units: []
                    };
                }

                let units = activeUnitData[floor][type].units;

                let existingUnit = units.find(u => u.id == unit.unit_id);

                if (!existingUnit) {

                    existingUnit = {
                        id: unit.unit_id,
                        unit_number: unit.unit_number,
                        subunits: []
                    };

                    units.push(existingUnit);
                }

                // Merge subunits
                if (unit.subunit_rents) {

                    unit.subunit_rents.forEach(sub => {

                        if (!existingUnit.subunits.some(s => s.id == sub.id)) {

                            existingUnit.subunits.push({
                                id: sub.id,
                                label: sub.label
                            });

                        }

                    });

                }

            });

        }

        function prefillB2CUnit(editData) {
            if (!editData || !editData.floor) return;

            const floorSelect = document.getElementById('floorSelect');
            const unitTypeSelect = document.getElementById('unitTypeSelect');
            const unitSelect = document.getElementById('unitNumberSelect');
            const subSelect = document.getElementById('subunitSelect');

            // Step 1: Select Floor
            floorSelect.value = editData.floor;
            onFloorChange(floorSelect);

            // Step 2: Select Unit Type
            setTimeout(() => {
                if (editData.unit_type_id) {
                    unitTypeSelect.value = editData.unit_type_id;
                    onUnitTypeChange(unitTypeSelect);

                    // Step 3: Select Unit Number
                    setTimeout(() => {
                        if (editData.unit_id) {
                            unitSelect.value = editData.unit_id;
                            onUnitNumberChange(unitSelect);

                            // Step 4: Select Subunit (if exists)
                            setTimeout(() => {
                                if (editData.subunit_id) {
                                    subSelect.value = editData.subunit_id;
                                }
                                updateUnitSummary(); // show summary bar
                            }, 50);
                        }
                    }, 50);
                }
            }, 50);
        }

        function deleteAgreementUnit(btn) {
            const rowIdx = btn.dataset.row;
            const agreementId = btn.dataset.agreementId;
            const unitDbId = btn.dataset.unitDbId; // ← from data attribute, set in prefillUnits

            if (!unitDbId) {
                // Not a saved unit — just remove from DOM, no API call
                removeB2BRow(rowIdx);
                return;
            }

            Swal.fire({
                title: 'Remove this unit?',
                text: 'This unit will be removed from the this tenant.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, remove it',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (!result.isConfirmed) return;

                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

                fetch(`/agreements/${agreementId}/unit/${unitDbId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]')?.value,
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Removed',
                                text: data.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                            removeB2BRow(rowIdx);
                        } else {
                            throw new Error(data.message || 'Failed to remove unit.');
                        }
                    })
                    .catch(err => {
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fas fa-trash-alt"></i>';
                        Swal.fire('Error', err.message, 'error');
                    });
            });
        }

        function refreshDeleteButtonVisibility() {
            const rows = document.querySelectorAll('#b2bUnitsBody .unit-data-row');
            const show = rows.length > 1;
            rows.forEach(row => {
                const rowIdx = row.id.replace('b2bRow_', '');
                const btn = document.getElementById(`delBtn_${rowIdx}`);
                const row_ag_unit_id = document.getElementById(`unitDbId_${rowIdx}`)?.value ?? '';
                console.log("vallue ", row_ag_unit_id.value);
                if (btn && row_ag_unit_id) {
                    btn.style.display = show ? 'inline-flex' : 'none';
                }
                const deleteHeader = document.getElementById('unit_delete');
                if (deleteHeader) {
                    deleteHeader.style.display = show ? '' : 'none';
                }
            });
        }

        function removeB2BRow(rowIdx) {
            document.getElementById(`b2bRow_${rowIdx}`)?.remove();
            document.getElementById(`expandRow_${rowIdx}`)?.remove();
            refreshDeleteButtonVisibility();
        }

        function onRowCheckboxChange(checkbox, rowIdx) {
            // alert("test");
            const isChecked = checkbox.checked;

            const annualInput = document.getElementById(`annual_${rowIdx}`);
            const monthlyInput = document.getElementById(`monthly_${rowIdx}`);

            if (annualInput) annualInput.required = isChecked;
            if (monthlyInput) monthlyInput.required = isChecked;

            // ── NEVER set required on subunit inputs ──
            // They live inside a collapsed expand-inner (max-height:0, overflow:hidden)
            // Browser validation cannot focus hidden elements → throws "not focusable" error
            // Subunit rents are optional — backend already skips empty ones with:
            // if (empty($subRents['monthly'])) continue;
            const expandInner = document.getElementById(`expandInner_${rowIdx}`);
            if (expandInner) {
                expandInner.querySelectorAll('input[type="number"]').forEach(input => {
                    input.required = false; // always false — never required
                });

                if (isChecked) {
                    if (!expandInner.classList.contains('open')) {
                        toggleExpandRow(rowIdx);
                    }
                } else {
                    // ── Force close the expand row ──
                    if (expandInner.classList.contains('open')) {
                        expandInner.classList.remove('open'); // ← directly remove, no toggle
                        const icon = document.getElementById(`subBtnIcon_${rowIdx}`);
                        const btn = document.getElementById(`subBtn_${rowIdx}`);
                        if (icon) icon.className = 'fas fa-chevron-down';
                        if (btn) btn.classList.remove('active');
                    }

                    // if (annualInput) annualInput.value = '';
                    // if (monthlyInput) monthlyInput.value = '';
                }
            }
        }

        function refreshB2CDocDeleteButtons() {
            const rows = document.querySelectorAll('#docRowsB2C .doc-row');
            // alert(rows.length);
            rows.forEach(row => {
                const btn = row.querySelector('.btn-remove-doc');
                // alert(btn);
                if (btn) {
                    // alert("testdoc");
                    btn.disabled = rows.length <= 1;
                }
            });
        }

        function deleteB2CDoc(btn, docId, rowId) {
            Swal.fire({
                title: 'Delete this document?',
                text: 'This document will be permanently removed.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (!result.isConfirmed) return;

                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

                const tenantId = document.getElementById('agreement_tenant_id').value;
                alert(tenantId);

                fetch(`/tenants/${tenantId}/document/${docId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]')?.value,
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById(rowId)?.remove();
                            refreshB2CDocDeleteButtons(); // ← add here
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted',
                                text: data.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                        }
                    })
                    .catch(err => {
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fas fa-trash-alt"></i> Delete';
                        Swal.fire('Error', err.message, 'error');
                    });
            });
        }
    </script>
    <script>
        function validateAll() {
            let submitBtn = $('#submitBtn');
            let allValid = true;

            // ── Trade License ──
            let tradeInput = $('#tlNumber');
            if (tradeInput.length) {
                let val = tradeInput.val();
                // let regex = /^[A-Z0-9\/-]{5,20}$/;
                let regex = /^[A-Z0-9\/\-.]{4,20}$/;
                // tradeInput.removeClass('is-invalid');
                tradeInput.removeClass('is-invalid');
                tradeInput.next('.invalid-feedback').remove();
                tradeInput.siblings('.invalid-feedback').remove();
                if (val.length != 0 && !regex.test(val)) {
                    showError(tradeInput, "Trade License must be 4–20 characters (letters, numbers, / or -,. only)");
                    allValid = false;
                }
            }

            // ── Passports (B2B owner blocks + B2C hardcoded) ──
            $('.passport-number, #b2cDocNumber_2').each(function() {
                let val = $(this).val().trim();
                let regex = /^[A-Z0-9]{6,9}$/;
                $(this).removeClass('is-invalid');
                $(this).next('.invalid-feedback').remove();
                if (val.length != 0 && !regex.test(val)) {
                    showError($(this), "Passport must be 6–9 characters (letters & numbers only)");
                    allValid = false;
                }
            });

            // ── Emirates IDs (B2B owner blocks + B2C hardcoded) ──
            $('.emirates-id, #b2cDocNumber_1').each(function() {
                let val = $(this).val().trim();
                let regex = /^\d{3}-\d{4}-\d{7}-\d{1}$/;
                $(this).removeClass('is-invalid');
                $(this).next('.invalid-feedback').remove();
                if (val.length != 0 && !regex.test(val)) {
                    showError($(this), "Emirates ID must be in format: 784-XXXX-XXXXXXX-X");
                    allValid = false;
                }
            });

            // ── Phone numbers ──
            // ── Phone numbers — only validate visible, enabled fields ──
            let mobileInput = $('input[name="tenant_mobile"]:visible:not(:disabled)');
            let contactInput = $('input[name="contact_number"]:visible:not(:disabled)');

            if (mobileInput.length) {
                if (!validatePhoneUAE(mobileInput.first())) allValid = false;
            }
            if (contactInput.length) {
                if (!validatePhoneUAE(contactInput.first())) allValid = false;
            }


            if ($('#typeB2C').is(':checked')) {

                let eidNumber = $('#b2cDocNumber_1').val().trim();
                let eidFile = $('input[name="docsB2C[1][file]"]')[0];
                let eidDocId = $('#b2cDocId_1').val();
                let eidFace = $('#b2cDocFace_1');
                eidFace.siblings('.invalid-feedback').remove();

                if (eidNumber.length > 0 && eidFile && !eidFile.files.length && !eidDocId) {
                    eidFace.after(
                        '<span class="invalid-feedback" style="display:block;">Emirates ID file is required.</span>');
                    allValid = false;
                }

                let ppNumber = $('#b2cDocNumber_2').val().trim();
                let ppFile = $('input[name="docsB2C[2][file]"]')[0];
                let ppDocId = $('#b2cDocId_2').val();
                let ppFace = $('#b2cDocFace_2');
                ppFace.siblings('.invalid-feedback').remove();

                if (ppNumber.length > 0 && ppFile && !ppFile.files.length && !ppDocId) {
                    ppFace.after('<span class="invalid-feedback" style="display:block;">Passport file is required.</span>');
                    allValid = false;
                }
            }
            // ── B2B: at least one unit must be checked ──
            if ($('#typeB2B').is(':checked')) {
                const totalUnits = $('#b2bUnitsBody .row-checkbox').length;
                const checkedUnits = $('#b2bUnitsBody .row-checkbox:checked').length;
                const b2bUnitError = $('#b2bUnitError');

                // ── Only validate if there are units in the table ──
                if (totalUnits > 0) {
                    if (checkedUnits === 0) {
                        if (!b2bUnitError.length) {
                            $('#b2bUnitsTable').after(
                                '<div id="b2bUnitError" class="doc-error visible" style="display:flex;">' +
                                '<i class="fas fa-exclamation-circle"></i>' +
                                ' Please select at least one unit.' +
                                '</div>'
                            );
                        }
                        allValid = false;
                    } else {
                        b2bUnitError.remove();
                    }
                } else {
                    // ── No units in table — remove error if it exists ──
                    b2bUnitError.remove();
                }
            }

            submitBtn.prop('disabled', !allValid);
            return allValid;
        }

        function showError(input, message) {
            input.addClass('is-invalid');

            // Remove any existing error for this input
            input.next('.invalid-feedback').remove();
            input.siblings('.invalid-feedback').remove();

            // Insert error message after the input
            input.after(`<span class="invalid-feedback" style="display:block;">${message}</span>`);
        }

        function validatePhoneUAE(input) {
            if (!input.length) return true;

            let val = input.val().toString().replace(/[^0-9]/g, '');

            // ── Always clean up first ──
            input.removeClass('is-invalid');
            input.next('.invalid-feedback').remove();
            input.siblings('.invalid-feedback').remove(); // ← add this

            if (val.length === 0) return true;

            let withCode = /^(9715[0-9]{8}|971[2-9][0-9]{7})$/;
            let withoutCode = /^(05[0-9]{8}|0[2-9][0-9]{7})$/;

            if (!withCode.test(val) && !withoutCode.test(val)) {
                showError(input, "Enter a valid UAE number (e.g. 971501234567 or 0501234567)");
                return false;
            }

            return true;
        }
        $(document).ready(function() {
            validateAll();

            // existing listeners...
            $(document).on('input', '#tlNumber', function() {
                this.value = this.value.toUpperCase();
                const tlFile = $('#tlFile')[0];
                const tlDocId = $('input[name="tl_id"]').val();
                const tlFace = $('#tlFileFace');

                tlFace.siblings('.invalid-feedback').remove();

                if ($(this).val().trim().length > 0 && tlFile && !tlFile.files.length && !tlDocId) {
                    tlFace.after(
                        '<span class="invalid-feedback" style="display:block;">Trade License file is required.</span>'
                    );
                }
                validateAll();
            });

            $(document).on('input', '.passport-number', function() {
                this.value = this.value.toUpperCase();
                this.value = this.value.replace(/[^A-Z0-9]/g, '');

                // Extract owner index from name attribute e.g. owners[1][1][passport_number]
                const name = $(this).attr('name');
                const match = name.match(/owners\[(\d+)\]/);
                if (match) {
                    const i = match[1];
                    const ppFile = $(`input[name="owners[${i}][1][passport_file]"]`)[0];
                    const ppDocId = $(`#ppDocId_${i}`).val();
                    const ppFace = $(`#ppFace_${i}`);

                    ppFace.siblings('.invalid-feedback').remove();

                    if ($(this).val().trim().length > 0 && ppFile && !ppFile.files.length && !ppDocId) {
                        ppFace.after(
                            '<span class="invalid-feedback" style="display:block;">Passport file is required.</span>'
                        );
                    }
                }


                validateAll();
            });

            $(document).on('input', '.emirates-id', function() {
                // alert("tesy");
                this.value = this.value.replace(/[^0-9-]/g, '');
                // Extract owner index from name attribute e.g. owners[1][2][emirates_id]
                const name = $(this).attr('name');
                const match = name.match(/owners\[(\d+)\]/);
                if (match) {
                    const i = match[1];
                    const eidFile = $(`input[name="owners[${i}][2][emirates_file]"]`)[0];
                    const eidDocId = $(`#eidDocId_${i}`).val();
                    const eidFace = $(`#eidFace_${i}`);

                    eidFace.siblings('.invalid-feedback').remove();

                    if ($(this).val().trim().length > 0 && eidFile && !eidFile.files.length && !eidDocId) {
                        eidFace.after(
                            '<span class="invalid-feedback" style="display:block;">Emirates ID file is required.</span>'
                        );
                    }
                }

                validateAll();
            });

            // ── B2C hardcoded passport field ──
            $(document).on('input', '#b2cDocNumber_2', function() {
                this.value = this.value.toUpperCase();
                this.value = this.value.replace(/[^A-Z0-9]/g, '');

                let ppFile = $('input[name="docsB2C[2][file]"]')[0];
                let ppDocId = $('#b2cDocId_2').val();
                let ppFace = $('#b2cDocFace_2');

                ppFace.siblings('.invalid-feedback').remove();

                if ($(this).val().trim().length > 0 && ppFile && !ppFile.files.length && !ppDocId) {
                    ppFace.after(
                        '<span class="invalid-feedback" style="display:block;">Passport file is required.</span>'
                    );
                }

                validateAll();
            });

            // ── B2C hardcoded Emirates ID field ──
            $(document).on('input', '#b2cDocNumber_1', function() {
                this.value = this.value.replace(/[^0-9-]/g, '');
                let eidFile = $('input[name="docsB2C[1][file]"]')[0];
                let eidDocId = $('#b2cDocId_1').val();
                let eidFace = $('#b2cDocFace_1');
                // alert(eidFile);

                eidFace.siblings('.invalid-feedback').remove();

                if ($(this).val().trim().length > 0 && !eidFile.files.length && !eidDocId) {
                    eidFace.after(
                        '<span class="invalid-feedback" style="display:block;">Emirates ID file is required.</span>'
                    );
                }
                validateAll();
            });

            $(document).on('input', 'input[name="tenant_mobile"], input[name="contact_number"]', function() {
                // alert("test");
                this.value = this.value.replace(/[^0-9]/g, '');
                validateAll();
            });
            // ── B2C file inputs — re-validate when file is chosen ──
            $(document).on('change', 'input[name="docsB2C[1][file]"], input[name="docsB2C[2][file]"]', function() {
                // Clear the file-required error when a file is selected
                let idx = $(this).attr('name') === 'docsB2C[1][file]' ? '1' : '2';
                let face = $('#b2cDocFace_' + idx);
                face.siblings('.invalid-feedback').remove();

                validateAll();
            });

            // ── B2B Owner: file chosen → clear error ──
            $(document).on('change', 'input[name*="emirates_file"], input[name*="passport_file"]', function() {
                const name = $(this).attr('name');
                const match = name.match(/owners\[(\d+)\]\[(\d+)\]/);
                if (match) {
                    const i = match[1];
                    const type = match[2]; // 1=passport, 2=emirates
                    const face = type == 2 ? $(`#eidFace_${i}`) : $(`#ppFace_${i}`);
                    face.siblings('.invalid-feedback').remove();
                }
                validateAll();
            });
            // ── B2B unit checkbox change ──
            $(document).on('change', '.row-checkbox', function() {
                validateAll();
            });
            $(document).on('change', '#tlFile', function() {
                $('#tlFileFace').siblings('.invalid-feedback').remove();
                validateAll();
            });
        });
    </script>

    @include('admin.sales.form-submit-js');
    @include('admin.master.tenants.form-submit-js')
@endsection
