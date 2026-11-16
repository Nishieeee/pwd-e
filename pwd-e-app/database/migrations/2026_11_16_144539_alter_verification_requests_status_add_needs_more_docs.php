<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement('ALTER TABLE verification_requests DROP CONSTRAINT IF EXISTS verification_requests_status_check');
        DB::statement("ALTER TABLE verification_requests ADD CONSTRAINT verification_requests_status_check CHECK (status IN ('pending','approved','rejected','needs_more_docs'))");
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement('ALTER TABLE verification_requests DROP CONSTRAINT IF EXISTS verification_requests_status_check');
        DB::statement("ALTER TABLE verification_requests ADD CONSTRAINT verification_requests_status_check CHECK (status IN ('pending','approved','rejected'))");
    }
};
