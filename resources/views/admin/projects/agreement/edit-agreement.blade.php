@if (isset($agreement))
    <script>
        $(document).ready(function() {
            let companyId = "{{ $agreement->company_id }}";
            let contractId = "{{ $agreement->contract_id }}";

            $('#company_id').val(companyId).trigger('change');
            // console.log("ids" + companyId, contractId);

            CompanyChange(companyId, contractId, editedContract);
        });
        let unitIndex = $('.unit-container .unit-row').length;

        $('#add_more_unit_edit').on('click', function() {
            let addedUnits = getUnitCount();
            const totalUnitsAvailable = window.unit_details.length;
            const firstDataCount = $('.unit-container .unit-row:first .rent_per_month').data('count');

            if (addedUnits >= totalUnitsAvailable) {
                $("#add_more_unit_edit")
                    .prop("disabled", true)
                    .addClass("shake");

                Swal.fire({
                    icon: "warning",
                    title: "Unit Limit Reached",
                    text: "No more units can be added.",
                });
                return;
            }
            addUnitRow(unitIndex, firstDataCount);
            unitIndex++;
        });

        function addUnitRow(index, firstDataCount) {
            let unitTypeOptions = `<option value="">Select Unit Type</option>`;

            // Add normal unit types
            window.unitTypeList.forEach(ut => {
                unitTypeOptions += `<option value="${ut.id}">${ut.unit_type}</option>`;
            });

            // Add deleted unit types if not already in the list
            deletedUnits.forEach(du => {
                if (!$(`.unit_type_select option[value="${du.unit_type_id}"]`).length) {
                    unitTypeOptions += `<option value="${du.unit_type_id}">${du.unit_type_name}</option>`;
                }
            });
            let rowHtml = `
            <div class="card mb-3 unit-row p-3" data-row-index="${index}">
                <div class="card-body">
                    <div class="row g-3 align-items-end">
                        <!-- Unit Type -->
                        <div class="col-md-3">
                            <label class="form-label asterisk">Unit Type</label>
                            <select class="form-control unit_type_select" name="unit_detail[${index}][unit_type_id]" required>
                                // <option value="">Select Unit Type</option>
                                // @foreach ($unitTypeList as $ut)
                                //     <option value="{{ $ut->id }}">{{ $ut->unit_type }}</option>
                                // @endforeach
                                ${unitTypeOptions}
                            </select>
                        </div>

                        <!-- Unit Number -->
                        <div class="col-md-3">
                            <label class="form-label asterisk">Select Unit No</label>
                            <select class="form-control unit_no_select" name="unit_detail[${index}][contract_unit_details_id]" required>
                                <option value="">Select Unit Number</option>
                            </select>
                        </div>

                        <!-- Sub Unit -->
                        <div class="col-md-3 subunit_number_div">
                            <label class="form-label">Sub Unit</label>
                            <select class="form-control select2 sub_unit_type" name="unit_detail[${index}][contract_subunit_details_id]" disabled></select>
                        </div>

                        <!-- Rent per Month -->
                        <div class="col-md-2">
                            <label class="form-label asterisk">Rent per Month</label>
                            <input type="text" class="form-control rent_per_month" name="unit_detail[${index}][rent_per_month]" data-count="${firstDataCount}" readonly>
                        </div>

                        <!-- Delete Button -->
                        <div class="col-md-1 text-end">
                            <button type="button" class="btn btn-danger delete-row-edit">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
            $('.unit-container').append(rowHtml);

            // Re-initialize Select2 for newly added row
            // $('.unit-container .unit-row:last .select2').select2();
            populateUnitTypesForNewRow(index);
            // index++;
            updateUnitOptions('.unit_no_select');
        }
    </script>
    <script>
        // Convert PHP unitTypeList to JS
        window.unitTypeList = @json($unitTypeList);
    </script>
@endif

<script>
    $(document).on('click', '.delete-row-edit', function() {
        let unitId = $(this).data('unit-id');
        let allRows = $('.unit-row');


        if (allRows.length <= 1) {
            toastr.error("At least one unit is required.");
            $(this).prop('disabled', true);
            return;
        }
        if (!unitId) {
            $(this).closest(".unit-row").remove();
            toastr.success("Unit row removed");
            return;
        }
        let contractId = $(this).data('contract-id');
        // console.log("Deleting unit:", unitId, "from contract:", contractId);
        let row = $(this).closest('.unit-row');

        Swal.fire({
            title: "Are you sure?",
            text: "Do you really want to delete this unit?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Yes, delete it",
            cancelButtonText: "Cancel"
        }).then((result) => {
            if (result.isConfirmed) {
                showLoader();
                $.ajax({
                    url: `/agreement-unit/delete/${unitId}`,
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        contract_id: contractId
                    },
                    success: function(response) {
                        // console.log('no of units', response.vacant_units)
                        if (response.success) {
                            if (response.deleted_agreement_unit_id) {
                                let deletedAgreementUnitIds = [];
                                deletedAgreementUnitIds.push(response
                                    .deleted_agreement_unit_id);
                            }
                            deletedUnits.push({
                                unit_type_id: row.find('.unit_type_select').val(),
                                unit_type_name: row.find(
                                    '.unit_type_select option:selected').text(),
                                unit_number: row.find(
                                    '.unit_no_select option:selected').text(),
                                contract_unit_id: row.find('.unit_no_select').val()
                            });
                            row.remove();
                            $(`.accordion-for-unit[data-unit-id='${unitId}']`).remove();
                            hideLoader();
                            toastr.success(response.message);
                        } else {
                            hideLoader();
                            toastr.error(response.message || "Unable to delete unit");
                        }
                        if (response.vacant_units > 0) {
                            $('.add_more_unit').removeClass('d-none');
                        } else {
                            $('.add_more_unit').addClass('d-none');
                        }
                    },
                    error: function(xhr) {
                        hideLoader();
                        toastr.error("Something went wrong. Please try again.");
                    }
                });
            }
        });


    });

    function buildUnitAccordion(unit, payments, unitIndex) {
        // console.log("Building accordion for unit index:", unitIndex);
        // console.log('Building accordion for unit:', unit, 'with payments:', payments);
        const containerPayment = document.querySelector('.payment_details');
        const collapseId = `collapse_${unit.id}`;
        let unitName = `Unit ${unitIndex + 1}`;
        // console.log("Unit Name ", unitName);
        // console.log("unitIndex :", unit);
        const row = document.querySelector(`.unit-row[data-row-index='${unitIndex}']`);
        // if (row) {
        //     const unitNoSelect = row.querySelector('.unit_no_select');
        //     console.log('unitNoSelect:', unitNoSelect);
        //     if (unitNoSelect) {
        //         unitName = unitNoSelect.options[unitNoSelect.selectedIndex].text;
        //     }
        //     console.log("Unit Name from select:", unitName);
        // } else {
        unitName = unit.unit_number;
        // }
        let loopSource = [];
        const installments = $('#no_of_installments').find('option:selected').text().trim();
        console.log("Installments count", installments)

        if (payments.length === installments) {
            loopSource = payments;
        } else {
            // Create empty objects based on installments count
            loopSource = Array.from({
                length: installments
            }, (_, i) => payments[i] || {});
        }


        let installmentBlocks = `
            <div class="row font-weight-bold mb-2">
                <div class="col-md-4 asterisk">Payment Mode</div>
                <div class="col-md-4 asterisk">Payment Date</div>
                <div class="col-md-4 asterisk">Payment Amount</div>
            </div>
        `;

        // payments.forEach((pay, payIndex) => {
        loopSource.forEach((pay, payIndex) => {
            const uniqueId = `${unit.id}_${payIndex}`;
            let formattedDate = '';
            let showDelete = false;
            // console.log("Pay", pay);

            if (pay.payment_date) {
                formattedDate = moment(pay.payment_date, 'YYYY-MM-DD').format('DD-MM-YYYY');
                // Check against termDate

            } else if (pay.receivable_date) {
                formattedDate = pay.receivable_date;
                // const paymentMoment = moment(pay.receivable_date, 'DD-MM-YYYY');
                // const termMoment = moment(termDate, 'DD-MM-YYYY');
                // console.log('DATES', paymentMoment, termMoment);
                // if (paymentMoment.isBefore(termMoment)) {
                //     showDelete = true;
                // }
            }

            installmentBlocks += `
            <div class="form-group row mb-2">
                <div class="mb-2  font-weight-bold text-info">
                            ${payIndex + 1}.
                        </div>
                <input type="hidden" name="payment_detail[${unit.id}][${payIndex}][detail_id]" value="${pay.id ?? ''}">
                <div class="col-md-3">
                    <select class="form-control" name="payment_detail[${unit.id}][${payIndex}][payment_mode_id]" id="payment_mode${uniqueId}">
                        <option value="">Select</option>
                        @foreach ($paymentmodes as $paymentmode)
                            <option value="{{ $paymentmode->id }}" ${pay.payment_mode_id == {{ $paymentmode->id }} ? 'selected' : ''}>
                                {{ $paymentmode->payment_mode_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <div class="input-group date" id="otherPaymentDate_${uniqueId}" data-target-input="nearest">
                        <input type="text" class="form-control datetimepicker-input otherPaymentDate"
                            name="payment_detail[${unit.id}][${payIndex}][payment_date]"
                            id="payment_date_${uniqueId}"
                            value="${formattedDate}"
                            data-target="#otherPaymentDate_${uniqueId}" placeholder="dd-mm-YYYY"  />
                        <div class="input-group-append" data-target="#otherPaymentDate_${uniqueId}" data-toggle="datetimepicker">
                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                <input type="text" class="form-control b2b_monthly_rent"
                    id="payment_amount_${uniqueId}"
                    name="payment_detail[${unit.id}][${payIndex}][payment_amount]"
                    value="${pay.payment_amount ?? ''}"
                    placeholder="Payment Amount" />
                </div>
            </div>
             <div class="form-group row extra-fields" id="extra_fields_${uniqueId}">
                <div class="mb-2  font-weight-bold text-info"></div>
                    <div class="col-md-3 bank ml-3" id="bank_${uniqueId}">
                        <label>Bank Name</label>
                        <select class="form-control " name="payment_detail[${unit.id}][${payIndex}][bank_id]" id="bank_name_${uniqueId}">
                            <option value="">Select bank</option>
                            @foreach ($banks as $bank)
                                <option value="{{ $bank->id }}" ${pay.bank_id == {{ $bank->id }} ? 'selected' : ''}>
                                    {{ $bank->bank_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 chq" id="chq_${uniqueId}">
                        <label>Cheque No</label>
                        <input type="number" min="0" pattern="\d{6,10}"
                        maxlength="10"
                        title="Cheque number must be 6–10 digits" class="form-control" id="cheque_no_${uniqueId}"
                            name="payment_detail[${unit.id}][${payIndex}][cheque_number]"
                            value="${pay.cheque_number ?? ''}"
                            placeholder="Cheque No">
                            @error('cheque_number')
                                <span class="invalid-feedback d-block">
                                    {{ $message }}
                                </span>
                            @enderror
                    </div>
                </div>
        `;
        });

        const accordionHTML = `
                <div class="card card-info accordion-for-unit mb-3" data-unit-id="${unit.id}">
                    <div class="card-header d-flex justify-content-between">
                        <div class="card-title">
                            <a class="w-100 text-white" data-toggle="collapse" href="#${collapseId}" aria-expanded="true">
                                Unit No : ${unitName}
                            </a>
                        </div>
                    </div>
                    <div id="${collapseId}" class="collapse show" data-parent="#accordion">
                        <div class="card-body bg-light">
                            ${installmentBlocks}
                        </div>
                    </div>
                </div>
            `;

        let accordion = document.querySelector('#accordion');
        if (!accordion) {
            accordion = document.createElement('div');
            accordion.id = 'accordion';
            containerPayment.appendChild(accordion);
        }

        // accordion.innerHTML += accordionHTML;
        accordion.insertAdjacentHTML('beforeend', accordionHTML);

        // console.log('termDATE', termDate);

        // Initialize datetimepickers, event listeners





    }
    // $(document).on('click', '.delete-row-edit-dfb2b', function() {
    //     const row = $(this).closest('.form-group.row.mb-2');
    //     const extra = row.next('.form-group.row.extra-fields');


    //     Swal.fire({
    //         title: 'Are you sure?',
    //         text: "You are about to delete this payment.",
    //         icon: 'warning',
    //         showCancelButton: true,
    //         confirmButtonColor: '#3085d6',
    //         cancelButtonColor: '#d33',
    //         confirmButtonText: 'Yes, delete it!',
    //         cancelButtonText: 'Cancel'
    //     }).then((result) => {
    //         if (result.isConfirmed) {
    //             row.remove();
    //             extra.remove();
    //             toastr.success('Installment deleted successfully.');
    //         }
    //     });
    // });
</script>
