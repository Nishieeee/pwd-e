<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApplicationStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $jobTitle,
        public string $status,
        public ?string $note = null,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('Application update: '.$this->jobTitle)
            ->greeting('Hello '.$notifiable->name)
            ->line('Your application for '.$this->jobTitle.' is now '.str_replace('_', ' ', $this->status).'.');

        if ($this->note) {
            $mail->line('Note: '.$this->note);
        }

        return $mail;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'job_title' => $this->jobTitle,
            'status' => $this->status,
            'note' => $this->note,
        ];
    }
}