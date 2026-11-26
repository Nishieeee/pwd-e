<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('medical_certificate_number')->nullable()->after('pwd_id_path');
            $table->string('medical_certificate_path')->nullable()->after('medical_certificate_number');
        });

        Schema::table('verification_requests', function (Blueprint $table) {
            $table->string('secondary_document_path')->nullable()->after('document_path');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['medical_certificate_number', 'medical_certificate_path']);
        });

        Schema::table('verification_requests', function (Blueprint $table) {
            $table->dropColumn('secondary_document_path');
        });
    }
};
