{{-- ================= APPROVE MODAL ================= --}}
<div class="modal fade" id="approveLeadModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form class="modal-content lead-ajax-form"
              action="{{ route('lead.approve', $lead->id) }}"
              method="POST"
              data-confirm-text="You want to approve this lead!"
              data-confirm-button="Yes, approve it!">
            @csrf

            <div class="modal-header">
                <h5 class="modal-title">Approve Lead</h5>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>

            <div class="modal-body">
                <div class="form-group">
                    <label>Lead</label>
                    <input type="text" class="form-control font-weight-bold"
                           value="{{ ucfirst($lead->contact_person_name) }}{{ !empty($lead->company_name) ? ' (' . ucfirst($lead->company_name) . ')' : '' }}"
                           readonly>
                </div>

                {{-- Default verifier. Replace with a <select> when the manager can choose. --}}
                <input type="hidden" name="verifier_id" value="{{ \App\Models\Lead::DEFAULT_VERIFIER_ID }}">

                <div class="form-group">
                    <label>Remarks</label>
                    <textarea name="remarks" id="approve_remarks" rows="3" maxlength="1000"
                              class="form-control" ></textarea>
                </div>
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-check-circle"></i> Approve
                </button>
            </div>
        </form>
    </div>
</div>


{{-- ================= REJECT MODAL ================= --}}
<div class="modal fade" id="rejectLeadModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form class="modal-content lead-ajax-form"
              action="{{ route('lead.reject', $lead->id) }}"
              method="POST"
              data-confirm-text="You want to reject this lead!"
              data-confirm-button="Yes, reject it!">
            @csrf

            <div class="modal-header">
                <h5 class="modal-title">Reject Lead</h5>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>

            <div class="modal-body">
                <div class="form-group">
                    <label>Lead</label>
                    <input type="text" class="form-control font-weight-bold"
                           value="{{ ucfirst($lead->contact_person_name) }}{{ !empty($lead->company_name) ? ' (' . ucfirst($lead->company_name) . ')' : '' }}"
                           readonly>
                </div>

                <div class="form-group">
                    <label class="asterisk">Reason for Rejection</label>
                    <textarea name="rejection_reason" id="rejection_reason" rows="4" maxlength="1000"
                              class="form-control" required
                              placeholder="Explain why this lead is rejected..."></textarea>
                </div>
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-times-circle"></i> Reject
                </button>
            </div>
        </form>
    </div>
</div>