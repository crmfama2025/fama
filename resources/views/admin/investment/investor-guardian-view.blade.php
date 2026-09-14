@if ($investor->investor_type == 1)
    <div class="tab-pane" id="guardian">

        <div class="card card-outline card-info">

            <div class="card-header">
                <h3 class="card-title font-weight-bold text-maroon">
                    <i class="fas fa-user-shield mr-1"></i>
                    Guardian Details
                </h3>
            </div>

            <div class="card-body">

                <div class="row">

                    <!-- Guardian Name -->
                    <div class="col-md-6 mb-4">
                        <div class=" small">
                            Guardian Name
                        </div>

                        <div class="font-weight-bold">
                            {{ ucfirst($investor->investorGuardian?->guardian_name) ?? '-' }}
                        </div>
                    </div>

                    <!-- Guardian Name Arabic -->
                    <div class="col-md-6 mb-4">
                        <div class=" small">
                            Guardian Name Arabic
                        </div>

                        <div class="font-weight-bold">
                            {{ $investor->investorGuardian?->guardian_name_arabic ?? '-' }}
                        </div>
                    </div>

                    <!-- Code -->
                    <div class="col-md-6 mb-4">
                        <div class=" small">
                            Guardian Code
                        </div>

                        <div class="font-weight-bold">
                            {{ $investor->investorGuardian?->investor_guardian_code ?? '-' }}
                        </div>
                    </div>

                    <!-- Mobile -->
                    <div class="col-md-6 mb-4">
                        <div class=" small">
                            Mobile Number
                        </div>

                        <div class="font-weight-bold">
                            {{ $investor->investorGuardian?->guardian_mobile ?? '-' }}
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="col-md-6 mb-4">
                        <div class=" small">
                            Email Address
                        </div>

                        <div class="font-weight-bold">
                            {{ $investor->investorGuardian?->guardian_email ?? '-' }}
                        </div>
                    </div>

                    <!-- Emirates ID -->
                    <div class="col-md-6 mb-4">
                        <div class=" small">
                            Emirates ID / Other ID
                        </div>

                        <div class="font-weight-bold">
                            {{ $investor->investorGuardian?->emirates_id_number ?? '-' }}
                        </div>
                    </div>

                    <!-- Passport -->
                    <div class="col-md-6 mb-4">
                        <div class=" small">
                            Passport Number
                        </div>

                        <div class="font-weight-bold">
                            {{ $investor->investorGuardian?->passport_number ?? '-' }}
                        </div>
                    </div>

                </div>

                <hr>


                <hr>

                <!-- Guardian Documents -->
                <h5 class="font-weight-bold text-maroon mb-4">
                    <i class="far fa-file-alt mr-1"></i>
                    Guardian Documents
                </h5>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="border rounded p-3">
                            <div class="small mb-1">
                                Emirates ID Copy
                            </div>

                            @if (!empty($investor->investorGuardian?->emirates_id_copy))
                                <a href="{{ asset('storage/' . $investor->investorGuardian->emirates_id_copy) }}"
                                    target="_blank" class="font-weight-bold text-maroon">
                                    <i class="far fa-file-pdf mr-1"></i>
                                    View Document
                                </a>
                            @else
                                <span>Not Available</span>
                            @endif

                            <div class="mt-2">
                                <small class="text-muted">Expiry Date</small>
                                <div class="font-weight-bold">
                                    {{ $investor->investorGuardian?->eid_expiry_date
                                        ? \Carbon\Carbon::parse($investor->investorGuardian->eid_expiry_date)->format('d M Y')
                                        : 'Not Available' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="border rounded p-3">
                            <div class="small mb-1">
                                Passport Copy
                            </div>

                            @if (!empty($investor->investorGuardian?->passport_copy))
                                <a href="{{ asset('storage/' . $investor->investorGuardian->passport_copy) }}"
                                    target="_blank" class="font-weight-bold text-maroon">
                                    <i class="far fa-file-pdf mr-1"></i>
                                    View Document
                                </a>
                            @else
                                <span>Not Available</span>
                            @endif

                            <div class="mt-2">
                                <small class="text-muted">Expiry Date</small>
                                <div class="font-weight-bold">
                                    {{ $investor->investorGuardian?->passport_expiry_date
                                        ? \Carbon\Carbon::parse($investor->investorGuardian->passport_expiry_date)->format('d M Y')
                                        : 'Not Available' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>
    ```

@endif
