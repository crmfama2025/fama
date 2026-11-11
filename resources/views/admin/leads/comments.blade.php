<div class="info-card mb-4">
    <div class="info-card-header">
        <h5><i class="fas fa-comments mr-2 text-primary"></i> Comments</h5>
    </div>

    <div class="info-card-body">

        @if (auth()->user()->hasAnyPermission(['leads.comment_add'])
            && in_array((int) $lead->approval_status, [1, 3, 4], true))
            <form id="leadCommentForm" action="{{ route('lead.comment.store', $lead->id) }}" method="POST" class="mb-4">
                @csrf

                <div class="form-group">
                    <label>Comment</label>
                    <textarea name="comment" class="form-control" rows="3" required maxlength="2000"></textarea>
                </div>

                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-paper-plane mr-1"></i> Add Comment
                </button>
            </form>
        @endif

        @forelse ($lead->comments as $comment)
            <div class="activity-item">
                <div class="activity-title">{{ $comment->comment }}</div>
                <div class="activity-user">
                    <i class="fas fa-user mr-1"></i>
                    {{ $comment->user->first_name ?? '' }} {{ $comment->user->last_name ?? '' }}
                </div>
                <div class="activity-date">
                    <i class="far fa-clock mr-1"></i>
                    {{ $comment->created_at->format('d M Y, h:i A') }}
                </div>
            </div>
        @empty
            <span class="text-muted">No comments yet.</span>
        @endforelse
    </div>
</div>