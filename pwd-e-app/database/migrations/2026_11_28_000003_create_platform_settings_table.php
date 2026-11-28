<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('platform_settings')) {
            Schema::create('platform_settings', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->text('value')->nullable();
                $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });

            DB::table('platform_settings')->insert([
                'key' => 'pwd_tax_deduction_rate',
                'value' => '20',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        Schema::table('applications', function (Blueprint $table) {
            if (! Schema::hasColumn('applications', 'multi_offer_ignored_at')) {
                $table->timestamp('multi_offer_ignored_at')->nullable()->after('offer_declined_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            if (Schema::hasColumn('applications', 'multi_offer_ignored_at')) {
                $table->dropColumn('multi_offer_ignored_at');
            }
        });

        Schema::dropIfExists('platform_settings');
    }
};
