<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            if (! Schema::hasColumn('applications', 'interview_at')) {
                $table->timestamp('interview_at')->nullable()->after('applied_at');
            }
            if (! Schema::hasColumn('applications', 'interview_location')) {
                $table->string('interview_location')->nullable()->after('interview_at');
            }
            if (! Schema::hasColumn('applications', 'interview_url')) {
                $table->string('interview_url')->nullable()->after('interview_location');
            }
            if (! Schema::hasColumn('applications', 'interview_accommodations')) {
                $table->text('interview_accommodations')->nullable()->after('interview_url');
            }
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn(['interview_at', 'interview_location', 'interview_url', 'interview_accommodations']);
        });
    }
};
