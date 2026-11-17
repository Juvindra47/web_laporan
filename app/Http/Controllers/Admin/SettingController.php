<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WebsiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class SettingController extends Controller
{
    public function edit()
    {
        return view('admin.settings.edit', ['setting' => WebsiteSetting::current()]);
    }

    public function update(Request $request)
    {
        $setting = WebsiteSetting::current();

        $data = $request->validate([
            'school_name' => ['required', 'string', 'max:150'],
            'school_address' => ['nullable', 'string', 'max:500'],
            'school_phone' => ['nullable', 'string', 'max:30'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'hero_type' => ['required', 'in:photo,video'],
            'hero_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'hero_video' => ['nullable', 'file', 'mimes:mp4', 'mimetypes:video/mp4', 'max:51200'],
            'hero_title' => ['required', 'string', 'max:200'],
            'hero_description' => ['nullable', 'string', 'max:600'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'google_maps_url' => ['nullable', 'url', 'max:2000'],
            'footer_text' => ['nullable', 'string', 'max:500'],
        ]);

        // Latitude & longitude harus diisi berpasangan
        if (($data['latitude'] ?? null) !== null xor ($data['longitude'] ?? null) !== null) {
            throw ValidationException::withMessages(['latitude' => 'Latitude dan longitude harus diisi bersamaan.']);
        }

        // File lama baru dihapus SETELAH file baru berhasil tersimpan.
        $toDelete = [];

        foreach (['logo' => 'branding', 'hero_photo' => 'hero', 'hero_video' => 'hero'] as $field => $folder) {
            if ($request->hasFile($field)) {
                $data[$field] = $request->file($field)->store($folder, 'public');
                $toDelete[] = $setting->{$field};
            } elseif ($request->boolean('remove_' . $field)) {
                $data[$field] = null;
                $toDelete[] = $setting->{$field};
            } else {
                unset($data[$field]);
            }
        }

        $finalPhoto = array_key_exists('hero_photo', $data) ? $data['hero_photo'] : $setting->hero_photo;
        $finalVideo = array_key_exists('hero_video', $data) ? $data['hero_video'] : $setting->hero_video;

        if ($data['hero_type'] === 'video' && ! $finalVideo) {
            // File baru yang sudah terlanjur tersimpan dibersihkan agar tidak menumpuk
            foreach (['logo', 'hero_photo', 'hero_video'] as $f) {
                if ($request->hasFile($f) && ! empty($data[$f])) {
                    Storage::disk('public')->delete($data[$f]);
                }
            }
            throw ValidationException::withMessages(['hero_video' => 'Unggah video MP4 terlebih dahulu jika memilih jenis media Video.']);
        }

        if ($data['hero_type'] === 'photo' && ! $finalPhoto) {
            // Tidak error: landing page memakai latar navy polos sampai foto diunggah.
        }

        $setting->update($data);
        \App\Services\ActivityLogger::log('ubah', 'Mengubah Pengaturan Website.', $setting);

        // Hapus file lama hanya jika tidak dipakai lagi oleh kolom lain
        $stillUsed = array_filter([$setting->logo, $setting->hero_photo, $setting->hero_video]);
        foreach (array_filter($toDelete) as $old) {
            if (! in_array($old, $stillUsed, true)) {
                Storage::disk('public')->delete($old);
            }
        }

        return back()->with('success', 'Pengaturan website berhasil disimpan dan langsung tampil di landing page.');
    }
}
