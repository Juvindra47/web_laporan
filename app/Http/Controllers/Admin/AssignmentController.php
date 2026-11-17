<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Report;
use App\Models\User;
use App\Services\ReportWorkflow;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AssignmentController extends Controller
{
    private const ASSIGNABLE = [Report::STATUS_MENUNGGU_PETUGAS, Report::STATUS_SEDANG_DIKERJAKAN];

    public function __construct(private ReportWorkflow $workflow)
    {
    }

    public function index(Request $request)
    {
        $reports = Report::with(['category', 'room', 'assignment.technician'])
            ->whereIn('status', [
                Report::STATUS_MENUNGGU_PETUGAS,
                Report::STATUS_SEDANG_DIKERJAKAN,
                Report::STATUS_MENUNGGU_VERIFIKASI_HASIL,
            ])
            ->when($request->search, fn ($q, $s) => $q->where(fn ($w) => $w->where('ticket_number', 'like', "%{$s}%")->orWhere('title', 'like', "%{$s}%")))
            ->when($request->technician_id, fn ($q, $t) => $q->whereHas('assignment', fn ($a) => $a->where('technician_id', $t)))
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.assignments.index', [
            'reports' => $reports,
            'technicians' => User::where('role', User::ROLE_PETUGAS)->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request, Report $report)
    {
        $data = $request->validate([
            'technician_id' => ['required', Rule::exists('users', 'id')->where('role', User::ROLE_PETUGAS)->where('is_active', true)->whereNull('deleted_at')],
        ]);

        if (! in_array($report->status, self::ASSIGNABLE, true)) {
            return back()->with('error', 'Petugas hanya dapat ditentukan untuk laporan yang sudah diterima dan belum selesai dikerjakan.');
        }

        app(ReportController::class)->assign($report, (int) $data['technician_id'], $request->user());

        return back()->with('success', 'Petugas berhasil ditugaskan.');
    }

    public function update(Request $request, Assignment $assignment)
    {
        $data = $request->validate([
            'technician_id' => ['required', Rule::exists('users', 'id')->where('role', User::ROLE_PETUGAS)->where('is_active', true)->whereNull('deleted_at')],
        ]);

        $report = $assignment->report;

        if (! in_array($report->status, self::ASSIGNABLE, true)) {
            return back()->with('error', 'Penugasan tidak dapat diubah pada status laporan saat ini.');
        }

        app(ReportController::class)->assign($report, (int) $data['technician_id'], $request->user());

        return back()->with('success', 'Penugasan petugas diperbarui.');
    }

    public function destroy(Request $request, Assignment $assignment)
    {
        $report = $assignment->report;

        if ($report->status !== Report::STATUS_MENUNGGU_PETUGAS) {
            return back()->with('error', 'Penugasan hanya bisa dibatalkan sebelum petugas mulai bekerja.');
        }

        $name = $assignment->technician->name;
        $assignment->delete();
        $this->workflow->note($report, $request->user(), 'Penugasan dibatalkan (sebelumnya: ' . $name . ').');

        return back()->with('success', 'Penugasan dibatalkan.');
    }
}
