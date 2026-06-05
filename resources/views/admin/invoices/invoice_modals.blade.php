 {{-- Generate Invoice Modal --}}
 <div class="modal fade" id="generateModal" tabindex="-1" role="dialog">
     <div class="modal-dialog modal-lg" role="document">

         <div class="modal-content">

             <form id="generateInvoiceForm">

                 <!-- Header -->
                 <div class="modal-header bg-primary">
                     <h5 class="modal-title text-white">
                         <i class="fas fa-file-invoice mr-2"></i> Generate Invoice
                     </h5>
                     <button type="button" class="close text-white" data-dismiss="modal">
                         <span>&times;</span>
                     </button>
                 </div>

                 <!-- Body -->
                 <div class="modal-body">

                     @csrf

                     <input type="hidden" id="gInvoiceId">

                     <input type="hidden" id="gPaymentDetailId" name="agreement_payment_detail_id">
                     <input type="hidden" id="gTenantId" name="tenant_id">
                     <input type="hidden" id="gAgreementId" name="agreement_id">
                     <input type="hidden" id="gContractId" name="contract_id">
                     <input type="hidden" id="gContractUnitId" name="contract_unit_details_id">
                     <input type="hidden" id="gAgreementUnitId" name="agreement_unit_id">

                     <!-- Tenant & Unit -->
                     <div class="row">

                         <div class="col-md-6">
                             <div class="info-box shadow-sm">
                                 <div class="info-box-content">
                                     <span class="info-box-text">Tenant Name</span>
                                     <span class="info-box-number text-danger" id="gTenantName">-</span>
                                 </div>
                             </div>
                         </div>

                         <div class="col-md-6">
                             <div class="info-box shadow-sm">
                                 <div class="info-box-content">
                                     <span class="info-box-text">Unit</span>
                                     <span class="info-box-number" id="gUnitNo">-</span>
                                 </div>
                             </div>
                         </div>

                     </div>

                     <hr>

                     <!-- Invoice Fields -->
                     <div class="row">

                         {{-- <div class="col-md-4"> --}}
                         {{-- <div class="form-group">
                                    <label class="asterisk">Invoice Number</label>
                                    <input type="text" id="gInvoiceNo" name="invoice_no" class="form-control"
                                        placeholder="e.g. INV-1001">
                                </div> --}}
                         {{-- </div> --}}

                         <div class="col-md-4">
                             <div class="form-group">
                                 <label class="asterisk">Invoice Date</label>

                                 <div class="input-group" id="invoiceDatePicker" data-target-input="nearest">
                                     <input type="text" id="gInvoiceDate" name="invoice_date"
                                         class="form-control datetimepicker-input" data-target="#invoiceDatePicker"
                                         placeholder="dd-mm-YYYY" required />

                                     <div class="input-group-append" data-target="#invoiceDatePicker"
                                         data-toggle="datetimepicker">
                                         <span class="input-group-text">
                                             <i class="fa fa-calendar"></i>
                                         </span>
                                     </div>
                                 </div>

                             </div>
                         </div>

                         <div class="col-md-4">
                             <div class="form-group">
                                 <label class="asterisk">Trade License Number</label>
                                 <input type="text" id="gTrnNumber" name="trn_number" class="form-control" required>
                             </div>
                         </div>

                         <div class="col-md-4">
                             <div class="form-group">
                                 <label class="asterisk">Month Start</label>

                                 <div class="input-group" id="monthStartPicker" data-target-input="nearest">
                                     <input type="text" id="gMonthStart" name="month_start"
                                         class="form-control datetimepicker-input" data-target="#monthStartPicker"
                                         placeholder="dd-mm-YYYY" readonly required />

                                     <div class="input-group-append" data-target="#monthStartPicker"
                                         data-toggle="datetimepicker">
                                         <span class="input-group-text">
                                             <i class="fa fa-calendar"></i>
                                         </span>
                                     </div>
                                 </div>

                             </div>
                         </div>

                         <div class="col-md-4">
                             <div class="form-group">
                                 <label class="asterisk">Month End</label>

                                 <div class="input-group" id="monthEndPicker" data-target-input="nearest">
                                     <input type="text" id="gMonthEnd" name="month_end"
                                         class="form-control datetimepicker-input" data-target="#monthEndPicker"
                                         placeholder="dd-mm-YYYY" readonly required />

                                     <div class="input-group-append" data-target="#monthEndPicker"
                                         data-toggle="datetimepicker">
                                         <span class="input-group-text">
                                             <i class="fa fa-calendar"></i>
                                         </span>
                                     </div>
                                 </div>

                             </div>
                         </div>
                         <div class="col-md-4">
                             <div class="form-group">
                                 <label class="asterisk">Total Amount</label>
                                 <input type="number" id="gTotalAmount" name="total_amount" class="form-control"
                                     placeholder="Enter Total Amount" readonly required>
                             </div>
                         </div>

                     </div>

                 </div>

                 <!-- Footer -->
                 <div class="modal-footer justify-content-between">
                     <button type="button" class="btn btn-secondary" data-dismiss="modal">
                         <i class="fas fa-times"></i> Cancel
                     </button>

                     <button type="submit" class="btn btn-danger">
                         <i class="fas fa-file-pdf mr-1"></i> Generate Invoice
                     </button>
                 </div>

             </form>

         </div>

     </div>
 </div>


 <!-- Approve Invoice Modal -->
 <div class="modal fade" id="approveInvoiceModal" tabindex="-1" role="dialog"
     aria-labelledby="approveInvoiceModalLabel" aria-hidden="true">
     <div class="modal-dialog modal-dialog-centered" role="document">
         <div class="modal-content"
             style="border-radius: 10px; overflow: hidden; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.15);">

             <!-- Header -->
             <div class="modal-header" style="background-color: #3a9e57; border-bottom: none; padding: 16px 20px;">
                 <h5 class="modal-title text-white d-flex align-items-center gap-2" id="approveInvoiceModalLabel">
                     <span
                         style="background: rgba(255,255,255,0.25); border-radius: 50%; width: 28px; height: 28px; display:inline-flex; align-items:center; justify-content:center;">
                         <i class="fas fa-check" style="font-size:13px;"></i>
                     </span>
                     &nbsp; Approve Invoice
                 </h5>
                 <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"
                     style="opacity:1;">
                     <span aria-hidden="true">&times;</span>
                 </button>
             </div>

             <!-- Body -->
             <!-- Body -->
             <form id="approve-invoice-form" method="POST" action="" style="display:inline;">
                 @csrf
                 <div class="modal-body" style="padding: 24px 24px 8px;">
                     <p style="font-size: 15px; color: #333; margin-bottom: 8px;">
                         Are you sure you want to approve invoice
                         <strong id="approve-invoice-number"></strong>
                         for tenant <strong id="approve-tenant-name"></strong>?
                     </p>
                     <p style="font-size: 13px; color: #888; margin-bottom: 16px;">
                         Once approved, the invoice cannot be edited.
                     </p>

                     <!-- ✅ Approval Comments -->
                     <div class="form-group">
                         <label for="approval_comment" style="font-size:14px; font-weight:500;" class="asterisk">
                             Approval Comments
                         </label>
                         <textarea name="comment" id="approval_comment" class="form-control" rows="3"
                             placeholder="Enter your approval comments..." required style="border-radius:6px; font-size:14px;"></textarea>
                     </div>
                 </div>

                 <!-- Footer -->
                 <div class="modal-footer" style="border-top: none; padding: 16px 24px 24px;">
                     <button type="button" class="btn btn-secondary" data-dismiss="modal"
                         style="background:#6c757d; border:none; border-radius:6px; padding: 8px 20px; font-size:14px;">
                         Cancel
                     </button>


                     <button type="submit" class="btn btn-success" data-status="2"
                         data-status=style="background:#3a9e57; border:none; border-radius:6px; padding: 8px 20px; font-size:14px;">
                         <i class="fas fa-check mr-1"></i> Approve
                     </button>
                     <button type="submit" name="status" value="on_hold" class="btn btn-warning ml-2"
                         data-status="3" style="border:none; border-radius:6px; padding: 8px 20px; font-size:14px;">
                         <i class="fas fa-pause mr-1"></i> On Hold
                     </button>
                 </div>
             </form>


         </div>
     </div>
 </div>


 {{-- Edit Invoice Modal --}}
 <div class="modal fade" id="editModal" tabindex="-1" role="dialog">
     <div class="modal-dialog modal-lg" role="document">

         <div class="modal-content">

             <form id="editInvoiceForm">

                 <!-- Header -->
                 <div class="modal-header bg-info">
                     <h5 class="modal-title text-white">
                         <i class="fas fa-edit mr-2"></i> Edit Invoice
                     </h5>
                     <button type="button" class="close text-white" data-dismiss="modal">
                         <span>&times;</span>
                     </button>
                 </div>

                 <!-- Body -->
                 <div class="modal-body">

                     @csrf

                     <input type="hidden" id="eInvoiceId" name="invoice_id">

                     {{-- <input type="hidden" id="ePaymentDetailId" name="agreement_payment_detail_id"> --}}
                     <input type="hidden" id="eTenantId" name="tenant_id">
                     {{-- <input type="hidden" id="eAgreementId" name="agreement_id">
                        <input type="hidden" id="eContractId" name="contract_id">
                        <input type="hidden" id="eContractUnitId" name="contract_unit_details_id">
                        <input type="hidden" id="eAgreementUnitId" name="agreement_unit_id"> --}}

                     <!-- Tenant & Unit -->
                     <div class="row">

                         <div class="col-md-6">
                             <div class="info-box shadow-sm">
                                 <div class="info-box-content">
                                     <span class="info-box-text">Tenant Name</span>
                                     <span class="info-box-number text-danger" id="eTenantName">-</span>
                                 </div>
                             </div>
                         </div>

                         <div class="col-md-6">
                             <div class="info-box shadow-sm">
                                 <div class="info-box-content">
                                     <span class="info-box-text">Unit</span>
                                     <span class="info-box-number" id="eUnitNo">-</span>
                                 </div>
                             </div>
                         </div>

                     </div>

                     <hr>

                     <!-- Invoice Fields -->
                     <div class="row">

                         <div class="col-md-4">
                             <div class="form-group">
                                 <label class="asterisk">Invoice Date</label>

                                 <div class="input-group" id="editInvoiceDatePicker" data-target-input="nearest">
                                     <input type="text" id="eInvoiceDate" name="invoice_date"
                                         class="form-control datetimepicker-input"
                                         data-target="#editInvoiceDatePicker" placeholder="dd-mm-YYYY" />

                                     <div class="input-group-append" data-target="#editInvoiceDatePicker"
                                         data-toggle="datetimepicker">
                                         <span class="input-group-text">
                                             <i class="fa fa-calendar"></i>
                                         </span>
                                     </div>
                                 </div>

                             </div>
                         </div>

                         <div class="col-md-4">
                             <div class="form-group">
                                 <label class="asterisk">Trade License Number</label>
                                 <input type="text" id="eTrnNumber" name="trn_number" class="form-control">
                             </div>
                         </div>

                         <div class="col-md-4">
                             <div class="form-group">
                                 <label class="asterisk">Month Start</label>
                                 <input type="text" id="eMonthStart" name="month_start" class="form-control"
                                     readonly />
                             </div>
                         </div>

                         <div class="col-md-4">
                             <div class="form-group">
                                 <label class="asterisk">Month End</label>
                                 <input type="text" id="eMonthEnd" name="month_end" class="form-control"
                                     readonly />
                             </div>
                         </div>

                         <div class="col-md-4">
                             <div class="form-group">
                                 <label class="asterisk">Total Amount</label>
                                 <input type="number" id="eTotalAmount" name="total_amount" class="form-control"
                                     readonly>
                             </div>
                         </div>

                     </div>

                 </div>

                 <!-- Footer -->
                 <div class="modal-footer justify-content-between">
                     <button type="button" class="btn btn-secondary" data-dismiss="modal">
                         <i class="fas fa-times"></i> Cancel
                     </button>

                     <button type="submit" class="btn btn-success">
                         <i class="fas fa-save mr-1"></i> Update Invoice
                     </button>
                 </div>

             </form>

         </div>

     </div>
 </div>


 <div class="modal fade" id="commentModal" tabindex="-1" role="dialog">
     <div class="modal-dialog" role="document">
         <div class="modal-content">

             <div class="modal-header">
                 <h5 class="modal-title">Invoice Comments</h5>
                 <button type="button" class="close" data-dismiss="modal">&times;</button>
             </div>

             <div class="modal-body">
                 <div id="commentList">
                     Loading...
                 </div>
             </div>

         </div>
     </div>
 </div>
