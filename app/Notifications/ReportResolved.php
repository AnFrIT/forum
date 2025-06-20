<?php

namespace App\Notifications;

use App\Models\Report;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReportResolved extends Notification implements ShouldQueue
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
        $url = $this->getReportableUrl();

        return (new MailMessage)
            ->subject(__('main.report_resolved_subject'))
            ->greeting(__('main.hello', ['name' => $notifiable->name]))
            ->line(__('main.report_resolved_notification'))
            ->line(__('main.report_reason', ['reason' => $this->report->reason]))
            ->when($this->report->moderator_notes, function ($message) {
                return $message->line(__('main.moderator_response') . ': ' . $this->report->moderator_notes);
            })
            ->when($url, function ($message) use ($url) {
                return $message->action(__('main.view_content'), $url);
            })
            ->line(__('main.thank_you_for_reporting'));
    }

    public function toArray($notifiable)
    {
        return [
            'report_id' => $this->report->id,
            'type' => 'report_resolved',
            'message' => __('main.report_resolved_notification'),
            'url' => $this->getReportableUrl(),
        ];
    }

    protected function getReportableUrl()
    {
        if (!$this->report->reportable) {
            return null;
        }

        if ($this->report->reportable instanceof \App\Models\Post) {
            return route('topics.show', [
                'topic' => $this->report->reportable->topic_id,
                'post' => $this->report->reportable->id
            ]) . '#post-' . $this->report->reportable->id;
        }

        if ($this->report->reportable instanceof \App\Models\Topic) {
            return route('topics.show', $this->report->reportable);
        }

        return null;
    }
} 