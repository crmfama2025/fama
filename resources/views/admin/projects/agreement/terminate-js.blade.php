<script>
    let remainingReceivables = [];
    let remainingTotal = 0;
    let termDate = '';

    function checkTerminatedAgreement(contractId) {
        // alert("test");
        $.get(`/contracts/${contractId}/terminated-agreement-details`, function(data) {
            if (!data) return;
            // alert("test");

            let terminated = data.terminated_agreement;
            console.log(terminated.terminated_date);
            let formattedDate = moment(terminated.terminated_date, "YYYY-MM-DD").format("DD-MM-YYYY");
            console.log('formated date', formattedDate)
            let contractEnd = new Date(data.contract_end_date);
            let today = new Date();

            if (terminated) {

                termDate = moment(terminated.terminated_date, "YYYY-MM-DD");
                ctenddate = moment(data.end_date, "YYYY-MM-DD");
                console.log('termdate', termDate);

                // if (termDate < today && contractEnd < today) {
                //     return;
                // }

                $('#start_date').prop('disabled', false).prop('readonly', false);
                $('#startdate').datetimepicker('minDate', termDate);
                $('#startdate').datetimepicker('date', termDate);

                $('#end_date').prop('disabled', false).prop('readonly', false);
                let endVal = $('#end_date').val();
                if (endVal) {
                    let enddate = moment(endVal, "DD-MM-YYYY");
                    console.log("ENDDATE", enddate);
                    if (enddate.isValid()) {
                        $('#enddate').datetimepicker('maxDate', enddate);
                        $('#enddate').datetimepicker('date', enddate);
                    }
                }
                $("#duration_months").val(data.remaining_installments);
                $('#no_of_installments option').each(function() {
                    const optionText = $(this).text().trim();
                    const countText = data.remaining_installments.toString().trim();
                    if (optionText === countText) {
                        $(this).prop('selected', true);
                        $('#no_of_installments').on('select2:opening', function(e) {
                            e.preventDefault();
                        });
                        $('#no_of_installments').trigger('change');
                    }
                });
                remainingReceivables = data.remaining_receivables;
                console.log("remaining", remainingReceivables);
                remainingTotal = data.remainingTotal.toFixed(2);




            } else {
                return;
            }
        });
    }


    function checkTermination(subunitId, unitId, contractId) {
        $.ajax({
            url: `/contracts/${contractId}/check-agreement`,
            type: 'GET',
            data: {
                unit_id: unitId,
                subunit_id: subunitId
            },
            success: function(response) {
                // alert("success");
                console.log("defaultduration", defaultStart);
                console.log('defaults', defaultStart, defaultEnd, defaultDuration)


                // Only override if there is a terminated agreement
                if (response.exists && response.remaining_receivables.length > 0) {
                    // alert("exists");

                    remainingReceivables = response.remaining_receivables;
                    remainingInstallments = response.remaining_installments;

                    const startDate = moment(remainingReceivables[0].receivable_date, "DD-MM-YYYY");
                    const endDate = moment(remainingReceivables[remainingReceivables.length - 1]
                        .receivable_date, "DD-MM-YYYY");

                    $('#start_date').prop('disabled', false).prop('readonly', false);
                    $('#startdate').datetimepicker('minDate', startDate);
                    $('#startdate').datetimepicker('date', startDate);

                    $('#end_date').prop('disabled', false).prop('readonly', false);
                    $('#enddate').datetimepicker('maxDate', endDate);
                    $('#enddate').datetimepicker('date', endDate);

                    $("#duration_months").val(remainingInstallments);
                    $('#no_of_installments option').each(function() {
                        const optionText = $(this).text().trim();
                        if (optionText === remainingInstallments.toString()) {
                            $(this).prop('selected', true);
                            $('#no_of_installments').trigger('change');
                        }
                    });
                    rent_per_month = $('.rent_per_month').val();
                    calculatepaymentamount(rent_per_month, remainingInstallments);
                    // remainingReceivables = response.remaining_receivables;

                } else {

                    // alert("exists");

                    remainingReceivables = null;
                    console.log('defaultsnon', defaultStart, defaultEnd, defaultDuration)
                    if (defaultStart && defaultStart.isValid()) {
                        $('#startdate').datetimepicker('date', defaultStart);
                        $('#start_date').val(defaultStart.format('DD-MM-YYYY')).prop('readonly', true);
                    }

                    if (defaultEnd && defaultEnd.isValid()) {
                        $('#enddate').datetimepicker('date', defaultEnd);
                        $('#end_date').val(defaultEnd.format('DD-MM-YYYY')).prop('readonly', true);
                    }

                    $(
                        "#duration_months").val(defaultDuration);

                    // $('#no_of_installments option').each(function() {
                    //     if ($(this).text().trim() === defaultDuration.toString()) {
                    //         $(this).prop('readonly', true);
                    //         $(this).prop('selected', true).trigger('change');
                    //         // $(this).next('.select2-container')
                    //         //     .find('.select2-selection')
                    //         //     .addClass('readonly');
                    //     }
                    // });
                }
                // Else: do nothing, keep the contract-level dates/duration
            },
            error: function(xhr, status, error) {
                console.error("Error checking agreement:", error);
            }
        });
    }
</script>
