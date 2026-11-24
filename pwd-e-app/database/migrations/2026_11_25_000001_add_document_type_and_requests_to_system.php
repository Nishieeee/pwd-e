<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('pwd_document_type', 32)->default('pwd_id')->after('pwd_id_path');
        });

        Schema::table('applications', function (Blueprint $table) {
            $table->json('requested_documents')->nullable()->after('application_materials');
        });

        Schema::table('verification_requests', function (Blueprint $table) {
            $table->string('type', 32)->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('pwd_document_type');
        });

        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn('requested_documents');
        });

        Schema::table('verification_requests', function (Blueprint $table) {
            $table->string('type', 32)->change();
        });
    }
};
