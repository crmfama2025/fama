<div class="modal fade" id="modal-company" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Company</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="" id="companyForm">
                @csrf
                <input type="hidden" name="id" id="company_id">
                <div class="modal-body">
                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-sm-6">
                                <label for="inputEmail3" class="col-form-label asterisk">Company Name</label>
                                <input type="text" name="company_name" id="company_name" class="form-control"
                                    id="inputEmail3" placeholder="Company Name" required>
                            </div>
                            <div class="col-sm-6">
                                <label for="inputEmail3" class="col-form-label asterisk">Company Short Code</label>
                                <input type="text" name="company_short_code" id="company_short_code"
                                    class="form-control" id="inputEmail3" placeholder="Company Short Code" required>
                            </div>


                        </div>
                        <div class="form-group row">
                            <div class="col-sm-6">
                                <label for="inputEmail3" class="col-form-label asterisk">Trade License Number</label>
                                <input type="text" name="trade_license_number" id="trade_license_number"
                                    class="form-control" id="inputEmail3" pattern="\d{6,8}"
                                    placeholder="Trade License Number " required>
                            </div>
                            <div class="col-sm-6">
                                <label for="inputEmail3" class="col-form-label ">Registration Number</label>
                                <input type="text" name="registration_no" id="registration_no" class="form-control"
                                    pattern="[A-Za-z0-9-]{6,15}" id="inputEmail3"
                                    placeholder="e.g. 12345678 or DMCC-123456">
                            </div>


                        </div>
                        <div class="form-group row">
                            <div class="col-sm-6">
                                <label for="industry_id" class="col-form-label asterisk">Industry</label>
                                <select name="industry_id" id="industry_id" class="form-control select2"
                                    style="width: 100%;" required>
                                    <option value="">-- Select Industry --</option>
                                    {{ $industry_dropdown }}
                                </select>
                            </div>
                            <div class="col-sm-6">
                                <label for="inputEmail3" class="col-form-label asterisk">Website</label>
                                <input type="text" name="website" id="website" class="form-control"
                                    id="inputEmail3" placeholder="Website" required>
                            </div>

                        </div>
                        <div class="form-group row">
                            <div class="col-sm-6">
                                <label for="inputEmail3" class="col-form-label asterisk">Phone</label>
                                <input type="number" name="phone" id="phone" class="form-control"
                                    id="inputEmail3" placeholder="Phone" required>
                            </div>
                            <div class="col-sm-6">
                                <label for="inputEmail3" class="col-form-label asterisk">Email</label>
                                <input type="email" name="email" id="email" class="form-control"
                                    id="inputEmail3" placeholder="Email" required>
                            </div>

                        </div>
                        <div class="form-group row">
                            <div class="col-sm-6">
                                <label for="inputEmail3" class="col-form-label">Address</label>
                                <textarea name="address" class="form-control" id="address"></textarea>

                            </div>
                            <div class="col-sm-6">
                                <label class="asterisk">Status</label>
                                <select name="status" id="status" class="form-control" required>
                                    <option value="1" default>Active</option>
                                    <option value="0">Inactive
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-6">
                                <label for="inputEmail3" class="col-form-label asterisk">Company Arabic Name</label>
                                <input type="text" name="company_arabic_name" id="company_arabic_name"
                                    class="form-control" id="inputEmail3" placeholder="Company Arabic Name" required>
                            </div>
                            <div class="col-sm-6">
                                <label for="inputEmail3" class="col-form-label ">Company Letter Head</label>
                                <input type="file" name="letter_head_path" id="letter_head" class="form-control"
                                    id="inputEmail3" placeholder="Company Letter Head">
                            </div>


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
    $('#companyForm').submit(function(e) {
        e.preventDefault();
        $('#company_id').prop('disabled', false);

        var form = document.getElementById('companyForm');
        var fdata = new FormData(form);

        $.ajax({
            type: "POST",
            url: "{{ route('company.store') }}",
            data: fdata,
            dataType: "json",
            processData: false,
            contentType: false,
            success: function(response) {
                toastr.success(response.message);
                @if (request()->is('company'))
                    window.location.reload();
                @else
                    let newCompany = {
                        id: response.data.id,
                        name: response.data.company_name
                    };

                    // 1️⃣ Create and prepend new option
                    let newOption = new Option(newCompany.name, newCompany.id, true, true);
                    $('#vc_company_id')
                        .prepend(newOption) // adds at top
                        .val(newCompany.id) // select it
                        .trigger('change'); // refresh select2

                    // 2️⃣ Store globally for use elsewhere (like vendor modal)
                    window.lastAddedCompanyId = newCompany.id;
                    window.lastAddedCompanyName = newCompany.name;

                    window.addedCompanyIdCopy = newCompany.id;
                    window.addedCompanyNameCopy = newCompany.name;



                    if (document.activeElement) {
                        document.activeElement.blur();
                    }

                    $('#modal-company').modal('hide');
                @endif
            },
            error: function(errors) {
                toastr.error(errors.responseJSON.message);

            }
        });
    });
</script>
