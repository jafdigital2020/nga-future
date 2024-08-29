<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LeaveRequestNotification extends Notification
{
    use Queueable;

    protected $leaveRequest;
    protected $employee;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($leaveRequest, $employee)
    {
        $this->leaveRequest = $leaveRequest;  // Assign the leave request to the class property
        $this->employee = $employee;  // Assign the employee to the class property
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
            ->subject('New Leave Request from ' . $this->employee->name)
            ->line($this->employee->name . ' has requested a ' . $this->leaveRequest->type . '.')
            ->line('From: ' . $this->leaveRequest->start_date . ' to ' . $this->leaveRequest->end_date)
            ->line('Leave type: ' . $this->leaveRequest->type)
            ->line('Status: Pending')
            ->action('View Leave Request', url('/'))
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
            'message' => $this->employee->name . ' has requested a ' . $this->leaveRequest->type . ' leave from ' . $this->leaveRequest->start_date . ' to ' . $this->leaveRequest->end_date,
            'employee_name' => $this->employee->name,
            'leave_type' => $this->leaveRequest->type,
            'start_date' => $this->leaveRequest->start_date,
            'end_date' => $this->leaveRequest->end_date,
            'status' => 'Pending',
        ];
    }
}
