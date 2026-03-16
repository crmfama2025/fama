<script>
    // $('#tenancyForm').on('submit', function(e) {
    //     e.preventDefault();

    //     const form = document.getElementById('tenancyForm');
    //     const formData = new FormData(form);

    //     // Show loading state
    //     $('#submitBtn').prop('disabled', true).html(
    //         '<i class="fas fa-spinner fa-spin mr-1"></i> Saving...'
    //     );

    //     $.ajax({
    //         url: '{{ route('tenant-registration.store') }}',
    //         method: 'POST',
    //         data: formData,
    //         processData: false,
    //         contentType: false,
    //         success: function(response) {
    //             console.log('Success:', response);

    //             Swal.fire({
    //                 icon: 'success',
    //                 title: 'Agreement Saved!',
    //                 text: response.message ??
    //                     'Tenancy agreement created successfully.',
    //                 confirmButtonColor: '#17a2b8',
    //             }).then(() => {
    //                 window.location.href = '{{ route('tenant-registration.index') }}';
    //             });
    //         },
    //         error: function(xhr) {
    //             console.error('Error:', xhr.responseJSON);

    //             // Laravel validation errors (422)
    //             if (xhr.status === 422) {
    //                 const errors = xhr.responseJSON.errors;
    //                 let errorList = '';
    //                 $.each(errors, function(field, messages) {
    //                     messages.forEach(msg => {
    //                         errorList += `<li>${msg}</li>`;
    //                     });
    //                 });

    //                 Swal.fire({
    //                     icon: 'error',
    //                     title: 'Validation Errors',
    //                     html: `<ul style="text-align:left;">${errorList}</ul>`,
    //                     confirmButtonColor: '#17a2b8',
    //                 });
    //             } else {
    //                 Swal.fire({
    //                     icon: 'error',
    //                     title: 'Something went wrong',
    //                     text: xhr.responseJSON?.message ?? 'Please try again.',
    //                     confirmButtonColor: '#17a2b8',
    //                 });
    //             }
    //         },
    //         complete: function() {
    //             $('#submitBtn').prop('disabled', false).html(
    //                 '<i class="fas fa-save mr-1"></i> Save Agreement'
    //             );
    //         }
    //     });
    // });
    $('#tenancyForm').on('submit', function(e) {
        e.preventDefault();

        const form = document.getElementById('tenancyForm');
        const formData = new FormData(form);

        // Determine if we are editing
        const agreementId = $(form).data('agreement-id'); // Set this in your blade if editing
        let url = '{{ route('tenant-registration.store') }}';
        let method = 'POST';

        if (agreementId) {
            url =
                `/tenant-registration/${agreementId}`; // or use route('tenant-registration.update', agreementId)
            method = 'POST'; // Laravel requires POST + _method=PUT for forms
            formData.append('_method', 'PUT'); // Laravel expects this
        }

        // Show loading state
        $('#submitBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Saving...');

        $.ajax({
            url: url,
            method: method,
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Agreement Saved!',
                    text: response.message ?? 'Tenancy agreement saved successfully.',
                    confirmButtonColor: '#17a2b8',
                }).then(() => {
                    window.location.href = '{{ route('tenant-registration.index') }}';
                });
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    let errorList = '';
                    $.each(errors, function(field, messages) {
                        messages.forEach(msg => errorList += `<li>${msg}</li>`);
                    });

                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Errors',
                        html: `<ul style="text-align:left;">${errorList}</ul>`,
                        confirmButtonColor: '#17a2b8',
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Something went wrong',
                        text: xhr.responseJSON?.message ?? 'Please try again.',
                        confirmButtonColor: '#17a2b8',
                    });
                }
            },
            complete: function() {
                $('#submitBtn').prop('disabled', false).html(
                    '<i class="fas fa-save mr-1"></i> Save Agreement');
            }
        });
    });
</script>
