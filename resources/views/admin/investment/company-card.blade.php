{{-- <div class="row mt-3" id="companyDetailsSection" style="display: none;">
    <div class="col-sm-12"> --}}
<div class="card card-outline card-info p-4" id="companyDetailsSection" style="display: none;">
    <h4 class="mb-3">Company Details</h4>
    <hr>

    <div class="form-group row">
        <div class="col-sm-3">
            <label class="asterisk">Trade License Number</label>
            <input type="text" name="investor[trade_license_number]" placeholder="Enter Trade License Number"
                id="trade_license_number" class="form-control" value="{{ $investor->trade_license_number ?? '' }}">
        </div>
        <div class="col-sm-3">
            <label class="asterisk">Registration Number</label>
            <input type="text" name="investor[registration_number]" placeholder="Enter registration Number"
                id="registration_number" class="form-control" value="{{ $investor->registration_number ?? '' }}">
        </div>
        <div class="col-sm-3">
            <label class="asterisk">Place of Incorporation (English)</label>
            <select name="investor[place_of_incorporation_id]" id="place_of_incorporation_en"
                class="form-control select2">
                <option value="">Select Place</option>
                @foreach ($places as $id => $place)
                    <option value="{{ $id }}"
                        {{ ($investor->place_of_incorporation_id ?? '') == $id ? 'selected' : '' }}>
                        {{ $place['name'] }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-sm-3">
            <label class="asterisk">Legal Type (English)</label>
            <select name="investor[legal_type_id]" id="legal_type_en" class="form-control select2">
                <option value="">Select Legal Type</option>
                @foreach ($legal_types as $id => $type)
                    <option value="{{ $id }}" {{ ($investor->legal_type_id ?? '') == $id ? 'selected' : '' }}>
                        {{ $type['name'] }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- <div class="col-sm-4">
            <label class="asterisk">Trade License Expiry Date</label>
            <div class="input-group date" id="trade_license_expiry_date_picker" data-target-input="nearest">
                <input type="text" name="investor[trade_license_expiry_date]" id="trade_license_expiry_date"
                    class="form-control datetimepicker-input" data-target="#trade_license_expiry_date_picker"
                    value="{{ $investor->trade_license_expiry_date ?? '' }}">
                <div class="input-group-append" data-target="#trade_license_expiry_date_picker"
                    data-toggle="datetimepicker">
                    <div class="input-group-text">
                        <i class="fa fa-calendar"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <label class="asterisk">Trade License Copy</label>
            <input type="file" name="investor[trade_license_copy]" id="trade_license_copy" class="form-control"
                accept=".pdf,.jpg,.jpeg,.png">

            @if (!empty($investor->trade_license_copy))
                <div class="mt-2">
                    <a href="{{ asset($investor->trade_license_copy) }}" target="_blank" class="btn btn-sm btn-info">
                        <i class="fas fa-file"></i> View Copy
                    </a>
                </div>
            @endif
        </div> --}}
    </div>

    <div class="form-group row">


        {{-- <div class="col-sm-6">
            <label class="asterisk">Place of Incorporation (Arabic)</label>
            <select name="investor[place_of_incorporation_ar]" id="place_of_incorporation_ar"
                class="form-control select2">
                <option value="">Select Place</option>
                @foreach ($places as $id => $place)
                    <option value="{{ $id }}"
                        {{ ($investor->place_of_incorporation ?? '') == $id ? 'selected' : '' }}>
                        {{ $place['arabic'] }}
                    </option>
                @endforeach
            </select>
        </div> --}}
    </div>

    <div class="form-group row">


        {{-- <div class="col-sm-6">
            <label class="asterisk">Legal Type (Arabic)</label>
            <select name="investor[legal_type_ar]" id="legal_type_ar" class="form-control select2">
                <option value="">Select Legal Type</option>
                @foreach ($legal_types as $id => $type)
                    <option value="{{ $id }}" {{ ($investor->legal_type ?? '') == $id ? 'selected' : '' }}>
                        {{ $type['arabic'] }}
                    </option>
                @endforeach
            </select>
        </div> --}}
    </div>
</div>
{{-- </div>
</div> --}}
