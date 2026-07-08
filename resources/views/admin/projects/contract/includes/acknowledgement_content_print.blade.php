<style>
    body {
        /* color: #000 !important; */
        background: url('{{ public_path('images/fama-letterhead.png') }}') no-repeat center center;
        background-size: cover;
    }
</style>
<div style="height: 100px;">&nbsp;</div>
<table width="88%" border="0" align="center" cellpadding="5" style="margin-top: 50px;">
    <tr>
        <td align="left" valign="top">
            <table width="95%" border="0" align="center" cellpadding="5" style="width:95%;">
                <tr>

                    <td>
                        <div align="left" class="mx-5"><strong>Date:
                                {{ $contract->acknowledgement_released_date }}</strong>
                        </div>
                    </td>
                    {{-- <td >
                        @if ($page == 0)
                            <div align="right" class="mx-5"><img width="300" height="100"
                                    src="{{ public_path('images/fama-dark.png') }}" alt="fama-logo"></div>
                        @else
                            <div align="right" class="mx-5"><img width="300" height="100"
                                    src="{{ asset('images/fama-dark.png') }}" alt="fama-logo"></div>
                        @endif
                    </td> --}}
                </tr>
                <tr>
                    <td colspan="3">
                        <div align="center">
                            <h3 class="style10"><strong>ACKNOWLEDGEMENT OF PAYMENT</strong></h4>
                        </div>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

</table>

<table width="88%" border="0" align="center" cellpadding="5">
    <tr>
        <td align="left" valign="top">
            <table width="95%" border="0" align="center" cellpadding="5" style="width:95%;">
                <tr>

                    <td>
                        <div align="left" class="mx-5"><strong>From,<br>
                                <p class="mt-2">Fama Real Estate</p>
                                P.O.Box : 32693<br>
                                Dubai, UAE
                            </strong>
                        </div>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<table width="88%" border="0" align="center" cellpadding="5">
    <tr>
        <td align="left" valign="top">
            <table width="95%" border="0" align="center" cellpadding="5" style="width:95%;">
                <tr>
                    <td>
                        <div align="left" class="mx-5">We,<strong>
                                {{ strtoupper($contract->vendor->vendor_name) }}</strong>
                            here by
                            acknowledge
                            the receipts of
                            the below
                            mentioned cheques ({{ paymentModeCount($contract->contract_payment_details) }}) in
                            favour against the below
                            Contract details.

                        </div>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<table width="88%" border="0" align="center" cellpadding="5">

    <tr>
        <td align="left" valign="top">
            <div class="mx-5">
                <table width="95%" border="2" align="center" cellpadding="2"
                    style="border-collapse: collapse; width:95%;background-color: #dadaff;border: solid 2px;">
                    <tr style="border: solid 1px;">
                        <td colspan="2">
                            <div align="center"><strong> Contract Details</strong></div>
                        </td>
                    </tr>
                    <tr style="border: solid 1px;">
                        <td width="50%" style="border: solid 1px;">
                            <div align="left" class="mx-1">Building Name</div>
                        </td>
                        <td style="border: solid 1px;">
                            <div align="center">{{ $contract->property->property_name }}</div>
                        </td>
                    </tr>
                    <tr style="border: solid 1px;">
                        <td width="50%" style="border: solid 1px;">
                            <div align="left" class="mx-1">Property Type</div>
                        </td>
                        <td style="border: solid 1px;">
                            <div align="center">{{ $contract->contract_unit->property_type }}</div>
                        </td>
                    </tr>
                    <tr style="border: solid 1px;">
                        <td width="50%" style="border: solid 1px;">
                            <div align="left" class="mx-1">Plot Number</div>
                        </td>
                        <td style="border: solid 1px;">
                            <div align="center">{{ $contract->property->plot_no }}</div>
                        </td>
                    </tr>
                    <tr style="border: solid 1px;">
                        <td width="50%" style="border: solid 1px;">
                            <div align="left" class="mx-1">Project Status</div>
                        </td>
                        <td style="border: solid 1px;">
                            <div align="center">
                                {{ empty($contract->parent_contract_id) ? 'New' : 'Renewal' }}
                            </div>
                        </td>
                    </tr>
                    <tr style="border: solid 1px;">
                        <td width="50%" style="border: solid 1px;">
                            <div align="left" class="mx-1">Renewal Number</div>
                        </td>
                        <td style="border: solid 1px;">
                            {{-- <div align="center">{{ $contract->renewal_count ?? 0 }}</div> --}}
                            <div align="center">{{ $contract->parent ? $contract->parent->project_number : '-' }}
                            </div>
                        </td>
                    </tr>
                    <tr style="border: solid 1px;">
                        <td width="50%" style="border: solid 1px;">
                            <div align="left" class="mx-1">Flat No.</div>
                        </td>
                        <td style="border: solid 1px;">
                            <div align="center">{{ $contract->contract_unit->unit_numbers }}</div>
                        </td>
                    </tr>
                    <tr style="border: solid 1px;">
                        <td width="50%" style="border: solid 1px;">
                            <div align="left" class="mx-1">Number of Floors</div>
                        </td>
                        <td style="border: solid 1px;">
                            <div align="center">{{ $contract->contract_unit->no_of_floors }}</div>
                        </td>
                    </tr>
                    <tr style="border: solid 1px;">
                        <td width="50%" style="border: solid 1px;">
                            <div align="left" class="mx-1">Floor Number</div>
                        </td>
                        <td style="border: solid 1px;">
                            <div align="center">{{ $contract->contract_unit->floor_numbers }}</div>
                        </td>
                    </tr>
                    <tr style="border: solid 1px;">
                        <td width="50%" style="border: solid 1px;">
                            <div align="left" class="mx-1">Number of Houses</div>
                        </td>
                        <td style="border: solid 1px;">
                            <div align="center">{{ $contract->contract_unit->no_of_units }}</div>
                        </td>
                    </tr>
                    <tr style="border: solid 1px;">
                        <td width="50%" style="border: solid 1px;">
                            <div align="left" class="mx-1">Closing Date</div>
                        </td>
                        <td style="border: solid 1px;">
                            <div align="center">{{ $contract->contract_detail->closing_date }}</div>
                        </td>
                    </tr>
                    <tr style="border: solid 1px;">
                        <td width="50%" style="border: solid 1px;">
                            <div align="left" class="mx-1">Unit Type</div>
                        </td>
                        <td style="border: solid 1px;">
                            <div align="center">{{ formatUnitTypes($contract->contract_unit->unit_type_count) }}
                            </div>
                        </td>
                    </tr>
                    <tr style="border: solid 1px;">
                        <td width="50%" style="border: solid 1px;">
                            <div align="left" class="mx-1">Contract Start Date</div>
                        </td>
                        <td style="border: solid 1px;">
                            <div align="center">{{ $contract->contract_detail->start_date }}</div>
                        </td>
                    </tr>
                    <tr style="border: solid 1px;">
                        <td width="50%" style="border: solid 1px;">
                            <div align="left" class="mx-1">Contract End Date</div>
                        </td>
                        <td style="border: solid 1px;">
                            <div align="center">{{ $contract->contract_detail->end_date }}</div>
                        </td>
                    </tr>
                    <tr style="border: solid 1px;">
                        <td width="50%" style="border: solid 1px;">
                            <div align="left" class="mx-1">Grace Period</div>
                        </td>
                        <td style="border: solid 1px;">
                            <div align="center">
                                {{ $contract->contract_detail->grace_period ? $contract->contract_detail->grace_period . ' Month' : '-' }}
                            </div>
                        </td>
                    </tr>
                    <tr style="border: solid 1px;">
                        <td width="50%" style="border: solid 1px;">
                            <div align="left" class="mx-1">Total Amount of Contract</div>
                        </td>
                        <td style="border: solid 1px;">
                            <div align="center">{{ $contract->contract_rentals->rent_per_annum_payable }}</div>
                        </td>
                    </tr>
                    <tr style="border: solid 1px;">
                        <td width="50%" style="border: solid 1px;">
                            <div align="left" class="mx-1">Deposit
                                {{ toNumeric($contract->contract_rentals->deposit_percentage) }}%</div>
                        </td>
                        <td style="border: solid 1px;">
                            <div align="center">{{ toNumeric($contract->contract_rentals->deposit) }}</div>
                        </td>
                    </tr>
                    <tr style="border: solid 1px;">
                        <td width="50%" style="border: solid 1px;">
                            <div align="left" class="mx-1">Commission
                                {{ toNumeric($contract->contract_rentals->commission_percentage) }}%</div>
                        </td>
                        <td style="border: solid 1px;">
                            <div align="center">{{ toNumeric($contract->contract_rentals->commission) }}</div>
                        </td>
                    </tr>
                    <tr style="border: solid 1px;">
                        <td width="50%" style="border: solid 1px;">
                            <div align="left" class="mx-1">Number of Payments</div>
                        </td>
                        <td style="border: solid 1px;">
                            <div align="center">{{ count($contract->contract_payment_details) }}</div>
                        </td>
                    </tr>
                    <tr style="border: solid 2px;">
                        <td width="50%" style="border: solid 2px;">
                            <div align="left" class="mx-1"><strong>Total Payable Amount</strong></div>
                        </td>
                        <td style="border: solid 1px;">
                            <div align="center">
                                <strong>{{ $contract->contract_rentals->total_payment_to_vendor }}</strong>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>

        </td>
    </tr>
</table>

<table width="88%" border="0" align="center" cellpadding="0">
    <tr>
        <td align="left" valign="top">
            <table width="95%" border="0" align="center" cellpadding="0" style="width:95%;">
                <tr>

                    <td>
                        <div align="left" class="mx-5 mt-4"><strong>Cheque Details:</strong>
                        </div>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<table width="88%" border="0" align="center" cellpadding="5">

    <tr>

        <td align="left" valign="top">
            <div class="mx-5">

                <table width="95%" border="2" align="center" cellpadding="2" style="width:95%;">
                    <tr style="border: solid 1px;">
                        <td style="border: solid 1px;">
                            <div align="left" class="mx-1"><strong>Cheque/Payment Date</strong></div>
                        </td>
                        <td style="border: solid 1px;">
                            <div align="left" class="mx-1"><strong>Bank Name</strong></div>
                        </td>
                        <td style="border: solid 1px;">
                            <div align="left" class="mx-1"><strong>Payment Method</strong></div>
                        </td>
                        <td style="border: solid 1px;">
                            <div align="left" class="mx-1"><strong>Cheque Number</strong></div>
                        </td>
                        <td style="border: solid 1px;">
                            <div align="center" class="mx-1"><strong>Payment Amount</strong></div>
                        </td>

                    </tr>

                    @foreach ($contract->contract_payment_details as $payment)
                        <tr style="border: solid 1px;">
                            <td style="border: solid 1px;">
                                <div align="left" class="mx-1">{{ $payment->payment_date }}</div>
                            </td>
                            <td style="border: solid 1px;">
                                <div align="left" class="mx-1">{{ $payment->bank?->bank_name }}</div>
                            </td>
                            <td style="border: solid 1px;">
                                <div align="left" class="mx-1">
                                    {{ $payment->payment_mode->payment_mode_name }}</div>
                            </td>
                            <td style="border: solid 1px;">
                                <div align="left" class="mx-1">{{ $payment->cheque_no }}</div>
                            </td>
                            <td style="border: solid 1px;">
                                <div align="center" class="mx-1">{{ formatNumber($payment->payment_amount) }}
                                </div>
                            </td>

                        </tr>
                    @endforeach
                    <!-- Total Row -->
                    <tr style="border: solid 1px; font-weight: bold;">
                        <td colspan="4" style="border: solid 1px;">
                            <div align="right" class="mx-1">Total</div>
                        </td>
                        <td style="border: solid 1px;">
                            <div align="center" class="mx-1">
                                {{ $contract->contract_rentals->total_payment_to_vendor }}</div>
                        </td>
                    </tr>
                </table>
            </div>

        </td>
    </tr>
</table>

<table width="88%" border="0" align="center" cellpadding="5">
    <tr>
        <td align="left" valign="top">
            <table width="95%" border="0" align="center" cellpadding="5" style="width:95%;">
                <tr>

                    <td>
                        <div align="left" class="mx-5 mb-5">
                            <p>l acknowledge that l have received
                                the
                                cheques with above details for the contract mentioned.</p>

                            <p>Authorized Signatory with Seal (Recipient):</p>
                            <p>Name: </p>

                        </div>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
