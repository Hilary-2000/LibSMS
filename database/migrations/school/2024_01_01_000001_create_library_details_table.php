<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * `library_details` - per-school library table (lives in each school's own database).
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
CREATE TABLE IF NOT EXISTS `library_details` (
  `book_id` int(11) NOT NULL AUTO_INCREMENT,
  `book_title` mediumtext DEFAULT NULL,
  `book_author` mediumtext DEFAULT NULL,
  `book_publishers` mediumtext DEFAULT NULL,
  `published_date` mediumtext DEFAULT NULL,
  `thumbnail_location` mediumtext DEFAULT NULL,
  `book_category` mediumtext DEFAULT NULL,
  `isbn_13` mediumtext DEFAULT NULL,
  `isbn_10` mediumtext DEFAULT NULL,
  `date_recorded` mediumtext DEFAULT NULL,
  `physical_dimensions` mediumtext DEFAULT NULL,
  `no_of_revisions` mediumtext DEFAULT NULL,
  `call_no` mediumtext DEFAULT NULL,
  `language` mediumtext DEFAULT NULL,
  `description` mediumtext DEFAULT NULL,
  `shelf_no_location` mediumtext DEFAULT NULL,
  `keywords` mediumtext DEFAULT NULL,
  `edition` mediumtext DEFAULT NULL,
  `no_of_pages` mediumtext DEFAULT NULL,
  `availability_status` int(11) NOT NULL DEFAULT 1 COMMENT '0 - absent\r\n1 - present',
  `library_location` varchar(200) NOT NULL DEFAULT '1',
  `lost_status` int(1) NOT NULL DEFAULT 0,
  `date_lost` varchar(200) DEFAULT NULL,
  `date_reported` varchar(200) DEFAULT NULL,
  `who_lost_it` varchar(200) DEFAULT NULL,
  `who_reported_it` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`book_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL);
    }

    public function down(): void
    {
        // intentionally empty: library data must never be dropped by a rollback
    }
};
