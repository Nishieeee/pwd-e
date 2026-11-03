<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('job_posting_id')->constrained()->cascadeOnDelete();
            $table->decimal('accessibility_score', 5, 2)->default(0);
            $table->decimal('skills_score', 5, 2)->default(0);
            $table->decimal('total_score', 5, 2)->default(0);
            $table->timestamp('computed_at')->nullable();
            $table->timestamps();

            $table->unique(['candidate_profile_id', 'job_posting_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('matches');
    }
};