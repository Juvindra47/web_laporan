<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('repair_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('reports')->cascadeOnDelete();
            $table->foreignId('technician_id')->constrained('users');
            $table->text('description');
            $table->string('before_photo')->nullable();
            $table->string('after_photo')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repair_logs');
    }
};
