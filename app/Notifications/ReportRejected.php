<?php

namespace App\Notifications;

use App\Models\Report;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReportRejected extends Notification implements ShouldQueue
{
    use Queueable;

    protected $report;

    public function __construct(Report $report)
    {
        $this->report = $report;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject(__('main.report_rejected_subject'))
            ->greeting(__('main.hello', ['name' => $notifiable->name]))
            ->line(__('main.report_rejected_notification'))
            ->line(__('main.report_reason', ['reason' => $this->report->reason]))
            ->when($this->report->moderator_notes, function ($message) {
                return $message->line(__('main.rejection_reason') . ': ' . $this->report->moderator_notes);
            })
            ->line(__('main.thank_you_for_reporting'));
    }

    public function toArray($notifiable)
    {
        return [
            'report_id' => $this->report->id,
            'type' => 'report_rejected',
            'message' => __('main.report_rejected_notification'),
            'reason' => $this->report->moderator_notes,
        ];
    }
} 