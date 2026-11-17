<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * `book_circulation` - per-school library table (lives in each school's own database).
 *
 * Copied verbatim from the SIMS school template (sims/school_manager/school_template.sql), which is
 * what creates it on real schools. Uses CREATE TABLE IF NOT EXISTS, so it never changes an
 * existing school database. Run against a school database with:
 *
 *   php artisan migrate --database=mysql2 --path=database/migrations/school
 *
 * (DB_DATABASE_2 in .env selects the school database.)
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::connection($this->getConnection())->statement(<<<'SQL'
CREATE TABLE IF NOT EXISTS `book_circulation` (
  `circulation_id` int(11) NOT NULL AUTO_INCREMENT,
  `book_isbn` mediumtext DEFAULT NULL,
  `book_id` varchar(200) DEFAULT NULL,
  `book_call_number` mediumtext DEFAULT NULL,
  `user_checked_out` mediumtext DEFAULT NULL,
  `user_borrowing` mediumtext DEFAULT NULL,
  `date_checked_out` mediumtext DEFAULT NULL,
  `expected_return_date` mediumtext DEFAULT NULL,
  `checked_out_by` mediumtext DEFAULT NULL,
  `checked_in_by` varchar(200) DEFAULT NULL,
  `fine_per_day` mediumtext DEFAULT NULL,
  `fine_status` mediumtext DEFAULT NULL,
  `return_status` int(11) NOT NULL DEFAULT 0,
  `return_date` varchar(200) DEFAULT NULL,
  `comments` varchar(2000) DEFAULT NULL,
  PRIMARY KEY (`circulation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL);
    }

    public function down(): void
    {
        // intentionally empty: library data must never be dropped by a rollback
    }
};
