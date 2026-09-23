 @if ($investment->parentInvestment)
     <div class="alert alert-default-info ">
         <strong>Reinvestment</strong><br>
         Parent Investment:
         <a href="{{ route('investment.show', $investment->parentInvestment->id) }}" class="text-bold text-info">
             #{{ $investment->parentInvestment->investment_code }}
         </a>
     </div>
 @endif
 <div class="card card-outline card-info">


     <div class="card-header">
         <h3 class="card-title text-teal text-bold">
             <i class="fas fa-file-invoice-dollar mr-2"></i> Investment Details
         </h3>
     </div>

     <div class="card-body p-0">
         <div class="table-responsive">
             <table class="table table-bordered table-striped ">
                 <thead class="bg-light">
                     <tr>
                         {{-- <th>#</th> --}}
                         <th>Investment Code</th>
                         <th>Company</th>
                         <th>Investment Date</th>
                         <th>Investment Amount</th>
                         <th>Investment Type</th>
                         <th>Received</th>
                         <th>Pending</th>
                         <th>Profit %</th>
                         <th>Profit</th>
                         <th>Status</th>
                     </tr>
                 </thead>

                 <tbody>
                     <tr>
                         <td>{{ $investment->investment_code }}</td>
                         <td>{{ $investment->company->company_name }}</td>
                         <td>{{ getFormattedDate($investment->investment_date) }}</td>
                         <td>{{ number_format($investment->investment_amount, 2) }} -
                             {{ $investment->investment_amount_arabic ?? ' - ' }}</td>
                         <td>{{ $investment->investment_term_type == 1 ? 'Long Term' : 'Short Term' }}</td>
                         <td class="text-success">
                             {{ number_format($investment->total_received_amount, 2) }}
                         </td>
                         <td class="text-danger">
                             {{ number_format($investment->balance_amount, 2) }}
                         </td>
                         <td class="text-info">
                             {{ $investment->profit_perc }}%
                         </td>
                         <td class="text-info">
                             {{ number_format($investment->profit_amount, 2) }}
                         </td>
                         <td>
                             @if ($investment->investment_status == 1)
                                 <span class="badge badge-success">Active</span>
                             @else
                                 <span class="badge badge-secondary">Terminated</span>
                             @endif
                         </td>
                     </tr>

                 </tbody>
             </table>
         </div>

     </div>

 </div>

 <div class="card card-outline card-info">


     <div class="card-header">
         <h3 class="card-title text-teal text-bold">
             <i class="fas fa-file-invoice-dollar mr-2"></i> Allocated Company
         </h3>
     </div>

     <div class="card-body p-0">
         <div class="table-responsive">
             <table class="table table-bordered table-striped ">
                 <thead class="bg-light">
                     <tr>
                         <th>#</th>
                         <th>Invested Company</th>
                         <th>Allocated Amount</th>
                     </tr>
                 </thead>

                 <tbody>
                     @forelse ($investment->companyAllocations as $item)
                         <tr>
                             <td>{{ $loop->iteration }}</td>
                             <td>{{ $item->company?->company_name ?? '-' }}</td>
                             <td>{{ number_format($item->allocated_amount, 2) }}</td>
                         </tr>
                     @empty
                         @if ($investment->invested_company_id)
                             <tr>
                                 <td>1</td>
                                 <td>
                                     {{ $investment->investedCompany?->company_name ?? '-' }}
                                 </td>
                                 <td>{{ number_format($investment->investment_amount, 2) }}</td>
                             </tr>
                         @else
                             <tr>
                                 <td colspan="3" class="text-center text-muted">
                                     No company allocation recorded.
                                 </td>
                             </tr>
                         @endif
                     @endforelse
                 </tbody>
             </table>
         </div>

     </div>

 </div>

 <div class="card card-outline card-info">
     <div class="card-header">
         <h3 class="card-title text-teal text-bold">
             <i class="fas fa-user mr-2"></i> Bank Details
         </h3>
     </div>

     <div class="card-body p-0">
         <div class="table-responsive">
             <table class="table table-bordered table-striped">
                 <thead class="bg-light">
                     <tr>
                         {{-- <th>#</th> --}}
                         <th>Bank Name</th>
                         <th>Iban</th>
                         <th>Account Number</th>

                     </tr>
                 </thead>

                 <tbody>
                     <tr>
                         <td>{{ $investment->companyBank->bank_name ?? ' - ' }}</td>
                         <td>{{ $investment->company_bank_iban ?? ' - ' }}</td>
                         <td>{{ $investment->company_bank_account_number ?? ' - ' }}</td>
                     </tr>

                 </tbody>
             </table>
         </div>
     </div>

 </div>

 <div class="card card-outline card-info">
     <div class="card-header">
         <h3 class="card-title text-teal text-bold">
             <i class="fas fa-file-alt mr-2"></i> Investment Document
         </h3>
     </div>

     <div class="card-body p-0">
         <div class="table-responsive">
             <table class="table table-bordered table-striped">
                 <thead class="bg-light">
                     <tr>
                         <th>Uploaded On</th>
                         <th>Type</th>
                         <th>File</th>
                     </tr>
                 </thead>

                 <tbody>
                     @if ($document)
                         <tr>
                             <td>
                                 {{ $document->created_at?->format('d M Y') ?? '-' }}
                             </td>
                             <td>Contract File</td>

                             <td>
                                 @if (!empty($document->investment_contract_file_path))
                                     <a href="{{ asset('storage/' . $document->investment_contract_file_path) }}"
                                         target="_blank" class="btn btn-xs btn-primary">
                                         <i class="fas fa-eye"></i> view
                                     </a>

                                     <a href="{{ asset('storage/' . $document->investment_contract_file_path) }}"
                                         download class="btn btn-xs btn-success">
                                         <i class="fas fa-download"></i>Download
                                     </a>
                                 @endif

                             </td>
                         </tr>
                     @else
                         <tr>
                             <td colspan="5" class="text-center text-muted">
                                 <i class="fas fa-exclamation-triangle"></i>
                                 No document found for this investment
                             </td>
                         </tr>
                     @endif
                 </tbody>
             </table>
         </div>
     </div>
 </div>
 @if ($investment->terminate_status !== 0)
     <div class="card card-outline card-info">
         <div class="card-header">
             <h3 class="card-title text-teal text-bold">
                 <i class="fas fa-file-alt mr-2"></i> Investment Termination
             </h3>
         </div>

         <div class="card-body p-0">
             <div class="table-responsive">
                 <table class="table table-bordered table-striped">
                     <thead class="bg-light">
                         <tr>
                             <th>Termination Status</th>
                             <th>Requested Date</th>
                             <th>Duration</th>
                             <th>Termination Date</th>
                             <th>Document</th>
                         </tr>
                     </thead>

                     <tbody>
                         <tr>
                             <td>
                                 @if ($investment->terminate_status == 1)
                                     <span class="badge badge-warning">Termination Requested</span>
                                 @elseif($investment->terminate_status == 2)
                                     <span class="badge badge-danger">Terminated</span>
                                 @endif
                             </td>
                             <td><span
                                     class="badge badge-light text-sm">{{ getFormattedDate($investment->termination_requested_date) }}</span>
                             </td>
                             <td><span
                                     class="badge badge-light text-sm text-danger">{{ $investment->termination_duration }}
                                     Days</span>
                             </td>
                             <td><span
                                     class="badge badge-light text-sm">{{ getFormattedDate($investment->termination_date) }}</span>
                             </td>
                             <td>
                                 @if (!empty($investment->termination_document))
                                     <a href="{{ asset('storage/' . $investment->termination_document) }}"
                                         target="_blank" class="btn btn-xs btn-primary">
                                         <i class="fas fa-eye"></i> view
                                     </a>

                                     <a href="{{ asset('storage/' . $investment->termination_document) }}" download
                                         class="btn btn-xs btn-success">
                                         <i class="fas fa-download"></i>Download
                                     </a>
                                 @endif
                             </td>

                         </tr>

                     </tbody>
                 </table>
             </div>
         </div>
     </div>
 @endif

 <div class="card card-outline card-info">
     <div class="card-header">
         <h3 class="card-title text-teal text-bold">
             <i class="fas fa-user mr-2"></i> Nominee Details
         </h3>
     </div>

     <div class="card-body p-0">
         <div class="table-responsive">
             <table class="table table-bordered table-striped">
                 <thead class="bg-light">
                     <tr>
                         {{-- <th>#</th> --}}
                         <th>Nominee Name</th>
                         <th>Nominee Email</th>
                         <th>Nominee Phone</th>

                     </tr>
                 </thead>

                 <tbody>
                     <tr>
                         <td>{{ $investment->nominee_name ?? ' - ' }}</td>
                         <td>{{ $investment->nominee_email ?? ' - ' }}</td>
                         <td>{{ $investment->nominee_phone ?? ' - ' }}</td>
                     </tr>

                 </tbody>
             </table>
         </div>
     </div>

 </div>

 <div class="card card-outline card-info">


     <div class="card-header">
         <h3 class="card-title text-teal text-bold">
             <i class="fas fa-file-invoice-dollar mr-2"></i> Investment Renewal Record
         </h3>
     </div>

     <div class="card-body p-0">
         <div class="table-responsive">
             <table class="table table-bordered table-striped ">
                 <thead class="bg-light">
                     <tr>
                         <th>#</th>
                         <th>Renewal Date</th>
                         <th>Renewal Count</th>
                         <th>Modifications</th>
                     </tr>
                 </thead>

                 <tbody>
                     @php
                         $renewalLogs = $investment->RenewalEditLog()->where('reason', 'renewal')->get();

                         $renewalCount = 0;
                         $previousRenewalDate = null;

                         $renewalLogs->each(function ($item) use (&$renewalCount, &$previousRenewalDate) {
                             $renewalDate = $item->created_at; //->toDateString()

                             if ($renewalDate !== $previousRenewalDate) {
                                 $renewalCount++;
                                 $previousRenewalDate = $renewalDate;
                             }

                             $item->renewal_count = $renewalCount;
                         });
                     @endphp
                     @forelse ($renewalLogs as $item)

                         <tr>
                             <td>{{ $loop->iteration }}</td>
                             <td>{{ $item->created_at?->format('d-m-Y') }}</td>
                             <td>Renewal {{ $item->renewal_count }}</td>
                             <td>
                                 @if (!empty($item->changes))
                                     <table class="table table-sm table-bordered mb-0">
                                         <thead class="table-light">
                                             <tr>
                                                 <th>Field Name</th>
                                                 <th>Previous value</th>
                                                 <th>New value</th>
                                             </tr>
                                         </thead>
                                         <tbody>
                                             @foreach ($item->changes as $field => $change)
                                                 @php
                                                     $old = data_get($change, 'old', '-');
                                                     $new = data_get($change, 'new', '-');

                                                     if ($field === 'maturity_date') {
                                                         $old =
                                                             $old !== '-' && $old
                                                                 ? \Carbon\Carbon::parse($old)->format('d/m/Y')
                                                                 : '-';

                                                         $new =
                                                             $new !== '-' && $new
                                                                 ? \Carbon\Carbon::parse($new)->format('d/m/Y')
                                                                 : '-';
                                                     }
                                                 @endphp

                                                 <tr>
                                                     <td class="text-capitalize">
                                                         {{ str_replace('_', ' ', $field) }}
                                                     </td>
                                                     <td>{{ $old }}</td>
                                                     <td>{{ $new }}</td>
                                                 </tr>
                                             @endforeach
                                         </tbody>
                                     </table>
                                 @else
                                     <span class="text-muted">No field modifications</span>
                                 @endif
                             </td>
                         </tr>


                         {{-- @foreach ($item->schedule_changes ?? [] as $scheduleChange)
                             <tr>
                                 <td>{{ $item->created_at?->format('d-m-Y H:i') }}</td>
                                 <td>Schedule: {{ $scheduleChange['action'] ?? '-' }}</td>
                                 <td>
                                     {{ data_get($scheduleChange, 'old.profit_amount', '-') }}
                                 </td>
                                 <td>
                                     {{ data_get($scheduleChange, 'new.profit_amount', '-') }}
                                 </td>
                             </tr>
                         @endforeach --}}
                     @empty
                         <tr>
                             <td colspan="4" class="text-center">No renewal modifications found.</td>
                         </tr>
                     @endforelse
                 </tbody>
             </table>
         </div>

     </div>

 </div>


 @php
     $profitRecords = $investment->profitRecords
         ->where('has_profit_amount', 1)
         ->sortBy(function ($record) {
             return \Carbon\Carbon::parse($record->getRawOriginal('profit_release_month'))->timestamp;
         })
         ->values();

     $renewalCounts = $profitRecords
         ->map(fn($record) => (int) ($record->renewal_count ?? 0))
         ->unique()
         ->sort()
         ->values();

     $selectedRenewalCount = $renewalCounts->isNotEmpty() ? $renewalCounts->max() : null;
 @endphp

 <div class="card card-outline card-success">
     <div class="card-header">
         <h3 class="card-title font-weight-bold">
             <i class="fas fa-file-invoice-dollar text-success mr-2"></i>
             Profit Details
         </h3>
     </div>

     <div class="card-body">

         <div class="row mb-3">
             <div class="col-md-3">
                 <label for="renewalFilter">Show Schedule</label>

                 <select id="renewalFilter" class="form-control form-control-md">
                     <option value="">All Schedules</option>

                     @foreach ($renewalCounts as $renewalCount)
                         <option value="{{ $renewalCount }}"
                             {{ $renewalCount === $selectedRenewalCount ? 'selected' : '' }}>
                             {{ $renewalCount === 0 ? 'New Investment' : 'Renewal ' . $renewalCount }}
                         </option>
                     @endforeach
                 </select>
             </div>
         </div>

         <div class="table-responsive">
             <table class="table table-bordered table-striped table-sm">
                 <thead>
                     <tr>
                         <th style="width: 70px;">#</th>
                         <th>Profit Date</th>
                         <th>Profit Amount</th>
                         <th>Renewal Count</th>
                         <th>Release Status</th>
                         <th>Total Released</th>
                         <th>Released Date</th>
                     </tr>
                 </thead>

                 <tbody>
                     @forelse ($profitRecords as $profitRecord)
                         @php
                             $renewalCount = (int) ($profitRecord->renewal_count ?? 0);

                         @endphp

                         <tr class="profit-record-row" data-renewal="{{ $renewalCount }}">

                             <td class="profit-row-number">
                                 {{ $loop->iteration }}
                             </td>

                             <td>
                                 <span class="badge badge-light text-sm">
                                     {{ getFormattedDate($profitRecord->profit_release_month) }}
                                 </span>
                             </td>

                             <td>
                                 <span class="badge badge-light text-sm">
                                     {{ $profitRecord->profit_amount !== null ? number_format($profitRecord->profit_amount, 2) : '-' }}
                                 </span>
                             </td>

                             <td>
                                 <span class="badge badge-light text-sm">
                                     {{ $renewalCount === 0 ? 'New' : 'Renewal ' . $renewalCount }}
                                 </span>
                             </td>

                             <td>
                                 <span class="badge badge-light text-sm text-danger">
                                     {{ toFirstCaps($profitRecord->release_status) }}
                                 </span>
                             </td>

                             <td>
                                 <span class="text-bold text-sm">
                                     {{ $profitRecord->released_total_amount !== null ? number_format($profitRecord->released_total_amount, 2) : '-' }}
                                 </span>
                             </td>

                             <td>
                                 <span class="text-bold text-sm">
                                     {{ $profitRecord->last_released_at ? getFormattedDate($profitRecord->last_released_at) : '-' }}
                                 </span>
                             </td>
                         </tr>
                     @empty
                         <tr>
                             <td colspan="7" class="text-center text-muted">
                                 No profit records found.
                             </td>
                         </tr>
                     @endforelse
                 </tbody>
             </table>
         </div>
     </div>
 </div>

 @section('custom_js')
     <script>
         $(document).ready(function() {
             function filterProfitRecords() {
                 const selectedRenewal = String($('#renewalFilter').val());
                 let visibleRowNumber = 1;

                 $('.profit-record-row').each(function() {
                     const renewalCount = String($(this).data('renewal'));

                     const shouldShow = selectedRenewal === '' ||
                         renewalCount === selectedRenewal;

                     $(this).toggle(shouldShow);

                     if (shouldShow) {
                         $(this).find('.profit-row-number').text(visibleRowNumber++);
                     }
                 });
             }

             $('#renewalFilter').on('change', filterProfitRecords);

             // On first page load: show only the highest/current renewal.
             filterProfitRecords();
         });
     </script>
 @endsection


 {{-- <div class="card card-outline card-info">
     <div class="card-header">
         <h3 class="card-title text-teal text-bold">
             <i class="fas fa-file-invoice-dollar mr-2"></i> Profit Release Details
         </h3>
     </div>

     <div class="card-body p-0">
         <div class="table-responsive">
             <table class="table table-bordered table-striped ">
                 <thead class="bg-light">
                     <tr>
                         <th>Last Profit Released on</th>
                         <th>Profit Release Due on</th>
                         <th>Outstanding Profit</th>
                         <th>Payout Batch</th>
                         <th>Profit Interval</th>

                     </tr>
                 </thead>

                 <tbody>
                     <tr>
                         <td>
                             <span class="badge badge-light text-sm">
                                 {{ getFormattedDate($investment->last_profit_released_date) }}
                             </span>
                         </td>
                         <td>
                             <span class="badge badge-light text-sm">
                                 {{ getFormattedDate($investment->next_profit_release_date) ?? ' - ' }}
                             </span>
                         </td>
                         <td>
                             <span class="badge badge-light text-sm text-danger">
                                 {{ number_format($investment->outstanding_profit, 2) }}
                             </span>
                         </td>
                         <td>
                             <span class="text-bold text-sm ">
                                 {{ $investment->payoutBatch->batch_name }}
                             </span>
                         </td>
                         <td>
                             <span class="text-bold text-sm ">
                                 {{ $investment->profitInterval->profit_interval_name }}
                             </span>
                         </td>

                     </tr>

                 </tbody>
             </table>
         </div>

     </div>

 </div> --}}
