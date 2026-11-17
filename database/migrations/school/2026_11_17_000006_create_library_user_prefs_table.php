<?php

use App\Services\SchoolSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * `library_user_prefs` - added by the library (not part of the SIMS school template).
 * The library also creates it automatically the first time a school uses the feature
 * (App\Services\SchoolSchema::ensure), so running this is optional. CREATE TABLE IF NOT EXISTS only.
 *
 *   php artisan migrate --database=mysql2 --path=database/migrations/school
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::connection($this->getConnection())->statement(SchoolSchema::TABLES["library_user_prefs"]);
    }

    public function down(): void
    {
        // intentionally empty: library data must never be dropped by a rollback
    }
};
