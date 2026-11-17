<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Tables added by the library on top of the SIMS school template (reservations, audit log, stock-take).
 *
 * Each school has its own database, created by SIMS. Instead of requiring a manual migration on every
 * school, the library creates a missing table the first time a school uses the feature:
 * CREATE TABLE IF NOT EXISTS only - existing tables are never altered or dropped.
 * The same definitions are used by the migrations in database/migrations/school.
 *
 * Dates follow the existing convention of the library tables: YmdHis strings (e.g. 20261117143000).
 */
class SchoolSchema
{
    public const TABLES = [
        "library_reservations" => <<<'SQL'
CREATE TABLE IF NOT EXISTS `library_reservations` (
  `reservation_id` int(11) NOT NULL AUTO_INCREMENT,
  `book_key` varchar(200) NOT NULL COMMENT 'isbn_13 of the title, or id:<book_id> when the book has no ISBN',
  `book_title` mediumtext DEFAULT NULL,
  `borrower_type` varchar(20) NOT NULL COMMENT 'student | staff',
  `borrower_id` varchar(200) NOT NULL COMMENT 'adm_no for students, user_id for staff',
  `status` varchar(20) NOT NULL DEFAULT 'waiting' COMMENT 'waiting | ready | fulfilled | cancelled',
  `held_book_id` varchar(200) DEFAULT NULL COMMENT 'the copy kept aside once it is returned',
  `date_reserved` varchar(100) NOT NULL,
  `date_ready` varchar(100) DEFAULT NULL,
  `date_closed` varchar(100) DEFAULT NULL,
  `reserved_by` varchar(200) DEFAULT NULL,
  `closed_by` varchar(200) DEFAULT NULL,
  `comments` varchar(2000) DEFAULT NULL,
  PRIMARY KEY (`reservation_id`),
  KEY `book_key_status` (`book_key`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
        "library_audit_log" => <<<'SQL'
CREATE TABLE IF NOT EXISTS `library_audit_log` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `date_created` varchar(100) NOT NULL,
  `user_id` varchar(200) DEFAULT NULL,
  `user_name` varchar(500) DEFAULT NULL,
  `action` varchar(200) NOT NULL,
  `target` varchar(500) DEFAULT NULL,
  `details` mediumtext DEFAULT NULL,
  `outcome` varchar(20) NOT NULL DEFAULT 'ok' COMMENT 'ok | failed',
  `ip_address` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`log_id`),
  KEY `date_created` (`date_created`),
  KEY `action` (`action`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
        "library_stocktakes" => <<<'SQL'
CREATE TABLE IF NOT EXISTS `library_stocktakes` (
  `stocktake_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(500) NOT NULL,
  `library_location` varchar(200) DEFAULT NULL COMMENT 'NULL = all libraries',
  `status` varchar(20) NOT NULL DEFAULT 'open' COMMENT 'open | closed',
  `date_started` varchar(100) NOT NULL,
  `date_closed` varchar(100) DEFAULT NULL,
  `started_by` varchar(200) DEFAULT NULL,
  `closed_by` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`stocktake_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
        "library_user_prefs" => <<<'SQL'
CREATE TABLE IF NOT EXISTS `library_user_prefs` (
  `user_id` varchar(200) NOT NULL,
  `pref_key` varchar(100) NOT NULL COMMENT 'e.g. theme',
  `pref_value` varchar(500) DEFAULT NULL,
  `date_updated` varchar(100) NOT NULL,
  PRIMARY KEY (`user_id`,`pref_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
        "library_whats_new_seen" => <<<'SQL'
CREATE TABLE IF NOT EXISTS `library_whats_new_seen` (
  `user_id` varchar(200) NOT NULL,
  `version` varchar(50) NOT NULL COMMENT 'config/whats_new.php version the user has seen',
  `date_seen` varchar(100) NOT NULL,
  PRIMARY KEY (`user_id`,`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
        "library_stocktake_scans" => <<<'SQL'
CREATE TABLE IF NOT EXISTS `library_stocktake_scans` (
  `scan_id` int(11) NOT NULL AUTO_INCREMENT,
  `stocktake_id` int(11) NOT NULL,
  `code` varchar(200) NOT NULL COMMENT 'what the scanner read',
  `book_id` varchar(200) DEFAULT NULL COMMENT 'NULL when the code matched no book',
  `date_scanned` varchar(100) NOT NULL,
  `scanned_by` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`scan_id`),
  KEY `stocktake_id` (`stocktake_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
    ];

    /** @var array<string,bool> tables already checked in this request */
    private static array $checked = [];

    /** Make sure the given library tables exist in the current school database (connection mysql2). */
    public static function ensure(string ...$tables): void
    {
        $database = (string) config("database.connections.mysql2.database");
        foreach ($tables as $table) {
            $key = $database."|".$table;
            if (isset(self::$checked[$key])) {
                continue;
            }
            // remembered for a day so normal requests do not even ask MySQL
            if (!Cache::get("school_schema:".$key)) {
                $exists = DB::connection("mysql2")->selectOne(
                    "SELECT COUNT(*) AS n FROM information_schema.tables WHERE table_schema = ? AND table_name = ?", [$database, $table]
                );
                // only issue DDL when really missing: CREATE TABLE commits any open transaction in MySQL
                if ((int) $exists->n === 0) {
                    DB::connection("mysql2")->statement(self::TABLES[$table]);
                }
                Cache::put("school_schema:".$key, 1, now()->addDay());
            }
            self::$checked[$key] = true;
        }
    }

    /** Point the mysql2 connection at the logged-in user's school and return it. */
    public static function schoolConnection()
    {
        $database = session("school_details")->database_name ?? null;
        if ($database && config("database.connections.mysql2.database") !== $database) {
            config(["database.connections.mysql2.database" => $database]);
            DB::purge("mysql2");
        }
        return DB::connection("mysql2");
    }

    /** Forget the per-request cache (used by tests). */
    public static function reset(): void
    {
        self::$checked = [];
    }
}
