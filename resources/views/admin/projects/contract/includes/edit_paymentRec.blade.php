@foreach ($contract_payment_receivables as $reckey => $paymentReceivable)
    <div class="receivableaddmore" data-index="{{ $reckey }}">
        <div class="form-group row">
            {{-- <span>{{ $reckey }}</span> --}}
            <input type="hidden" name="receivables[id][]" value="{{ $paymentReceivable->id ?? '' }}">
            <div class="col-md-4">
                <div class="input-group date" id="receivable_date{{ $reckey }}" data-target-input="nearest">
                    <input type="text" class="form-control datetimepicker-input receivable_date"
                        name="receivables[receivable_date][]" id="rec_payment_date{{ $reckey }}"
                        data-target="#receivable_date{{ $reckey }}"
                        value="{{ $paymentReceivable->receivable_date ?? '' }}" placeholder="dd-mm-YYYY" required />
                    <div class="input-group-append" data-target="#receivable_date{{ $reckey }}"
                        data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <input type="number" class="form-control rec_payment_amount" id="rec_payment_amount{{ $reckey }}"
                    name="receivables[payment_amount][]" value="{{ $paymentReceivable->receivable_amount ?? '' }}"
                    placeholder="Payment Amount" required>
            </div>
        </div>
    </div>
@endforeach
