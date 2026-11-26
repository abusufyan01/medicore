<?php

declare(strict_types=1);

namespace App\Modules\Auth\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserAccountCreatedNotification extends Notification
{
    use Queueable;

    public function __construct(
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
            ->subject('Your MediCore HMS Account Has Been Created')
            ->line('An administrator has created an account for you on MediCore Hospital Management System.')
            ->line('Temporary Password: '.$this->temporaryPassword)
            ->action('Log In to Portal', rtrim($frontendUrl, '/').'/login')
            ->line('Please change your password after your first login.');
    }
}
