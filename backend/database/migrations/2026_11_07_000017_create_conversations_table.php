<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->index()->constrained('candidate_profiles')->cascadeOnDelete();
            $table->foreignId('recruiter_id')->index()->constrained('recruiter_profiles')->cascadeOnDelete();
            $table->foreignId('job_id')->nullable()->index()->constrained('jobs')->nullOnDelete();
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
