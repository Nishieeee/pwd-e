<?php

namespace App\Notifications;

use App\Models\ApplicationMessage;
use App\Models\JobPosting;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class AccommodationMessageReceived extends Notification
{
    use Queueable;

    public function __construct(
        public ApplicationMessage $applicationMessage,
        public JobPosting $jobPosting,
        public User $sender,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $isEmployer = $notifiable->role === 'employer';
        $url = $isEmployer
            ? route('employer.applicants', ['job' => $this->jobPosting->id])
            : route('candidate.applications');

        return [
            'type' => 'accommodation_message',
            'application_id' => $this->applicationMessage->application_id,
            'job_posting_id' => $this->jobPosting->id,
            'job_title' => $this->jobPosting->title,
            'sender_id' => $this->sender->id,
            'sender_name' => $this->sender->name,
            'message_snippet' => Str::limit($this->applicationMessage->body, 60),
            'inquiry_type' => $this->applicationMessage->type,
            'action_url' => $url,
        ];
    }
}
