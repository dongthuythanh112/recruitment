<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('logo', 500)->nullable();
            $table->text('description')->nullable();
            $table->string('website', 500)->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('address', 500)->nullable();
            $table->string('company_size', 50)->nullable();
            $table->string('tax_code', 50)->nullable();
            $table->string('status', 30)->default('active');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
