<style>
    @media print {
        body {
            font-size: 20px !important;
            /* font-family: Arial, Helvetica, sans-serif !important; */
            color: #000 !important;
            background: url('{{ public_path('images/fama-letterhead.jpg') }}') no-repeat center center;
            background-size: cover;
        }

        table {
            font-size: 18pt !important;
            border-collapse: collapse;
        }

        td,
        th,
        div,
        span {
            font-size: 14pt !important;
            font-weight: 900 !important;
            /* Force bold */
            line-height: 1.4 !important;
        }

        strong {
            font-weight: 700 !important;
        }

        .text-sm {
            font-size: 18pt !important;
        }

        .text-xs {
            font-size: 14pt !important;
        }
    }
</style>


@php
    $contractType = $agreement->contract->contract_type_id;
    $business_type = $agreement->contract->contract_unit->business_type;
    if ($contractType == 2) {
        $contact_person = $agreement->tenant->contact_person;
        $contact_number = $agreement->tenant->contact_number;
        //   $email = 'Adil@faateh.ae';
        $email = $agreement->tenant->tenant_email;
        $tenant_name = $agreement->tenant->tenant_name;
    } else {
        $tenant_name = $agreement->tenant->tenant_name;
        $contact_number = $agreement->tenant->tenant_mobile;
        $email = $agreement->tenant->tenant_email;
        $contact_person = $agreement->tenant->contact_person;
        //   $total_receivable = $agreement->$contact->
    }
@endphp
{{-- {{ dd($agreement->agreement_units) }} --}}
@foreach ($agreement->agreement_units as $unit)
    {{-- {{ dd($unit) }} --}}
    <table width="100%" align="center" class="mt-5" cellpadding="0" cellspacing="0"
        style="box-shadow:-4px 4px 20px #666; max-width:1025px;{{ !$loop->first ? 'margin-top:100px;' : '' }} ">
        {{-- @if ($page == 1)
            <tbody style="padding: 20px;display: block;">
        @endif --}}

        <tr height="50">
            <td width="100%" colspan="6" style="max-width:1025px;">
                <table width="100%" height="80" border="0" align="center" cellpadding="5" class="table0"
                    style="max-width:1025px;">
                    <tr>
                        <td height="150">
                            <table width="100%" height="70" border="0" align="center" cellpadding="10">
                                <tr>
                                    {{-- <td height="66">
                                        <strong>
                                            @if ($page == 0)
                                                <img width="280" height="90"
                                                    src="{{ public_path('images/fama-dark.png') }}" alt="fama-logo"
                                                    style="margin-left: 10px;margin-top:30px;" />
                                            @else
                                                <img width="280" height="90" src="{{ asset('images/fg.png') }}"
                                                    alt="fama-logo" style="margin-left: 10px;" />
                                            @endif
                                        </strong>
                                    </td>
                                    <td>
                                        <div align="right" style="margin-right: 10px; ">
                                            <strong>
                                                P.O. Box:32963, Dubai, U.A.E<br />
                                        </div>
                                    </td> --}}
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td width="100%" colspan="6" style="max-width:1025px;">
                <table width="100%" border="1" align="center"
                    style="border-collapse: collapse; border: 1px solid #000;">
                    <tr>
                        <td width="15%">
                            <div align="center" class="text-sm text-sm">
                                <strong> <span class="text-sm text-sm style14">Building
                                        Management</span></strong>
                            </div>
                        </td>
                        <td width="11%">
                            <div align="center" class="text-sm text-sm">
                                <strong> <span class="text-sm text-sm style14">Area</span></strong>
                            </div>
                        </td>
                        <td width="15%">
                            <div align="center" class="text-sm text-sm"><strong>Plot No:</strong></div>
                        </td>
                        <td width="15%">
                            <div align="center" class="text-sm text-sm"><strong>Agreement Date:</strong></div>
                        </td>
                        <td rowspan="2">
                            <div align="center" class="text-sm text-sm style72 style8">
                                <strong>Flat No.</strong>
                            </div>
                            <div align="center" class="style72">
                                <strong>{{ $unit->contractUnitDetail->unit_number }}</strong>
                            </div>
                        </td>
                        <td width="16%">
                            <div align="center" class="text-sm text-sm"><strong>Unit Type:</strong></div>
                        </td>
                        <td width="15%">
                            <div align="center" class="text-sm text-sm">
                                <strong>{{ $unit->contractUnitDetail->unit_type->unit_type }}</strong>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div align="center" class="text-sm text-sm">
                                <strong>{{ ucfirst($agreement->contract->vendor->vendor_name) }}</strong>
                            </div>
                        </td>
                        <td>
                            <div align="center" class="text-sm text-sm">
                                <strong>{{ $agreement->contract->locality->locality_name }} -
                                    {{ $agreement->contract->area->area_name }}</strong>
                            </div>
                        </td>
                        <td>
                            <div align="center" class="text-sm text-sm">
                                <strong>{{ $agreement->contract->property->plot_no ?? ' - ' }}</strong>
                            </div>
                        </td>
                        <td>
                            <div align="center" class="text-sm text-sm">
                                <strong>
                                    {{ \Carbon\Carbon::parse($agreement->contract->contract_detail->closing_date)->format('d/m/Y') }}
                                </strong>
                            </div>
                        </td>
                        <td>
                            <div align="center" class="text-sm text-sm">
                                <strong>
                                    Building Name:</strong>
                            </div>
                        </td>
                        <td>
                            <div align="center" class="text-sm text-sm">
                                <strong>
                                    {{ $agreement->contract->property->property_name }}</strong>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td width="26.5%" colspan="2" style="padding-left:6px;" style="padding-left:6px;">
                            <div class="text-sm text-sm ml-1">
                                <strong> <span class="text-sm text-sm style14">Tenant Name:</span></strong>
                            </div>
                        </td>
                        <td colspan="3" width="41.9%" style="padding-left:6px;">
                            <div class="text-sm text-sm ml-1">
                                {{-- <strong> <span
                                    class="text-sm text-am style14">{{ $agreement->tenant->tenant_name }}</span></strong> --}}
                                <strong> <span
                                        class="text-sm text-sm style14 text-uppercase">{{ $tenant_name }}</span></strong>

                            </div>
                        </td>
                        <td width="33%" colspan="2">
                            <div align="center" class="text-sm text-sm"><strong>Project No:
                                    <strong>
                                        {{ $agreement->contract->project_number }}{{ $agreement->contract->parent_contract_id ? ' (Renewal)' : '' }}
                                    </strong>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td width="26.5%" colspan="2" style="padding-left:6px;">
                            <div class="text-sm text-sm ml-1">
                                <strong> <span class="text-sm text-sm style14">Mobile Number</span></strong>
                            </div>
                        </td>
                        <td colspan="2" width="30.9%">
                            <div align="center" class="text-sm text-sm">
                                {{-- <strong>{{ $agreement->tenant->tenant_mobile }}</strong> --}}
                                <strong>{{ $contact_number ?? ' - ' }}</strong>

                            </div>
                        </td>
                        <td>
                            <div align="center" class="text-sm text-sm">
                                <strong>
                                    Email
                                </strong>
                            </div>
                        </td>
                        <td colspan="2">
                            <div align="center" class="text-sm text-sm" style="color: #1a73e8;">
                                {{-- <strong>
                                {{ $agreement->tenant->tenant_email }}</strong> --}}
                                <strong>
                                    {{ $email ?? ' - ' }}</strong>
                            </div>
                        </td>
                    </tr>

                    <tr>

                        <td width="26.5%" colspan="2" style="padding-left:6px;">
                            <div class="text-sm text-sm ml-1">
                                <strong> <span class="text-sm text-sm style14">Contact Person</span></strong>
                            </div>
                        </td>
                        {{-- {{ dd($contact_person) }} --}}
                        <td colspan="2" width="30.9%">
                            <div align="center" class="text-sm text-sm">
                                <strong>{{ ucfirst($contact_person) }}</strong>
                            </div>
                        </td>
                        <td>
                            <div align="center" class="text-sm text-sm">
                                <strong>
                                    TRN No.
                                </strong>
                            </div>
                        </td>
                        <td>
                            <div align="center" class="text-sm text-sm">
                                <strong>
                                    @foreach ($agreement->agreement_documents as $document)
                                        @if ($document->document_type == 3)
                                            {{ $document->document_number ?? ' - ' }}
                                        @endif
                                    @endforeach
                                </strong>
                            </div>
                        </td>
                        <td>
                            <div align="center" class="text-sm text-sm">
                                <strong></strong>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td width="26.5%" colspan="2" style="padding-left:6px;">
                            <div class="text-sm text-sm ml-1">
                                <strong> <span class="text-sm text-sm style14">Start Date</span></strong>
                            </div>
                        </td>
                        <td>
                            <div align="center" class="text-sm text-sm">
                                <strong>{{ \Carbon\Carbon::parse($agreement->start_date)->format('F j, Y') }}</strong>
                            </div>
                        </td>
                        <td>
                            <div align="center" class="text-sm text-sm">
                                <strong>Months/Days</strong>
                            </div>
                        </td>
                        <td>
                            <div align="center" class="text-sm text-sm">
                                <strong>
                                    {{ $agreement->duration_in_months }} M-0D
                                </strong>
                            </div>
                        </td>
                        <td>
                            <div align="center" class="text-sm text-sm">
                                <strong>Expiry</strong>
                            </div>
                        </td>
                        <td>
                            <div align="center" class="text-sm text-sm">
                                <strong>{{ \Carbon\Carbon::parse($agreement->end_date)->format('F j, Y') }}</strong>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td width="26.5%" colspan="2" style="padding-left:6px;">
                            <div class="text-sm text-sm ml-1">
                                <strong> <span class="text-sm text-sm style14">Rent Charges P.A</span></strong>
                            </div>
                        </td>
                        @php
                            if ($contractType == 2) {
                                $rev = $unit->contractUnitDetail->unit_revenue;
                                $depo = $unit->contractUnitDetail->unit_deposit;
                                $rpa = $rev - $depo;
                                $deposit = $unit->contractUnitDetail->unit_deposit;
                            } elseif ($contractType == 2 && $business_type == 1) {
                                $rev = $unit->contractUnitDetail->rent_per_unit_per_annum;
                                $rpa = $rev;
                            } else {
                                //   $rev = $unit->contractUnitDetail->rent_per_unit_per_annum;
                                $rev = $unit->unit_revenue;
                                $rpa = $rev;
                            }

                        @endphp
                        <td>
                            <div class="text-sm text-sm ml-1">
                                <strong>{{ number_format($rpa, 2) }}</strong>
                            </div>
                        </td>
                        <td>
                            <div align="center" class="text-sm text-sm">
                                <strong>Installments</strong>
                            </div>
                        </td>
                        <td>
                            <div align="center" class="text-sm text-sm">
                                <strong>
                                    {{ $agreement->agreement_payment->installment->installment_name }}
                                    <br> Cheques
                                </strong>
                            </div>
                        </td>
                        <td colspan="2">
                            <div align="center" class="text-sm text-sm">
                                <strong>VAT Breakups</strong>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td width="26.5%" colspan="2" style="padding-left:6px;">
                            <div class="text-sm text-sm ml-1">
                                <strong> <span class="text-sm text-sm style14">Rent for Period</span></strong>
                            </div>
                        </td>
                        <td colspan="3" width="41.9%" style="padding-left:6px;" style="padding-left:6px;">
                            <div class="text-sm text-sm ml-1">
                                <strong>{{ number_format($rpa, 2) }}</strong>
                            </div>
                        </td>
                        <td>
                            <div class="text-sm text-sm ml-1" style="padding-left:6px;">
                                <strong>Rent</strong>
                            </div>
                        </td>
                        <td>
                            <div class="text-sm text-sm ml-1" style="padding-left:6px;">
                                <strong>
                                    0.00
                                </strong>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td width="26.5%" colspan="2" style="padding-left:6px;">
                            <div class="text-sm text-sm ml-1">
                                <strong> <span class="text-sm text-sm style14">Commission</span></strong>
                            </div>
                        </td>
                        <td colspan="3" width="41.9%" style="padding-left:6px;">
                            <div class="text-sm text-sm ml-1">
                                <strong>0.00</strong>
                            </div>
                        </td>
                        <td>
                            <div class="text-sm text-sm ml-1" style="padding-left:6px;">
                                <strong>Commission</strong>
                            </div>
                        </td>
                        <td>
                            <div class="text-sm text-sm ml-1" style="padding-left:6px;">
                                <strong>
                                    0.00
                                </strong>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td width="26.5%" colspan="2" style="padding-left:6px;">
                            <div class="text-sm text-sm ml-1">
                                <strong> <span class="text-sm text-sm style14">Refundable Security
                                        Deposit</span></strong>
                            </div>
                        </td>
                        <td colspan="3" width="41.9%" style="padding-left:6px;">
                            <div class="text-sm text-sm ml-1">
                                <strong>{{ $unit->contractUnitDetail->unit_deposit }}</strong>
                            </div>
                        </td>
                        <td>
                            <div align="center" class="text-sm text-sm" style="padding-left:6px;">
                                <strong>RSD</strong>
                            </div>
                        </td>
                        <td>
                            <div class="text-sm text-sm ml-1" style="padding-left:6px;">
                                <strong>
                                    0.00
                                </strong>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td width="26.5%" colspan="2" style="padding-left:6px;">
                            <div class="text-sm text-sm ml-1">
                                <strong> <span class="text-sm text-sm style14">DEWA Deposit</span></strong>
                            </div>
                        </td>
                        <td colspan="3" width="41.9%" style="padding-left:6px;">
                            <div class="text-sm text-sm ml-1">
                                <strong>0.00</strong>
                            </div>
                        </td>
                        <td>
                            <div class="text-sm text-sm ml-1" style="padding-left:6px;">
                                <strong>DEWA Deposit</strong>
                            </div>
                        </td>
                        <td>
                            <div class="text-sm text-sm ml-1" style="padding-left:6px;">
                                <strong>
                                    0.00
                                </strong>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td width="26.5%" colspan="2" style="padding-left:6px;">
                            <div class="text-sm text-sm ml-1">
                                <strong> <span class="text-sm text-sm style14">Admin Charges</span></strong>
                            </div>
                        </td>
                        <td colspan="3" width="41.9%" style="padding-left:6px;">
                            <div class="text-sm text-sm ml-1">
                                <strong>0.00</strong>
                            </div>
                        </td>
                        <td>
                            <div class="text-sm text-sm ml-1" style="padding-left:6px;">
                                <strong>Admin Charges</strong>
                            </div>
                        </td>
                        <td>
                            <div class="text-sm text-sm ml-1" style="padding-left:6px;">
                                <strong>
                                    0.00
                                </strong>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td width="26.5%" colspan="2" style="padding-left:6px;">
                            <div class="text-sm text-sm ml-1">
                                <strong> <span class="text-sm text-sm style14">Chiller</span></strong>
                            </div>
                        </td>
                        <td colspan="3" width="41.9%" style="padding-left:6px;">
                            <div class="text-sm text-sm ml-1">
                                <strong>0.00</strong>
                            </div>
                        </td>
                        <td>
                            <div class="text-sm text-sm ml-1" style="padding-left:6px;">
                                <strong>Chiller</strong>
                            </div>
                        </td>
                        <td>
                            <div class="text-sm text-sm ml-1" style="padding-left:6px;">
                                <strong>
                                    0.00
                                </strong>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td width="26.5%" colspan="2" style="padding-left:6px;">
                            <div class="text-sm text-sm ml-1">
                                <strong> <span class="text-sm text-sm style14">A/C Charges</span></strong>
                            </div>
                        </td>
                        <td colspan="3" width="41.9%" style="padding-left:6px;">
                            <div class="text-sm text-sm ml-1">
                                <strong>0.00</strong>
                            </div>
                        </td>
                        <td>
                            <div class="text-sm text-sm ml-1" style="padding-left:6px;">
                                <strong>A/C Charges</strong>
                            </div>
                        </td>
                        <td>
                            <div class="text-sm text-sm ml-1" style="padding-left:6px;">
                                <strong>
                                    0.00
                                </strong>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td width="26.5%" colspan="2" style="padding-left:6px;">
                            <div class="text-sm text-sm ml-1">
                                <strong> <span class="text-sm text-sm style14">Maint/Others</span></strong>
                            </div>
                        </td>
                        <td colspan="3" width="41.9%" style="padding-left:6px;">
                            <div class="text-sm text-sm ml-1">
                                <strong>0.00</strong>
                            </div>
                        </td>
                        <td>
                            <div class="text-sm text-sm ml-1" style="padding-left:6px;">
                                <strong>Maint/Others</strong>
                            </div>
                        </td>
                        <td>
                            <div class="text-sm text-sm ml-1" style="padding-left:6px;">
                                <strong>
                                    0.00
                                </strong>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td width="26.5%" colspan="2" style="padding-left:6px;">
                            <div class="text-sm text-sm ml-1">
                                <strong> <span class="text-sm text-sm style14">Ejari</span></strong>
                            </div>
                        </td>
                        <td colspan="3" width="41.9%" style="padding-left:6px;">
                            <div class="text-sm text-sm ml-1">
                                <strong>0.00</strong>
                            </div>
                        </td>
                        <td colspan="2">
                            <div align="center" class="text-sm text-sm">
                                <strong></strong>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td width="26.5%" colspan="2" style="padding-left:6px;">
                            <div class="text-sm text-sm ml-1">
                                <strong> <span class="text-sm text-sm style14">Misc</span></strong>
                            </div>
                        </td>
                        <td colspan="3" width="41.9%" style="padding-left:6px;">
                            <div class="text-sm text-sm ml-1">
                                <strong>0.00</strong>
                            </div>
                        </td>
                        <td>
                            <div class="text-sm text-sm ml-1" style="padding-left:6px;">
                                <strong>Total VAT</strong>
                            </div>
                        </td>
                        <td>
                            <div class="text-sm text-sm ml-1" style="padding-left:6px;">
                                <strong>
                                    0.00
                                </strong>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td width="26.5%" colspan="2" style="padding-left:6px;">
                            <div class="text-sm text-sm ml-1">
                                <strong> <span class="text-sm text-sm style14">VAT</span></strong>
                            </div>
                        </td>
                        <td colspan="3" width="41.9%" style="padding-left:6px;">
                            <div class="text-sm text-sm ml-1">
                                <strong>0.00</strong>
                            </div>
                        </td>
                        <td colspan="2">
                            <div align="center" class="text-sm text-sm">
                                <strong>Purpose</strong>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td width="26.5%" colspan="2" style="padding-left:6px;">
                            <div class="text-sm text-sm ml-1">
                                <strong> <span class="text-sm text-sm style14">Total Receivable</span></strong>
                            </div>
                        </td>
                        <td colspan="3" width="41.9%" style="padding-left:6px;">
                            <div class="text-sm text-sm ml-1">
                                <strong>{{ number_format($rev, 2) }}</strong>
                            </div>
                        </td>
                        <td colspan="2">
                            <div align="center" class="text-sm text-sm">
                                <strong>Residential</strong>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td colspan="7">
                            <div align="center" class="text-sm text-sm">
                                <strong>Payment Schedule</strong>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr>
            <td width="100%" colspan="6" style="max-width:1025px;">
                <table width="100%" border="1" align="center"
                    style="border-collapse: collapse; border: 1px solid #000;">
                    <tr>
                        <td width="26.5%">
                            <div align="center" class="text-sm text-sm">
                                <strong> <span class="text-sm text-sm style14">Date</span></strong>
                            </div>
                        </td>
                        <td width="25%">
                            <div align="center" class="text-sm text-sm">
                                <strong> <span class="text-sm text-sm style14">Amount</span></strong>
                            </div>
                        </td>
                        <td width="22%">
                            <div align="center" class="text-sm text-sm"><strong>Cash/ Cheque/
                                    Transfer</strong></div>
                        </td>
                        <td width="15%">
                            <div align="center" class="text-sm text-sm"><strong>Composition</strong></div>
                        </td>
                    </tr>

                    @foreach ($agreement->agreement_payment->agreementPaymentDetails->where('agreement_unit_id', $unit->id) as $index => $detail)
                        <tr>
                            <td width="15%">
                                <div align="center" class="text-sm text-sm">
                                    <strong> <span
                                            class="text-sm text-sm style14"></span>{{ \Carbon\Carbon::parse($detail->payment_date)->format('F j, Y') }}</strong>
                                </div>
                            </td>
                            <td width="11%">
                                <div align="center" class="text-sm text-sm">
                                    <strong> <span
                                            class="text-sm text-sm style14"></span>{{ number_format($detail->payment_amount, 2) }}</strong>
                                </div>
                            </td>
                            <td width="15%">
                                <div align="center" class="text-sm text-sm"><strong>Bank Transfer
                                        #{{ $loop->iteration }}:
                                    </strong>
                                </div>
                            </td>
                            <td width="15%">
                                <div align="center" class="text-sm text-sm"><strong>| Rent
                                        {{ $loop->iteration }}/{{ $agreement->agreement_payment->installment->installment_name }}
                                        - {{ number_format($detail->payment_amount, 2) }}</strong>
                                </div>
                            </td>
                        </tr>
                    @endforeach





                    <tr>
                        <td width="15%">
                            <div align="center" class="text-sm text-sm">
                                <strong> <span class="text-sm text-sm style14">&nbsp;</span></strong>
                            </div>
                        </td>
                        <td width="11%">
                            <div align="center" class="text-sm text-sm">
                                <strong> <span class="text-sm text-sm style14"></span></strong>
                            </div>
                        </td>
                        <td width="15%" colspan="2">
                            <div align="center" class="text-sm text-sm"><strong></strong></div>
                        </td>
                    </tr>

                    <tr>
                        <td width="15%" style="padding-left:6px;">
                            <div class="text-sm text-sm ml-1">
                                <strong> <span class="text-sm text-sm style14">To Collect</span></strong>
                            </div>
                        </td>
                        <td width="15%" colspan="3" style="padding-left:6px;">
                            <div class="text-sm text-sm ml-1"><strong>All as above on or before due
                                    date</strong></div>
                        </td>
                    </tr>

                    <tr>
                        <td width="100%" colspan="4" style="padding-left:10px;">
                            <div class="text-xs ml-1" style="font-size: .65rem !important"><strong>Additional
                                    Terms:</br>
                                    1. I the tenant hereby agree to follow the laws of the UAE and undertake not
                                    to carry out any illegal activity in the above premises. If there is breach
                                    of this rule, I undertake to vacate the premises immediately without any
                                    compensation.</br>
                                    2. It is agreed that if any of the cheque issued by the tenant for rental or
                                    other payments is returned unpaid by the bank for any reason then there will
                                    be a penalty of AED 1000/- for each cheque on each occasion plus the tenant
                                    has to make good the cheque amount by cash immediately failing which he will
                                    have to vacate the premises immediately and landlord has full right to enter
                                    and take over the flat in the event of non-payment of rent.</br>
                                    3. Tenant hereby confirms and agrees with the Landlord that they will not
                                    hang clothes to dry in the balconies and outside the windows in compliance
                                    with municipal regulations and if there is breach on the part of the tenant
                                    then he is responsible for the municipal fines if any.</br>
                                    4. Tenant hereby confirms and agrees with the Landlord that more than 4
                                    persons will not occupy a flat at any time.</br>
                                    5. Tenant hereby confirms and agrees that they will not make any alteration
                                    or additions in the leased premises and not to fix showers in half
                                    bathrooms.</br>
                                    6. All levies due to the Government including VAT or other levies as and
                                    when applicable is payable by the tenant.</br>
                                    7. Tenant must inform the landlord 90 days before expiry of contract whether
                                    they want to renew or vacate the Unit and if the Tenant fails to inform then
                                    contract will be renewed for 1 year.</br>
                                    8. Tenant must pay an amount of AED 1000/- as Admin Charges.</br>
                                    9. In case of Contract cancellation, Faateh Real Estate must return the flat
                                    same as at time of procurement time.</br>
                                </strong>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td width="100%" colspan="6" style="max-width:1025px;">
                <table width="100%" border="1" align="center" class="table0"
                    style="max-width:1025px;border-collapse: collapse; border: 1px solid #000;">
                    <tr>
                        <td style="padding-left:6px;">
                            <div class="ml-1 text-sm ">
                                <strong>
                                    Agreed & Accepted By: Fama Real Estate
                                </strong>
                            </div>
                        </td>
                        <td style="padding-left:6px;">
                            <div class="ml-1 text-sm ">
                                <strong>Agreed & Accepted By: Faateh Real Estate</strong>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td height="70">


                        </td>
                        <td height="70">

                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        {{-- @if ($page == 1)
            </tbody>
        @endif --}}

    </table>
@endforeach
