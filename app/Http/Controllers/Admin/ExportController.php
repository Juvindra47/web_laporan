<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Report;
use App\Models\Room;
use App\Models\WebsiteSetting;
use App\Services\ActivityLogger;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ExportController extends Controller
{
    private const FILTER_KEYS = ['search', 'status', 'category_id', 'room_id', 'date_from', 'date_to'];

    private function query(Request $request): Builder
    {
        $filters = $request->only(self::FILTER_KEYS);

        return Report::with(['reporter', 'category', 'room', 'assignment.technician'])
            ->filter($filters)
            ->orderBy('created_at');
    }

    public function index(Request $request): View
    {
        return view('admin.exports.index', [
            'reports' => $this->query($request)->paginate(15)->withQueryString(),
            'categories' => Category::orderBy('name')->get(),
            'rooms' => Room::orderBy('room_name')->get(),
            'statusOptions' => Report::STATUS_LABELS,
        ]);
    }

    public function pdf(Request $request): Response
    {
        $filters = $request->only(self::FILTER_KEYS);
        $reports = $this->query($request)->limit(1000)->get();

        $pdf = Pdf::loadView('admin.exports.pdf', [
            'reports' => $reports,
            'setting' => WebsiteSetting::current(),
            'filters' => $filters,
            'categories' => Category::pluck('name', 'id'),
            'rooms' => Room::pluck('room_name', 'id'),
        ])->setPaper('a4', 'landscape');

        ActivityLogger::log('ekspor', 'Mengunduh laporan PDF ('.$reports->count().' laporan).');

        return $pdf->download('laporan-kerusakan-'.now()->format('Ymd-His').'.pdf');
    }
}
