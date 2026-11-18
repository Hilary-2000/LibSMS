# CLAUDE.md

Instructions for Claude Code when working in this repository.

## Things to remember (user instructions)

- **Commits and pushes:** Do NOT add a `Co-Authored-By: Claude ...` line (or any other
  Claude/AI attribution) to commit messages. Do not add "Generated with Claude Code"
  to pull request descriptions either. Commits should read as the user's own.

- **Shared database structure with SIMS and Nuelas College:** LibSMS shares the same
  database structure as the SIMS and nuelascollege projects (locally `/opt/lampp/htdocs/sims`
  and `/opt/lampp/htdocs/nuelas_college`). LibSMS uses only some of the tables in that
  structure, not the whole database. Any change to a shared table (schema, columns, data
  format, password hashing) can break those apps too, so check how they use a table before
  altering it, and prefer adding library-only tables (`library_*`) over modifying shared ones.
- **Same cloud server:** All three projects run on the same cloud server. On the server this
  project lives at `/var/www/html/LibSMS` (locally it is `/opt/lampp/htdocs/LibSMS`).
  Deployments, migrations and server changes there affect the other apps as well.

<!-- Add new "remember this" instructions from the user above this line. -->

## Project overview

LibSMS is a Laravel 10 library management system (runs on PHP 8.1–8.4, served via XAMPP
from `/opt/lampp/htdocs/LibSMS`). Remote: `origin` → github.com/Hilary-2000/LibSMS, branch `main`.

- Controllers: `app/Http/Controllers/` (Acquisitions, Cataloguing, Circulation,
  CirculationStats, StockTake, Settings, login, ...)
- Services: `app/Services/` (CirculationInsights, Borrowers, SchoolSchema, UserPrefs, WhatsNew)
- View helpers loaded at startup: `app/Support/view_helpers.php`
- Views: `resources/views/` with shared partials in `resources/views/partials/`
- "What's new" content: `config/whats_new.php`
- Tests: `tests/Feature/` (run with `php artisan test`)

## Conventions

- Use bound parameters for all SQL; never concatenate user input into queries.
- State-changing actions (delete/cancel/check-in) use CSRF-protected POST, not GET.
- Escape user data in generated HTML.
- Password hashing is shared with SIMS/LMS; do not change it.
- When changing a JS file loaded by views, bump its version query string so browsers
  drop cached copies.
