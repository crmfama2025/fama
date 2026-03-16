@foreach ($contract_units_details as $unitkey => $unitDetail)
    @php
        $unit_rent = toNumeric($unitDetail->unit_rent_per_annum) ?? 0;

        if (!$renew) {
            $unitComm =
                $unitDetail->unit_commission ??
                toNumeric($unit_rent * toNumeric($unitDetail->unit_commission ?? 0)) / 100;
            $unitDepo =
                $unitDetail->unit_deposit ?? toNumeric($unit_rent * toNumeric($unitDetail->unit_deposit ?? 0)) / 100;
        } else {
            $unitComm = 0;
            $unitDepo = 0;
        }

        $unit_payable = $unitDetail->unit_amount_payable ?? toNumeric($unit_rent + $unitComm + $unitDepo);
    @endphp
    <div class="rentPerUnitFFaddmore profitDeletecls{{ $unitkey }}" data-index="{{ $unitkey }}">
        <div class="form-group row">
            <div class="col-md-2">
                <label for="exampleInputEmail1">Unit No</label>
                <input type="text" class="form-control unit_noFF" id="unit_noFF{{ $unitkey }}" readonly
                    value="{{ $unitDetail->unit_number ?? '' }}">
                <input type="hidden" id="unit_amount_payable{{ $unitkey }}" value="{{ toNumeric($unit_payable) }}"
                    name="unit_detail[unit_amount_payable][]">
                <input type="hidden" value="{{ $unitComm }}" id="unit_commission{{ $unitkey }}"
                    name="unit_detail[unit_commission][]">
                <input type="hidden" value="{{ $unitDepo }}" id="unit_deposit{{ $unitkey }}"
                    name="unit_detail[unit_deposit][]">
            </div>
            <div class="col-md-2">
                <label for="exampleInputEmail1">Unit Type</label>
                <input type="text" class="form-control" id="unit_typeFF{{ $unitkey }}" readonly
                    value="{{ $unitDetail->unit_type->unit_type ?? '' }}">
            </div>
            <div class="col-md-2">
                <label for="exampleInputEmail1" class="asterisk">Profit %</label>
                <input type="number" class="form-control unit_profit_perc" name="unit_detail[unit_profit_perc][]"
                    id="unit_profit_perc{{ $unitkey }}" value="{{ $unitDetail->unit_profit_perc ?? '' }}"
                    placeholder="Profit %" required>
            </div>
            <div class="col-md-2">
                <label for="exampleInputEmail1">Profit</label>
                <input type="number" class="form-control unit_profit" name="unit_detail[unit_profit][]"
                    id="unit_profit{{ $unitkey }}" value="{{ $unitDetail->unit_profit ?? '' }}"
                    placeholder="Profit" readonly>
            </div>
            <div class="col-md-3">
                <label for="exampleInputEmail1">Revenue</label>
                <input type="number" class="form-control unit_revenue" name="unit_detail[unit_revenue][]"
                    id="unit_revenue{{ $unitkey }}" value="{{ $unitDetail->unit_revenue ?? '' }}"
                    placeholder="Revenue" readonly>
            </div>
        </div>
    </div>
@endforeach
