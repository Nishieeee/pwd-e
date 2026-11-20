<?php

namespace App\Notifications;

use App\Models\JobPosting;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class JobInvitationWithdrawn extends Notification
{
    use Queueable;

    public function __construct(
        public JobPosting $jobPosting,
        public string $employerCompanyName,
        public string $withdrawnReason,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Update: Job Invitation Withdrawn by ' . $this->employerCompanyName)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line($this->employerCompanyName . ' has withdrawn their invitation for ' . $this->jobPosting->title . '.')
            ->line('Reason provided: "' . $this->withdrawnReason . '"')
            ->line('You can continue discovering other inclusive opportunities on PWD-E.')
            ->action('Browse Job Postings', route('candidate.jobs'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'job_invitation_withdrawn',
            'job_posting_id' => $this->jobPosting->id,
            'job_title' => $this->jobPosting->title,
            'employer_name' => $this->employerCompanyName,
            'withdrawn_reason' => $this->withdrawnReason,
        ];
    }
}
