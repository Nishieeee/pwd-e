<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmploymentSeparationResponse extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $candidateName,
        public string $positionTitle,
        public string $responseType, // 'acknowledged' or 'disputed'
        public ?string $disputeReason = null,
        public ?int $workforceRecordId = null,
    ) {}

    public function via(object $notifiable): array
    {
        $channels = ['database'];

        $pref = method_exists($notifiable, 'notificationPreferences')
            ? $notifiable->notificationPreferences()->where('event_type', 'application_status')->first()
            : null;

        if ($pref === null || $pref->email_enabled) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->greeting('Hello '.$notifiable->name);

        if ($this->responseType === 'disputed') {
            $mail->subject("DISPUTE FILED: Employment Separation Contested by {$this->candidateName} ({$this->positionTitle})")
                ->error()
                ->line("{$this->candidateName} has formally CONTESTED the employment separation for the position {$this->positionTitle}.")
                ->line("Candidate's Statement of Dispute: {$this->disputeReason}")
                ->line('Under DOLE and RA 7277 regulations, this record has been flagged for compliance review. Unilateral tax incentive adjustments are held pending dispute resolution.')
                ->action('Review Workforce Record', route('employer.workforce'));
        } else {
            $mail->subject("Separation Confirmed: {$this->candidateName} ({$this->positionTitle})")
                ->line("{$this->candidateName} has acknowledged and confirmed the employment separation for the position {$this->positionTitle}.")
                ->action('View Workforce Roster', route('employer.workforce'));
        }

        return $mail;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'employment_separation_response',
            'candidate_name' => $this->candidateName,
            'position_title' => $this->positionTitle,
            'response_type' => $this->responseType,
            'dispute_reason' => $this->disputeReason,
            'workforce_record_id' => $this->workforceRecordId,
            'action_url' => route('employer.workforce'),
        ];
    }
}
