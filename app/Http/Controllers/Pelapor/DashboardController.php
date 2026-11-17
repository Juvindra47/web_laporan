<?php

namespace App\Http\Controllers\Pelapor;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();
        $mine = fn () => Report::where('user_id', $user->id);

        $stats = [
            'total' => $mine()->count(),
            'menunggu' => $mine()->where('status', Report::STATUS_MENUNGGU_VERIFIKASI)->count(),
            // "Diproses" = sudah diterima admin dan belum selesai
            'diproses' => $mine()->whereIn('status', [
                Report::STATUS_MENUNGGU_PETUGAS,
                Report::STATUS_SEDANG_DIKERJAKAN,
                Report::STATUS_MENUNGGU_VERIFIKASI_HASIL,
            ])->count(),
            'selesai' => $mine()->where('status', Report::STATUS_SELESAI)->count(),
        ];

        return view('pelapor.dashboard', [
            'stats' => $stats,
            'recentReports' => $mine()->with(['category', 'room'])->latest()->take(6)->get(),
        ]);
    }
}
