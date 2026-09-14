<script>
    $('#investor_prefix').on('change', function() {
        let prefix = $(this).val();
        let arabicPrefix = '';

        if (prefix === 'Mr') {
            arabicPrefix = 'السيد';
        } else if (prefix === 'Ms') {
            arabicPrefix = 'السيدة';
        }

        $('#investor_prefix_arabic').val(arabicPrefix);
        $('#investor_prefix_arabic_hidden').val(arabicPrefix);
    });
</script>
