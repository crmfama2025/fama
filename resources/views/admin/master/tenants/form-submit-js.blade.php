 <script>
     $(document).ready(function() {

         $('#tenantForm').on('submit', function(e) {
             e.preventDefault(); // prevent normal form submission

             let form = this;
             let formData = new FormData(form);

             // Clear previous errors
             $(form).find('.is-invalid').removeClass('is-invalid');
             $(form).find('.invalid-feedback').remove();

             // Detect if this is update (tenant ID present)
             let tenantId = $(form).data(
                 'tenant-id');
             // alert(tenantId); // make sure your form has data-tenant-id="{{ $tenant->id ?? '' }}"
             let url = tenantId ?
                 `/tenant/${tenantId}` // RESTful route for update
                 :
                 "{{ route('tenant.store') }}";

             let method = 'POST'; // always POST
             if (tenantId) {
                 formData.append('_method', 'PUT'); // Laravel reads this and treats it as PUT
             }
             showLoader();

             $.ajax({
                 url: url,
                 method: method,
                 data: formData,
                 processData: false, // required for FormData
                 contentType: false, // required for FormData
                 headers: {
                     'X-CSRF-TOKEN': $('input[name="_token"]').val()
                 },
                 beforeSend: function() {
                     $(form).find('button[type="submit"]').prop('disabled', true);
                 },
                 success: function(response) {
                     hideLoader();
                     toastr.success('Tenant saved successfully!', 'Success');
                     // Redirect to tenant list or any other page
                     window.location.href = "{{ route('tenant.index') }}";
                     form.reset();
                 },
                 error: function(xhr) {
                     hideLoader();
                     if (xhr.status === 422) {
                         // Validation errors
                         let errors = xhr.responseJSON.errors;
                         $('.is-invalid').removeClass('is-invalid');
                         $('.invalid-feedback').remove();

                         $.each(errors, function(dotKey, messages) {
                             // Convert "owners.1.1.passport_expiry" → "owners[1][1][passport_expiry]"
                             let bracketKey = dotKey.replace(/\.(\w+)/g, '[$1]');
                             let input = $(form).find(`[name="${bracketKey}"]`);

                             if (input.length) {
                                 input.addClass('is-invalid');
                                 // For inputs inside .input-group (datepickers), append after the group
                                 let target = input.closest('.input-group').length ?
                                     input.closest('.input-group') :
                                     input;
                                 target.after(
                                     '<div class="invalid-feedback d-block">' +
                                     messages[0] + '</div>');
                             }

                             // Show every error message in toastr regardless
                             toastr.error(messages[0], 'Validation Error');
                         });
                     } else {
                         toastr.error('Something went wrong. Please try again.', 'Error');
                     }
                 },
                 complete: function() {
                     hideLoader();
                     $(form).find('button[type="submit"]').prop('disabled', false);
                 }
             });

         });

     });
 </script>
 <script>
     document.addEventListener('click', function(e) {
         if (e.target.classList.contains('remove-owner-btn')) {

             let button = e.target;
             let tenantId = button.dataset.tenantId;
             let ownerIndex = button.dataset.owner;
             let ownerBlock = document.getElementById('owner_' + ownerIndex);

             if (!ownerBlock) return;

             let hiddenInputs = ownerBlock.querySelectorAll('input[type="hidden"][name*="[id]"]');
             let documentIds = [];

             hiddenInputs.forEach(function(input) {
                 if (input.value) {
                     documentIds.push(input.value);
                 }
             });

             if (documentIds.length === 0) {
                 ownerBlock.remove();
                 // Update existingOwnerKeys to reflect removal
                 existingOwnerKeys = existingOwnerKeys.filter(k => k !== parseInt(ownerIndex));
                 return;
             }

             Swal.fire({
                 title: 'Are you sure?',
                 text: "This will permanently remove this owner and related documents.",
                 icon: 'warning',
                 showCancelButton: true,
                 confirmButtonColor: '#d33',
                 cancelButtonColor: '#6c757d',
                 confirmButtonText: 'Yes, remove it!',
                 cancelButtonText: 'Cancel'
             }).then((result) => {
                 if (result.isConfirmed) {
                     fetch('/tenant/remove-owner-documents', {
                             method: 'POST',
                             headers: {
                                 'Content-Type': 'application/json',
                                 'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                             },
                             body: JSON.stringify({
                                 tenant_id: tenantId,
                                 document_ids: documentIds
                             })
                         })
                         .then(response => {
                             if (!response.ok) {
                                 return response.json().then(err => {
                                     throw new Error(err.message);
                                 });
                             }
                             return response.json();
                         })
                         .then(data => {
                             ownerBlock.remove();

                             // ✅ Keep existingOwnerKeys in sync after removal
                             existingOwnerKeys = existingOwnerKeys.filter(k => k !== parseInt(
                                 ownerIndex));

                             Swal.fire('Removed!', data.message, 'success');
                         })
                         .catch(error => {
                             Swal.fire('Error!', error.message, 'error');
                         });
                 }
             });
         }
     });
 </script>
