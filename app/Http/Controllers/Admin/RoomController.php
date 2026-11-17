<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        $rooms = Room::withCount('reports')
            ->when($request->search, fn ($q, $s) => $q->where(fn ($w) => $w->where('room_name', 'like', "%{$s}%")->orWhere('building', 'like', "%{$s}%")))
            ->orderBy('room_name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.rooms.index', compact('rooms'));
    }

    public function create()
    {
        return view('admin.rooms.form', ['room' => new Room()]);
    }

    public function store(Request $request)
    {
        $room = Room::create($this->validated($request));
        \App\Services\ActivityLogger::log('buat', "Menambah ruangan {$room->room_name}.", $room);

        return redirect()->route('admin.rooms.index')->with('success', 'Ruangan berhasil ditambahkan.');
    }

    public function edit(Room $room)
    {
        return view('admin.rooms.form', compact('room'));
    }

    public function update(Request $request, Room $room)
    {
        $room->update($this->validated($request));
        \App\Services\ActivityLogger::log('ubah', "Mengubah ruangan {$room->room_name}.", $room);

        return redirect()->route('admin.rooms.index')->with('success', 'Ruangan berhasil diperbarui.');
    }

    public function destroy(Room $room)
    {
        if ($room->reports()->exists()) {
            return back()->with('error', 'Ruangan tidak dapat dihapus karena sudah dipakai oleh laporan.');
        }

        $room->delete();
        \App\Services\ActivityLogger::log('hapus', "Menghapus ruangan {$room->room_name}.", $room);

        return redirect()->route('admin.rooms.index')->with('success', 'Ruangan dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'room_name' => ['required', 'string', 'max:100'],
            'building' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);
    }
}
