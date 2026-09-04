 <div class="modal fade" id="modal-apply-novation" tabindex="-1">
     <div class="modal-dialog modal-lg">
         <div class="modal-content">

             <div class="modal-header">
                 <div>
                     <h4 class="modal-title">Apply Investment Novation</h4>
                     <small id="novation-investor-name" class="text-muted"></small>
                 </div>

                 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                     <span aria-hidden="true">&times;</span>
                 </button>
             </div>

             <form id="applyNovationForm" method="POST">
                 @csrf

                 <input type="hidden" name="investor_id" id="novation_investor_id">

                 <div class="modal-body">

                     <div id="novation-investments-loader" class="text-center py-4" style="display: none;">
                         <i class="fas fa-spinner fa-spin"></i>
                         Loading investments...
                     </div>

                     <div id="novation-investments-empty" class="alert alert-info" style="display: none;">
                         This investor does not have any active investments.
                     </div>

                     <div id="novation-investments-container" style="display: none;">

                         <div class="d-flex justify-content-between align-items-center mb-3">
                             <span>
                                 Select the investments that require novation.
                             </span>

                             <div>
                                 <button type="button" class="btn btn-sm btn-outline-primary"
                                     id="select-all-investments">
                                     Select All
                                 </button>

                                 <button type="button" class="btn btn-sm btn-outline-secondary"
                                     id="clear-all-investments">
                                     Clear
                                 </button>
                             </div>
                         </div>

                         <div class="table-responsive">
                             <table class="table table-bordered table-hover">
                                 <thead>
                                     <tr>
                                         <th style="width: 50px;"></th>
                                         <th>Investment ID</th>
                                         <th>Investment Date</th>
                                         <th>Investment Amount</th>
                                         <th>Company</th>
                                     </tr>
                                 </thead>

                                 <tbody id="novation-investments-list"></tbody>
                             </table>
                         </div>

                         <small id="selected-investments-count" class="text-muted">
                             0 investments selected
                         </small>

                         <div id="novation-investment-error" class="text-danger mt-2" style="display: none;">
                             Please select at least one investment.
                         </div>
                     </div>
                 </div>

                 <div class="modal-footer justify-content-between">
                     <button type="button" class="btn btn-default" data-dismiss="modal">
                         Close
                     </button>

                     <button type="submit" id="submitNovation" class="btn btn-info" disabled>
                         Apply Novation
                     </button>
                 </div>
             </form>
         </div>
     </div>
 </div>

 <script>
     $(function() {
         const investmentsUrlTemplate =
             @json(route('investor.novation.investments', ['investor' => '__INVESTOR__']));

         const applyNovationUrl =
             @json(route('investor.novation.apply'));

         $('#modal-apply-novation').on('show.bs.modal', function(event) {
             const button = $(event.relatedTarget);
             const investorId = button.data('investor-id');
             const investorName = button.data('investor-name') || '';

             resetNovationModal();

             $('#novation_investor_id').val(investorId);
             $('#novation-investor-name').text(investorName);

             loadInvestorInvestments(investorId);
         });

         function resetNovationModal() {
             $('#applyNovationForm')[0].reset();
             $('#novation-investments-list').empty();
             $('#novation-investments-loader').hide();
             $('#novation-investments-empty').hide();
             $('#novation-investments-container').hide();
             $('#novation-investment-error').hide();
             $('#submitNovation').prop('disabled', true);
             updateSelectedCount();
         }

         function loadInvestorInvestments(investorId) {
             $('#novation-investments-loader').show();

             const url = investmentsUrlTemplate.replace(
                 '__INVESTOR__',
                 investorId
             );

             $.ajax({
                 url: url,
                 type: 'GET',
                 dataType: 'json',

                 success: function(response) {
                     $('#novation-investments-loader').hide();

                     const investments = response.data || [];

                     if (investments.length === 0) {
                         $('#novation-investments-empty').show();
                         return;
                     }

                     investments.forEach(function(investment) {
                         appendInvestmentRow(investment);
                     });

                     $('#novation-investments-container').show();
                     updateSelectedCount();
                 },

                 error: function(xhr) {
                     $('#novation-investments-loader').hide();

                     toastr.error(
                         xhr.responseJSON?.message ||
                         'Failed to load investor investments.'
                     );
                 }
             });
         }

         function appendInvestmentRow(investment) {
             const checkbox = $('<input>', {
                 type: 'checkbox',
                 name: 'investment_ids[]',
                 value: investment.id,
                 class: 'novation-investment-checkbox'
             });

             const row = $('<tr>');

             $('<td>').append(checkbox).appendTo(row);
             $('<td>').text(investment.investment_code).appendTo(row);
             $('<td>').text(investment.investment_date).appendTo(row);
             $('<td>').text(investment.investment_amount).appendTo(row);
             $('<td>').text(investment.company_name || '-').appendTo(row);

             $('#novation-investments-list').append(row);
         }

         $(document).on('change', '.novation-investment-checkbox', function() {
             $('#novation-investment-error').hide();
             updateSelectedCount();
         });

         $('#select-all-investments').on('click', function() {
             $('.novation-investment-checkbox')
                 .prop('checked', true)
                 .trigger('change');
         });

         $('#clear-all-investments').on('click', function() {
             $('.novation-investment-checkbox')
                 .prop('checked', false)
                 .trigger('change');
         });

         function updateSelectedCount() {
             const count =
                 $('.novation-investment-checkbox:checked').length;

             $('#selected-investments-count').text(
                 count + (count === 1 ?
                     ' investment selected' :
                     ' investments selected')
             );

             $('#submitNovation').prop('disabled', count === 0);
         }

         $('#applyNovationForm').on('submit', function(event) {
             event.preventDefault();

             const selectedCount =
                 $('.novation-investment-checkbox:checked').length;

             if (selectedCount === 0) {
                 $('#novation-investment-error').show();
                 return;
             }

             const submitButton = $('#submitNovation');

             submitButton
                 .prop('disabled', true)
                 .text('Applying...');

             showLoader();

             $.ajax({
                 url: applyNovationUrl,
                 type: 'POST',
                 data: $(this).serialize(),
                 dataType: 'json',

                 success: function(response) {
                     hideLoader();

                     toastr.success(
                         response.message ||
                         'Novation applied successfully.'
                     );

                     $('#modal-apply-novation').modal('hide');

                     // Reload if the investor list needs updating.
                     window.location.reload();
                 },

                 error: function(xhr) {
                     hideLoader();

                     submitButton
                         .prop('disabled', false)
                         .text('Apply Novation');

                     const errors = xhr.responseJSON?.errors;

                     if (errors) {
                         const firstError = Object.values(errors)[0];

                         toastr.error(
                             Array.isArray(firstError) ?
                             firstError[0] :
                             firstError
                         );
                     } else {
                         toastr.error(
                             xhr.responseJSON?.message ||
                             'Unable to apply novation.'
                         );
                     }
                 }
             });
         });
     });
 </script>
