<?php

namespace App\Notifications;

use App\Models\JobPosting;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class JobInvitationReceived extends Notification
{
    use Queueable;

    public function __construct(
        public JobPosting $jobPosting,
        public string $employerCompanyName,
        public ?string $personalMessage = null,
        public ?int $jobInvitationId = null,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('Invitation to Apply: ' . $this->jobPosting->title . ' at ' . $this->employerCompanyName)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line($this->employerCompanyName . ' has reviewed your profile on PWD-E and invited you to apply for ' . $this->jobPosting->title . '.');

        if ($this->personalMessage) {
            $mail->line('Message from Employer: "' . $this->personalMessage . '"');
        }

        return $mail->action('View Position & Apply', route('candidate.apply', $this->jobPosting));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'job_invitation',
            'job_invitation_id' => $this->jobInvitationId,
            'job_posting_id' => $this->jobPosting->id,
            'job_title' => $this->jobPosting->title,
            'employer_name' => $this->employerCompanyName,
            'personal_message' => $this->personalMessage,
            'action_url' => route('candidate.apply', $this->jobPosting),
        ];
    }
}
