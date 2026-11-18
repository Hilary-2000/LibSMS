<?php

namespace App\Console\Commands;

use App\Http\Middleware\LibraryAccess;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * One-time upgrade for the separate "Stock-take" privilege (added 2026-11-18).
 * Stock-take used to come with "Acquisition", so every user who has "Acquisition" but not
 * "Stock-take" gets "Stock-take" added and keeps the access they had. Users with an empty list
 * already have full access and are left alone. Safe to run more than once.
 *
 * Only the `library_previleges` column of the shared `user_tbl` is touched; SIMS and
 * Nuelas College do not use that column.
 */
class AddStockTakePrivilege extends Command
{
    protected $signature = 'library:add-stocktake-privilege {--dry-run : only list the users that would change}';
    protected $description = 'Give the new Stock-take privilege to users who already have Acquisitions';

    public function handle(): int
    {
        $users = DB::connection('mysql')->select(
            "SELECT `user_id`, `school_code`, `library_previleges` FROM `user_tbl` WHERE `library_previleges` LIKE ?",
            ['%"Acquisition"%']
        );

        $changed = 0;
        foreach ($users as $user) {
            $list = LibraryAccess::parsePrivileges($user->library_previleges);
            if (!in_array("Acquisition", $list, true) || in_array("Stock-take", $list, true)) {
                continue;
            }
            $list[] = "Stock-take";
            $ordered = array_values(array_intersect(array_keys(LibraryAccess::PRIVILEGES), $list));
            $this->line("user {$user->user_id} ({$user->school_code}): ".json_encode($ordered));
            if (!$this->option('dry-run')) {
                DB::connection('mysql')->update(
                    "UPDATE `user_tbl` SET `library_previleges` = ? WHERE `user_id` = ? AND `school_code` = ?",
                    [json_encode($ordered), $user->user_id, $user->school_code]
                );
            }
            $changed++;
        }

        $this->info(($this->option('dry-run') ? "Would update" : "Updated")." {$changed} user(s).");
        return self::SUCCESS;
    }
}
