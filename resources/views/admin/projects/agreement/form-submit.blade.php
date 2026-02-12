<script>
    document.getElementById('agreementForm').addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            return false;
        }
    });
</script>
<script>
    $('.agreementFormSubmit').click(function(e) {
        e.preventDefault();
        const contractForm = $(this);
        const stepIndex = window.stepper._currentIndex;
        if (!validateStep(stepIndex)) {
            Swal.fire({
                icon: 'warning',
                title: 'Incomplete Step',
                text: 'Please fill in all required fields before submitting.',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2500
            });
            return;
        }

        // if (!validateChequeNumber()) {
        //     return;
        // }



        const agreementId = $('input[name="agreement_id"]').val();
        let url = "{{ route('agreement.store') }}";
        let method = 'POST';
        var form = document.getElementById('agreementForm');
        var fdata = new FormData(form);
        // Update
        if (agreementId) {
            url = "{{ url('agreement') }}/" + agreementId;
            method = 'POST';
            fdata.append('_method', 'PUT');
        }

        // Add CSRF
        fdata.append('_token', $('meta[name="csrf-token"]').attr('content'));
        // ===== Add confirmation dialog here =====
        Swal.fire({
            title: 'Are you sure?',
            text: "Do you want to submit this agreement?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, submit it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                showLoader();

                $.ajax({
                    url: url,
                    type: method,
                    data: fdata,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        hideLoader();
                        // Swal.fire({
                        //     title: 'Success!',
                        //     text: response.message,
                        //     icon: 'success',
                        //     showConfirmButton: false,
                        //     timer: 1500
                        // }).then(() => {
                        //     window.location = "{{ route('agreement.index') }}"
                        // })
                        toastr.success(response.message);
                        window.location = "{{ route('agreement.index') }}"
                    },
                    error: function(xhr) {
                        hideLoader();

                        const response = xhr.responseJSON;
                        if (xhr.status === 422 && response?.errors) {
                            $.each(response.errors, function(key, messages) {
                                toastr.error(messages[0]);
                                // alert(key);
                                // if (key.includes('cheque_number')) {
                                //     let match = key.match(
                                //         /payment_detail\[(\d+)\]\[cheque_number\]/);
                                //     if (match) {
                                //         let index = match[1];
                                //         console.log(index);
                                //         $(`#cheque_no${index}`)
                                //             .addClass('is-invalid')
                                //             .after(
                                //                 `<span class="invalid-feedback d-block">${messages[0]}</span>`
                                //             );
                                //     }

                                //     // Optional: Also show a nice alert
                                //     Swal.fire({
                                //         icon: 'error',
                                //         title: 'Cheque Validation Error',
                                //         text: messages[0],
                                //     });
                                // }
                            });
                        } else if (response.message) {
                            console.log(response);
                            toastr.error(response.message);
                            const original = response?.error?.response?.original;

                            if (original && original.cheque_number && original.bank_id) {
                                const chequeNumber = original.cheque_number;
                                const bankId = original.bank_id;

                                $('input[id^="cheque_no"]').each(function() {
                                    const chequeInput = $(this);
                                    const val = chequeInput.val()?.trim();
                                    const parent = chequeInput.closest(
                                        '[id^="extra_fields_"]');
                                    const selectedBank = parent.find(
                                            'select[id^="bank_name"]')
                                        .val();
                                    parent.find('input[id^="cheque_no"]')
                                        .removeClass('is-invalid');
                                    parent.find('.invalid-feedback').remove();

                                    if (val === chequeNumber && selectedBank ===
                                        bankId) {
                                        // Remove old errors first
                                        chequeInput.removeClass('is-invalid')
                                            .removeClass(
                                                'is-valid');
                                        parent.find('.invalid-feedback')
                                            .remove();
                                        chequeInput
                                            .addClass('is-invalid')
                                            .removeClass('is-valid')
                                            .after(
                                                `<span class="invalid-feedback d-block">${original.message}</span>`
                                            );
                                    }
                                });
                            }
                        }
                    }
                });
            }

        });
    });


    // function validateChequeNumber() {
    //     let isValid = true;
    //     let entries = {};
    //     let invalidCheques = [];
    //     let duplicateCheques = [];

    //     $('[id^="extra_fields_"]').each(function() {
    //         let cheque = $(this).find('input[id^="cheque_no_"]').val()?.trim();
    //         let bank = $(this).find('select[id^="bank_name_"]').val();
    //         let chequeInput = $(this).find('input[id^="cheque_no_"]');

    //         // Invalid cheque format
    //         if (cheque && !/^\d{6,10}$/.test(cheque)) {
    //             // Swal.fire({
    //             //     icon: 'warning',
    //             //     title: 'Invalid Cheque Number',
    //             //     text: 'Cheque number must be 6 to 10 digits only.',
    //             // });
    //             // chequeInput.focus();
    //             // chequeInput.addClass('is-invalid').removeClass('is-valid');
    //             invalidCheques.push(chequeInput);

    //             isValid = false;
    //             // return false; // break loop
    //         } else if (cheque) {
    //             chequeInput.addClass('is-valid').removeClass('is-invalid');
    //         }
    //         invalidCheques.forEach(function(input) {
    //             input.addClass('is-invalid').removeClass('is-valid');
    //         });
    //         if (invalidCheques.length) {

    //             Swal.fire({
    //                 icon: 'warning',
    //                 title: 'Invalid Cheque Number',
    //                 text: 'Cheque number must be 6 to 10 digits only.',
    //             });
    //             return false; // break loop

    //         }


    //         // Duplicate cheque for same bank
    //         if (cheque && bank) {
    //             let key = bank + '_' + cheque;
    //             if (entries[key]) {
    //                 Swal.fire({
    //                     icon: 'error',
    //                     title: 'Duplicate Cheque Number',
    //                     text: 'This cheque number is already used for the selected bank.',
    //                 });
    //                 // chequeInput.focus();
    //                 chequeInput.addClass('is-invalid').removeClass('is-valid');
    //                 isValid = false;
    //                 return false; // break loop
    //             } else {
    //                 entries[key] = true;
    //                 chequeInput.addClass('is-valid').removeClass('is-invalid');
    //             }
    //         }
    //     });

    //     return isValid; // ✅ important
    // }
    function validateChequeNumber() {
        // alert("called");
        let isValid = true;
        let entries = {};
        let invalidCheques = [];
        let duplicateCheques = [];

        $('[id^="extra_fields_"]').each(function() {
            // alert('called');
            let cheque = $(this).find('input[id^="cheque_no"]').val()?.trim();
            let bank = $(this).find('select[id^="bank_name"]').val();
            let chequeInput = $(this).find('input[id^="cheque_no"]');


            // Check invalid format
            if (cheque && !/^\d{6,10}$/.test(cheque)) {
                invalidCheques.push(chequeInput);
                isValid = false;
            }

            // Check duplicate for same bank
            if (cheque && bank) {
                let key = bank + '_' + cheque;
                if (entries[key]) {
                    duplicateCheques.push(chequeInput);
                    isValid = false;
                } else {
                    entries[key] = true;
                }
            }

            // Clear valid styling for correct cheques
            if (!invalidCheques.includes(chequeInput) && !duplicateCheques.includes(
                    chequeInput) &&
                cheque) {
                chequeInput.addClass('is-valid').removeClass('is-invalid');
            }
        });

        // Mark all invalid cheques
        invalidCheques.forEach(function(input) {
            input.addClass('is-invalid').removeClass('is-valid');
        });

        // Mark all duplicate cheques
        duplicateCheques.forEach(function(input) {
            input.addClass('is-invalid').removeClass('is-valid');
        });

        // Show alert if any invalid
        if (invalidCheques.length) {
            Swal.fire({
                icon: 'warning',
                title: 'Invalid Cheque Number',
                text: 'Cheque numbers must be 6-10 digits.',
            });
        }

        // Show alert if any duplicates
        if (duplicateCheques.length) {
            Swal.fire({
                icon: 'error',
                title: 'Duplicate Cheque Number',
                text: 'This cheque number is already used for the selected bank.',
            });
        }

        return isValid;
    }
</script>
