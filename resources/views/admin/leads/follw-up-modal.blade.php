{{-- Follow-up Modal --}}
<div class="modal fade" id="followUpModal" tabindex="-1" role="dialog" aria-labelledby="followUpModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            {{-- Header --}}
            <div class="modal-header">
                <h5 class="modal-title" id="followUpModalLabel">
                    <i class="fas fa-phone-volume mr-2 text-primary"></i>
                    <span id="followUpModalTitle">Add Follow-up</span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            {{-- Form --}}
            <form id="followUpForm">
                @csrf
                <input type="hidden" name="follow_up_id" id="followUpId">

                <div class="modal-body">
                    {{-- Lead Information --}}
                    <div class="follow-up-lead-info mb-4">
                        <div class="d-flex align-items-center">
                            <div class="follow-up-lead-icon">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <div>
                                <div class="follow-up-lead-name">
                                    {{ $lead->contact_person_name ?: 'Lead' }}
                                </div>
                                <div class="follow-up-lead-company">
                                    <i class="fas fa-building mr-1"></i>
                                    {{ $lead->company_name ?: 'Individual Lead' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Outcome --}}
                    <div class="form-group">
                        <label for="followUpOutcome" class="asterisk">Follow-up Outcome</label>
                        <select name="follow_up_status" id="followUpOutcome" class="form-control select2" required>
                            <option value="">Select Outcome</option>
                            <option value="2">Interested</option>
                            <option value="3">Call Back</option>
                            <option value="4">No Answer</option>
                            <option value="5">Not Interested</option>
                            <option value="6">Meeting Scheduled</option>
                            <option value="7">Proposal Sent</option>
                            <option value="8">Negotiation</option>
                            <option value="9">Converted</option>
                            <option value="10">Lost</option>
                            <option value="11">Others</option>
                        </select>
                    </div>

                    {{-- Follow Up Type + Date --}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="follow_up_type" class="asterisk">Follow Up Type</label>
                                <select name="follow_up_type" id="follow_up_type" class="form-control select2" required>
                                    <option value="">Select Type</option>
                                    <option value="1">Phone Call</option>
                                    <option value="2">WhatsApp</option>
                                    <option value="3">Email</option>
                                    <option value="4">Meeting</option>
                                    <option value="5">SMS</option>
                                    <option value="6">Other</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="followUpDate" class="asterisk">Follow-up Date</label>
                                <input type="date" name="follow_up_date" id="followUpDate" class="form-control"
                                    value="{{ now()->format('Y-m-d') }}" required>
                            </div>
                        </div>
                    </div>

                    {{-- Not Interested Reason --}}
                    <div class="form-group d-none" id="notInterestedReasonWrapper">
                        <label for="notInterestedReason" class="asterisk">Reason</label>
                        <textarea name="not_interested_reason" id="notInterestedReason" class="form-control" rows="4"
                            placeholder="Please enter the reason..." required></textarea>
                    </div>

                    {{-- Meeting Details --}}
                    <div id="meetingDetailsWrapper" class="d-none">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="meetingDate" class="asterisk">Meeting Date</label>
                                    <input type="date" name="meeting_date" id="meetingDate" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="meetingTime" class="asterisk">Meeting Time</label>
                                    <input type="time" name="meeting_time" id="meetingTime" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="meetingLocation">Meeting Location</label>
                            <input type="text" name="meeting_location" id="meetingLocation" class="form-control"
                                placeholder="Enter meeting location">
                        </div>
                    </div>

                    {{-- Notes --}}
                    <div class="form-group" id="notesWrapper">
                        <label for="followUpNotes">Notes</label>
                        <textarea name="notes" id="followUpNotes" class="form-control" rows="4"
                            placeholder="Enter details about the conversation..."></textarea>
                    </div>

                    {{-- Next Follow-up --}}
                    <div id="nextFollowUpWrapper">
                        <div class="form-group">
                            <label>Next Follow-up</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <input type="date" name="next_follow_up_date" id="nextFollowUpDate"
                                        class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <input type="time" name="next_follow_up_time" id="nextFollowUpTime"
                                        class="form-control">
                                    <span id="clearFollowUpTime"
                                        style="position:absolute; right:10px; top:8px; cursor:pointer;">&times;</span>
                                </div>
                            </div>
                            <small class="text-muted">
                                Select when you want to contact this lead again.
                            </small>
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" id="saveFollowUpBtn">
                        <i class="fas fa-save mr-1"></i>
                        Save Follow-up
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
