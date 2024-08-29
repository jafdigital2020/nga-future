<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AttendanceSubmissionNotification extends Notification
{
    use Queueable;

    protected $attendance;
    protected $employee;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($attendance, $employee)
    {
        $this->attendance = $attendance;
        $this->employee = $employee;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'mail'];  // Notify via both database and email
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
            ->subject('Attendance Submission by ' . $this->employee->name)
            ->line($this->employee->name . ' has submitted attendance for ' . $this->attendance->month)
            ->line('Worked Hours: ' . $this->attendance->totalHours)
            ->line('Late Hours: ' . $this->attendance->totalLate)
            ->line('Status: ' . $this->attendance->status)
            ->action('View Attendance', url('/attendance'))
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
            'message' => $this->employee->name . ' has submitted attendance for ' . $this->attendance->month,
            'employee_name' => $this->employee->name,
            'total_worked' => $this->attendance->totalHours,
            'total_late' => $this->attendance->totalLate,
            'cutoff' => $this->attendance->cut_off,
            'status' => $this->attendance->status,
        ];
    }
}
