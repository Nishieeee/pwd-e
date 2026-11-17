<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Enhance job_postings with rich posting content from Figma
        Schema::table('job_postings', function (Blueprint $table) {
            $table->string('overview_headline')->nullable()->after('description');
            $table->string('experience_level')->default('Entry level')->after('employment_type');
            $table->json('what_you_will_do')->nullable()->after('overview_headline');
            $table->json('what_you_will_bring')->nullable()->after('what_you_will_do');
            $table->json('benefits')->nullable()->after('what_you_will_bring');
        });

        // 2. Enhance job_accommodations with matrix metadata (scope, description, availability)
        Schema::table('job_accommodations', function (Blueprint $table) {
            $table->string('scope')->nullable()->after('accommodation_id');
            $table->text('description')->nullable()->after('scope');
            $table->string('availability')->default('+ Guaranteed')->after('description');
        });

        // 3. Relax or expand applications status and add interview & tracking fields
        // In PostgreSQL, drop any check constraints on applications.status if they exist
        try {
            DB::statement('ALTER TABLE applications DROP CONSTRAINT IF EXISTS applications_status_check');
        } catch (\Throwable $e) {
            // Ignore if constraint doesn't exist
        }

        Schema::table('applications', function (Blueprint $table) {
            $table->string('status', 50)->change();
            $table->string('status_subtext')->nullable()->after('status');
            $table->timestamp('interview_at')->nullable()->after('status_subtext');
            $table->string('interview_location')->nullable()->after('interview_at');
            $table->string('interview_url')->nullable()->after('interview_location');
            $table->text('interview_accommodations')->nullable()->after('interview_url');
            $table->json('offer_details')->nullable()->after('employer_notes');
        });

        // 4. Add education / alumni badge to candidate_profiles
        Schema::table('candidate_profiles', function (Blueprint $table) {
            $table->string('education_badge')->nullable()->after('headline');
        });
    }

    public function down(): void
    {
        Schema::table('job_postings', function (Blueprint $table) {
            $table->dropColumn([
                'overview_headline',
                'experience_level',
                'what_you_will_do',
                'what_you_will_bring',
                'benefits',
            ]);
        });

        Schema::table('job_accommodations', function (Blueprint $table) {
            $table->dropColumn(['scope', 'description', 'availability']);
        });

        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn([
                'status_subtext',
                'interview_at',
                'interview_location',
                'interview_url',
                'interview_accommodations',
                'offer_details',
            ]);
        });

        Schema::table('candidate_profiles', function (Blueprint $table) {
            $table->dropColumn(['education_badge']);
        });
    }
};

