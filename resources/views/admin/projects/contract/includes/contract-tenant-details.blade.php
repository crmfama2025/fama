<div class="col-6">
    <div class="card">
        <div class="card-header bg-gradient-olive">
            <h4 class="card-title w-100">
                {{ $agreementUnit->agreement->agreement_code }}
            </h4>
        </div>
        <div class="card-body text-muted">

            {{-- <div class="col-sm-6"> --}}
            <address>
                <span>Tenant Name :
                    {{ $agreementUnit->agreement->tenant->tenant_name }}</span></br>
                <span>Nationality :
                    {{ $agreementUnit->agreement->tenant->nationality->nationality_name }}</span></br>
                <span>Mobile :
                    {{ $agreementUnit->agreement->tenant->tenant_mobile }}</span></br>
                <span>Email :
                    {{ $agreementUnit->agreement->tenant->tenant_email }}</span></br>
                <span>Contact Person :
                    {{ $agreementUnit->agreement->tenant->contact_person }}</span></br>
                <span>Contact No :
                    {{ $agreementUnit->agreement->tenant->contact_number }}</span></br>
                <span>Contact Email :
                    {{ $agreementUnit->agreement->tenant->contact_email }}</span></br>
                {{-- <span>{{ strtoupper($contract->company->company_name) }}</span></br> --}}

                </br>
            </address>
            {{-- </div> --}}

            @if ($agreementUnit->agreement->agreement_documents->isNotEmpty())
                <table class="table">
                    <tr>
                        <th>#</th>
                        <th>Document name</th>
                        <th>view</th>
                    </tr>

                    @foreach ($agreementUnit->agreement->agreement_documents as $agreement_document)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $agreement_document->TenantIdentity->identity_type }}
                            </td>
                            <td><a href="{{ asset('storage/' . $agreement_document->original_document_path) }}"
                                    target="_blank" class="btn btn-sm btn-outline-info" title="Click to View">
                                    <i class="fas fa-eye"></i>
                                </a></td>
                        </tr>
                    @endforeach

                </table>
            @else
                <span class="text-red">No documents uploaded...</span>
            @endif
        </div>
    </div>
</div>
