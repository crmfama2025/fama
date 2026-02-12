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
                         <th>Invested Company</th>
                         <th>Investment Date</th>
                         <th>Investment Amount</th>
                         <th>Received</th>
                         <th>Pending</th>
                         <th>Profit</th>
                         <th>Status</th>
                     </tr>
                 </thead>

                 <tbody>
                     <tr>
                         <td>{{ $investment->investment_code }}</td>
                         <td>{{ $investment->company->company_name }}</td>
                         <td>{{ $investment->investedCompany->company_name ?? ' - ' }}</td>
                         <td>{{ getFormattedDate($investment->investment_date) }}</td>
                         <td>{{ number_format($investment->investment_amount, 2) }}</td>
                         <td class="text-success">
                             {{ number_format($investment->total_received_amount, 2) }}
                         </td>
                         <td class="text-danger">
                             {{ number_format($investment->balance_amount, 2) }}
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
             <i class="fas fa-file-invoice-dollar mr-2"></i> Profit Release Details
         </h3>
     </div>

     <div class="card-body p-0">
         <div class="table-responsive">
             <table class="table table-bordered table-striped ">
                 <thead class="bg-light">
                     <tr>
                         {{-- <th>#</th> --}}
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

 </div>
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
