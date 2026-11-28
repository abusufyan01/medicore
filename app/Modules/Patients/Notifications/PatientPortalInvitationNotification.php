<?php

declare(strict_types=1);

namespace App\Modules\Patients\Notifications;

use App\Modules\Patients\Models\Patient;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PatientPortalInvitationNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly Patient $patient,
        private readonly string $temporaryPassword,
    ) {}

    /**
     * @return list<string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        /** @var string $frontendUrl */
        $frontendUrl = config('app.frontend_url', 'http://localhost:3000');

        return (new MailMessage)
            ->subject('Your MediCore Patient Portal Invitation')
            ->line("Hello {$this->patient->name}, your MediCore Patient Portal account has been created for Patient Number {$this->patient->patient_number}.")
            ->line('Temporary Password: '.$this->temporaryPassword)
            ->action('Sign In to Patient Portal', rtrim($frontendUrl, '/').'/login')
            ->line('Please change your password after signing in.');
    }
}
