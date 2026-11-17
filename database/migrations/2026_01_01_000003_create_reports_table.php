<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('category_id')->constrained('categories');
            $table->foreignId('room_id')->constrained('rooms');
            $table->string('title');
            $table->text('description');
            $table->string('photo');
            $table->enum('status', [
                'menunggu_verifikasi',
                'ditolak',
                'menunggu_petugas',
                'sedang_dikerjakan',
                'menunggu_verifikasi_hasil',
                'selesai',
            ])->default('menunggu_verifikasi')->index();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
