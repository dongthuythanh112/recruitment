<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('candidate_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->string('avatar', 500)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('gender', 20)->nullable();
            $table->string('address', 500)->nullable();
            $table->text('summary')->nullable();
            $table->string('desired_position')->nullable();
            $table->decimal('desired_salary_min', 15, 2)->nullable();
            $table->decimal('desired_salary_max', 15, 2)->nullable();
            $table->decimal('years_of_experience', 4, 1)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidate_profiles');
    }
};
