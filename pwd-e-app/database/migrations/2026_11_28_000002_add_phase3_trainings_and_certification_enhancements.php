<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('candidate_trainings')) {
            Schema::create('candidate_trainings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('candidate_profile_id')->constrained()->cascadeOnDelete();
                $table->string('title');
                $table->string('organizer')->nullable();
                $table->string('year_completed', 10)->nullable();
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        Schema::table('candidate_certifications', function (Blueprint $table) {
            if (! Schema::hasColumn('candidate_certifications', 'category')) {
                $table->string('category', 40)->default('certification')->after('candidate_profile_id');
            }
            if (! Schema::hasColumn('candidate_certifications', 'year_obtained')) {
                $table->string('year_obtained', 10)->nullable()->after('issuing_organization');
            }
            if (! Schema::hasColumn('candidate_certifications', 'file_path')) {
                $table->string('file_path')->nullable()->after('credential_url');
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidate_trainings');

        Schema::table('candidate_certifications', function (Blueprint $table) {
            foreach (['category', 'year_obtained'] as $col) {
                if (Schema::hasColumn('candidate_certifications', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
