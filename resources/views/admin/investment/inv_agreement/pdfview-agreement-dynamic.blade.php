<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>REAL ESTATE | CRM</title>

    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="{{ asset('assets/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <link rel="stylesheet" href="{{ asset('assets/toastr/toastr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/adminlte.css') }}">
    <link rel="stylesheet" href="{{ asset('css/main.css') }}?v=3">
    <link rel="stylesheet" href="{{ asset('assets/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/adminlte.min.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&display=swap" rel="stylesheet">

    <style>
        #file-print-area {

            /* ── SCREEN VIEW ── */
            .new-page {
                position: relative;
                width: 210mm;
                min-height: 297mm;
                margin: 0 auto 40px auto;
                background-color: #fff;
                /* background-image: url("{{ $data['letterHead'] }}"); */
                background-size: 210mm 297mm;
                background-repeat: no-repeat;
                background-position: top left;
                font-family: 'Times New Roman', Times, serif;
                font-size: 12px;
                box-shadow: 0 4px 24px #aaa;
                box-sizing: border-box;
                overflow: hidden;
            }

            .new-page.annexure-page {
                background-image: none !important;
            }

            /*
         * Page 1 — top: 29mm clears your letterhead header
         *           bottom: 48mm clears your letterhead footer
         */
            .file-content {
                position: relative;
                padding: 29mm 16mm 48mm 16mm;
                box-sizing: border-box;
            }

            /*
         * Pages 2+ — your letterhead has a slightly taller header zone on continuation pages
         *             top: 33mm (was .page-top in your original code)
         */
            .file-content.page-subsequent {
                padding-top: 33mm;
            }

            .arabic {
                direction: rtl;
                text-align: right;
                padding-right: 3px;
                unicode-bidi: embed;
                font-family: amiri;
            }

            .customsign {
                margin-bottom: 120px !important;
            }

            .customsignInvestor {
                margin-bottom: 100px !important;
            }

            .ltr-number {
                direction: ltr;
                unicode-bidi: isolate;
                display: inline-block;
                text-align: left;
            }

            .english {
                direction: ltr;
                padding-left: 3px;
                text-align: left;
                font-family: "Times New Roman";
            }

            .text-lg {
                font-size: 12pt !important;
                font-weight: 700 !important;
            }

            .text-md {
                font-size: 8.5pt !important;
                font-weight: 700 !important;
                margin-top: 4px;
            }

            .text-sm {
                font-size: 8pt !important;
            }

            .mt-15 {
                padding-top: 15px;
            }

            strong {
                font-weight: 700 !important;
            }

            /* p {
                margin: 4px;
            } */

            .marginClass {
                margin: 4px;
            }

            .underline-date {
                text-decoration: underline;
                text-underline-offset: 3px;
            }

            #file-print-area .signature-table {
                width: 100% !important;
                table-layout: fixed !important;
                border-collapse: collapse !important;
            }

            #file-print-area .signature-table td {
                width: 50% !important;
                vertical-align: top !important;
            }
        }

        /* ── SIGNATURE STAMP ── */
        .signature-stamp {
            position: absolute;
            bottom: 14mm;
            /* tune: distance from page bottom */
            left: 16mm;
            /* tune: left side for investor signature */
            width: 40mm;
            height: 16mm;
            z-index: 10;
            pointer-events: none;
        }

        .signature-stamp img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            object-position: left bottom;
        }

        .signature-label {
            position: absolute;
            bottom: 10mm;
            left: 16mm;
            font-size: 6.5pt;
            color: #444;
            font-family: 'Times New Roman', serif;
            width: 40mm;
            text-align: left;
        }

        /* Modal overlay */
        .sig-modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.55);
            z-index: 9999;
            align-items: center;
            justify-content: center;
        }

        .sig-modal-overlay.active {
            display: flex;
        }

        .sig-modal {
            background: #fff;
            border-radius: 10px;
            padding: 28px 28px 20px;
            width: 480px;
            max-width: 95vw;
            box-shadow: 0 8px 40px rgba(0, 0, 0, 0.25);
        }

        .sig-modal h5 {
            margin: 0 0 16px;
            font-size: 15px;
            font-weight: 700;
            color: #333;
        }

        .sig-tabs {
            display: flex;
            gap: 8px;
            margin-bottom: 14px;
        }

        .sig-tab {
            flex: 1;
            padding: 8px;
            border: 1.5px solid #ccc;
            border-radius: 6px;
            background: #f8f8f8;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            text-align: center;
            transition: all .2s;
        }

        .sig-tab.active {
            border-color: #007bff;
            background: #e8f0fe;
            color: #007bff;
        }

        .sig-panel {
            display: none;
        }

        .sig-panel.active {
            display: block;
        }

        #sig-canvas {
            border: 1.5px solid #ccc;
            border-radius: 6px;
            background: #fafafa;
            cursor: crosshair;
            display: block;
            width: 100%;
            touch-action: none;
        }

        .sig-canvas-hint {
            font-size: 11px;
            color: #aaa;
            text-align: center;
            margin-top: 5px;
        }

        .sig-upload-area {
            border: 2px dashed #ccc;
            border-radius: 8px;
            padding: 28px 16px;
            text-align: center;
            cursor: pointer;
            transition: border-color .2s;
        }

        .sig-upload-area:hover {
            border-color: #007bff;
        }

        .sig-upload-area input[type="file"] {
            display: none;
        }

        .sig-upload-preview {
            display: none;
            margin-top: 12px;
        }

        .sig-upload-preview img {
            max-height: 80px;
            max-width: 100%;
            border: 1px solid #eee;
            border-radius: 4px;
            padding: 4px;
        }

        .sig-actions {
            display: flex;
            gap: 8px;
            margin-top: 16px;
            justify-content: flex-end;
        }

        .sig-btn {
            padding: 8px 18px;
            border-radius: 6px;
            border: none;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
        }

        .sig-btn-clear {
            background: #f1f1f1;
            color: #555;
        }

        .sig-btn-apply {
            background: #007bff;
            color: #fff;
        }

        .sig-btn-apply:disabled {
            background: #aaa;
            cursor: not-allowed;
        }

        .sig-btn-cancel {
            background: #fff;
            color: #888;
            border: 1px solid #ddd;
        }

        .sig-placeholder-btn {
            position: absolute;
            z-index: 15;
            padding: 4px 10px;
            font-size: 8pt;
            font-weight: 700;
            color: #fff;
            background: #007bff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.25);
        }

        .sig-placeholder-btn:hover {
            background: #0056b3;
        }

        .sig-placed-wrap {
            position: absolute;
            z-index: 15;
            width: 40mm;
            height: 16mm;
            cursor: move;
        }

        .sig-placed-wrap img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            object-position: left bottom;
            pointer-events: none;
        }

        .sig-placed-remove {
            position: absolute;
            top: -8px;
            right: -8px;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: #dc3545;
            color: #fff;
            font-size: 11px;
            line-height: 18px;
            text-align: center;
            cursor: pointer;
            border: none;
            padding: 0;
        }


        /* ── PRINT ── */
        @media print {
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                box-sizing: border-box;
            }

            @page {
                size: A4 portrait;
                margin: 0;
            }

            html,
            body {
                width: 210mm;
                height: 297mm;
                margin: 0 !important;
                padding: 0 !important;
                background: #fff;
            }

            .sig-placeholder-btn,
            .sig-placed-remove {
                display: none !important;
            }

            .no-print {
                display: none !important;
            }

            #file-print-area,
            .content-wrapper,
            .content,
            .container-fluid {
                width: 210mm !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            .new-page {
                width: 210mm !important;
                height: 297mm !important;
                min-height: 297mm !important;
                margin: 0 !important;
                box-shadow: none !important;
                overflow: hidden !important;
                page-break-after: always;
                break-after: page;
                /* ──  background-image: url("{{ asset('images/investment_letter_head.png') }}") !important;── */
                background-size: 210mm 297mm !important;
                background-repeat: no-repeat !important;
                background-position: top left !important;
            }

            .new-page:last-child {
                page-break-after: auto;
                break-after: auto;
            }

            .file-content {
                padding: 34mm 16mm 48mm 16mm !important;
            }

            .file-content.page-subsequent {
                padding-top: 33mm !important;
            }

            table {
                width: 100%;
                border-collapse: collapse;
            }

            td {
                vertical-align: top;
            }

            p {
                margin: 4px !important;

            }
        }
    </style>


    {{-- Investment annexture style --}}

    <style>
        body {
            background: #eef0f2;
            font-family: 'Segoe UI', Arial, sans-serif;
            color: #222;
        }

        .letter-sheet {
            background: #fff;
            max-width: 800px;
            margin: 40px auto;
            padding: 127px 70px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.08);
            border: 1px solid #e0e0e0;
        }

        .letter-title {
            text-align: center;
            font-weight: 700;
            font-size: 1.4rem;
            letter-spacing: 0.5px;
            margin-bottom: 40px;
        }

        .letter-meta {
            margin-bottom: 30px;
        }

        .letter-meta .label {
            font-weight: 600;
        }

        .letter-subject {
            font-weight: 700;
            margin: 25px 0;
        }

        .investment-table th {
            background: #e8eaed;
            font-weight: 600;
            border-top: none;
        }

        .investment-table td,
        .investment-table th {
            vertical-align: middle;
            padding: 12px 16px;
        }

        .investment-table tfoot td {
            font-weight: 700;
            background: #e8eaed;
            border-top: 2px solid #333;
        }

        .signature-block {
            margin-top: 100px;
        }

        .signature-line {
            display: inline-block;
            border-bottom: 1px solid #333;
            min-width: 260px;
            margin-top: 45px;
        }

        /* ANNEXURE SIGNATURE BUTTON */
        /* .annexure-signature-btn {
            transform: translate(-5px, -10px);
        } */

        @media print {
            body {
                background: #fff;
            }

            .letter-sheet {
                box-shadow: none;
                border: none;
                margin: 0;
                padding: 20px;
            }
        }
    </style>
    {{-- <style>
        #file-print-area {

            /* ── SCREEN VIEW ── */
            .new-page {
                position: relative;
                width: 210mm;
                min-height: 297mm;
                margin: 0 auto 40px auto;
                background-color: #fff;
                background-image: url("{{ asset('images/investment_letter_head.png') }}");
                background-size: 210mm 297mm;
                background-repeat: no-repeat;
                background-position: top left;
                font-family: 'Times New Roman', Times, serif;
                font-size: 12px;
                box-shadow: 0 4px 24px #aaa;
                box-sizing: border-box;
                overflow: hidden;
            }

            /*
         * Page 1 — top: 29mm clears your letterhead header
         *           bottom: 48mm clears your letterhead footer
         */
            .file-content {
                position: relative;
                padding: 34mm 16mm 48mm 16mm;
                box-sizing: border-box;
            }

            /*
         * Pages 2+ — your letterhead has a slightly taller header zone on continuation pages
         *             top: 33mm (was .page-top in your original code)
         */
            .file-content.page-subsequent {
                padding-top: 35mm;
            }

            .arabic {
                direction: rtl;
                text-align: right;
                padding-right: 3px;
                unicode-bidi: embed;
                font-family: amiri;
            }

            .english {
                direction: ltr;
                padding-left: 3px;
                text-align: left;
                font-family: "Times New Roman";
            }

            .text-lg {
                font-size: 12pt !important;
                font-weight: 700 !important;
            }

            .text-md {
                font-size: 8.5pt !important;
                font-weight: 700 !important;
                margin-top: 4px;
            }

            .text-medium {
                font-size: 9.5pt !important;
                margin-top: 4px;
            }

            .text-sm {
                font-size: 8pt !important;
            }

            .mt-15 {
                padding-top: 15px;
            }

            strong {
                font-weight: 700 !important;
            }

            p {
                margin: 4px;
            }
        }

        .marginClass {
            margin: 4px !important;
            margin-bottom: 1rem !important;
        }

        /* ── SIGNATURE STAMP ── */
        .signature-stamp {
            position: absolute;
            bottom: 14mm;
            /* tune: distance from page bottom */
            left: 16mm;
            /* tune: left side for investor signature */
            width: 40mm;
            height: 16mm;
            z-index: 10;
            pointer-events: none;
        }

        .signature-stamp img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            object-position: left bottom;
        }

        .signature-label {
            position: absolute;
            bottom: 10mm;
            left: 16mm;
            font-size: 6.5pt;
            color: #444;
            font-family: 'Times New Roman', serif;
            width: 40mm;
            text-align: left;
        }

        /* Modal overlay */
        .sig-modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.55);
            z-index: 9999;
            align-items: center;
            justify-content: center;
        }

        .sig-modal-overlay.active {
            display: flex;
        }

        .sig-modal {
            background: #fff;
            border-radius: 10px;
            padding: 28px 28px 20px;
            width: 480px;
            max-width: 95vw;
            box-shadow: 0 8px 40px rgba(0, 0, 0, 0.25);
        }

        .sig-modal h5 {
            margin: 0 0 16px;
            font-size: 15px;
            font-weight: 700;
            color: #333;
        }

        .sig-tabs {
            display: flex;
            gap: 8px;
            margin-bottom: 14px;
        }

        .sig-tab {
            flex: 1;
            padding: 8px;
            border: 1.5px solid #ccc;
            border-radius: 6px;
            background: #f8f8f8;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            text-align: center;
            transition: all .2s;
        }

        .sig-tab.active {
            border-color: #007bff;
            background: #e8f0fe;
            color: #007bff;
        }

        .sig-panel {
            display: none;
        }

        .sig-panel.active {
            display: block;
        }

        #sig-canvas {
            border: 1.5px solid #ccc;
            border-radius: 6px;
            background: #fafafa;
            cursor: crosshair;
            display: block;
            width: 100%;
            touch-action: none;
        }

        .sig-canvas-hint {
            font-size: 11px;
            color: #aaa;
            text-align: center;
            margin-top: 5px;
        }

        .sig-upload-area {
            border: 2px dashed #ccc;
            border-radius: 8px;
            padding: 28px 16px;
            text-align: center;
            cursor: pointer;
            transition: border-color .2s;
        }

        .sig-upload-area:hover {
            border-color: #007bff;
        }

        .sig-upload-area input[type="file"] {
            display: none;
        }

        .sig-upload-preview {
            display: none;
            margin-top: 12px;
        }

        .sig-upload-preview img {
            max-height: 80px;
            max-width: 100%;
            border: 1px solid #eee;
            border-radius: 4px;
            padding: 4px;
        }

        .sig-actions {
            display: flex;
            gap: 8px;
            margin-top: 16px;
            justify-content: flex-end;
        }

        .sig-btn {
            padding: 8px 18px;
            border-radius: 6px;
            border: none;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
        }

        .sig-btn-clear {
            background: #f1f1f1;
            color: #555;
        }

        .sig-btn-apply {
            background: #007bff;
            color: #fff;
        }

        .sig-btn-apply:disabled {
            background: #aaa;
            cursor: not-allowed;
        }

        .sig-btn-cancel {
            background: #fff;
            color: #888;
            border: 1px solid #ddd;
        }

        /* ── PRINT ── */
        @media print {
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                box-sizing: border-box;
            }

            @page {
                size: A4 portrait;
                margin: 0;
            }

            html,
            body {
                width: 210mm;
                height: 297mm;
                margin: 0 !important;
                padding: 0 !important;
                background: #fff;
            }

            .no-print {
                display: none !important;
            }

            #file-print-area,
            .content-wrapper,
            .content,
            .container-fluid {
                width: 210mm !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            .new-page {
                width: 210mm !important;
                height: 297mm !important;
                min-height: 297mm !important;
                margin: 0 !important;
                box-shadow: none !important;
                overflow: hidden !important;
                page-break-after: always;
                break-after: page;
                background-image: url("{{ asset('images/investment_letter_head.png') }}") !important;
                background-size: 210mm 297mm !important;
                background-repeat: no-repeat !important;
                background-position: top left !important;
            }

            .new-page:last-child {
                page-break-after: auto;
                break-after: auto;
            }

            .file-content {
                padding: 34mm 16mm 48mm 16mm !important;
            }

            .file-content.page-subsequent {
                padding-top: 35mm !important;
            }

            table {
                width: 100%;
                border-collapse: collapse;
            }

            td {
                vertical-align: top;
            }

            p {
                margin: 4px !important;
            }

            .underline-date {
                text-decoration: underline;
                text-underline-offset: 3px;
            }
        }
    </style> --}}
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <section class="content">
            <div class="container-fluid">

                <section class="content-header no-print">
                    <div class="container-fluid">
                        <div class="row mb-2"></div>
                    </div>
                </section>
                @php
                    // If this signer already signed, we already have a saved snapshot of the
                    // fully-paginated document with their signature image baked into the exact
                    // spot they placed it (see submitSignatures() -> signed_html below).
                    // Render that directly instead of rebuilding pages from the raw row source.
                    $currentRole = $signerRole ?? 'investor';
                    $hasSavedSnapshot =
                        ($contractDocument->is_investor_signed && $contractDocument->contract_document_html) ||
                        $contractDocument->is_investor_signed;
                @endphp

                {{-- {{ dd($contractDocument->contract_document_html) }} --}}
                @if ($hasSavedSnapshot && $contractDocument->contract_document_html)
                    {{-- Already signed: show the saved snapshot, signature already in place --}}
                    <div id="file-print-area">{!! $contractDocument->contract_document_html !!}</div>
                @else
                    {{-- Not signed yet: JS paginates this raw row source into #file-print-area --}}
                    <div id="file-print-area"></div>
                    {!! $data['html'] !!}
                    @if ($investments['grand_total'] != 0 && $investment?->investment_term_type == 1)
                        @include('admin.investment.inv_agreement.investment_annexture', [
                            'investor' => $investor,
                            'investments' => $investments,
                        ])
                    @endif
                @endif

                <div class="mt-4 mb-5 text-center no-print">
                    @if (Auth::check())
                        <a href="{{ route('investmentContracts') }}" class="btn btn-secondary mr-2">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                        @if (!$contractDocument->is_investor_signed || !$contractDocument->is_company_signed)
                            <button onclick="openSendModal()" class="btn btn-success mr-2">
                                <i class="fas fa-paper-plane"></i> Send
                            </button>
                        @endif
                        {{-- <button onclick="downloadPdfServer()" class="btn btn-outline-primary">
                            <i class="fas fa-file-pdf"></i> Download PDF
                        </button> --}}
                    @else
                        @if (
                            (!$contractDocument->is_investor_signed && $currentRole == 'investor') ||
                                (!$contractDocument->is_company_signed && $currentRole == 'company'))
                            <button onclick="openSignatureModal()" class="btn btn-success mr-2">
                                <i class="fas fa-signature"></i> Add Signature
                            </button>

                            <button id="sigSubmitBtn" class="btn btn-primary" disabled onclick="submitSignatures()">
                                <i class="fas fa-check"></i> Submit Signed Agreement
                            </button>
                        @endif
                    @endif
                    {{-- <a href="{{ route('invoices.generated') }}" class="btn btn-secondary mr-2">
                        <i class="fas fa-arrow-left"></i> Back
                    </a> --}}


                    {{-- <button onclick="printInvoice()" class="btn btn-primary">
                        <i class="fas fa-print"></i> Print
                    </button> --}}

                </div>

            </div>
        </section>
    </div>

    <!-- ── SIGNATURE MODAL ── -->
    <div class="sig-modal-overlay" id="sigModalOverlay">
        <div class="sig-modal">
            <h5>✍️ Investor Signature</h5>
            <p style="font-size:12px;color:#888;margin:-8px 0 14px;">
                Your signature will appear on every page of this agreement.
            </p>

            <!-- Tabs -->
            <div class="sig-tabs">
                <div class="sig-tab active" data-tab="draw">✏️ Draw Signature</div>
                <div class="sig-tab" data-tab="upload">📁 Upload Signature</div>
            </div>

            <!-- Draw Panel -->
            <div class="sig-panel active" id="sig-panel-draw">
                <canvas id="sig-canvas" width="420" height="160"></canvas>
                <p class="sig-canvas-hint">Draw your signature above using mouse or touch</p>
            </div>

            <!-- Upload Panel -->
            <div class="sig-panel" id="sig-panel-upload">
                <div class="sig-upload-area" id="sigUploadArea"
                    onclick="document.getElementById('sigFileInput').click()">
                    <div style="font-size:28px;">📂</div>
                    <p style="margin:6px 0 2px;font-size:13px;font-weight:600;">Click to upload signature image</p>
                    <p style="font-size:11px;color:#aaa;">PNG, JPG — transparent background recommended</p>
                    <input type="file" id="sigFileInput" accept="image/*">
                </div>
                <div class="sig-upload-preview" id="sigUploadPreview">
                    <img id="sigUploadImg" src="" alt="Signature preview">
                    <p style="font-size:11px;color:#888;margin-top:4px;">Preview — <a href="#" id="sigChangeFile"
                            style="color:#007bff;">change file</a></p>
                </div>
            </div>

            <!-- Actions -->
            <div class="sig-actions">
                <button class="sig-btn sig-btn-cancel" id="sigCancelBtn">Cancel</button>
                <button class="sig-btn sig-btn-clear" id="sigClearBtn">Clear</button>
                <button class="sig-btn sig-btn-apply" id="sigApplyBtn" disabled>Apply to All Pages</button>
            </div>
        </div>
    </div>

    <div class="sig-modal-overlay" id="sendModalOverlay">
        <div class="sig-modal" style="width:360px;">
            <h5>Send for Signature</h5>
            <p style="font-size:12px;color:#888;margin:-8px 0 14px;">
                Choose how to deliver the signing link.
            </p>
            <div class="sig-actions" style="justify-content:center;gap:12px;">
                <button class="sig-btn" style="background:#25D366;color:#fff;"
                    onclick="sendForSignature('whatsapp')">
                    <i class="fab fa-whatsapp"></i> WhatsApp
                </button>
                <button class="sig-btn" style="background:#007bff;color:#fff;" onclick="sendForSignature('email')">
                    <i class="fas fa-envelope"></i> Email
                </button>
            </div>
            <div class="sig-actions">
                <button class="sig-btn sig-btn-cancel" onclick="closeSendModal()">Cancel</button>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/toastr/toastr.min.js') }}"></script>
    <script src="{{ asset('assets/select2/js/select2.full.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.22.5/dist/sweetalert2.all.min.js"></script>

    <script>
        // ── CONFIG — must match your CSS padding values exactly ──────────────────
        const CFG = {
            MM_TO_PX: 96 / 25.4, // browser renders screen at 96 dpi
            PAGE_H_MM: 297,
            PAD_TOP_P1_MM: 29, // .file-content padding-top  (page 1)
            PAD_TOP_PN_MM: 33, // .page-subsequent padding-top    (pages 2+)
            PAD_BOT_MM: 38, // footer clearance (same all pages)
            LETTERHEAD: "{{ $data['letterHead'] }}"
        };

        const CONTENT_H_P1 = (CFG.PAGE_H_MM - CFG.PAD_TOP_P1_MM - CFG.PAD_BOT_MM) * CFG.MM_TO_PX;
        const CONTENT_H_PN = (CFG.PAGE_H_MM - CFG.PAD_TOP_PN_MM - CFG.PAD_BOT_MM) * CFG.MM_TO_PX;

        // ── AUTO PAGINATOR ───────────────────────────────────────────────────────
        function buildPages() {
            const source = document.getElementById('all-rows-source');
            const printArea = document.getElementById('file-print-area');
            const annexure = document.querySelector('.letter-sheet.new-page'); // your annexure root
            if (!source || !printArea) return;

            const rows = Array.from(source.querySelectorAll('tr[data-row]'));
            printArea.innerHTML = '';

            let pageIndex = 0;
            let usedH = 0;
            let currentTbl = null;
            let maxH = 0;

            function newPage() {
                const isFirst = (pageIndex === 0);
                pageIndex++;

                const page = document.createElement('div');
                page.className = 'new-page';
                page.style.backgroundImage = `url('${CFG.LETTERHEAD}')`;

                const content = document.createElement('div');
                content.className = 'file-content' + (isFirst ? '' : ' page-subsequent');

                const tbl = document.createElement('table');
                tbl.setAttribute('width', '100%');
                tbl.setAttribute('border', '0');
                tbl.setAttribute('cellpadding', '0');
                tbl.setAttribute('cellspacing', '0');

                content.appendChild(tbl);
                page.appendChild(content);
                printArea.appendChild(page);

                currentTbl = tbl;
                usedH = 0;
                maxH = isFirst ? CONTENT_H_P1 : CONTENT_H_PN;
            }


            newPage(); // start page 1

            rows.forEach((row, index) => {
                // ── FORCE NEW PAGE (annexures always start fresh) ──
                if (row.getAttribute('data-force-page') === 'true' && usedH > 0) {
                    newPage();
                }

                currentTbl.appendChild(row);
                const rowH = row.getBoundingClientRect().height;

                if (usedH + rowH > maxH && usedH > 0) {
                    currentTbl.removeChild(row);
                    newPage();
                    currentTbl.appendChild(row);
                    usedH += rowH;
                    return;
                }

                usedH += rowH;

                // ── ORPHAN HEADING GUARD ──
                const isHeading = row.style.backgroundColor || row.getAttribute('style')?.includes(
                    'background-color');
                const nextRow = rows[index + 1];
                if (isHeading && nextRow && !nextRow.getAttribute('data-force-page')) {
                    currentTbl.appendChild(nextRow);
                    const nextRowH = nextRow.getBoundingClientRect().height;
                    currentTbl.removeChild(nextRow);
                    if (usedH + nextRowH > maxH) {
                        currentTbl.removeChild(row);
                        usedH -= rowH;
                        newPage();
                        currentTbl.appendChild(row);
                        usedH += rowH;
                    }
                }
            });
            if (annexure && printArea && !printArea.contains(annexure)) {
                annexure.style.backgroundImage = 'none';
                printArea.appendChild(annexure);
            }
        }

        document.addEventListener('DOMContentLoaded', buildPages);

        // ── PRINT ────────────────────────────────────────────────────────────────
        function printInvoice() {
            const printArea = document.getElementById('file-print-area');

            if (!printArea) {
                console.error('Print area not found.');
                return;
            }

            // Clone so print-only controls never appear in the popup.
            const clone = printArea.cloneNode(true);
            clone.querySelectorAll(
                '.no-print, .sig-placeholder-btn, .sig-placed-remove'
            ).forEach(el => el.remove());

            const invoiceHtml = clone.innerHTML;

            console.log(invoiceHtml)

            const win = window.open('', '_blank', 'width=900,height=700');

            if (!win) {
                alert('Please allow popups to print the agreement.');
                return;
            }

            win.document.open();
            win.document.write(`
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Investment Agreement</title>

    <link href="https://fonts.googleapis.com/css2?family=Amiri&display=swap" rel="stylesheet">

    <style>
        @page {
            size: A4 portrait;
            margin: 0;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        html,
        body {
            width: 210mm;
            margin: 0;
            padding: 0;
            background: #fff;
            font-family: "Times New Roman", Times, serif;
        }

        .no-print,
        .sig-placeholder-btn,
        .sig-placed-remove {
            display: none !important;
        }

        #file-print-area {
            width: 210mm;
            margin: 0;
            padding: 0;
        }

        /*
         * Every generated .new-page is one physical A4 page.
         * Do not change these dimensions.
         */
        #file-print-area > .new-page {
            position: relative;
            display: flow-root;
            width: 210mm;
            height: 297mm;
            min-height: 297mm;
            max-height: 297mm;
            margin: 0;
            padding: 0;
            overflow: hidden;
            background-color: #fff;
            background-repeat: no-repeat;
            background-position: top left;
            background-size: 210mm 297mm;
            page-break-after: always;
            break-after: page;
        }

        #file-print-area > .new-page:last-child {
            page-break-after: auto;
            break-after: auto;
        }

        /*
         * Must match CFG in buildPages():
         * First page: 29mm top, 38mm bottom
         * Other pages: 33mm top, 38mm bottom
         */
        #file-print-area .file-content {
            position: relative;
            width: 100%;
            height: 297mm;
            margin: 0;
            padding: 29mm 16mm 38mm;
            overflow: hidden;
            font-family: "Times New Roman", Times, serif;
        }

        #file-print-area .file-content.page-subsequent {
            padding-top: 33mm;
        }

        #file-print-area table {
            width: 100%;
            border-collapse: collapse;
            border-spacing: 0;
        }

        #file-print-area tr,
        #file-print-area td,
        #file-print-area th {
            vertical-align: top;
            page-break-inside: avoid;
            break-inside: avoid;
        }

        #file-print-area p {
            margin: 10px 3px !important;
        }

        #file-print-area .text-sm {
            font-size: 8pt !important;
            line-height: 1.15 !important;
        }

        #file-print-area .text-md {
            margin-top: 4px !important;
            font-size: 8.5pt !important;
            line-height: 1.15 !important;
            font-weight: 700 !important;
        }

        #file-print-area .text-lg {
            font-size: 12pt !important;
            line-height: 1.15 !important;
            font-weight: 700 !important;
        }

        #file-print-area .english {
            direction: ltr;
            text-align: left;
            font-family: "Times New Roman", Times, serif;
        }

        #file-print-area .arabic {
            direction: rtl;
            text-align: right;
            font-family: Amiri, serif;
        }

        #file-print-area strong {
            font-weight: 700 !important;
        }

        #file-print-area .underline-date {
            text-decoration: underline;
            text-underline-offset: 3px;
        }

        /* Named investor/company signature slots */
        #file-print-area .signature-slot {
            position: relative;
            min-height: 16mm;
            padding-right: 42mm;
        }

        #file-print-area .sig-placed-wrap {
            position: absolute !important;
            z-index: 20;
            width: 28mm !important;
            height: 10mm !important;
            cursor: default !important;
        }

        #file-print-area .sig-placed-wrap img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: contain;
            object-position: left bottom;
        }

        @media print {
            html,
            body,
            #file-print-area {
                width: 210mm !important;
                margin: 0 !important;
                padding: 0 !important;
            }
        }

        /* =========================================================
   INVESTMENT ANNEXURE PRINT STYLES
   ========================================================= */

#file-print-area > .letter-sheet.new-page {
    width: 210mm;
    height: 297mm;
    min-height: 297mm;
    max-width: none;
    margin: 0;
    padding: 28mm 18mm;
    overflow: hidden;
    background-color: #fff;
    background-image: none !important;
    border: none;
    box-shadow: none;
    font-family: "Segoe UI", Arial, sans-serif;
    color: #222;
}

/* Bootstrap helpers used by the annexure HTML */
#file-print-area .d-flex {
    display: flex !important;
}

#file-print-area .justify-content-between {
    justify-content: space-between !important;
}

#file-print-area .align-items-start {
    align-items: flex-start !important;
}

#file-print-area .flex-grow-1 {
    flex-grow: 1 !important;
}

#file-print-area .text-right {
    text-align: right !important;
}

#file-print-area .font-weight-bold {
    font-weight: 700 !important;
}

#file-print-area .mt-4 {
    margin-top: 1.5rem !important;
}

#file-print-area .mb-3 {
    margin-bottom: 1rem !important;
}

/* Annexure heading and body */
#file-print-area .letter-title {
    margin: 0 0 12mm;
    text-align: center;
    font-size: 16pt;
    line-height: 1.2;
    font-weight: 700;
    letter-spacing: 0.5px;
}

#file-print-area .letter-meta {
    margin-bottom: 7mm;
    font-size: 10pt;
    line-height: 1.4;
}

#file-print-area .letter-meta .label {
    font-weight: 600;
}

#file-print-area .letter-subject {
    margin: 8mm 0;
    font-size: 10pt;
    font-weight: 700;
}

#file-print-area .letter-sheet p {
    margin: 0 0 4mm !important;
    font-size: 10pt;
    line-height: 1.45;
}

/* Investment table */
#file-print-area .investment-table {
    width: 100%;
    margin-top: 3mm;
    border-collapse: collapse;
    font-size: 9.5pt;
}

#file-print-area .investment-table th,
#file-print-area .investment-table td {
    padding: 3.5mm 4mm;
    vertical-align: middle;
    border: 1px solid #aaa;
}

#file-print-area .investment-table th {
    background: #e8eaed !important;
    border-top: 0;
    font-weight: 600;
}

#file-print-area .investment-table tfoot td {
    background: #e8eaed !important;
    border-top: 2px solid #333;
    font-weight: 700;
}

/* Investor signature area in the annexure */
#file-print-area .signature-block {
    position: relative;
    min-height: 30mm;
    margin-top: 25mm;
    font-size: 10pt;
}

#file-print-area .annexure-signature-slot {
    position: relative;
    min-height: 16mm;
    padding-top: 3mm;
    padding-right: 42mm;
}

/* Signature image inserted by your existing JavaScript */
#file-print-area .annexure-signature.sig-placed-wrap,
#file-print-area .sig-placed-wrap.annexure-signature {
    position: absolute !important;
    z-index: 20;
    width: 28mm !important;
    height: 10mm !important;
}

#file-print-area .sig-placed-wrap.annexure-signature img {
    display: block;
    width: 100%;
    height: 100%;
    object-fit: contain;
    object-position: left bottom;
}
    #file-print-area .customsign {
                margin-bottom: 120px !important;
            }
    </style>
</head>
<body>
    <div id="file-print-area">${invoiceHtml}</div>

    <script>
        window.onload = async function () {
            try {
                if (document.fonts && document.fonts.ready) {
                    await document.fonts.ready;
                }
            } catch (error) {
                console.warn('Fonts could not be fully loaded before print.', error);
            }

            setTimeout(function () {
                window.print();
            }, 300);
        };

        window.onafterprint = function () {
            window.close();
        };
    <\/script>
</body>
</html>
    `);

            win.document.close();
        }
    </script>

    <script>
        // ════════════════════════════════════════════════
        // SIGNATURE PAD
        // ════════════════════════════════════════════════
        let signatureDataUrl = null; // final approved signature
        let activeTab = 'draw';
        let uploadedDataUrl = null;

        const SIG_W_MM = 40;
        const SIG_H_MM = 16;

        // ── Canvas drawing ───────────────────────────────
        const canvas = document.getElementById('sig-canvas');
        const ctx = canvas.getContext('2d');
        let isDrawing = false;
        let hasDrawn = false;

        function getPos(e) {
            const rect = canvas.getBoundingClientRect();
            const scaleX = canvas.width / rect.width;
            const scaleY = canvas.height / rect.height;
            const clientX = e.touches ? e.touches[0].clientX : e.clientX;
            const clientY = e.touches ? e.touches[0].clientY : e.clientY;
            return {
                x: (clientX - rect.left) * scaleX,
                y: (clientY - rect.top) * scaleY
            };
        }

        canvas.addEventListener('mousedown', e => {
            isDrawing = true;
            ctx.beginPath();
            const p = getPos(e);
            ctx.moveTo(p.x, p.y);
        });
        canvas.addEventListener('mousemove', e => {
            if (!isDrawing) return;
            const p = getPos(e);
            ctx.lineWidth = 2;
            ctx.lineCap = 'round';
            ctx.strokeStyle = '#1a1a2e';
            ctx.lineTo(p.x, p.y);
            ctx.stroke();
            hasDrawn = true;
            checkApplyBtn();
        });
        canvas.addEventListener('mouseup', () => {
            isDrawing = false;
        });
        canvas.addEventListener('mouseleave', () => {
            isDrawing = false;
        });
        canvas.addEventListener('touchstart', e => {
            e.preventDefault();
            isDrawing = true;
            ctx.beginPath();
            const p = getPos(e);
            ctx.moveTo(p.x, p.y);
        }, {
            passive: false
        });
        canvas.addEventListener('touchmove', e => {
            e.preventDefault();
            if (!isDrawing) return;
            const p = getPos(e);
            ctx.lineWidth = 2;
            ctx.lineCap = 'round';
            ctx.strokeStyle = '#1a1a2e';
            ctx.lineTo(p.x, p.y);
            ctx.stroke();
            hasDrawn = true;
            checkApplyBtn();
        }, {
            passive: false
        });
        canvas.addEventListener('touchend', () => {
            isDrawing = false;
        });

        // ── Tabs ─────────────────────────────────────────
        document.querySelectorAll('.sig-tab').forEach(tab => {
            tab.addEventListener('click', function() {
                activeTab = this.dataset.tab;
                document.querySelectorAll('.sig-tab').forEach(t => t.classList.remove('active'));
                document.querySelectorAll('.sig-panel').forEach(p => p.classList.remove('active'));
                this.classList.add('active');
                document.getElementById('sig-panel-' + activeTab).classList.add('active');
                checkApplyBtn();
            });
        });

        // ── File upload ───────────────────────────────────
        document.getElementById('sigFileInput').addEventListener('change', function() {
            const file = this.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = e => {
                uploadedDataUrl = e.target.result;
                document.getElementById('sigUploadImg').src = uploadedDataUrl;
                document.getElementById('sigUploadArea').style.display = 'none';
                document.getElementById('sigUploadPreview').style.display = 'block';
                checkApplyBtn();
            };
            reader.readAsDataURL(file);
        });

        document.getElementById('sigChangeFile').addEventListener('click', e => {
            e.preventDefault();
            uploadedDataUrl = null;
            document.getElementById('sigFileInput').value = '';
            document.getElementById('sigUploadArea').style.display = 'block';
            document.getElementById('sigUploadPreview').style.display = 'none';
            checkApplyBtn();
        });

        // ── Apply button state ────────────────────────────
        function checkApplyBtn() {
            const ready = (activeTab === 'draw' && hasDrawn) || (activeTab === 'upload' && uploadedDataUrl);
            document.getElementById('sigApplyBtn').disabled = !ready;
        }

        // ── Clear ─────────────────────────────────────────
        document.getElementById('sigClearBtn').addEventListener('click', () => {
            if (activeTab === 'draw') {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                hasDrawn = false;
            } else {
                uploadedDataUrl = null;
                document.getElementById('sigFileInput').value = '';
                document.getElementById('sigUploadArea').style.display = 'block';
                document.getElementById('sigUploadPreview').style.display = 'none';
            }
            checkApplyBtn();
        });

        // ── Cancel ────────────────────────────────────────
        document.getElementById('sigCancelBtn').addEventListener('click', closeSignatureModal);

        // ── Open / Close modal ────────────────────────────
        function openSignatureModal() {
            document.getElementById('sigModalOverlay').classList.add('active');

            // Re-sync canvas pixel size to its CSS display size after modal becomes visible
            setTimeout(() => {
                const rect = canvas.getBoundingClientRect();
                if (rect.width > 0 && canvas.width !== Math.round(rect.width)) {
                    // preserve existing drawing
                    const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                    canvas.width = Math.round(rect.width);
                    canvas.height = 160;
                    ctx.putImageData(imageData, 0, 0);
                }
            }, 50);
        }

        function closeSignatureModal() {
            document.getElementById('sigModalOverlay').classList.remove('active');
        }

        // // ── Apply: capture signature, then handle re-signing ──
        // document.getElementById('sigApplyBtn').addEventListener('click', () => {
        //     signatureDataUrl = (activeTab === 'draw') ? canvas.toDataURL('image/png') : uploadedDataUrl;
        //     closeSignatureModal();

        //     const alreadyPlaced = document.querySelectorAll('.sig-placed-wrap').length;

        //     if (alreadyPlaced === 0) {
        //         enableSignaturePlacement();
        //         return;
        //     }

        //     Swal.fire({
        //         title: 'Update existing signatures?',
        //         text: `You already placed a signature on ${alreadyPlaced} page(s). What would you like to do?`,
        //         icon: 'question',
        //         showDenyButton: true,
        //         showCancelButton: true,
        //         confirmButtonText: 'Update in place',
        //         denyButtonText: 'Clear & re-place manually',
        //         cancelButtonText: 'Cancel'
        //     }).then((result) => {
        //         if (result.isConfirmed) {
        //             updatePlacedSignatures();
        //             enableSignaturePlacement(); // still adds buttons to any unsigned pages
        //         } else if (result.isDenied) {
        //             clearPlacedSignatures();
        //             enableSignaturePlacement();
        //         }
        //         // cancel = do nothing, signatureDataUrl still holds the new sig for next placements
        //     });
        // });

        // // ── Option A: swap the image on every already-placed stamp, keep position ──
        // function updatePlacedSignatures() {
        //     document.querySelectorAll('.sig-placed-wrap img').forEach(img => {
        //         img.src = signatureDataUrl;
        //     });
        //     toastr.success('Updated signature on all previously signed pages.');
        // }

        // // ── Option B: remove placed stamps and restore the "Place Signature" button ──
        // function clearPlacedSignatures() {
        //     document.querySelectorAll('.new-page').forEach(page => {
        //         const wraps = page.querySelectorAll('.sig-placed-wrap'); // ALL, not just the first

        //         wraps.forEach(wrap => {
        //             const slotId = wrap.dataset.slotId || null;
        //             const top = wrap.style.top;
        //             const left = wrap.style.left;
        //             const bottom = wrap.style.bottom;

        //             wrap.remove();

        //             // Restore a placeholder button in the same spot so they can re-place
        //             const btn = createPlaceButton(page, slotId);
        //             btn.style.top = top;
        //             btn.style.left = left;
        //             btn.style.bottom = bottom;
        //             page.appendChild(btn);
        //         });
        //     });

        //     updateSubmitButtonState();
        //     toastr.info('Cleared previous signatures — click "Sign" again on each spot.');
        // }

        document.getElementById('sigApplyBtn').addEventListener('click', () => {
            signatureDataUrl = (activeTab === 'draw') ? canvas.toDataURL('image/png') : uploadedDataUrl;
            closeSignatureModal();

            const cfg = window.AgreementConfig;
            const alreadyPlaced = document.querySelectorAll(`.sig-placed-wrap[data-signer="${cfg.signerRole}"]`)
                .length;

            if (alreadyPlaced === 0) {
                enableSignaturePlacement();
                return;
            }

            Swal.fire({
                title: 'Update existing signatures?',
                text: `You already placed a signature on ${alreadyPlaced} page(s). What would you like to do?`,
                icon: 'question',
                showDenyButton: true,
                showCancelButton: true,
                confirmButtonText: 'Update in place',
                denyButtonText: 'Clear & re-place manually',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    updatePlacedSignatures();
                    enableSignaturePlacement();
                } else if (result.isDenied) {
                    clearPlacedSignatures();
                    enableSignaturePlacement();
                }
            });
        });

        function updatePlacedSignatures() {
            const cfg = window.AgreementConfig;
            document.querySelectorAll(`.sig-placed-wrap[data-signer="${cfg.signerRole}"] img`).forEach(img => {
                img.src = signatureDataUrl;
            });
            toastr.success('Updated your signature on all previously signed pages.');
        }

        function clearPlacedSignatures() {
            const cfg = window.AgreementConfig;
            document.querySelectorAll(`.new-page`).forEach(page => {
                const wraps = page.querySelectorAll(`.sig-placed-wrap[data-signer="${cfg.signerRole}"]`);

                wraps.forEach(wrap => {
                    const slotId = wrap.dataset.slotId || null;
                    const top = wrap.style.top,
                        left = wrap.style.left,
                        bottom = wrap.style.bottom;
                    wrap.remove();

                    const btn = createPlaceButton(page, slotId, wrap.dataset.spotKey);
                    btn.style.top = top;
                    btn.style.left = left;
                    btn.style.bottom = bottom;
                    page.appendChild(btn);
                });
            });
            updateSubmitButtonState();
            toastr.info('Cleared your previous signatures — click "Sign" again on each spot.');
        }

        // ════════════════════════════════════════════════
        // PER-PAGE SIGNATURE PLACEMENT
        // ════════════════════════════════════════════════
        function enableSignaturePlacement() {
            if (!signatureDataUrl) return;
            const cfg = window.AgreementConfig;
            console.log(cfg);

            const alreadySigned = (cfg.signerRole === 'investor' && cfg.isInvestorSigned) ||
                (cfg.signerRole === 'company' && cfg.isCompanySigned);
            if (alreadySigned) {
                toastr.info('You have already signed this agreement.');
                return;
            }

            // Build the required-spots registry ONCE — this becomes the single source of truth
            if (!window.requiredSignatureSpots) {
                window.requiredSignatureSpots = new Set();

                document.querySelectorAll('.new-page').forEach((page, pageIndex) => {
                    const slots = page.querySelectorAll(`[data-signature-slot][data-signer="${cfg.signerRole}"]`);
                    const hasOwnSignaturePad = page.querySelector('[data-own-signature-pad]') !== null;

                    slots.forEach(slot => {
                        window.requiredSignatureSpots.add(`${pageIndex}-${slot.dataset.signatureSlot}`);
                    });

                    // Only require the default stamp on pages WITHOUT their own dedicated pad
                    if (!hasOwnSignaturePad) {
                        window.requiredSignatureSpots.add(`${pageIndex}-default-${cfg.signerRole}`);
                    }
                });
            }


            document.querySelectorAll('.new-page').forEach((page, pageIndex) => {
                page.style.position = 'relative';

                const slots = page.querySelectorAll(`[data-signature-slot][data-signer="${cfg.signerRole}"]`);
                const hasOwnSignaturePad = page.querySelector('[data-own-signature-pad]') !== null;

                if (slots.length > 0) {
                    // Named slots (e.g. fama_en/fama_ar or investor_en/investor_ar on the signature-pad page)
                    slots.forEach((slot) => {
                        const slotId = slot.dataset.signatureSlot;
                        const spotKey = `${pageIndex}-${slotId}`;

                        if (page.querySelector(`.sig-placed-wrap[data-spot-key="${spotKey}"]`)) return;
                        if (page.querySelector(`.sig-placeholder-btn[data-spot-key="${spotKey}"]`)) return;

                        const pageRect = page.getBoundingClientRect();
                        const slotRect = slot.getBoundingClientRect();

                        const btn = createPlaceButton(page, slotId, spotKey);

                        // Detect annexure signature slot
                        const isAnnexure = !!page.querySelector('.annexure-signature');

                        if (isAnnexure) { // If it's an annexure signature slot, add a special class for styling
                            btn.classList.add('annexure-signature-btn');

                            // ANNEXURE:
                            // Position directly over/near the Signature slot
                            btn.style.left = (slotRect.left - pageRect.left + 100) + 'px';
                            btn.style.top = (slotRect.top - pageRect.top - 15) + 'px';

                        } else {

                            if (cfg.signerRole === 'investor') {
                                btn.style.top = (slotRect.top - pageRect.top + 4) + 'px';
                            } else {
                                if (cfg.DocumentTypeId == 3) {
                                    btn.style.top = (slotRect.top - pageRect.top + 20) + 'px';
                                } else if (cfg.DocumentTypeId == 5) {
                                    btn.style.top = (slotRect.top - pageRect.top + 45) + 'px';
                                } else {
                                    btn.style.top = (slotRect.top - pageRect.top + 59) + 'px';
                                }

                            }
                            btn.style.left = (slotRect.left - pageRect.left + 125) + 'px';
                        }

                        page.appendChild(btn);
                    });
                }

                // Skip the default per-page stamp entirely on the dedicated signature-pad page —
                // it already has its own named slots for both signers above.
                if (hasOwnSignaturePad) return;

                // Default per-page stamp — every OTHER page gets one, positioned per role.
                const spotKey = `${pageIndex}-default-${cfg.signerRole}`;

                if (page.querySelector(`.sig-placed-wrap[data-spot-key="${spotKey}"]`)) return;
                if (page.querySelector(`.sig-placeholder-btn[data-spot-key="${spotKey}"]`)) return;

                const btn = createPlaceButton(page, null, spotKey);
                btn.style.bottom = '37mm';
                if (cfg.signerRole === 'investor') {
                    btn.style.right = '16mm';
                } else {
                    btn.style.left = '16mm';
                }
                page.appendChild(btn);
            });

            updateSubmitButtonState();
        }

        function updateSubmitButtonState() {
            if (!window.requiredSignatureSpots) return;

            const requiredCount = window.requiredSignatureSpots.size;
            let placed = 0;

            window.requiredSignatureSpots.forEach(spotKey => {
                if (document.querySelector(`.sig-placed-wrap[data-spot-key="${spotKey}"]`)) placed++;
            });

            const submitBtn = document.getElementById('sigSubmitBtn');
            if (!submitBtn) return;

            submitBtn.disabled = (placed < requiredCount);
            submitBtn.title = submitBtn.disabled ?
                `Place signature on all required spot(s) first (${placed}/${requiredCount} done)` :
                '';
        }

        function createPlaceButton(page, slotId, spotKey) {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'sig-placeholder-btn no-print';
            btn.textContent = slotId ? '✍️ Place Signature' : '✍️ Place Signature';
            if (slotId) btn.dataset.slotId = slotId;
            btn.dataset.spotKey = spotKey;
            btn.addEventListener('click', () => placeSignatureOnPage(page, btn, slotId, spotKey));
            return btn;
        }

        function placeSignatureOnPage(page, btn, slotId, spotKey) {
            const cfg = window.AgreementConfig;
            const wrap = document.createElement('div');
            wrap.className = 'sig-placed-wrap';

            if (btn.classList.contains('annexure-signature-btn')) {
                wrap.classList.add('annexure-signature');
            }

            wrap.dataset.signer = cfg.signerRole;
            if (slotId) wrap.dataset.slotId = slotId;
            wrap.dataset.spotKey = spotKey;

            wrap.style.top = btn.style.top || '';
            wrap.style.left = btn.style.left || '';
            wrap.style.right = btn.style.right || '';
            wrap.style.bottom = btn.style.bottom || '';
            wrap.style.width = slotId ? '28mm' : '40mm';
            wrap.style.height = slotId ? '10mm' : '16mm';

            const img = document.createElement('img');
            img.src = signatureDataUrl;
            wrap.appendChild(img);

            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'sig-placed-remove no-print';
            removeBtn.textContent = '×';
            removeBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                wrap.remove();
                const newBtn = createPlaceButton(page, slotId, spotKey);
                newBtn.style.top = wrap.style.top;
                newBtn.style.left = wrap.style.left;
                newBtn.style.right = wrap.style.right;
                newBtn.style.bottom = wrap.style.bottom;
                page.appendChild(newBtn);
                updateSubmitButtonState();
            });
            wrap.appendChild(removeBtn);

            makeDraggable(wrap, page);

            btn.remove();
            page.appendChild(wrap);
            updateSubmitButtonState();
        }
        // ── Optional: let them nudge the signature within the page ──
        function makeDraggable(el, container) {
            let dragging = false,
                startX, startY, startLeft, startTop;

            el.addEventListener('mousedown', startDrag);
            el.addEventListener('touchstart', startDrag, {
                passive: false
            });

            function startDrag(e) {
                if (e.target.classList.contains('sig-placed-remove')) return;
                e.preventDefault();
                dragging = true;
                const point = e.touches ? e.touches[0] : e;
                startX = point.clientX;
                startY = point.clientY;
                const rect = el.getBoundingClientRect();
                const containerRect = container.getBoundingClientRect();
                startLeft = rect.left - containerRect.left;
                startTop = rect.top - containerRect.top;
                document.addEventListener('mousemove', onDrag);
                document.addEventListener('touchmove', onDrag, {
                    passive: false
                });
                document.addEventListener('mouseup', endDrag);
                document.addEventListener('touchend', endDrag);
            }

            function onDrag(e) {
                if (!dragging) return;
                e.preventDefault();
                const point = e.touches ? e.touches[0] : e;
                const dx = point.clientX - startX;
                const dy = point.clientY - startY;
                el.style.bottom = 'auto';
                el.style.left = (startLeft + dx) + 'px';
                el.style.top = (startTop + dy) + 'px';
            }

            function endDrag() {
                dragging = false;
                document.removeEventListener('mousemove', onDrag);
                document.removeEventListener('touchmove', onDrag);
                document.removeEventListener('mouseup', endDrag);
                document.removeEventListener('touchend', endDrag);
            }
        }

        async function submitSignatures() {
            const cfg = window.AgreementConfig;

            // const placements = Array.from(document.querySelectorAll('.sig-placed-wrap')).map(wrap => ({
            //     slot_id: wrap.dataset.slotId,
            //     image: wrap.querySelector('img').src, // base64 data URL
            //     top: wrap.style.top,
            //     left: wrap.style.left,
            //     bottom: wrap.style.bottom,
            //     slot_id: wrap.dataset.slotId,
            // }));

            const placedCount = document.querySelectorAll('.sig-placed-wrap').length;

            if (placedCount === 0) {
                toastr.error('Please place your signature before submitting.');
                return;
            }

            const sigWrap = document.querySelector(`.sig-placed-wrap[data-signer="${cfg.signerRole}"]`);

            // Clone the print area and strip ALL interactive-only elements —
            // this removes both this session's buttons AND any leftover buttons
            // from a previously-saved signer's stamps.
            const printArea = document.getElementById('file-print-area');
            const clone = printArea.cloneNode(true);
            clone.querySelectorAll('.sig-placeholder-btn, .sig-placed-remove').forEach(el => el.remove());

            const signedHtml = clone.innerHTML;

            // console.log(signedHtml);

            try {
                const url = "{{ route('agreements.sign', ['contract' => 'PLACEHOLDER_ID']) }}".replace(
                    'PLACEHOLDER_ID', cfg.agreementId);
                // const res = await fetch(`/agreements/${cfg.agreementId}/sign`, {
                const res = await fetch(url, {
                    method: 'POST',
                    credentials: 'same-origin', // ensure session cookie rides along — avoids silent 302→login
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json', // tells Laravel to return JSON errors, not a Blade page
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                            .content
                    },
                    body: JSON.stringify({
                        signer_role: cfg.signerRole,
                        signature_count: placedCount,
                        signed_html: signedHtml,
                        signature: sigWrap ? sigWrap.querySelector('img').src : null,
                    })
                });

                // Catch redirects explicitly — if this is true, res.url is likely the login page
                if (res.status == 'success') {
                    console.error('Request was redirected to:', res.url);
                    window.location.href = "{{ route('investor.sign.success') }}";
                    // toastr.error('Your session may have expired. Please refresh and try again.');
                    return;
                }

                const rawText = await res
                    .text(); // always read as text first, never blind res.json()

                if (!res.ok) {
                    console.error('Server responded with error', res.status, rawText);
                    toastr.error(`
                            Submit failed(HTTP $ {
                                res.status
                            }).Check console
                            for details.
                            `);
                    return;
                }

                let data;
                try {
                    data = JSON.parse(rawText);
                } catch (parseErr) {
                    console.error('Response was not valid JSON:', rawText);
                    toastr.error('Server returned an unexpected response. Check console.');
                    return;
                }

                toastr.success('Agreement signed and submitted.');
                window.location.href = "{{ route('investor.sign.success') }}";
                document.getElementById('sigSubmitBtn').disabled = true;
                document.querySelectorAll('.sig-placeholder-btn, .sig-placed-remove').forEach(el =>
                    el
                    .remove()); // lock it down
            } catch (err) {
                console.error('Network/fetch error:', err);
                toastr.error('Please complete signature in all pages.');
            }
        }
    </script>

    <script>
        window.AgreementConfig = {
            agreementId: {{ $contractDocument->id }},
            DocumentTypeId: {{ $contractDocument->investor_agreement_type_id }},
            isInvestorSigned: @json((bool) $contractDocument->is_investor_signed),
            isCompanySigned: @json((bool) $contractDocument->is_company_signed),
            // Who is viewing this page right now — investor-facing link vs internal staff link
            signerRole: '{{ $signerRole ?? 'investor' }}' // set this from the route/controller
        };
    </script>

    <script>
        function openSendModal() {
            document.getElementById('sendModalOverlay').classList.add('active');
        }

        function closeSendModal() {
            document.getElementById('sendModalOverlay').classList.remove('active');
        }

        async function sendForSignature(channel) {
            closeSendModal();
            try {
                // const res = await fetch(`/agreements/{{ $contractDocument->id }}/send`, {
                const res = await fetch(`{{ route('agreements.send', $contractDocument->id) }}`, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        channel
                    })
                });
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || 'Send failed');
                toastr.success(data.message);
            } catch (err) {
                console.error(err);
                toastr.error('Could not send the agreement. Please try again.');
            }
        }
    </script>

</body>

</html>
