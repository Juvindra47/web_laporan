<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('reports')->cascadeOnDelete();
            $table->string('old_status')->nullable();
            $table->string('new_status');
            $table->foreignId('changed_by')->constrained('users');
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_status_histories');
    }
};
