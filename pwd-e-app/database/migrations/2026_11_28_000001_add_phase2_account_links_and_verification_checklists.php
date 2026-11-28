<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('verification_requests', function (Blueprint $table) {
            if (! Schema::hasColumn('verification_requests', 'verification_checklist')) {
                $table->json('verification_checklist')->nullable()->after('rejection_reason');
            }
        });

        if (! Schema::hasTable('account_links')) {
            Schema::create('account_links', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employer_user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('candidate_user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('linked_by_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->unique(['employer_user_id', 'candidate_user_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('account_links');

        Schema::table('verification_requests', function (Blueprint $table) {
            if (Schema::hasColumn('verification_requests', 'verification_checklist')) {
                $table->dropColumn('verification_checklist');
            }
        });
    }
};
