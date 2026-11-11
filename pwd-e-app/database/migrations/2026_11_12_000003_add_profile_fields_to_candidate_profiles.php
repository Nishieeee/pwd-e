<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('candidate_profiles', function (Blueprint $table) {
            $table->string('resume_pdf_path')->nullable()->after('fsl_caption_path');
            $table->string('disability_type')->nullable()->after('resume_pdf_path');
            $table->text('disability_description')->nullable()->after('disability_type');
            $table->string('phone')->nullable()->after('disability_description');
            $table->date('date_of_birth')->nullable()->after('phone');
            $table->string('gender')->nullable()->after('date_of_birth');
        });
    }

    public function down(): void
    {
        Schema::table('candidate_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'resume_pdf_path',
                'disability_type',
                'disability_description',
                'phone',
                'date_of_birth',
                'gender',
            ]);
        });
    }
};
