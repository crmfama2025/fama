<script>
    let fifo_company_id, fifoCompanyName, fifo_month, fifo_investor, fifo_investorName, fifo_year, payment_amount_total;
    let fifoXhr = null;
    let fifoAllocatedIds = [];
    let fifoAllocatedAmounts = {};
    let fifoLast = null;

    const fifoTypeBadge = {
        1: {
            label: 'Profit',
            cls: 'badge-success'
        },
        2: {
            label: 'Commission',
            cls: 'badge-info'
        },
        6: {
            label: 'Withdrawal',
            cls: 'bg-orange'
        }
    };

    function fifoTypeHtml(r) {
        // type 6 on a terminated investment is a Settlement
        if (r.payout_type === 6 && r.terminate_status === 1) {
            return '<span class="badge bg-danger">Settlement</span>';
        }
        const t = fifoTypeBadge[r.payout_type];
        return t ? `<span class="badge ${t.cls}">${t.label}</span>` : '-';
    }

    $(document).on('click', '#runInvestorPayoutAllocationBtn', function() {
        loadInvestorCompanyAllocation();
    });

    function resetFifo() {
        fifoAllocatedIds = [];
        fifoAllocatedAmounts = {};
        fifoLast = null; // 
        $('#in_rows').html('');
        $('#in_banner, #in_covered_summary').hide();
        $('#payoutAllocationCard').hide();
        $('#in_submit_btn').prop('disabled', true);
    }

    function loadInvestorCompanyAllocation() {
        fifo_company_id = $('#fifo_company_id').val();
        fifoCompanyName = $('#fifo_company_id option:selected').text().trim();
        fifo_month = $('#fifo_month').val();
        fifo_investor = $('#fifo_investor_id').val();
        fifo_investorName = $('#fifo_investor_id option:selected').text().trim();
        fifo_year = $('#fifo_year').val();
        payment_amount_total = String($('#payment_amount_total').val()).replace(/,/g, '');

        if (!fifo_company_id) {
            toastr.error('Please select a Company.');
            return;
        }
        if (!fifo_investor) {
            toastr.error('Please select an Investor.');
            return;
        }
        if (!(parseFloat(payment_amount_total) > 0)) {
            toastr.error('Please type the amount to pay.');
            return;
        }

        resetFifo();
        if (fifoXhr) fifoXhr.abort();
        showLoader();

        fifoXhr = $.get("{{ route('investor.pending.payouts') }}", {
                month: fifo_month,
                year: fifo_year,
                company_id: fifo_company_id,
                investor_id: fifo_investor,
                amount: payment_amount_total,
            })
            .done(function(res) {
                renderFifo(res.data);
            })
            .fail(function(xhr, status) {
                if (status === 'abort') return; // keep the loader for the newer request
                resetFifo();
                if (xhr.status === 403) {
                    toastr.error('You are not authorized for this company.');
                } else if (xhr.status === 422) {
                    const errs = xhr.responseJSON?.errors || {};
                    toastr.error(Object.values(errs).flat().join('<br>') || 'Invalid input.');
                } else {
                    toastr.error('Something went wrong. Please try again.');
                }
            })
            .always(function(a, status) {
                if (status !== 'abort') hideLoader();
            });
    }

    function renderFifo(d) {
        const rows = d.allocations || [];
        const entered = parseFloat(d.requested) || 0;
        const allocated = parseFloat(d.allocated) || 0;
        const total = parseFloat(d.total_pending) || 0;
        const surplus = parseFloat(d.unallocated) || 0;

        if (rows.length === 0 || entered <= 0) {
            resetFifo();
            toastr.info('No pending payouts found for this investor and company.');
            return;
        }

        let html = '';
        fifoAllocatedIds = [];
        fifoAllocatedAmounts = {};

        rows.forEach(function(r) {
            const apply = parseFloat(r.apply);
            const due = parseFloat(r.due);
            const remaining = parseFloat(r.balance_after);
            const full = r.fully_paid;

            fifoAllocatedIds.push(r.id);
            fifoAllocatedAmounts[r.id] = apply;

            const badgeClass = full ? 'badge-success' : 'badge-warning';
            const badgeLabel = full ? 'Covered' : 'Partial';
            const cardStyle = full ? 'border:1px solid #28a745;' : 'border:1px solid #ffc107;';

            html += `
            <div class="card mb-2" style="${cardStyle}border-radius:6px;">
                <div class="card-body py-2 px-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <strong style="font-size:13px; color:#d63384; background:#fff0f6; padding:1px 6px; border-radius:4px;">
                                ${r.investment_code ?? '-'}
                            </strong> | <strong style="font-size:13px; padding:1px 6px;">
                               ${d.investor?.code ?? '-'} - ${d.investor?.name ?? '-'}
                            </strong> | ${fifoTypeHtml(r)}
                            <div style="font-size:11px;color:#6c757d;">${r.payout_date ?? '-'}</div>
                        </div>
                        <div class="text-right">
                            <span class="badge ${badgeClass}">${badgeLabel}</span>
                            <div style="font-size:13px;font-weight:600;margin-top:3px;">${fmtAED(due)}</div>
                            ${!full ? `
                            <div style="font-size:11px;color:#28a745;font-weight:600;">Covered: ${fmtAED(apply)}</div>
                            <div style="font-size:11px;color:#856404;font-weight:600;">Remaining: ${fmtAED(remaining)}</div>` : ''}
                        </div>
                    </div>
                </div>
            </div>`;
        });

        $('#in_rows').html(html);
        $('#payoutAllocationCard').show();
        $('#in_summary').show();
        $('#in_total').text(fmtAED(total));
        $('#in_allocated').text(fmtAED(allocated));
        $('#in_total_covered').text(fmtAED(allocated));
        $('#in_covered_summary').show();

        const banner = $('#in_banner');
        const short = total - allocated;

        const hasSurplus = surplus > 0;

        if (hasSurplus) {
            banner.removeClass().addClass('alert alert-danger py-2')
                .html(`<i class="fa fa-ban mr-1"></i>
               Amount exceeds total outstanding by <strong>${fmtAED(surplus)}</strong>.
               Reduce it to <strong>${fmtAED(total)}</strong> or less to submit.`).show();
        } else if (short > 0) {
            banner.removeClass().addClass('alert alert-info py-2')
                .html(`<i class="fa fa-info-circle mr-1"></i>
               <strong>${fmtAED(short)}</strong> still outstanding after this payment.`).show();
        } else {
            banner.hide();
        }

        fifoLast = {
            investor_id: fifo_investor,
            company_id: fifo_company_id,
            month: fifo_month || '',
            year: fifo_year || '',
            amount: allocated.toFixed(2)
        };

        $('#in_submit_btn').prop('disabled', allocated <= 0 || hasSurplus);
    }

    $('#fifo_company_id, #fifo_investor_id, #fifo_month, #fifo_year, #payment_amount_total')
        .on('change input', resetFifo);

    function fmtAED(n) {
        const v = parseFloat(n) || 0;
        return 'AED ' + v.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    // ── Submit button → open confirmation modal ───────────────────────
    $('#in_submit_btn').on('click', function() {
        if (!fifoLast || parseFloat(fifoLast.amount) <= 0 || fifoAllocatedIds.length === 0) return;

        $('#conf_tenant').text(fifo_investorName);
        $('#conf_company').text(fifoCompanyName);
        $('#conf_amount').text(fmtAED(fifoLast.amount));
        $('#conf_count').text(fifoAllocatedIds.length + ' payout(s)');

        $('.in_payment_mode').prop('checked', false);
        $('.in_banksingle, .in_companySingle, .in_cheque, .in_modechange').hide();
        $('#in_bank_name').html('<option value="">Select Bank</option>');
        $('#in_cheque_no, #in_payment_remarks, #in_clearing_date_input').val('');
        $('#modal-in-confirm .is-invalid').removeClass('is-invalid');
        $('#in_submitBtn').prop('disabled', false);
        $('#in_paid_company').val(fifo_company_id).trigger('change');

        $('#modal-in-confirm').modal('show');
    });

    $('#in_submitBtn').on('click', function() {
        if (!fifoLast) return;

        const date = $('#in_clearing_date_input').val().trim();
        const mode = $('.in_payment_mode:checked').val();
        const company = $('#in_paid_company').val();
        const bank = $('#in_bank_name').val();
        const cheque = $('#in_cheque_no').val().trim();

        $('#modal-in-confirm .is-invalid').removeClass('is-invalid');
        let ok = true;
        const bad = sel => {
            $(sel).addClass('is-invalid');
            ok = false;
        };

        if (!date) bad('#in_clearing_date_input');
        if (!mode) {
            ok = false;
            toastr.error('Please select a payment mode.');
        }
        if (mode == 2) {
            if (!company) bad('#in_paid_company');
            if (!bank) bad('#in_bank_name');
        }
        if (mode == 3 && !cheque) bad('#in_cheque_no');
        if (!ok) return;

        const btn = $(this).prop('disabled', true); // double-click guard
        showLoader();

        $.ajax({
                type: 'POST',
                url: "{{ route('investor.payout.fifo.save') }}",
                dataType: 'json',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    ...fifoLast, // investor_id, company_id, month, year, amount
                    paid_date: date,
                    paid_mode: mode,
                    paid_company_id: mode == 2 ? company : '',
                    paid_bank: mode == 2 ? bank : '',
                    paid_cheque_number: mode == 3 ? cheque : '',
                    payment_remarks: $('#in_payment_remarks').val()
                }
            })
            .done(function(res) {
                $('#modal-in-confirm').modal('hide');
                toastr.success(res.message);
                resetFifo();
                $('#payment_amount_total').val('');
                clearSelection();
                $('#payoutPendingTable').DataTable().ajax.reload(null, false);
            })
            .fail(function(xhr) {
                const r = xhr.responseJSON || {};
                const first = r.errors ? Object.values(r.errors).flat()[0] : null;
                toastr.error(first || r.message || 'Something went wrong.');
            })
            .always(function() {
                hideLoader();
                btn.prop('disabled', false);
            });
    });



    // ── Payment type toggle inside confirm modal ──────────────────────
    $(document).on('change', '.in_payment_mode', function() {
        $('.in_payment_mode').not(this).prop('checked', false);

        $('.in_banksingle, .in_companySingle, .in_cheque, .in_modechange').hide();

        // Remove required
        $('#in_paid_company').prop('required', false);
        $('#in_bank_name').prop('required', false);
        $('#in_cheque_no').prop('required', false);
        $('#in_payment_remarks').prop('required', false);

        if ($(this).prop('checked')) {
            console.log($(this).prop('checked'));
            $('.in_modechange').show();
            if ($(this).val() == 2) {
                $('.in_banksingle').show();
                $('.in_companySingle').show();
                $('.in_cheque').hide();
            } else if ($(this).val() == 3) {
                $('.in_banksingle').hide();
                $('.in_companySingle').hide();
                $('.in_cheque').show();
            } else {
                $('.in_banksingle').hide();
                $('.in_companySingle').hide();
                $('.in_cheque').hide();
            }
        } else {
            $('.in_banksingle').hide();
            $('.in_companySingle').hide();
            $('.in_cheque').hide();
            $('.in_modechange').hide();
        }
    });

    $(document).on('change', '#in_paid_company', function() {
        const companyId = $(this).val();
        let options = '<option value="">Select Bank</option>';
        allBanks.filter(b => b.company_id == companyId)
            .forEach(b => options += `<option value="${b.id}">${b.bank_name}</option>`);
        $('#in_bank_name').html(options).trigger('change');
    });
</script>
