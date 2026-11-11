<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employer_profiles', function (Blueprint $table) {
            $table->string('business_permit_number')->nullable()->after('company_name');
            $table->string('business_permit_path')->nullable()->after('business_permit_number');
            $table->timestamp('verified_at')->nullable()->after('business_permit_path');
        });
    }

    public function down(): void
    {
        Schema::table('employer_profiles', function (Blueprint $table) {
            $table->dropColumn(['business_permit_number', 'business_permit_path', 'verified_at']);
        });
    }
};
