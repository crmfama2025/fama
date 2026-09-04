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

    /* Follow-up table */
    .follow-up-table {
        border: 1px solid #e5e7eb;
        border-radius: 9px;
        overflow: hidden;
    }

    .follow-up-table-head {
        display: grid;
        grid-template-columns: 32px 1.4fr 1fr 1.2fr 1.2fr 28px;
        gap: 10px;
        padding: 10px 16px;
        background: #f8fafc;
        border-bottom: 1px solid #e5e7eb;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .4px;
        color: #6c757d;
    }

    .follow-up-row {
        display: grid;
        grid-template-columns: 32px 1.4fr 1fr 1.2fr 1.2fr 28px;
        gap: 10px;
        align-items: center;
        padding: 12px 16px;
        border-bottom: 1px solid #f0f0f0;
        cursor: pointer;
        transition: background-color .15s ease;
    }

    .follow-up-row:hover {
        background-color: #f8fafc;
    }

    .follow-up-row:last-child {
        border-bottom: none;
    }

    .follow-up-row.expanded {
        background-color: #f8fafc;
    }

    .follow-up-index {
        font-size: 12px;
        color: #6c757d;
    }

    .follow-up-value {
        font-size: 13px;
        color: #212529;
        word-break: break-word;
    }

    .follow-up-value.text-muted-value {
        color: #6c757d;
    }

    .follow-up-chevron {
        font-size: 13px;
        color: #adb5bd;
        transition: transform .15s ease;
        justify-self: end;
    }

    .follow-up-row.expanded .follow-up-chevron {
        transform: rotate(180deg);
    }

    /* Follow-up status pill */
    .follow-up-status {
        display: inline-flex;
        align-items: center;
        padding: 4px 9px;
        border-radius: 15px;
        font-size: 11px;
        font-weight: 600;
        white-space: nowrap;
    }

    .follow-up-status.status-2 {
        background: rgba(34, 197, 94, 0.12);
        border: 1px solid rgba(34, 197, 94, 0.28);
        color: #15803d;
    }

    .follow-up-status.status-3 {
        background: rgba(59, 130, 246, 0.12);
        border: 1px solid rgba(59, 130, 246, 0.28);
        color: #2563eb;
    }

    .follow-up-status.status-4 {
        background: rgba(107, 114, 128, 0.12);
        border: 1px solid rgba(107, 114, 128, 0.28);
        color: #4b5563;
    }

    .follow-up-status.status-5 {
        background: rgba(239, 68, 68, 0.12);
        border: 1px solid rgba(239, 68, 68, 0.28);
        color: #dc2626;
    }

    .follow-up-status.status-6 {
        background: rgba(139, 92, 246, 0.12);
        border: 1px solid rgba(139, 92, 246, 0.28);
        color: #7c3aed;
    }

    .follow-up-status.status-7 {
        background: rgba(6, 182, 212, 0.12);
        border: 1px solid rgba(6, 182, 212, 0.28);
        color: #0891b2;
    }

    .follow-up-status.status-8 {
        background: rgba(217, 119, 6, 0.12);
        border: 1px solid rgba(217, 119, 6, 0.28);
        color: #b45309;
    }

    .follow-up-status.status-9 {
        background: rgba(22, 163, 74, 0.12);
        border: 1px solid rgba(22, 163, 74, 0.28);
        color: #15803d;
    }

    .follow-up-status.status-10 {
        background: rgba(220, 38, 38, 0.12);
        border: 1px solid rgba(220, 38, 38, 0.28);
        color: #b91c1c;
    }

    .follow-up-status.status-11 {
        background: rgba(75, 85, 99, 0.12);
        border: 1px solid rgba(75, 85, 99, 0.28);
        color: #374151;
    }

    /* Expanded detail panel */
    .follow-up-detail {
        display: none;
        padding: 16px 16px 18px 58px;
        background: #fbfbfb;
        border-bottom: 1px solid #f0f0f0;
    }

    .follow-up-detail.open {
        display: block;
    }

    .follow-up-detail-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        margin-bottom: 14px;
    }

    .follow-up-detail-field {
        min-width: 0;
    }

    .follow-up-label {
        display: block;
        margin-bottom: 5px;
        color: #6c757d;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .4px;
    }

    .follow-up-detail-value {
        display: block;
        color: #212529;
        font-size: 14px;
        font-weight: 500;
        word-break: break-word;
    }

    .follow-up-notes-block {
        padding-top: 12px;
        border-top: 1px solid #f0f0f0;
    }

    .follow-up-notes-block .follow-up-detail-value {
        line-height: 1.6;
        color: #495057;
        font-weight: 400;
    }

    .follow-up-notes-text,
    .follow-up-reason-text {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .follow-up-notes-text.expanded,
    .follow-up-reason-text.expanded {
        -webkit-line-clamp: unset;
        display: block;
    }

    .follow-up-notes-toggle,
    .follow-up-reason-toggle {
        display: none;
        margin-top: 4px;
        padding: 0;
        border: none;
        background: none;
        color: #4338ca;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
    }

    .follow-up-notes-toggle:hover,
    .follow-up-reason-toggle:hover {
        text-decoration: underline;
    }

    .follow-up-detail-actions {
        margin-top: 14px;
        text-align: right;
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

    /* Responsive */
    @media (max-width: 767px) {
        .follow-up-table-head {
            display: none;
        }

        .follow-up-row {
            grid-template-columns: 24px 1fr 28px;
            row-gap: 4px;
        }

        .follow-up-detail {
            padding-left: 16px;
        }

        .follow-up-detail-grid {
            grid-template-columns: 1fr 1fr;
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
                @if (auth()->user()->id === $lead->assigned_to && $lead->status !== 9 && $lead->status !== 10)
                    <button type="button" class="btn btn-primary btn-sm addFollowUpBtn" data-toggle="modal"
                        data-target="#followUpModal">
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
                    <div class="follow-up-table">
                        <div class="follow-up-table-head">
                            <span>#</span>
                            <span>Status</span>
                            <span>Type</span>
                            <span>Follow up date</span>
                            <span>Next follow-up</span>
                            <span></span>
                        </div>
                        @foreach ($lead->followUps as $followUp)
                            <div class="follow-up-row" data-toggle-detail="followUpDetail{{ $followUp->id }}">
                                <span class="follow-up-index">{{ $loop->iteration }}</span>

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

                                <span class="follow-up-value">
                                    {{ $followUp->follow_up_date ? \Carbon\Carbon::parse($followUp->follow_up_date)->format('d M Y') : '-' }}
                                </span>

                                <span class="follow-up-value text-muted-value">
                                    @if (!in_array((int) $followUp->follow_up_status, [9, 10]) && $followUp->next_follow_up_date)
                                        {{ \Carbon\Carbon::parse($followUp->next_follow_up_date)->format('d M Y') }}
                                        @if ($followUp->next_follow_up_time)
                                            ,
                                            {{ \Carbon\Carbon::parse($followUp->next_follow_up_time)->format('h:i A') }}
                                        @endif
                                    @else
                                        -
                                    @endif
                                </span>

                                <i class="fas fa-chevron-down follow-up-chevron"></i>
                            </div>

                            <div class="follow-up-detail" id="followUpDetail{{ $followUp->id }}">
                                <div class="follow-up-detail-grid">
                                    {{-- Meeting Details --}}
                                    @if ((int) $followUp->follow_up_status === 6)
                                        <div class="follow-up-detail-field">
                                            <span class="follow-up-label">Meeting Date</span>
                                            <span class="follow-up-detail-value">
                                                <i class="far fa-calendar-check mr-1 text-info"></i>
                                                {{ $followUp->meeting_date ? \Carbon\Carbon::parse($followUp->meeting_date)->format('d M Y') : '-' }}
                                            </span>
                                        </div>
                                        <div class="follow-up-detail-field">
                                            <span class="follow-up-label">Meeting Time</span>
                                            <span class="follow-up-detail-value">
                                                <i class="far fa-clock mr-1 text-info"></i>
                                                {{ $followUp->meeting_time ? \Carbon\Carbon::parse($followUp->meeting_time)->format('h:i A') : '-' }}
                                            </span>
                                        </div>
                                        <div class="follow-up-detail-field">
                                            <span class="follow-up-label">Meeting Location</span>
                                            <span class="follow-up-detail-value">
                                                <i class="fas fa-map-marker-alt mr-1 text-danger"></i>
                                                {{ $followUp->meeting_location ?: '-' }}
                                            </span>
                                        </div>
                                    @endif

                                    {{-- Not Interested Reason --}}
                                    @if ((int) $followUp->follow_up_status === 5)
                                        <div class="follow-up-detail-field">
                                            <span class="follow-up-label">Reason</span>
                                            <span class="follow-up-detail-value">
                                                <i class="fas fa-info-circle mr-1 text-danger"></i>
                                                <span
                                                    class="follow-up-reason-text">{{ $followUp->not_interested_reason ?: '-' }}</span>
                                            </span>
                                            <button type="button" class="follow-up-reason-toggle">Show more</button>
                                        </div>
                                    @endif

                                    <div class="follow-up-detail-field">
                                        <span class="follow-up-label">Follow up date</span>
                                        <span class="follow-up-detail-value">
                                            <i class="far fa-calendar-alt mr-1 text-primary"></i>
                                            {{ $followUp->follow_up_date ? \Carbon\Carbon::parse($followUp->follow_up_date)->format('d M Y') : '-' }}
                                        </span>
                                    </div>
                                </div>

                                {{-- Notes --}}
                                @if ($followUp->notes)
                                    <div class="follow-up-notes-block">
                                        <span class="follow-up-label">Notes</span>
                                        <span class="follow-up-detail-value">
                                            <i class="fas fa-sticky-note mr-1 text-warning"></i>
                                            <span class="follow-up-notes-text">{{ $followUp->notes }}</span>
                                        </span>
                                        <button type="button" class="follow-up-notes-toggle">Show more</button>
                                    </div>
                                @endif

                                {{-- Edit / Delete (latest follow-up, owner only) --}}
                                @if ($followUp->id === $latestFollowUpId && auth()->user()->id === $lead->assigned_to)
                                    <div class="follow-up-detail-actions">
                                        <button type="button" class="btn btn-sm btn-outline-primary editFollowUpBtn"
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
                                            data-next-time="{{ $followUp->next_follow_up_time ?? '' }}">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm delete-follow-up"
                                            data-id="{{ $followUp->id }}">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </div>
                                @endif
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
