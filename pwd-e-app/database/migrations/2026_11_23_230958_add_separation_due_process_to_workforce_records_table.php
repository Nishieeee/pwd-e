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
        Schema::table('workforce_records', function (Blueprint $table) {
            $table->string('separation_status')->default('active')->after('ended_notes');
            $table->timestamp('separation_notice_at')->nullable()->after('separation_status');
            $table->timestamp('candidate_response_at')->nullable()->after('separation_notice_at');
            $table->text('candidate_dispute_reason')->nullable()->after('candidate_response_at');
            $table->text('resolution_notes')->nullable()->after('candidate_dispute_reason');
            $table->timestamp('resolved_at')->nullable()->after('resolution_notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workforce_records', function (Blueprint $table) {
            $table->dropColumn([
                'separation_status',
                'separation_notice_at',
                'candidate_response_at',
                'candidate_dispute_reason',
                'resolution_notes',
                'resolved_at',
            ]);
        });
    }
};
