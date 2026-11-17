<?php

namespace App\Services;

/**
 * Has the logged-in user seen the current "What's new" (config/whats_new.php)?
 * Stored per school in library_whats_new_seen, so it is remembered on every computer.
 * The answer is cached in the session; a new login asks again.
 */
class WhatsNew
{
    public static function version(): string
    {
        return (string) config("whats_new.version");
    }

    public static function unseen(): bool
    {
        if (session("school_details") == null || session("user_id") == null) {
            return false;
        }
        $key = "whats_new_unseen:".self::version();
        if (session()->has($key)) {
            return (bool) session($key);
        }
        try {
            $db = SchoolSchema::schoolConnection();
            SchoolSchema::ensure("library_whats_new_seen");
            $seen = $db->selectOne("SELECT 1 AS x FROM `library_whats_new_seen` WHERE `user_id` = ? AND `version` = ?", [(string) session("user_id"), self::version()]);
            session()->put($key, !$seen);
            return !$seen;
        } catch (\Throwable $e) {
            report($e);
            return false; // never block a page because of the announcement
        }
    }

    public static function markSeen(): void
    {
        $db = SchoolSchema::schoolConnection();
        SchoolSchema::ensure("library_whats_new_seen");
        $db->statement("INSERT IGNORE INTO `library_whats_new_seen` (`user_id`, `version`, `date_seen`) VALUES (?, ?, ?)",
            [(string) session("user_id"), self::version(), date("YmdHis")]);
        session()->put("whats_new_unseen:".self::version(), false);
    }
}
