 <div class="modal fade" id="modal-add-bank">
     <div class="modal-dialog">
         <div class="modal-content">
             <div class="modal-header">
                 <h4 class="modal-title">Investor Bank Details</h4>
                 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                     <span aria-hidden="true">&times;</span>
                 </button>
             </div>
             <form action="" id="addInvestorBank" method="POST" enctype="multipart/form-data">
                 <input type="hidden" name="investor_id" id="investor_id">
                 <input type="hidden" name="investor_bank_id" id="investor_bank_id">
                 <div class="modal-body">
                     <div class="card-body">
                         <div class="form-group row">
                             <label for="inputEmail3" class="asterisk">Benenficiary
                                 Name</label>
                             <input type="text" name="investor_beneficiary" id="investor_beneficiary"
                                 class="form-control" placeholder="Benenficiary Name" required>
                         </div>
                         <div class="form-group row">
                             <label for="inputEmail3" class="asterisk">Bank
                                 Name</label>
                             <input type="text" name="investor_bank_name" id="investor_bank_name"
                                 class="form-control" placeholder="Bank Name" required>
                         </div>
                         <div class="form-group row">
                             <label for="inputEmail3" class="asterisk">IBAN</label>
                             <input type="text" name="investor_iban" id="investor_iban" class="form-control"
                                 id="inputEmail3" placeholder="IBAN" required>
                         </div>
                         <input type="hidden" name="is_primary" id="is_primary" value="0">
                     </div>
                     <!-- /.card-body -->
                 </div>
                 <div class="modal-footer justify-content-between">
                     <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                     <button type="button" id="submitBank" class="btn btn-info">Submit</button>
                 </div>
             </form>
         </div>
         <!-- /.modal-content -->
     </div>
     <!-- /.modal-dialog -->
 </div>

 <script>
     $('#submitBank').click(function(e) {
         e.preventDefault();


         let isValid = true;
         $(".error-text").remove(); // clear old errors

         // validate ALL required fields
         $("#addInvestorBank").find("[required]:visible").each(function() {
             const value = $(this).val()?.trim();

             if (!value) {
                 isValid = false;
                 setInvalid(this, "This field is required");
             } else {
                 setValid(this);
             }

         });

         if (!isValid) return;

         submitBankForm(); // everything passed
     });


     // helper: invalid
     function setInvalid(input, message) {
         $(input).addClass("is-invalid").removeClass("is-valid");
     }

     // helper: valid
     function setValid(input) {
         $(input).addClass("is-valid").removeClass("is-invalid");
     }

     function submitBankForm(e) {
         let id = $('#investor_bank_id').val();

         let url = "{{ route('investor.bank.save') }}";

         let method = 'POST';


         var form = document.getElementById('addInvestorBank');
         var fdata = new FormData(form);

         fdata.append('_token', $('meta[name="csrf-token"]').attr('content'));
         fdata.append('_method', method);

         $.ajax({
             type: "POST",
             url: url,
             data: fdata,
             dataType: "json",
             processData: false,
             contentType: false,
             success: function(response) {
                 // console.log(response);
                 toastr.success(response.message);
                 window.location.href = "/investor/" + $('#investor_id').val();
             },
             error: function(errors) {
                 toastr.error(errors.responseJSON.message);
             }
         });
     }


     $('#modal-add-bank').on('show.bs.modal', function(event) {
         var button = event.relatedTarget;

         // values from button
         var investor_id = button.getAttribute('data-investor-id');
         var id = button.getAttribute('data-id');

         // set to modal fields
         $('#investor_id').val(investor_id);
         $('#investor_bank_id').val(id);
         $('#approval_token').val($('meta[name="csrf-token"]').attr('content'));
         $('#approval_status').val(4);

         if (id) {
             let url = "{{ route('investor.bank', ':id') }}";
             url = url.replace(':id', id);
             $.ajax({
                 //  url: '/investor/get-investor-bank/' + id, // Replace with your Laravel route
                 url: url,
                 type: 'GET', // or 'POST' if needed
                 data: {
                     investor_id: investor_id,
                     id: id,
                     _token: $('meta[name="csrf-token"]').attr('content') // Only for POST
                 },
                 success: function(response) {
                     $('#investor_beneficiary').val(response.investor_beneficiary);
                     $('#investor_bank_name').val(response.investor_bank_name);
                     $('#investor_iban').val(response.investor_iban);
                     $('#is_primary').val(response.is_primary);
                 },
                 error: function(xhr) {
                     console.error('AJAX error:', xhr.responseText);
                     alert('Failed to fetch bank details.');
                 }
             });
         }
     });
 </script>
