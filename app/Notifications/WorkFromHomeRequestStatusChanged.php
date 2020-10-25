<?php

namespace App\Notifications;

use App\Models\WorkFromHome;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class WorkFromHomeRequestStatusChanged extends Notification
{
    use Queueable;

    /**
     * The WorkFromHome instance.
     *
     * @var User
     */
    public $request;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(WorkFromHome $workFromHomeRequest)
    {
        $this->request = $workFromHomeRequest;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->line('Status of your work from home request has been changed')
            ->line('For date: ' . $this->request->date)
            ->line('Status: ' . (bool) $this->request->approved)
            ->line('Thank you for using our application!');
    }
}
