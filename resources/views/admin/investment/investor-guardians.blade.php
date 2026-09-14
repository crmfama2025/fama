{{-- <div class="col-sm-12 mt-3" id="guardianDetailsCard"> --}}
{{-- @dump($investorGuardian) --}}
{{-- <div class="card card-outline card-info p-4" id="guardianDetailsCard">
    <h4>
        Guardian Details
    </h4>

    <div class="form-group row">
        <div class="col-sm-4">
            <label class="asterisk">Guardian Name</label>
            <input type="text" name="guardian[guardian_name]" class="form-control" placeholder="Guardian Name"
                value="{{ $investorGuardian->guardian_name ?? '' }}" required>
        </div>

        <div class="col-sm-4">
            <label class="asterisk">Guardian Name In Arabic</label>
            <input type="text" name="guardian[guardian_name_arabic]" class="form-control arabic-input"
                placeholder="Guardian Name in Arabic" value="{{ $investorGuardian->guardian_name_arabic ?? '' }}"
                required>
        </div>

        <div class="col-sm-4">
            <label class="asterisk">Guardian Mobile</label>
            <input type="text" name="guardian[guardian_mobile]" class="form-control" placeholder="Guardian Mobile"
                value="{{ $investorGuardian->guardian_mobile ?? '' }}" required>
        </div>
    </div>

    <div class="form-group row">
        <div class="col-sm-4">
            <label class="asterisk">Guardian Email</label>
            <input type="email" name="guardian[guardian_email]" class="form-control" placeholder="Guardian Email"
                value="{{ $investorGuardian->guardian_email ?? '' }}" required>
        </div>

        <div class="col-sm-4">
            <label class="asterisk">Guardian Emirates ID</label>
            <input type="text" name="guardian[emirates_id_number]" class="form-control"
                placeholder="Guardian Emirates ID" value="{{ $investorGuardian->emirates_id_number ?? '' }}" required>
        </div>

        <div class="col-sm-4">
            <label class="asterisk">Guardian Passport Number</label>
            <input type="text" name="guardian[passport_number]" class="form-control"
                placeholder="Guardian Passport Number" value="{{ $investorGuardian->passport_number ?? '' }}" required>
        </div>
    </div>

    <div class="form-group row">
        <div class="col-sm-6">
            <label class="asterisk">Emirates ID Copy</label>
            <input type="file" name="guardian[emirates_id_copy]" class="form-control" required>
            @if (!empty($investorGuardian->emirates_id_copy))
                <div class="mb-2">
                    <a href="{{ asset('storage/' . $investorGuardian->emirates_id_copy) }}" target="_blank">
                        View Emirates ID Copy
                    </a>
                </div>
            @endif
        </div>
        <div class="col-sm-6">
            <label class="asterisk">Emirates ID Expiry Date</label>
            <input type="date" name="guardian[eid_expiry_date]" class="form-control"
                value="{{ $investorGuardian->eid_expiry_date ?? '' }}" required>
        </div>
    </div>
    <div class="form-group row">

        <div class="col-sm-6">
            <label class="asterisk">Passport Copy</label>
            <input type="file" name="guardian[passport_copy]" class="form-control" required>
            @if (!empty($investorGuardian->passport_copy))
                <div class="mb-2">
                    <a href="{{ asset('storage/' . $investorGuardian->passport_copy) }}" target="_blank">
                        View Passport Copy
                    </a>
                </div>
            @endif
        </div>
        <div class="col-sm-6">
            <label class="asterisk">Passport Expiry Date</label>
            <input type="date" name="guardian[passport_expiry_date]" class="form-control"
                value="{{ $investorGuardian->passport_expiry_date ?? '' }}" required>
        </div>
    </div>
</div> --}}
{{-- </div> --}}
<div class="card card-outline card-info p-4" id="guardianDetailsCard">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Guardian Details</h4>
    </div>

    <div class="form-group row">
        <div class="col-sm-10">
            <label class="asterisk">Select Guardian</label>
            <select name="investor[investor_guardian_id]" id="guardian_id" class="form-control">
                <option value="">Select Guardian</option>
                @foreach ($guardians as $guardian)
                    <option
                        value="{{ $guardian->id }}"{{ old('investor.investor_guardian_id', $investor->investor_guardian_id ?? '') == $guardian->id ? 'selected' : '' }}>
                        {{ $guardian->guardian_name }}
                        @if (!empty($guardian->guardian_mobile))
                            - {{ $guardian->investor_guardian_code }}
                        @endif
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-sm-2 d-flex align-items-end">
            <button type="button" class="btn btn-info btn-block" data-toggle="modal" data-target="#addGuardianModal">
                <i class="fas fa-plus mr-1"></i> Add Guardian
            </button>
        </div>
    </div>

    {{-- <input type="hidden" name="guardian[is_existing]" id="guardian_is_existing" value="0"> --}}

    {{-- <div id="guardianManualDetails">
        <div class="form-group row">
            <div class="col-sm-6">
                <label class="asterisk">Guardian Name</label>
                <input type="text" name="guardian[guardian_name]" class="form-control" placeholder="Guardian Name"
                    value="{{ $investorGuardian->guardian_name ?? '' }}">
            </div>

            <div class="col-sm-6">
                <label class="asterisk">Guardian Name In Arabic</label>
                <input type="text" name="guardian[guardian_name_arabic]" class="form-control arabic-input"
                    placeholder="Guardian Name in Arabic" value="{{ $investorGuardian->guardian_name_arabic ?? '' }}">
            </div>
        </div>

        <div class="form-group row">
            <div class="col-sm-6">
                <label class="asterisk">Guardian Mobile</label>
                <input type="text" name="guardian[guardian_mobile]" class="form-control"
                    placeholder="Guardian Mobile" value="{{ $investorGuardian->guardian_mobile ?? '' }}">
            </div>

            <div class="col-sm-6">
                <label class="asterisk">Guardian Email</label>
                <input type="email" name="guardian[guardian_email]" class="form-control" placeholder="Guardian Email"
                    value="{{ $investorGuardian->guardian_email ?? '' }}">
            </div>
        </div>

        <div class="form-group row">
            <div class="col-sm-6">
                <label class="asterisk">Guardian Emirates ID</label>
                <input type="text" name="guardian[emirates_id_number]" class="form-control"
                    placeholder="Guardian Emirates ID" value="{{ $investorGuardian->emirates_id_number ?? '' }}">
            </div>

            <div class="col-sm-6">
                <label class="asterisk">Guardian Passport Number</label>
                <input type="text" name="guardian[passport_number]" class="form-control"
                    placeholder="Guardian Passport Number" value="{{ $investorGuardian->passport_number ?? '' }}">
            </div>
        </div>

        <div class="form-group row">
            <div class="col-sm-6">
                <label class="asterisk">Emirates ID Copy</label>
                <input type="file" name="guardian[emirates_id_copy]" class="form-control">

                @if (!empty($investorGuardian->emirates_id_copy))
                    <div class="mt-2">
                        <a href="{{ asset('storage/' . $investorGuardian->emirates_id_copy) }}" target="_blank">
                            View Emirates ID Copy
                        </a>
                    </div>
                @endif
            </div>

            <div class="col-sm-6">
                <label class="asterisk">Emirates ID Expiry Date</label>
                <input type="date" name="guardian[eid_expiry_date]" class="form-control"
                    value="{{ $investorGuardian->eid_expiry_date ?? '' }}">
            </div>
        </div>

        <div class="form-group row">
            <div class="col-sm-6">
                <label class="asterisk">Passport Copy</label>
                <input type="file" name="guardian[passport_copy]" class="form-control">

                @if (!empty($investorGuardian->passport_copy))
                    <div class="mt-2">
                        <a href="{{ asset('storage/' . $investorGuardian->passport_copy) }}" target="_blank">
                            View Passport Copy
                        </a>
                    </div>
                @endif
            </div>

            <div class="col-sm-6">
                <label class="asterisk">Passport Expiry Date</label>
                <input type="date" name="guardian[passport_expiry_date]" class="form-control"
                    value="{{ $investorGuardian->passport_expiry_date ?? '' }}">
            </div>
        </div>
    </div> --}}
</div>
