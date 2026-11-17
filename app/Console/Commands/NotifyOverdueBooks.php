<?php

namespace App\Console\Commands;

use App\Services\OverdueNotifications;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Create overdue-book notifications for every school that has the library enabled.
 * Scheduled every 15 minutes (see App\Console\Kernel). Needs the standard Laravel cron entry:
 *   * * * * * cd /path/to/libSMS && php artisan schedule:run >> /dev/null 2>&1
 */
class NotifyOverdueBooks extends Command
{
    protected $signature = 'library:notify-overdue {--school= : only this school_code}';
    protected $description = 'Create "due for check-in" notifications for overdue library books';

    public function handle(): int
    {
        $query = "SELECT `school_code`, `school_name`, `database_name` FROM `school_information` WHERE `library_access` = 1";
        $bindings = [];
        if ($this->option('school')) {
            $query .= " AND `school_code` = ?";
            $bindings[] = $this->option('school');
        }
        $schools = DB::connection('mysql')->select($query, $bindings);

        $total = 0;
        foreach ($schools as $school) {
            $database = (string) $school->database_name;
            // skip schools whose database does not exist here or has no library tables yet
            $ready = DB::connection('mysql')->selectOne(
                "SELECT COUNT(*) AS n FROM information_schema.tables WHERE table_schema = ? AND table_name IN ('book_circulation','library_details','library_notifications','student_data')",
                [$database]
            );
            if ((int) $ready->n < 4) {
                $this->line("skip  {$database} (library tables missing)");
                continue;
            }
            try {
                $created = OverdueNotifications::generate($database);
                $total += $created;
                $this->line("ok    {$database}: {$created} new");
            } catch (\Throwable $e) {
                $this->error("error {$database}: ".$e->getMessage());
            }
        }
        $this->info("Done. {$total} notification(s) created.");
        return self::SUCCESS;
    }
}
