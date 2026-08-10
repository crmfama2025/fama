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
                padding-top: 33mm;
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

            .underline-date {
                text-decoration: underline;
                text-underline-offset: 3px;
            }
        }
    </style>
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

                {{-- Pages are injected here by JS --}}
                <div id="file-print-area"></div>

                {{-- <table id="all-rows-source" style="display:none; width:100%; border-collapse:collapse;" border="0"
                    cellpadding="0" cellspacing="0">


                    <tr data-row>
                        <td colspan="2" align="center">
                            <p class="text-lg">
                                PARTIAL WITHDRAWAL FORM / نموذج السحب الجزئي
                            </p>
                        </td>
                    </tr>


                    <tr data-row style="background-color:#F2F2F2">
                        <td width="50%" height="30" style="border:1px solid #ccc;">
                            <div class="english text-md" style="margin-top:6px;">PARTIAL WITHDRAWAL FORM
                            </div>
                        </td>
                        <td width="50%" height="30" style="border:1px solid #ccc;">
                            <div class="arabic text-md" style="margin-top:6px;">نموذج السحب الجزئي
                            </div>
                        </td>
                    </tr>


                    <tr data-row>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-sm">Date: {doc_created_date}<br>
                                    Investor Name: {investor_name_eng}<br>
                                    Emirates ID / Passport No.: {investor_id}
                                </p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-sm">
                                    التاريخ: {doc_created_date}<br>
                                    اسم المستثمر: {investor_name_ar}<br>
                                    رقم الهوية الإماراتية / جواز السفر: {investor_id}

                                </p>
                            </div>
                        </td>
                    </tr>

                    <tr data-row>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-sm">1. This Partial Withdrawal Form is executed in relation to the
                                    Mudarabah Investment Agreement dated {mudarabah_created_date} entered into between
                                    {company_name_eng} (“Company”) and the undersigned Investor, together with
                                    addendum(s),
                                    if any.</p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-sm"><span>1 </span>. تم توقيع نموذج السحب الجزئي هذا فيما يتعلق باتفاقية
                                    استثمار
                                    المضاربة المؤرخة في {mudarabah_created_date} والمبرمة بين شركة {company_name_ar}
                                    («الشركة») والمستثمر الموقّع أدناه، مع الملحق أو الملاحق، إن وجدت.</p>
                            </div>
                        </td>
                    </tr>

                    <tr data-row>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-sm">2. The Investor has requested, and the Company has agreed, to permit
                                    partial withdrawal of AED /- {withdrwal_amount} ({withdrwal_amount_eng}) from the
                                    Investor’s aggregate investment with the Company.</p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-sm"><span>2 </span>.طلب المستثمر، ووافقت الشركة، السماح بسحب جزئي بمبلغ
                                    {withdrwal_amount}/- درهم إماراتي ({withdrwal_amount_ar}) من إجمالي استثمار المستثمر
                                    لدى الشركة.</p>
                            </div>
                        </td>
                    </tr>

                    <tr data-row>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-sm">3. Accordingly, upon payment of the above amount, the Investor’s
                                    aggregate investment with the Company shall stand revised as follows:</p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-sm"><span>3 </span>.وبناءً عليه، عند دفع المبلغ المذكور أعلاه، يُعد
                                    إجمالي استثمار المستثمر لدى الشركة معدلاً على النحو الآتي:</p>
                            </div>
                        </td>
                    </tr>

                    <tr data-row style="background-color:#F2F2F2">
                        <td width="50%" height="20" style="border:1px solid #ccc;">
                            <div class="english text-md">Revised Investment Statement</div>
                        </td>
                        <td width="50%" height="20" style="border:1px solid #ccc;">
                            <div class="arabic text-md">بيان الاستثمار المعدّل</div>
                        </td>
                    </tr>

                    <tr data-row>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-sm">S. No. | Particulars | Amount (AED) | Received/Paid on | Date of
                                    Document<br>
                                    1 | Original Investment | __________ | ______ | ______<br>
                                    2 | Partial Withdrawal | ___________ | ______ | ______<br>
                                    3 | Additional Investment | __________ | ______ | ______<br>
                                    4 | Additional Investment | __________ | ______ | ______<br>
                                    5 | Total Revised Capital | ___________ | ______ | ______
                                </p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-sm">
                                    الرقم التسلسلي | البيان | المبلغ (درهم إماراتي) | تاريخ الاستلام/الدفع | تاريخ
                                    المستند<br>
                                    1 | الاستثمار الأصلي | __________ | ______ | ______<br>
                                    2 | سحب جزئي | ___________ | ______ | ______<br>
                                    3 | استثمار إضافي | __________ | ______ | ______<br>
                                    4 | استثمار إضافي | __________ | ______ | ______<br>
                                    5 | إجمالي رأس المال المعدّل | ___________ | ______ | ______

                                </p>
                            </div>
                        </td>
                    </tr>

                    <tr data-row>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-sm">4. Except to the extent of the above partial withdrawal, all terms
                                    and conditions of the Mudarabah Investment Agreement and addendum(s), if any, shall
                                    continue to remain valid and binding.</p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-sm"><span>4 </span>.باستثناء نطاق السحب الجزئي المذكور أعلاه، تستمر جميع
                                    شروط وأحكام اتفاقية استثمار المضاربة والملحق أو الملاحق، إن وجدت، صحيحة وملزمة.</p>
                            </div>
                        </td>
                    </tr>

                    <tr data-row style="background-color:#F2F2F2">
                        <td width="50%" height="20" style="border:1px solid #ccc;">
                            <div class="english text-md">Signatures</div>
                        </td>
                        <td width="50%" height="20" style="border:1px solid #ccc;">
                            <div class="arabic text-md">التوقيعات</div>
                        </td>
                    </tr>

                    <tr data-row>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-sm">For {company_name_eng}<br>
                                    Authorized Signatory: ______________________<br>
                                    Date: __________________
                                </p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-sm">عن شركة {company_name_ar}<br>
                                    المفوّض بالتوقيع: ______________________<br>
                                    التاريخ: __________________
                                </p>
                            </div>
                        </td>
                    </tr>

                    <tr data-row>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-sm">Investor {investor_name_eng}<br>
                                    Signature: ______________________<br>
                                    Date: ______________________
                                </p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-sm">المستثمر {investor_name_ar}<br>
                                    التوقيع: ______________________<br>
                                    التاريخ: ______________________
                                </p>
                            </div>
                        </td>
                    </tr>

                </table> --}}

                <table id="all-rows-source" style="width:100%; border-collapse:collapse;" border="0" cellpadding="0"
                    cellspacing="0">

                    <tr data-row>
                        <td align="center">
                            <p class="text-lg" style="text-decoration:underline;">MUDARABAH COMPLETION AND SETTLEMENT
                                AGREEMENT</p>
                        </td>
                    </tr>

                    <tr data-row>
                        <td style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-sm">This Mudarabah Completion and Settlement Agreement ("Settlement
                                    Agreement") is made on {settlement_day} day of {settlement_month}
                                    {settlement_year}</p>
                            </div>
                        </td>
                    </tr>

                    <tr data-row>
                        <td style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-sm"><strong>BY AND BETWEEN</strong></p>
                                <p class="text-sm"><strong>{company_name_eng}</strong>, a company incorporated under
                                    the laws of the United Arab Emirates, holding Licence No.
                                    <strong>{company_licence_no}</strong>
                                    and Registration No. <strong>{company_reg_no}</strong> (hereinafter referred to
                                    as the
                                    "Company" or "Mudarib");
                                </p>
                                <p class="text-sm"><strong>AND</strong></p>
                                <p class="text-sm">Mr./Ms. <strong>{investor_name_eng}</strong>, holder of Emirates
                                    ID/Passport No. <strong>{investor_id}</strong> (hereinafter referred to as
                                    the
                                    "Investor" or <strong>"Rabb-ul-Maal"</strong>).</p>
                                <p class="text-sm">The Company and the Investor are hereinafter collectively referred
                                    to
                                    as the "Parties".</p>
                            </div>
                        </td>
                    </tr>

                    <tr data-row style="background-color:#F2F2F2">
                        <td height="20" style="border:1px solid #ccc;">
                            <div class="english text-md">RECITALS</div>
                        </td>
                    </tr>

                    <tr data-row>
                        <td style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-sm">
                                    A. The Parties entered into a Profit-Sharing Investment Agreement
                                    (Mudarabah Agreement) dated
                                    <span class="underline-date">{mudarabah_created_date}</span>
                                    ("Original Agreement") and Additional capital contribution document(s)
                                    ("Addendum(s)") dated
                                    {addendum_dates}
                                    (Collectively referred to as the "Investment Documents").
                                </p>
                                <p class="text-sm">C. On <span
                                        class="underline-date">{termination_requested_date}</span>, the Investor
                                    requested withdrawal and
                                    settlement of the entire investment relationship under the Investment Documents.</p>
                                <p class="text-sm">D. The Company has completed the settlement process and has paid all
                                    amounts due to the Investor in accordance with the Investment Documents.</p>
                            </div>
                        </td>
                    </tr>

                    <tr data-row>
                        <td style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-sm"><strong>NOW THEREFORE</strong>, the Parties agree as follows:</p>
                            </div>
                        </td>
                    </tr>

                    <tr data-row style="background-color:#F2F2F2">
                        <td height="20" style="border:1px solid #ccc;">
                            <div class="english text-md">1. ACKNOWLEDGEMENT OF SETTLEMENT</div>
                        </td>
                    </tr>

                    <tr data-row>
                        <td style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-sm">1.1 The Parties acknowledge that at the time of termination notice
                                    from the Investor, the total capital amount invested with the Company was under the
                                    Investment Documents amounted to AED {capital}.</p>
                                <p class="text-sm">1.2 The Parties further acknowledge that the Company has completed
                                    the final reconciliation of accounts and determined the amounts payable to the
                                    Investor.</p>
                                <p class="text-sm">1.3 The Investor confirms that the Company has paid and the Investor
                                    has received the following:</p>
                                <p class="text-sm">(a) Capital Returned: AED {capital}.<br>
                                    (b) Profit Paid: AED {profit}.<br>
                                    (c) Total Settlement Amount: AED {total_amount}.</p>
                                <p class="text-sm">1.4 The Investor confirms that the above amounts have been received
                                    in full and to his/her complete satisfaction.</p>
                            </div>
                        </td>
                    </tr>

                    <tr data-row style="background-color:#F2F2F2">
                        <td height="20" style="border:1px solid #ccc;">
                            <div class="english text-md">2. COMPLETION OF MUDARABAH RELATIONSHIP</div>
                        </td>
                    </tr>

                    <tr data-row>
                        <td style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-sm">2.1 Upon execution of this Settlement Agreement, all investments
                                    made
                                    by the Investor under the Investment Documents shall be deemed fully settled,
                                    completed and concluded.</p>
                                <p class="text-sm">2.2 The Original Agreement and all related addendums or additional
                                    capital contribution documents shall stand terminated by performance and mutual
                                    settlement.</p>
                                <p class="text-sm">2.3 Neither Party shall have any continuing obligation under the
                                    Investment Documents.</p>
                            </div>
                        </td>
                    </tr>

                    <tr data-row style="background-color:#F2F2F2">
                        <td height="20" style="border:1px solid #ccc;">
                            <div class="english text-md">3. FULL AND FINAL RELEASE</div>
                        </td>
                    </tr>

                    <tr data-row>
                        <td style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-sm">3.1 The Investor irrevocably acknowledges that:</p>
                                <p class="text-sm">(a) no Capital remains outstanding;<br>
                                    (b) no profit remains outstanding;<br>
                                    (c) no payment remains due from the Company under the Investment Documents; and<br>
                                    (d) all obligations of the Company arising from the Investment Documents have been
                                    fully satisfied and discharged.</p>
                                <p class="text-sm">3.2 The Investor releases and forever discharges the Company, its
                                    shareholders, directors, managers, employees, representatives, successors and
                                    assigns from any and all claims, demands, actions, liabilities, costs or disputes
                                    arising out of or relating to the Investment Documents up to the date of this
                                    Settlement Agreement.</p>
                                <p class="text-sm">3.3 The Company similarly releases the Investor from any obligations
                                    arising under the Investment Documents except obligations expressly stated to
                                    survive therein.</p>
                            </div>
                        </td>
                    </tr>

                    <tr data-row style="background-color:#F2F2F2">
                        <td height="20" style="border:1px solid #ccc;">
                            <div class="english text-md">4. CONFIDENTIALITY</div>
                        </td>
                    </tr>

                    <tr data-row>
                        <td style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-sm">The Parties shall keep the terms of this Settlement Agreement
                                    confidential except where disclosure is required by law, court order, regulator,
                                    auditor, tax authority or professional adviser.</p>
                            </div>
                        </td>
                    </tr>

                    <tr data-row style="background-color:#F2F2F2">
                        <td height="20" style="border:1px solid #ccc;">
                            <div class="english text-md">5. GOVERNING LAW AND DISPUTE RESOLUTION</div>
                        </td>
                    </tr>

                    <tr data-row>
                        <td style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-sm">This Settlement Agreement shall be governed by the laws applicable
                                    in the Emirate of Dubai and the federal laws of the United Arab Emirates.</p>
                                <p class="text-sm">Any dispute arising out of or in connection with this Settlement
                                    Agreement shall be referred to arbitration in accordance with the Arbitration Rules
                                    of the Dubai International Arbitration Centre (DIAC). The seat of arbitration shall
                                    be Dubai, United Arab Emirates, and the language of arbitration shall be English.
                                </p>
                            </div>
                        </td>
                    </tr>

                    <tr data-row style="background-color:#F2F2F2">
                        <td height="20" style="border:1px solid #ccc;">
                            <div class="english text-md">6. ENTIRE AGREEMENT</div>
                        </td>
                    </tr>

                    <tr data-row>
                        <td style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-sm">This Settlement Agreement constitutes the entire understanding
                                    between the Parties concerning the settlement of the Investment Documents and
                                    supersedes all prior discussions relating to such settlement.</p>
                            </div>
                        </td>
                    </tr>

                    <tr data-row>
                        <td style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-sm"><strong>IN WITNESS WHEREOF</strong>, the Parties have executed this
                                    Settlement Agreement on the date first written above.</p>
                            </div>
                        </td>
                    </tr>

                    <tr data-row style="background-color:#F2F2F2">
                        <td width="100%" height="20" style="border:1px solid #ccc;">
                            <div class="english text-md">Signatures</div>
                        </td>
                    </tr>

                    <tr data-row>
                        <td style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-sm">For the Company<br>
                                    Signature: ______________________<br>
                                    Date: ______________________
                                </p>
                            </div>
                        </td>
                    </tr>

                    <tr data-row>
                        <td style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-sm">Investor<br>
                                    Signature: ______________________<br>
                                    Date: ______________________
                                </p>
                            </div>
                        </td>
                    </tr>

                </table>




                <div class="mt-4 mb-5 text-center no-print">
                    <a href="{{ route('invoices.generated') }}" class="btn btn-secondary mr-2">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>

                    <button onclick="openSignatureModal()" class="btn btn-success mr-2">
                        <i class="fas fa-signature"></i> Add Signature
                    </button>

                    <button onclick="printInvoice()" class="btn btn-primary">
                        <i class="fas fa-print"></i> Print
                    </button>
                </div>

            </div>
        </section>
    </div>

    <!-- ── SIGNATURE MODAL ── -->
    <div class="sig-modal-overlay" id="sigModalOverlay">
        <div class="sig-modal">
            <h5>✍️ Investor Signature</h5>
            <p class="marginClass" style="font-size:11px;color:#888;margin:-8px 0 14px;">
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
                <p class="marginClass" class="sig-canvas-hint">Draw your signature above using mouse or touch</p>
            </div>

            <!-- Upload Panel -->
            <div class="sig-panel" id="sig-panel-upload">
                <div class="sig-upload-area" id="sigUploadArea"
                    onclick="document.getElementById('sigFileInput').click()">
                    <div style="font-size:28px;">📂</div>
                    <p class="marginClass" style="margin:6px 0 2px;font-size:13px;font-weight:600;">Click to upload
                        signature image</p>
                    <p class="marginClass" style="font-size:11px;color:#aaa;">PNG, JPG — transparent background
                        recommended</p>
                    <input type="file" id="sigFileInput" accept="image/*">
                </div>
                <div class="sig-upload-preview" id="sigUploadPreview">
                    <img id="sigUploadImg" src="" alt="Signature preview">
                    <p class="marginClass" style="font-size:11px;color:#888;margin-top:4px;">Preview — <a
                            href="#" id="sigChangeFile" style="color:#007bff;">change file</a></p>
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
            LETTERHEAD: "{{ asset('images/investment_letter_head.png') }}"
        };

        const CONTENT_H_P1 = (CFG.PAGE_H_MM - CFG.PAD_TOP_P1_MM - CFG.PAD_BOT_MM) * CFG.MM_TO_PX;
        const CONTENT_H_PN = (CFG.PAGE_H_MM - CFG.PAD_TOP_PN_MM - CFG.PAD_BOT_MM) * CFG.MM_TO_PX;

        // ── AUTO PAGINATOR ───────────────────────────────────────────────────────
        function buildPages() {
            const source = document.getElementById('all-rows-source');
            const printArea = document.getElementById('file-print-area');
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
        }

        document.addEventListener('DOMContentLoaded', buildPages);

        // ── PRINT ────────────────────────────────────────────────────────────────
        function printInvoice() {
            const invoiceHtml = document.getElementById('file-print-area').innerHTML;
            const styles = Array.from(document.querySelectorAll('style'))
                .map(s => s.innerHTML).join('\n');

            const win = window.open('', '_blank');
            win.document.write(`<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Investment Agreement</title>
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&display=swap" rel="stylesheet">
    <style>
        ${styles}
        * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; color-adjust: exact !important; box-sizing: border-box; }
        @page { size: A4 portrait; margin: 0; }
        html, body { width: 210mm; margin: 0 !important; padding: 0 !important; }
        #file-print-area { width: 210mm !important; margin: 0 !important; padding: 0 !important; }
        .new-page {
            width: 210mm !important; height: 297mm !important; min-height: 297mm !important;
            margin: 0 !important; box-shadow: none !important; overflow: hidden !important;
            page-break-after: always; break-after: page;
            background-size: 210mm 297mm !important; background-repeat: no-repeat !important; background-position: top left !important;
        }
        .new-page:last-child { page-break-after: auto; break-after: auto; }
        .file-content { padding: 34mm 16mm 48mm 16mm !important; }
        .file-content.page-subsequent { padding-top: 33mm !important; }
        .no-print { display: none !important; }
        table { width: 100%; border-collapse: collapse; }
        td { vertical-align: top; }
        p { margin: 4px !important; }
        .signature-stamp {
            position: absolute !important;
            bottom: 14mm !important;
            left: 16mm !important;
            width: 40mm !important;
            height: 16mm !important;
            z-index: 10 !important;
            pointer-events: none !important;
        }
        .signature-stamp img {
            width: 100% !important;
            height: 100% !important;
            object-fit: contain !important;
            object-position: left bottom !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        .signature-label {
            position: absolute !important;
            bottom: 10mm !important;
            left: 16mm !important;
            font-size: 6.5pt !important;
            color: #444 !important;
            font-family: 'Times New Roman', serif !important;
            width: 40mm !important;
            text-align: left !important;
        }
    </style>
</head>
<body>
    <div id="file-print-area">${invoiceHtml}</div>
    <script>
        window.onload = function () {
            setTimeout(function () { window.print(); }, 1000);
            window.onafterprint = function () { window.close(); };
        };
    <\/script>
</body>
</html>`);
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

        // ── Apply: stamp signature on every page ──────────
        document.getElementById('sigApplyBtn').addEventListener('click', () => {
            // Get final data URL
            if (activeTab === 'draw') {
                signatureDataUrl = canvas.toDataURL('image/png');
            } else {
                signatureDataUrl = uploadedDataUrl;
            }

            stampSignatureOnAllPages(signatureDataUrl);
            closeSignatureModal();
        });

        function stampSignatureOnAllPages(dataUrl) {
            // Remove any existing stamps
            document.querySelectorAll('.signature-stamp, .signature-label').forEach(el => el.remove());

            const pages = document.querySelectorAll('.new-page');

            pages.forEach((page, i) => {
                page.style.position = 'relative';

                // ── Signature image stamp ──
                const stamp = document.createElement('div');
                stamp.className = 'signature-stamp';

                const img = document.createElement('img');
                img.src = dataUrl;
                img.style.cssText = 'width:100%;height:100%;object-fit:contain;object-position:left bottom;';
                stamp.appendChild(img);
                page.appendChild(stamp);

                // ── Label below signature ──
                const label = document.createElement('div');
                label.className = 'signature-label';
                label.innerHTML = 'Investor Signature<br>Page ' + (i + 1) + ' of ' + pages.length;
                page.appendChild(label);
            });

            toastr.success('Signature applied to all ' + pages.length + ' pages.');
        }
    </script>
</body>

</html>
