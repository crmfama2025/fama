@foreach ($contract_payment_details as $paymentkey => $paymentDetail)
    <div class="payment_mode_div" data-index="{{ $paymentkey }}">
        <div class="form-group row">
            <input type="hidden" name="payment_detail[id][]" value="{{ $paymentDetail->id ?? '' }}">
            <div class="col-md-4">
                <label class="asterisk">Payment Mode</label>
                <select class="form-control select2 payment_mode" data-id="{{ $paymentkey }}"
                    name="payment_detail[payment_mode_id][]" id="payment_mode{{ $paymentkey }}" required>
                    <option value="">Select</option>
                    @foreach ($paymentmodes as $paymentmode)
                        <option value="{{ $paymentmode->id }}"
                            {{ ($paymentDetail->payment_mode_id ?? '') == $paymentmode->id ? 'selected' : '' }}>
                            {{ $paymentmode->payment_mode_name }} </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <label class="asterisk">Payment Date</label>
                <div class="input-group date" id="otherPaymentDate{{ $paymentkey }}" data-target-input="nearest">
                    <input type="text" class="form-control datetimepicker-input otherPaymentDate"
                        name="payment_detail[payment_date][]" value="{{ $paymentDetail->payment_date ?? '' }}"
                        id="payment_date{{ $paymentkey }}" data-target="#otherPaymentDate{{ $paymentkey }}"
                        placeholder="dd-mm-YYYY" required />
                    <div class="input-group-append" data-target="#otherPaymentDate{{ $paymentkey }}"
                        data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <label class="asterisk">Payment Amount</label>
                <input type="text" class="form-control" id="payment_amount{{ $paymentkey }}"
                    name="payment_detail[payment_amount][]" placeholder="Payment Amount"
                    value="{{ $paymentDetail->payment_amount ?? '' }}" required>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-md-4 bank" id="bank{{ $paymentkey }}">
                <label for="exampleInputEmail1" class="asterisk">Bank Name</label>

                <select class="form-control select2 bank_name" name="payment_detail[bank_id][]"
                    id="bank_name{{ $paymentkey }}" required>
                    <option value="">Select Bank</option>
                    @foreach ($banks as $bank)
                        <option value="{{ $bank->id }}"
                            {{ ($paymentDetail->bank_id ?? '') == $bank->id ? 'selected' : '' }}>
                            {{ $bank->bank_name }} </option>
                    @endforeach


                </select>

            </div>

            <div class="col-md-3 chq" id="chq{{ $paymentkey }}">
                <label for="exampleInputEmail1" class="asterisk">Cheque No</label>
                <input type="text" class="form-control cheque_no" id="cheque_no{{ $paymentkey }}"
                    name="payment_detail[cheque_no][]" placeholder="Cheque No"
                    value="{{ $paymentDetail->cheque_no ?? '' }}" required>
            </div>
        </div>
        <hr>
    </div>
@endforeach
