<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * `library_notifications` - per-school library table (lives in each school's own database).
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
CREATE TABLE IF NOT EXISTS `library_notifications` (
  `notification_id` int(11) NOT NULL AUTO_INCREMENT,
  `notification_title` varchar(1000) NOT NULL,
  `notification_content` longtext NOT NULL,
  `notification_action` longtext NOT NULL DEFAULT ('[]'),
  `date_created` varchar(100) NOT NULL,
  `book_id` varchar(200) NOT NULL COMMENT 'this value will be used to associate a book to the notification  so that if the book was die for checkin the book id will be added here and when the book is checked out this notification will be delete by the system,',
  `notification_type` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`notification_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL);
    }

    public function down(): void
    {
        // intentionally empty: library data must never be dropped by a rollback
    }
};
