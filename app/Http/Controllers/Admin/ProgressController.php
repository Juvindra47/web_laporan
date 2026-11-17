<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;

class ProgressController extends Controller
{
    public function index(Request $request)
    {
        $statuses = [Report::STATUS_MENUNGGU_PETUGAS, Report::STATUS_SEDANG_DIKERJAKAN, Report::STATUS_MENUNGGU_VERIFIKASI_HASIL];

        $reports = Report::with(['room', 'category', 'assignment.technician'])
            ->withCount('repairLogs')
            ->whereIn('status', $statuses)
            ->when($request->status && in_array($request->status, $statuses, true), fn ($q) => $q->where('status', $request->status))
            ->when($request->search, fn ($q, $s) => $q->where(fn ($w) => $w->where('ticket_number', 'like', "%{$s}%")->orWhere('title', 'like', "%{$s}%")))
            ->latest('updated_at')
            ->paginate(10)
            ->withQueryString();

        return view('admin.progress.index', compact('reports', 'statuses'));
    }
}
