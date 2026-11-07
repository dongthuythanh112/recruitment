<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('saved_jobs', function (Blueprint $table) {
            $table->foreignId('candidate_id')->constrained('candidate_profiles')->cascadeOnDelete();
            $table->foreignId('job_id')->index()->constrained('jobs')->cascadeOnDelete();
            $table->timestamp('created_at')->nullable();

            $table->primary(['candidate_id', 'job_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saved_jobs');
    }
};
