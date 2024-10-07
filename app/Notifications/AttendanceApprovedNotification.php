<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AttendanceApprovedNotification extends Notification
{
    use Queueable;

    protected $attendance;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($attendance)
    {
        $this->attendance = $attendance;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
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
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
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
            'message' => 'Your attendance for the cutoff period ' . $this->attendance->cut_off . ' has been approved.',
            'attendance_id' => $this->attendance->id,
            'cutoff' => $this->attendance->cut_off,
            'start_date' => $this->attendance->start_date,
            'end_date' => $this->attendance->end_date,
            'total_worked' => $this->attendance->totalHours,
            'total_late' => $this->attendance->totalLate,
        ];
    }
}
