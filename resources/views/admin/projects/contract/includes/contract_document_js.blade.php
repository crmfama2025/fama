<script>
    $('#ContractUploadForm').submit(function(e) {
        e.preventDefault();

        showLoader(); //'Processing upload...', 'Please wait while the documents are being uploaded.'

        var form = document.getElementById('ContractUploadForm');
        var fdata = new FormData(form);
        var contractId = $('#contract_id_upload').val();

        fdata.append('_token', $('meta[name="csrf-token"]').attr('content'));

        $.ajax({
            type: "POST",
            url: "{{ route('contract.document_upload') }}",
            data: fdata,
            dataType: "json",
            processData: false,
            contentType: false,
            success: function(response) {
                toastr.success(response.message);
                window.location.href = "{{ url('contract-documents') }}/" + contractId;
            },
            error: function(errors) {
                hideLoader();
                // Example: get first file error
                let message = errors.responseJSON.message;
                if (message.file) {
                    toastr.error(message.file[0]);
                } else if (message.signed_contract) {
                    toastr.error(message.signed_contract[0]);
                } else {
                    toastr.error('Something went wrong.');
                }
            }
        });
    });
</script>
