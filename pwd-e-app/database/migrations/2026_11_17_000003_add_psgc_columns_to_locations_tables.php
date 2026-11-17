<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('location_countries')) {
            Schema::create('location_countries', function (Blueprint $table) {
                $table->id();
                $table->string('iso_code', 10)->unique();
                $table->string('name', 100)->unique();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        Schema::table('location_cities', function (Blueprint $table) {
            if (! Schema::hasColumn('location_cities', 'psgc_code')) {
                $table->string('psgc_code', 20)->nullable()->unique()->after('id');
            }
            if (! Schema::hasColumn('location_cities', 'region_code')) {
                $table->string('region_code', 20)->nullable()->after('country');
            }
            if (! Schema::hasColumn('location_cities', 'region_name')) {
                $table->string('region_name', 120)->nullable()->after('region_code');
            }
            if (! Schema::hasColumn('location_cities', 'province_code')) {
                $table->string('province_code', 20)->nullable()->after('region_name');
            }
            if (! Schema::hasColumn('location_cities', 'province_name')) {
                $table->string('province_name', 120)->nullable()->after('province_code');
            }
            if (! Schema::hasColumn('location_cities', 'city_class')) {
                $table->string('city_class', 20)->default('HUC')->after('name');
            }
        });

        Schema::table('location_barangays', function (Blueprint $table) {
            if (! Schema::hasColumn('location_barangays', 'psgc_code')) {
                $table->string('psgc_code', 20)->nullable()->unique()->after('id');
            }
            if (! Schema::hasColumn('location_barangays', 'city_psgc_code')) {
                $table->string('city_psgc_code', 20)->nullable()->index()->after('location_city_id');
            }
            if (! Schema::hasColumn('location_barangays', 'urban_rural')) {
                $table->string('urban_rural', 5)->default('U')->after('name');
            }
        });

        Schema::table('candidate_profiles', function (Blueprint $table) {
            if (! Schema::hasColumn('candidate_profiles', 'location_city_id')) {
                $table->foreignId('location_city_id')
                    ->nullable()
                    ->after('country')
                    ->constrained('location_cities')
                    ->nullOnDelete();
            }
            if (! Schema::hasColumn('candidate_profiles', 'location_barangay_id')) {
                $table->foreignId('location_barangay_id')
                    ->nullable()
                    ->after('city')
                    ->constrained('location_barangays')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('candidate_profiles', function (Blueprint $table) {
            if (Schema::hasColumn('candidate_profiles', 'location_barangay_id')) {
                $table->dropConstrainedForeignId('location_barangay_id');
            }
            if (Schema::hasColumn('candidate_profiles', 'location_city_id')) {
                $table->dropConstrainedForeignId('location_city_id');
            }
        });

        Schema::table('location_barangays', function (Blueprint $table) {
            $cols = array_filter(['psgc_code', 'city_psgc_code', 'urban_rural'], fn ($c) => Schema::hasColumn('location_barangays', $c));
            if (! empty($cols)) {
                $table->dropColumn($cols);
            }
        });

        Schema::table('location_cities', function (Blueprint $table) {
            $cols = array_filter(['psgc_code', 'region_code', 'region_name', 'province_code', 'province_name', 'city_class'], fn ($c) => Schema::hasColumn('location_cities', $c));
            if (! empty($cols)) {
                $table->dropColumn($cols);
            }
        });

        Schema::dropIfExists('location_countries');
    }
};

