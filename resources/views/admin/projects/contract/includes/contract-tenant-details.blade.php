{{-- <div class="col-6">
    <div class="card">
        <div class="card-header bg-gradient-olive">
            <h4 class="card-title w-100">
                {{ $agreementUnit->agreement->agreement_code }}
            </h4>
        </div>
        <div class="card-body text-muted">

            <address>
                <span>Tenant Name:
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
                <span class="text-bold">Annual rent :
                    {{ $agreementUnit->unit_revenue }}</span></br>

                </br>
            </address>

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
</div> --}}
{{-- {{ dd($agreementUnits) }} --}}
<div class="col-12">
    <div class="card">
        {{-- <div class="card-header bg-gradient-olive">
            <h4 class="card-title">Agreement Units</h4>
        </div> --}}
        <div class="card-body table-responsive">
            <table id="agreementUnitsTable" class="table table-bordered table-striped table-hover agreementUnitsTable ">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Agreement Code</th>
                        <th>Tenant Name</th>
                        <th>Unit-Subunit</th>
                        <th>Monthly Rent</th>
                        <th>Annual Rent</th>
                        <th>Nationality</th>
                        <th>Mobile</th>
                        <th>Documents</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($agreementUnits as $index => $agreementUnit)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $agreementUnit->agreement->agreement_code }}</td>
                            <td>{{ $agreementUnit->agreement->tenant->tenant_name }}</td>

                            <td class="text-blue">{{ $agreementUnit->contractUnitDetail->unit_number }} -
                                {{ $agreementUnit->contractSubunitDetail->subunit_no }}</td>
                            <td>{{ $agreementUnit->rent_per_month }}</td>
                            <td>{{ $agreementUnit->unit_revenue }}</td>
                            <td>{{ $agreementUnit->agreement->tenant->nationality->nationality_name }}</td>
                            <td>{{ $agreementUnit->agreement->tenant->tenant_mobile }}</td>
                            <td>
                                @if ($agreementUnit->agreement->agreement_documents->isNotEmpty())
                                    <span class="badge badge-success">
                                        {{ $agreementUnit->agreement->agreement_documents->count() }} file(s)
                                    </span>
                                @else
                                    <span class="text-danger">None</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('agreement.show', $agreementUnit->agreement->id) }}"
                                    class="btn btn-primary btn-sm" title="Click to View" target="_blank">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- @p('scripts') --}}

{{-- @endpush --}}
