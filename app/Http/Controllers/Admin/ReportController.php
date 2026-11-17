<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Category;
use App\Models\Report;
use App\Models\Room;
use App\Models\User;
use App\Services\ReportWorkflow;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ReportController extends Controller
{
    public function __construct(private ReportWorkflow $workflow)
    {
    }

    public function index(Request $request)
    {
        return $this->list($request, 'Data Laporan', 'admin.reports.index');
    }

    public function verification(Request $request)
    {
        // Menu "Verifikasi Laporan": laporan yang butuh keputusan admin
        $request->merge(['status' => $request->input('status', Report::STATUS_MENUNGGU_VERIFIKASI)]);

        return $this->list($request, 'Verifikasi Laporan', 'admin.verifications', [
            Report::STATUS_MENUNGGU_VERIFIKASI => Report::STATUS_LABELS[Report::STATUS_MENUNGGU_VERIFIKASI],
            Report::STATUS_MENUNGGU_VERIFIKASI_HASIL => Report::STATUS_LABELS[Report::STATUS_MENUNGGU_VERIFIKASI_HASIL],
        ]);
    }

    private function list(Request $request, string $title, string $routeName, ?array $statusOptions = null)
    {
        $reports = Report::with(['reporter', 'category', 'room', 'assignment.technician'])
            ->filter($request->all())
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.reports.index', [
            'reports' => $reports,
            'title' => $title,
            'routeName' => $routeName,
            'statusOptions' => $statusOptions ?? Report::STATUS_LABELS,
            'categories' => Category::orderBy('name')->get(),
            'rooms' => Room::orderBy('room_name')->get(),
        ]);
    }

    public function show(Report $report)
    {
        $report->load(['reporter', 'category', 'room', 'assignment.technician', 'assignment.assigner', 'repairLogs.technician', 'histories.user']);

        return view('admin.reports.show', [
            'report' => $report,
            'technicians' => User::where('role', User::ROLE_PETUGAS)->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function accept(Request $request, Report $report)
    {
        $data = $request->validate([
            'technician_id' => ['nullable', Rule::exists('users', 'id')->where('role', User::ROLE_PETUGAS)->where('is_active', true)->whereNull('deleted_at')],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $this->workflow->transition($report, Report::STATUS_MENUNGGU_PETUGAS, $request->user(), $data['notes'] ?? 'Laporan diterima admin.');

        if (! empty($data['technician_id'])) {
            $this->assign($report, (int) $data['technician_id'], $request->user());
        }

        return back()->with('success', 'Laporan diterima.' . (empty($data['technician_id']) ? ' Silakan tentukan petugas.' : ' Petugas sudah ditugaskan.'));
    }

    public function reject(Request $request, Report $report)
    {
        $data = $request->validate([
            'rejection_reason' => ['required', 'string', 'min:5', 'max:1000'],
        ]);

        $this->workflow->transition(
            $report,
            Report::STATUS_DITOLAK,
            $request->user(),
            'Ditolak: ' . $data['rejection_reason'],
            ['rejection_reason' => $data['rejection_reason']]
        );

        return back()->with('success', 'Laporan ditolak dan alasan sudah dikirim ke pelapor.');
    }

    public function approve(Request $request, Report $report)
    {
        $request->validate(['notes' => ['nullable', 'string', 'max:500']]);

        $this->workflow->transition($report, Report::STATUS_SELESAI, $request->user(), $request->input('notes') ?: 'Hasil perbaikan disetujui admin.');

        return back()->with('success', 'Hasil perbaikan disetujui. Laporan selesai.');
    }

    public function returnWork(Request $request, Report $report)
    {
        $data = $request->validate(['notes' => ['required', 'string', 'min:5', 'max:500']]);

        $this->workflow->transition($report, Report::STATUS_SEDANG_DIKERJAKAN, $request->user(), 'Dikembalikan ke petugas: ' . $data['notes']);

        return back()->with('success', 'Hasil dikembalikan ke petugas untuk diperbaiki lagi.');
    }

    public function destroy(Report $report)
    {
        // Hanya laporan final yang boleh dihapus (soft delete, data historis tetap tersimpan).
        if (! in_array($report->status, [Report::STATUS_DITOLAK, Report::STATUS_SELESAI], true)) {
            return back()->with('error', 'Hanya laporan berstatus Ditolak atau Selesai yang dapat dihapus.');
        }

        $report->delete();
        \App\Services\ActivityLogger::log('hapus', "Menghapus (mengarsipkan) laporan {$report->ticket_number}.", $report);

        return redirect()->route('admin.reports.index')->with('success', 'Laporan dihapus (diarsipkan).');
    }

    /** Dipakai juga oleh AssignmentController. */
    public function assign(Report $report, int $technicianId, User $admin): Assignment
    {
        $assignment = Assignment::updateOrCreate(
            ['report_id' => $report->id],
            ['technician_id' => $technicianId, 'assigned_by' => $admin->id, 'assigned_at' => now()]
        );

        $technician = User::find($technicianId);
        $this->workflow->note($report, $admin, 'Petugas ditugaskan: ' . $technician->name);
        $this->workflow->notifyUsers(collect([$technician]), $report, "Anda mendapat tugas baru: laporan {$report->ticket_number}.");

        return $assignment;
    }
}
