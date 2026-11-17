<?php

namespace App\Http\Controllers;

use App\Services\BookCode;
use App\Services\Borrowers;
use App\Services\Reservations;
use App\Services\SchoolSchema;
use App\Support\RawPaginator;
use Illuminate\Http\Request;

date_default_timezone_set('Africa/Nairobi');

/** Waiting list for books that are out (managed by librarians). */
class ReservationsController extends Controller
{
    const STATUSES = ["waiting", "ready", "fulfilled", "cancelled"];

    function index(Request $request)
    {
        $db = SchoolSchema::schoolConnection();
        SchoolSchema::ensure("library_reservations");

        $status = $request->input("status", "open");
        $where = $status === "open" ? "WHERE `status` IN ('waiting','ready')"
            : (in_array($status, self::STATUSES, true) ? "WHERE `status` = ?" : "");
        $bindings = in_array($status, self::STATUSES, true) ? [$status] : [];
        // ready first (someone should collect), then oldest first = queue order
        [$rows, $pager] = RawPaginator::paginate(
            "SELECT * FROM `library_reservations` $where ORDER BY FIELD(`status`,'ready','waiting','fulfilled','cancelled'), `reservation_id`",
            $bindings, "page", $db
        );
        foreach ($rows as $row) {
            $row->borrower_name = Borrowers::name($row->borrower_type, $row->borrower_id);
            $row->position = $row->status === "waiting"
                ? (int) $db->selectOne("SELECT COUNT(*) AS n FROM `library_reservations` WHERE `book_key` = ? AND `status` = 'waiting' AND `reservation_id` <= ?", [$row->book_key, $row->reservation_id])->n
                : null;
            $row->held_code = $row->held_book_id ? BookCode::encode($row->held_book_id) : null;
        }
        $counts = [];
        foreach ($db->select("SELECT `status`, COUNT(*) AS n FROM `library_reservations` GROUP BY `status`") as $c) {
            $counts[$c->status] = (int) $c->n;
        }
        return view("reservations", $this->layoutData($request, [
            "rows" => $rows, "pager" => $pager, "status" => $status, "counts" => $counts,
        ]));
    }

    /** Reserve the title of this copy for a student or staff member. */
    function store(Request $request)
    {
        $db = SchoolSchema::schoolConnection();
        $book = $db->selectOne("SELECT * FROM `library_details` WHERE `book_id` = ?", [(int) $request->input("book_id")]);
        $type = $request->input("borrower_type") === "staff" ? "staff" : "student";
        $borrower = trim((string) ($type === "staff" ? $request->input("staff_borrower") : $request->input("student_borrower")));
        $back = $book ? "/Acquisitions/Book-details/".$book->book_id : "/Circulation/Reservations";

        if (!$book) {
            session()->flash("error", "That book could not be found.");
            return redirect($back);
        }
        if ($borrower === "" || $borrower === "Select an option") {
            session()->flash("error", "Select who the book is reserved for.");
            return redirect($back);
        }
        $key = BookCode::titleKey($book);
        // a free copy (not held for someone) means there is nothing to wait for
        $free = $db->selectOne(
            "SELECT COUNT(*) AS n FROM `library_details` d WHERE ".(str_starts_with($key, "id:") ? "d.`book_id` = ?" : "d.`isbn_13` = ?").
            " AND d.`availability_status` = 1 AND d.`lost_status` = 0 AND NOT EXISTS (SELECT 1 FROM `library_reservations` r WHERE r.`status` = 'ready' AND r.`held_book_id` = d.`book_id`)",
            [str_starts_with($key, "id:") ? $book->book_id : $key]
        );
        if ((int) $free->n > 0) {
            session()->flash("error", "A copy of \"".$book->book_title."\" is on the shelf - check it out instead of reserving.");
            return redirect($back);
        }
        if (Reservations::openFor($key, $type, $borrower)) {
            session()->flash("error", Borrowers::name($type, $borrower)." already has a reservation for this book.");
            return redirect($back);
        }
        Reservations::create($book, $type, $borrower, $request->input("comments"));
        $position = Reservations::waitingCount($book);
        session()->flash("success", "\"".$book->book_title."\" reserved for ".Borrowers::name($type, $borrower).". Position in queue: ".$position.".");
        return redirect($back);
    }

    function cancel($reservation_id)
    {
        $db = SchoolSchema::schoolConnection();
        SchoolSchema::ensure("library_reservations");
        $reservation = $db->selectOne("SELECT * FROM `library_reservations` WHERE `reservation_id` = ? AND `status` IN ('waiting','ready')", [(int) $reservation_id]);
        if (!$reservation) {
            session()->flash("error", "That reservation is not open any more.");
            return redirect("/Circulation/Reservations");
        }
        Reservations::close($reservation, "cancelled");
        session()->flash("success", "Reservation for \"".$reservation->book_title."\" cancelled.");
        return redirect("/Circulation/Reservations");
    }
}
