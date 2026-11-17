<?php

namespace App\Notifications;

use App\Models\Report;
use Illuminate\Notifications\Notification;

class ReportStatusChanged extends Notification
{
    public function __construct(public Report $report, public string $message)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $url = match ($notifiable->role) {
            'admin' => route('admin.reports.show', $this->report->id),
            'petugas' => route('petugas.tasks.show', $this->report->id),
            default => route('pelapor.reports.show', $this->report->id),
        };

        return [
            'report_id' => $this->report->id,
            'ticket' => $this->report->ticket_number,
            'message' => $this->message,
            'url' => $url,
        ];
    }
}
