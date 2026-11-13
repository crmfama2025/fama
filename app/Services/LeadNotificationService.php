<?php

namespace App\Services;

use App\Mail\LeadApprovedMail;
use App\Models\FcmToken;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class LeadNotificationService
{
    public function __construct(protected FcmService $fcm) {}

    public function leadCreated(Lead $lead): void
    {
        $this->push(
            $this->idsWithPermission('leads.assign'),
            'New Lead Created',
            "Lead {$this->label($lead)} was created and needs assignment.",
            $lead
        );
    }

    public function leadAssigned(Lead $lead): void
    {
        $this->push(
            [$lead->assigned_to],
            'Lead Assigned to You',
            "Lead {$this->label($lead)} has been assigned to you.",
            $lead
        );
    }

    public function followUpSaved(Lead $lead, string $action, string $statusLabel): void
    {
        $recipients = [$lead->created_by];

        // Manager only needs to hear about it while approval is still pending
        if ((int) $lead->approval_status === 0) {
            $recipients = array_merge($recipients, $this->idsWithPermission('leads.approve'));
        }

        $this->push(
            $recipients,
            'Lead Follow-up ' . ucfirst($action),
            "Lead {$this->label($lead)} follow-up {$action}. Status: {$statusLabel}.",
            $lead
        );
    }

    public function leadApproved(Lead $lead): void
    {
        $this->push(
            [$lead->created_by, $lead->verifier_id],
            'Lead Approved',
            "Lead {$this->label($lead)} has been approved.",
            $lead
        );

        // Email Alex (the verifier)
        try {
            $verifier = User::find($lead->verifier_id);

            if ($verifier && $verifier->email) {
                Mail::to($verifier->email)->send(new LeadApprovedMail($lead, auth()->user()));
            }
        } catch (\Throwable $e) {
            Log::error('Lead approved email failed: ' . $e->getMessage());
        }
    }

    public function leadRejected(Lead $lead): void
    {
        $this->push(
            [$lead->created_by],
            'Lead Rejected',
            "Lead {$this->label($lead)} was rejected: {$lead->rejection_reason}",
            $lead
        );
    }

    public function verificationUpdated(Lead $lead): void
    {
        $status = (int) $lead->approval_status === 4 ? 'Completed' : 'Unavailable';

        $this->push(
            array_merge([$lead->created_by], $this->idsWithPermission('leads.approve')),
            'Lead Verification Updated',
            "Lead {$this->label($lead)} was marked {$status}.",
            $lead
        );
    }

    public function commentAdded(Lead $lead, string $comment): void
    {
        $this->push(
            array_merge(
                [$lead->created_by, $lead->verifier_id],
                $this->idsWithPermission('leads.approve')
            ),
            'New Comment on Lead',
            "{$this->label($lead)}: " . \Str::limit($comment, 80),
            $lead
        );
    }

    /* ---------------- helpers ---------------- */

    protected function push(array $userIds, string $title, string $body, Lead $lead): void
    {
        try {
            $ids = collect($userIds)
                ->filter()
                ->map(fn($id) => (int) $id)
                ->unique()
                ->reject(fn($id) => $id === (int) auth()->id()) // don't notify the actor
                ->values();

            if ($ids->isEmpty()) return;

            $tokens = FcmToken::whereIn('user_id', $ids)
                ->where('last_active_at', '>=', now()->subMinutes(config('session.lifetime')))
                ->pluck('token')
                ->unique()
                ->toArray();

            if (empty($tokens)) return;

            $this->fcm->sendNotification($tokens, $title, $body, $lead->id, route('lead.show', $lead->id));
        } catch (\Throwable $e) {
            Log::error('Lead notification failed: ' . $e->getMessage());
        }
    }

    protected function idsWithPermission(string $permission): array
    {
        try {
            return User::all()
                ->filter(fn($user) => $user->hasAnyPermission([$permission]))
                ->pluck('id')
                ->toArray();
        } catch (\Throwable $e) {
            Log::error("Permission lookup failed for {$permission}: " . $e->getMessage());
            return [];
        }
    }

    protected function label(Lead $lead): string
    {
        return $lead->company_name ?: $lead->contact_person_name ?: ('#' . $lead->id);
    }
}
