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
            $table->string('ended_reason')->nullable()->after('ended_at');
            $table->text('ended_notes')->nullable()->after('ended_reason');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workforce_records', function (Blueprint $table) {
            $table->dropColumn(['ended_reason', 'ended_notes']);
        });
    }
};
