<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $id = $request->user()->id;
        $mine = fn () => Report::whereHas('assignment', fn ($q) => $q->where('technician_id', $id));

        $stats = [
            'total' => $mine()->count(),
            'baru' => $mine()->where('status', Report::STATUS_MENUNGGU_PETUGAS)->count(),
            'dikerjakan' => $mine()->where('status', Report::STATUS_SEDANG_DIKERJAKAN)->count(),
            'verifikasi' => $mine()->where('status', Report::STATUS_MENUNGGU_VERIFIKASI_HASIL)->count(),
            'selesai' => $mine()->where('status', Report::STATUS_SELESAI)->count(),
        ];

        return view('petugas.dashboard', [
            'stats' => $stats,
            'tasks' => $mine()->with(['category', 'room'])->where('status', '!=', Report::STATUS_SELESAI)->latest('updated_at')->take(6)->get(),
        ]);
    }
}
