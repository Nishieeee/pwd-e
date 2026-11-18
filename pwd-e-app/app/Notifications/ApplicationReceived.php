<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApplicationReceived extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $candidateName,
        public string $jobTitle,
        public ?string $applicationUrl = null,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('New application received: '.$this->jobTitle)
            ->greeting('Hello '.$notifiable->name)
            ->line($this->candidateName.' has applied for '.$this->jobTitle.'.');

        if ($this->applicationUrl) {
            $mail->action('Review application', $this->applicationUrl);
        }

        return $mail;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'candidate_name' => $this->candidateName,
            'job_title' => $this->jobTitle,
            'application_url' => $this->applicationUrl,
        ];
    }
}