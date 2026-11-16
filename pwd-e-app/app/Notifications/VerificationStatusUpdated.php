<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerificationStatusUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $status,
        public ?string $reason = null,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $subject = match ($this->status) {
            'approved' => 'Your account has been verified',
            'rejected' => 'Your verification request was rejected',
            'needs_more_docs' => 'Additional documents requested',
            default => 'Verification status update',
        };

        $mail = (new MailMessage)->subject($subject)->greeting('Hello '.$notifiable->name);

        match ($this->status) {
            'approved' => $mail->line('Your account is now active. You can sign in and use the platform.'),
            'rejected' => null,
            'needs_more_docs' => null,
            default => $mail->line('Your verification status has been updated.'),
        };

        if ($this->status === 'rejected') {
            $mail->line('Your verification request was rejected.');
            if ($this->reason) {
                $mail->line('Reason: '.$this->reason);
            }
        }

        if ($this->status === 'needs_more_docs') {
            $mail->line('We need additional documents to verify your account.');
            if ($this->reason) {
                $mail->line('Details: '.$this->reason);
            }
        }

        return $mail;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'status' => $this->status,
            'reason' => $this->reason,
        ];
    }
}
