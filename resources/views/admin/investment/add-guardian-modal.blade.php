<div class="modal fade" id="addGuardianModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <form id="addGuardianForm" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="guardian_id" id="modal_guardian_id">

            <div class="modal-content">
                <div class="modal-header bg-info">
                    <h5 class="modal-title text-white">
                        <i class="fas fa-user-plus mr-1"></i>
                        Add Guardian
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">

                    <div class="form-group row">
                        <div class="col-sm-6">
                            <label class="asterisk">Guardian Name</label>
                            <input type="text" name="guardian_name" id="guardian_name" class="form-control"
                                placeholder="Guardian Name" required>
                        </div>

                        <div class="col-sm-6">
                            <label class="asterisk">Guardian Name In Arabic</label>
                            <input type="text" name="guardian_name_arabic" id="guardian_name_arabic"
                                class="form-control arabic-input" placeholder="Guardian Name in Arabic" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-sm-6">
                            <label class="asterisk">Guardian Mobile</label>
                            <input type="text" name="guardian_mobile" id="guardian_mobile" class="form-control"
                                placeholder="Guardian Mobile" required>
                        </div>

                        <div class="col-sm-6">
                            <label class="asterisk">Guardian Email</label>
                            <input type="email" name="guardian_email" id="guardian_email" class="form-control"
                                placeholder="Guardian Email" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-sm-6">
                            <label class="asterisk">Guardian Emirates ID</label>
                            <input type="text" name="emirates_id_number" id="emirates_id_number" class="form-control"
                                placeholder="Guardian Emirates ID" required>
                        </div>

                        <div class="col-sm-6">
                            <label class="asterisk">Emirates ID Expiry Date</label>
                            <input type="date" name="eid_expiry_date" id="eid_expiry_date" class="form-control"
                                required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-sm-6">
                            <label class="asterisk">Guardian Passport Number</label>
                            <input type="text" name="passport_number" id="passport_number" class="form-control"
                                placeholder="Guardian Passport Number" required>
                        </div>

                        <div class="col-sm-6">
                            <label class="asterisk">Passport Expiry Date</label>
                            <input type="date" name="passport_expiry_date" id="passport_expiry_date"
                                class="form-control" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-sm-6">
                            <label class="asterisk" id="emiratesIdCopyLabel">Emirates ID Copy</label>
                            <input type="file" name="emirates_id_copy" class="form-control">
                            <div id="currentEmiratesIdCopy" class="mt-2"></div>
                        </div>

                        <div class="col-sm-6">
                            <label class="asterisk" id="passportCopyLabel">Passport Copy</label>
                            <input type="file" name="passport_copy" class="form-control">
                            <div id="currentPassportCopy" class="mt-2"></div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-6">
                            <label class="asterisk">Guardian Address</label>
                            <input type="text" name="guardian_address" id="guardian_address" class="form-control"
                                placeholder="Guardian Address" required>
                        </div>
                        <div class="col-sm-6">
                            <label class="asterisk">Guardian Address Arabic</label>
                            <input type="text" name="guardian_address_ar" id="guardian_address_ar"
                                class="form-control arabic-input" placeholder="Guardian Address Arabic" required>
                        </div>
                    </div>

                </div>


                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Cancel
                    </button>

                    <button type="submit" class="btn btn-info">
                        <i class="fas fa-save mr-1"></i>
                        Save Guardian
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
    $('#addGuardianBtn').on('click', function() {
        $('#addGuardianForm')[0].reset();
        $('#modal_guardian_id').val('');

        $('#guardianModalTitle').html(
            '<i class="fas fa-user-plus mr-1"></i> Add Guardian'
        );

        $('#guardianSubmitBtn').html(
            '<i class="fas fa-save mr-1"></i> Save Guardian'
        );

        // Files are required when adding
        $('#emirates_id_copy').prop('required', true);
        $('#passport_copy').prop('required', true);

        $('#emiratesIdCopyLabel').addClass('asterisk');
        $('#passportCopyLabel').addClass('asterisk');

        $('#currentEmiratesIdCopy').html('');
        $('#currentPassportCopy').html('');
    });

    $('#addGuardianForm').on('submit', function(e) {
        e.preventDefault();

        let form = this;
        let formData = new FormData(form);
        let guardianId = $('#modal_guardian_id').val();
        let url = "{{ route('investor-guardian.store') }}";
        if (guardianId) {
            url = "{{ route('investor-guardian.update', ':id') }}".replace(':id', guardianId);
            formData.append('_method', 'PUT');
        }

        $.ajax({
            url: url,
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,

            beforeSend: function() {
                $('#addGuardianForm button[type="submit"]').prop('disabled', true);
            },

            success: function(response) {
                if (response.success) {
                    let guardian = response.guardian;
                    if (!guardianId) {
                        let option = new Option(
                            guardian.guardian_name + ' - ' + guardian.investor_guardian_code,
                            guardian.id,
                            true,
                            true
                        );

                        $('#guardian_id').append(option).trigger('change');
                    }

                    $('#addGuardianModal').modal('hide');
                    $('#addGuardianForm')[0].reset();
                    $('#modal_guardian_id').val('');


                    $('#guardianModalTitle').html(
                        '<i class="fas fa-user-plus mr-1"></i> Add Guardian');
                    $('#guardianSubmitBtn').html('<i class="fas fa-save mr-1"></i> Save Guardian');
                    $('#currentEmiratesIdCopy').html('');
                    $('#currentPassportCopy').html('');

                    if ($('#InvestorGuardianList').length) {
                        $('#InvestorGuardianList').DataTable().ajax.reload(null, false);
                    }

                    toastr.success(response.message || (guardianId ?
                        'Guardian updated successfully.' : 'Guardian added successfully.'));
                }
            },

            error: function(xhr) {
                let response = xhr.responseJSON;
                if (response?.errors) {
                    $.each(response.errors, function(key, messages) {
                        if (Array.isArray(messages)) {
                            messages.forEach(function(message) {
                                toastr.error(message);
                            });
                        } else {
                            toastr.error(messages);
                        }
                    });
                } else if (response
                    ?.message
                ) {
                    toastr.error(response.message);
                } else {
                    toastr.error('Something went wrong.');
                }
            },

            complete: function() {
                $('#addGuardianForm button[type="submit"]').prop('disabled', false);
            }
        });
    });
</script>
