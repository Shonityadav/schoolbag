<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;

class AttendanceRequested extends Notification
{
    use Queueable;

    public $student;
    public $date;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $student, $date)
    {
        $this->student = $student;
        $this->date = $date;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'student_id' => $this->student->id,
            'student_name' => $this->student->name,
            'class_id' => $this->student->class_id,
            'date' => $this->date,
            'message' => "{$this->student->name} requested attendance for {$this->date}",
            'action_url' => route('admin.attendance.index', ['class_id' => $this->student->class_id, 'date' => $this->date, 'user_type' => 3])
        ];
    }
}
