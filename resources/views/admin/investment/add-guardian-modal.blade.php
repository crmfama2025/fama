<div class="modal fade" id="addGuardianModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <form id="addGuardianForm" enctype="multipart/form-data">
            @csrf

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
                            <input type="text" name="guardian_name" class="form-control" placeholder="Guardian Name"
                                required>
                        </div>

                        <div class="col-sm-6">
                            <label class="asterisk">Guardian Name In Arabic</label>
                            <input type="text" name="guardian_name_arabic" class="form-control arabic-input"
                                placeholder="Guardian Name in Arabic" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-sm-6">
                            <label class="asterisk">Guardian Mobile</label>
                            <input type="text" name="guardian_mobile" class="form-control"
                                placeholder="Guardian Mobile" required>
                        </div>

                        <div class="col-sm-6">
                            <label class="asterisk">Guardian Email</label>
                            <input type="email" name="guardian_email" class="form-control"
                                placeholder="Guardian Email" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-sm-6">
                            <label class="asterisk">Guardian Emirates ID</label>
                            <input type="text" name="emirates_id_number" class="form-control"
                                placeholder="Guardian Emirates ID" required>
                        </div>

                        <div class="col-sm-6">
                            <label class="asterisk">Emirates ID Expiry Date</label>
                            <input type="date" name="eid_expiry_date" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-sm-6">
                            <label class="asterisk">Guardian Passport Number</label>
                            <input type="text" name="passport_number" class="form-control"
                                placeholder="Guardian Passport Number" required>
                        </div>

                        <div class="col-sm-6">
                            <label class="asterisk">Passport Expiry Date</label>
                            <input type="date" name="passport_expiry_date" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-sm-6">
                            <label class="asterisk">Emirates ID Copy</label>
                            <input type="file" name="emirates_id_copy" class="form-control" required>
                        </div>

                        <div class="col-sm-6">
                            <label class="asterisk">Passport Copy</label>
                            <input type="file" name="passport_copy" class="form-control" required>
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
    $('#addGuardianForm').on('submit', function(e) {
        e.preventDefault();

        let form = this;
        let formData = new FormData(form);

        $.ajax({
            url: "{{ route('investor.guardian.store') }}",
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

                    let option = new Option(
                        guardian.guardian_name + ' - ' + guardian.guardian_mobile,
                        guardian.id,
                        true,
                        true
                    );

                    $('#guardian_id').append(option).trigger('change');

                    $('#addGuardianModal').modal('hide');
                    $('#addGuardianForm')[0].reset();

                    toastr.success('Guardian added successfully.');
                }
            },

            error: function(xhr) {
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    $.each(xhr.responseJSON.errors, function(key, messages) {
                        toastr.error(messages[0]);
                    });
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
