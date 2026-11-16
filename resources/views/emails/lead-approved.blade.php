<div style="font-family: Arial, sans-serif; font-size: 14px; color: #212529;">
    <p>Hello {{ $lead->verifier->first_name ?? 'there' }},</p>

    <p>
        A lead has been approved by
        <strong>{{ $approver->first_name ?? '' }} {{ $approver->last_name ?? '' }}</strong>
        and is now waiting for your verification.
    </p>

    <table cellpadding="6" style="border-collapse: collapse;">
        <tr><td><strong>Company</strong></td><td>{{ $lead->company_name ?: '-' }}</td></tr>
        <tr><td><strong>Contact Person</strong></td><td>{{ $lead->contact_person_name }}</td></tr>
        <tr><td><strong>Phone</strong></td><td>{{ $lead->phone_number }}</td></tr>
        <tr><td><strong>Location</strong></td><td>{{ $lead->required_location ?: '-' }}</td></tr>
        <tr><td><strong>Total Staff</strong></td><td>{{ $lead->total_staff ?? '-' }}</td></tr>
        @if ($lead->approval_remarks)
            <tr><td><strong>Remarks</strong></td><td>{{ $lead->approval_remarks }}</td></tr>
        @endif
    </table>

    <p>
        <a href="{{ route('lead.show', $lead->id) }}"
           style="background:#007bff;color:#fff;padding:10px 18px;border-radius:5px;text-decoration:none;">
            View Lead
        </a>
    </p>
</div>