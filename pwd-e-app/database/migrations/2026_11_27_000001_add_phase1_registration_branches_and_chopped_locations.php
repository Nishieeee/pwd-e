<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'phone')) {
                $table->string('phone', 30)->nullable()->after('email');
            }
        });

        Schema::table('candidate_profiles', function (Blueprint $table) {
            if (! Schema::hasColumn('candidate_profiles', 'disability_types')) {
                $table->text('disability_types')->nullable()->after('disability_type');
            }
        });

        Schema::table('employer_profiles', function (Blueprint $table) {
            if (! Schema::hasColumn('employer_profiles', 'company_type')) {
                $table->string('company_type', 100)->nullable()->after('company_name');
            }
            if (! Schema::hasColumn('employer_profiles', 'contact_phone')) {
                $table->string('contact_phone', 30)->nullable()->after('business_permit_path');
            }
            if (! Schema::hasColumn('employer_profiles', 'street_address')) {
                $table->string('street_address')->nullable()->after('location');
                $table->string('country', 100)->nullable()->default('Philippines')->after('street_address');
                $table->string('province', 120)->nullable()->after('country');
                $table->string('city', 120)->nullable()->after('province');
                $table->string('barangay', 120)->nullable()->after('city');
                $table->string('zip_code', 20)->nullable()->after('barangay');
            }
        });

        if (! Schema::hasTable('employer_branches')) {
            Schema::create('employer_branches', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employer_profile_id')->constrained('employer_profiles')->cascadeOnDelete();
                $table->string('name', 150);
                $table->string('street_address')->nullable();
                $table->string('country', 100)->default('Philippines');
                $table->string('province', 120)->nullable();
                $table->string('city', 120)->nullable();
                $table->string('barangay', 120)->nullable();
                $table->string('zip_code', 20)->nullable();
                $table->string('contact_phone', 30)->nullable();
                $table->boolean('is_headquarters')->default(false);
                $table->timestamps();
            });
        }

        Schema::table('job_postings', function (Blueprint $table) {
            if (! Schema::hasColumn('job_postings', 'employer_branch_id')) {
                $table->foreignId('employer_branch_id')->nullable()->after('employer_profile_id')->constrained('employer_branches')->nullOnDelete();
                $table->string('street_address')->nullable()->after('location');
                $table->string('country', 100)->nullable()->default('Philippines')->after('street_address');
                $table->string('province', 120)->nullable()->after('country');
                $table->string('city', 120)->nullable()->after('province');
                $table->string('barangay', 120)->nullable()->after('city');
                $table->string('zip_code', 20)->nullable()->after('barangay');
            }
        });
    }

    public function down(): void
    {
        Schema::table('job_postings', function (Blueprint $table) {
            if (Schema::hasColumn('job_postings', 'employer_branch_id')) {
                $table->dropConstrainedForeignId('employer_branch_id');
                $table->dropColumn(['street_address', 'country', 'province', 'city', 'barangay', 'zip_code']);
            }
        });

        Schema::dropIfExists('employer_branches');

        Schema::table('employer_profiles', function (Blueprint $table) {
            $cols = array_filter(
                ['company_type', 'contact_phone', 'street_address', 'country', 'province', 'city', 'barangay', 'zip_code'],
                fn ($c) => Schema::hasColumn('employer_profiles', $c)
            );
            if (! empty($cols)) {
                $table->dropColumn($cols);
            }
        });

        Schema::table('candidate_profiles', function (Blueprint $table) {
            if (Schema::hasColumn('candidate_profiles', 'disability_types')) {
                $table->dropColumn('disability_types');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'phone')) {
                $table->dropColumn('phone');
            }
        });
    }
};
