{{-- Assign Lead Modal --}}
<div class="modal fade" id="assignLeadModal" tabindex="-1" role="dialog" aria-labelledby="assignLeadModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content">
            {{-- Header --}}
            <div class="modal-header">
                <h5 class="modal-title" id="assignLeadModalLabel">
                    <i class="fas fa-user-plus mr-2 text-primary"></i>
                    {{ $lead->assigned_to ? 'Reassign Lead' : 'Assign Lead' }}
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            {{-- Form --}}
            <form id="assignLeadForm">
                @csrf
                <div class="modal-body">
                    {{-- Sales Person --}}
                    <div class="form-group">
                        <label for="assigned_to" class="asterisk">
                            Sales Person
                        </label>
                        <select name="assigned_to" id="assigned_to" class="form-control select2" style="width: 100%;"
                            required>
                            <option value="">Select Sales Person</option>
                            @foreach ($salesPersons as $salesPerson)
                                <option value="{{ $salesPerson->id }}"
                                    {{ (int) $lead->assigned_to === (int) $salesPerson->id ? 'selected' : '' }}>
                                    {{ $salesPerson->first_name }} {{ $salesPerson->last_name }}
                                </option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">
                            Select the salesperson responsible for following up this lead.
                        </small>
                    </div>

                    {{-- Assignment Remarks --}}
                    <div class="form-group">
                        <label for="assignment_remarks">Remarks</label>
                        <textarea name="assignment_remarks" id="assignment_remarks" class="form-control" rows="3"
                            placeholder="Add any instructions or remarks for the salesperson..."></textarea>
                    </div>

                    {{-- Current Assignment --}}
                    @if ($lead->assignedTo)
                        <div class="current-assignment">
                            <div class="current-assignment-title">
                                <i class="fas fa-info-circle mr-1"></i>
                                Current Assignment
                            </div>
                            <div class="current-assignment-details">
                                <strong>
                                    {{ $lead->assignedTo->first_name }} {{ $lead->assignedTo->last_name }}
                                </strong>
                                @if ($lead->assigned_at)
                                    <span class="ml-2">
                                        Assigned on {{ $lead->assigned_at->format('d M Y, h:i A') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Footer --}}
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" id="assignLeadBtn">
                        <i class="fas fa-user-check mr-1"></i>
                        {{ $lead->assigned_to ? 'Reassign Lead' : 'Assign Lead' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
