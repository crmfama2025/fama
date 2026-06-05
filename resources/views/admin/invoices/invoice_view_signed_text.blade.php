@extends('admin.layout.admin_master')

@section('custom_css')
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="{{ asset('assets/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/adminlte.min.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&display=swap" rel="stylesheet">
    <style>
        .invoice-page {
            position: relative;
            max-width: 794px;
            min-height: 1123px;
            margin: 0 auto 40px auto;
            background-color: #fff;
            background-image: url("{{ asset('images/Faateh-letterhead.png') }}");
            background-size: 100% 100%;
            background-repeat: no-repeat;
            background-position: center center;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            box-shadow: 0 4px 24px #aaa;
        }

        .invoice-content {
            position: relative;
            padding: 230px 60px 190px 60px;
        }

        .inv-title {
            text-align: center;
            font-size: 22px;
            font-weight: 900;
            letter-spacing: 3px;
            margin-bottom: 6px;
            text-decoration: underline;
        }

        .inv-meta {
            display: flex;
            justify-content: space-between;
            margin-bottom: 4px;
            font-weight: 700;
            font-size: 12px;
        }

        .inv-info {
            font-weight: 700;
            font-size: 12px;
            margin-bottom: 4px;
        }

        .inv-period {
            text-align: center;
            font-weight: 800;
            font-size: 12px;
            border: 1px solid #000;
            padding: 4px;
            margin-bottom: 0;
            background: rgba(255, 255, 255, 0.7);
        }

        .inv-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }

        .inv-table th {
            border: 1px solid #000;
            padding: 5px 6px;
            text-align: center;
            font-weight: 800;
            background: rgba(240, 240, 240, 0.85);
        }

        .inv-table td {
            border: 1px solid #000;
            padding: 4px 6px;
            font-weight: 600;
            background: rgba(255, 255, 255, 0.7);
        }

        .inv-table td.c {
            text-align: center;
        }

        .inv-table td.r {
            text-align: right;
        }

        .inv-table tr.total td {
            font-weight: 900;
            background: rgba(245, 245, 245, 0.85);
        }

        .pay-section {
            margin-top: 10px;
            font-weight: 700;
            font-size: 11px;
        }

        .bank-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            margin-top: 5px;
        }

        .bank-table th {
            border: 1px solid #000;
            padding: 4px 6px;
            font-weight: 800;
            background: rgba(240, 240, 240, 0.85);
        }

        .bank-table td {
            border: 1px solid #000;
            padding: 4px 6px;
            font-weight: 600;
            background: rgba(255, 255, 255, 0.7);
        }

        /* ---- Signature Row ---- */
        .sig-row {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
            align-items: flex-end;
        }

        .sig-block {
            text-align: center;
            width: 42%;
            font-weight: 700;
            font-size: 12px;
            position: relative;
        }

        .sig-line {
            border-top: 1px solid #000;
            padding-top: 5px;
            margin-top: 4px;
        }

        /* ---- Digital Approval Stamp ---- */
        .digital-approval-wrap {
            position: relative;
            height: 100px;
            display: flex;
            align-items: flex-end;
            justify-content: center;
        }

        /* Cursive auto-signature */
        .auto-signature {
            font-family: 'Dancing Script', cursive;
            font-size: 30px;
            color: #1a3a8f;
            position: absolute;
            bottom: 12px;
            left: 10px;
            letter-spacing: 1px;
            z-index: 2;
        }

        /* Round SVG seal positioned bottom-right */
        .approval-seal {
            position: absolute;
            bottom: -10px;
            right: 0px;
            width: 90px;
            height: 90px;
            opacity: 0.85;
            z-index: 3;
        }

        /* "Digitally Approved" green ribbon */
        .digital-approved-ribbon {
            display: inline-block;
            background: #e8f5e9;
            border: 1.5px solid #2e7d32;
            color: #2e7d32;
            font-size: 9px;
            font-weight: 800;
            letter-spacing: 1.5px;
            padding: 2px 10px;
            border-radius: 2px;
            text-transform: uppercase;
            margin-bottom: 3px;
        }

        /* ---- Print ---- */
        @media print {
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color-adjust: exact !important;
            }

            .no-print {
                display: none !important;
            }

            body {
                margin: 0;
                padding: 0;
            }

            .content-wrapper,
            .content,
            .container-fluid {
                padding: 0 !important;
                margin: 0 !important;
                background: transparent !important;
            }

            .invoice-page {
                box-shadow: none;
                margin: 0;
                max-width: 100%;
                width: 100%;
                min-height: 100vh;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            @page {
                margin: 0;
                size: A4 portrait;
            }
        }
    </style>
@endsection

@section('content')
    <div class="content-wrapper">
        <section class="content">
            <div class="container-fluid">

                <section class="content-header no-print">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-12">
                                <h1>Invoice #{{ $invoice->invoice_no }}</h1>
                            </div>
                        </div>
                    </div>
                </section>

                <div class="invoice-page">
                    <div class="invoice-content">

                        <div class="inv-title">INVOICE</div>

                        @php
                            $tenant = $invoice->agreement->tenant ?? null;
                            $tenantName = $tenant->tenant_name ?? '-';
                            $trn_number =
                                $tenant?->tenantDocuments?->where('document_type', 3)->first()?->document_number ?? '-';
                        @endphp

                        <div class="inv-meta">
                            <div>
                                <strong>{{ $tenantName }}</strong><br>UAE.
                            </div>
                            <div style="text-align:right;">
                                <strong>Invoice No. {{ $invoice->invoice_no }}</strong><br>
                                <strong>Date: {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d/m/Y') }}</strong>
                            </div>
                        </div>

                        <div class="inv-info">
                            <strong>License No: {{ $trn_number }}</strong>
                        </div>

                        <div class="inv-period">
                            <strong>For the M/o
                                {{ \Carbon\Carbon::parse($invoice->month_start)->format('d-m-Y') }}
                                to
                                {{ \Carbon\Carbon::parse($invoice->month_end)->format('d-m-Y') }}
                            </strong>
                        </div>

                        @php
                            $agreementUnit = $invoice->agreementUnit;
                            $subunits = $agreementUnit->agreementSubunitRentBifurcation ?? collect();
                            $contract = $invoice->contract;
                            $buildingName = $contract->property->property_name ?? '-';
                            $flatNo = $agreementUnit->contractUnitDetail->unit_number ?? '-';
                            $area = $contract->area->area_name ?? '-';
                            $unitType = $agreementUnit->contractUnitDetail->unit_type->unit_type ?? '-';
                            $tenantType = ($contract->contract_unit->business_type ?? 0) == 1 ? 'B2B' : 'B2C';
                            $project_no = $contract->project_number;

                            // {{-- ✏️ ADJUST this condition to match your approval field --}}
                            // {{-- e.g.  $invoice->status == 2  OR  $invoice->status === 'approved' --}}
                            $isApproved = ($invoice->status ?? '') === 2;

                            $approvedAt =
                                $isApproved && $invoice->approved_date
                                    ? \Carbon\Carbon::parse($invoice->approved_date)->format('d/m/Y H:i')
                                    : null;
                        @endphp

                        <table class="inv-table">
                            <thead>
                                <tr>
                                    <th>Project Number</th>
                                    <th>Building Name</th>
                                    <th>Flat No.</th>
                                    <th>Area</th>
                                    <th>Type</th>
                                    <th>Tenant Type</th>
                                    <th>Partitions No.</th>
                                    <th>Amount AED.</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($subunits as $subunit)
                                    <tr>
                                        <td>Project - {{ $project_no }}</td>
                                        <td class="c">{{ $buildingName }}</td>
                                        <td class="c">{{ $flatNo }}</td>
                                        <td class="c">{{ $area }}</td>
                                        <td class="c">{{ $unitType }}</td>
                                        <td class="c">{{ $tenantType }}</td>
                                        <td class="c">{{ $subunit->contractSubunitDetail->subunit_no ?? '-' }}</td>
                                        <td class="r">{{ number_format($subunit->rent_per_month, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="c">{{ $buildingName }}</td>
                                        <td class="c">{{ $flatNo }}</td>
                                        <td class="c">{{ $area }}</td>
                                        <td class="c">{{ $unitType }}</td>
                                        <td class="c">{{ $tenantType }}</td>
                                        <td class="c">-</td>
                                        <td class="r">{{ number_format($invoice->total_amount, 2) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr class="total">
                                    <td colspan="6" class="r"><strong>Total Amount---AED</strong></td>
                                    <td class="r"><strong>{{ number_format($invoice->total_amount, 2) }}</strong></td>
                                </tr>
                            </tfoot>
                        </table>

                        <div class="pay-section">
                            <p><strong>Payment Methods: Cheque Or Bank Transfer</strong></p>
                            <p><strong>Account Details: -</strong></p>
                            <table class="bank-table">
                                <thead>
                                    <tr>
                                        <th>Bank Name.</th>
                                        <th>Account Name.</th>
                                        <th>Account No.</th>
                                        <th>IBAN</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Emirates Islamic</td>
                                        <td>Faateh Real Estate LLC</td>
                                        <td>3708467001801</td>
                                        <td>AE630340003708467001801</td>
                                    </tr>
                                    <tr>
                                        <td>Emirates NBD</td>
                                        <td>Faateh Real Estate LLC</td>
                                        <td>1015900588801</td>
                                        <td>AE090260001015900588801</td>
                                    </tr>
                                    <tr>
                                        <td>ADCB</td>
                                        <td>Faateh Real Estate LLC</td>
                                        <td>14107498920001</td>
                                        <td>AE910030014107498920001</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        {{-- ===== SIGNATURES ===== --}}
                        <div class="sig-row">

                            {{-- LEFT: Faateh Real Estate — digital approval --}}
                            <div class="sig-block">
                                @if ($isApproved)
                                    {{-- <div class="digital-approval-wrap">

                                        <span class="auto-signature">Faateh R.E.</span>

                                        <svg class="approval-seal" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                                            <circle cx="50" cy="50" r="47" fill="rgba(232,240,255,0.5)"
                                                stroke="#1a3a8f" stroke-width="2.5" />
                                            <circle cx="50" cy="50" r="40" fill="none" stroke="#1a3a8f"
                                                stroke-width="1.2" />
                                            <circle cx="50" cy="50" r="43.5" fill="none" stroke="#1a3a8f"
                                                stroke-width="1" stroke-dasharray="3 4" />
                                            <!-- Star center -->
                                            <text x="50" y="54" text-anchor="middle" font-size="14" fill="#1a3a8f"
                                                font-weight="bold">★</text>
                                            <!-- Top arc text -->
                                            <path id="sealTop" d="M 12,50 A 38,38 0 1,1 88,50" fill="none" />
                                            <text font-size="7.5" fill="#1a3a8f" font-weight="bold" letter-spacing="1.2">
                                                <textPath href="#sealTop" startOffset="8%">FAATEH REAL ESTATE LLC</textPath>
                                            </text>
                                            <!-- Bottom arc text -->
                                            <path id="sealBottom" d="M 15,55 A 38,38 0 0,0 85,55" fill="none" />
                                            <text font-size="6.5" fill="#1a3a8f" letter-spacing="1">
                                                <textPath href="#sealBottom" startOffset="12%">DUBAI · UAE · EST. 2020
                                                </textPath>
                                            </text>
                                        </svg>

                                    </div> --}}

                                    {{-- Digital approved label + timestamp --}}
                                    <div style="margin-bottom:3px;">
                                        <span class="digital-approved-ribbon">✔ Digitally Approved</span><br>
                                        {{-- <span
                                            style="font-size:9px; color:#555; font-weight:500;">{{ $approvedAt }}</span> --}}
                                    </div>
                                @else
                                    <div style="height:100px;"></div>
                                @endif

                                <div class="sig-line">For Faateh Real Estate</div>
                            </div>

                            {{-- RIGHT: Receiver --}}
                            <div class="sig-block">
                                <div style="height:100px;"></div>
                                <div class="sig-line">Receiver's Signature</div>
                            </div>

                        </div>
                        {{-- ===== END SIGNATURES ===== --}}

                    </div>{{-- /invoice-content --}}
                </div>{{-- /invoice-page --}}

                <div class="mt-4 mb-5 text-center no-print">
                    <a href="{{ route('invoices.index') }}" class="btn btn-secondary mr-2">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                    <button onclick="printInvoice()" class="btn btn-primary">
                        <i class="fas fa-print"></i> Print
                    </button>
                </div>

            </div>
        </section>
    </div>
@endsection

@section('custom_js')
    <script src="{{ asset('assets/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script>
        function printInvoice() {
            const invoiceHtml = document.querySelector('.invoice-page').outerHTML;

            const allStyles = Array.from(document.styleSheets).map(ss => {
                try {
                    return Array.from(ss.cssRules).map(r => r.cssText).join('\n');
                } catch (e) {
                    return '';
                }
            }).join('\n');

            const win = window.open('', '_blank');
            win.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset="utf-8">
                    <title>Invoice</title>
                    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&display=swap" rel="stylesheet">
                    <style>
                        * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; color-adjust: exact !important; box-sizing: border-box; }
                        @page { margin: 0; size: A4 portrait; }
                        html, body { margin: 0; padding: 0; background: #fff; }
                        ${allStyles}
                        .invoice-page { max-width: 100% !important; width: 100% !important; box-shadow: none !important; margin: 0 !important; }
                        .no-print { display: none !important; }
                    </style>
                </head>
                <body>
                    ${invoiceHtml}
                    <script>
                        window.onload = function() {
                            window.print();
                            window.onafterprint = function() { window.close(); };
                        };
                    <\/script>
                </body>
                </html>
            `);
            win.document.close();
        }
    </script>
@endsection
