<?php

/*
| "What's new" window (top bar link; opens by itself once per user after each release).
|
| To announce a new release: change "version" (any new string) and replace "sections".
| Every user then sees the window once more on their next login. Links are optional.
*/

return [
    "version" => "2026-11",
    "title" => "What's new in the Ladybird Library",
    "intro" => "The library has had a big update. Here is what changed and where to find it.",

    "sections" => [
        [
            "title" => "Lending and returning",
            "icon" => "bx-rotate-left",
            "items" => [
                ["text" => "The <b>Circulation</b> menu now opens into <b>Lend a book</b>, <b>Borrowing history</b> (where you return books), <b>Statistics</b> and <b>Reservations</b>.", "link" => "/Circulation/check-out"],
                ["text" => "<b>Scan to lend or return:</b> scan a book's barcode label in the scan box and the right screen opens &mdash; check-out if it is on the shelf, its loan (to check it in) if it is out."],
                ["text" => "Every check-out and check-in now asks you to <b>confirm</b> and explains in one line what will happen, including the due date and whether a book is late."],
                ["text" => "<b>Reservations:</b> when every copy of a book is out, put a student or staff member on the waiting list. The next copy returned is kept aside for them and you get a notification.", "link" => "/Circulation/Reservations"],
                ["text" => "The check-in details window now has a grey <b>Close</b> button, separate from <b>Cancel Check-In</b>."],
            ],
        ],
        [
            "title" => "Books",
            "icon" => "bx-book",
            "items" => [
                ["text" => "<b>Barcode labels:</b> tick books in Acquisitions and choose <b>Print labels</b> to print sheets of 24 labels.", "link" => "/Acquisitions"],
                ["text" => "<b>Import books</b> from a CSV or Excel file &mdash; missing details can be filled in from Google Books using the ISBN.", "link" => "/Acquisitions/Import"],
                ["text" => "<b>Stock-take:</b> scan every shelf to find missing or misplaced books. Progress is saved as you go &mdash; choose <b>Continue later</b> and pick up where you stopped.", "link" => "/Acquisitions/Stock-take"],
                ["text" => "Book tables now show <b>all</b> books, a page at a time, with a <b>search bar</b> right on the table (type to filter the page, press Enter to search everything)."],
            ],
        ],
        [
            "title" => "Statistics and reports",
            "icon" => "bx-bar-chart-alt-2",
            "items" => [
                ["text" => "A new <b>Statistics</b> page for this term: top readers, reading by class, most borrowed titles, late returns and the busiest times at the desk.", "link" => "/Circulation/Stats"],
                ["text" => "Download the statistics to <b>Excel</b>, or print a one-page <b>term report</b> for the headteacher."],
                ["text" => "<b>Reports</b> can be exported to Excel, PDF or CSV, or printed.", "link" => "/Reports"],
                ["text" => "The <b>Dashboard</b> shows borrowing charts for the last 12 months.", "link" => "/Dashboard"],
            ],
        ],
        [
            "title" => "Look and feel",
            "icon" => "bx-palette",
            "items" => [
                ["text" => "Choose <b>Light</b>, <b>Dark</b> or <b>Same as device</b> with the <i class=\"bx bx-sun\"></i> <i class=\"bx bx-moon\"></i> <i class=\"bx bx-desktop\"></i> switch in the top bar. Your choice is saved to your account and follows you to any computer."],
                ["text" => "A refreshed Ladybird logo and browser-tab icon."],
            ],
        ],
        [
            "title" => "Safety and records",
            "icon" => "bx-shield-quarter",
            "items" => [
                ["text" => "<b>Audit log</b> (Settings): who lent, returned, deleted or changed what, and when.", "link" => "/Settings/Audit-log"],
                ["text" => "Each person only reaches the sections they have permission for, and deactivated staff accounts can no longer sign in."],
                ["text" => "<b>Remember me</b> is safer, and <b>Logout</b> now signs you out completely &mdash; log out on shared computers."],
            ],
        ],
    ],
];
