<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('candidate_skills', function (Blueprint $table) {
            $table->foreignId('candidate_id')->constrained('candidate_profiles')->cascadeOnDelete();
            $table->foreignId('skill_id')->index()->constrained('skills')->cascadeOnDelete();
            $table->string('level', 50)->nullable();
            $table->timestamp('created_at')->nullable();

            $table->primary(['candidate_id', 'skill_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidate_skills');
    }
};
