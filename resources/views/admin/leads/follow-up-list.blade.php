<style>
    .sales-card {
        border: 1px solid #e5e7eb;
        border-radius: 9px;
        background: #fff;
        height: 100%;
        overflow: hidden;
    }

    .sales-card-header {
        padding: 15px 18px;
        background: #f8fafc;
        border-bottom: 1px solid #e5e7eb;
    }

    .sales-card-header h5 {
        margin: 0;
        font-size: 15px;
        font-weight: 600;
        color: #343a40;
    }

    .sales-card-body {
        padding: 20px;
    }

    .follow-up-lead-info {
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 14px 16px;
    }

    .follow-up-lead-icon {
        width: 42px;
        height: 42px;
        min-width: 42px;
        border-radius: 8px;
        background: #efe6d0;
        color: #8a7b4e;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 12px;
    }

    .follow-up-lead-name {
        font-size: 15px;
        font-weight: 600;
        color: #212529;
    }

    .follow-up-lead-company {
        font-size: 12px;
        color: #6c757d;
        margin-top: 2px;
    }

    #followUpModal .form-group {
        margin-bottom: 18px;
    }

    #followUpModal label {
        font-size: 13px;
        font-weight: 600;
        color: #343a40;
    }

    #followUpModal .form-control {
        border-radius: 6px;
    }

    #followUpModal textarea {
        resize: vertical;
    }

    /* Follow-up list */
    .follow-up-list {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .follow-up-item {
        border: 1px solid #e5e7eb;
        border-radius: 9px;
        background: #fff;
        overflow: hidden;
        transition: box-shadow .2s ease, border-color .2s ease;
    }

    .follow-up-item:hover {
        border-color: #d1d5db;
        box-shadow: 0 3px 10px rgba(0, 0, 0, .04);
    }

    /* Follow-up header */
    .follow-up-item-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 11px 16px;
        background: #f8fafc;
        border-bottom: 1px solid #e5e7eb;
    }

    .follow-up-number {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 32px;
        height: 26px;
        padding: 0 9px;
        border-radius: 5px;
        background: #eef2ff;
        color: #4338ca;
        font-size: 13px;
        font-weight: 700;
    }

    /* Follow-up body */
    .follow-up-item-body {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 18px;
        padding: 17px 16px;
    }

    .follow-up-field {
        min-width: 0;
    }

    .follow-up-label {
        display: block;
        margin-bottom: 5px;
        color: #6c757d;
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .4px;
    }

    .follow-up-value {
        display: block;
        color: #212529;
        font-size: 15px;
        font-weight: 500;
        word-break: break-word;
    }

    /* Follow-up status */
    .follow-up-status {
        display: inline-flex;
        align-items: center;
        padding: 4px 9px;
        border-radius: 15px;
        font-size: 11px;
        font-weight: 600;
        white-space: nowrap;
    }

    /* Interested */
    .follow-up-status.status-2 {
        background: rgba(34, 197, 94, 0.12);
        border: 1px solid rgba(34, 197, 94, 0.28);
        color: #15803d;
    }

    /* Call Back */
    .follow-up-status.status-3 {
        background: rgba(59, 130, 246, 0.12);
        border: 1px solid rgba(59, 130, 246, 0.28);
        color: #2563eb;
    }

    /* No Answer */
    .follow-up-status.status-4 {
        background: rgba(107, 114, 128, 0.12);
        border: 1px solid rgba(107, 114, 128, 0.28);
        color: #4b5563;
    }

    /* Not Interested */
    .follow-up-status.status-5 {
        background: rgba(239, 68, 68, 0.12);
        border: 1px solid rgba(239, 68, 68, 0.28);
        color: #dc2626;
    }

    /* Meeting Scheduled */
    .follow-up-status.status-6 {
        background: rgba(139, 92, 246, 0.12);
        border: 1px solid rgba(139, 92, 246, 0.28);
        color: #7c3aed;
    }

    /* Proposal Sent */
    .follow-up-status.status-7 {
        background: rgba(6, 182, 212, 0.12);
        border: 1px solid rgba(6, 182, 212, 0.28);
        color: #0891b2;
    }

    /* Negotiation */
    .follow-up-status.status-8 {
        background: rgba(217, 119, 6, 0.12);
        border: 1px solid rgba(217, 119, 6, 0.28);
        color: #b45309;
    }

    /* Converted */
    .follow-up-status.status-9 {
        background: rgba(22, 163, 74, 0.12);
        border: 1px solid rgba(22, 163, 74, 0.28);
        color: #15803d;
    }

    /* Lost */
    .follow-up-status.status-10 {
        background: rgba(220, 38, 38, 0.12);
        border: 1px solid rgba(220, 38, 38, 0.28);
        color: #b91c1c;
    }

    /* Others */
    .follow-up-status.status-11 {
        background: rgba(75, 85, 99, 0.12);
        border: 1px solid rgba(75, 85, 99, 0.28);
        color: #374151;
    }

    /* Follow-up notes */
    .follow-up-notes {
        grid-column: 1 / -1;
        padding-top: 14px;
        border-top: 1px solid #f0f0f0;
    }

    .follow-up-notes .follow-up-value {
        line-height: 1.6;
        color: #495057;
    }

    /* Empty state */
    .follow-up-empty {
        text-align: center;
        padding: 35px 20px;
        border: 1px dashed #d1d5db;
        border-radius: 8px;
        background: #fafafa;
    }

    .follow-up-empty-icon {
        width: 46px;
        height: 46px;
        margin: 0 auto 12px;
        border-radius: 50%;
        background: #eef2ff;
        color: #4338ca;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }

    .follow-up-empty-title {
        font-size: 14px;
        font-weight: 600;
        color: #343a40;
    }

    .follow-up-empty-text {
        margin-top: 5px;
        color: #6c757d;
        font-size: 12px;
    }

    /* Lead status */
    .lead-status.pending {
        background: rgba(217, 155, 27, 0.14);
        border: 1px solid rgba(217, 155, 27, 0.3);
        color: #92650b;
    }

    .lead-status.active {
        background: rgba(37, 99, 235, 0.12);
        border: 1px solid rgba(37, 99, 235, 0.28);
        color: #1d4ed8;
    }

    .lead-status.interested {
        background: rgba(34, 197, 94, 0.12);
        border: 1px solid rgba(34, 197, 94, 0.28);
        color: #15803d;
    }

    .lead-status.callback {
        background: rgba(59, 130, 246, 0.12);
        border: 1px solid rgba(59, 130, 246, 0.28);
        color: #2563eb;
    }

    .lead-status.no-answer {
        background: rgba(107, 114, 128, 0.12);
        border: 1px solid rgba(107, 114, 128, 0.28);
        color: #4b5563;
    }

    .lead-status.not-interested {
        background: rgba(239, 68, 68, 0.12);
        border: 1px solid rgba(239, 68, 68, 0.28);
        color: #dc2626;
    }

    .lead-status.meeting {
        background: rgba(139, 92, 246, 0.12);
        border: 1px solid rgba(139, 92, 246, 0.28);
        color: #7c3aed;
    }

    .lead-status.proposal {
        background: rgba(6, 182, 212, 0.12);
        border: 1px solid rgba(6, 182, 212, 0.28);
        color: #0891b2;
    }

    .lead-status.negotiation {
        background: rgba(217, 119, 6, 0.12);
        border: 1px solid rgba(217, 119, 6, 0.28);
        color: #b45309;
    }

    .lead-status.converted {
        background: rgba(22, 163, 74, 0.12);
        border: 1px solid rgba(22, 163, 74, 0.28);
        color: #15803d;
    }

    .lead-status.lost {
        background: rgba(220, 38, 38, 0.12);
        border: 1px solid rgba(220, 38, 38, 0.28);
        color: #b91c1c;
    }

    .lead-status.others {
        background: rgba(75, 85, 99, 0.12);
        border: 1px solid rgba(75, 85, 99, 0.28);
        color: #374151;
    }

    .lead-status {
        display: inline-flex;
        align-items: center;
        gap: 2px;
        padding: 2px 8px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        white-space: nowrap;
    }

    .empty-value {
        color: #adb5bd;
        font-style: italic;
    }

    .lead-status i {
        font-size: 8px;
        margin-right: 2px;
    }

    /* Responsive */
    @media (max-width: 991px) {
        .follow-up-item-body {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media (max-width: 767px) {
        .follow-up-item-body {
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }
    }

    @media (max-width: 480px) {
        .follow-up-item-body {
            grid-template-columns: 1fr;
        }

        .follow-up-notes {
            grid-column: auto;
        }
    }
</style>

<div class="row">
    <div class="col-lg-12 mb-4">
        <div class="sales-card">
            <div class="sales-card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-phone-volume mr-2 text-primary"></i>
                    Follow Up
                </h5>
                @if (auth()->user()->id === $lead->assigned_to && !in_array($lead->status, [5, 9, 10]))
                    <button type="button" class="btn btn-primary btn-sm addFollowUpBtn" data-toggle="modal"
                        data-target="#followUpModal" title="Add Follow-up">
                        <i class="fas fa-plus mr-1"></i>
                        Add
                    </button>
                @endif
            </div>
            <div class="sales-card-body">
                @php
                    $latestFollowUpId = $lead->followUps()->latest('id')->value('id');
                @endphp
                @if ($lead->followUps->count())
                    <div class="follow-up-list">
                        @foreach ($lead->followUps->sortByDesc('created_at') as $followUp)
                            <div class="follow-up-item">
                                {{-- Header --}}
                                <div class="follow-up-item-header">
                                    <span class="follow-up-number">
                                        #{{ $lead->followUps->count() - $loop->iteration + 1 }}
                                    </span>
                                    @if ($followUp->id === $latestFollowUpId && auth()->user()->id === $lead->assigned_to)
                                        <div>
                                            <button type="button"
                                                class="btn btn-sm btn-outline-primary editFollowUpBtn"
                                                data-id="{{ $followUp->id }}"
                                                data-status="{{ $followUp->follow_up_status }}"
                                                data-type="{{ $followUp->follow_up_type }}"
                                                data-date="{{ $followUp->follow_up_date ? $followUp->follow_up_date->format('Y-m-d') : '' }}"
                                                data-not-interested-reason="{{ $followUp->not_interested_reason ?? '' }}"
                                                data-meeting-date="{{ $followUp->meeting_date ? $followUp->meeting_date->format('Y-m-d') : '' }}"
                                                data-meeting-time="{{ $followUp->meeting_time ?? '' }}"
                                                data-meeting-location="{{ $followUp->meeting_location ?? '' }}"
                                                data-notes="{{ $followUp->notes ?? '' }}"
                                                data-next-date="{{ $followUp->next_follow_up_date ? $followUp->next_follow_up_date->format('Y-m-d') : '' }}"
                                                data-next-time="{{ $followUp->next_follow_up_time ?? '' }}"
                                                title="Edit Follow-up">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-danger btn-sm delete-follow-up"
                                                title="Delete Follow-up" data-id="{{ $followUp->id }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                                {{-- Content --}}
                                <div class="follow-up-item-body">
                                    {{-- Status --}}
                                    <div class="follow-up-field">
                                        <span class="follow-up-label">Status</span>
                                        <span class="follow-up-status status-{{ $followUp->follow_up_status }}">
                                            @switch((int) $followUp->follow_up_status)
                                                @case(2)
                                                    Interested
                                                @break

                                                @case(3)
                                                    Call Back
                                                @break

                                                @case(4)
                                                    No Answer
                                                @break

                                                @case(5)
                                                    Not Interested
                                                @break

                                                @case(6)
                                                    Meeting Scheduled
                                                @break

                                                @case(7)
                                                    Proposal Sent
                                                @break

                                                @case(8)
                                                    Negotiation
                                                @break

                                                @case(9)
                                                    Converted
                                                @break

                                                @case(10)
                                                    Lost
                                                @break

                                                @case(11)
                                                    Others
                                                @break

                                                @default
                                                    -
                                            @endswitch
                                        </span>
                                    </div>
                                    {{-- Type --}}
                                    <div class="follow-up-field">
                                        <span class="follow-up-label">Type</span>
                                        <span class="follow-up-value">
                                            @switch((int) $followUp->follow_up_type)
                                                @case(1)
                                                    <i class="fas fa-phone text-primary mr-1"></i>
                                                    Call
                                                @break

                                                @case(2)
                                                    <i class="fab fa-whatsapp text-success mr-1"></i>
                                                    WhatsApp
                                                @break

                                                @case(3)
                                                    <i class="fas fa-envelope text-primary mr-1"></i>
                                                    Email
                                                @break

                                                @case(4)
                                                    <i class="fas fa-users text-info mr-1"></i>
                                                    Meeting
                                                @break

                                                @case(5)
                                                    <i class="fas fa-sms text-primary mr-1"></i>
                                                    SMS
                                                @break

                                                @case(6)
                                                    <i class="fas fa-comment text-secondary mr-1"></i>
                                                    Other
                                                @break

                                                @default
                                                    -
                                            @endswitch
                                        </span>
                                    </div>
                                    {{-- Follow Up Date --}}
                                    <div class="follow-up-field">
                                        <span class="follow-up-label">Follow Up Date</span>
                                        <span class="follow-up-value">
                                            <i class="far fa-calendar-alt mr-1 text-primary"></i>
                                            {{ $followUp->follow_up_date ? \Carbon\Carbon::parse($followUp->follow_up_date)->format('d M Y') : '-' }}
                                        </span>
                                    </div>
                                    {{-- Meeting Details --}}
                                    @if ((int) $followUp->follow_up_status === 6)
                                        <div class="follow-up-field">
                                            <span class="follow-up-label">Meeting Date</span>
                                            <span class="follow-up-value">
                                                <i class="far fa-calendar-check mr-1 text-info"></i>
                                                {{ $followUp->meeting_date ? \Carbon\Carbon::parse($followUp->meeting_date)->format('d M Y') : '-' }}
                                            </span>
                                        </div>
                                        <div class="follow-up-field">
                                            <span class="follow-up-label">Meeting Time</span>
                                            <span class="follow-up-value">
                                                <i class="far fa-clock mr-1 text-info"></i>
                                                {{ $followUp->meeting_time ? \Carbon\Carbon::parse($followUp->meeting_time)->format('h:i A') : '-' }}
                                            </span>
                                        </div>
                                        <div class="follow-up-field">
                                            <span class="follow-up-label">Meeting Location</span>
                                            <span class="follow-up-value">
                                                <i class="fas fa-map-marker-alt mr-1 text-danger"></i>
                                                {{ !empty($followUp->meeting_location) ? ucfirst($followUp->meeting_location) : '-' }}
                                            </span>
                                        </div>
                                    @endif
                                    {{-- Not Interested Reason --}}
                                    @if ((int) $followUp->follow_up_status === 5)
                                        <div class="follow-up-field follow-up-notes">
                                            <span class="follow-up-label">Reason</span>
                                            <span class="follow-up-value">
                                                <i class="fas fa-info-circle mr-1 text-danger"></i>
                                                {{ $followUp->not_interested_reason ?: '-' }}
                                            </span>
                                        </div>
                                    @endif
                                    {{-- Next Follow-up --}}
                                    @if (!in_array((int) $followUp->follow_up_status, [9, 10]))
                                        <div class="follow-up-field">
                                            <span class="follow-up-label">Next Follow-up</span>
                                            <span class="follow-up-value">
                                                @if ($followUp->next_follow_up_date)
                                                    <i class="far fa-calendar-alt mr-1 text-primary"></i>
                                                    {{ \Carbon\Carbon::parse($followUp->next_follow_up_date)->format('d M Y') }}
                                                @else
                                                    -
                                                @endif
                                            </span>
                                        </div>
                                        <div class="follow-up-field">
                                            <span class="follow-up-label">Time</span>
                                            <span class="follow-up-value">
                                                @if ($followUp->next_follow_up_time)
                                                    <i class="far fa-clock mr-1 text-primary"></i>
                                                    {{ \Carbon\Carbon::parse($followUp->next_follow_up_time)->format('h:i A') }}
                                                @else
                                                    -
                                                @endif
                                            </span>
                                        </div>
                                    @endif
                                    {{-- Notes --}}
                                    @if ($followUp->notes)
                                        <div class="follow-up-field follow-up-notes">
                                            <span class="follow-up-label">Notes</span>
                                            <span class="follow-up-value">
                                                <i class="fas fa-sticky-note mr-1 text-warning"></i>
                                                {{ $followUp->notes }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="follow-up-empty">
                        <div class="follow-up-empty-icon">
                            <i class="fas fa-calendar-plus"></i>
                        </div>
                        <div class="follow-up-empty-title">
                            No Follow-up Added
                        </div>
                        <div class="follow-up-empty-text">
                            Add a follow-up to keep track of your communication with this lead.
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
