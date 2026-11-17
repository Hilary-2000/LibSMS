<?php

namespace App\Support;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

/**
 * Server-side pagination for the raw SQL queries used throughout the library.
 *
 *   [$rows, $pager] = RawPaginator::paginate("SELECT * FROM `library_details` WHERE ... ORDER BY `book_id` DESC", $bindings);
 *
 * $rows is a plain array (so existing views that use $rows[$i] / count($rows) keep working) and
 * $pager renders the page links: {{ $pager->links() }}. Page size comes from ?per_page=
 * (10/25/50/100, default 25); other query-string values (search keyword, filters) are kept in links.
 */
class RawPaginator
{
    public const SIZES = [10, 25, 50, 100];

    public static function paginate(string $sql, array $bindings = [], string $pageName = "page", ?ConnectionInterface $db = null, int $default = 25): array
    {
        $db = $db ?? DB::connection();
        $request = request();
        $perPage = (int) $request->input("per_page", $default);
        if (!in_array($perPage, self::SIZES, true)) {
            $perPage = $default;
        }
        $sql = rtrim(trim($sql), ";");

        $total = (int) ($db->selectOne("SELECT COUNT(*) AS aggregate FROM ($sql) AS paginated", $bindings)->aggregate ?? 0);
        $lastPage = max(1, (int) ceil($total / $perPage));
        $page = max(1, min((int) $request->input($pageName, 1), $lastPage));

        $rows = $total > 0
            ? $db->select("$sql LIMIT ".(int) $perPage." OFFSET ".(int) (($page - 1) * $perPage), $bindings)
            : [];

        $pager = new LengthAwarePaginator($rows, $total, $perPage, $page, [
            "path" => $request->url(),
            "pageName" => $pageName,
        ]);
        $pager->appends($request->except($pageName));

        return [$rows, $pager];
    }
}
