 {{-- <div class="invoice-page">
     <div class="invoice-content">
         <div class="inv-title">INVOICE</div>

         @php
             $tenant = $invoice->agreement->tenant ?? null;
             $tenantName = $tenant->tenant_name ?? '-';
             $companyName = $company->company_name ?? '-';
             $trn_number = $tenant?->tenantDocuments?->where('document_type', 3)->first()?->document_number ?? '-';
         @endphp

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
             <strong>License No:
                 {{ str_starts_with($trn_number, '000') ? 'N/A' : $trn_number }}</strong><br>
             <strong>{{ $companyName }} | P - {{ $contract->project_number }}</strong>
         </div>

         <div class="inv-period">
             <strong>For the M/o
                 {{ \Carbon\Carbon::parse($invoice->month_start)->format('d-m-Y') }}
                 to
                 {{ \Carbon\Carbon::parse($invoice->month_end)->format('d-m-Y') }}
             </strong>
         </div>




         <table class="inv-table">
             <thead>
                 <tr>
                     <th>Building Name</th>
                     <th>Flat No.</th>
                     <th>Area</th>
                     <th>Type</th>
                     <th>Tenant Type</th>
                     <th>Amount AED.</th>
                 </tr>
             </thead>
             <tbody>
                 <tr>
                     <td class="c">{{ $buildingName }}</td>
                     <td class="c">{{ $flatNo }}</td>
                     <td class="c">{{ $area }}</td>
                     <td class="c">{{ $unitType }}</td>
                     <td class="c">{{ $tenantType }}</td>
                     <td class="r">{{ number_format($receivableRentPerMonth, 2) }}</td>

             </tbody>
             <tfoot>
                 <tr class="total">
                     <td colspan="5 class="r"><strong>Total Amount---AED</strong></td>
                     <td class="r">
                         <strong>{{ number_format($invoice->total_amount, 2) }}</strong>
                     </td>
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

         <div class="sig-row">


             <div class="sig-block text-right">
                 @if ($isApproved)
                     <div
                         style="
                                                border: 1.5px solid #198555;
                                                border-radius: 5px;
                                                padding: 6px 10px;
                                                display: inline-block;
                                                background: #fff;
                                            ">
                         <div style="display: flex; align-items: center; gap: 5px; margin-bottom: 4px;">
                             <span style="color: #198555; font-size: 11px; font-weight: bold;">✓</span>
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
                 @endif

             </div>

         </div>


     </div>
 </div> --}}


 @foreach ($subunitPages as $pageSubunits)
     @php
         $isFirstPage = $loop->first;
         $isLastPage = $loop->last;
     @endphp

     <div class="invoice-page">
         <div class="invoice-content">
             @if ($isFirstPage)
                 <div class="inv-title">INVOICE</div>

                 @php
                     $tenant = $invoice->agreement->tenant ?? null;
                     $tenantName = $tenant->tenant_name ?? '-';
                     $companyName = $company->company_name ?? '-';
                     $trn_number =
                         $tenant?->tenantDocuments?->where('document_type', 3)->first()?->document_number ?? '-';
                 @endphp

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
                     <strong>License No:
                         {{ str_starts_with($trn_number, '000') ? 'N/A' : $trn_number }}</strong><br>
                     <strong>{{ $companyName }} | P - {{ $contract->project_number }}</strong>
                 </div>

                 <div class="inv-period">
                     <strong>For the M/o
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
                         {{-- <th>Project Number</th> --}}
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
                     @foreach ($pageSubunits as $subunit)
                         <tr>
                             {{-- <td>Project - {{ $project_no }}</td> --}}
                             <td class="c">{{ $buildingName }}</td>
                             <td class="c">{{ $flatNo }}</td>
                             <td class="c">{{ $area }}</td>
                             <td class="c">{{ $unitType }}</td>
                             <td class="c">{{ $tenantType }}</td>
                             <td class="c">
                                 {{ $subunit->contractSubunitDetail->subunit_no ?? '-' }}
                             </td>
                             <td class="r">{{ number_format($subunitRent, 2) }}
                             </td>
                         </tr>
                     @endforeach
                 </tbody>
                 @if ($isLastPage)
                     <tfoot>
                         <tr class="total">
                             <td colspan="6" class="r"><strong>Total Amount---AED</strong>
                             </td>
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
             @endif

             {{-- ===== SIGNATURES ===== --}}
             @if ($isLastPage)
                 <div class="sig-row">

                     {{-- LEFT: Faateh Real Estate — digital approval --}}
                     {{-- <div class="sig-block">


                                                    <div class="sig-line">For Faateh Real Estate</div>
                                                </div> --}}

                     {{-- RIGHT: Receiver --}}
                     <div class="sig-block text-right">
                         @if ($isApproved)
                             <div
                                 style="
                                                            border: 1.5px solid #198555;
                                                            border-radius: 5px;
                                                            padding: 6px 10px;
                                                            display: inline-block;
                                                            background: #fff;
                                                        ">
                                 <div style="display: flex; align-items: center; gap: 5px; margin-bottom: 4px;">
                                     <span style="color: #198555; font-size: 11px; font-weight: bold;">✓</span>
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

             {{-- ===== END SIGNATURES ===== --}}

         </div>{{-- /invoice-content --}}
     </div>{{-- /invoice-page --}}
 @endforeach
