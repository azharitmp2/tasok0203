<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentVerified extends Notification
{
    use Queueable;

    public function __construct(public Student $student)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $graduation = $this->student->graduation;

        return (new MailMessage())
            ->subject('Your payment is verified — ' . $graduation->title)
            ->greeting("Hi {$this->student->name},")
            ->line("Your payment for **{$graduation->title}** has been received.")
            ->line('Ceremony date: ' . $graduation->ceremony_date->format('d M Y'))
            ->line('Fee paid: RM ' . number_format((float) $graduation->fee, 2))
            ->line('See you there!');
    }
}