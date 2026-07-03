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

            p {
                margin: 4px;
                margin-bottom: 1rem;
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
                    padding: 29mm 16mm 48mm 16mm !important;
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

                <table id="all-rows-source" style="display:none; width:100%; border-collapse:collapse;" border="0"
                    cellpadding="0" cellspacing="0">


                    <tr data-row>
                        <td colspan="2" align="center">
                            <p class="text-lg">
                                ADDENDUM TO MUDARABAH AGREEMENT<br />
                                (ملحق لاتفاقية المضاربة)
                            </p>
                        </td>
                    </tr>


                    <tr data-row style="background-color:#F2F2F2">
                        <td width="50%" height="30" style="border:1px solid #ccc;">
                            <div class="english text-md" style="margin-top:6px;">ADDENDUM TO MUDARABAH AGREEMENT
                            </div>
                        </td>
                        <td width="50%" height="30" style="border:1px solid #ccc;">
                            <div class="arabic text-md" style="margin-top:6px;">ملحق لاتفاقية المضاربة
                            </div>
                        </td>
                    </tr>


                    <tr data-row>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-sm">This Addendum (“Addendum”) is made on this {investment_long_date_eng}
                                    and shall form an integral part of the Mudarabah Investment Agreement originally
                                    executed on {mudarabah_created_long_date_eng} (“Original Agreement”) between:</p>
                                <p class="text-sm">{company_name_eng}, a company duly incorporated and existing under
                                    the laws of the United Arab Emirates, having license number {company_license} and
                                    registration
                                    no. {company_reg}, hereinafter referred to as the “Company” or the “Mudarib”;</p>
                                <p class="text-sm">AND</p>
                                <p class="text-sm">{investor_name_eng}, holder of Investor ID {id_number}, hereinafter
                                    referred to as the “Investor” or the
                                    “Rabb-ul-Maal”.</p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-sm">أُبرم هذا الملحق («الملحق») في هذا اليوم {investment_long_date_ar}،
                                    ويُعد جزءاً لا يتجزأ من اتفاقية استثمار المضاربة المبرمة أصلاً في
                                    {mudarabah_created_long_date_ar} («الاتفاقية الأصلية») بين:</p>
                                <p class="text-sm">شركة {company_name_ar}، وهي شركة مؤسسة وقائمة
                                    حسب الأصول بموجب قوانين دولة الإمارات العربية المتحدة، وتحمل رقم الرخصة 1465937 ورقم
                                    التسجيل 2520181، ويُشار إليها فيما بعد بـ«الشركة» أو «المضارب»؛
                                </p>
                                <p class="text-sm">و</p>
                                <p class="text-sm">السيد/السيده {investor_name_ar} حامل بطاقة ا هوية المستثمر رقم
                                    {id_number}
                                    ويشار إليه فيما بعد بـ«المستثمر» أو «رب المال».
                                </p>
                            </div>
                        </td>
                    </tr>

                    <tr data-row style="background-color:#F2F2F2">
                        <td width="50%" height="20" style="border:1px solid #ccc;">
                            <div class="english text-md">1. Purpose</div>
                        </td>
                        <td width="50%" height="20" style="border:1px solid #ccc;">
                            <div class="arabic text-md"><strong>1 - </strong> الغرض</div>
                        </td>
                    </tr>

                    <tr data-row>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-sm">1.1 The Investor has previously invested AED
                                    {tot_prev_invested_amount}/-
                                    ({tot_prev_invested_amount_eng}) with the Company, pursuant to the Original
                                    Agreement
                                    (and
                                    addendum(s), if any).</p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-sm"><span>1-1 </span> سبق للمستثمر أن استثمر مبلغاً قدره
                                    {tot_prev_invested_amount}/- درهم
                                    إماراتي ({tot_prev_invested_amount_ar }) لدى الشركة ، وذلك بموجب الاتفاقية الأصلية
                                    والملحق أو
                                    الملاحق ، إن
                                    وجدت.</p>
                            </div>
                        </td>
                    </tr>

                    <tr data-row>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-sm">1.2 The Investor now desires to contribute an additional investment
                                    amount of AED {current_invested_amount}/- ({current_invested_amount_eng})
                                    (“Additional
                                    Capital”).
                                </p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-sm"><span>2-1 </span>يرغب المستثمر حالياً في تقديم مبلغ استثماري إضافي
                                    قدره {current_invested_amount}/- درهم إماراتي ({current_invested_amount_ar}) («رأس
                                    مال إضافي»).</p>
                            </div>
                        </td>
                    </tr>

                    <tr data-row>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-sm">1.3 The Parties agree that the Additional Capital shall form part of
                                    the Investor’s aggregate investment with the Company and shall be governed by the
                                    terms and conditions of the Original Agreement, mutatis mutandis, unless expressly
                                    modified by this Addendum.
                                </p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-sm"><span>3-1 </span>يتفق الطرفان على أن رأس المال الإضافي يُعد جزءاً من
                                    إجمالي استثمار المستثمر لدى الشركة ، ويخضع لشروط وأحكام الاتفاقية الأصلية ، مع إجراء
                                    مايلزم من تعديلات بما يتناسب مع السياق ، ما لم يُنص صراحةً في هذا الملحق على خلاف
                                    ذلك.</p>
                            </div>
                        </td>
                    </tr>

                    <tr data-row style="background-color:#F2F2F2">
                        <td width="50%" height="20" style="border:1px solid #ccc;">
                            <div class="english text-md">2. Revised Aggregate Investment</div>
                        </td>
                        <td width="50%" height="20" style="border:1px solid #ccc;">
                            <div class="arabic text-md"><strong>2 - </strong> إجمالي الاستثمار المعدّل</div>
                        </td>
                    </tr>

                    <tr data-row>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-sm">2.1 Upon receipt of the Additional Capital by the Company, the
                                    Investor’s aggregate investment shall stand revised to AED
                                    {new_total_investment_amount}
                                    ({new_total_investment_amount_eng}). All investments made and/or Partial Withdrawals
                                    made by
                                    the Investor under the original Agreement and subsequent addendum(s) and/or
                                    Withdrawal request form(s), are mentioned in the Revised Capital Schedule i.e.,
                                    Annexure-A.</p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-sm"><span>1-2 </span>عند استلام الشركة لرأس المال الإضافي، يُعد إجمالي
                                    استثمار المستثمر
                                    معدلاً ليصبح {new_total_investment_amount} درهم إماراتي
                                    ({new_total_investment_amount_ar}).وتُبيَّن جميع الاستثمارات التي قام
                                    بها المستثمر و/أو السحوبات الجزئية التي أجراها بموجب الاتفاقية الأصلية والملحق أو
                                    الملاحق اللاحقة و/أو نماذج طلب السحب في جدول رأس المال المعدّل، أي الملحق (أ).</p>
                            </div>
                        </td>
                    </tr>

                    <tr data-row>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-sm">2.2 The aggregate investment reflected herein shall constitute the
                                    revised Capital of the Investor under the Original Agreement.</p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-sm"><span>2-2 </span>يشكّل إجمالي الاستثمار المبيّن في هذا الملحق رأس
                                    المال المعدّل للمستثمر بموجب الاتفاقية الأصلية.</p>
                            </div>
                        </td>
                    </tr>

                    <tr data-row style="background-color:#F2F2F2">
                        <td width="50%" height="20" style="border:1px solid #ccc;">
                            <div class="english text-md">3. Deployment Period for Additional Capital</div>
                        </td>
                        <td width="50%" height="20" style="border:1px solid #ccc;">
                            <div class="arabic text-md"><strong>3 - </strong> فترة توظيف رأس المال الإضافي</div>
                        </td>
                    </tr>

                    <tr data-row>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-sm">3.1 A deployment period of up to forty-five (45) days shall apply
                                    from the date the Additional Capital is received by the Company for purposes of
                                    identifying, structuring, and deploying the Additional Capital. The deployment
                                    period is not absolute and may, where reasonably necessary, be extended for an
                                    additional period not exceeding fifteen (15) days. During any period in which the
                                    Additional Capital remains undeployed, no profit-sharing shall be applicable.</p>

                                <p class="text-sm">If the Company is able to deploy all or any portion of the
                                    Additional Capital before
                                    expiry of the deployment period and actual profits are realized from such
                                    deployment, the Investor shall be entitled to the agreed share of actual realized
                                    profits from the date of such deployment. However, the Investor acknowledges that
                                    these realized profits, if any, shall be paid only after completion of the
                                    deployment period mentioned in Clause 3.1.</p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-sm"><strong>1 - 3</strong> تطبق فترة توظيف تصل إلى خمسة وأربعين (45)
                                    يوماً
                                    من تاريخ استلام الشركة لرأس المال الإضافي، وذلك لأغراض تحديد وهيكلة وتوظيف رأس المال
                                    الإضافي. ولا تعتبر فترة التوظيف مطلقة، ويجوز، عند الضرورة المعقولة، تمديدها لمدة
                                    إضافية لا تتجاوز خمسة عشر (15) يوماً. وخلال أي فترة يبقى فيها رأس المال الإضافي غير
                                    موظف، لا تسري أي مشاركة في الأرباح </p>

                                <p class="text-sm">
                                    إذا تمكنت الشركة من توظيف كامل رأس المال الإضافي أو أي جزء منه قبل انتهاء فترة
                                    التوظيف وتم تحقيق أرباح فعلية من ذلك التوظيف، فيحق للمستثمر الحصول على حصته المتفق
                                    عليها من الأرباح الفعلية المحققة اعتباراً من تاريخ ذلك التوظيف. ومع ذلك، يقر
                                    المستثمر بأن هذه الأرباح المحققة، إن وجدت، لن يتم دفعها إلا بعد انتهاء فترة التوظيف
                                    المشار إليها في البند 1.3 .
                                </p>
                            </div>
                        </td>
                    </tr>

                    <tr data-row>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-sm">3.2 After deployment of the Additional Capital, the Company shall
                                    calculate and disburse the Investor’s share of actual realized profits on a monthly
                                    basis, or as otherwise mutually agreed between the Parties, subject to final
                                    reconciliation of accounts.</p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-sm"><strong>2 - 3</strong> بعد توظيف رأس المال الإضافي، تقوم الشركة
                                    باحتساب وصرف حصة المستثمر
                                    من الأرباح الفعلية المحققة على أساس شهري، أو وفقاً لما يتفق عليه الطرفان خلاف ذلك،
                                    وذلك مع مراعاة التسوية النهائية للحسابات.</p>
                            </div>
                        </td>
                    </tr>

                    <tr data-row style="background-color:#F2F2F2">
                        <td width="50%" height="20" style="border:1px solid #ccc;">
                            <div class="english text-md">4. Profit-Sharing</div>
                        </td>
                        <td width="50%" height="20" style="border:1px solid #ccc;">
                            <div class="arabic text-md"><strong>4 - </strong> المشاركة في الأرباح</div>
                        </td>
                    </tr>


                    <tr data-row>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-sm">4.1 After expiry of the applicable deployment period, the Additional
                                    Capital shall participate in profit-sharing in accordance with the profit-sharing
                                    arrangement applicable under the Original Agreement, unless otherwise expressly
                                    agreed in writing by the Parties.</p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-sm"><strong>1 - 4</strong> بعد انتهاء فترة التوظيف المعمول بها، يشارك
                                    رأس
                                    المال الإضافي في
                                    الأرباح وفقاً لترتيب المشاركة في الأرباح الساري بموجب الاتفاقية الأصلية، ما لم يتفق
                                    الطرفان صراحةً وخطياً على خلاف ذلك.</p>
                            </div>
                        </td>
                    </tr>

                    <tr data-row style="background-color:#F2F2F2">
                        <td width="50%" height="20" style="border:1px solid #ccc;">
                            <div class="english text-md">5. Confirmation of Continuity</div>
                        </td>
                        <td width="50%" height="20" style="border:1px solid #ccc;">
                            <div class="arabic text-md"><strong>5 - </strong> تأكيد الاستمرارية</div>
                        </td>
                    </tr>

                    <tr data-row>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-sm">5.1 Except to the extent modified by this Addendum, all terms,
                                    conditions, covenants, obligations, representations, warranties, indemnities,
                                    rights, limitations, and dispute resolution provisions contained in the Original
                                    Agreement shall remain valid, binding, and enforceable.</p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-sm"><strong>1 - 5</strong> باستثناء ما يتم تعديله صراحةً بموجب هذا
                                    الملحق، تظل جميع الشروط
                                    والأحكام والتعهدات والالتزامات والإقرارات والضمانات والتعويضات والحقوق والقيود
                                    وأحكام تسوية المنازعات الواردة في الاتفاقية الأصلية صحيحة وملزمة وواجبة النفاذ.</p>
                            </div>
                        </td>
                    </tr>

                    <tr data-row>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-sm">5.2 This Addendum shall be read together with the Original Agreement
                                    and shall not constitute a novation, replacement, termination, waiver, or release of
                                    the Original Agreement.</p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-sm"><strong>2 - 5</strong> يُقرأ هذا الملحق مع الاتفاقية الأصلية، ولا
                                    يُعد تجديداً أو استبدالاً أو إنهاءً أو تنازلاً أو إبراءً من الاتفاقية الأصلية.</p>
                            </div>
                        </td>
                    </tr>

                    <tr data-row style="background-color:#F2F2F2">
                        <td width="50%" height="20" style="border:1px solid #ccc;">
                            <div class="english text-md">6. Governing Law</div>
                        </td>
                        <td width="50%" height="20" style="border:1px solid #ccc;">
                            <div class="arabic text-md"><strong>6 - </strong> القانون الواجب التطبيق</div>
                        </td>
                    </tr>


                    <tr data-row>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-sm">6.1 This Addendum shall be governed by and construed in accordance
                                    with the laws applicable in the Emirate of Dubai and the federal laws of the United
                                    Arab Emirates.</p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-sm"> <strong>1 - 6</strong> يخضع هذا الملحق ويفسَّر وفقاً للقوانين
                                    السارية
                                    في إمارة دبي والقوانين الاتحادية لدولة الإمارات العربية المتحدة. </p>
                            </div>
                        </td>
                    </tr>

                    <tr data-row style="background-color:#F2F2F2">
                        <td width="50%" height="20" style="border:1px solid #ccc;">
                            <div class="english text-md">7. Counterparts and Electronic Signature</div>
                        </td>
                        <td width="50%" height="20" style="border:1px solid #ccc;">
                            <div class="arabic text-md"><strong>7 - </strong> النسخ والتوقيع الإلكتروني</div>
                        </td>
                    </tr>

                    <tr data-row>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-sm">7.1 This Addendum may be executed in counterparts and by electronic
                                    or scanned signatures, each of which shall be deemed an original.</p>
                                <p class="text-sm">IN WITNESS WHEREOF, the parties hereto have executed this Addendum
                                    on the date first written above.</p>
                                <p class="text-sm">{company_name_eng}</p>
                                <p class="text-sm">Sign:</p><br>
                                <p class="text-sm">Authorized Signatory</p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-sm"><strong>1 - 7</strong> يجوز توقيع هذا الملحق على عدة نسخ وباستخدام
                                    التوقيعات الإلكترونية أو النسخ الممسوحة ضوئياً، وتُعد كل نسخة منها أصلا.</p>

                                <p class="text-sm">وإشهاداً علىما تقدم، قام الطرفان بتوقيع هذا الملحقاعتباراً من
                                    التاريخ المذكور في صدر هذه الاتفاقية. </p>
                                <p class="text-sm">شركة {company_name_ar}</p>
                                <p class="text-sm">التوقيع :</p><br>
                                <p class="text-sm">المفوض بالتوقيع</p>
                            </div>
                        </td>
                    </tr>

                    <tr data-row>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-sm">Sign:</p><br>
                                <p class="text-sm">Investor: {investor_name_eng}</p>
                                <p class="text-sm">Date: </p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-sm">التوقيع :</p><br>
                                <p class="text-sm">المستثمر: {investor_name_ar}</p>
                                <p class="text-sm">التاريخ: </p>
                            </div>
                        </td>
                    </tr>


                    <tr data-row data-force-page="true">
                        <td colspan="2" style="padding:0;">
                            <table width="100%" align="center" class="mt-15" cellpadding="0" cellspacing="0"
                                style="max-width:100%;">

                                <tr>
                                    <td colspan="2" align="center">
                                        <p class="text-md" style="margin-bottom: 0rem">
                                            (ANNEXURE -A /
                                            الملحق أ)
                                        </p>
                                        <p class="text-md">
                                            (REVISED INVESTMENT STATEMENT / بيان الاستثمار المعدّل)
                                        </p>
                                    </td>
                                </tr>

                                <tr>
                                    <td width="50%" style="border:1px solid #ccc;">
                                        <div class="english">
                                            <p class="text-sm">
                                                ANNEXURE -A
                                                (REVISED INVESTMENT STATEMENT)
                                            </p>
                                        </div>
                                    </td>
                                    <td width="50%" style="border:1px solid #ccc;">
                                        <div class="arabic">
                                            <p class="text-sm">
                                                الملحق (أ)
                                                (بيان الاستثمار المعدّل)
                                            </p>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td width="50%" style="border:1px solid #ccc;">
                                        <div class="english">
                                            <p class="text-sm">
                                                S. No. | Particulars | Amount (AED) | Received/Paid on | Date of
                                                Document
                                            </p>
                                        </div>
                                    </td>
                                    <td width="50%" style="border:1px solid #ccc;">
                                        <div class="arabic">
                                            <p class="text-sm">
                                                الرقم التسلسلي | البيان | المبلغ (درهم إماراتي) | تاريخ الاستلام/الدفع |
                                                تاريخ المستند
                                            </p>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td width="50%" style="border:1px solid #ccc;">
                                        <div class="english">
                                            <p class="text-sm">
                                                1 | Original Investment | ___________ | ______ | ______
                                            </p>
                                        </div>
                                    </td>
                                    <td width="50%" style="border:1px solid #ccc;">
                                        <div class="arabic">
                                            <p class="text-sm">
                                                1 | الاستثمار الأصلي | ____ | ____ | ____
                                            </p>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td width="50%" style="border:1px solid #ccc;">
                                        <div class="english">
                                            <p class="text-sm">
                                                2 | Partial Withdrawal | ___________ | ______ | ______
                                            </p>
                                        </div>
                                    </td>
                                    <td width="50%" style="border:1px solid #ccc;">
                                        <div class="arabic">
                                            <p class="text-sm">
                                                2 | سحب جزئي | ____ | ____ | ____
                                            </p>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td width="50%" style="border:1px solid #ccc;">
                                        <div class="english">
                                            <p class="text-sm">
                                                3 | Additional Investment | ___________ | ______ | ______
                                            </p>
                                        </div>
                                    </td>
                                    <td width="50%" style="border:1px solid #ccc;">
                                        <div class="arabic">
                                            <p class="text-sm">
                                                3 | استثمار إضافي | ____ | ____ | ____
                                            </p>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td width="50%" style="border:1px solid #ccc;">
                                        <div class="english">
                                            <p class="text-sm">
                                                5 | Total Revised Capital | ___________ | ______ | ______
                                            </p>
                                        </div>
                                    </td>
                                    <td width="50%" style="border:1px solid #ccc;">
                                        <div class="arabic">
                                            <p class="text-sm">
                                                5 | إجمالي رأس المال المعدّل | _____ | _____
                                            </p>
                                        </div>
                                    </td>
                                </tr>

                            </table>
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
                    <p style="font-size:11px;color:#888;margin-top:4px;">Preview — <a href="#"
                            id="sigChangeFile" style="color:#007bff;">change file</a></p>
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
        .file-content { padding: 29mm 16mm 48mm 16mm !important; }
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
