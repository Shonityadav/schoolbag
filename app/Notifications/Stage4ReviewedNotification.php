<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\LessonProgress;

class Stage4ReviewedNotification extends Notification
{
    use Queueable;

    public $progress;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(LessonProgress $progress)
    {
        $this->progress = $progress;
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
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $chapterNumber = $this->progress->chapter->chapter_number ?? 'N/A';
        $chapterName = $this->progress->chapter->chapter_name ?? 'N/A';
        
        $assignedEbook = \App\Models\AssignedEbook::where('user_id', $this->progress->user_id)
                                                  ->where('ebook_id', $this->progress->ebook_id)
                                                  ->first();
        $link = $assignedEbook ? route('student.assigned_ebooks.show', $assignedEbook->id) : route('student.ebooks');

        return [
            'title' => 'Stage 4 Reviewed!',
            'message' => "Your answers for Chapter {$chapterNumber} ({$chapterName}) Stage 4 have been reviewed. You scored {$this->progress->score} XP.",
            'icon' => '⭐',
            'link' => $link
        ];
    }
}
