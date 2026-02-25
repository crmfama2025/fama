@php
    // Ensure $contract is defined, even on create page
    $contract = $contract ?? null;
@endphp

<script>
    // document.getElementById('contractForm').addEventListener('keydown', function(e) {
    //     if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') {
    //         stepper.next();
    //         // e.preventDefault();
    //         return false;
    //     }
    // });
</script>

<script>
    // Add a custom :focusable selector
    $.extend($.expr[':'], {
        focusable: function(el) {
            var nodeName = el.nodeName.toLowerCase();
            var tabIndex = $(el).attr('tabindex');
            if (tabIndex !== undefined && tabIndex < 0) return false;
            return (nodeName === 'input' || nodeName === 'select' || nodeName === 'textarea' || nodeName ===
                'button' || $(el).is('a[href]')) && !$(el).is(':disabled');
        }
    });


    // $('.contractFormSubmit').click(function(e) {
    function ContractFormSubmit(e) {
        e.preventDefault();


        if ($('.contractFormSubmit').prop('disabled') == false) {
            Swal.fire({
                title: "Are you sure?",
                text: "You want to submit!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, submit!"
            }).then((result) => {
                if (result.isConfirmed) {
                    showLoader();
                    // $('#company_id').prop('disabled', false);
                    // const contractForm = $(this);
                    // $(':input').not(':focusable').prop('disabled', true);
                    var form = document.getElementById('contractForm');
                    var fdata = new FormData(form);

                    fdata.append('_token', $('meta[name="csrf-token"]').attr('content'));

                    // If you're updating (PUT/PATCH/DELETE)

                    // let isEdit = @json($contract && $contract->exists);

                    let url =
                        "{{ $edit ? route('contract.update', $contract->id) : route('contract.store') }}";
                    fdata.append('_method', "{{ $edit ? 'PUT' : 'POST' }}");


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
                            window.location.href = "{{ route('contract.index') }}";
                        },
                        error: function(errors) {
                            hideLoader();
                            toastr.error(errors.responseJSON.message);
                        }
                    });
                }
            });
        } else {
            Swal.fire({
                icon: 'warning',
                text: 'Please fix the errors before submitting.',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2500,
            });
        }


    }
</script>
