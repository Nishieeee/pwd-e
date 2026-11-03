<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_postings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employer_profile_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description');
            $table->json('required_skills')->nullable();
            $table->string('location')->nullable();
            $table->enum('work_arrangement', ['onsite', 'remote', 'hybrid'])->default('onsite');
            $table->enum('employment_type', ['full_time', 'part_time', 'contract'])->default('full_time');
            $table->decimal('salary_min', 12, 2)->nullable();
            $table->decimal('salary_max', 12, 2)->nullable();
            $table->date('closes_at')->nullable();
            $table->enum('status', ['draft', 'published', 'closed'])->default('draft');
            $table->json('scan_result')->nullable();
            $table->timestamp('scanned_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['status', 'location']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_postings');
    }
};