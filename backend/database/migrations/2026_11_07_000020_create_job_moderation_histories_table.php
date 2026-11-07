<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_moderation_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->index()->constrained('jobs')->cascadeOnDelete();
            $table->foreignId('admin_id')->index()->constrained('users')->restrictOnDelete();
            $table->string('action', 30); // approve, reject, hide, restore
            $table->text('note')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_moderation_histories');
    }
};
