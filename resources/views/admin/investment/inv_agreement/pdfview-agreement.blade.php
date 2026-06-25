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
                                PROFIT-SHARING INVESTMENT AGREEMENT<br />
                                (اتفاقية استثمار بالمشاركة في الأرباح)
                            </p>
                        </td>
                    </tr>


                    <tr data-row style="background-color:#F2F2F2">
                        <td width="50%" height="30" style="border:1px solid #ccc;">
                            <div class="english text-md" style="margin-top:6px;">PROFIT-SHARING INVESTMENT AGREEMENT
                            </div>
                        </td>
                        <td width="50%" height="30" style="border:1px solid #ccc;">
                            <div class="arabic text-md" style="margin-top:6px;">اتفاقية استثمار بالمشاركة في الأرباح
                            </div>
                        </td>
                    </tr>


                    <tr data-row>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-sm">This Profit-sharing Investment Agreement ("Agreement") is entered
                                    into on this {mudarabah_created_long_date_eng} by and between:</p>
                                <p class="text-sm">{company_name_eng}, a Company duly incorporated and existing under
                                    the laws of United Arab Emirates, having license number {company_license} and
                                    registration no.
                                    {company_reg} (hereinafter referred to as the "Company" or "Mudarib")</p>
                                <p class="text-sm">AND</p>
                                <p class="text-sm">{investor_name_eng} resident of {resident_country_eng}, having
                                    Investor ID no. {id_number}, (hereinafter referred to as the "Investor" or
                                    "Rabb-ul-Maal")</p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-sm">إنه في يوم {mudarabah_created_long_date_ar} أُبرمت اتفاقية الاستثمار
                                    بمشاركة الربح هذه ("الاتفاقية") وذلك بين كل من :</p>
                                <p class="text-sm">شركة {company_name_ar}، وهي شركة مؤسسة وقائمة أصولاً بموجب
                                    قوانين دولة الإمارات العربية المتحدة، وتحمل الرخصة رقم {company_license} ورقم
                                    التسجيل {company_reg}،
                                    (ويُشار إليها فيما بعد بـ "الشركة" أو "المضارب ")</p>
                                <p class="text-sm">و</p>
                                <p class="text-sm">{investor_name_ar}المقيم في {resident_country_ar} العربية المتحدة،
                                    ويحمل
                                    هوية المستثمر رقم. {id_number}، (والذي يُشار إليه فيما بعد بـ
                                    "المستثمر" أو "رب المال ").</p>
                            </div>
                        </td>
                    </tr>


                    <tr data-row>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-sm">Whereas the Investor wishes to invest with the Company in lawful
                                    commercial opportunities.</p>
                                <p class="text-sm">Whereas the Company agrees to accept the investment on a
                                    profit-sharing basis, in accordance with the principles of Mudarabah.</p>
                                <p class="text-sm">NOW, THEREFORE, in consideration of the mutual covenants contained
                                    herein, the parties hereto agree as follows:</p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-sm">حيث يرغب المستثمر في الاستثمار مع الشركة في فرص تجارية مشروعة .</p>
                                <p class="text-sm">وحيث توافق الشركة على قبول الاستثمار على أساس مشاركة الربح،وفقاً
                                    لمبادئ المضاربة .</p>
                                <p class="text-sm">وعليه، ولقاء التعهدات المتبادلة التي نصت عليها هذه الاتفاقية فقداتفق
                                    الطرفان على ما يلي :</p>
                            </div>
                        </td>
                    </tr>


                    <tr data-row style="background-color:#F2F2F2">
                        <td width="50%" height="20" style="border:1px solid #ccc;">
                            <div class="english text-md">1. Investment Terms</div>
                        </td>
                        <td width="50%" height="20" style="border:1px solid #ccc;">
                            <div class="arabic text-md"><strong>1 - </strong> شروط الاستثمار</div>
                        </td>
                    </tr>


                    <tr data-row>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-sm">1.1 The Investor shall transfer AED {invested_amount}/-
                                    ({invested_amount_eng})
                                    as investment capital (the "Capital") via bank transfer/Cash/Cheques to the
                                    Company's designated bank account. The Investor confirms that these funds are
                                    legally sourced and not subject to any regulatory or judicial restrictions.</p>
                                <p class="text-sm">Company Bank Details</p>
                                <p class="text-sm">Account Name: {company_name_eng}<br>
                                    Bank Name: {company_bank_eng}<br>
                                    Currency: AED<br>
                                    Account No.: {company_account_no}<br>
                                    IBAN: {company_iban}</p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-sm"><span>1-1 </span>يلتزم المستثمر بتحويل مبلغ وقدره
                                    {invested_amount}/- درهم
                                    إماراتي ({invested_amount_ar}) كرأس مال استثماري ("رأس المال") ، عن طريق التحويل
                                    البنكي / النقد
                                    / الشيكات إلى الحساب البنكي المخصص للشركة. ويؤكد المستثمر أن هذه الأموال ذات مصدر
                                    مشروع وغير خاضعة لأي قيود تنظيمية أو قضائية.</p>
                                <p class="text-sm">تفاصيل الحساب البنكي للشركة</p>
                                <p class="text-sm"><strong>اسم الحساب: </strong>شركة {company_name_ar}</p>
                                <p class="text-sm"><strong>اسم البنك : </strong>{company_bank_ar}</p>
                                <p class="text-sm"><strong>العملة: </strong>الدرهم الإماراتي</p>
                                <p class="text-sm"><strong>رقم الحساب: </strong>{company_account_no}</p>
                                <p class="text-sm"><strong>رقم الحساب المصرفي الدولي (آيبان) :
                                    </strong>{company_iban}</p>
                            </div>
                        </td>
                    </tr>


                    <tr data-row>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-sm">1.2 The Company shall invest the Capital in lawful and
                                    Shariah-compliant commercial projects relating to real estate rental properties. The
                                    Investor agrees that, during the initial term of this Agreement or any renewed term
                                    thereof, the Company may, at its discretion, redeploy or reinvest the Capital into
                                    other similar lawful and Shariah-compliant real estate rental projects. For the
                                    avoidance of doubt, the Company shall not invest the Capital in any separate
                                    Mudarabah arrangement, sub-Mudarabah structure, or profit-sharing investment
                                    arrangement with any third party.</p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-sm"><span>2-1</span> تستثمر الشركة رأس المال في مشاريع تجارية مشروعة
                                    ومتوافقة مع أحكام الشريعة الإسلامية تتعلق بعقارات التأجير. يوافق المستثمر على أنه
                                    يجوز للشركة خلال المدة الأولية لهذه الاتفاقية أو أي مدة مجددة لها، ووفقاً لتقديرها
                                    المنفرد، إعادة توظيف أو إعادة استثمار رأس المال في مشاريع أخرى مماثلة تتعلق بعقارات
                                    التأجير ، شريطة أن تكون تلك المشاريع مشروعة ومتوافقة مع أحكام الشريعة الإسلامية.
                                    ولتجنب الشك، لايجوزللشركة استثمار رأس المال في أي ترتيب مضاربة مستقل أو هيكل مضاربة
                                    فرعية أو أي ترتيب استثماري قائم على مشاركة الربح مع أي طرف ثالث .</p>
                            </div>
                        </td>
                    </tr>


                    <tr data-row>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-sm">1.3 The Investor understands and agrees that:<br>
                                    <b>.</b> There is no guaranteed or fixed return or profit on the capital or
                                    otherwise.<br>
                                    <b>.</b> There is a risk of Capital Loss.<br>
                                    <b>.</b> All profit distributions are strictly contingent upon actual or
                                    constructive realization of profits in accordance with Shariah principles.<br>
                                    <b>.</b> Losses, if incurred, shall be solely borne by the Investor to the extent of
                                    the capital contribution. The Company shall be liable for losses in case of gross
                                    negligence, willful misconduct or breach of this Agreement.
                                </p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-sm"><span>1-3</span> يدرك المستثمر ويوافق على ما يلي:
                                    <br><b>.</b> ليس هناك أي عائد أو ربح مضمون أو ثابت على رأس المال أو خلافه .
                                    <br><b>.</b> هناك مخاطر خسارة رأس المال.
                                    <br><b>.</b> تكون جميع توزيعات الأرباح مشروطة بشكل صارم بتحقق الأرباح وجوباً أو
                                    حكماً وفقاً لمبادئ الشريعة.
                                    <br><b>.</b> يتحمل المستثمر وحده الخسائر، إن وجدت، في حدود مساهمته في رأس المال.
                                    وتكون الشركة مسؤولة عن الخسائر في حال الإهمال الجسيم أو سوء السلوك المتعمد أو
                                    الإخلال بالاتفاقية.
                                </p>
                            </div>
                        </td>
                    </tr>


                    <tr data-row style="background-color:#F2F2F2">
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-md" style="font-weight:700 !important;">2. Profit-Sharing and Ceiling
                                    Arrangement</p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-md" style="font-weight:700 !important;"><strong>2 - </strong>ترتيب
                                    تقاسم الأرباح و الحد الأقصى</p>
                            </div>
                        </td>
                    </tr>


                    <tr data-row>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-sm">2.1 The Net Profit derived from the Capital shall be distributed
                                    between the Parties as follows:</p>
                                <p class="text-sm">(a) <i>Profit up to Ceiling</i> – Where the total annual Net Profit
                                    is equal to or less than an amount equivalent to fifty percent (50%) of the Capital
                                    contributed by the Investor, such Net Profit shall be distributed as follows:</p>

                                <p class="text-sm">(i) Forty percent ({inv_profit_perc}%) to the Investor; and</p>
                                <p class="text-sm">(ii) Sixty percent ({company_profit_perc}%) to the Company.</p>
                                <p class="text-sm">(b) <i>Discretionary Additional Profit</i> – The Company may, at its
                                    sole discretion, allocate to the Investor a profit share higher than forty percent
                                    ({inv_profit_perc}%) of the Net Profit, provided that such additional allocation is
                                    voluntary and
                                    shall not create any recurring right, entitlement, guarantee, or precedent in favour
                                    of the Investor.</p>
                                <p class="text-sm">(c) <i>Profit Above Ceiling</i> – Where the total annual Net Profit
                                    exceeds an amount equivalent to fifty percent (50%) of the Capital contributed by
                                    the Investor, the Investor's entitlement shall, unless the Company voluntarily
                                    decides otherwise under Clause 2.1(b), be limited to Forty percent
                                    ({inv_profit_perc}%) of such
                                    fifty percent (50%) profit ceiling. Any Net Profit exceeding the said ceiling shall
                                    be allocated to the Company.</p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-sm"><span>1-2</span> يُوزَّع صافي الربح الناتج عن رأس المال بين الطرفين
                                    على النحو الآتي:</p>
                                <p class="text-sm">(أ) الربح حتى حد الربح الأقصى – إذا كان إجمالي صافي الربح السنوي
                                    مساوياً أو أقل من مبلغ يعادل خمسين بالمائة (50%) من رأس المال الذي ساهم به المستثمر،
                                    فيوزع صافي الربح على النحو التالي:</p>
                                <p class="text-sm"><span>(1) </span>أربعون بالمائة ({inv_profit_perc}%) للمستثمر؛ و</p>
                                <p class="text-sm"><span>(2) </span>ستون بالمائة ({company_profit_perc}%) للشركة</p>
                                <p class="text-sm">(ب) ربح إضافي تقديري – يجوز للشركة، وفقاً لتقديرها المنفرد والمطلق،
                                    أن تخصص للمستثمر حصة من الأرباح تزيد على أربعين بالمائة ({inv_profit_perc}%) من صافي
                                    الربح، على أن
                                    يكون هذا التخصيص الإضافي اختيارياً وطوعياً بالكامل، وألا يترتب عليه أو ينشئ أي حق
                                    مكتسب أو استحقاق متكرر أو ضمان أو سابقة قانونية أو تعاقدية لصالح المستثمر.</p>
                                <p class="text-sm">(ج) الربح الزائد عن الحد الأقصى – إذا تجاوز إجمالي صافي الربح السنوي
                                    مبلغاً يعادل خمسين بالمائة (50%) من رأس المال الذي ساهم به المستثمر، فيقتصر استحقاق
                                    المستثمر، ما لم تقرر الشركة خلاف ذلك طوعاً بموجب البند 2-1(ب)، على أربعين بالمائة
                                    ({inv_profit_perc}%) من حد الربح البالغ خمسين بالمائة (50%) من رأس المال. ويؤول أي
                                    صافي ربح يتجاوز
                                    الحد المذكور بالكامل إلى الشركة.</p>
                            </div>
                        </td>
                    </tr>


                    <tr data-row>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-sm">2.2 Net Profit means gross revenue actually received or
                                    constructively realised from the relevant investment activity, less direct and
                                    reasonable expenses incurred for that activity, including rent, utilities,
                                    maintenance, furnishing, staff or service costs, brokerage, marketing, government
                                    fees, taxes, bank charges, and other documented operational expenses. No personal,
                                    unrelated, excessive, undocumented, or non-commercial expense shall be deducted from
                                    Net Profit.</p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-sm"><span>2-2</span> يقصد بمصطلح صافي الربح إجمالي الإيرادات المقبوضة
                                    وجوباً أو المحققة حكماً من النشاط الاستثماري ذي الصلة، بعد خصم المصروفات المباشرة
                                    والمعقولة المنفقة لذلك النشاط، بما في ذلك الإيجار والمرافق والصيانة والتأثيث وتكاليف
                                    الموظفين أو الخدمات والوساطة والتسويق والرسوم الحكومية و الضرائب والرسوم البنكية وأي
                                    مصرفات تشغيلية أخرى موثقة. ولايجوز خصم أي مصرفات شخصية أو غير ذات صلة أو مفرطة أو
                                    غير موثقة أو غير تجارية من صافي الربح.</p>
                            </div>
                        </td>
                    </tr>


                    <tr data-row style="background-color:#F2F2F2">
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-md" style="font-weight:700 !important;">3. Deployment Period and Profit
                                    Disbursement:</p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-md" style="font-weight:700 !important;"><strong>3 - </strong> فترة
                                    توظيف رأس المال وصرف الأرباح:</p>
                            </div>
                        </td>
                    </tr>


                    <tr data-row>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-sm">3.1 A deployment period of up to forty-five (45) days shall apply
                                    from the date the Capital is received by the Company for purposes of identifying,
                                    structuring, and deploying the Capital. The deployment period is not absolute and
                                    may, where reasonably necessary, be extended for an additional period not exceeding
                                    fifteen (15) days. During any period in which the Capital remains undeployed, no
                                    profit-sharing shall be applicable.</p>
                                <p class="text-sm">If the Company deploys all or any portion of the Capital before
                                    expiry of the deployment period and actual profits are realized from such
                                    deployment, the Investor shall be entitled to the agreed share of actual realized
                                    profits from the date of such deployment. However, the Investor acknowledges that
                                    these realized profits, if any, shall be paid only after completion of the
                                    deployment period mentioned in Clause 3.1.</p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-sm"><span>1-3</span> تطبق فترة توظيف تصل إلى خمسة وأربعين (45) يوماً من
                                    تاريخ استلام الشركة لرأس المال، وذلك لأغراض تحديد وهيكلة وتوظيف رأس المال. لا تعتبر
                                    فترة التوظيف مطلقة، ويجوز، عند الضرورة المعقولة تمديدها لمدة إضافية لا تتجاوز خمسة
                                    عشر (15) يوماً . خلال أي فترة يبقى فيها رأس المال غير موظف، لاتسري أي مشاركة في
                                    الأرباح.</p>
                                <p class="text-sm">إذا قامت الشركة بتوظيف كامل رأس المال أو أي جزء منه قبل انتهاء فترة
                                    التوظيف وتم تحقيق أرباح فعلية من ذلك التوظيف، فيحق للمستثمر الحصول على حصته المتفق
                                    عليها من الأرباح الفعلية المحققة اعتباراً من تاريخ ذلك التوظيف.</p>
                                <p class="text-sm">و مع ذلك ، يقر المستثمر بأن هذه الأرباح المحققة، إن وجدت، لن يتم
                                    دفعها إلا بعد انتهاء فترة التوظيف المشار إليها في البند 3-1.</p>
                            </div>
                        </td>
                    </tr>


                    <tr data-row>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-sm">3.2 After deployment of the Capital, the Company shall calculate and
                                    disburse the Investor's share of actual realized profits on a monthly basis, or as
                                    otherwise mutually agreed between the Parties, subject to final reconciliation of
                                    accounts.</p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-sm">2-3 بعد توظيف رأس المال، تقوم الشركة باحتساب وصرف حصة المستثمر من
                                    الأرباح الفعلية المحققة على أساس شهري، أو وفقاً لما يتفق عليه الطرفان خلاف ذلك، وذلك
                                    مع مراعاة التسوية النهائية للحسابات.</p>
                            </div>
                        </td>
                    </tr>


                    <tr data-row style="background-color:#F2F2F2">
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-md" style="font-weight:700 !important;">4. Representations &amp;
                                    Warranties</p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-md" style="font-weight:700 !important;"><strong>4 - </strong> الإقرارات
                                    والضمانات</p>
                            </div>
                        </td>
                    </tr>


                    <tr data-row>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-sm">4.1 The Investor acknowledges and agrees that the Company shall act
                                    as the Mudarib under this Agreement and shall manage, deploy, administer, and
                                    operate the investment Capital in Shariah-compliant commercial activities on a
                                    profit-sharing basis.</p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-sm"><span>1-4</span> يقر المستثمر ويوافق على أن الشركة تعمل بصفتها
                                    المضارب بموجب هذه الاتفاقية، وتتولى إدارة وتوظيف وتشغيل رأس المال الاستثماري في
                                    أنشطة تجارية متوافقة مع الشريعة الإسلامية على أساس مشاركة الربح.</p>
                            </div>
                        </td>
                    </tr>


                    <tr data-row>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-sm">4.2 The Investor further acknowledges that the Company is not a
                                    lender, deposit-taking institution, fund, securities company, portfolio manager,
                                    financial adviser, or regulated investment management company. The Company is not
                                    regulated or authorised by the UAE Securities and Commodities Authority, Dubai
                                    Financial Services Authority, Central Bank of the UAE, or any other financial
                                    services regulator to carry out regulated financial services activities.</p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-sm"><span>2-4</span> يقر المستثمر كذلك بأن الشركة ليست مقرضاًُ أو مؤسسة
                                    قبول ودائع أو صندوقاً أو شركة أوراق مالية أو مدير محافظ أو مستشاراًُ مالياً أو شركة
                                    إدارة استثمار منظمة . كما أن الشركة ليست منظمة أو مرخصة من هيئة الأوراق المالية
                                    والسلع في دولة الإمارات العربية المتحدة أو سلطة دبي للخدمات المالية أو مصرف الإمارات
                                    العربية المتحدة المركزي أو أي جهة تنظيمية أخرى للخدمات المالية لمزاولة أنشطة الخدمات
                                    المالية المنظمة.</p>
                            </div>
                        </td>
                    </tr>



                    <tr data-row>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-sm">4.3 The Investor acknowledges and agrees that the Company has not
                                    guaranteed, promised, assured, or represented any fixed profit, minimum profit,
                                    fixed
                                    monthly return, repayment of profit, or protection of Capital.</p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-sm"> <span>3-4</span> يقر المستثمر ويوافق على أن الشركة لم تضمن أو تعد
                                    أو تؤكد
                                    أو تقدم أي تعهد بشأن أي ربح ثابت أو حد أدنى من الربح أو عائد شهري ثابت أو سداد أرباح
                                    أو
                                    حماية لرأس المال.</p>
                            </div>
                        </td>
                    </tr>


                    <tr data-row>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-sm">4.4 Any illustration, schedule, estimate, projection, expected
                                    profit
                                    figure, business plan, or discussion shared with the Investor is indicative only and
                                    shall
                                    not create any contractual entitlement, warranty, guarantee, or representation.</p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-sm"> <span>4-4</span> يكون أي بيان توضيحي أو جدول أو تقدير أو توقع أو
                                    رقم ربح
                                    متوقع أو خطة عمل أونقاش تمت مشاركته مع المستثمر لأغراض إرشادية فقط، ولا ينشئ أي
                                    استحقاق
                                    تعاقدي أو ضمان أو كفالة أو إقرار.</p>
                            </div>
                        </td>
                    </tr>


                    <tr data-row>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-sm">4.5 The Investor confirms that he/she has entered into this
                                    Agreement
                                    voluntarily, after considering the risks of the arrangement, and has not relied on
                                    any
                                    statement, discussion, projection, or assurance other than the express terms of this
                                    Agreement.</p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-sm"> <span>5-4 </span>يقر المستثمر أنه دخل في هذه الاتفاقية طوعاً، بعد
                                    النظر في
                                    مخاطر هذا الترتيب وأنه لم يعتمد على أي بيان أو نقاش أو توقع أو تأكيد بخلاف الشروط
                                    الصريحة
                                    لهذه الاتفاقية.</p>
                            </div>
                        </td>
                    </tr>


                    <tr data-row>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-sm">4.6 The Investor confirms that he/she has not entered into this
                                    Agreement on
                                    the basis of any public advertisement, public solicitation, regulated financial
                                    advice,
                                    investment recommendation, or offer made to the public. The Investor has made this
                                    investment purely out of a friendly bond with the Company owners.</p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-sm"> <span>6-4 </span>يوكد المستثمر أنه لم يدخل في هذه الاتفاقية بناءً
                                    على أي
                                    إعلان عام أو دعوة عامة أو مشورة مالية منظمة أو توصية استثمارية أو عرض مقدم للجمهور.
                                    دخل
                                    المستثمر في هذا الاستثمار حصراً بناءً على علاقة ودية مع ملاك الشركة.</p>
                            </div>
                        </td>
                    </tr>


                    <tr data-row style="background-color:#F2F2F2">
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-md" style="font-weight: 700 !important;">
                                    5. Term & Termination
                                </p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-md" style="font-weight: 700 !important;">
                                    <strong>5 - </strong> المدة والإنهاء
                                </p>
                            </div>
                        </td>
                    </tr>


                    <tr data-row>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-sm">5.1 This Agreement is valid for an initial period of 14 months from
                                    the date
                                    of the receipt of investment amount to the Company. If not terminated by either
                                    Party, the
                                    Agreement shall automatically renew for further term(s) of twelve (12) months each
                                    and shall
                                    continue to remain in force unless terminated by either Party in accordance with the
                                    provisions of this Agreement.</p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-sm"> <span>1-5 </span>تسري هذه الاتفاقية لمدة أولية قدرها أربعة عشر (14)
                                    شهراً
                                    من تاريخ استلام الشركة لمبلغ الاستثمار. وإذا لم ينهيها أي طرف من الطرفين فتتجدد
                                    الاتفاقية
                                    تلقائياً لمدة أو مدد إضافية، كل منها اثنا عشر (12) شهراً، وتظل نافذة ما لم ينهيها أي
                                    طرف من
                                    الطرفين وفقاً لما نصت عليه هذه الاتفاقية من أحكام. </p>
                            </div>
                        </td>
                    </tr>


                    <tr data-row>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-sm">5.2 Termination by Investor - The Investor may terminate this
                                    Agreement by
                                    issuing a termination notice/non-renewal notice to the Company thirty (30) days
                                    prior to the
                                    renewal date of the Agreement. In such case, the Company shall settle the accounts
                                    and pay
                                    the Investor the due profit and principal amount as per this Agreement within six
                                    (6) months
                                    of such termination notice.</p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-sm"> <span>2-5 </span>الإنهاء من جانب المستثمر - يجوز للمستثمر إنهاء هذه
                                    الاتفاقية من خلال تقديم إشعار إنهاء أو إشعار بعدم التجديد إلى الشركة قبل ثلاثين (30)
                                    يوماً
                                    من تاريخ تجديد الاتفاقية .في هذه الحالة، تقوم الشركة بتسوية الحسابات ودفع الأرباح
                                    المستحقة
                                    ومبلغ رأس المال للمستثمر وفقاً لهذه الاتفاقية خلال ستة (6) أشهر من تاريخ إشعار
                                    الإنهاء .
                                </p>
                            </div>
                        </td>
                    </tr>


                    <tr data-row>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-sm">5.3 Pre-mature Termination - The Investor may request for premature
                                    termination of the investment by giving the Company not less than thirty (30) days’
                                    prior
                                    written notice. The Company shall use reasonable commercial efforts to complete
                                    settlement
                                    as early as practicable and shall not delay settlement without genuine operational,
                                    commercial, or liquidation necessity.</p>

                                <p class="text-sm">Where the Capital has already been deployed, the Company may defer
                                    settlement until orderly liquidation, replacement of the Investor’s Capital,
                                    completion of
                                    the relevant rental or commercial cycle, or final reconciliation of accounts,
                                    provided that
                                    such period shall not exceed twelve (12) months from the date of the Investor’s
                                    withdrawal
                                    notice unless otherwise mutually agreed by the Parties. In case of premature
                                    termination,
                                    the Investor shall bear actual and reasonable operational costs, liquidation
                                    expenses,
                                    Mudarib’s service compensation, third-party charges, or direct costs necessarily
                                    incurred
                                    due to pre-mature withdrawal, in accordance with prevailing market practice and
                                    subject to
                                    mutual settlement.</p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-sm"> <span>3-5 </span> الإنهاء المبكر - يجوز للمستثمر طلب الإنهاء المبكر
                                    للاستثمار من خلال تقديم إشعار كتابي مسبق إلى الشركة قبل مدة لا تقل عن ثلاثين (30)
                                    يوماً.
                                    تبذل الشركة جهوداً تجارية معقولة لإتمام التسوية في أقرب وقت عملي ممكن، ولا يجوز لها
                                    تأخير
                                    التسوية دون وجود ضرورة تشغيلية أو تجارية أو متعلقة بالتصفية .
                                </p>

                                <p class="text-sm"> إذا كان رأس المال قد تم توظيفه بالفعل، فيجوز للشركة تأجيل التسوية
                                    إلى حين
                                    التصفية المنظمة أو استبدال رأس مال المستثمر أو إتمام دورة الإيجار أو الدورة التجارية
                                    ذات
                                    الصلة أو التسوية النهائية للحسابات، على ألا تتجاوز هذه المدة اثني عشر (12) شهراً من
                                    تاريخ
                                    إشعار السحب المقدم من المستثمر ما لم يتفق الطرفان على خلاف ذلك. في حالة الإنهاء
                                    المبكر،
                                    يتحمل المستثمر التكاليف التشغيلية الفعلية والمعقولة ومصاريف التصفية وتعويض خدمات
                                    المضارب
                                    ورسوم الأطراف الثالثة أو أي تكاليف مباشرة تنشأ بالضرورة بسبب السحب المبكر، وذلك
                                    وفقاً
                                    للممارسات السوقية السائدة وبما يخضع للتسوية المتبادلة .</p>
                            </div>
                        </td>
                    </tr>


                    <tr data-row>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-sm">5.4 Termination by Company - The Company may terminate this
                                    Agreement at any
                                    time by providing written notice to the Investor. In such case, the Company shall
                                    settle the
                                    accounts and pay the Investor the due profit and principal amount as per this
                                    Agreement
                                    within six (6) months of such termination notice.</p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-sm"> <span>4-5 </span>الإنهاء من جانب الشركة - يجوز للشركة إنهاء هذه
                                    الاتفاقية
                                    في أي وقت عن طريق تقديم إشعار كتابي إلى المستثمر. في هذه الحالة، تقوم الشركة بتسوية
                                    الحسابات
                                    ودفع الأرباح المستحقة ومبلغ رأس المال للمستثمر وفقاً لهذه الاتفاقية خلال ستة (6)
                                    أشهر من
                                    تاريخ إشعار الإنهاء .</p>
                            </div>
                        </td>
                    </tr>


                    <tr data-row>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-sm">5.5 Termination Upon Breach - Either Party may terminate this
                                    Agreement
                                    immediately if the other Party commits a material breach of its obligations and
                                    fails to
                                    remedy such breach within 30 days of receiving written notice thereof.</p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-sm"> <span>5-5 </span>الإنهاء عند الإخلال - يجوز لأي من الطرفين إنهاء
                                    هذه
                                    الاتفاقية فوراً إذا ارتكب الطرف الآخر إخلالاً جوهرياً بالتزاماته ولم يقم بمعالجة ذلك
                                    الإخلال
                                    خلال ثلاثين (30) يوماً من استلام إشعار كتابي بذلك .</p>
                            </div>
                        </td>
                    </tr>


                    <tr data-row style="background-color:#F2F2F2">
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-md" style="font-weight: 700 !important;">
                                    6. Reporting & Transparency
                                </p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-md" style="font-weight: 700 !important;">
                                    <strong>6 - </strong> التقارير والشفافية
                                </p>
                            </div>
                        </td>
                    </tr>


                    <tr data-row>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-sm">6.1 The Company shall provide the Investor with a performance
                                    summary on a
                                    monthly or quarterly basis or as otherwise agreed.</p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-sm"> <span>1-6 </span> تقدم الشركة إلى المستثمر ملخصاً عن الأداء على
                                    أساس شهري
                                    أو ربع سنوي أو وفقاً لما يتم الاتفاق عليه خلاف ذلك .</p>
                            </div>
                        </td>
                    </tr>


                    <tr data-row>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-sm">6.2 The Investor may seek reasonable clarifications or updates
                                    regarding the
                                    investment activity related to the Capital</p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-sm"> <span>2-6 </span> يجوز للمستثمر طلب إيضاحات أو تحديثات معقولة بشأن
                                    النشاط
                                    الاستثماري المتعلق برأس المال .</p>
                            </div>
                        </td>
                    </tr>


                    <tr data-row style="background-color:#F2F2F2">
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-md" style="font-weight: 700 !important;">
                                    7. Force Majeure
                                </p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-md" style="font-weight: 700 !important;">
                                    <strong>7 - </strong>القوة القاهرة
                                </p>
                            </div>
                        </td>
                    </tr>


                    <tr data-row>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-sm">7.1 If the Company is prevented or materially delayed from
                                    generating,
                                    realizing, calculating, distributing profits, or returning the Investment Capital
                                    due to any
                                    event beyond its reasonable control, including, natural calamity, geo-political
                                    crisis,
                                    regulatory restriction, or any other event affecting the company’s ability to
                                    perform its
                                    obligations under this Agreement, the Company shall be entitled to defer or suspend
                                    such
                                    payment for a reasonable period. In such case, the Investor shall allow the Company
                                    additional time and shall not raise any claim, objection, or dispute for such delay,
                                    suspension, partial payment, or deferred payment.</p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-sm"> <span>1-7 </span> إذا مُنعت الشركة أو تأخرت تأخراً جوهرياً عن توليد
                                    الأرباح
                                    أو تحقيقها أو احتسابها أو توزيعها أو إعادة رأس المال الاستثماري بسبب أي حدث خارج عن
                                    سيطرتها
                                    المعقولة، بما في ذلك الكوارث الطبيعية أو الأزمات الجيوسياسية أو القيود التنظيمية أو
                                    أي حدث
                                    آخر يؤثر في قدرة الشركة على أداء التزاماتها بموجب هذه الاتفاقية، فيحق للشركة تأجيل
                                    أو تعليق
                                    ذلك الدفع لمدة معقولة. في هذه الحالة، يمنح المستثمر الشركة وقتاً إضافياً ولا يثير أي
                                    مطالبة
                                    أو اعتراض أو نزاع بشأن ذلك التأخير أو التعليق أو الدفع الجزئي أو الدفع المؤجل .</p>
                            </div>
                        </td>
                    </tr>


                    <tr data-row style="background-color:#F2F2F2">
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-md" style="font-weight: 700 !important;">
                                    8. KYC, AML, Banking Compliance and Investor Indemnity
                                </p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-md" style="font-weight: 700 !important;">
                                    <strong>8 - </strong>متطلبات اعرف عميلك ومكافحة غسل الأموال والامتثال البنكي وتعويض
                                    المستثمر
                                </p>
                            </div>
                        </td>
                    </tr>


                    <tr data-row>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-sm">8.1 The Investor shall promptly provide all KYC, AML,
                                    source-of-funds, tax
                                    residency, banking, identification, nominee, beneficiary, and compliance documents
                                    requested
                                    by the Company or its bank.</p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-sm"> <span>1-8 </span> يلتزم المستثمر بتقديم جميع مستندات اعرف عميلك
                                    ومكافحة غسل
                                    الأموال ومصدر الأموال والإقامة الضريبية والمستندات البنكية والتعريفية ومستندات
                                    المرشح
                                    والمستفيد والامتثال التي تطلبها الشركة أو بنكها، وذلك دون إبطاء.</p>
                            </div>
                        </td>
                    </tr>


                    <tr data-row>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-sm">8.2 The Company may refuse to accept funds, suspend deployment,
                                    withhold
                                    profit distribution, delay repayment, freeze settlement, or terminate this Agreement
                                    where
                                    required or reasonably considered necessary for KYC, AML, sanctions, banking, tax,
                                    regulatory, court, or law-enforcement compliance.</p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-sm"> <span>2-8 </span> يجوز للشركة رفض قبول الأموال أو تعليق التوظيف أو
                                    حجب
                                    توزيع الأرباح أو تأخير السداد أو تجميد التسوية أو إنهاء هذه الاتفاقية متى كان ذلك
                                    مطلوباً أو
                                    اعتُبر ضرورياً بصورة معقولة لأغراض الامتثال لمتطلبات اعرفعميلك أو مكافحة غسل الأموال
                                    أو
                                    العقوبات أو المتطلبات البنكية أو الضريبية أو التنظيمية أو القضائية أو إنفاذ القانون
                                    .</p>
                            </div>
                        </td>
                    </tr>


                    <tr data-row>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-sm">8.3 The Company shall not be liable for any delay or non-payment
                                    caused by
                                    incomplete KYC, banking restrictions, compliance review, suspicious transaction
                                    concerns, or
                                    directions from any bank, regulator, authority, or court.</p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-sm"> <span>3-8 </span> لا تكون الشركة مسؤولة عن أي تأخير أو عدم دفع ناشئ
                                    عن عدم
                                    اكتمال إجراءات اعرف عميلك أو القيود البنكية أو مراجعة الامتثال أو مخاوف المعاملات
                                    المشبوهة
                                    أو توجيهات أي بنك أو جهة تنظيمية أو سلطة أو محكمة .</p>
                            </div>
                        </td>
                    </tr>


                    <tr data-row>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-sm">8.4 The Investor shall indemnify and hold harmless the Company, its
                                    shareholders, managers, officers, employees and representatives from and against any
                                    loss,
                                    claim, damage, penalty, liability, cost, expense, banking restriction, regulatory
                                    action,
                                    investigation, or third-party claim arising from or connected with:</p>

                                (a) false, incomplete, or misleading information provided by the Investor;<br>
                                (b) unlawful, restricted, sanctioned, borrowed, disputed, or third-party funds
                                introduced by the
                                Investor;<br>
                                (c) breach of the Investor’s representations, warranties, or obligations;<br>
                                (d) claims by any family member, partner, creditor, heir, nominee, or third party
                                claiming an
                                interest in the Capital; or<br>
                                (e) the Investor’s tax, reporting, or legal non-compliance.
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-sm"> <span>4-8 </span> ليلتزم المستثمر بتعويض الشركة ومساهميها ومديريها
                                    ومسؤوليها وموظفيها وممثليها وإبراء ذمتهم من وضد أي خسارة أو مطالبة أو ضرر أو غرامة
                                    أو
                                    مسؤولية أو تكلفة أو مصروف أو قيد بنكي أو إجراء تنظيمي أو تحقيق أو مطالبة من طرف ثالث
                                    تنشأ عن
                                    أو تتصل بما يلي:</p>
                                <br>(أ) أي معلومات خاطئة أو غير مكتملة أو مضللة يقدمها المستثمر؛
                                <br>(ب) أي أموال غير مشروعة أو مقيدة أو خاضعة لعقوبات أو مقترضة أو محل نزاع أو عائدة
                                لطرف ثالث
                                يقدمها المستثمر؛
                                <br>(ج) إخلال المستثمر بإقراراته أو ضماناته أو التزاماته؛
                                <br>(د) مطالبات أي فرد من العائلة أو شريك أو دائن أو وارث أو مرشح أو طرف ثالث يدعي وجود
                                مصلحة في
                                رأس
                                المال؛ أو
                                <br>(هـ) عدم امتثال المستثمر للمتطلبات الضريبية أو متطلبات الإبلاغ أو المتطلبات
                                القانونية.
                            </div>
                        </td>
                    </tr>


                    <tr data-row style="background-color:#F2F2F2">
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-md" style="font-weight: 700 !important;">
                                    9. Investor Death, Incapacity, Insolvency or Succession
                                </p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-md" style="font-weight: 700 !important;">
                                    <strong>9 - </strong>وفاة المستثمر أو عدم أهليته أو إعساره أو الخلافة
                                </p>
                            </div>
                        </td>
                    </tr>


                    <tr data-row>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-sm">9.1 In the event of the Investor’s death, legal incapacity,
                                    insolvency,
                                    bankruptcy, or any claim by heirs, nominees, family members, creditors, legal
                                    representatives, or any third party claiming through or under the Investor, the
                                    Company
                                    shall be entitled to suspend any profit distribution, capital repayment, settlement,
                                    or
                                    transfer of rights until the rightful recipient is legally verified to the Company’s
                                    reasonable satisfaction.</p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-sm"> <span>1-9 </span> في حال وفاة المستثمر أو فقدانه الأهلية القانونية
                                    أو
                                    إعساره أو إفلاسه، أو في حال وجود أي مطالبة من الورثة أو المرشحين أو أفراد العائلة أو
                                    الدائنين أو الممثلين القانونيين أو أي طرف ثالث يدعي من خلال المستثمر أو بموجبه، يحق
                                    للشركة
                                    تعليق أي توزيع للأرباح أو إعادة لرأس المال أو تسوية أو نقل للحقوق إلى أن يتم التحقق
                                    قانوناً
                                    من المستلم المستحق بما يرضي الشركة بصورة معقولة . </p>
                            </div>
                        </td>
                    </tr>


                    <tr data-row>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-sm">9.2 The Company may require succession documents, court orders,
                                    probate
                                    documents, power of attorney, no-objection letters, identification documents, KYC
                                    documents,
                                    bank details, indemnities, or any other documents reasonably required to verify the
                                    lawful
                                    recipient.</p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-sm"> <span>2-9 </span> يجوز للشركة طلب مستندات الخلافة أو أوامر المحكمة
                                    أو
                                    مستندات إثبات التركة أو الوكالة أو خطابات عدم الممانعة أو مستندات التعريف أو مستندات
                                    اعرف
                                    عميلك أو التفاصيل البنكية أو التعويضات أو أي مستندات أخرى مطلوبة بصورة معقولة للتحقق
                                    من
                                    المستلم القانوني. </p>
                            </div>
                        </td>
                    </tr>


                    <tr data-row style="background-color:#F2F2F2">
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-md" style="font-weight: 700 !important;">
                                    10. Limitation of Liability
                                </p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-md" style="font-weight: 700 !important;">
                                    <strong>10 - </strong>حدود المسؤولية
                                </p>
                            </div>
                        </td>
                    </tr>


                    <tr data-row>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-sm">10.1 The Company shall not be liable for ordinary commercial losses,
                                    market
                                    downturn, reduced rental demand, regulatory restriction, banking delay, force
                                    majeure, or
                                    any loss arising from risks inherent in the investment activity. The Company shall
                                    not be
                                    liable for indirect, consequential, special, punitive, reputational, or opportunity
                                    losses,
                                    or for loss of expected profit.</p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-sm"> <span>1-10 </span> لا تكون الشركة مسؤولة عن الخسائر التجارية
                                    العادية أو
                                    تراجع السوق أو انخفاض الطلب على الإيجارات أو القيود التنظيمية أو التأخير البنكي أو
                                    القوة
                                    القاهرة أو أي خسارة ناشئة عن المخاطر الملازمة للنشاط الاستثماري. ولا تكون الشركة
                                    مسؤولة عن
                                    الخسائر غير المباشرة أو التبعية أو الخاصة أو العقابية أو المتعلقة بالسمعة أو خسائر
                                    الفرص أو
                                    خسارة الربح المتوقع. </p>
                            </div>
                        </td>
                    </tr>


                    <tr data-row>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-sm">10.2 Nothing in this Agreement shall exclude Company’s liability for
                                    fraud,
                                    wilful misconduct, gross negligence, or liability that cannot be excluded under
                                    applicable
                                    law.</p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-sm"> <span>2-10 </span> ليس في هذه الاتفاقية ما يستبعد مسؤولية الشركة عن
                                    الاحتيال أو سوء السلوك المتعمد أو الإهمال الجسيم أو أي مسؤولية لا يجوز استبعادها
                                    بموجب
                                    القانون الواجب التطبيق. </p>
                            </div>
                        </td>
                    </tr>



                    <tr data-row style="background-color:#F2F2F2">
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-md" style="font-weight: 700 !important;">
                                    11. No Partnership, Shareholding, Agency or Management Rights
                                </p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-md" style="font-weight: 700 !important;">
                                    <strong>11 - </strong>انتفاء حقوق الشراكة أو المساهمة أو الوكالة أو الحقوق الإدارية
                                </p>
                            </div>
                        </td>
                    </tr>


                    <tr data-row>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-sm">11.1 Nothing in this Agreement shall make the Investor a
                                    shareholder,
                                    partner, director, manager, employee, agent, representative, or authorised signatory
                                    of the
                                    Company. The Investor shall have no right to bind the Company, represent the
                                    Company,
                                    participate in management, deal with landlords, tenants, suppliers, banks,
                                    regulators or
                                    authorities on behalf of the Company, or claim any ownership interest in the
                                    Company’s
                                    assets, licences, goodwill, brand, bank accounts, leases, or business.</p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-sm"> <span>1-11 </span> للا يترتب على أي حكم وردفي هذه الاتفاقية اعتبار
                                    المستثمر
                                    مساهماً أو شريكاً أو مديراً أو عضواً إدارياً أو موظفاً أو وكيلاً أو ممثلاً أو مفوض
                                    توقيع عن
                                    الشركة. ولا يكون للمستثمر أي حق في إلزام الشركة أو تمثيلها أو المشاركة في إدارتها أو
                                    التعامل
                                    مع المؤجرين أو المستأجرين أو الموردين أو البنوك أو الجهات التنظيمية أو السلطات نيابة
                                    عن
                                    الشركة، أو المطالبة بأي مصلحة ملكية في أصول الشركة أو تراخيصها أو شهرتها التجارية أو
                                    علامتها
                                    أو حساباتها البنكية أو عقود إيجارها أو أعمالها. </p>
                            </div>
                        </td>
                    </tr>



                    <tr data-row style="background-color:#F2F2F2">
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-md" style="font-weight: 700 !important;">
                                    12. Assignment and Transfer
                                </p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-md" style="font-weight: 700 !important;">
                                    <strong>12 - </strong> التنازل ونقل الملكية
                                </p>
                            </div>
                        </td>
                    </tr>


                    <tr data-row>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-sm">12.1 The Investor shall not assign, transfer, pledge, encumber,
                                    nominate,
                                    sell, or otherwise dispose of any right, benefit, claim, or interest under this
                                    Agreement
                                    without the Company’s prior written consent. Any attempted assignment or transfer by
                                    the
                                    Investor without the Company’s written consent shall be invalid and shall not bind
                                    the
                                    Company.</p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-sm"> <span>1-12 </span> لا يجوز للمستثمر التنازل أو النقل أو الرهن أو
                                    ترتيب أي
                                    عبء أو ترشيح أو بيع أو التصرف بأي شكل آخر في أي حق أو منفعة أو مطالبة أو مصلحة بموجب
                                    هذه
                                    الاتفاقية دون الحصول على موافقة كتابية مسبقة من الشركة. ويقع باطلاً أي تنازل أو نقل
                                    يحاول
                                    المستثمر القيام به دون موافقة كتابية من الشركة ولا يلزم الشركة. </p>
                            </div>
                        </td>
                    </tr>


                    <tr data-row>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-sm">12.2 The Company may assign, transfer, novate, or restructure its
                                    rights and
                                    obligations under this Agreement to an affiliate, successor entity, related entity,
                                    or
                                    business transferee, provided that such entity assumes the Company’s material
                                    obligations
                                    under this Agreement.</p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-sm"> <span>2-12 </span> ليجوز للشركة التنازل عن حقوقها والتزاماتها بموجب
                                    هذه
                                    الاتفاقية أو نقلها أو تجديدها أو إعادة هيكلتها إلى شركة تابعة أو خلف قانوني أو كيان
                                    ذي صلة
                                    أو متنازل إليه عن الأعمال، شريطة أن يتحمل ذلك الكيان الالتزامات الجوهرية للشركة
                                    بموجب هذه
                                    الاتفاقية. </p>
                            </div>
                        </td>
                    </tr>


                    <tr data-row style="background-color:#F2F2F2">
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-md" style="font-weight: 700 !important;">
                                    13. Set-Off and Withholding
                                </p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-md" style="font-weight: 700 !important;">
                                    <strong>13 - </strong> المقاصة والاقتطاع من المنبع
                                </p>
                            </div>
                        </td>
                    </tr>


                    <tr data-row>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-sm">13.1 The Company may set off, deduct, withhold, or adjust from any
                                    amount
                                    payable to the Investor any amount due, payable, disputed in good faith, or
                                    reasonably
                                    recoverable from the Investor under this Agreement, including excess provisional
                                    distributions, indemnity claims, or other liabilities connected with the Investor or
                                    the
                                    Capital.</p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-sm"> <span>1-13 </span> ليجوز للشركة إجراء المقاصة أو الخصم أو الاقتطاع
                                    من
                                    المنبع أو التسوية من أي مبلغ مستحق الدفع للمستثمر مقابل أي مبلغ مستحق أو واجب الدفع
                                    أو محل
                                    نزاع بحسن نية أو قابل للاسترداد بصورة معقولة من المستثمر بموجب هذه الاتفاقية، بما في
                                    ذلك
                                    التوزيعات المؤقتة الزائدة أو مطالبات التعويض أو أي التزامات أخرى متصلة بالمستثمر أو
                                    رأس
                                    المال.</p>
                            </div>
                        </td>
                    </tr>


                    <tr data-row style="background-color:#F2F2F2">
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-md" style="font-weight: 700 !important;">
                                    14. Shariah Interpretation and No Loan
                                </p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-md" style="font-weight: 700 !important;">
                                    <strong>14 - </strong> التفسير الشرعي وانتفاء القروض
                                </p>
                            </div>
                        </td>
                    </tr>


                    <tr data-row>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-sm">14.1 The Parties intend this Agreement to operate as a
                                    Shariah-compliant
                                    Mudarabah arrangement. No provision of this Agreement shall be interpreted as
                                    creating a
                                    loan, interest-bearing arrangement, guaranteed return, guaranteed profit, guaranteed
                                    capital
                                    repayment, deposit, debt obligation, partnership in the Company, or investment
                                    product with
                                    capital protection, except to the extent an amount has become finally due under this
                                    Agreement.</p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-sm"> <span>1-14 </span> يقصد الطرفان أن تمضي هذه الاتفاقية كترتيب مضاربة
                                    متوافق
                                    مع الشريعة ولا يجوز تفسير أي حكم في هذه الاتفاقية على أنه ينشئ قرضاً أو ترتيباً
                                    بفائدة أو
                                    عائداً مضموناً أو ربحاً مضموناً أو رداً مضموناً لرأس المال أو وديعة أو التزام دين أو
                                    شراكة
                                    في الشركة أو منتجاً استثمارياً يتمتع بحماية رأس المال، إلا في حدود أي مبلغ أصبح
                                    مستحقاً
                                    نهائياً بموجب هذه الاتفاقية.</p>
                            </div>
                        </td>
                    </tr>



                    <tr data-row>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-sm">14.2 If any provision is found or alleged to be inconsistent with
                                    essential
                                    Mudarabah principles, the Parties shall interpret or amend it to preserve the
                                    closest lawful
                                    and commercially practical Shariah-compliant effect.</p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-sm"> <span>2-14 </span> إذا تبين أو زُعم أن أي حكم غير متوافق مع المبادئ
                                    الأساسية للمضاربة، فيقوم الطرفان بتفسيره أو تعديله بما يحافظ على أقرب أثر مشروع
                                    وعملي
                                    تجارياً ومتوافق مع الشريعة.</p>
                            </div>
                        </td>
                    </tr>



                    <tr data-row>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-sm">14.3 Nothing in this Agreement shall make the Company liable and
                                    accountable for commercial loss except where such loss is caused by the Company’s
                                    fraud,
                                    wilful misconduct, gross negligence, or material breach of this Agreement.</p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-sm"> <span>3-14 </span> ليس في هذه الاتفاقية ما يجعل الشركة مسؤولة أو
                                    محاسبة عن
                                    الخسائر التجارية إلا إذا كانت تلك الخسارة ناتجة عن احتيال الشركة أو سوء سلوكها
                                    المتعمد أو
                                    إهمالها الجسيم أو إخلالها الجوهري بهذه الاتفاقية .</p>
                            </div>
                        </td>
                    </tr>


                    <tr data-row style="background-color:#F2F2F2">
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-md" style="font-weight: 700 !important;">
                                    15. Notices
                                </p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-md" style="font-weight: 700 !important;">
                                    <strong>15 - </strong> الإخطارات
                                </p>
                            </div>
                        </td>
                    </tr>


                    <tr data-row>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-sm">15.1 Any notice under this Agreement shall be in writing and shall
                                    be
                                    delivered by email to the addresses as mentioned below or any updated address
                                    notified in
                                    writing:</p>

                                Investor's email: {investor_email}<br>
                                Company’s email: {company_email}
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-sm"> <span>1-15 </span> يكون أي إخطار بموجب هذه الاتفاقية مكتوباً ويتم
                                    تسليمه
                                    عبر البريد الإلكتروني إلى العناوين المذكورة أدناه أو إلى أي عنوان محدث يتم الإخطار
                                    به
                                    كتابةً:</p>

                                البريد الإلكتروني للمستثمر :{investor_email}<br>
                                البريد الإلكتروني للشركة :{company_email}
                            </div>
                        </td>
                    </tr>



                    <tr data-row>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-sm">15.2 The Investor shall keep the Company always updated in writing
                                    regarding his/her current address, email address, phone number, bank details,
                                    nominee
                                    details, and identification documents. The Company shall not be liable for any delay
                                    or
                                    non-receipt caused by outdated or incorrect details provided by the Investor.</p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-sm"> <span>2-15 </span> يلتزم المستثمر بإبقاء الشركة محدثة دائماً وبشكل
                                    كتابي
                                    بشأن عنوانه الحالي وبريده الإلكتروني ورقم هاتفه وتفاصيله البنكية وتفاصيل المرشح
                                    ومستندات
                                    التعريف ولا تكون الشركة مسؤولة عن أي تأخير أو عدم استلام ناجم عن بيانات قديمة أو غير
                                    صحيحة
                                    قدمها المستثمر.</p>
                            </div>
                        </td>
                    </tr>


                    <tr data-row style="background-color:#F2F2F2">
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-md" style="font-weight: 700 !important;">
                                    16. Confidentiality
                                </p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-md" style="font-weight: 700 !important;">
                                    <strong>16 - </strong> السرية
                                </p>
                            </div>
                        </td>
                    </tr>


                    <tr data-row>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-sm">16.1 Both the parties agree to maintain the confidentiality of all
                                    information disclosed and commercial understanding between the parties regarding
                                    this
                                    Agreement.</p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-sm"> <span>1-16 </span> يتفق الطرفان على الحفاظ على سرية جميع المعلومات
                                    المفصح
                                    عنها والتفاهمات التجارية بين الطرفين بشأن هذه الاتفاقية .</p>
                            </div>
                        </td>
                    </tr>



                    <tr data-row style="background-color:#F2F2F2">
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-md" style="font-weight: 700 !important;">
                                    17. Governing Law and Dispute Resolution
                                </p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-md" style="font-weight: 700 !important;">
                                    <strong>17 - </strong> القانون الواجب التطبيق وتسوية المنازعات
                                </p>
                            </div>
                        </td>
                    </tr>


                    <tr data-row>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-sm">17.1 This Agreement shall be governed by the laws applicable in the
                                    Emirate
                                    of Dubai and the federal laws of the United Arab Emirates, as applicable.</p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-sm"> <span>1-17 </span> تخضع هذه الاتفاقية من حيث التنظيم لأحكام
                                    القوانين
                                    السارية في إمارة دبي والقوانين الاتحادية لدولة الإمارات العربية المتحدة، حسبما ينطبق
                                    .</p>
                            </div>
                        </td>
                    </tr>



                    <tr data-row>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-sm">17.2 Any dispute arising out of or in connection with this Agreement
                                    shall
                                    be referred to and finally resolved by arbitration under the Arbitration Rules of
                                    the Dubai
                                    International Arbitration Centre, which Rules are deemed incorporated by reference
                                    into this
                                    clause. The seat of arbitration shall be Dubai, United Arab Emirates. The language
                                    of
                                    arbitration shall be English.</p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-sm"> <span>2-17 </span> يحال أي نزاع ينشأ عن هذه الاتفاقية أو يتعلق بها
                                    إلى
                                    التحكيم ويتم الفصل فيه نهائياً بموجب قواعد التحكيم لدى مركز دبي للتحكيم الدولي،
                                    وتُعد تلك
                                    القواعد مدمجة بالإحالة في هذا البند. ويكون مقر التحكيم دبي، دولة الإمارات العربية
                                    المتحدة.
                                    تكون لغة التحكيم الإنجليزية .</p>
                            </div>
                        </td>
                    </tr>


                    <tr data-row style="background-color:#F2F2F2">
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-md" style="font-weight: 700 !important;">
                                    18. Entire Agreement and Amendment
                                </p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-md" style="font-weight: 700 !important;">
                                    <strong>18 - </strong> مجمل الاتفاق والتعديل
                                </p>
                            </div>
                        </td>
                    </tr>


                    <tr data-row>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-sm">18.1 This Agreement constitutes the entire understanding between the
                                    parties with respect to the subject matter hereof and supersedes all prior and
                                    contemporaneous agreements and understandings, if any, whether written or oral.</p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-sm"> <span>1-18 </span> تشكل هذه الاتفاقية مجمل ما توصل إليه طرفاها من
                                    اتفاق
                                    فيما يتعلق بموضوعها، وتحل محل وتنسخ جميع الاتفاقيات والتفاهمات السابقة والحاضرة، إن
                                    وجدت
                                    سواء كانت مكتوبة أو شفوية.</p>
                            </div>
                        </td>
                    </tr>


                    <tr data-row>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-sm">18.2 No amendment or modification of this Agreement shall be valid
                                    unless
                                    made in writing and signed by both Parties.</p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-sm"> <span>2-18 </span> لا يصح أي تعديل أو تغيير يطرأ على هذه الاتفاقية
                                    إلا إذا
                                    تم كتابياً ووقع عليه الطرفان .</p>
                            </div>
                        </td>
                    </tr>



                    <tr data-row style="background-color:#F2F2F2">
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-md" style="font-weight: 700 !important;">
                                    19. Severability
                                </p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-md" style="font-weight: 700 !important;">
                                    <strong>19 - </strong> قابلية الفصل بين بنود الاتفاق
                                </p>
                            </div>
                        </td>
                    </tr>


                    <tr data-row>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-sm">19.1 If any provision of this Agreement is held to be invalid,
                                    illegal, or
                                    unenforceable, the remaining provisions shall continue in full force and effect. The
                                    Parties
                                    shall replace the invalid provision with a valid and enforceable provision that most
                                    closely
                                    reflects the original commercial and Shariah-compliant intention.</p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-sm"> <span>1-19 </span> إذا اعتُبر أي حكم من أحكام هذه الاتفاقية باطلاً
                                    أو غير
                                    قانوني أو غير قابل للتنفيذ، فتظل الأحكام المتبقية نافذة وسارية بكامل أثرها. يستبدل
                                    الطرفان
                                    الحكم الباطل بحكم صحيح وقابل للتنفيذ يعكس بأقرب صورة النية التجارية الأصلية
                                    والمتوافقة مع
                                    الشريعة .</p>
                            </div>
                        </td>
                    </tr>



                    <tr data-row style="background-color:#F2F2F2">
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-md" style="font-weight: 700 !important;">
                                    20. Survival
                                </p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-md" style="font-weight: 700 !important;">
                                    <strong>20 - </strong> استمرار النفاذ
                                </p>
                            </div>
                        </td>
                    </tr>


                    <tr data-row>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-sm">20.1 Clauses relating to confidentiality, dispute resolution,
                                    accounting
                                    reconciliation, payment obligations, set-off, indemnity, limitation of liability,
                                    notices,
                                    Shariah compliance interpretation, tax responsibility, succession verification, and
                                    accrued
                                    rights or obligations shall survive expiry or termination of this Agreement.</p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-sm"> <span>1-20 </span> تستمر الأحكام المتعلقة بالسرية وتسوية المنازعات
                                    والتسويات المحاسبية والتزامات الدفع والمقاصة والتعويض وتحديد المسؤولية والإخطارات
                                    وتفسير
                                    الامتثال للشريعة والمسؤولية الضريبية والتحقق من الخلافة والحقوق أو الالتزامات
                                    المستحقة بعد
                                    انتهاء هذه الاتفاقية أو إنهائها .</p>
                            </div>
                        </td>
                    </tr>


                    <tr data-row style="background-color:#F2F2F2">
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-md" style="font-weight: 700 !important;">
                                    21. Counterparts and Electronic Signature
                                </p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-md" style="font-weight: 700 !important;">
                                    <strong>21 - </strong> النسخ والتوقيع الالكتروني
                                </p>
                            </div>
                        </td>
                    </tr>


                    <tr data-row>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-sm">21.1 This Agreement may be executed in counterparts, each of which
                                    shall
                                    constitute an original and all of which together shall constitute one instrument.
                                </p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-sm"> <span>1-21 </span> يجوز توقيع هذه الاتفاقية على عدة نسخ، وتعتبر كل
                                    نسخة
                                    منها أصلاً، وتشكل جميعها معاً وثيقة واحدة .</p>
                            </div>
                        </td>
                    </tr>


                    <tr data-row>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-sm">21.2 Signatures exchanged electronically, by scanned copy, or
                                    through an
                                    electronic signing platform shall be treated as valid signatures.</p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-sm"> <span>2-21 </span> تعامل التواقيع المتبادلة إلكترونياً أو من خلال
                                    نسخة
                                    ممسوحة ضوئياً أو عبر منصة توقيع إلكتروني كتواقيع صحيحة .</p>
                            </div>
                        </td>
                    </tr>


                    <tr data-row>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-sm">
                                    IN WITNESS WHEREOF, the parties hereto have executed this Agreement as of the date
                                    first
                                    above written.
                                </p>

                                <p class="text-sm"> {company_name_eng}</p>

                                <p class="text-sm"> Sign:</p>

                                <p class="text-sm"> Authorized Signatory</p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-sm">وإشهاداً علىما تقدم، قام الطرفان بتوقيع هذه الاتفاقية اعتباراً من
                                    التاريخ
                                    المذكور في صدر هذه الاتفاقية.</p>
                                <p class="text-sm">شركة {company_name_ar}</p>
                                <p class="text-sm">التوقيع :</p>
                                <p class="text-sm">المفوض بالتوقيع</p>
                            </div>
                        </td>
                    </tr>


                    <tr data-row>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="english">
                                <p class="text-sm"> Sign:</p>

                                <p class="text-sm"> Investor:{investor_name_eng}</p>

                                <p class="text-sm"> Date: {mudarabah_created_short_date_eng}</p>
                            </div>
                        </td>
                        <td width="50%" style="border:1px solid #ccc;">
                            <div class="arabic">
                                <p class="text-sm">التوقيع :</p>
                                <p class="text-sm">المستثمر:{investor_name_ar}</p>
                                <p class="text-sm">التاريخ {mudarabah_created_short_date_ar}</p>
                            </div>
                        </td>
                    </tr>



                    <tr data-row data-force-page="true">
                        <td colspan="2" style="padding:0;">
                            <table width="100%" border="1" align="center" class="mt-15" cellpadding="0"
                                cellspacing="0" style="max-width:100%;">


                                <tr style="background-color:#F2F2F2">
                                    <td width="50%" style="border:1px solid #ccc;">
                                        <div class="english">
                                            <p class="text-md" style="font-weight:700 !important;">ANNEXURE-A
                                            </p>
                                        </div>
                                    </td>
                                    <td width="50%" style="border:1px solid #ccc;">
                                        <div class="arabic">
                                            <p class="text-md" style="font-weight:700 !important;">الملحق (أ)
                                            </p>
                                        </div>
                                    </td>
                                </tr>



                                <tr>
                                    <td width="50%" style="border:1px solid #ccc;">
                                        <div class="english">
                                            <p class="text-sm">Investment Date: {investment_date_eng}</p>
                                        </div>
                                    </td>
                                    <td width="50%" style="border:1px solid #ccc;">
                                        <div class="arabic">
                                            <p class="text-sm">تاريخ الاستثمار: {investment_date_ar}</p>
                                        </div>
                                    </td>
                                </tr>


                                <tr>
                                    <td width="50%" style="border:1px solid #ccc;">
                                        <div class="english">
                                            <p class="text-sm">Investment Amount: {invested_amount}/- AED</p>
                                        </div>
                                    </td>
                                    <td width="50%" style="border:1px solid #ccc;">
                                        <div class="arabic">
                                            <p class="text-sm">مبلغ الاستثمار: {invested_amount}/- درهم إماراتي</p>
                                        </div>
                                    </td>
                                </tr>


                                <tr>
                                    <td width="50%" style="border:1px solid #ccc;">
                                        <div class="english">
                                            <p class="text-sm">Investor Name: {investor_name_eng} </p>
                                        </div>
                                    </td>
                                    <td width="50%" style="border:1px solid #ccc;">
                                        <div class="arabic">
                                            <p class="text-sm">اسم المستثمر: {investor_name_ar}</p>
                                        </div>
                                    </td>
                                </tr>


                                <tr>
                                    <td width="50%" style="border:1px solid #ccc;">
                                        <div class="english">
                                            <p class="text-sm">Mobile No: {investor_mobile}</p>
                                        </div>
                                    </td>
                                    <td width="50%" style="border:1px solid #ccc;">
                                        <div class="arabic">
                                            <p class="text-sm">رقم الهاتف المتحرك: {investor_mobile}</p>
                                        </div>
                                    </td>
                                </tr>


                                <tr>
                                    <td width="50%" style="border:1px solid #ccc;">
                                        <div class="english">
                                            <p class="text-sm">Email ID: {investor_email}</p>
                                        </div>
                                    </td>
                                    <td width="50%" style="border:1px solid #ccc;">
                                        <div class="arabic">
                                            <p class="text-sm">البريد الإلكتروني: {investor_email}</p>
                                        </div>
                                    </td>
                                </tr>


                                <tr>
                                    <td width="50%" style="border:1px solid #ccc;">
                                        <div class="english">
                                            <p class="text-sm">Address: {investor_address}</p>
                                        </div>
                                    </td>
                                    <td width="50%" style="border:1px solid #ccc;">
                                        <div class="arabic">
                                            <p class="text-sm">العنوان: {investor_address}</p>
                                        </div>
                                    </td>
                                </tr>


                                <tr>
                                    <td width="50%" style="border:1px solid #ccc;">
                                        <div class="english">
                                            <p class="text-sm">Investor ID/ Passport: {investor_id_no}</p>
                                        </div>
                                    </td>
                                    <td width="50%" style="border:1px solid #ccc;">
                                        <div class="arabic">
                                            <p class="text-sm">هوية المستثمر/جواز السفر: {investor_id_no}</p>
                                        </div>
                                    </td>
                                </tr>


                                <tr>
                                    <td width="50%" style="border:1px solid #ccc;">
                                        <div class="english">
                                            <p class="text-sm">Nationality: {investor_nationality}</p>
                                        </div>
                                    </td>
                                    <td width="50%" style="border:1px solid #ccc;">
                                        <div class="arabic">
                                            <p class="text-sm">الجنسية: {investor_nationality}</p>
                                        </div>
                                    </td>
                                </tr>


                                <tr>
                                    <td width="50%" style="border:1px solid #ccc;">
                                        <div class="english">
                                            <p class="text-sm">Country of Residence: {investor_residence}</p>
                                        </div>
                                    </td>
                                    <td width="50%" style="border:1px solid #ccc;">
                                        <div class="arabic">
                                            <p class="text-sm">بلد الإقامة: {investor_residence}</p>
                                        </div>
                                    </td>
                                </tr>


                                <tr>
                                    <td width="50%" style="border:1px solid #ccc;">
                                        <div class="english">
                                            <p class="text-sm">Passport No: {passport_no}</p>
                                        </div>
                                    </td>
                                    <td width="50%" style="border:1px solid #ccc;">
                                        <div class="arabic">
                                            <p class="text-sm">رقم جواز السفر: {passport_no}</p>
                                        </div>
                                    </td>
                                </tr>


                                <tr>
                                    <td width="50%" style="border:1px solid #ccc;">
                                        <div class="english">
                                            <p class="text-sm">Grace Period (Days): {grace_period}</p>
                                        </div>
                                    </td>
                                    <td width="50%" style="border:1px solid #ccc;">
                                        <div class="arabic">
                                            <p class="text-sm">فترة السماح (بالأيام): {grace_period}يوم</p>
                                        </div>
                                    </td>
                                </tr>


                                <tr>
                                    <td width="50%" style="border:1px solid #ccc;">
                                        <div class="english">
                                            <p class="text-sm">Mode of Payment: {mode_of_payment_eng}</p>
                                        </div>
                                    </td>
                                    <td width="50%" style="border:1px solid #ccc;">
                                        <div class="arabic">
                                            <p class="text-sm">طريقة الدفع: {mode_of_payment_ar}</p>
                                        </div>
                                    </td>
                                </tr>


                                <tr>
                                    <td width="50%" style="border:1px solid #ccc;">
                                        <div class="english">
                                            <p class="text-sm">Tenure of Profit: {tenure_eng}</p>
                                        </div>
                                    </td>
                                    <td width="50%" style="border:1px solid #ccc;">
                                        <div class="arabic">
                                            <p class="text-sm">مدة الربح: {tenure_ar}</p>
                                        </div>
                                    </td>
                                </tr>


                                <tr>
                                    <td width="50%" style="border:1px solid #ccc;">
                                        <div class="english">
                                            <p class="text-sm">Beneficiary Name: {beneficiary_name_eng}</p>
                                        </div>
                                    </td>
                                    <td width="50%" style="border:1px solid #ccc;">
                                        <div class="arabic">
                                            <p class="text-sm">اسم المستفيد: {beneficiary_name_ar}</p>
                                        </div>
                                    </td>
                                </tr>


                                <tr>
                                    <td width="50%" style="border:1px solid #ccc;">
                                        <div class="english">
                                            <p class="text-sm">Beneficiary Bank Name: {beneficiary_bankname_eng}</p>
                                        </div>
                                    </td>
                                    <td width="50%" style="border:1px solid #ccc;">
                                        <div class="arabic">
                                            <p class="text-sm">البنك المستفيد: {beneficiary_bankname_eng}</p>
                                        </div>
                                    </td>
                                </tr>


                                <tr>
                                    <td width="50%" style="border:1px solid #ccc;">
                                        <div class="english">
                                            <p class="text-sm">Beneficiary IBAN: {beneficiary_iban}</p>
                                        </div>
                                    </td>
                                    <td width="50%" style="border:1px solid #ccc;">
                                        <div class="arabic">
                                            <p class="text-sm">رقم آيبان الخاص بالمستفيد: {beneficiary_iban}</p>
                                        </div>
                                    </td>
                                </tr>

                            </table>
                        </td>
                    </tr>


                    <tr data-row data-force-page="true">
                        <td colspan="2" style="padding:0;">
                            <table width="100%" border="1" align="center" class="mt-15" cellpadding="0"
                                cellspacing="0" style="max-width:100%;">


                                <tr style="background-color:#F2F2F2">
                                    <td width="50%" style="border:1px solid #ccc;">
                                        <div class="english">
                                            <p class="text-md" style="font-weight:700 !important;">Annexure
                                                B</p>
                                        </div>
                                    </td>
                                    <td width="50%" style="border:1px solid #ccc;">
                                        <div class="arabic">
                                            <p class="text-md" style="font-weight:700 !important;">الملحق
                                                (ب)</p>
                                        </div>
                                    </td>
                                </tr>


                                <tr>
                                    <td width="50%" style="border:1px solid #ccc;">
                                        <div class="english" style="padding: 8px;">
                                            <p class="text-sm">Monthly Profit Distribution (Investor's Share)
                                                (For illustrative purposes only, subject to realization of
                                                profits)</p>
                                            <p class="text-sm">
                                                · Capital contributed by Investor: AED {total_invested_amount}/-<br>
                                                · Expected annual profit: AED {total_profit}/-<br>
                                                · Investor's profit share ratio: {inv_profit_perc}%<br>
                                                · Equivalent monthly estimate for investor: AED {monthly_estimate}/-
                                            </p>

                                            <table width="100%" border="1" cellpadding="1" cellspacing="0">
                                                <tr>
                                                    <td width="50%" style="border:1px solid #ccc;">
                                                        <div class="english">
                                                            <p class="text-md" style="font-weight:700 !important;">
                                                                Period</p>
                                                        </div>
                                                    </td>
                                                    <td width="50%" style="border:1px solid #ccc;">
                                                        <div class="english">
                                                            <p class="text-md" style="font-weight:700 !important;">
                                                                Expected Profit</p>
                                                        </div>
                                                    </td>
                                                </tr>
                                                {profit_month_eng}
                                                <tr>
                                                    <td width="50%" style="border:1px solid #ccc;">
                                                        <div class="english">
                                                            <p class="text-md">13 Total (Annual)</p>
                                                        </div>
                                                    </td>
                                                    <td width="50%" style="border:1px solid #ccc;">
                                                        <div class="english">
                                                            <p class="text-md">AED {total_profit}/-</p>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </table>

                                        </div>
                                    </td>


                                    <td width="50%" style="border:1px solid #ccc;">
                                        <div class="arabic" style="padding: 8px;">
                                            <p class="text-sm">توزيع الأرباح الشهرية (حصة المستثمر) (لأغراض
                                                توضيحية فقط، وخاضع لتحقق الأرباح)</p>
                                            <p class="text-sm">
                                                <br>. رأس المال المساهم به من المستثمر: {total_invested_amount}/- درهم
                                                إماراتي
                                                <br>. الربح السنوي المتوقع: {total_profit}/- درهم إماراتي
                                                <br>. نسبة حصة المستثمر من الربح :{inv_profit_perc}%
                                                <br>. التقدير الشهري المعادل للمستثمر: {monthly_estimate}/- درهم إماراتي
                                            </p>
                                            <table width="100%" border="1" cellpadding="1" cellspacing="0">
                                                <tr>
                                                    <td width="50%" style="border:1px solid #ccc;">
                                                        <div class="arabic">
                                                            <p class="text-md" style="font-weight:700 !important;">
                                                                الفترة</p>
                                                        </div>
                                                    </td>
                                                    <td width="50%" style="border:1px solid #ccc;">
                                                        <div class="arabic">
                                                            <p class="text-md" style="font-weight:700 !important;">
                                                                الربح المتوقع</p>
                                                        </div>
                                                    </td>
                                                </tr>
                                                {profit_month_ar}
                                                <tr>
                                                    <td width="50%" style="border:1px solid #ccc;">
                                                        <div class="arabic">
                                                            <p class="text-md">13 الإجمالي (سنوي)</p>
                                                        </div>
                                                    </td>
                                                    <td width="50%" style="border:1px solid #ccc;">
                                                        <div class="arabic">
                                                            <p class="text-md">{total_profit}/- درهم إماراتي</p>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td width="50%" style="border:1px solid #ccc;">
                                        <div class="english">
                                            <p class="text-md">Disclaimer<br>
                                                This schedule is illustrative only and does not constitute a
                                                guarantee of any monthly or annual return. Any interim profit
                                                distribution is provisional and subject to adjustment at final
                                                annual valuation.</p>
                                        </div>
                                    </td>
                                    <td width="50%" style="border:1px solid #ccc;">
                                        <div class="arabic">
                                            <p class="text-md">إخلاء المسؤولية<br>هذا المرفق هدفه التوضيح فقط
                                                ولا يشكل ضماناً لأي عائد شهري أو سنوي. ويكون أي توزيع مؤقت
                                                للأرباح مؤقتاً وخاضعاً للتسوية عند التقييم السنوي النهائي .</p>
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
