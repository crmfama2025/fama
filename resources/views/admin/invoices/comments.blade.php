@forelse($invoice->comments as $comment)
    <div class="border p-2 d-flex align-items-start">

        {{-- Profile Image --}}
        <div class="mr-2">
            <img src="{{ $comment->user->profile_path ? asset($comment->user->profile_path) : asset('images/default-user.png') }}"
                alt="User" width="40" height="40" class="rounded-circle">
        </div>

        {{-- Comment Content --}}
        <div>
            <strong>
                {{ $comment->user->first_name ?? '' }}
                {{ $comment->user->last_name ?? '' }}
            </strong>

            <br>

            {{ $comment->comment }}

            <br>

            <small class="text-muted">
                {{ $comment->created_at->format('d M Y, h:i A') }}
            </small>
        </div>

    </div>
@empty
    <p>No comments found.</p>
@endforelse
