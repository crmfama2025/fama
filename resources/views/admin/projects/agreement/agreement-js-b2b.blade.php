<script>
    function dfB2bTotalrentChange() {
        // alert('Total Rent Per Annum changed');
        let totalRentAnnum = parseFloat(document.getElementById('total_rent_annum').value) || 0;
        $('#total_rent_per_annum').text(totalRentAnnum);
        validateInstallmentsTotal()
    }

    function calculateTotalInstallments() {
        let total = 0;

        $('.b2b_monthly_rent').each(function() {
            let val = parseFloat($(this).val());
            if (!isNaN(val)) {
                total += val;
            }
        });

        return total;
    }


    function validateInstallmentsTotal() {
        // alert("testr");
        console.log('Validating installments total...');
        let totalInstallments = calculateTotalInstallments();
        let rentPerAnnum = parseFloat($('#total_rent_annum').val());
        console.log('Total Installments:', totalInstallments, 'Rent Per Annum:', rentPerAnnum);

        if (totalInstallments !== rentPerAnnum) {
            $('#installment_error').remove();
            $('#accordion').before(`
            <div id="installment_error" class="alert alert-default-danger">
                Total installments (${totalInstallments}) must equal Rent Per Annum (${rentPerAnnum})
            </div>
        `);
            $('#submitBtn').prop('disabled', true);
            return false;
        }

        $('#installment_error').remove();
        $('#submitBtn').prop('disabled', false);
        return true;
    }
    $(document).on('input change', '.b2b_monthly_rent', function() {
        if (selectedContract.contract_type_id === 1 && selectedContract?.contract_unit
            ?.business_type == 1) {
            validateInstallmentsTotal();
        }
    });

    function getallowedcount(count) {
        let duration = $("#duration_months").val();
        if (duration < count) {
            count = duration;
        }
        return count;
    }

    function dfB2cTotalrentChange() {
        let totalRentAnnum = parseFloat(document.getElementById('total_rent_annum').value) || 0;
        $('#total_rent_per_annum').text(totalRentAnnum);
        validateTotalPayment();
    }
</script>
