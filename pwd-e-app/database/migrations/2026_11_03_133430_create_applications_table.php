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
            $table->foreignId('candidate_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('job_posting_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['applied', 'reviewed', 'shortlisted', 'hired', 'rejected', 'withdrawn'])->default('applied');
            $table->string('fsl_video_path')->nullable();
            $table->string('fsl_caption_path')->nullable();
            $table->decimal('fit_score_at_apply', 5, 2)->nullable();
            $table->timestamp('applied_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->unique(['candidate_profile_id', 'job_posting_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};