<?php

namespace Tests;

use App\Services\SchoolSchema;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Create the library's own tables in the demo school database BEFORE the per-test transaction
     * starts: CREATE TABLE commits any open transaction in MySQL, which would make the test's
     * rollback (and its temporary users/books) permanent.
     */
    protected function setUpTraits()
    {
        $db = DB::connection("mysql2");
        $database = $db->getDatabaseName();
        foreach (SchoolSchema::TABLES as $table => $ddl) {
            $exists = $db->selectOne("SELECT COUNT(*) AS n FROM information_schema.tables WHERE table_schema = ? AND table_name = ?", [$database, $table]);
            if ((int) $exists->n === 0) {
                $db->statement($ddl);
            }
        }
        SchoolSchema::reset();
        return parent::setUpTraits();
    }
}
