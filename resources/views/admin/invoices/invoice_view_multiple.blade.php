@extends('admin.layout.admin_master')

@section('custom_css')
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="{{ asset('assets/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/adminlte.min.css') }}">

    <style>
        .invoice-page {
            position: relative;
            width: 210mm;
            height: 297mm;
            margin: 0 auto 40px auto;
            background-color: #fff;
            background-image: url("{{ asset('images/Faateh-letterhead.png') }}");
            background-size: 100% 100%;
            background-repeat: no-repeat;
            background-position: center center;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            box-shadow: 0 4px 24px #aaa;
            overflow: hidden;
        }

        .invoice-content {
            position: relative;
            padding: 42mm 16mm 35mm 16mm;
        }

        .continued-space {
            height: 28mm;
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

        .inv-table,
        .bank-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }

        .inv-table th,
        .bank-table th {
            border: 1px solid #000;
            padding: 5px 6px;
            text-align: center;
            font-weight: 800;
            background: rgba(240, 240, 240, 0.85);
        }

        .inv-table td,
        .bank-table td {
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
            margin-top: 5px;
        }

        .sig-row {
            display: flex;
            justify-content: end;
            margin-top: 30px;
            align-items: flex-end;
        }

        .sig-block {
            text-align: right;
            width: 42%;
            font-weight: 700;
            font-size: 12px;
        }

        @media print {
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color-adjust: exact !important;
                box-sizing: border-box;
            }

            @page {
                size: A4 portrait;
                margin: 0;
            }

            html,
            body {
                width: 210mm;
                margin: 0 !important;
                padding: 0 !important;
                background: #fff !important;
            }

            .no-print {
                display: none !important;
            }

            .content-wrapper,
            .content,
            .container-fluid,
            #invoice-print-area {
                width: 210mm !important;
                margin: 0 !important;
                padding: 0 !important;
                background: transparent !important;
            }

            .invoice-page {
                width: 210mm !important;
                height: 297mm !important;
                min-height: 297mm !important;
                max-width: none !important;
                margin: 0 !important;
                box-shadow: none !important;
                page-break-after: always;
                break-after: page;
                overflow: hidden;
                background-image: url("{{ asset('images/Faateh-letterhead.png') }}") !important;
                background-size: 100% 100% !important;
                background-repeat: no-repeat !important;
                background-position: center center !important;
            }

            .invoice-page:last-child {
                page-break-after: auto;
                break-after: auto;
            }

            .invoice-content {
                padding: 42mm 16mm 35mm 16mm !important;
            }

            .continued-space {
                height: 28mm !important;
            }

            .inv-table tr,
            .bank-table tr,
            .pay-section,
            .sig-row {
                page-break-inside: avoid;
                break-inside: avoid;
            }

            .inv-table td,
            .inv-table th {
                padding: 3px 5px;
                height: 8mm;
                font-size: 11px;
            }
        }
    </style>
@endsection

@section('content')
    @php
        $tenant = $invoice->agreement->tenant ?? null;
        $tenantName = $tenant->tenant_name ?? '-';

        $agreementUnit = $invoice->agreementUnit;
        $subunits = $agreementUnit->agreementSubunitRentBifurcation ?? collect();

        $contract = $invoice->contract;
        $buildingName = $contract->property->property_name ?? '-';
        $flatNo = $agreementUnit->contractUnitDetail->unit_number ?? '-';
        $area = $contract->area->area_name ?? '-';
        $unitType = $agreementUnit->contractUnitDetail->unit_type->unit_type ?? '-';
        $tenantType = ($contract->contract_unit->business_type ?? 0) == 1 ? 'B2B' : 'B2C';
        $project_no = $contract->project_number;

        $isApproved = ($invoice->status ?? '') === 2;

        $approvedAt =
            $isApproved && $invoice->approved_date
                ? \Carbon\Carbon::parse($invoice->approved_date)->format('d/m/Y H:i')
                : null;

        $rowsPerPage = 25;

        $subunitPages = $subunits->count() ? $subunits->chunk($rowsPerPage) : collect([collect()]);
        $trn_number = $tenant?->tenantDocuments?->where('document_type', 3)->first()?->document_number ?? '-';
    @endphp

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

                <div id="invoice-print-area">
                    @foreach ($subunitPages as $pageSubunits)
                        @php
                            $isFirstPage = $loop->first;
                            $isLastPage = $loop->last;
                        @endphp

                        <div class="invoice-page">
                            <div class="invoice-content">

                                @if ($isFirstPage)
                                    <div class="inv-title">INVOICE</div>

                                    <div class="inv-meta">
                                        <div>
                                            <strong>{{ $tenantName }}</strong><br>UAE.
                                        </div>
                                        <div style="text-align:right;">
                                            <strong>Invoice No. {{ $invoice->invoice_no }}</strong><br>
                                            <strong>Date:
                                                {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d/m/Y') }}</strong>
                                        </div>
                                    </div>

                                    <div class="inv-info">
                                        <strong>License No: {{ $trn_number }}</strong>
                                    </div>

                                    <div class="inv-period">
                                        <strong>
                                            For the M/o
                                            {{ \Carbon\Carbon::parse($invoice->month_start)->format('d-m-Y') }}
                                            to
                                            {{ \Carbon\Carbon::parse($invoice->month_end)->format('d-m-Y') }}
                                        </strong>
                                    </div>
                                @else
                                    <div class="continued-space"></div>
                                @endif

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
                                        @forelse ($pageSubunits as $subunit)
                                            <tr>
                                                <td>Project - {{ $project_no }}</td>
                                                <td class="c">{{ $buildingName }}</td>
                                                <td class="c">{{ $flatNo }}</td>
                                                <td class="c">{{ $area }}</td>
                                                <td class="c">{{ $unitType }}</td>
                                                <td class="c">{{ $tenantType }}</td>
                                                <td class="c">{{ $subunit->contractSubunitDetail->subunit_no ?? '-' }}
                                                </td>
                                                <td class="r">{{ number_format($subunit->rent_per_month, 2) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td>Project - {{ $project_no }}</td>
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

                                    @if ($isLastPage)
                                        <tfoot>
                                            <tr class="total">
                                                <td colspan="7" class="r"><strong>Total Amount---AED</strong></td>
                                                <td class="r">
                                                    <strong>{{ number_format($invoice->total_amount, 2) }}</strong>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    @endif
                                </table>

                                @if ($isLastPage)
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

                                    <div class="sig-row">
                                        <div class="sig-block">
                                            @if ($isApproved)
                                                <div
                                                    style="border: 1.5px solid #198555; border-radius: 5px; padding: 6px 10px; display: inline-block; background: #fff;">
                                                    <div
                                                        style="display: flex; align-items: center; gap: 5px; margin-bottom: 4px;">
                                                        <span
                                                            style="color: #198555; font-size: 11px; font-weight: bold;">&#10003;</span>
                                                        <span
                                                            style="font-size: 9px; font-weight: 600; color: #198555; letter-spacing: 0.4px; text-transform: uppercase;">
                                                            Digitally Approved
                                                        </span>
                                                    </div>
                                                    <div style="border-top: 0.5px solid #b7ddc8; padding-top: 4px;">
                                                        <p style="font-size: 8.5px; color: #555; margin: 0 0 1px;">
                                                            <span style="color: #999;">Approved by</span>
                                                            &nbsp;{{ $invoice->approvedBy->first_name ?? '' }}
                                                            {{ $invoice->approvedBy->last_name ?? '' }}
                                                        </p>
                                                        <p style="font-size: 8.5px; color: #555; margin: 0;">
                                                            <span style="color: #999;">Date</span>
                                                            &nbsp;{{ $approvedAt }}
                                                        </p>
                                                    </div>
                                                </div>
                                            @else
                                                <div style="height: 60px;"></div>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4 mb-5 text-center no-print">
                    <a href="{{ route('invoices.index') }}" class="btn btn-secondary mr-2">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>

                    @if ($invoice->status == 2)
                        <button onclick="printInvoice()" class="btn btn-primary">
                            <i class="fas fa-print"></i> Print
                        </button>
                    @endif
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
            const invoiceHtml = document.querySelector('#invoice-print-area').innerHTML;
            const styles = Array.from(document.querySelectorAll('style'))
                .map(style => style.innerHTML)
                .join('\n');

            const win = window.open('', '_blank');

            win.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset="utf-8">
                    <title>Invoice</title>
                    <style>
                        ${styles}
                    </style>
                </head>
                <body>
                    <div id="invoice-print-area">
                        ${invoiceHtml}
                    </div>

                    <script>
                        window.onload = function() {
                            setTimeout(function() {
                                window.print();
                            }, 300);

                            window.onafterprint = function() {
                                window.close();
                            };
                        };
                    <\/script>
                </body>
                </html>
            `);

            win.document.close();
        }
    </script>
@endsection
