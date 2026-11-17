<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebsiteSetting extends Model
{
    protected $fillable = [
        'school_name', 'school_address', 'school_phone', 'logo',
        'hero_type', 'hero_photo', 'hero_video', 'hero_title', 'hero_description',
        'latitude', 'longitude', 'google_maps_url', 'footer_text',
    ];

    /** Pengaturan hanya punya satu baris; dibuat otomatis jika belum ada. */
    public static function current(): self
    {
        return static::query()->first() ?? static::create([]);
    }

    private function url(?string $path): ?string
    {
        return $path ? asset('storage/' . $path) : null;
    }

    public function getLogoUrlAttribute(): ?string      { return $this->url($this->logo); }
    public function getHeroPhotoUrlAttribute(): ?string { return $this->url($this->hero_photo); }
    public function getHeroVideoUrlAttribute(): ?string { return $this->url($this->hero_video); }

    public function hasCoordinates(): bool
    {
        return $this->latitude !== null && $this->longitude !== null;
    }

    /** URL iframe peta: embed Google jika admin memasukkan link embed, selain itu OpenStreetMap dari koordinat. */
    public function getMapEmbedUrlAttribute(): ?string
    {
        if ($this->google_maps_url && str_contains($this->google_maps_url, 'google.com/maps/embed')) {
            return $this->google_maps_url;
        }

        if ($this->hasCoordinates()) {
            $lat = (float) $this->latitude;
            $lng = (float) $this->longitude;
            $bbox = implode('%2C', [$lng - 0.005, $lat - 0.003, $lng + 0.005, $lat + 0.003]);

            return "https://www.openstreetmap.org/export/embed.html?bbox={$bbox}&layer=mapnik&marker={$lat}%2C{$lng}";
        }

        return null;
    }

    public function getDirectionsUrlAttribute(): ?string
    {
        if ($this->hasCoordinates()) {
            return "https://www.google.com/maps/dir/?api=1&destination={$this->latitude},{$this->longitude}";
        }

        return $this->google_maps_url && ! str_contains($this->google_maps_url, '/maps/embed') ? $this->google_maps_url : null;
    }

    public function getOpenMapsUrlAttribute(): ?string
    {
        if ($this->google_maps_url && ! str_contains($this->google_maps_url, '/maps/embed')) {
            return $this->google_maps_url;
        }

        return $this->hasCoordinates()
            ? "https://www.google.com/maps/search/?api=1&query={$this->latitude},{$this->longitude}"
            : null;
    }
}
