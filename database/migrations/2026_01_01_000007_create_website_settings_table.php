<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('website_settings', function (Blueprint $table) {
            $table->id();
            $table->string('school_name')->default("SMK Ma'arif NU 1 Ajibarang");
            $table->text('school_address')->nullable();
            $table->string('school_phone', 30)->nullable();
            $table->string('logo')->nullable();
            $table->enum('hero_type', ['photo', 'video'])->default('photo');
            $table->string('hero_photo')->nullable();   // sekaligus fallback jika video gagal diputar
            $table->string('hero_video')->nullable();
            $table->string('hero_title')->default('Fasilitas Sekolah Terawat, Lingkungan Belajar Nyaman.');
            $table->text('hero_description')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->text('google_maps_url')->nullable();
            $table->text('footer_text')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('website_settings');
    }
};
