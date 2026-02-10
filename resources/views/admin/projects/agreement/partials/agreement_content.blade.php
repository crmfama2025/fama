<style>
    @media print {
        body {
            font-size: 12pt !important;
            font-family: Arial, Helvetica, sans-serif !important;
            color: #000 !important;
        }

        table {
            font-size: 11pt !important;
            border-collapse: collapse;
        }

        td,
        th,
        div,
        span {
            font-size: 11pt !important;
            font-weight: 600 !important;
            /* Force bold */
            line-height: 1.4 !important;
        }

        strong {
            font-weight: 700 !important;
        }

        .text-sm {
            font-size: 11pt !important;
        }

        .text-xs {
            font-size: 10pt !important;
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
    <table width="100%" border="2" align="center" class="mt-5" cellpadding="0" cellspacing="0"
        style="box-shadow:-4px 4px 20px #666; max-width:1025px;">
        @if ($page == 1)
            <tbody style="padding: 20px;display: block;">
        @endif

        <tr height="21">
            <td width="100%" colspan="6" bgcolor="#FFFFFF" style="max-width:1025px;">
                <table width="100%" height="80" border="0" align="center" cellpadding="5" class="table0"
                    style="max-width:1025px;">
                    <tr>
                        <td height="70">
                            <table width="100%" height="70" border="0" align="center" cellpadding="10">
                                <tr>
                                    <td height="66">
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
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td width="100%" colspan="6" bgcolor="#FFFFFF" style="max-width:1025px;">
                <table width="100%" border="1" align="center">
                    <tr>
                        <td width="15%" bgcolor="#FFFFFF">
                            <div align="center" class="text-sm text-sm">
                                <strong> <span class="text-sm text-sm style14">Building
                                        Management</span></strong>
                            </div>
                        </td>
                        <td width="11%" bgcolor="#FFFFFF">
                            <div align="center" class="text-sm text-sm">
                                <strong> <span class="text-sm text-sm style14">Area</span></strong>
                            </div>
                        </td>
                        <td width="15%" bgcolor="#FFFFFF">
                            <div align="center" class="text-sm text-sm"><strong>Plot No:</strong></div>
                        </td>
                        <td width="15%" bgcolor="#FFFFFF">
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
                        <td bgcolor="#FFFFFF">
                            <div align="center" class="text-sm text-sm">
                                <strong>{{ ucfirst($agreement->contract->vendor->vendor_name) }}</strong>
                            </div>
                        </td>
                        <td bgcolor="#FFFFFF">
                            <div align="center" class="text-sm text-sm">
                                <strong>{{ $agreement->contract->locality->locality_name }} -
                                    {{ $agreement->contract->area->area_name }}</strong>
                            </div>
                        </td>
                        <td bgcolor="#FFFFFF">
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
                        <td width="26.5%" bgcolor="#FFFFFF" colspan="2">
                            <div class="text-sm text-sm ml-1">
                                <strong> <span class="text-sm text-sm style14">Tenant Name:</span></strong>
                            </div>
                        </td>
                        <td colspan="3" width="41.9%" bgcolor="#FFFFFF">
                            <div class="text-sm text-sm ml-1">
                                {{-- <strong> <span
                                    class="text-sm text-am style14">{{ $agreement->tenant->tenant_name }}</span></strong> --}}
                                <strong> <span
                                        class="text-sm text-sm style14 text-uppercase">{{ $tenant_name }}</span></strong>

                            </div>
                        </td>
                        <td width="33%" colspan="2" bgcolor="#FFFFFF">
                            <div align="center" class="text-sm text-sm"><strong>Project No:
                                    {{ $agreement->contract->project_number }}</strong></div>
                        </td>
                    </tr>
                    <tr>
                        <td width="26.5%" bgcolor="#FFFFFF" colspan="2">
                            <div class="text-sm text-sm ml-1">
                                <strong> <span class="text-sm text-sm style14">Mobile Number</span></strong>
                            </div>
                        </td>
                        <td bgcolor="#FFFFFF" colspan="2" width="30.9%">
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

                        <td width="26.5%" bgcolor="#FFFFFF" colspan="2">
                            <div class="text-sm text-sm ml-1">
                                <strong> <span class="text-sm text-sm style14">Contact Person</span></strong>
                            </div>
                        </td>
                        {{-- {{ dd($contact_person) }} --}}
                        <td bgcolor="#FFFFFF" colspan="2" width="30.9%">
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
                        <td width="26.5%" bgcolor="#FFFFFF" colspan="2">
                            <div class="text-sm text-sm ml-1">
                                <strong> <span class="text-sm text-sm style14">Start Date</span></strong>
                            </div>
                        </td>
                        <td bgcolor="#FFFFFF">
                            <div align="center" class="text-sm text-sm">
                                <strong>{{ \Carbon\Carbon::parse($agreement->start_date)->format('F j, Y') }}</strong>
                            </div>
                        </td>
                        <td bgcolor="#FFFFFF">
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
                        <td width="26.5%" bgcolor="#FFFFFF" colspan="2">
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
                        <td bgcolor="#FFFFFF">
                            <div class="text-sm text-sm ml-1">
                                <strong>{{ number_format($rpa, 2) }}</strong>
                            </div>
                        </td>
                        <td bgcolor="#FFFFFF">
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
                        <td width="26.5%" bgcolor="#FFFFFF" colspan="2">
                            <div class="text-sm text-sm ml-1">
                                <strong> <span class="text-sm text-sm style14">Rent for Period</span></strong>
                            </div>
                        </td>
                        <td bgcolor="#FFFFFF" colspan="3" width="41.9%">
                            <div class="text-sm text-sm ml-1">
                                <strong>{{ number_format($rpa, 2) }}</strong>
                            </div>
                        </td>
                        <td bgcolor="#FFFFFF">
                            <div class="text-sm text-sm ml-1">
                                <strong>Rent</strong>
                            </div>
                        </td>
                        <td>
                            <div class="text-sm text-sm ml-1">
                                <strong>
                                    0.00
                                </strong>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td width="26.5%" bgcolor="#FFFFFF" colspan="2">
                            <div class="text-sm text-sm ml-1">
                                <strong> <span class="text-sm text-sm style14">Commission</span></strong>
                            </div>
                        </td>
                        <td bgcolor="#FFFFFF" colspan="3" width="41.9%">
                            <div class="text-sm text-sm ml-1">
                                <strong>0.00</strong>
                            </div>
                        </td>
                        <td bgcolor="#FFFFFF">
                            <div class="text-sm text-sm ml-1">
                                <strong>Commission</strong>
                            </div>
                        </td>
                        <td>
                            <div class="text-sm text-sm ml-1">
                                <strong>
                                    0.00
                                </strong>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td width="26.5%" bgcolor="#FFFFFF" colspan="2">
                            <div class="text-sm text-sm ml-1">
                                <strong> <span class="text-sm text-sm style14">Refundable Security
                                        Deposit</span></strong>
                            </div>
                        </td>
                        <td bgcolor="#FFFFFF" colspan="3" width="41.9%">
                            <div class="text-sm text-sm ml-1">
                                <strong>{{ $unit->contractUnitDetail->unit_deposit }}</strong>
                            </div>
                        </td>
                        <td bgcolor="#FFFFFF">
                            <div align="center" class="text-sm text-sm">
                                <strong>RSD</strong>
                            </div>
                        </td>
                        <td>
                            <div class="text-sm text-sm ml-1">
                                <strong>
                                    0.00
                                </strong>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td width="26.5%" bgcolor="#FFFFFF" colspan="2">
                            <div class="text-sm text-sm ml-1">
                                <strong> <span class="text-sm text-sm style14">DEWA Deposit</span></strong>
                            </div>
                        </td>
                        <td bgcolor="#FFFFFF" colspan="3" width="41.9%">
                            <div class="text-sm text-sm ml-1">
                                <strong>0.00</strong>
                            </div>
                        </td>
                        <td bgcolor="#FFFFFF">
                            <div class="text-sm text-sm ml-1">
                                <strong>DEWA Deposit</strong>
                            </div>
                        </td>
                        <td>
                            <div class="text-sm text-sm ml-1">
                                <strong>
                                    0.00
                                </strong>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td width="26.5%" bgcolor="#FFFFFF" colspan="2">
                            <div class="text-sm text-sm ml-1">
                                <strong> <span class="text-sm text-sm style14">Admin Charges</span></strong>
                            </div>
                        </td>
                        <td bgcolor="#FFFFFF" colspan="3" width="41.9%">
                            <div class="text-sm text-sm ml-1">
                                <strong>0.00</strong>
                            </div>
                        </td>
                        <td bgcolor="#FFFFFF">
                            <div class="text-sm text-sm ml-1">
                                <strong>Admin Charges</strong>
                            </div>
                        </td>
                        <td>
                            <div class="text-sm text-sm ml-1">
                                <strong>
                                    0.00
                                </strong>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td width="26.5%" bgcolor="#FFFFFF" colspan="2">
                            <div class="text-sm text-sm ml-1">
                                <strong> <span class="text-sm text-sm style14">Chiller</span></strong>
                            </div>
                        </td>
                        <td bgcolor="#FFFFFF" colspan="3" width="41.9%">
                            <div class="text-sm text-sm ml-1">
                                <strong>0.00</strong>
                            </div>
                        </td>
                        <td bgcolor="#FFFFFF">
                            <div class="text-sm text-sm ml-1">
                                <strong>Chiller</strong>
                            </div>
                        </td>
                        <td>
                            <div class="text-sm text-sm ml-1">
                                <strong>
                                    0.00
                                </strong>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td width="26.5%" bgcolor="#FFFFFF" colspan="2">
                            <div class="text-sm text-sm ml-1">
                                <strong> <span class="text-sm text-sm style14">A/C Charges</span></strong>
                            </div>
                        </td>
                        <td bgcolor="#FFFFFF" colspan="3" width="41.9%">
                            <div class="text-sm text-sm ml-1">
                                <strong>0.00</strong>
                            </div>
                        </td>
                        <td bgcolor="#FFFFFF">
                            <div class="text-sm text-sm ml-1">
                                <strong>A/C Charges</strong>
                            </div>
                        </td>
                        <td>
                            <div class="text-sm text-sm ml-1">
                                <strong>
                                    0.00
                                </strong>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td width="26.5%" bgcolor="#FFFFFF" colspan="2">
                            <div class="text-sm text-sm ml-1">
                                <strong> <span class="text-sm text-sm style14">Maint/Others</span></strong>
                            </div>
                        </td>
                        <td bgcolor="#FFFFFF" colspan="3" width="41.9%">
                            <div class="text-sm text-sm ml-1">
                                <strong>0.00</strong>
                            </div>
                        </td>
                        <td bgcolor="#FFFFFF">
                            <div class="text-sm text-sm ml-1">
                                <strong>Maint/Others</strong>
                            </div>
                        </td>
                        <td>
                            <div class="text-sm text-sm ml-1">
                                <strong>
                                    0.00
                                </strong>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td width="26.5%" bgcolor="#FFFFFF" colspan="2">
                            <div class="text-sm text-sm ml-1">
                                <strong> <span class="text-sm text-sm style14">Ejari</span></strong>
                            </div>
                        </td>
                        <td bgcolor="#FFFFFF" colspan="3" width="41.9%">
                            <div class="text-sm text-sm ml-1">
                                <strong>0.00</strong>
                            </div>
                        </td>
                        <td bgcolor="#FFFFFF" colspan="2">
                            <div align="center" class="text-sm text-sm">
                                <strong></strong>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td width="26.5%" bgcolor="#FFFFFF" colspan="2">
                            <div class="text-sm text-sm ml-1">
                                <strong> <span class="text-sm text-sm style14">Misc</span></strong>
                            </div>
                        </td>
                        <td bgcolor="#FFFFFF" colspan="3" width="41.9%">
                            <div class="text-sm text-sm ml-1">
                                <strong>0.00</strong>
                            </div>
                        </td>
                        <td bgcolor="#FFFFFF">
                            <div class="text-sm text-sm ml-1">
                                <strong>Total VAT</strong>
                            </div>
                        </td>
                        <td>
                            <div class="text-sm text-sm ml-1">
                                <strong>
                                    0.00
                                </strong>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td width="26.5%" bgcolor="#FFFFFF" colspan="2">
                            <div class="text-sm text-sm ml-1">
                                <strong> <span class="text-sm text-sm style14">VAT</span></strong>
                            </div>
                        </td>
                        <td bgcolor="#FFFFFF" colspan="3" width="41.9%">
                            <div class="text-sm text-sm ml-1">
                                <strong>0.00</strong>
                            </div>
                        </td>
                        <td bgcolor="#FFFFFF" colspan="2">
                            <div align="center" class="text-sm text-sm">
                                <strong>Purpose</strong>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td width="26.5%" bgcolor="#FFFFFF" colspan="2">
                            <div class="text-sm text-sm ml-1">
                                <strong> <span class="text-sm text-sm style14">Total Receivable</span></strong>
                            </div>
                        </td>
                        <td bgcolor="#FFFFFF" colspan="3" width="41.9%">
                            <div class="text-sm text-sm ml-1">
                                <strong>{{ number_format($rev, 2) }}</strong>
                            </div>
                        </td>
                        <td bgcolor="#FFFFFF" colspan="2">
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
            <td width="100%" colspan="6" bgcolor="#FFFFFF" style="max-width:1025px;">
                <table width="100%" border="1" align="center">
                    <tr>
                        <td width="26.5%" bgcolor="#FFFFFF">
                            <div align="center" class="text-sm text-sm">
                                <strong> <span class="text-sm text-sm style14">Date</span></strong>
                            </div>
                        </td>
                        <td width="25%" bgcolor="#FFFFFF">
                            <div align="center" class="text-sm text-sm">
                                <strong> <span class="text-sm text-sm style14">Amount</span></strong>
                            </div>
                        </td>
                        <td width="22%" bgcolor="#FFFFFF">
                            <div align="center" class="text-sm text-sm"><strong>Cash/ Cheque/
                                    Transfer</strong></div>
                        </td>
                        <td width="15%" bgcolor="#FFFFFF">
                            <div align="center" class="text-sm text-sm"><strong>Composition</strong></div>
                        </td>
                    </tr>

                    @foreach ($agreement->agreement_payment->agreementPaymentDetails->where('agreement_unit_id', $unit->id) as $index => $detail)
                        <tr>
                            <td width="15%" bgcolor="#FFFFFF">
                                <div align="center" class="text-sm text-sm">
                                    <strong> <span
                                            class="text-sm text-sm style14"></span>{{ \Carbon\Carbon::parse($detail->payment_date)->format('F j, Y') }}</strong>
                                </div>
                            </td>
                            <td width="11%" bgcolor="#FFFFFF">
                                <div align="center" class="text-sm text-sm">
                                    <strong> <span
                                            class="text-sm text-sm style14"></span>{{ number_format($detail->payment_amount, 2) }}</strong>
                                </div>
                            </td>
                            <td width="15%" bgcolor="#FFFFFF">
                                <div align="center" class="text-sm text-sm"><strong>Bank Transfer
                                        #{{ $loop->iteration }}:
                                    </strong>
                                </div>
                            </td>
                            <td width="15%" bgcolor="#FFFFFF">
                                <div align="center" class="text-sm text-sm"><strong>| Rent
                                        {{ $loop->iteration }}/{{ $agreement->agreement_payment->installment->installment_name }}
                                        - {{ number_format($detail->payment_amount, 2) }}</strong>
                                </div>
                            </td>
                        </tr>
                    @endforeach





                    <tr>
                        <td width="15%" bgcolor="#FFFFFF">
                            <div align="center" class="text-sm text-sm">
                                <strong> <span class="text-sm text-sm style14">&nbsp;</span></strong>
                            </div>
                        </td>
                        <td width="11%" bgcolor="#FFFFFF">
                            <div align="center" class="text-sm text-sm">
                                <strong> <span class="text-sm text-sm style14"></span></strong>
                            </div>
                        </td>
                        <td width="15%" colspan="2" bgcolor="#FFFFFF">
                            <div align="center" class="text-sm text-sm"><strong></strong></div>
                        </td>
                    </tr>

                    <tr>
                        <td width="15%" bgcolor="#FFFFFF">
                            <div class="text-sm text-sm ml-1">
                                <strong> <span class="text-sm text-sm style14">To Collect</span></strong>
                            </div>
                        </td>
                        <td width="15%" colspan="3" bgcolor="#FFFFFF">
                            <div class="text-sm text-sm ml-1"><strong>All as above on or before due
                                    date</strong></div>
                        </td>
                    </tr>

                    <tr>
                        <td width="100%" colspan="4" bgcolor="#FFFFFF">
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
            <td width="100%" colspan="6" bgcolor="#FFFFFF" style="max-width:1025px;">
                <table width="100%" border="1" align="center" class="table0" style="max-width:1025px;">
                    <tr>
                        <td>
                            <div class="ml-1 text-sm ">
                                <strong>
                                    Agreed & Accepted By: Fama Real Estate
                                </strong>
                            </div>
                        </td>
                        <td>
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
        @if ($page == 1)
            </tbody>
        @endif

    </table>
@endforeach
