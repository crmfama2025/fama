<div class="modal fade" id="modal-locality-pricing" tabindex="-1"
    role="dialog" aria-hidden="true">

    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">Locality Pricing</h4>

                <button type="button" class="close"
                    data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="localityPricingForm">
                @csrf

                <input type="hidden" name="locality_id"
                    id="pricing_locality_id">

                <div class="modal-body">

                    {{-- Area and Locality details --}}
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label>Area Name</label>
                            <input type="text"
                                id="pricing_area_name"
                                class="form-control"
                                readonly>
                        </div>

                        <div class="col-md-6">
                            <label>Locality Name</label>
                            <input type="text"
                                id="pricing_locality_name"
                                class="form-control"
                                readonly>
                        </div>
                    </div>

                    {{-- Unit Types and Prices --}}
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Unit Type</th>
                                    <th style="width: 40%">Price</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($unitTypes as $unitType)
                                    <tr>
                                        <td>
                                            <input type="text"
                                                class="form-control"
                                                value="{{ $unitType->unit_type }}"
                                                readonly>

                                            <input type="hidden"
                                                name="prices[{{ $unitType->id }}][unit_type_id]"
                                                value="{{ $unitType->id }}">
                                        </td>

                                        <td>
                                            <input type="number"
                                                name="prices[{{ $unitType->id }}][price]"
                                                class="form-control"
                                                min="0"
                                                step="0.01"
                                                placeholder="Enter price"
                                                >
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2"
                                            class="text-center">
                                            No unit types found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>

                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default"
                        data-dismiss="modal">
                        Close
                    </button>

                    <button type="submit"
                        class="btn btn-info savebtninfo">
                        Save Pricing
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>
<script>
    $('#localityPricingForm').on('submit', function (e) {
        e.preventDefault();

        const form = $(this);
        const submitButton = form.find('button[type="submit"]');

        submitButton.prop('disabled', true);

        $.ajax({
            type: 'POST',
            url: "{{ route('locality.pricing.store') }}",
            data: form.serialize(),
            dataType: 'json',

            success: function (response) {
                toastr.success(response.message);
                $('#modal-locality-pricing').modal('hide');
                $('#localityTable').DataTable().ajax.reload(null, false);
            },

            error: function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON.errors) {
                    const errors = Object.values(xhr.responseJSON.errors)
                        .flat();

                    toastr.error(errors.join('\n'));
                } else {
                    toastr.error(
                        xhr.responseJSON?.message ||
                        'Failed to save locality pricing.'
                    );
                }
            },

            complete: function () {
                submitButton.prop('disabled', false);
            }
        });
    });
</script>
