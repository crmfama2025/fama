@php
    $unitNumList = $vacantData['units'];
    $vacantSubunitsByUnit = $vacantData['subunits_by_unit'];
    $mergedUnits = collect($unitNumList);

    // Add missing agreement units
    foreach ($agreement_units as $agreementUnit) {
        if (!$mergedUnits->pluck('id')->contains($agreementUnit->contract_unit_details_id)) {
            $mergedUnits->push($agreementUnit->contractUnitDetail);
        }
    }

    $mergedUnitsByType = $mergedUnits->filter()->groupBy('unit_type_id')->map(
        fn($items) => $items
            ->map(
                fn($i) => [
                    'id' => $i->id,
                    'unit_number' => $i->unit_number,
                ],
            )
            ->values(),
    );
    $mergedSubunitsByUnit = collect($agreement_units)->mapWithKeys(function ($agreementUnit) use (
        $vacantSubunitsByUnit,
    ) {
        $unitId = $agreementUnit->contract_unit_details_id;
        $vacant = $vacantSubunitsByUnit->get($unitId, collect());
        $selected = $agreementUnit->contractSubunitDetail;
        $merged = collect([$selected])
            ->filter()
            ->merge($vacant)
            ->unique('id')
            ->values();
        return [$unitId => $merged];
    });
@endphp

<script>
    var unitNumList = @json($unitNumList);
    var mergedUnitsByType = @json($mergedUnitsByType);
    var mergedSubunitsByUnit = @json($mergedSubunitsByUnit);
</script>


@if ($businessType == 1)
    <div class="row mb-3">
        <div class="col-12 text-end">
            <button type="button" class="btn btn-success add_more_unit_edit" id="add_more_unit_edit">+ Add More
                Unit</button>
        </div>
    </div>
@endif


<div class="unit-container ">
    @foreach ($agreement_units as $unitkey => $unitDetail)
        {{-- @dump($vacantSubunitsByUnit[$unitDetail->contract_unit_details_id]); --}}
        {{-- @dump($unitDetail); --}}
        <div class="card mb-3 unit-row p-3" data-row-index="{{ $unitkey }}">
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <input type="hidden" name="unit_detail[{{ $unitkey }}][agreement_unit_id]"
                        value="{{ $unitDetail->id }}" class="agreement_unit_id">
                    <!-- Unit Type -->
                    <div class="col-md-3">
                        <label class="form-label asterisk">Unit Type</label>
                        <select class="form-control unit_type_select"
                            name="unit_detail[{{ $unitkey }}][unit_type_id]" required>
                            <option value="">Select Unit Type</option>
                            @foreach ($unitTypeList as $ut)
                                <option value="{{ $ut->id }}"
                                    {{ $ut->id == $unitDetail->unit_type_id ? 'selected' : '' }}>
                                    {{ $ut->unit_type }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Unit Number -->
                    <div class="col-md-3">
                        <label class="form-label asterisk">Select Unit No</label>
                        <select class="form-control unit_no_select"
                            name="unit_detail[{{ $unitkey }}][contract_unit_details_id]" required>
                            <option value="">Select Unit Number</option>
                            @php
                                $initialUnits = $mergedUnitsByType[$unitDetail->unit_type_id] ?? [];
                            @endphp
                            @foreach ($initialUnits as $mu)
                                <option value="{{ $mu['id'] }}"
                                    {{ $mu['id'] == $unitDetail->contract_unit_details_id ? 'selected' : '' }}>
                                    {{ $mu['unit_number'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Sub Unit -->
                    {{-- <div class="col-md-3 subunit_number_div">
                        <label class="form-label">Sub Unit</label>
                        <select class="form-control  sub_unit_type"
                            name="unit_detail[{{ $unitkey }}][contract_subunit_details_id]" disabled></select>
                    </div> --}}
                    <div class="col-md-3 subunit_number_div">
                        <label class="form-label">Sub Unit</label>
                        @php
                            $subunits = $vacantSubunitsByUnit->get($unitDetail->contract_unit_details_id, collect());
                        @endphp
                        {{-- @dump($subunits); --}}
                        <select class="form-control sub_unit_type"
                            name="unit_detail[{{ $unitkey }}][contract_subunit_details_id]"
                            @if ($businessType == 2) data-business-type="2" @else disabled @endif>
                            @if ($businessType == 2)
                                <option value="">Select Sub Unit</option>

                                @foreach ($mergedSubunitsByUnit[$unitDetail->contract_unit_details_id] ?? collect() as $subunit)
                                    <option value="{{ $subunit->id }}"
                                        {{ $subunit->id == $unitDetail->contract_subunit_details_id ? 'selected' : '' }}>
                                        {{ $subunit->subunit_no }}
                                    </option>
                                @endforeach
                            @endif

                        </select>
                    </div>

                    <!-- Rent per Month -->
                    <div class="@if ($businessType == 1) col-md-2 @else col-md-3 @endif">
                        <label class="form-label asterisk">Rent per Month</label>
                        <input type="text" class="form-control rent_per_month"
                            name="unit_detail[{{ $unitkey }}][rent_per_month]" data-count={{ $count }}
                            value="{{ $unitDetail->rent_per_month }}"
                            @if ($businessType == 1) readonly @endif>
                    </div>

                    @if ($businessType == 1)
                        <!-- Delete Button -->
                        <div class="col-md-1 text-end">
                            <button type="button" class="btn btn-danger delete-row-edit"
                                data-unit-id="{{ $unitDetail->id }}" data-contract-id="{{ $contract_id }}">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
</div>

<!-- Container for dynamically added units -->
<div id="additional_unit_rows"></div>
