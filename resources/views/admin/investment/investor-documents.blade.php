   <div class="tab-pane" id="investmentDocuments">
       <div class="card card-outline card-info">
           <div class="card-header">
               <h3 class="card-title"><i class="fas fa-coins mr-1"></i> Investment Details
               </h3>
           </div>


           <!-- /.card-header -->
           <div class="card-body table-responsive">
               <div class="mb-3 text-center">
                   <div class="btn-group btn-group-toggle" data-toggle="buttons">
                       <label class="btn btn-outline-primary active">
                           <input type="radio" name="agreementFilter" value="all" autocomplete="off" checked> All
                       </label>
                       <label class="btn btn-outline-success">
                           <input type="radio" name="agreementFilter" value="1" autocomplete="off">
                           Pending
                       </label>
                       <label class="btn btn-outline-warning">
                           <input type="radio" name="agreementFilter" value="2" autocomplete="off">
                           Investor Signed
                       </label>
                       <label class="btn btn-outline-danger">
                           <input type="radio" name="agreementFilter" value="3" autocomplete="off">
                           Both Signed
                       </label>
                   </div>
               </div>

               <table id="investmentContractsTable"
                   class="table table-striped table-bordered table-responsive nowrap collapsed"width="100%">
                   <thead>
                       <tr>
                           <th>#</th>
                           {{-- <th>Status</th> --}}
                           <th>Signed Status</th>
                           <th>Company</th>
                           <th>Investment</th>
                           <th>Contract Type</th>
                           <th>Generated Version</th>
                           <th>Generated Document</th>
                           <th>Uploaded Documents</th>
                           <th>Generated Date</th>
                       </tr>
                   </thead>
                   <tbody>
                       @forelse($investmentDocuments as $document)
                           <tr>
                               <td>{{ $loop->iteration }}</td>

                               {{-- <td>
                                   @if ($document->generated_date)
                                       <span class="badge badge-success">Generated</span>
                                   @else
                                       <span class="badge badge-warning">Pending</span>
                                   @endif
                               </td> --}}

                               <td>
                                   @if ($document->is_investor_signed == 0 && $document->is_company_signed == 0)
                                       <span class="badge badge-warning">Pending</span>
                                   @elseif($document->is_investor_signed == 1 && $document->is_company_signed == 0)
                                       <span class="badge badge-info">Investor Signed</span>
                                   @elseif($document->is_investor_signed == 1 && $document->is_company_signed == 1)
                                       <span class="badge badge-success">Both Signed</span>
                                   @endif
                               </td>
                               <td>{{ $document->company->company_name }}</td>
                               <td>
                                   @if ($document->investment_id != 0)
                                       <a href="{{ route('investment.show', $document->investment_id) }}">
                                           {{ $document->investment?->investment_code ?? '-' }}
                                       </a>
                                   @else
                                       -
                                   @endif
                               </td>

                               <td>
                                   {{ $document->agreementType?->investor_agreement_type ?? '-' }}
                               </td>

                               <td>
                                   @if ($document->action_type == 0)
                                       {{-- upload --}}
                                       V{{ $document->version_number }}
                                   @else
                                       {{-- Generated from CRM --}}
                                       V{{ $document->agreementTemplate?->version_no ?? '-' }}
                                   @endif
                               </td>
                               <td>
                                   @if ($document->action_type == 1)
                                       <a href="{{ route('legal_template.contractview', [
                                           'docId' => $document->id,
                                           'companyId' => $document->company_id,
                                       ]) }}"
                                           class="btn btn-sm btn-success m-1" title="View Document">
                                           <i class="fas fa-external-link-alt"></i>
                                       </a>
                                   @else
                                       -
                                   @endif
                               </td>

                               <td>
                                   @forelse($document->investmentDocuments as $mainDocument)
                                       @if ($document->action_type == 0)
                                           @if ($mainDocument->investment_contract_file_path)
                                               <div class="mb-2">
                                                   <a href="{{ Storage::url($mainDocument->investment_contract_file_path) }}"
                                                       target="_blank" title="click to View" class="text-blue">
                                                       {{ $mainDocument->agreementType->investor_agreement_type ?? 'View Document' }}
                                                       - V{{ $mainDocument->version_number }}
                                                   </a>
                                                   <div class="px-5 pl-3">
                                                       @foreach ($mainDocument->additionalDocuments as $additionalDocument)
                                                           @if ($additionalDocument->document_path)
                                                               <li>
                                                                   <a href="{{ Storage::url($additionalDocument->document_path) }}"
                                                                       target="_blank" title="click to View">
                                                                       {{ $additionalDocument->document_name ?? 'Additional Document' }}
                                                                   </a>
                                                               </li>
                                                           @endif
                                                       @endforeach
                                                   </div>
                                               </div>
                                           @endif
                                       @elseif($document->action_type == 1)
                                           <div class="mb-2">
                                               <div class="px-5 pl-3">
                                                   @foreach ($mainDocument->additionalDocuments as $additionalDocument)
                                                       @if ($additionalDocument->document_path)
                                                           <li style="list-style-type: none;">
                                                               <a href="{{ Storage::url($additionalDocument->document_path) }}"
                                                                   target="_blank" title="click to View">
                                                                   {{ $additionalDocument->document_name ?? 'Additional Document' }}
                                                               </a>
                                                           </li>
                                                       @endif
                                                   @endforeach
                                               </div>
                                           </div>
                                       @endif
                                   @empty
                                       -
                                   @endforelse
                               </td>


                               <td>
                                   {{ $document->generated_date ? \Carbon\Carbon::parse($document->generated_date)->format('d M Y h:i A') : '-' }}
                               </td>
                           </tr>
                       @empty
                           <tr>
                               <td colspan="10" class="text-center">
                                   No investment documents found.
                               </td>
                           </tr>
                       @endforelse
                   </tbody>
               </table>
           </div>
           <!-- /.card-body -->
       </div>
   </div>
