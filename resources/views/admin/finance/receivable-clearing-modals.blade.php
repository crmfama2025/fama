<div class="modal fade" id="modal-ap-confirm" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Confirm Payment</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <!-- Summary read-only -->
                <table class="table table-sm table-borderless mb-0">
                    {{-- <tr>
                            <td class="text-muted" style="width:40%">Tenant</td>
                            <td><strong id="conf_tenant">—</strong></td>
                        </tr> --}}
                    <tr>
                        <td class="text-muted" style="width:40%">Company</td>
                        <td><strong id="conf_company">—</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Amount</td>
                        <td><strong id="conf_amount">—</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Receivables covered</td>
                        <td><strong id="conf_count">—</strong></td>
                    </tr>
                </table>

                <hr>

                <!-- Other Adjustments -->
                <div class="form-group">
                    <div class="form-check">
                        <input type="checkbox" name="other_adjustments" id="other_adjustments" class="form-check-input"
                            value="1">
                        <label class="form-check-label" for="other_adjustments">
                            Other Adjustments
                        </label>
                    </div>
                </div>

                <!-- Clearing date -->
                <div class="form-group">
                    <label class="asterisk">Clearing Date</label>
                    <div class="input-group date" id="ap_clearingDate" data-target-input="nearest">
                        <input type="text" name="paid_date" class="form-control datetimepicker-input"
                            id="ap_clearing_date_input" data-target="#ap_clearingDate" placeholder="dd-mm-YYYY"
                            required />
                        <div class="input-group-append" data-target="#ap_clearingDate" data-toggle="datetimepicker">
                            <span class="input-group-text">
                                <i class="fa fa-calendar"></i>
                            </span>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="allocated_amount" id="ap_allocated_amount" value="0">

                <div class="row">
                    <!-- Payment type -->

                    <div class="form-group col-md-6">
                        <label><strong>Payment Type</strong></label>
                        <div class="d-flex gap-3">
                            <div class="form-check mr-4">
                                <input type="checkbox" class="form-check-input ap_payment_mode" id="ap_cheque"
                                    value="3">
                                <label class="form-check-label" for="ap_cheque">Cheque</label>
                            </div>
                            <div class="form-check mr-4">
                                <input type="checkbox" class="form-check-input ap_payment_mode" id="ap_bank"
                                    value="2">
                                <label class="form-check-label" for="ap_bank">Bank Transfer</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input ap_payment_mode" id="ap_cash"
                                    value="1">
                                <label class="form-check-label" for="ap_cash">Cash</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-md-6 receiptDiv">
                        <label class="asterisk">Receipt Allocation</label>
                        <select id="receipt_allocation" name="receipt_allocation_id" class="form-control" required>
                            <option value="">Select</option>
                            @foreach ($receiptAlloctaions as $receipt)
                                <option value="{{ $receipt->id }}">{{ $receipt->receipt_allocation_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>


                <!-- Company + Bank (shown when payment type selected) -->
                <div id="ap_bank_div" class="row" style="display:none;">
                    <div class="form-group col-md-6">
                        <label class="asterisk">Company</label>
                        <select id="ap_company_id" class="form-control">
                            <option value="">Select Company</option>
                            @foreach ($companies as $com)
                                <option value="{{ $com->id }}">{{ $com->company_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Bank</label>
                        <select id="ap_bank_id" class="form-control">
                            <option value="">Select Bank</option>
                        </select>
                    </div>
                    <div class="form-group col-md-12">
                        <label class="asterisk">Bank Reference</label>
                        <input type="text" id="ap_reference" class="form-control" placeholder="Bank Reference">
                    </div>
                </div>

                <!-- Cheque number -->
                <div class="form-group" id="ap_cheque_div" style="display:none;">
                    <label>Cheque Number</label>
                    <input type="text" id="ap_cheque_no" class="form-control" placeholder="Enter cheque number">
                </div>

                <!-- Remarks -->
                <div class="form-group">
                    <label>Remarks</label>
                    <textarea id="ap_remarks" class="form-control" rows="2" placeholder="Optional remarks"></textarea>
                </div>

            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" id="ap_confirm_btn" class="btn btn-success">
                    <i class="fa fa-check mr-1"></i> Confirm & Clear
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-loss-discount">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Loss / Discount</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <form id="lossDiscountForm">
                <div class="modal-body">
                    <input type="hidden" name="id" id="loss_discount_id">

                    <div class="form-group">
                        <label>Adjustment Type</label>

                        <div class="icheck-primary d-inline mr-3">
                            <input type="radio" id="loss_type" name="adjustment_type" value="loss" checked>
                            <label for="loss_type">
                                Loss
                            </label>
                        </div>

                        <div class="icheck-primary d-inline">
                            <input type="radio" id="discount_type" name="adjustment_type" value="discount">
                            <label for="discount_type">
                                Discount
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="asterisk">Date</label>
                        <div class="input-group date" id="lossDiscountDate" data-target-input="nearest">
                            <input type="text" name="date" class="form-control datetimepicker-input"
                                id="loss_discount_date" data-target="#lossDiscountDate" placeholder="dd-mm-YYYY"
                                required>

                            <div class="input-group-append" data-target="#lossDiscountDate"
                                data-toggle="datetimepicker">
                                <span class="input-group-text">
                                    <i class="fa fa-calendar"></i>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Amount</label>
                        <input type="number" name="amount" id="loss_discount_amount" class="form-control"
                            step="0.01" min="0" oninput="this.value = Math.max(0, this.value)">
                    </div>

                    <div class="form-group">
                        <label>Balance Amount</label>
                        <input type="number" name="amount" id="balance_amount" class="form-control" readonly>
                    </div>


                    <div class="form-group">
                        <label>Reason</label>
                        <textarea name="reason" id="loss_discount_reason" class="form-control" rows="3" placeholder="Enter reason"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Cancel
                    </button>

                    <button type="submit" class="btn btn-primary">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
