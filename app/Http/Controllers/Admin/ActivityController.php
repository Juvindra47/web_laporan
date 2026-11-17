<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Report;
use App\Models\ReportStatusHistory;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->input('tab') === 'laporan' ? 'laporan' : 'pengguna';

        $activities = null;
        $logs = null;

        if ($tab === 'laporan') {
            // Riwayat perubahan status laporan (termasuk penugasan petugas)
            $activities = ReportStatusHistory::with(['report', 'user'])
                ->when($request->search, function ($q, $s) {
                    $q->where(function ($w) use ($s) {
                        $w->whereHas('report', fn ($r) => $r->withTrashed()->where('ticket_number', 'like', "%{$s}%"))
                            ->orWhereHas('user', fn ($u) => $u->withTrashed()->where('name', 'like', "%{$s}%"))
                            ->orWhere('notes', 'like', "%{$s}%");
                    });
                })
                ->when($request->status, fn ($q, $s) => $q->where('new_status', $s))
                ->when($request->date_from, fn ($q, $d) => $q->whereDate('created_at', '>=', $d))
                ->when($request->date_to, fn ($q, $d) => $q->whereDate('created_at', '<=', $d))
                ->latest('created_at')->latest('id')
                ->paginate(15)
                ->withQueryString();
        } else {
            // Aktivitas pengguna: login, logout, tambah/ubah/hapus data, cetak
            $logs = ActivityLog::with('user')
                ->when($request->search, function ($q, $s) {
                    $q->where(function ($w) use ($s) {
                        $w->where('description', 'like', "%{$s}%")
                            ->orWhereHas('user', fn ($u) => $u->withTrashed()->where('name', 'like', "%{$s}%"));
                    });
                })
                ->when($request->action, fn ($q, $a) => $q->where('action', $a))
                ->when($request->date_from, fn ($q, $d) => $q->whereDate('created_at', '>=', $d))
                ->when($request->date_to, fn ($q, $d) => $q->whereDate('created_at', '<=', $d))
                ->latest('created_at')->latest('id')
                ->paginate(15)
                ->withQueryString();
        }

        return view('admin.activities.index', [
            'tab' => $tab,
            'activities' => $activities,
            'logs' => $logs,
            'statusOptions' => Report::STATUS_LABELS,
            'actionOptions' => ActivityLog::ACTION_LABELS,
        ]);
    }
}
