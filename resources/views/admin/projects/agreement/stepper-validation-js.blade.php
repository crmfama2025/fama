<script>
    document.addEventListener('DOMContentLoaded', function() {
        const stepperElement = document.querySelector('.bs-stepper');

        if (stepperElement) {
            window.stepper = new Stepper(stepperElement);

            // Initialize all Select2 fields
            $('.select2').select2({
                placeholder: 'Select an option',
                allowClear: true
            });


            document.addEventListener('click', function(e) {
                if (e.target.matches('.prevBtn')) {
                    window.stepper.previous();
                }
                if (e.target.matches('.nextBtn')) {
                    const stepIndex = window.stepper._currentIndex;
                    // if (stepIndex === 1) {
                    //     // alert('validating documents');
                    //     let hasError = false;

                    //     $('.document_number').each(function() {
                    //         let rowIndex = this.id.split('_').pop();
                    //         validateDocumentRow(rowIndex);


                    //         if ($('#error_' + rowIndex).text() !== '' ||
                    //             $('#issued_date_' + rowIndex).hasClass('is-invalid') ||
                    //             $('#expiry_date_' + rowIndex).hasClass('is-invalid')) {
                    //             hasError = true;
                    //         }
                    //     });

                    //     if (hasError) {
                    //         Swal.fire({
                    //             icon: 'warning',
                    //             title: 'Incomplete Step',
                    //             text: 'Please correct the errors in the document fields.',
                    //             toast: true,
                    //             position: 'top-end',
                    //             showConfirmButton: false,
                    //             timer: 2500,
                    //         });
                    //         return;
                    //     }

                    // }

                    if (validateStep(stepIndex)) {
                        window.stepper.next();
                        // if (window.stepper._currentIndex === 3) {
                        //     $('#no_of_installments').trigger('change');
                        // }
                        if (window.stepper._currentIndex === 2) {
                            // console.log('ct', selectedContract)

                            if (!(selectedContract.contract_type_id === 1 &&
                                    selectedContract.contract_unit.business_type === 2)) {
                                $('#no_of_installments').trigger('change');

                            }

                        }

                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Incomplete Step',
                            text: 'Please complete all required inputs.',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 2500,
                        });



                    }
                }
            });
        }

        updateDisabledFields(0);

    });

    function updateDisabledFields(currentIndex) {
        const steps = document.querySelectorAll('.step-content');

        steps.forEach((step, index) => {
            const isVisible = step.offsetParent !== null;

            step.querySelectorAll('input, select, textarea').forEach(el => {
                // ✅ disable only past steps, not future ones
                if (index < currentIndex) {
                    el.disabled = true;
                } else {
                    el.disabled = false; // enable for current and future steps
                }
            });
        });
    }

    function validateStep(stepIndex) {
        let isValid = true;
        const stepContainer = document.querySelectorAll('.step-content')[stepIndex]; // <-- fix

        if (!stepContainer) return false;

        // Validate inputs, selects, and textareas
        stepContainer.querySelectorAll('[required]:not([type="radio"])').forEach(field => {
            if (field.offsetParent === null) return; // skip hidden
            // if (!field.value.trim()) {
            //     field.classList.add('is-invalid');
            //     field.classList.remove('is-valid');
            //     isValid = false;
            // } else {
            //     field.classList.add('is-valid');
            //     field.classList.remove('is-invalid');
            // }
            if (!field.checkValidity()) {
                field.classList.add('is-invalid');
                field.classList.remove('is-valid');
                isValid = false;
            } else {
                field.classList.add('is-valid');
                field.classList.remove('is-invalid');
            }
        });

        // Validate Select2 fields separately
        $(stepContainer).find('select.select2[required]').each(function() {
            const value = $(this).val();
            const container = $(this).next('.select2-container');

            if (!$(this).is(':visible')) return;

            if (!value || value.length === 0) {
                container.addClass('is-invalid').removeClass('is-valid');
                isValid = false;
            } else {
                container.addClass('is-valid').removeClass('is-invalid');
            }
        });

        // ✅ Custom conditional validation for documents
        $(stepContainer).find('.form-row').each(function() {
            const firstField = $(this).find('input[name*="[document_number]"]');
            const secondField = $(this).find('input[name*="[document_path]"]');
            const hiddenIdField = $(this).find('input[name*="[id]"]')

            const firstVal = firstField.val().trim();
            const secondVal = secondField.val().trim();
            const hasExistingFile = hiddenIdField.length > 0;

            // Reset validation first
            firstField.removeClass('is-invalid').addClass('is-valid');
            secondField.removeClass('is-invalid').addClass('is-valid');

            // Validation logic
            if (firstVal && !secondVal && !hasExistingFile) {
                // Number filled but no file (and no existing one)
                secondField.addClass('is-invalid').removeClass('is-valid');
                isValid = false;
            } else if (!firstVal && (secondVal || hasExistingFile)) {
                // File present (new or existing), but number missing
                firstField.addClass('is-invalid').removeClass('is-valid');
                isValid = false;
            }
        });

        return isValid;
    }

    $(document).on('blur', 'input[id^="cheque_no"]', function() {

        let result = hasDuplicateChequeNumbers();

        if (result.invalid) {
            // $(this).addClass('is-invalid').removeClass('is-valid');
            $(this).val('');


            $('#submitBtn').prop('disabled', true);
            Swal.fire({
                icon: 'error',
                position: 'top-end',
                toast: true,
                title: 'Invalid Cheque Number',
                text: 'Cheque numbers must be 6-10 digits.',
                timer: 2500,
            });
            // $(this).val('');
            return;
        }

        if (result.duplicate) {
            // $(this).addClass('is-invalid').removeClass('is-valid');
            $(this).val('');

            $('#submitBtn').prop('disabled', true);
            // $(this).val('');
            Swal.fire({
                icon: 'error',
                position: 'top-end',
                toast: true,
                title: 'Duplicate Cheque Number',
                text: 'This cheque number is already used for the selected bank.',
                timer: 2500,
            });
            return;
        }

        // If no issues
        // $(this).removeClass('is-invalid');
        $('#submitBtn').prop('disabled', false);
    });
    // $(document).on('input change', 'input[id^="cheque_no"], select[id^="bank_name"]', function() {
    //     const $parent = $(this).closest('[id^="extra_fields_"]');
    //     $parent.find('input[id^="cheque_no"]').removeClass('is-invalid is-valid');
    //     $('#submitBtn').prop('disabled', false);
    // });




    function hasDuplicateChequeNumbers() {
        let entries = {};
        // let valid = false;
        let invalidFound = false;
        let duplicateFound = false;


        $('[id^="extra_fields_"]').each(function() {

            let chequeInput = $(this).find('input[id^="cheque_no"]');
            let cheque = chequeInput.val()?.trim();
            let bank = $(this).find('select[id^="bank_name"]').val();

            if (cheque && !/^\d{6,10}$/.test(cheque)) {
                invalidFound = true;
                chequeInput.addClass('is-invalid');
            } else {
                chequeInput.removeClass('is-invalid');
            }

            // if (!cheque || !bank || invalidFound) {
            //     return;
            // }
            if (!cheque || !bank || !/^\d{6,10}$/.test(cheque)) return;


            if (cheque && bank) {
                let key = bank + '_' + cheque;

                if (entries[key]) {
                    duplicateFound = true;
                    return false;
                } else {
                    entries[key] = true;
                }
            }
        });

        // return valid;
        return {
            duplicate: duplicateFound,
            invalid: invalidFound
        };
    }
</script>
