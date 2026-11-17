<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('candidate_profiles', function (Blueprint $table) {
            $table->string('avatar_path')->nullable()->after('user_id');
            $table->string('suffix', 30)->nullable()->after('avatar_path');
            $table->string('country', 100)->default('Philippines')->after('location');
            $table->string('city', 120)->nullable()->after('country');
            $table->string('barangay', 120)->nullable()->after('city');
            $table->string('zip_code', 20)->nullable()->after('barangay');
            $table->json('spoken_languages')->nullable()->after('zip_code');
            $table->string('other_language')->nullable()->after('spoken_languages');
            $table->string('highest_education')->nullable()->after('education_badge');
            $table->string('input_mode', 30)->default('voice')->after('highest_education');
            $table->text('accommodation_notes')->nullable()->after('visibility_settings');
        });

        Schema::table('accommodations', function (Blueprint $table) {
            if (! Schema::hasColumn('accommodations', 'description')) {
                $table->string('description')->nullable()->after('category');
            }
        });

        Schema::create('location_cities', function (Blueprint $table) {
            $table->id();
            $table->string('country', 100)->default('Philippines');
            $table->string('name', 120)->unique();
            $table->string('zip_code', 20);
            $table->timestamps();
        });

        Schema::create('location_barangays', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_city_id')->constrained('location_cities')->cascadeOnDelete();
            $table->string('name', 120);
            $table->timestamps();
            $table->unique(['location_city_id', 'name']);
        });

        Schema::create('skill_options', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->string('category', 50)->default('general');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('language_options', function (Blueprint $table) {
            $table->id();
            $table->string('name', 80)->unique();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('education_levels', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('education_levels');
        Schema::dropIfExists('language_options');
        Schema::dropIfExists('skill_options');
        Schema::dropIfExists('location_barangays');
        Schema::dropIfExists('location_cities');

        if (Schema::hasColumn('accommodations', 'description')) {
            Schema::table('accommodations', function (Blueprint $table) {
                $table->dropColumn('description');
            });
        }

        Schema::table('candidate_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'avatar_path',
                'suffix',
                'country',
                'city',
                'barangay',
                'zip_code',
                'spoken_languages',
                'other_language',
                'highest_education',
                'input_mode',
                'accommodation_notes',
            ]);
        });
    }
};
