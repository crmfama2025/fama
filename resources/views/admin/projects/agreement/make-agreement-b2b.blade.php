<div id="unit_details_container">
    <div id="unit_details_div_df">

        <!-- Add More Button at the Top -->
        {{-- <div class="d-flex mb-3">
            <button type="button" class="btn btn-success" id="add_more_unit">
                + Add More Unit
            </button>
        </div> --}}

        @foreach ($salesAgreement->agreementUnits as $index => $unit)
            <div class="card mb-3 p-3 unit-row">
                <div class="card-body">
                    <div class="row g-3 align-items-end">

                        <!-- Unit Type -->
                        <div class="col-sm-3">
                            <label class="form-label asterisk">Unit Type</label>
                            <input type="hidden" name="unit_detail[{{ $index }}][unit_id]"
                                value="{{ $unit->id }}">
                            <select class="form-control unit_type_id"
                                name="unit_detail[{{ $index }}][unit_type_id]" required>
                                <option value="{{ $unit->unit_type_id }}" selected>
                                    {{ optional($unit->unitType)->unit_type_name }}</option>
                            </select>
                        </div>

                        <!-- Unit Number -->
                        <div class="col-sm-3">
                            <label class="form-label asterisk">Select Unit No</label>
                            <select class="form-control unit_type{{ $index }} unit_type0"
                                name="unit_detail[{{ $index }}][contract_unit_details_id]" required>
                                <option value="{{ $unit->contract_unit_details_id }}" selected>
                                    {{ optional($unit->contractUnitDetail)->unit_number }}</option>
                            </select>
                        </div>

                        <!-- Sub Unit -->
                        <div class="col-sm-3 subunit_number_div">
                            <label class="form-label">Sub Unit</label>
                            <select class="form-control sub_unit_type"
                                name="unit_detail[{{ $index }}][contract_subunit_details_id]">
                                <option value="{{ $unit->contract_subunit_details_id }}" selected>
                                    {{ optional($unit->contractSubunitDetail)->subunit_no }}</option>
                            </select>
                        </div>

                        <!-- Rent per Month -->
                        <div class="col-sm-3">
                            {{-- @dump($unit->monthly_rent) --}}
                            <label class="form-label asterisk">Rent per Month</label>
                            <input type="text" class="form-control rent_per_month"
                                name="unit_detail[{{ $index }}][rent_per_month]"
                                value="{{ $unit->monthly_rent }}" placeholder="Rent per month">
                        </div>

                    </div>
                </div>
            </div>
        @endforeach

        <!-- Container for dynamically added unit rows -->
        {{-- <div id="additional_unit_rows"></div> --}}
    </div>
</div>
