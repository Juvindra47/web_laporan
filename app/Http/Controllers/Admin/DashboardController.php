<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Report;
use App\Models\ReportStatusHistory;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $count = fn (string $status) => Report::where('status', $status)->count();

        $stats = [
            'total' => Report::count(),
            'menunggu' => $count(Report::STATUS_MENUNGGU_VERIFIKASI),
            'diterima' => $count(Report::STATUS_MENUNGGU_PETUGAS),
            'dikerjakan' => $count(Report::STATUS_SEDANG_DIKERJAKAN),
            'selesai' => $count(Report::STATUS_SELESAI),
        ];

        // Grafik per bulan: 12 bulan terakhir
        $start = now()->subMonths(11)->startOfMonth();
        $perMonth = Report::where('created_at', '>=', $start)->pluck('created_at')
            ->groupBy(fn ($d) => $d->format('Y-m'))
            ->map->count();

        $monthLabels = [];
        $monthData = [];
        for ($i = 0; $i < 12; $i++) {
            $m = $start->copy()->addMonths($i);
            $monthLabels[] = $m->translatedFormat('M Y');
            $monthData[] = $perMonth[$m->format('Y-m')] ?? 0;
        }

        // Grafik per kategori
        $categories = Category::withCount('reports')->orderByDesc('reports_count')->get();

        return view('admin.dashboard', [
            'stats' => $stats,
            'monthLabels' => $monthLabels,
            'monthData' => $monthData,
            'categoryLabels' => $categories->pluck('name'),
            'categoryData' => $categories->pluck('reports_count'),
            'recentReports' => Report::with(['reporter', 'category', 'room'])->latest()->take(6)->get(),
            'activities' => ReportStatusHistory::with(['report', 'user'])->latest('created_at')->latest('id')->take(8)->get(),
        ]);
    }
}
