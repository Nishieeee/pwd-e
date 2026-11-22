<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->foreignId('reapplied_from_id')->nullable()->after('job_posting_id')->constrained('applications')->nullOnDelete();
            $table->timestamp('offer_extended_at')->nullable()->after('offer_details');
            $table->timestamp('offer_accepted_at')->nullable()->after('offer_extended_at');
            $table->timestamp('offer_declined_at')->nullable()->after('offer_accepted_at');
            $table->text('offer_decline_reason')->nullable()->after('offer_declined_at');

            // Drop unique constraint to allow re-applying after withdrawal
            $table->dropUnique(['candidate_profile_id', 'job_posting_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropForeign(['reapplied_from_id']);
            $table->dropColumn([
                'reapplied_from_id',
                'offer_extended_at',
                'offer_accepted_at',
                'offer_declined_at',
                'offer_decline_reason',
            ]);
            $table->unique(['candidate_profile_id', 'job_posting_id']);
        });
    }
};
