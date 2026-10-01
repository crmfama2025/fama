@foreach ($contract_units_details as $unitkey => $unitDetail)
    @php
        $hasPartition = (int) $unitDetail->partition === 1;
        $hasBedspace = (int) $unitDetail->bedspace === 2;
        $hasRoom = (int) $unitDetail->room === 3;
        $isFlat = !$hasPartition && !$hasBedspace && !$hasRoom;
    @endphp
    <div class="rentPerUnitDFaddmore profitDeleteclsDF{{ $unitkey }}" data-index="{{ $unitkey }}"
        data-has-part="{{ $hasPartition ? 1 : 0 }}" data-has-bs="{{ $hasBedspace ? 1 : 0 }}"
        data-has-room="{{ $hasRoom ? 1 : 0 }}">
        <div class="form-group row">
            <div class="col-md-2">
                <label>Unit No</label>
                <input type="text" class="form-control unit_noDF" id="unit_noDF{{ $unitkey }}" readonly
                    value="{{ $unitDetail->unit_number ?? '' }}">
            </div>

            <div class="col-md-2 dfPartition" @if (!$hasPartition) style="display:none" @endif>
                <label class="asterisk">Rent per Partition</label>
                <input type="number" class="form-control rent_per_part_unit editafterapprove"
                    name="unit_detail[rent_per_partition][]" id="rent_per_part{{ $unitkey }}"
                    value="{{ toNumeric($unitDetail->rent_per_partition) ?? '' }}" placeholder="Rent per Partition"
                    {{ $hasPartition ? 'required' : '' }}>
                <input type="hidden" class="total_partitions_ref" value="{{ $unitDetail->total_partition ?? 0 }}">
            </div>

            <div class="col-md-2 dfBedspace" @if (!$hasBedspace) style="display:none" @endif>
                <label class="asterisk">Rent per Bedspace</label>
                <input type="number" class="form-control rent_per_bs_unit editafterapprove"
                    name="unit_detail[rent_per_bedspace][]" id="rent_per_bs{{ $unitkey }}"
                    value="{{ toNumeric($unitDetail->rent_per_bedspace) ?? '' }}" placeholder="Rent per Bedspace"
                    {{ $hasBedspace ? 'required' : '' }}>
                <input type="hidden" class="total_bedspaces_ref" value="{{ $unitDetail->total_bedspace ?? 0 }}">
            </div>

            <div class="col-md-2 dfRoom" @if (!$hasRoom) style="display:none" @endif>
                <label class="asterisk">Rent per Room</label>
                <input type="number" class="form-control rent_per_room_unit editafterapprove"
                    name="unit_detail[rent_per_room][]" id="rent_per_room{{ $unitkey }}"
                    value="{{ toNumeric($unitDetail->rent_per_room) ?? '' }}" placeholder="Rent per Room"
                    {{ $hasRoom ? 'required' : '' }}>
                <input type="hidden" class="total_room_ref" value="{{ $unitDetail->total_room ?? 0 }}">
            </div>

            <div class="col-md-2 dfFlat" @if (!$isFlat) style="display:none" @endif>
                <label class="asterisk">Rent per Flat</label>
                <input type="number" class="form-control rent_per_flat_unit editafterapprove"
                    name="unit_detail[rent_per_flat][]" id="rent_per_flat{{ $unitkey }}"
                    value="{{ toNumeric($unitDetail->rent_per_flat) ?? '' }}" placeholder="Rent per Flat"
                    {{ $isFlat ? 'required' : '' }}>
            </div>

            <div class="col-md-2">
                <label>Unit Total / Month</label>
                <input type="number" class="form-control unit_total_rent" id="unit_total_rent{{ $unitkey }}"
                    readonly>
            </div>
        </div>
        <hr>
    </div>
@endforeach
