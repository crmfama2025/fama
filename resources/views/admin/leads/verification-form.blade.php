@php
    $canUpdate = ($isVerifier ?? false)
        && in_array((int) $lead->approval_status, [1, 3], true);
@endphp

<div class="info-card mb-4">
    <div class="info-card-header">
        <h5><i class="fas fa-clipboard-check mr-2 text-primary"></i> Verification</h5>
    </div>

    <div class="info-card-body">
        <form id="verificationForm" action="{{ route('lead.verification.update', $lead->id) }}" method="POST">
            @csrf

            <div class="form-group">
                <label>Verification Details</label>
                <textarea name="verification_description" class="form-control" rows="6" maxlength="5000"
                    required {{ $canUpdate ? '' : 'readonly' }}>{{ old('verification_description', $lead->verification_description) }}</textarea>
            </div>

            <div class="form-group">
                <label>Status</label>
                <select name="approval_status" class="form-control" required {{ $canUpdate ? '' : 'disabled' }}>
                    <option value="3" {{ (int) $lead->approval_status === 3 ? 'selected' : '' }}>Unavailable</option>
                    <option value="4" {{ (int) $lead->approval_status === 4 ? 'selected' : '' }}>Completed</option>
                </select>
            </div>

            @if ($canUpdate)
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-save mr-1"></i> Save
                </button>
            @elseif ($isVerifier ?? false)
                <span class="text-muted">This lead is completed and can no longer be edited.</span>
            @endif
        </form>
    </div>
</div>