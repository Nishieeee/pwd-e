<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmploymentSeparationNotice extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $companyName,
        public string $positionTitle,
        public string $reason,
        public string $effectiveDate,
        public ?string $notes = null,
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
        $reasonLabel = match ($this->reason) {
            'resigned' => 'Voluntary Resignation / Candidate Departure',
            'relieved' => 'Relieved / Employer-Initiated Separation',
            'contract_ended' => 'End of Fixed Employment Contract',
            default => 'Employment Separation',
        };

        $mail = (new MailMessage)
            ->subject("Notice of Employment Separation: {$this->companyName} ({$this->positionTitle})")
            ->greeting('Hello '.$notifiable->name)
            ->line("{$this->companyName} has recorded a formal Notice of Employment Separation for your role as {$this->positionTitle}.")
            ->line("Separation Reason: {$reasonLabel}")
            ->line("Effective End Date: {$this->effectiveDate}");

        if ($this->notes) {
            $mail->line("Employer Remarks: {$this->notes}");
        }

        $mail->line('Under Republic Act No. 7277 and Philippine Labor Standards, you have the right of due process. Please log in to your dashboard to either acknowledge this separation or file a formal dispute if this separation was unauthorized, unjust, or without due process.')
            ->action('Review & Respond on Dashboard', route('dashboard'));

        return $mail;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'employment_separation_notice',
            'company_name' => $this->companyName,
            'position_title' => $this->positionTitle,
            'reason' => $this->reason,
            'effective_date' => $this->effectiveDate,
            'notes' => $this->notes,
            'workforce_record_id' => $this->workforceRecordId,
            'action_url' => route('dashboard'),
        ];
    }
}
