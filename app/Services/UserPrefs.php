<?php

namespace App\Services;

/**
 * Per-person settings that follow the user to any computer (stored per school in library_user_prefs,
 * not in the shared SIMS user table). Currently: the colour theme (light / dark / system).
 */
class UserPrefs
{
    public const THEMES = ["light", "dark", "system"];

    public static function get(string $key): ?string
    {
        try {
            $db = SchoolSchema::schoolConnection();
            SchoolSchema::ensure("library_user_prefs");
            $row = $db->selectOne("SELECT `pref_value` FROM `library_user_prefs` WHERE `user_id` = ? AND `pref_key` = ?", [(string) session("user_id"), $key]);
            return $row ? $row->pref_value : null;
        } catch (\Throwable $e) {
            report($e);
            return null;
        }
    }

    public static function set(string $key, string $value): void
    {
        $db = SchoolSchema::schoolConnection();
        SchoolSchema::ensure("library_user_prefs");
        $db->statement(
            "INSERT INTO `library_user_prefs` (`user_id`, `pref_key`, `pref_value`, `date_updated`) VALUES (?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE `pref_value` = VALUES(`pref_value`), `date_updated` = VALUES(`date_updated`)",
            [(string) session("user_id"), $key, $value, date("YmdHis")]
        );
    }

    /** At login: put the user's saved theme in the session so every page starts in it. */
    public static function loadIntoSession(): void
    {
        $theme = self::get("theme");
        if (in_array($theme, self::THEMES, true)) {
            session()->put("theme_mode", $theme);
        } else {
            session()->forget("theme_mode");
        }
    }
}
