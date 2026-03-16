@extends('admin.layout.admin_master')

@section('custom_css')
    <link rel="stylesheet" href="{{ asset('assets/daterangepicker/daterangepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <style>
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

        .conditional-section {
            display: none;
        }

        .conditional-section.visible {
            display: block;
        }

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
            transition: all 0.2s;
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

        .subunit-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 10px;
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

        .existing-customer-panel {
            display: none;
            margin-bottom: 16px;
        }

        .existing-customer-panel.visible {
            display: block;
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
    </style>
@endsection

@section('content')
    <div class="content-wrapper">

        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>{{ $title }}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('tenant-registration.index') }}">Tenant
                                    Registration</a></li>
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
                            <i class="fas fa-file-contract mr-2"></i>
                            {{ isset($agreement) ? 'Edit Tenancy Agreement' : 'New Tenancy Agreement' }}
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

                        <form
                            action="{{ isset($agreement) ? route('tenant-registration.update', $agreement->id) : route('tenant-registration.store') }}"
                            method="POST" enctype="multipart/form-data" id="tenancyForm">
                            @csrf
                            @isset($agreement)
                                @method('PUT')
                            @endisset

                            {{-- ═══ SECTION 1 — Property Location ═══ --}}
                            <div class="card-section shadow-sm p-4">
                                <p class="section-title"><i class="fas fa-map-marker-alt mr-1"></i> Property Location</p>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Property <span class="text-danger">*</span></label>
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
                                                placeholder="Auto-filled from Makani"
                                                value="{{ isset($agreement) ? $agreement->area->area_name ?? '' : old('area') }}">
                                            <input type="hidden" name="area_id" id="areaName"
                                                value="{{ isset($agreement) ? $agreement->area_id : old('area_id') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Locality</label>
                                            <input type="text" id="localityField" class="form-control" readonly
                                                placeholder="Auto-filled from Makani"
                                                value="{{ isset($agreement) ? $agreement->locality->locality_name ?? '' : old('locality') }}">
                                            <input type="hidden" name="locality_id" id="localityName"
                                                value="{{ isset($agreement) ? $agreement->locality_id : old('locality_id') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- ═══ SECTION 2 — Business Type ═══ --}}
                            <div class="card-section shadow-sm p-4">
                                <p class="section-title"><i class="fas fa-briefcase mr-1"></i> Business Type</p>
                                <div class="btype-toggle">
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

                            {{-- ═══ SECTION 3A — Unit Details (B2C) ═══ --}}
                            <div class="card-section shadow-sm p-4 conditional-section" id="unitSection">
                                <p class="section-title"><i class="fas fa-door-open mr-1"></i> Unit Details</p>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Floor <span class="text-danger">*</span></label>
                                            <select id="floorSelect" name="floor_number" class="form-control"
                                                onchange="onFloorChange(this)">
                                                <option value="">— Select Floor —</option>
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
                                            <small class="text-muted">e.g. P1, P2 — or Full Unit</small>
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

                            {{-- ═══ SECTION 3B — Building Units & Rent (B2B) ═══ --}}
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
                                                <th>Annual Rent (AED)</th>
                                                <th>Monthly Rent (AED)</th>
                                                <th>Subunit Rents</th>
                                            </tr>
                                        </thead>
                                        <tbody id="b2bUnitsBody">
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- ═══ SECTION 4 — Tenant Details ═══ --}}
                            <div class="card-section shadow-sm p-4 conditional-section" id="tenantSection">
                                <p class="section-title"><i class="fas fa-user mr-1"></i> Tenant Details</p>

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

                                    <div class="existing-customer-panel" id="existingCustomerPanel"
                                        style="display:none;">
                                        <div class="form-group mb-0">
                                            <label style="font-size:13px; font-weight:600; color:#495057;">
                                                <i class="fas fa-building mr-1 text-muted"></i> Select Company
                                            </label>
                                            <select id="existingCustomerSelect" class="form-control select2"
                                                onchange="onExistingCustomerSelected(this)" style="width:100%;">
                                                <option value="">— Search or select a company —</option>
                                            </select>
                                        </div>
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
                                <div id="newCustomerForm">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label id="tenantNameLabel">
                                                    Name as per Emirates ID / Passport <span class="text-danger">*</span>
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
                                                <input type="text" name="tenant_mobile" id="tenantMobile"
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

                                        {{-- Address --}}
                                        <div id="addressFields" class="col-md-12">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Address Line 1</label>
                                                        <input type="text" name="tenant_address" class="form-control"
                                                            value="{{ isset($tenant) ? $tenant->tenant_address : old('tenant_address') }}"
                                                            placeholder="Building / Flat No., Street">
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
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Office Landline / Mobile <span
                                                                class="text-danger">*</span></label>
                                                        <input type="text" name="office_landline" id="officeLandline"
                                                            class="form-control"
                                                            value="{{ isset($tenant) ? $tenant->office_landline : old('office_landline') }}"
                                                            placeholder="+971 4 000 0000">
                                                    </div>
                                                </div>
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

                                        {{-- B2C Contact --}}
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
                                                        <input type="text" name="contact_number" id="contactNumberB2C"
                                                            class="form-control"
                                                            value="{{ isset($tenant) ? $tenant->contact_number : old('contact_number') }}"
                                                            placeholder="+971 50 000 0000">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Emergency Contact Email</label>
                                                        <input type="email" name="contact_email" id="contactEmailB2C"
                                                            class="form-control"
                                                            value="{{ isset($tenant) ? $tenant->contact_email : old('contact_email') }}"
                                                            placeholder="contact@email.com">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Documents --}}
                                <hr class="mt-3 mb-3">
                                <p class="section-title tenant-documents" id="tenant-documents-head">
                                    <i class="fas fa-file-alt mr-1"></i> Tenant Documents
                                </p>

                                <div id="b2cDocNotice" class="doc-notice" style="display:none;">
                                    <i class="fas fa-info-circle text-info mt-1" style="flex-shrink:0;"></i>
                                    <span>
                                        <strong class="text-dark">B2C Requirement:</strong>
                                        At least one <strong class="text-info">Emirates ID</strong> or
                                        <strong class="text-info">Passport</strong> must be uploaded.
                                    </span>
                                </div>
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
                                        Please upload all mandatory documents.
                                    </div>

                                    <div class="d-flex align-items-center gap-3 mb-3 p-3 bg-light rounded border">
                                        <i class="fas fa-users text-muted"></i>
                                        <label class="mb-0 font-weight-600" style="font-size:14px;font-weight:600;">
                                            Number of Owners / Authorised Signatories
                                        </label>
                                        <select id="ownerCountSelect" name="no_of_owners" class="form-control"
                                            style="width:90px;" onchange="buildOwnerDocBlocks()">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <option value="{{ $i }}"
                                                    {{ isset($ownerDocs) && $ownerDocs->count() == $i ? 'selected' : '' }}>
                                                    {{ $i }}
                                                </option>
                                            @endfor
                                        </select>
                                    </div>

                                    <div id="ownerDocBlocks"></div>

                                    {{-- Trade License --}}
                                    <div class="trade-license-block">
                                        <div class="owner-block-title">
                                            <i class="fas fa-id-card"></i>
                                            Company Trade License
                                            <span class="badge-mandatory">Mandatory</span>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Trade License Number <span class="text-danger">*</span></label>
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
                                                            <span id="tlFileLabel">
                                                                @isset($tradeLicense)
                                                                    {{ $tradeLicense->original_document_name ?? 'Click to replace file' }}
                                                                @else
                                                                    Click to upload or drag &amp; drop
                                                                @endisset
                                                            </span>
                                                        </div>
                                                    </div>
                                                    @isset($tradeLicense)
                                                        @if ($tradeLicense->original_document_path)
                                                            <small>
                                                                <a href="{{ asset('storage/' . $tradeLicense->original_document_path) }}"
                                                                    target="_blank">
                                                                    <i class="fas fa-eye mr-1"></i> View current file
                                                                </a>
                                                            </small>
                                                        @endif
                                                    @endisset
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Issued Date <span class="text-danger">*</span></label>
                                                    <div class="input-group date" id="tradeLicenseIssuedDate"
                                                        data-target-input="nearest">
                                                        <input type="text" class="form-control datetimepicker-input"
                                                            name="tl_issued" id="trade_license_issued"
                                                            data-target="#tradeLicenseIssuedDate" placeholder="dd-mm-YYYY"
                                                            value="{{ isset($tradeLicense) && $tradeLicense->issued_date ? \Carbon\Carbon::parse($tradeLicense->issued_date)->format('d-m-Y') : old('tl_issued') }}">
                                                        <div class="input-group-append"
                                                            data-target="#tradeLicenseIssuedDate"
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
                                                    <div class="input-group date" id="tradeLicenseExpiryDate"
                                                        data-target-input="nearest">
                                                        <input type="text" class="form-control datetimepicker-input"
                                                            name="tl_expiry" id="trade_license_expiry"
                                                            data-target="#tradeLicenseExpiryDate" placeholder="dd-mm-YYYY"
                                                            onchange="checkExpiry('trade_license_expiry','tlExpiryWarn')"
                                                            value="{{ isset($tradeLicense) && $tradeLicense->expiry_date ? \Carbon\Carbon::parse($tradeLicense->expiry_date)->format('d-m-Y') : old('tl_expiry') }}">
                                                        <div class="input-group-append"
                                                            data-target="#tradeLicenseExpiryDate"
                                                            data-toggle="datetimepicker">
                                                            <div class="input-group-text"><i class="fa fa-calendar"></i>
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
                                <div id="b2cDocSection" style="display:none;">
                                    <div id="docRowsB2C"></div>
                                    <button type="button" class="btn-add-doc" onclick="addDocRowB2C()">
                                        <i class="fas fa-plus"></i> Add Another Document
                                    </button>
                                </div>
                            </div>

                            {{-- ═══ SECTION 5 — Agreement Duration ═══ --}}
                            <div class="card-section shadow-sm p-4 conditional-section" id="datesSection">
                                <p class="section-title"><i class="fas fa-calendar-alt mr-1"></i> Agreement Duration</p>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Start Date <span class="text-danger">*</span></label>
                                            <div class="input-group date" id="start_date" data-target-input="nearest">
                                                <input type="text" class="form-control datetimepicker-input"
                                                    name="start_date" id="startDate" data-target="#start_date"
                                                    placeholder="dd-mm-YYYY" required
                                                    value="{{ isset($agreement) && $agreement->start_date ? \Carbon\Carbon::parse($agreement->start_date)->format('d-m-Y') : old('start_date') }}">
                                                <div class="input-group-append" data-target="#start_date"
                                                    data-toggle="datetimepicker">
                                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                </div>
                                            </div>
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
                                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                </div>
                                            </div>
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

                            {{-- ═══ SECTION 6 — Rent Details (B2C) ═══ --}}
                            <div class="card-section shadow-sm p-4 conditional-section" id="rentSection">
                                <p class="section-title"><i class="fas fa-money-bill-wave mr-1"></i> Rent Details</p>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Rent Per Month <span class="text-danger">*</span> <small
                                                    class="text-muted font-weight-normal">(AED)</small></label>
                                            <input type="number" name="rent_per_month" id="rentMonth"
                                                class="form-control @error('rent_per_month') is-invalid @enderror"
                                                value="{{ old('rent_per_month') }}" placeholder="0.00" step="0.01"
                                                oninput="calculateAnnual()">
                                            @error('rent_per_month')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Rent Per Annum <span class="text-danger">*</span> <small
                                                    class="text-muted font-weight-normal">(AED)</small></label>
                                            <input type="number" name="rent_per_annum" id="rentAnnum"
                                                class="form-control @error('rent_per_annum') is-invalid @enderror"
                                                value="{{ old('rent_per_annum') }}" placeholder="0.00" step="0.01"
                                                oninput="calculateMonthly()">
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

                            {{-- ═══ ACTION BUTTONS ═══ --}}
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <a href="{{ route('tenant-registration.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left mr-1"></i> Back
                                </a>
                                <button type="submit" class="btn signinbtn px-4" id="submitBtn">
                                    <i class="fas fa-save mr-1"></i>
                                    {{ isset($agreement) ? 'Update Agreement' : 'Save Agreement' }}
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection


@section('custom_js')
    <script src="{{ asset('assets/moment/moment.min.js') }}"></script>
    <script src="{{ asset('assets/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>
    <script src="{{ asset('assets/daterangepicker/daterangepicker.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

    <script>
        // ── Date pickers ──
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

        $('#tradeLicenseExpiryDate').on('change.datetimepicker', function() {
            checkExpiry('trade_license_expiry', 'tlExpiryWarn');
        });
    </script>

    <script>
        const existingCustomers = @json($formData['existingCustomers'] ?? []);
        const propertyUnitMap = @json($formData['propertyUnitMap'] ?? []);
        let activeUnitData = {};
        let selectedCustomerId = null;

        // ── Property / Makani change ──
        function onMakaniChange(select) {
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
            if (!rawData || Array.isArray(rawData)) return;

            window.activePropertyData = rawData;

            const isB2C = document.getElementById('typeB2C').checked;
            const isB2B = document.getElementById('typeB2B').checked;

            if (isB2C) {
                activeUnitData = rawData.b2c || {};
                populateFloors();
            } else if (isB2B) {
                activeUnitData = rawData.b2b || {};
                buildB2BTable();
            }
        }

        function populateFloors() {
            const floorSelect = document.getElementById('floorSelect');
            const helper = document.getElementById('floorHelper');
            resetSelect(floorSelect, '— Select Floor —');
            floorSelect.disabled = true;
            helper.textContent = '';

            const floors = Object.keys(activeUnitData);
            if (floors.length === 0) {
                helper.textContent = 'No vacant units available for this property.';
                return;
            }

            floors.sort((a, b) => {
                if (a === 'G') return -1;
                if (b === 'G') return 1;
                return parseInt(a) - parseInt(b);
            });

            floors.forEach(f => floorSelect.add(new Option(`Floor ${f}`, f)));
            floorSelect.disabled = false;
            helper.textContent = `${floors.length} floor${floors.length > 1 ? 's' : ''} available`;
        }

        function onFloorChange(select) {
            const floor = select.value;
            const typeSelect = document.getElementById('unitTypeSelect');
            const unitSelect = document.getElementById('unitNumberSelect');
            const subSelect = document.getElementById('subunitSelect');

            resetSelect(typeSelect, '— Select Unit Type —');
            resetSelect(unitSelect, '— Select Unit Type First —');
            resetSelect(subSelect, '— Select Unit First —');
            typeSelect.disabled = unitSelect.disabled = subSelect.disabled = true;
            hideSummary();

            if (!floor || !activeUnitData[floor]) return;
            Object.entries(activeUnitData[floor]).forEach(([typeId, typeData]) => {
                typeSelect.add(new Option(typeData.label, typeId));
            });
            typeSelect.disabled = false;
            const count = Object.keys(activeUnitData[floor]).length;
            document.getElementById('unitTypeHelper').textContent =
                `${count} unit type${count > 1 ? 's' : ''} on this floor`;
        }

        function onUnitTypeChange(select) {
            const floor = document.getElementById('floorSelect').value;
            const typeId = select.value;
            const unitSelect = document.getElementById('unitNumberSelect');
            const subSelect = document.getElementById('subunitSelect');

            resetSelect(unitSelect, '— Select Unit Number —');
            resetSelect(subSelect, '— Select Unit First —');
            unitSelect.disabled = subSelect.disabled = true;
            hideSummary();

            if (!typeId || !activeUnitData[floor]?.[typeId]) return;
            activeUnitData[floor][typeId].units.forEach(unit => {
                unitSelect.add(new Option(unit.unit_number, unit.id));
            });
            unitSelect.disabled = false;
        }

        function onUnitNumberChange(select) {
            const floor = document.getElementById('floorSelect').value;
            const typeId = document.getElementById('unitTypeSelect').value;
            const unitId = parseInt(select.value);
            const subSelect = document.getElementById('subunitSelect');

            resetSelect(subSelect, '— Select Subunit —');
            subSelect.disabled = true;
            hideSummary();

            if (!unitId || !activeUnitData[floor]?.[typeId]?.units) return;
            const unitObj = activeUnitData[floor][typeId].units.find(u => u.id === unitId);
            if (!unitObj) return;

            if (unitObj.subunits.length === 0) {
                subSelect.add(new Option('Full Unit', 'full'));
                subSelect.value = 'full';
            } else {
                unitObj.subunits.forEach(sub => subSelect.add(new Option(sub.label, sub.id)));
                if (unitObj.subunits.length === 1) subSelect.value = unitObj.subunits[0].id;
            }
            subSelect.disabled = false;
            updateUnitSummary();
        }

        // ── Business Type change ──
        function onBusinessTypeChange() {
            const isB2B = document.getElementById('typeB2B').checked;
            const isB2C = document.getElementById('typeB2C').checked;

            if (window.activePropertyData) {
                activeUnitData = isB2C ? (window.activePropertyData.b2c || {}) : (window.activePropertyData.b2b || {});
            } else {
                activeUnitData = {};
            }

            toggleVisible('unitSection', isB2C);
            toggleVisible('b2bUnitsSection', isB2B);
            toggleVisible('tenantSection', true);
            toggleVisible('datesSection', true);
            toggleVisible('rentSection', isB2C);

            document.getElementById('existingCustomerToggleWrap').style.display = isB2B ? '' : 'none';
            document.getElementById('existingCustomerCheck').checked = false;
            document.getElementById('existingCustomerPanel').style.display = 'none';
            document.getElementById('existingDivider').style.display = 'none';
            clearSelectedCustomer();
            document.getElementById('newCustomerForm').style.display = '';

            document.getElementById('b2cDocNotice').style.display = isB2C ? 'flex' : 'none';
            document.getElementById('b2cDocError').classList.remove('visible');
            document.getElementById('b2bDocSection').style.display = isB2B ? '' : 'none';
            document.getElementById('b2cDocSection').style.display = isB2C ? '' : 'none';

            document.getElementById('b2bContactFields').style.display = isB2B ? '' : 'none';
            document.getElementById('b2cContactFields').style.display = isB2C ? '' : 'none';

            document.querySelectorAll('#b2cContactFields input').forEach(el => {
                el.required = isB2C;
                el.disabled = !isB2C;
            });
            document.querySelectorAll('#b2bContactFields input').forEach(el => {
                el.required = isB2B;
                el.disabled = !isB2B;
            });

            const lbl = document.getElementById('tenantNameLabel');
            if (isB2B) {
                lbl.innerHTML = 'Name as per Trade License <span class="text-danger">*</span>';
                document.getElementById('tenantPassportName').placeholder = 'Exactly as shown on trade license';
            } else {
                lbl.innerHTML = 'Name as per Emirates ID / Passport <span class="text-danger">*</span>';
                document.getElementById('tenantPassportName').placeholder = 'Exactly as shown on document';
            }

            document.getElementById('endDateField').style.display = isB2C ? 'none' : '';
            if (isB2C) {
                document.getElementById('endDate').value = '';
                document.getElementById('durationPill').style.display = 'none';
            }

            if (isB2B) {
                document.getElementById('ownerDocBlocks').innerHTML = '';
                buildOwnerDocBlocks();
            }
            if (isB2C) {
                document.getElementById('docRowsB2C').innerHTML = '';
                docCountB2C = 0;
                addDocRowB2C();
            }

            if (isB2B) buildB2BTable();
            if (isB2C) {
                resetUnitCascade();
                populateFloors();
            }
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
                ['subunitSelect', '— Select Unit First —']
            ]
            .forEach(([id, placeholder]) => {
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

        function resetSelect(el, placeholder) {
            el.innerHTML = '';
            el.add(new Option(placeholder, ''));
        }

        function hideSummary() {
            document.getElementById('unitSummary').style.display = 'none';
        }

        // ── B2B Units Table ──
        function buildB2BTable() {
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
                    typeData.units.forEach(unit => {
                        rowIdx++;
                        const key = `${floor}_${typeId}_${unit.id}`;
                        const subs = unit.subunits;
                        const hasMulti = subs.length > 0;

                        const tr = document.createElement('tr');
                        tr.id = `b2bRow_${rowIdx}`;
                        tr.dataset.unitKey = key;
                        tr.innerHTML = `
                            <td><input type="checkbox" class="row-checkbox" id="selRow_${rowIdx}" value="${rowIdx}"></td>
                            <td><span class="floor-label">${floorLabel}</span></td>
                            <td><span class="type-badge">${typeData.label}</span></td>
                            <td style="font-weight:600;">${unit.unit_number}</td>
                            <td><input type="number" placeholder="0.00" id="annual_${rowIdx}"
                                name="unit_rent[${key}][annual]"></td>
                            <td><input type="number" placeholder="0.00" id="monthly_${rowIdx}"
                                name="unit_rent[${key}][monthly]"></td>
                            <td>${hasMulti
                                ? `<button type="button" class="btn-subunit" id="subBtn_${rowIdx}" onclick="toggleExpandRow(${rowIdx})">
                                               <i class="fas fa-chevron-down" id="subBtnIcon_${rowIdx}"></i> Subunit Rents
                                           </button>`
                                : '<span class="text-muted small">—</span>'}
                            </td>`;
                        tbody.appendChild(tr);

                        if (hasMulti) {
                            const expandTr = document.createElement('tr');
                            expandTr.className = 'expand-row';
                            expandTr.id = `expandRow_${rowIdx}`;
                            const cards = subs.map(sub => {
                                const nk = `${key}_${sub.id}`;
                                return `<div class="subunit-rent-card">
                                    <div class="subunit-rent-card-label">${sub.label}</div>
                                    <div class="form-group mb-0">
                                        <label style="font-size:11px;color:#6c757d;font-weight:600;">Monthly Rent (AED)</label>
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

        function onSubInput(rowIdx, subId) {
            const val = parseFloat(document.getElementById(`smonthly_${rowIdx}_${subId}`)?.value);
            const chip = document.getElementById(`chip_${rowIdx}_${subId}`);
            if (chip) chip.classList.toggle('has-rent', !isNaN(val) && val > 0);
        }

        // ── Owner doc blocks ──
        function buildOwnerDocBlocks() {
            const count = parseInt(document.getElementById('ownerCountSelect').value) || 1;
            const container = document.getElementById('ownerDocBlocks');
            container.innerHTML = '';
            for (let i = 1; i <= count; i++) {
                const block = document.createElement('div');
                block.className = 'owner-block';
                block.innerHTML = `
                <div class="owner-block-title">
                    <span class="owner-num-badge">${i}</span>
                    Owner ${i} Documents
                    <span class="badge-mandatory">Mandatory</span>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Emirates ID Number <span class="text-danger">*</span></label>
                            <input type="text" name="owners[${i}][2][emirates_id]"
                                class="form-control emirates-id" placeholder="784-XXXX-XXXXXXX-X"
                                value="${getOwnerDocValue(${i}, 2, 'emirates_id')}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Emirates ID Upload <span class="text-danger">*</span></label>
                            <div class="file-upload-wrap">
                                <input type="file" name="owners[${i}][2][emirates_file]" accept="image/*,.pdf"
                                    onchange="onFileChange(this,'eidFace_${i}','eidLabel_${i}')">
                                <div class="file-upload-face" id="eidFace_${i}">
                                    <i class="fas fa-upload"></i>
                                    <span id="eidLabel_${i}">Click to upload or drag &amp; drop</span>
                                </div>
                            </div>
                            ${getOwnerDocViewLink(${i}, 2)}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Emirates ID Issued Date</label>
                            <div class="input-group date" id="emiratesIssued_${i}" data-target-input="nearest">
                                <input type="text" class="form-control datetimepicker-input"
                                    name="owners[${i}][2][emirates_issued]"
                                    data-target="#emiratesIssued_${i}" placeholder="DD-MM-YYYY"
                                    value="${getOwnerDocValue(${i}, 2, 'issued_date')}">
                                <div class="input-group-append" data-target="#emiratesIssued_${i}" data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Emirates ID Expiry Date</label>
                            <div class="input-group date" id="emiratesExpiry_${i}" data-target-input="nearest">
                                <input type="text" class="form-control datetimepicker-input"
                                    name="owners[${i}][2][emirates_expiry]" id="eidExpiry_${i}"
                                    data-target="#emiratesExpiry_${i}" placeholder="DD-MM-YYYY"
                                    value="${getOwnerDocValue(${i}, 2, 'expiry_date')}"
                                    onchange="checkExpiry('eidExpiry_${i}','eidWarn_${i}')">
                                <div class="input-group-append" data-target="#emiratesExpiry_${i}" data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                </div>
                            </div>
                            <span class="expiry-warn" id="eidWarn_${i}"></span>
                        </div>
                    </div>
                    <div class="col-md-12"><hr class="my-2"></div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Passport Number <span class="text-danger">*</span></label>
                            <input type="text" name="owners[${i}][1][passport_number]"
                                class="form-control passport-number" placeholder="e.g. A12345678"
                                value="${getOwnerDocValue(${i}, 1, 'passport_number')}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Passport Upload <span class="text-danger">*</span></label>
                            <div class="file-upload-wrap">
                                <input type="file" name="owners[${i}][1][passport_file]" accept="image/*,.pdf"
                                    onchange="onFileChange(this,'ppFace_${i}','ppLabel_${i}')">
                                <div class="file-upload-face" id="ppFace_${i}">
                                    <i class="fas fa-upload"></i>
                                    <span id="ppLabel_${i}">Click to upload or drag &amp; drop</span>
                                </div>
                            </div>
                            ${getOwnerDocViewLink(${i}, 1)}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Passport Issued Date</label>
                            <div class="input-group date" id="passportIssued_${i}" data-target-input="nearest">
                                <input type="text" class="form-control datetimepicker-input"
                                    name="owners[${i}][1][passport_issued]"
                                    data-target="#passportIssued_${i}" placeholder="DD-MM-YYYY"
                                    value="${getOwnerDocValue(${i}, 1, 'issued_date')}">
                                <div class="input-group-append" data-target="#passportIssued_${i}" data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Passport Expiry Date</label>
                            <div class="input-group date" id="passportExpiry_${i}" data-target-input="nearest">
                                <input type="text" class="form-control datetimepicker-input"
                                    name="owners[${i}][1][passport_expiry]" id="ppExpiry_${i}"
                                    data-target="#passportExpiry_${i}" placeholder="DD-MM-YYYY"
                                    value="${getOwnerDocValue(${i}, 1, 'expiry_date')}"
                                    onchange="checkExpiry('ppExpiry_${i}','ppWarn_${i}')">
                                <div class="input-group-append" data-target="#passportExpiry_${i}" data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                </div>
                            </div>
                            <span class="expiry-warn" id="ppWarn_${i}"></span>
                        </div>
                    </div>
                </div>`;
                container.appendChild(block);
                initOwnerDatePickers(i);
            }
        }

        // ── Owner doc helpers for edit pre-fill ──
        const existingOwnerDocs = @json($existingOwnerDocsJson ?? []);

        function getOwnerDocValue(ownerIndex, docType, field) {
            const ownerGroup = existingOwnerDocs[ownerIndex];
            if (!ownerGroup) return '';
            const doc = ownerGroup.find(d => d.document_type == docType);
            if (!doc) return '';
            if (field === 'emirates_id' || field === 'passport_number') return doc.document_number || '';
            return doc[field] || '';
        }

        function getOwnerDocViewLink(ownerIndex, docType) {
            const ownerGroup = existingOwnerDocs[ownerIndex];
            if (!ownerGroup) return '';
            const doc = ownerGroup.find(d => d.document_type == docType);
            if (!doc || !doc.view_url) return '';
            return `<small><a href="${doc.view_url}" target="_blank"><i class="fas fa-eye mr-1"></i> View current file</a></small>`;
        }

        // ── B2C doc rows ──
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
        let docCountB2C = 0;

        function addDocRowB2C() {
            docCountB2C++;
            const idx = docCountB2C;
            const container = document.getElementById('docRowsB2C');
            const row = document.createElement('div');
            row.className = 'doc-row';
            row.id = `docRowB2C_${idx}`;
            row.innerHTML = `
            <div class="doc-row-header">
                <span class="doc-row-title">Document ${idx}</span>
                ${idx > 1 ? `<button type="button" class="btn-remove-doc"
                            onclick="document.getElementById('docRowB2C_${idx}').remove()">
                            <i class="fas fa-times"></i> Remove</button>` : ''}
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Document Type <span class="text-danger">*</span></label>
                        <select name="docsB2C[${idx}][type]" class="form-control">
                            ${docTypesB2C.map(d=>`<option value="${d.value}">${d.label}</option>`).join('')}
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Document Number</label>
                        <input type="text" name="docsB2C[${idx}][number]" class="form-control" placeholder="e.g. reference number">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Upload Document <small class="text-muted font-weight-normal">(JPG, PNG, PDF)</small></label>
                        <div class="file-upload-wrap">
                            <input type="file" name="docsB2C[${idx}][file]" accept="image/*,.pdf"
                                onchange="onFileChange(this,'b2cDocFace_${idx}','b2cDocLabel_${idx}')">
                            <div class="file-upload-face" id="b2cDocFace_${idx}">
                                <i class="fas fa-upload"></i>
                                <span id="b2cDocLabel_${idx}">Click to upload or drag &amp; drop</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Issued Date</label>
                        <input type="date" name="docsB2C[${idx}][issued_date]" class="form-control">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Expiry Date</label>
                        <input type="date" name="docsB2C[${idx}][expiry_date]"
                            id="b2cDocExpiry_${idx}" class="form-control"
                            onchange="checkExpiry('b2cDocExpiry_${idx}','b2cDocWarn_${idx}')">
                        <span class="expiry-warn" id="b2cDocWarn_${idx}"></span>
                    </div>
                </div>
            </div>`;
            container.appendChild(row);
        }

        // ── Existing customer ──
        function onExistingCustomerToggle() {
            const checked = document.getElementById('existingCustomerCheck').checked;
            const panel = document.getElementById('existingCustomerPanel');
            const newForm = document.getElementById('newCustomerForm');

            if (checked) {
                populateExistingCustomerSelect();
                panel.style.display = 'block';
                newForm.style.display = 'none';
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
            if (select.options.length > 1) return;
            existingCustomers.forEach(c => {
                const opt = document.createElement('option');
                opt.value = c.id;
                opt.textContent = `${c.code}  —  ${c.name}`;
                select.appendChild(opt);
            });
            if (typeof $ !== 'undefined' && $.fn.select2) {
                $('#existingCustomerSelect').select2({
                        placeholder: '— Search or select a company —',
                        allowClear: true,
                        width: '100%'
                    })
                    .on('change', function() {
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
                documentshead.style.display = 'none';
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
                documentshead.style.display = '';
                const hiddenInput = document.getElementById('hiddenExistingCustomerId');
                if (hiddenInput) hiddenInput.value = '';
            }
        }

        function clearSelectedCustomer() {
            const select = document.getElementById('existingCustomerSelect');
            const newForm = document.getElementById('newCustomerForm');
            const clearWrap = document.getElementById('clearExistingWrap');
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

        // ── Dates ──
        function onDateChange() {
            const s = document.getElementById('startDate').value;
            const e = document.getElementById('endDate').value;
            const pill = document.getElementById('durationPill');
            if (s && e) {
                const sp = s.split('-'),
                    ep = e.split('-');
                const start = new Date(`${sp[2]}-${sp[1]}-${sp[0]}`);
                const end = new Date(`${ep[2]}-${ep[1]}-${ep[0]}`);
                if (end > start) {
                    const months = Math.round((end - start) / (1000 * 60 * 60 * 24 * 30.44));
                    const years = Math.floor(months / 12);
                    const rem = months % 12;
                    let txt = '';
                    if (years > 0) txt += `${years} year${years>1?'s':''}`;
                    if (rem > 0) txt += (txt ? ', ' : '') + `${rem} month${rem>1?'s':''}`;
                    document.getElementById('durationText').textContent = `Agreement duration: ${txt || '< 1 month'}`;
                    pill.style.display = '';
                    return;
                }
            }
            pill.style.display = 'none';
        }

        $('#start_date').on('change.datetimepicker', onDateChange);
        $('#end_date').on('change.datetimepicker', onDateChange);

        // ── Rent calculation ──
        function calculateAnnual() {
            const v = parseFloat(document.getElementById('rentMonth').value);
            document.getElementById('displayMonth').innerHTML = (!isNaN(v) && v > 0) ? v : '—<span>AED</span>';
        }

        function calculateMonthly() {
            const v = parseFloat(document.getElementById('rentAnnum').value);
            document.getElementById('displayAnnum').innerHTML = (!isNaN(v) && v > 0) ? v : '—<span>AED</span>';
        }

        // ── File upload ──
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

        // ── Expiry check ──
        function checkExpiry(expiryId, warnId) {
            const input = document.getElementById(expiryId);
            const warn = document.getElementById(warnId);
            if (!input?.value) {
                warn.className = 'expiry-warn';
                return;
            }
            const parts = input.value.split('-');
            if (parts.length !== 3) return;
            const expiryDate = new Date(`${parts[2]}-${parts[1]}-${parts[0]}`);
            if (isNaN(expiryDate)) return;
            const diff = Math.ceil((expiryDate - new Date().setHours(0, 0, 0, 0)) / 86400000);
            if (diff < 0) {
                warn.textContent = `⚠ Expired ${Math.abs(diff)} day${Math.abs(diff)>1?'s':''} ago`;
                warn.className = 'expiry-warn show expired';
                input.classList.add('is-invalid');
            } else if (diff <= 30) {
                warn.textContent = `⚠ Expires in ${diff} day${diff>1?'s':''}`;
                warn.className = 'expiry-warn show soon';
                input.style.borderColor = '#ffc107';
            } else {
                warn.textContent = '';
                warn.className = 'expiry-warn';
                input.classList.remove('is-invalid');
                input.style.borderColor = '';
            }
        }

        // ── DOMContentLoaded: init for create OR edit ──
        window.addEventListener('DOMContentLoaded', function() {
            @isset($agreement)
                // 1. Trigger property change to auto-fill area/locality and load unit map
                const propertySelect = document.getElementById('makaniNumber');
                if (propertySelect.value) {
                    propertySelect.dispatchEvent(new Event('change'));
                }

                // 2. Check business type radio & trigger all sections
                const btypeId = '{{ $agreement->business_type == 1 ? 'typeB2B' : 'typeB2C' }}';
                const btypeInput = document.getElementById(btypeId);
                if (btypeInput) {
                    btypeInput.checked = true;
                    onBusinessTypeChange();
                }

                // 3. Pre-fill unit rents after table is built
                setTimeout(function() {
                    const existingUnits = @json($existingUnits ?? []);

                    existingUnits.forEach(unit => {
                        // Find annual input by name
                        const annualInput = document.querySelector(
                            `input[name="unit_rent[${unit.key}][annual]"]`);
                        if (!annualInput) return;

                        // Extract rowIdx from input id (annual_X)
                        const rowIdx = annualInput.id.replace('annual_', '');

                        // Check the row checkbox
                        const rowCheckbox = document.getElementById(`selRow_${rowIdx}`);
                        if (rowCheckbox) rowCheckbox.checked = true;

                        // Fill annual & monthly
                        annualInput.value = unit.annual ?? '';
                        const monthlyInput = document.getElementById(`monthly_${rowIdx}`);
                        if (monthlyInput) monthlyInput.value = unit.monthly ?? '';

                        // Fill subunit rents
                        if (unit.subunit_rents && unit.subunit_rents.length) {
                            // Open the expand panel
                            const expandInner = document.getElementById(`expandInner_${rowIdx}`);
                            if (expandInner && !expandInner.classList.contains('open')) {
                                toggleExpandRow(rowIdx);
                            }
                            unit.subunit_rents.forEach(sub => {
                                const subInput = document.querySelector(
                                    `input[name="subunit_rent[${unit.key}_${sub.id}][monthly]"]`
                                );
                                if (subInput) subInput.value = sub.monthly ?? '';
                            });
                        }
                    });
                }, 400);
            @elseif (old('business_type'))
                onBusinessTypeChange();
            @endisset
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

            $('#emiratesExpiry_' + ownerIndex).on('change.datetimepicker', function() {
                checkExpiry('eidExpiry_' + ownerIndex, 'eidWarn_' + ownerIndex);
            });
            $('#passportExpiry_' + ownerIndex).on('change.datetimepicker', function() {
                checkExpiry('ppExpiry_' + ownerIndex, 'ppWarn_' + ownerIndex);
            });
        }
    </script>

    @include('admin.sales.form-submit-js')
@endsection
