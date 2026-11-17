<?php

namespace App\Http\Controllers\Pelapor;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Report;
use App\Models\Room;
use App\Services\ReportWorkflow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    public function __construct(private ReportWorkflow $workflow)
    {
    }

    /** Hanya laporan milik pengguna yang sedang login (laporan orang lain => 404). */
    private function own(Request $request, $id): Report
    {
        return $request->user()->reports()->findOrFail($id);
    }

    public function index(Request $request)
    {
        $reports = $request->user()->reports()
            ->with(['category', 'room'])
            ->filter($request->all())
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('pelapor.reports.index', [
            'reports' => $reports,
            'categories' => Category::orderBy('name')->get(),
            'rooms' => Room::orderBy('room_name')->get(),
        ]);
    }

    public function create()
    {
        return view('pelapor.reports.form', [
            'report' => new Report(),
            'categories' => Category::orderBy('name')->get(),
            'rooms' => Room::orderBy('room_name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request, true);

        $path = $request->file('photo')->store('reports', 'public');

        try {
            $report = DB::transaction(function () use ($request, $data, $path) {
                $report = Report::create([
                    'ticket_number' => Report::generateTicketNumber(),
                    'user_id' => $request->user()->id,
                    'category_id' => $data['category_id'],
                    'room_id' => $data['room_id'],
                    'title' => $data['title'],
                    'description' => $data['description'],
                    'photo' => $path,
                    'status' => Report::STATUS_MENUNGGU_VERIFIKASI,
                ]);

                $this->workflow->created($report, $request->user());

                return $report;
            });
        } catch (\Throwable $e) {
            Storage::disk('public')->delete($path);
            report($e);

            return back()->withInput()->with('error', 'Laporan gagal dikirim. Silakan coba lagi.');
        }

        \App\Services\ActivityLogger::log('buat', "Mengirim laporan {$report->ticket_number}.", $report);

        return redirect()->route('pelapor.reports.show', $report)
            ->with('success', "Laporan berhasil dikirim. Nomor tiket Anda: {$report->ticket_number}");
    }

    public function show(Request $request, $id)
    {
        $report = $this->own($request, $id);
        $report->load(['category', 'room', 'assignment.technician', 'repairLogs.technician', 'histories.user']);

        return view('pelapor.reports.show', compact('report'));
    }

    public function edit(Request $request, $id)
    {
        $report = $this->own($request, $id);

        if ($report->status !== Report::STATUS_MENUNGGU_VERIFIKASI) {
            return redirect()->route('pelapor.reports.show', $report)->with('error', 'Laporan yang sudah diproses admin tidak dapat diubah.');
        }

        return view('pelapor.reports.form', [
            'report' => $report,
            'categories' => Category::orderBy('name')->get(),
            'rooms' => Room::orderBy('room_name')->get(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $report = $this->own($request, $id);

        if ($report->status !== Report::STATUS_MENUNGGU_VERIFIKASI) {
            return redirect()->route('pelapor.reports.show', $report)->with('error', 'Laporan yang sudah diproses admin tidak dapat diubah.');
        }

        $data = $this->validated($request, false);

        if ($request->hasFile('photo')) {
            $new = $request->file('photo')->store('reports', 'public');
            Storage::disk('public')->delete($report->photo);
            $data['photo'] = $new;
        }

        unset($data['status']);
        $report->update($data);
        \App\Services\ActivityLogger::log('ubah', "Mengubah laporan {$report->ticket_number}.", $report);

        return redirect()->route('pelapor.reports.show', $report)->with('success', 'Laporan berhasil diperbarui.');
    }

    public function destroy(Request $request, $id)
    {
        $report = $this->own($request, $id);

        if ($report->status !== Report::STATUS_MENUNGGU_VERIFIKASI) {
            return back()->with('error', 'Hanya laporan yang belum diverifikasi yang dapat dibatalkan.');
        }

        $report->delete(); // soft delete
        \App\Services\ActivityLogger::log('hapus', "Membatalkan laporan {$report->ticket_number}.", $report);

        return redirect()->route('pelapor.reports.index')->with('success', 'Laporan dibatalkan.');
    }

    private function validated(Request $request, bool $photoRequired): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'category_id' => ['required', 'exists:categories,id,deleted_at,NULL'],
            'room_id' => ['required', 'exists:rooms,id,deleted_at,NULL'],
            'description' => ['required', 'string', 'min:10', 'max:2000'],
            'photo' => [$photoRequired ? 'required' : 'nullable', 'image', 'mimes:jpg,jpeg,png', 'mimetypes:image/jpeg,image/png', 'max:2048'],
        ]);
    }
}
