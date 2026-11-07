<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained('jobs')->restrictOnDelete();
            $table->foreignId('candidate_id')->index()->constrained('candidate_profiles')->restrictOnDelete();
            $table->foreignId('resume_id')->index()->constrained('resumes')->restrictOnDelete();
            $table->text('cover_letter')->nullable();
            $table->string('status', 30)->default('pending'); // pending, reviewing, shortlisted, interview, offered, hired, rejected, withdrawn
            $table->timestamp('applied_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();

            $table->unique(['job_id', 'candidate_id']);
            $table->index(['job_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
