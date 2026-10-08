<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class Stage4SubmittedNotification extends Notification
{
    use Queueable;

    public $studentName;
    public $chapterName;
    public $lessonProgressId;

    public function __construct($studentName, $chapterName, $lessonProgressId)
    {
        $this->studentName = $studentName;
        $this->chapterName = $chapterName;
        $this->lessonProgressId = $lessonProgressId;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'New Stage 4 Submission',
            'message' => "{$this->studentName} submitted answers for {$this->chapterName}.",
            'url' => route('admin.question_reviews.index', ['highlight' => $this->lessonProgressId]),
            'lesson_progress_id' => $this->lessonProgressId
        ];
    }
}
