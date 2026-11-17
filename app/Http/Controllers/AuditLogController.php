<?php

namespace App\Http\Controllers;

use App\Services\SchoolSchema;
use App\Support\RawPaginator;
use Illuminate\Http\Request;

date_default_timezone_set('Africa/Nairobi');

/** Settings > Audit log: who checked out, deleted or changed what, and when. */
class AuditLogController extends Controller
{
    function index(Request $request)
    {
        $db = SchoolSchema::schoolConnection();
        SchoolSchema::ensure("library_audit_log");

        $where = [];
        $bindings = [];
        if ($request->filled("action")) {
            $where[] = "`action` = ?";
            $bindings[] = $request->input("action");
        }
        if ($request->filled("user")) {
            $where[] = "`user_name` LIKE ?";
            $bindings[] = "%".$request->input("user")."%";
        }
        if (in_array($request->input("outcome"), ["ok", "failed"], true)) {
            $where[] = "`outcome` = ?";
            $bindings[] = $request->input("outcome");
        }
        if ($request->filled("search")) {
            $where[] = "(`target` LIKE ? OR `details` LIKE ?)";
            $bindings[] = "%".$request->input("search")."%";
            $bindings[] = "%".$request->input("search")."%";
        }
        // dates are stored as YmdHis strings, so whole days compare as text ranges
        if ($request->filled("from") && ($from = strtotime($request->input("from")))) {
            $where[] = "`date_created` >= ?";
            $bindings[] = date("Ymd", $from)."000000";
        }
        if ($request->filled("to") && ($to = strtotime($request->input("to")))) {
            $where[] = "`date_created` <= ?";
            $bindings[] = date("Ymd", $to)."235959";
        }
        $sql = "SELECT * FROM `library_audit_log`".($where ? " WHERE ".implode(" AND ", $where) : "")." ORDER BY `log_id` DESC";
        [$rows, $pager] = RawPaginator::paginate($sql, $bindings, "page", $db, 50);

        return view("audit_log", $this->layoutData($request, [
            "rows" => $rows,
            "pager" => $pager,
            "actions" => array_column($db->select("SELECT DISTINCT `action` FROM `library_audit_log` ORDER BY `action`"), "action"),
        ]));
    }
}
