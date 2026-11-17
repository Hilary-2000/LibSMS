<?php

namespace App\Http\Controllers;

use App\Services\BookCode;
use App\Services\Reservations;
use App\Services\SchoolSchema;
use Illuminate\Http\Request;
use Picqer\Barcode\BarcodeGeneratorSVG;

date_default_timezone_set('Africa/Nairobi');

/** Barcode labels for each copy, and the scanner entry point used at the front desk. */
class BookLabels extends Controller
{
    /**
     * Printable A4 sheet of labels (3 x 8). Which books:
     *   ?ids=1,2,3        specific copies (from the Acquisitions list check-boxes or a book page)
     *   ?from=10&to=40    a range of book ids (e.g. everything entered today)
     */
    function labels(Request $request)
    {
        $db = SchoolSchema::schoolConnection();
        $ids = array_values(array_unique(array_filter(array_map("intval", explode(",", (string) $request->input("ids", ""))))));
        if ($ids) {
            $ids = array_slice($ids, 0, 480); // 20 sheets per print
            $books = $db->select("SELECT `book_id`, `book_title`, `book_author`, `call_no`, `isbn_13` FROM `library_details` WHERE `book_id` IN (".implode(",", array_fill(0, count($ids), "?")).") ORDER BY `book_id`", $ids);
        } elseif ($request->filled("from")) {
            $from = (int) $request->input("from");
            $to = (int) $request->input("to", $from);
            $books = $db->select("SELECT `book_id`, `book_title`, `book_author`, `call_no`, `isbn_13` FROM `library_details` WHERE `book_id` BETWEEN ? AND ? ORDER BY `book_id` LIMIT 480", [min($from, $to), max($from, $to)]);
        } else {
            $books = [];
        }

        $generator = new BarcodeGeneratorSVG();
        foreach ($books as $book) {
            $book->code = BookCode::encode($book->book_id);
            // widthFactor 1.4 / height 38 prints reliably on a 63.5 x 33.9 mm label
            $book->barcode_svg = $generator->getBarcode($book->code, $generator::TYPE_CODE_128, 1.4, 38);
        }
        return view("labels", [
            "books" => $books,
            "school_name" => session("school_details")->school_name ?? "",
            "skip" => max(0, min(23, (int) $request->input("skip", 0))), // start part-way down a used sheet
        ]);
    }

    /**
     * Front-desk scanner: scan (or type) a label code, book id, call number or ISBN.
     * Copy on the shelf -> check-out page. Copy out on loan -> its loan, where it is checked in.
     */
    function scan(Request $request)
    {
        $db = SchoolSchema::schoolConnection();
        $code = trim((string) $request->input("code", ""));
        $book = BookCode::resolve($db, $code);
        if (!$book) {
            session()->flash("error", "No book matches the code \"".$code."\".");
            $back = (string) $request->input("back", "/Circulation");
            // only return to pages of this app (no open redirects)
            return redirect(preg_match('#^/(?!/)[A-Za-z0-9/_\-?=&.%]*$#', $back) ? $back : "/Circulation");
        }
        if ((int) $book->lost_status === 1) {
            session()->flash("error", "\"".$book->book_title."\" (".BookCode::encode($book->book_id).") is marked as lost. Open the book to mark it as found first.");
            return redirect("/Acquisitions/Book-details/".$book->book_id);
        }
        if ((int) $book->availability_status === 1) {
            $hold = Reservations::holdOn($book->book_id);
            if ($hold) {
                session()->flash("error", "Note: this copy is reserved and being kept aside. It can only be lent to the person who reserved it.");
            }
            return redirect("/Circulation/check-out/".$book->book_id);
        }
        $loan = $db->selectOne("SELECT `circulation_id` FROM `book_circulation` WHERE `book_id` = ? AND `return_status` = '0' ORDER BY `circulation_id` DESC LIMIT 1", [(string) $book->book_id]);
        if ($loan) {
            return redirect("/Circulation/View/check-out/".$book->book_id."/".$loan->circulation_id);
        }
        session()->flash("error", "\"".$book->book_title."\" is marked as checked out, but no open loan was found for it.");
        return redirect("/Acquisitions/Book-details/".$book->book_id);
    }
}
