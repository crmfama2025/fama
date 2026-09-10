<script>
    window.leadHasTenant = @json(!is_null($leadTenantId ?? null));
    $(document).ready(function() {
        @if (request()->has('lead_id'))
            createLeadFromRequest({{ request('lead_id') }}, {{ $leadTenantId ?? 'null' }});
        @endif
    });

    function createLeadFromRequest(leadId, leadTenantId) {
        console.log('Lead ID:', leadId, 'Existing Tenant ID:', leadTenantId);
        console.log('Lead ID:', leadId);
        $('#typeB2B').prop('checked', true);
        onBusinessTypeChange();
        if (leadTenantId) {
            lockExistingCustomerForLead(leadTenantId);
        } else {
            $('#existingCustomerToggleWrap').hide();

        }

    }

    function lockExistingCustomerForLead(tenantId) {
        document.getElementById('existingCustomerToggleWrap').style.display = '';

        const checkbox = document.getElementById('existingCustomerCheck');
        checkbox.checked = true;
        onExistingCustomerToggle(); // shows the panel, hides+disables the new-tenant fields

        populateExistingCustomerSelect();
        const select = document.getElementById('existingCustomerSelect');
        select.value = tenantId;
        onExistingCustomerSelected(select); // sets hidden existing_customer_id, hides B2B doc section

        if (typeof $ !== 'undefined' && $.fn.select2) {
            $(select).val(tenantId).trigger('change.select2');
        }

        // Lock it — this tenant came from a converted lead, it isn't a choice here
        select.disabled = true;
        $(select).prop('disabled', true);
        checkbox.disabled = true;
        document.querySelector('#existingCustomerToggleWrap .existing-customer-toggle')
            ?.classList.add('locked');
        document.getElementById('clearExistingWrap').style.display = 'none';

        if (!document.getElementById('leadLockedNote')) {
            $('#existingCustomerPanel').prepend(
                '<div id="leadLockedNote" class="text-muted small mb-2">' +
                '<i class="fas fa-lock mr-1"></i> This tenant was already created from this lead and can\'t be changed here.' +
                '</div>'
            );
        }
    }
</script>
