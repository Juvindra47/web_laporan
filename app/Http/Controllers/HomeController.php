<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\WebsiteSetting;

class HomeController extends Controller
{
    public function index()
    {
        $totalsByStatus = Report::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $stats = [
            'total' => (int) $totalsByStatus->sum(),
            'menunggu' => (int) ($totalsByStatus[Report::STATUS_MENUNGGU_VERIFIKASI] ?? 0),
            'menunggu_petugas' => (int) ($totalsByStatus[Report::STATUS_MENUNGGU_PETUGAS] ?? 0),
            'dikerjakan' => (int) ($totalsByStatus[Report::STATUS_SEDANG_DIKERJAKAN] ?? 0),
            'selesai' => (int) ($totalsByStatus[Report::STATUS_SELESAI] ?? 0),
        ];

        $recentReports = Report::with(['reporter', 'category', 'room', 'assignment'])
            ->where('status', '!=', Report::STATUS_DITOLAK)
            ->latest()
            ->take(9)
            ->get();

        return view('landing', [
            'setting' => WebsiteSetting::current(),
            'stats' => $stats,
            'recentReports' => $recentReports,
            'statusCounts' => $recentReports->countBy('status'),
        ]);
    }
}
