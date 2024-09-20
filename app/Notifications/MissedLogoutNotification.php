<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MissedLogoutNotification extends Notification
{
    use Queueable;

    protected $user;
    protected $date;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->user = $user;
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
            ->subject('Missed Logout Alert')
            ->line('User ' . $this->user->fName . ' ' . $this->user->lName . ' did not log out yesterday.')
            ->action('View Attendance', url('/attendance/' . $this->user->id));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'employee_name' => $this->user->fName . ' ' . $this->user->lName,
            'date' => $this->date,
            'image' => $this->user->image ?? 'default.png', 
            'missed_logout' => true, 
        ];
    }
}
