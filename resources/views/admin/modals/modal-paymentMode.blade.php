 <div class="modal fade" id="modal-payment-mode" tabindex="-1" role="dialog" aria-hidden="true">
     <div class="modal-dialog">
         <div class="modal-content">
             <div class="modal-header">
                 <h4 class="modal-title">Payment mode</h4>
                 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                     <span aria-hidden="true">&times;</span>
                 </button>
             </div>
             <form action="" id="PaymentModeForm">
                 @csrf
                 <input type="hidden" name="id" id="payment_mode_id">
                 <div class="modal-body">
                     <div class="card-body">
                         {{-- <div class="form-group row"> --}}
                         {{-- @if (auth()->user()->company_id)
                                 <input type="hidden" name="company_id" id="company_id"
                                     value="{{ auth()->user()->company_id }}">
                             @else --}}
                         {{-- <label class="col-sm-4 col-form-label">Company</label>
                             <select class="form-control select2 col-sm-8" name="company_id" id="company_id">
                                 <option value="">Select Company</option>
                                 {{ $company_dropdown }}
                             </select> --}}
                         {{-- @endif --}}
                         {{-- </div> --}}
                         <div class="form-group row">
                             <label for="inputEmail3" class="col-sm-4 col-form-label asterisk">Payment mode</label>
                             <input type="text" name="payment_mode_name" id="payment_mode_name"
                                 class="col-sm-8 form-control" id="inputEmail3" placeholder="Payment mode" required>
                         </div>
                         <div class="form-group row">
                             <label for="inputEmail3" class="col-sm-4 col-form-label asterisk">Payment mode in
                                 Arabic</label>
                             <input type="text" name="payment_mode_arabic_name" id="payment_mode_arabic_name"
                                 class="col-sm-8 form-control" id="inputEmail3" placeholder="Payment mode in Arabic"
                                 required>
                         </div>

                         <div class="form-group row">
                             <label for="inputEmail3" class="col-sm-4 col-form-label asterisk">Short code</label>
                             <input type="text" name="payment_mode_short_code" id="payment_mode_short_code"
                                 class="col-sm-8 form-control" id="inputEmail3" placeholder="Short code" required>
                         </div>
                     </div>
                     <!-- /.card-body -->
                 </div>
                 <div class="modal-footer justify-content-between">
                     <button type="button" class="btn btn-default closebtn" data-dismiss="modal">Close</button>
                     <button type="submit" class="btn btn-info savebtninfo">Save changes</button>
                 </div>
             </form>
         </div>
         <!-- /.modal-content -->
     </div>
     <!-- /.modal-dialog -->
 </div>
 <!-- /.modal -->

 <script>
     $('#PaymentModeForm').submit(function(e) {
         e.preventDefault();
         //  $('#company_id').prop('disabled', false);
         const pmform = $(this);
         //  pmform.find('select[name="company_id"]').prop('disabled', false);

         var form = document.getElementById('PaymentModeForm');
         var fdata = new FormData(form);

         $.ajax({
             type: "POST",
             url: "{{ route('payment_mode.store') }}",
             data: fdata,
             dataType: "json",
             processData: false,
             contentType: false,
             success: function(response) {
                 toastr.success(response.message);
                 //  window.location.reload();
                 @if (request()->is('payment_mode'))
                     window.location.reload();
                 @else
                     let newOption = new Option(response.data.payment_mode_name, response.data
                         .id, false,
                         false);
                     $('.payment_mode').each(function() {
                         $(this).append(newOption.cloneNode(true));
                     });

                     // Automatically select it in the select that opened the modal
                     if (currentSelectModeId) {
                         $(`#${currentSelectModeId}`).val(response.data.id).trigger(
                             'change.select2');
                     }
                     if (document.activeElement) {
                         document.activeElement.blur();
                     }
                     pmform[0].reset();
                     //  pmform.find('select[name="company_id"]').prop('disabled', true);

                     $('#modal-payment-mode').modal('hide');
                 @endif
             },
             error: function(errors) {
                 toastr.error(errors.responseJSON.message);
                 //  if ($('#payment_mode_id').val()) {
                 //      $('#company_id').prop('disabled', true);
                 //  }

             }
         });
     });
 </script>
