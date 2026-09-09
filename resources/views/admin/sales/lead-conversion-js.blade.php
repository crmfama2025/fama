<script>
    $(document).ready(function() {
        @if (request()->has('lead_id'))
            createLeadFromRequest({{ request('lead_id') }});
        @endif
    });

    function createLeadFromRequest(leadId) {
        // Your logic here
        console.log('Lead ID:', leadId);
        $('#typeB2B').prop('checked', true);
        onBusinessTypeChange();
        $('#existingCustomerToggleWrap').hide();

    }
</script>
