<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->index()->constrained('companies')->restrictOnDelete();
            $table->foreignId('created_by')->index()->constrained('users')->restrictOnDelete();
            $table->foreignId('industry_id')->index()->constrained('industries')->restrictOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->text('requirements')->nullable();
            $table->text('benefits')->nullable();
            $table->string('employment_type', 30)->nullable();
            $table->string('workplace_type', 30)->nullable();
            $table->string('location', 500)->nullable();
            $table->decimal('salary_min', 15, 2)->nullable();
            $table->decimal('salary_max', 15, 2)->nullable();
            $table->string('salary_currency', 10)->nullable()->default('VND');
            $table->decimal('experience_years', 4, 1)->nullable();
            $table->integer('quantity')->default(1);
            $table->date('deadline')->nullable()->index();
            $table->string('status', 30)->default('draft')->index(); // draft, pending, approved, rejected, hidden, closed
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'industry_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};
