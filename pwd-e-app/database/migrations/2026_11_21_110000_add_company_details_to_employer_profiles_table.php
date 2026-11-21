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
        Schema::table('employer_profiles', function (Blueprint $table) {
            $table->text('about')->nullable()->after('location');
            $table->string('website')->nullable()->after('about');
            $table->json('accessibility_highlights')->nullable()->after('website');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employer_profiles', function (Blueprint $table) {
            $table->dropColumn(['about', 'website', 'accessibility_highlights']);
        });
    }
};
