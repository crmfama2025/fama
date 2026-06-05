<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-print-color-adjust: exact;
        }

        /* ── Page size & margins ── */
        @page {
            margin: 0;
            size: A4 portrait;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            margin: 0;
            padding: 0;

        }

        /* ── Letterhead: fixed behind every page ── */
        #letterhead-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 210mm;
            height: 297mm;
            z-index: -1;
        }


        #letterhead-bg img {
            width: 210mm;
            height: 297mm;
        }

        /* ── Content wrapper respects letterhead header/footer space ── */
        #content {
            margin-top: 58mm;
            /* push below letterhead top graphic */
            margin-bottom: 48mm;
            /* stay above letterhead footer graphic */
            margin-left: 15mm;
            margin-right: 15mm;
        }

        /* ── Invoice title ── */
        .inv-title {
            text-align: center;
            font-size: 18px;
            font-weight: 900;
            letter-spacing: 3px;
            margin-bottom: 6px;
            text-decoration: underline;
        }

        /* ── Meta row ── */
        .inv-meta {
            width: 100%;
            margin-bottom: 4px;
        }

        .inv-meta td {
            font-weight: 700;
            font-size: 14px;
            vertical-align: top;
            padding: 0;
        }

        /* ── License line ── */
        .inv-info {
            font-weight: 700;
            font-size: 14px;
            margin-bottom: 4px;
        }

        /* ── Period banner ── */
        .inv-period {
            text-align: center;
            font-weight: 800;
            font-size: 14px;
            border: 1px solid #000;
            padding: 4px;
            margin-bottom: 6px;
            background-color: #f9f9f9;
        }

        /* ── Main invoice table ── */
        .inv-table {
            width: 100%;
            border-collapse: collapse;
            /* font-size: 10px; */
            margin-bottom: 0;
        }

        .inv-table th {
            border: 1px solid #000;
            padding: 5px 4px;
            text-align: center;
            font-weight: 800;
            background-color: #e8e8e8;
            line-height: 1.5 !important;
            font-size: 14px;
        }

        .inv-table td {
            border: 1px solid #000;
            padding: 4px 6px;
            padding: 4px;
            font-weight: 600;
            line-height: 1.5 !important;
            font-size: 14px;
        }

        .inv-table td.c {
            text-align: center;
        }

        .inv-table td.r {
            text-align: right;
        }

        .inv-table tr.total td {
            font-weight: 900;
            background-color: #f0f0f0;
        }

        /* ── Payment section ── */
        .pay-section {
            margin-top: 30px;
            font-weight: 700;
            font-size: 14px;
        }

        .bank-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            margin-top: 4px;
        }

        .bank-table th {
            border: 1px solid #000;
            padding: 5px 4px;
            font-weight: 800;
            background-color: #e8e8e8;
            font-size: 14px;
        }

        .bank-table td {
            border: 1px solid #000;
            padding: 4px 6px;
            font-weight: 600;
            font-size: 14px;
        }

        /* ── Signature ── */
        .sig-row {
            margin-top: 20px;
            text-align: right;
        }

        .approval-box {
            display: inline-block;
            border: 1.5px solid #198555;
            border-radius: 4px;
            padding: 6px 10px;
            background: #fff;
            font-size: 9px;
        }

        .approval-box .check {
            color: #198555;
            font-weight: bold;
            font-size: 11px;
        }

        .approval-box .label {
            color: #198555;
            font-weight: 800;
            font-size: 8px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .approval-box .sub {
            border-top: 0.5px solid #b7ddc8;
            margin-top: 3px;
            padding-top: 3px;
            color: #555;
            font-size: 8px;
        }

        .approval-box .sub span {
            color: #999;
        }

        /* @media print {
            body {
                background-image: url("{{ public_path('images/Faateh-letterhead.png') }}");
                background-size: cover;
                background-position: center;
            }
        } */

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            margin: 0;
            padding: 0;
        }

        #letterhead-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 210mm;
            height: 297mm;
            z-index: 0;
        }

        #letterhead-bg img {
            width: 210mm;
            height: 297mm;
        }

        #content {
            position: relative;
            z-index: 1;
            margin-top: 58mm;
            margin-bottom: 48mm;
            margin-left: 15mm;
            margin-right: 15mm;
        }
    </style>
</head>

<body>

    {{-- ── Fixed letterhead background (repeats on every page) ── --}}
    {{-- <div id="letterhead-bg">
        <img src="{{ public_path('images/Faateh-letterhead.png') }}">
    </div> --}}
    @php
        $rowsPerPage = 25;

        $subunitPages = $subunits->count() ? $subunits->chunk($rowsPerPage) : collect([collect()]);
    @endphp

    @foreach ($subunitPages as $pageSubunits)
        @php
            $isFirstPage = $loop->first;
            $isLastPage = $loop->last;
        @endphp

        <div id="letterhead-bg">
            <img src="file://{{ public_path('images/Faateh-letterhead.png') }}">
        </div>


        {{-- ── Main content ── --}}
        <div id="content">
            @if (!$isFirstPage)
                <div style="height:400px;"></div>
            @else
                <div class="inv-title">INVOICE</div>

                {{-- Meta --}}
                <table class="inv-meta">
                    <tr>
                        <td style="width:50%;">
                            <strong>{{ $tenantName }}</strong><br>UAe.
                        </td>
                        <td style="width:50%; text-align:right;">
                            <strong>Invoice No. {{ $invoice->invoice_no }}</strong><br>
                            <strong>Date: {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d/m/Y') }}</strong>
                        </td>
                    </tr>
                </table>

                {{-- License --}}
                <div class="inv-info">
                    <strong>License No: {{ $trn_number }}</strong>
                </div>

                {{-- Period --}}
                <div class="inv-period">
                    <strong>
                        For the M/o
                        {{ \Carbon\Carbon::parse($invoice->month_start)->format('d-m-Y') }}
                        to
                        {{ \Carbon\Carbon::parse($invoice->month_end)->format('d-m-Y') }}
                    </strong>
                </div>
            @endif


            {{-- Main Table --}}
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
                            <td class="c">Project - {{ $projectNo }}</td>
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
                @if ($isLastPage)
                    <tfoot>
                        <tr class="total">
                            <td colspan="7" class="r"><strong>Total Amount---AED</strong></td>
                            <td class="r"><strong>{{ number_format($invoice->total_amount, 2) }}</strong></td>
                        </tr>
                    </tfoot>
                @endif
            </table>
            @if ($isLastPage)
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
            @endif
            @if ($isLastPage)
                {{-- Signature / Approval --}}
                <div class="sig-row">
                    @if ($isApproved)
                        <div class="approval-box">
                            <span class="check"><i class="fas fa-check check-icon"></i></span>
                            <span class="label">&nbsp;Digitally Approved</span>
                            <div class="sub">
                                <span>Approved by</span>
                                &nbsp;{{ $invoice->approvedBy->first_name ?? '' }}
                                {{ $invoice->approvedBy->last_name ?? '' }}<br>
                                <span>Date</span> &nbsp;{{ $approvedAt }}
                            </div>
                        </div>
                    @endif
                </div>
            @endif

        </div>{{-- /content --}}
    @endforeach

</body>

</html>
