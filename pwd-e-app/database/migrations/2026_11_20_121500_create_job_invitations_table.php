<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employer_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('job_posting_id')->constrained()->cascadeOnDelete();
            $table->foreignId('candidate_profile_id')->constrained()->cascadeOnDelete();
            $table->string('status', 30)->default('pending'); // pending, accepted, declined, withdrawn
            $table->text('personal_message')->nullable();
            $table->text('withdrawn_reason')->nullable();
            $table->timestamp('withdrawn_at')->nullable();
            $table->timestamps();

            $table->index(['employer_profile_id', 'status']);
            $table->index(['candidate_profile_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_invitations');
    }
};
