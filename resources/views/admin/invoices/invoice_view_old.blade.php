@extends('admin.layout.admin_master')

@section('custom_css')
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="{{ asset('assets/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/adminlte.min.css') }}">
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

        /* ---- Title ---- */
        .inv-title {
            text-align: center;
            font-size: 22px;
            font-weight: 900;
            letter-spacing: 3px;
            margin-bottom: 6px;
            text-decoration: underline;
        }

        /* ---- Meta row ---- */
        .inv-meta {
            display: flex;
            justify-content: space-between;
            margin-bottom: 4px;
            font-weight: 700;
            font-size: 12px;
        }

        /* ---- Info block ---- */
        .inv-info {
            font-weight: 700;
            font-size: 12px;
            margin-bottom: 4px;
        }

        /* ---- Period ---- */
        .inv-period {
            text-align: center;
            font-weight: 800;
            font-size: 12px;
            border: 1px solid #000;
            padding: 4px;
            margin-bottom: 0;
            background: rgba(255, 255, 255, 0.7);
        }

        /* ---- Tables ---- */
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

        /* ---- Payment ---- */
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

        /* ---- Signature ---- */
        .sig-row {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
        }

        .sig-block {
            text-align: center;
            width: 40%;
            font-weight: 700;
            font-size: 12px;
        }

        .sig-line {
            border-top: 1px solid #000;
            margin-top: 45px;
            padding-top: 5px;
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
                /* Force background to print */
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

                {{-- ===== INVOICE PAGE ===== --}}
                <div class="invoice-page">
                    <div class="invoice-content">

                        {{-- Title --}}
                        <div class="inv-title">INVOICE</div>

                        {{-- Meta: Billed To + Invoice No / Date --}}
                        @php
                            $tenant = $invoice->agreement->tenant ?? null;
                            $tenantName = $tenant->tenant_name ?? '-';
                            $trn_number =
                                $tenant?->tenantDocuments?->where('document_type', 3)->first()?->document_number ?? '-';
                        @endphp
                        <div class="inv-meta">
                            <div>
                                <strong>{{ $tenantName }}</strong><br>
                                UAE.
                            </div>
                            <div style="text-align:right;">
                                <strong>Invoice No. {{ $invoice->invoice_no }}</strong><br>
                                <strong>Date: {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d/m/Y') }}</strong>
                            </div>
                        </div>

                        {{-- License --}}
                        <div class="inv-info">
                            <strong>License No: {{ $trn_number }}</strong>
                        </div>

                        {{-- Period --}}
                        <div class="inv-period">
                            <strong>For the M/o
                                {{ \Carbon\Carbon::parse($invoice->month_start)->format('d-m-Y') }}
                                to
                                {{ \Carbon\Carbon::parse($invoice->month_end)->format('d-m-Y') }}
                            </strong>
                        </div>

                        {{-- Line Items Table --}}
                        @php
                            $agreementUnit = $invoice->agreementUnit;
                            $subunits = $agreementUnit->agreementSubunitRentBifurcation ?? collect();
                            $contract = $invoice->contract;
                            $buildingName = $contract->property->property_name ?? '-';
                            $flatNo = $agreementUnit->contractUnitDetail->unit_number ?? '-';
                            $area = $contract->area->area_name ?? '-';
                            $unitType = $agreementUnit->contractUnitDetail->unit_type->unit_type ?? '-';
                            $tenantType = ($contract->contract_unit->business_type ?? 0) == 1 ? 'B2C' : 'B2B';
                        @endphp

                        <table class="inv-table">
                            <thead>
                                <tr>
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
                                @forelse ($subunits as $i => $subunit)
                                    <tr>
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

                        {{-- Payment --}}
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

                        {{-- Signatures --}}
                        <div class="sig-row">
                            <div class="sig-block">
                                <div class="sig-line">For Faateh Real Estate</div>
                            </div>
                            <div class="sig-block">
                                <div class="sig-line">Receiver's Signature</div>
                            </div>
                        </div>

                    </div>{{-- /invoice-content --}}
                </div>{{-- /invoice-page --}}

                {{-- Action Buttons --}}
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
            // Open invoice in a new blank window so we fully control the print styles
            var invoiceHtml = document.querySelector('.invoice-page').outerHTML;
            var bgImage = document.querySelector('.invoice-page').style.backgroundImage ||
                getComputedStyle(document.querySelector('.invoice-page')).backgroundImage;

            var win = window.open('', '_blank');
            win.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset="utf-8">
                    <title>Invoice</title>
                    <style>
                        * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; color-adjust: exact !important; box-sizing: border-box; }
                        @page { margin: 0; size: A4 portrait; }
                        html, body { margin: 0; padding: 0; background: #fff; }
                        /* Clone all styles from parent */
                        ${Array.from(document.styleSheets).map(ss => {
                            try { return Array.from(ss.cssRules).map(r => r.cssText).join('\n'); }
                            catch(e) { return ''; }
                        }).join('\n')}
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
