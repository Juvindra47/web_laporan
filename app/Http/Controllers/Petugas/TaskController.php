<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\RepairLog;
use App\Models\Report;
use App\Services\ReportWorkflow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
    public function __construct(private ReportWorkflow $workflow)
    {
    }

    /** Query laporan yang ditugaskan ke petugas yang sedang login. */
    private function mine(Request $request)
    {
        $id = $request->user()->id;

        return Report::whereHas('assignment', fn ($q) => $q->where('technician_id', $id));
    }

    /** Petugas lain / laporan yang tidak ditugaskan => 404. */
    private function task(Request $request, $id): Report
    {
        return $this->mine($request)->findOrFail($id);
    }

    public function index(Request $request)
    {
        $tasks = $this->mine($request)
            ->with(['category', 'room'])
            ->where('status', '!=', Report::STATUS_SELESAI)
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->search, fn ($q, $s) => $q->where(fn ($w) => $w->where('ticket_number', 'like', "%{$s}%")->orWhere('title', 'like', "%{$s}%")))
            ->latest('updated_at')
            ->paginate(10)
            ->withQueryString();

        return view('petugas.tasks.index', ['tasks' => $tasks]);
    }

    public function history(Request $request)
    {
        $tasks = $this->mine($request)
            ->with(['category', 'room'])
            ->where('status', Report::STATUS_SELESAI)
            ->when($request->search, fn ($q, $s) => $q->where(fn ($w) => $w->where('ticket_number', 'like', "%{$s}%")->orWhere('title', 'like', "%{$s}%")))
            ->latest('updated_at')
            ->paginate(10)
            ->withQueryString();

        return view('petugas.tasks.history', ['tasks' => $tasks]);
    }

    public function show(Request $request, $id)
    {
        $report = $this->task($request, $id);
        $report->load(['category', 'room', 'reporter', 'assignment', 'repairLogs.technician', 'histories.user']);

        return view('petugas.tasks.show', compact('report'));
    }

    public function start(Request $request, $id)
    {
        $report = $this->task($request, $id);

        $this->workflow->transition($report, Report::STATUS_SEDANG_DIKERJAKAN, $request->user(), 'Petugas mulai mengerjakan perbaikan.');

        return back()->with('success', 'Pekerjaan dimulai. Status: Sedang Dikerjakan.');
    }

    public function submit(Request $request, $id)
    {
        $report = $this->task($request, $id);
        $request->validate(['notes' => ['nullable', 'string', 'max:500']]);

        if (! $report->repairLogs()->whereNotNull('after_photo')->exists()) {
            return back()->with('error', 'Tambahkan minimal satu catatan perbaikan dengan foto sesudah perbaikan sebelum mengirim hasil.');
        }

        $this->workflow->transition(
            $report,
            Report::STATUS_MENUNGGU_VERIFIKASI_HASIL,
            $request->user(),
            $request->input('notes') ?: 'Petugas mengirim hasil perbaikan untuk diverifikasi.'
        );

        return back()->with('success', 'Hasil perbaikan dikirim ke admin untuk diverifikasi.');
    }

    public function storeLog(Request $request, $id)
    {
        $report = $this->task($request, $id);

        if ($report->status !== Report::STATUS_SEDANG_DIKERJAKAN) {
            return back()->with('error', 'Catatan hanya dapat ditambahkan saat status Sedang Dikerjakan.');
        }

        $data = $this->validated($request);

        foreach (['before_photo', 'after_photo'] as $field) {
            $data[$field] = $request->hasFile($field) ? $request->file($field)->store('repairs', 'public') : null;
        }

        RepairLog::create($data + [
            'report_id' => $report->id,
            'technician_id' => $request->user()->id,
        ]);

        // Menyentuh updated_at agar tugas naik ke atas daftar
        $report->touch();

        return back()->with('success', 'Catatan perbaikan ditambahkan.');
    }

    public function updateLog(Request $request, RepairLog $log)
    {
        $this->authorizeLog($request, $log);
        $data = $this->validated($request);

        foreach (['before_photo', 'after_photo'] as $field) {
            if ($request->hasFile($field)) {
                $new = $request->file($field)->store('repairs', 'public');
                if ($log->{$field}) {
                    Storage::disk('public')->delete($log->{$field});
                }
                $data[$field] = $new;
            } else {
                unset($data[$field]);
            }
        }

        $log->update($data);

        return back()->with('success', 'Catatan perbaikan diperbarui.');
    }

    public function destroyLog(Request $request, RepairLog $log)
    {
        $this->authorizeLog($request, $log);
        $log->delete(); // soft delete, file foto dipertahankan untuk riwayat

        return back()->with('success', 'Catatan perbaikan dihapus.');
    }

    private function authorizeLog(Request $request, RepairLog $log): void
    {
        abort_unless($log->technician_id === $request->user()->id, 404);
        abort_unless($log->report->status === Report::STATUS_SEDANG_DIKERJAKAN, 403, 'Catatan tidak dapat diubah pada status laporan saat ini.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'description' => ['required', 'string', 'min:5', 'max:1500'],
            'before_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'mimetypes:image/jpeg,image/png', 'max:2048'],
            'after_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'mimetypes:image/jpeg,image/png', 'max:2048'],
        ]);
    }
}
