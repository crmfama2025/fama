<script>
    // ================================================================
    // MAIN PAGE ALLOCATION PANEL
    // ================================================================
    let apReceivables = []; // fetched pending receivables for tenant
    let apTenantId = null;
    let apTenantName = '';
    let apAllocatedIds = []; // IDs that will actually be touched
    let apAllocatedAmounts = {};
    let apCompanyId = null;

    function fmtAED(n) {
        return 'AED ' + parseFloat(n).toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    // ── When tenant dropdown changes ──────────────────────────────────
    // $(document).on('change', '#tenantSelectAllocation', function() {
    // $(document).on('change', '#companySelectAllocation', function() {
    //     // apTenantId = $(this).val();
    //     apCompanyId = $(this).val();
    //     // apTenantName = $(this).find('option:selected').text().trim();
    //     apCompanyName = $(this).find('option:selected').text().trim();

    //     apFromDate = $('#fromDateAllocation').val();
    //     apToDate = $('#toDateAllocation').val();
    //     if (!apFromDate || !apToDate) {
    //         toastr.error('Please select both From Date and To Date.');
    //         return;
    //     }

    //     apReceivables = [];
    //     $('#ap_rows').html('');
    //     $('#ap_summary').hide();
    //     $('#ap_banner').hide();
    //     $('#ap_amount_input').val('');
    //     $('#ap_covered_summary').hide();
    //     $('#ap_submit_btn').prop('disabled', true);

    //     // if (!apTenantId) {
    //     //     $('#allocationCard').hide();
    //     //     $('#ap_tenant_name').text('—');
    //     //     return;
    //     // }
    //     if (!apCompanyId) {
    //         $('#allocationCard').hide();
    //         return;
    //     }

    //     // $('#ap_tenant_name').text(apTenantName);
    //     // $('#allocationCard').show();
    //     showLoader();
    //     // fetch pending receivables for this tenant
    //     $.get("{{ route('tenant.pending.receivables') }}", {
    //             // tenant_id: apTenantId,
    //             from_date: apFromDate,
    //             to_date: apToDate,
    //             company_id: apCompanyId
    //         },
    //         function(res) {
    //             // alert("test");
    //             hideLoader();
    //             console.log('Received receivables:', res.data);
    //             apReceivables = res.data || [];

    //             if (apReceivables.length === 0) {
    //                 toastr.info('No pending receivables found for this tenant.');
    //                 $('#ap_submit_btn').prop('disabled', true);
    //                 $('#ap_rows').html('');
    //                 $('#ap_summary').hide();
    //                 $('#ap_banner').hide();
    //                 $('#ap_covered_summary').hide();
    //                 $('#allocationCard').hide();
    //                 return;
    //             }
    //             $('#ap_tenant_name').text(apTenantName);
    //             $('#ap_company_name').text(apCompanyName);
    //             $('#allocationCard').show();
    //             // auto-run if amount already typed
    //             if ($('#ap_amount_input').val()) runApAllocation();
    //         }
    //     );
    //     // AFTER (client-side filter):
    //     // apReceivables = allReceivables.filter(r => r.tenant_id == apTenantId);
    //     // hideLoader();

    //     // if (apReceivables.length === 0) {
    //     //     toastr.info('No pending receivables found for this tenant.');
    //     //     $('#ap_submit_btn').prop('disabled', true);
    //     //     $('#ap_rows').html('');
    //     //     $('#ap_summary').hide();
    //     //     $('#ap_banner').hide();
    //     //     $('#ap_tenant_name').text('');
    //     //     $('#allocationCard').hide();
    //     //     return;
    //     // }

    //     // $('#ap_tenant_name').text(apTenantName);
    //     // $('#allocationCard').show();
    //     // if ($('#ap_amount_input').val()) runApAllocation();
    //     console.log('ap', apReceivables);
    // });

    // $(document).on('change.datetimepicker', '#fromDateAllocation, #toDateAllocation', function() {
    $('#fromDateAllocationPicker, #toDateAllocationPicker').on('change.datetimepicker', function(e) {
        // if (apTenantId) {
        //     $('#tenantSelectAllocation').trigger('change');
        // }
        // alert(apCompanyId);
        // alert("test");
        const fromDate = $('#fromDateAllocationPicker').datetimepicker('date');
        const toDate = $('#toDateAllocationPicker').datetimepicker('date');

        // check if both dates are selected and if toDate is before fromDate
        if (fromDate && toDate && toDate.isBefore(fromDate, 'day')) {
            toastr.error('To Date must be greater than or equal to From Date.');
            $('#toDateAllocationPicker').datetimepicker('clear');
            return;
        }

        // console.log('apCompanyId', apCompanyId);
        // if (fromDate && toDate && apCompanyId) {
        //     $('#companySelectAllocation').trigger('change');
        // if (parseFloat($('#ap_amount_input').val()) > 0) {
        //     runApAllocation();
        // }
        // }
    });

    function loadCompanyAllocation() {
        apCompanyId = $('#companySelectAllocation').val();
        apCompanyName = $('#companySelectAllocation option:selected').text().trim();

        apFromDate = $('#fromDateAllocation').val();
        apToDate = $('#toDateAllocation').val();

        if (!apFromDate || !apToDate) {
            toastr.error('Please select both From Date and To Date.');
            return;
        }

        if (!apCompanyId) {
            toastr.error('Please select a Company.');
            return;
        }

        apReceivables = [];
        $('#ap_rows').html('');
        $('#ap_summary').hide();
        $('#ap_banner').hide();
        $('#ap_amount_input').val('');
        $('#ap_covered_summary').hide();
        $('#ap_submit_btn').prop('disabled', true);

        // if (!apTenantId) {
        //     $('#allocationCard').hide();
        //     $('#ap_tenant_name').text('—');
        //     return;
        // }
        if (!apCompanyId) {
            $('#allocationCard').hide();
            return;
        }

        // $('#ap_tenant_name').text(apTenantName);
        // $('#allocationCard').show();
        showLoader();
        // fetch pending receivables for this tenant
        $.get("{{ route('tenant.pending.receivables') }}", {
                // tenant_id: apTenantId,
                from_date: apFromDate,
                to_date: apToDate,
                company_id: apCompanyId
            },
            function(res) {
                // alert("test");
                hideLoader();
                console.log('Received receivables:', res.data);
                apReceivables = res.data || [];

                if (apReceivables.length === 0) {
                    toastr.info('No pending receivables found for this company.');
                    $('#ap_submit_btn').prop('disabled', true);
                    $('#ap_rows').html('');
                    $('#ap_summary').hide();
                    $('#ap_banner').hide();
                    $('#ap_covered_summary').hide();
                    $('#allocationCard').hide();
                    return;
                }
                $('#ap_tenant_name').text(apTenantName);
                $('#ap_company_name').text(apCompanyName);
                $('#allocationCard').show();
                // auto-run if amount already typed
                if ($('#ap_amount_input').val()) runApAllocation();
            }
        );
    }

    $(document).on('click', '#runAllocationBtn', function() {
        loadCompanyAllocation();
    });

    // ── When amount changes ───────────────────────────────────────────
    $('#ap_amount_input').on('input', function() {
        runApAllocation();
    });

    // ── Core allocation logic ─────────────────────────────────────────
    // function runApAllocation() {
    //     const entered = parseFloat($('#ap_amount_input').val()) || 0;
    //     apAllocatedIds = [];

    //     if (apReceivables.length === 0 || entered <= 0) {
    //         $('#ap_summary').hide();
    //         $('#ap_rows').html('');
    //         $('#ap_banner').hide();
    //         $('#ap_submit_btn').prop('disabled', true);
    //         return;
    //     }

    //     let pool = entered;
    //     let allocated = 0;
    //     const total = apReceivables.reduce((s, r) => s + r.amount, 0);
    //     let html = '';

    //     apReceivables.forEach(function(r) {
    //         const covered = Math.min(pool, r.amount);
    //         const pct = r.amount > 0 ? Math.round((covered / r.amount) * 100) : 0;
    //         const remaining = r.amount - covered;
    //         pool = Math.max(0, pool - r.amount);
    //         allocated += covered;

    //         // track which IDs are at least partially covered
    //         if (covered > 0) apAllocatedIds.push(r.id);

    //         const state = covered === 0 ? 'none' :
    //             covered === r.amount ? 'full' : 'partial';
    //         const badgeClass = state === 'full' ? 'badge-success' :
    //             state === 'partial' ? 'badge-warning' :
    //             'badge-secondary';
    //         const badgeLabel = state === 'full' ? 'Covered' :
    //             state === 'partial' ? 'Partial' :
    //             'Pending';
    //         const barColor = state === 'full' ? '#28a745' :
    //             state === 'partial' ? '#ffc107' :
    //             '#dee2e6';
    //         const cardStyle = state === 'full' ?
    //             'background:#f0fff4;border:1px solid #28a745;' :
    //             state === 'partial' ?
    //             'background:#fffbea;border:1px solid #ffc107;' :
    //             'border:1px solid #dee2e6;opacity:.55;';

    //         html += `
    //     <div class="card mb-2" style="${cardStyle}border-radius:6px;">
    //         <div class="card-body py-2 px-3">
    //             <div class="d-flex justify-content-between align-items-start">
    //                 <div>
    //                     <strong style="font-size:13px;">${r.label}</strong>
    //                     <div style="font-size:11px;color:#6c757d;">
    //                         ${r.date} &middot; ${r.mode} &middot; ${r.property}
    //                     </div>
    //                 </div>
    //                 <div class="text-right">
    //                     <span class="badge ${badgeClass}">${badgeLabel}</span>
    //                     <div style="font-size:13px;font-weight:600;margin-top:3px;">
    //                         ${fmtAED(r.amount)}
    //                     </div>
    //                     ${state === 'partial' ? `
    //                     <div style="font-size:11px;color:#28a745;">
    //                         Covered: ${fmtAED(covered)}
    //                     </div>
    //                     <div style="font-size:11px;color:#856404;">
    //                         Remaining: ${fmtAED(remaining)}
    //                     </div>` : ''}
    //                 </div>
    //             </div>
    //             <div class="progress mt-2" style="height:5px;border-radius:99px;">
    //                 <div class="progress-bar"
    //                      style="width:${pct}%;background:${barColor};
    //                             transition:width .3s ease;">
    //                 </div>
    //             </div>
    //         </div>
    //     </div>`;
    //     });

    //     $('#ap_rows').html(html);
    //     $('#ap_summary').show();
    //     $('#ap_total').text(fmtAED(total));
    //     $('#ap_allocated').text(fmtAED(allocated));

    //     const surplus = entered - allocated;
    //     const short = total - allocated;
    //     const balEl = $('#ap_balance');
    //     const banner = $('#ap_banner');

    //     if (surplus > 0) {
    //         // paid more than all outstanding
    //         balEl.text(fmtAED(surplus)).css('color', '#fd7e14');
    //         banner.removeClass()
    //             .addClass('alert alert-warning py-2')
    //             .html(`<i class="fa fa-exclamation-triangle mr-1"></i>
    //                  <strong>${fmtAED(surplus)}</strong>
    //                  surplus — exceeds all outstanding receivables.`)
    //             .show();
    //     } else if (short > 0) {
    //         // amount doesn't cover everything
    //         balEl.text(fmtAED(short)).css('color', '#dc3545');
    //         banner.removeClass()
    //             .addClass('alert alert-info py-2')
    //             .html(`<i class="fa fa-info-circle mr-1"></i>
    //                  <strong>${fmtAED(short)}</strong>
    //                  still outstanding after this payment.`)
    //             .show();
    //     } else {
    //         balEl.text(fmtAED(0)).css('color', '#28a745');
    //         banner.hide();
    //     }

    //     // enable submit only if something is allocated
    //     $('#ap_submit_btn').prop('disabled', allocated <= 0);
    // }

    function runApAllocation() {

        // $('#ap_tenant_name').text(apTenantName);
        // $('#allocationCard').show();
        const entered = parseFloat($('#ap_amount_input').val()) || 0;
        apAllocatedIds = [];
        apAllocatedAmounts = {};

        if (apReceivables.length === 0 || entered <= 0) {
            $('#ap_summary').hide();
            $('#ap_rows').html('');
            $('#ap_banner').hide();
            $('#ap_covered_summary').hide();
            $('#ap_submit_btn').prop('disabled', true);
            return;
        }


        let pool = entered;
        let allocated = 0;
        const total = apReceivables.reduce((s, r) => s + r.amount, 0);
        let html = '';

        apReceivables.forEach(function(r) {

            // once pool is empty, stop rendering rows
            if (pool <= 0) return;

            const covered = Math.min(pool, r.amount);
            // const pct = r.amount > 0 ? Math.round((covered / r.amount) * 100) : 0;
            const remaining = r.amount - covered;
            pool = Math.max(0, pool - r.amount);
            allocated += covered;

            // if (covered > 0) apAllocatedIds.push(r.id);
            if (covered > 0) {
                apAllocatedIds.push(r.id);
                apAllocatedAmounts[r.id] = parseFloat(covered.toFixed(2));
            }
            // console.log('Allocating:', {
            //     id: r.id,
            //     amount: r.amount,
            //     covered: covered,
            //     remaining: remaining,
            //     pool: pool
            // });

            const state = covered.toFixed(2) === r.amount.toFixed(2) ? 'full' : 'partial';
            const badgeClass = state === 'full' ? 'badge-success' : 'badge-warning';
            const badgeLabel = state === 'full' ? 'Covered' : 'Partial';
            const barColor = state === 'full' ? '#28a745' : '#ffc107';
            const cardStyle = state === 'full' ?
                'border:1px solid #28a745;' :
                'border:1px solid #ffc107;';

            html += `
            <div class="card mb-2" style="${cardStyle}border-radius:6px;">
                <div class="card-body py-2 px-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <strong style="font-size:13px; color:#d63384; background:#fff0f6; padding:1px 6px; border-radius:4px;">${r.company_name}</strong> |
                            <strong style="font-size:13px;">P-${r.project_number}</strong> |
                             <strong style="font-size:13px;"> Unit - ${r.unit_number}</strong> |
                            ${r.subunit_number && r.subunit_number !== '-'
                                ? ` | Subunit - <strong style="font-size:13px;">${r.subunit_number}</strong>`
                                : ''}
                            <strong style="font-size:13px;">${r.label}</strong>

                            <div style="font-size:11px;color:#6c757d;">
                                ${r.date} &middot; ${r.mode} &middot; ${r.property}
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="badge ${badgeClass}">${badgeLabel}</span>
                            <div style="font-size:13px;font-weight:600;margin-top:3px;">
                                ${fmtAED(r.amount)}
                            </div>
                            ${state === 'partial' ? `
                            <div style="font-size:11px;color:#28a745;font-weight:600;">
                                Covered: ${fmtAED(covered)}
                            </div>
                            <div style="font-size:11px;color:#856404;font-weight:600;">
                                Remaining: ${fmtAED(remaining)}
                            </div>` : ''}
                        </div>
                    </div>

                </div>
            </div>`;


        });

        $('#ap_rows').html(html);
        $('#ap_summary').show();
        $('#ap_total').text(fmtAED(total));
        $('#ap_allocated').text(fmtAED(allocated));
        $('#ap_total_covered').text(fmtAED(allocated));
        $('#ap_covered_summary').show();

        const surplus = entered - allocated;
        const short = total - allocated;
        const balEl = $('#ap_balance');
        const banner = $('#ap_banner');

        // if (surplus > 0) {
        //     balEl.text(fmtAED(surplus)).css('color', '#fd7e14');
        //     banner.removeClass()
        //         .addClass('alert alert-warning py-2')
        //         .html(`<i class="fa fa-exclamation-triangle mr-1"></i>
        //              <strong>${fmtAED(surplus)}</strong>
        //              surplus — exceeds all outstanding receivables.`)
        //         .show();
        // } else if (short > 0) {
        //     balEl.text(fmtAED(short)).css('color', '#dc3545');
        //     banner.removeClass()
        //         .addClass('alert alert-info py-2')
        //         .html(`<i class="fa fa-info-circle mr-1"></i>
        //              <strong>${fmtAED(short)}</strong>
        //              still outstanding after this payment.`)
        //         .show();
        // } else {
        //     balEl.text(fmtAED(0)).css('color', '#28a745');
        //     banner.hide();
        // }

        $('#ap_submit_btn').prop('disabled', allocated <= 0);
    }

    // ── Submit button → open confirmation modal ───────────────────────
    $('#ap_submit_btn').on('click', function() {
        const entered = parseFloat($('#ap_amount_input').val()) || 0;
        // if (!apTenantId || entered <= 0 || apAllocatedIds.length === 0) return;
        if (!apCompanyId || entered <= 0 || apAllocatedIds.length === 0) return;

        console.log('Submitting allocation:', {
            company_id: apCompanyId,
            tenant_name: apTenantName,
            entered_amount: entered,
            allocated_ids: apAllocatedIds,
            allocated_amounts: apAllocatedAmounts
        });

        // populate confirmation modal summary
        $('#conf_tenant').text(apTenantName);
        $('#conf_company').text(apCompanyName);
        $('#conf_amount').text(fmtAED(entered));
        $('#ap_allocated_amount').val(entered);
        $('#conf_count').text(apAllocatedIds.length + ' receivable(s)');

        // reset form fields in confirm modal
        $('.ap_payment_mode').prop('checked', false);
        $('#ap_bank_div').hide();
        $('#ap_cheque_div').hide();
        $('#ap_bank_id').html('<option value="">Select Bank</option>');
        $('#ap_cheque_no').val('');
        $('#ap_remarks').val('');
        $('#ap_clearing_date_input').val('');
        $('#ap_company_id').val(apCompanyId).trigger('change');

        $('#modal-ap-confirm').modal('show');
    });

    // ── Payment type toggle inside confirm modal ──────────────────────
    $(document).on('change', '.ap_payment_mode', function() {
        $('.ap_payment_mode').not(this).prop('checked', false);
        const mode = parseInt($(this).val());

        $('#ap_bank_div').hide();
        $('#ap_cheque_div').hide();
        $('.receiptDiv').hide();

        // Remove required
        $('#ap_company_id').prop('required', false);
        $('#ap_bank_id').prop('required', false);
        $('#ap_cheque_no').prop('required', false);
        $('#receipt_allocation').prop('required', false);
        $('#ap_reference').prop('required', false);

        if (mode === 3) { // cheque
            $('#ap_bank_div').show();
            $('#ap_cheque_div').show();
            $('.receiptDiv').show();

            $('#ap_company_id').prop('required', true);
            $('#ap_bank_id').prop('required', true);
            $('#ap_cheque_no').prop('required', true);
            $('#receipt_allocation').prop('required', true);
            $('#ap_reference').prop('required', true);
            addClassAsterisk('#ap_bank_id');
            addClassAsterisk('#receipt_allocation');
        } else if (mode === 2) { // bank transfer
            $('#ap_bank_div').show();
            $('.receiptDiv').show();

            $('#ap_company_id').prop('required', true);
            $('#ap_bank_id').prop('required', true);
            $('#receipt_allocation').prop('required', true);
            $('#ap_reference').prop('required', true);
            addClassAsterisk('#ap_bank_id');
            // addClassAsterisk('#ap_reference');
            addClassAsterisk('#ap_clearing_date_input');
            addClassAsterisk('#receipt_allocation');
            $('#ap_cheque_no').val('');

        } else if (mode === 1) {
            $('#ap_bank_div').hide();
            $('#ap_cheque_div').hide();
            $('.receiptDiv').hide();
            $('#receipt_allocation').val('');
            $('#ap_reference').val('');
            $('#ap_company_id').val('').trigger('change');
            $('#ap_bank_id').val('').trigger('change');
            $('#ap_cheque_no').val('');

        }
    });

    // ── Company → filter banks ────────────────────────────────────────
    $('#ap_company_id').on('change', function() {
        const companyId = $(this).val();
        const bankSelect = $('#ap_bank_id');
        bankSelect.html('<option value="">Select Bank</option>');
        if (!companyId) return;

        const filtered = banks.filter(b => b.company_id == companyId);
        filtered.forEach(b => {
            bankSelect.append(`<option value="${b.id}">${b.bank_name}</option>`);
        });
    });

    // ── Confirm & Clear ───────────────────────────────────────────────
    $('#ap_confirm_btn').on('click', function() {
        // alert("test");
        const clearingDate = $('#ap_clearing_date_input').val();
        if (!clearingDate) {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'warning',
                title: 'Please select a clearing date.',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
            return;
        }
        const mode = $('.ap_payment_mode:checked').val();

        if (!mode) {
            toastr.error('Please select a payment type.');
            return;
        }

        if ((mode == 2 || mode == 3) && !$('#ap_company_id').val()) {
            toastr.error('Please select a company.');
            return;
        }

        if ((mode == 2 || mode == 3) && !$('#receipt_allocation').val()) {
            toastr.error('Please select a receipt allocation.');
            return;
        }

        if ((mode == 2 || mode == 3) && !$('#ap_reference').val()) {
            toastr.error('Please enter a bank reference.');
            return;
        }

        if ((mode == 2 || mode == 3) && !$('#ap_bank_id').val()) {
            toastr.error('Please select a bank.');
            return;
        }

        if (mode == 3 && !$('#ap_cheque_no').val()) {
            toastr.error('Please enter the cheque number.');
            return;
        }


        const formData = new FormData();
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
        formData.append('paid_date', clearingDate);
        formData.append('paid_amount', $('#ap_amount_input').val());
        formData.append('tenant_id', apTenantId);
        formData.append('paid_mode_id', $('.ap_payment_mode:checked').val() || '');
        formData.append('paid_company_id', $('#ap_company_id').val());
        formData.append('paid_bank_id', $('#ap_bank_id').val());
        formData.append('paid_cheque_number', $('#ap_cheque_no').val());
        formData.append('payment_remarks', $('#ap_remarks').val());
        formData.append('allocated_amount', $('#ap_allocated_amount').val());
        formData.append('receipt_allocation_id', $('#receipt_allocation').val());
        formData.append('bank_reference', $('#ap_reference').val());
        formData.append('have_other_adjustments', $('#other_adjustments').is(':checked') ? 1 : 0);

        // attach all touched receivable IDs
        apAllocatedIds.forEach(id => {
            formData.append('payment_detail_ids[]', id);
        });

        // Send per-ID allocated amounts to backend
        Object.entries(apAllocatedAmounts).forEach(([id, amount]) => {
            formData.append('allocated_amounts[' + id + ']', amount);
        });
        showLoader();
        $.ajax({
            url: "{{ route('receivable.cheque.clear.submit') }}",
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                $('#ap_confirm_btn').prop('disabled', true).text('Processing...');
            },
            success: function(response) {
                $('#ap_confirm_btn').prop('disabled', false)
                    .html('<i class="fa fa-check mr-1"></i> Confirm & Clear');
                if (response.success) {
                    $('#modal-ap-confirm').modal('hide');
                    toastr.success(response.message);
                    // reset allocation panel
                    apReceivables = [];
                    apAllocatedIds = [];
                    $('#ap_amount_input').val('');
                    $('#ap_rows').html('');
                    $('#ap_allocated_amount').val(0);
                    $('#ap_summary').hide();
                    $('#ap_banner').hide();
                    $('#ap_submit_btn').prop('disabled', true);
                    $('#allocationCard').hide();
                    $('#tenantSelectAllocation').val(null).trigger('change.select2');
                    $('#companySelectAllocation').val(null).trigger('change.select2');
                    $('#modal-ap-confirm').find('select').val(null).trigger('change');

                    $.get("{{ route('tenant.pending.receivables.all') }}", function(res) {
                        allReceivables = res.data || [];
                        console.log('Receivables refreshed:', allReceivables.length);
                    });
                    // reload datatable
                    table.ajax.reload(null, false);

                    hideLoader();


                } else {
                    toastr.error(response.message || 'Something went wrong!');
                }
            },
            error: function(xhr) {
                $('#ap_confirm_btn').prop('disabled', false)
                    .html('<i class="fa fa-check mr-1"></i> Confirm & Clear');
                toastr.error(xhr.statusText || 'Server error');
            }
        });
    });

    // ── Datetimepicker for confirm modal ─────────────────────────────
    $('#ap_clearingDate').datetimepicker({
        format: 'DD-MM-YYYY'
    });
</script>
