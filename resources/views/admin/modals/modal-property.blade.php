 <div class="modal fade" id="modal-property" tabindex="-1" role="dialog" aria-hidden="true">
     <div class="modal-dialog modal-lg">
         <div class="modal-content">
             <div class="modal-header">
                 <h4 class="modal-title">Property</h4>
                 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                     <span aria-hidden="true">&times;</span>
                 </button>
             </div>
             <form action="" id="PropertyForm">
                 @csrf
                 <input type="hidden" name="id" id="property_id">

                 <div class="modal-body">
                     <div class="card-body">

                         <!-- Property Basic Info -->
                         <h6 class="mb-4 text-lightblue vendorformh6">Property Basic Info</h6>
                         <div class="form-row">
                             <div class="col-md-6 col-sm-12 mb-3">
                                 <label class="asterisk">Area</label>
                                 <select class="form-control select2" name="area_id" id="area_id" required>
                                     <option value="">Select Area</option>
                                     {{ $area_dropdown }}
                                 </select>
                             </div>
                             <div class="col-md-6 col-sm-12 mb-3">
                                 <label class="asterisk">Locality</label>
                                 <select class="form-control select2" name="locality_id" id="locality_id" required>
                                     <option value="">Select Locality</option>
                                 </select>
                             </div>
                         </div>



                         <div class="form-row">
                             <div class="col-md-6 col-sm-12 mb-3">
                                 <label class="asterisk">Property Name</label>
                                 <input type="text" name="property_name" id="property_name" class="form-control"
                                     placeholder="Property Name" required>
                             </div>
                             <div class="col-md-6 col-sm-12 mb-3">
                                 <label>Property Size</label>
                                 <div class="input-group">
                                     <div class="input-group-prepend">
                                         <select name="property_size_unit" id="property_size_unit" class="form-control">
                                             <option value="">Select Unit</option>
                                             {{ $propertySizeUnits_dropdown }}
                                         </select>
                                     </div>
                                     <input type="number" name="property_size" id="property_size" class="form-control"
                                         placeholder="Property Size">
                                 </div>
                             </div>
                             {{-- @if (auth()->user()->company_id)
                                 <input type="hidden" name="company_id" id="company_id"
                                     value="{{ auth()->user()->company_id }}">
                             @else --}}
                             {{-- <div class="col-sm-4">
                                 <label>Company</label>
                                 <select class="form-control select2" name="company_id" id="company_id">
                                     <option value="">Select Company</option>
                                     {{ $company_dropdown }}
                                 </select>
                             </div> --}}
                             {{-- @endif --}}
                         </div>

                         <hr>

                         <!-- Plot & Coordinates -->
                         <h6 class="mb-4 text-lightblue vendorformh6">Plot & Coordinates</h6>
                         <div class="form-row">
                             <div class="col-md-4 col-sm-12 mb-3">
                                 <label class="asterisk">Plot No</label>
                                 <input type="text" name="plot_no" id="plot_no" class="form-control"
                                     placeholder="Plot No" required>
                             </div>
                             <div class="col-md-4 col-sm-12 mb-3">
                                 <label>Latitude</label>
                                 <input type="number" name="latitude" id="latitude" class="form-control"
                                     placeholder="e.g. 25.204849" step="0.00000001" min="-90" max="90">
                             </div>
                             <div class="col-md-4 col-sm-12 mb-3">
                                 <label>Longitude</label>
                                 <input type="number" name="longitude" id="longitude" class="form-control"
                                     placeholder="e.g. 55.270783" step="0.00000001" min="-180" max="180">
                             </div>
                         </div>
                         <hr>

                         <!-- Location & Address -->
                         <h6 class="mb-4 text-lightblue vendorformh6">Location & Address</h6>
                         <div class="form-row">
                             <div class="col-md-6 col-sm-12 mb-3">
                                 <label>Location <small class="text-muted font-weight-lighter">(Enter a valid URL
                                         starting
                                         with http:// or
                                         https://)</small></label>
                                 <textarea name="location" id="location" class="form-control" rows="2" placeholder="Enter location link"
                                     pattern="https?://.+" title="Please enter a valid URL starting with http:// or https://"></textarea>
                             </div>

                             <div class="col-md-6 col-sm-12 mb-3">
                                 <label>Address</label>
                                 <textarea name="address" id="address" class="form-control" rows="2" placeholder="Full property address"></textarea>
                             </div>
                         </div>
                         <hr>

                         <!-- Makani & Status -->
                         <h6 class="mb-4 text-lightblue vendorformh6">Identifiers & Status</h6>
                         <div class="form-row">
                             <div class="col-md-6 col-sm-12 mb-3">
                                 <label>Makani Number <small class="text-muted font-weight-lighter">(Should have 10
                                         digits)</small></label>
                                 <input type="text" name="makani_number" id="makani_number" class="form-control"
                                     placeholder="e.g. 1234567890" maxlength="11">
                             </div>
                             <div class="col-md-6 col-sm-12 mb-3">
                                 <label class="asterisk">Status</label>
                                 <select name="status" id="status" class="form-control" required>
                                     <option value="1" selected>Active</option>
                                     <option value="0">Inactive</option>
                                 </select>
                             </div>
                         </div>
                         <hr>

                         <!-- Remarks -->
                         <div class="form-row">
                             <div class="col-md-12 mb-3">
                                 <label>Remarks</label>
                                 <textarea name="remarks" id="remarks" class="form-control" rows="3"
                                     placeholder="Any additional notes or remarks"></textarea>
                             </div>
                         </div>

                     </div>
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
     let allLocalities = @json($localities);
     let allpropertytypes = @json($property_types);

     //  $('#company_id').on('change', function() {
     //      let companyId = $(this).val();
     //      companyChange(companyId, null); // reset areaVal when adding
     //  });

     //  function companyChange(companyId, areaVal, propertytypeVal, localityVal) {
     //      let options = '<option value="">Select Area</option>';
     //      let options2 = '<option value="">Select Property Type</option>';

     //      Areas
     //          .filter(a => a.company_id == companyId)
     //          .forEach(a => {
     //              options += `<option value="${a.id}" ${(a.id == areaVal) ? 'selected' : ''}>${a.area_name}</option>`;
     //          });
     //      $('#area_id').html(options).trigger('change');
     //      areaChange(areaVal, localityVal);

     //      allpropertytypes
     //          .filter(pt => pt.company_id == companyId)
     //          .forEach(pt => {
     //              options2 +=
     //                  `<option value="${pt.id}" ${(pt.id == propertytypeVal) ? 'selected' : ''}>${pt.property_type}</option>`;
     //          });
     //      $('#property_type_id').html(options2).trigger('change');
     //  }

     $('#area_id').on('change', function() {
         let areaId = $(this).val();
         areaChange(areaId, null); // reset areaVal when adding
     });

     function areaChange(areaId, localityVal) {
         let options = '<option value="">Select Locality</option>';

         allLocalities
             .filter(l => l.area_id == areaId)
             .forEach(l => {
                 options +=
                     `<option value="${l.id}" ${(l.id == localityVal) ? 'selected' : ''}>${l.locality_name}</option>`;
             });
         $('#locality_id').html(options).trigger('change');
     }
 </script>

 <script>
     $('#PropertyForm').submit(function(e) {
         e.preventDefault();
         if (!validateform()) {
             return;
         }
         //  $('#company_id').prop('disabled', false);
         const ptform = $(this);
         //  ptform.find('select[name="company_id"]').prop('disabled', false);
         ptform.find('select[name="area_id"]').prop('disabled', false);
         ptform.find('select[name="locality_id"]').prop('disabled', false);
         ptform.find('select[name="property_type_id"]').prop('disabled', false);



         var form = document.getElementById('PropertyForm');
         var fdata = new FormData(form);

         $.ajax({
             type: "POST",
             url: "{{ route('property.store') }}",
             data: fdata,
             dataType: "json",
             processData: false,
             contentType: false,
             success: function(response) {
                 toastr.success(response.message);
                 //  window.location.reload();
                 @if (request()->is('property'))
                     window.location.reload();
                 @else
                     let newOption = new Option(response.data.property_name, response.data.id, true,
                         true);
                     //  console.log(newOption);

                     $('#vc_property_id').prepend(newOption).val(response.data.id).trigger('change');
                     if (document.activeElement) {
                         document.activeElement.blur();
                     }
                     ptform[0].reset();
                     //  ptform.find('select[name="company_id"]').prop('disabled', true);
                     ptform.find('select[name="area_id"]').prop('disabled', true);
                     ptform.find('select[name="locality_id"]').prop('disabled', true);
                     ptform.find('select[name="property_type_id"]').prop('disabled', true);
                     $('#modal-property').modal('hide');
                 @endif
             },
             error: function(errors) {
                 toastr.error(errors.responseJSON.message);
                 //  if ($('#property_id').val()) {
                 //      $('#company_id').prop('disabled', true);
                 //  }

             }
         });
     });

     $('#modal-property').on('hidden.bs.modal', function() {
         const $modal = $(this);
         const $form = $modal.find('form#PropertyForm');

         $form[0].reset();

         //  $form.find(
         //      'select[name="company_id"], select[name="area_id"], select[name="locality_id"]]'
         //  ).each(function() {
         //      const $select = $(this);

         //      $select.empty();

         //      $select.val(null).trigger('change');

         //      $select.prop('disabled', false);
         //  });
         $form.find(
             'select[name="area_id"], select[name="locality_id"]'
         ).each(function() {
             const $select = $(this);

             //  $select.empty();
             $select.val(null).trigger('change');
             $select.prop('disabled', false);
         });

     });

     function validateform(e) {
         //  var makani = $('#makani_number').val().trim();
         var location = $('#location').val().trim();

         //  if (makani && !/^\d{10}$/.test(makani)) {
         //      alert('Makani number must be exactly 10 digits.');
         //      $('#makani_number').focus();
         //      return false;
         //  }

         if (location && !/^https?:\/\/[^\s]+$/.test(location)) {
             toastr.error('Please enter a valid URL starting with http:// or https://');
             $('#location').focus();
             return false;
         }
         return true;
     }
 </script>
 <script>
     document.addEventListener('DOMContentLoaded', function() {
         const input = document.getElementById('makani_number');

         input.addEventListener('input', function() {
             let value = this.value.replace(/\D/g, ''); // digits only

             if (value.length > 10) {
                 value = value.substring(0, 10);
             }

             if (value.length > 5) {
                 value = value.substring(0, 5) + ' ' + value.substring(5);
             }

             this.value = value;
         });
     });
 </script>
