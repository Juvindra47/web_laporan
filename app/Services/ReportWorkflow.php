<?php

namespace App\Services;

use App\Models\Report;
use App\Models\ReportStatusHistory;
use App\Models\User;
use App\Notifications\ReportStatusChanged;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;

/**
 * Satu-satunya pintu untuk mengubah status laporan.
 * Memvalidasi alur + hak akses, mencatat riwayat, dan mengirim notifikasi.
 */
class ReportWorkflow
{
    public function transition(Report $report, string $newStatus, User $actor, ?string $notes = null, array $extra = []): Report
    {
        if (! $report->canTransitionTo($newStatus, $actor)) {
            throw ValidationException::withMessages([
                'status' => 'Perubahan status tidak diizinkan untuk laporan ini atau untuk peran Anda.',
            ]);
        }

        return DB::transaction(function () use ($report, $newStatus, $actor, $notes, $extra) {
            $old = $report->status;

            $report->fill(array_merge(['status' => $newStatus], $extra))->save();

            ReportStatusHistory::create([
                'report_id' => $report->id,
                'old_status' => $old,
                'new_status' => $newStatus,
                'changed_by' => $actor->id,
                'notes' => $notes,
            ]);

            $this->notifyStatus($report, $actor);

            return $report;
        });
    }

    /** Dicatat saat laporan pertama kali dibuat. */
    public function created(Report $report, User $actor): void
    {
        ReportStatusHistory::create([
            'report_id' => $report->id,
            'old_status' => null,
            'new_status' => $report->status,
            'changed_by' => $actor->id,
            'notes' => 'Laporan dibuat.',
        ]);

        $this->notifyUsers(
            User::where('role', User::ROLE_ADMIN)->where('is_active', true)->get(),
            $report,
            "Laporan baru {$report->ticket_number} menunggu verifikasi."
        );
    }

    /** Mencatat kejadian tanpa perubahan status (mis. penugasan petugas). */
    public function note(Report $report, User $actor, string $notes): void
    {
        ReportStatusHistory::create([
            'report_id' => $report->id,
            'old_status' => $report->status,
            'new_status' => $report->status,
            'changed_by' => $actor->id,
            'notes' => $notes,
        ]);
    }

    public function notifyUsers($users, Report $report, string $message): void
    {
        if ($users->isNotEmpty()) {
            Notification::send($users, new ReportStatusChanged($report, $message));
        }
    }

    private function notifyStatus(Report $report, User $actor): void
    {
        $message = "Status laporan {$report->ticket_number} berubah menjadi {$report->status_label}.";

        $recipients = collect();
        $recipients->push($report->reporter);

        if ($report->assignment && $report->assignment->technician) {
            $recipients->push($report->assignment->technician);
        }

        if ($report->status === Report::STATUS_MENUNGGU_VERIFIKASI_HASIL) {
            $recipients = $recipients->merge(User::where('role', User::ROLE_ADMIN)->where('is_active', true)->get());
        }

        $recipients = $recipients->filter()->unique('id')->reject(fn ($u) => $u->id === $actor->id);

        $this->notifyUsers($recipients, $report, $message);
    }
}
