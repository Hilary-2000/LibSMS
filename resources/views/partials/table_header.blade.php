{{--
    Header for a paginated book table: a prominent title with the record count, and a search bar.
    Typing filters the rows on this page instantly; pressing Enter (or the button) searches ALL books
    on the server - the same search as "Advanced Keyword Search".

    @include('partials.table_header', [
        'title'  => 'Book Table',          // heading
        'icon'   => 'bx-book',             // boxicons name
        'table'  => 'datatable',           // id of the <table> to filter
        'action' => '/Acquisitions',       // page the search submits to (GET ?keyword_search=)
        'search' => $search_title,         // current server search, if any
        'pager'  => $pager,                // for the count
        'noun'   => 'book',                // "12 books"
    ])
--}}
@php
    $noun = $noun ?? 'book';
    $total = isset($pager) ? $pager->total() : null;
@endphp
<div class="table-header d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3 pb-3 border-bottom">
    <div class="table-header-title ps-3">
        <h5 class="mb-1 fw-semibold d-flex align-items-center gap-2">
            <i class="bx {{ $icon ?? 'bx-book' }} text-primary font-size-20"></i>
            <span>{{ $title }}</span>
            @if ($total !== null)
                <span class="badge badge-soft-primary font-size-12">{{ number_format($total) }} {{ \Illuminate\Support\Str::plural($noun, $total) }}</span>
            @endif
        </h5>
        @if (!empty($search))
            <div class="small text-muted">
                Showing results for <b class="text-body">"{{ $search }}"</b> across all books
                &middot; <a href="{{ $action }}">clear search</a>
            </div>
        @else
            <div class="small text-muted">Type to filter this page &middot; press Enter to search all books</div>
        @endif
    </div>

    <form method="GET" action="{{ $action }}" class="table-quick-search" role="search" autocomplete="off">
        @if (request('per_page'))<input type="hidden" name="per_page" value="{{ request('per_page') }}">@endif
        <div class="input-group">
            <span class="input-group-text bg-transparent"><i class="bx bx-search-alt"></i></span>
            <input type="search" name="keyword_search" class="form-control" value="{{ $search ?? '' }}"
                   placeholder="Search title, author, ISBN, call no…" aria-label="Search {{ \Illuminate\Support\Str::plural($noun) }}"
                   data-filter-table="#{{ $table }}">
            <button class="btn btn-primary" type="submit" title="Search all books">Search all</button>
        </div>
        <div class="small text-muted mt-1 d-none" data-filter-hint>
            No match on this page &mdash; press <b>Enter</b> to search all {{ \Illuminate\Support\Str::plural($noun) }}.
        </div>
    </form>
</div>

@once
    <style>
        .table-header-title { border-left: 4px solid var(--bs-primary, #5F9EA0); }
        .table-header-title h5 { font-size: 1.05rem; letter-spacing: .2px; }
        .table-quick-search { min-width: 280px; flex: 0 1 420px; }
    </style>
    <script>
        // Instant filter of the rows already on the page; Enter submits to the server for a full search.
        document.addEventListener("input", function (e) {
            var input = e.target.closest ? e.target.closest("[data-filter-table]") : null;
            if (!input) return;
            var table = document.querySelector(input.getAttribute("data-filter-table"));
            if (!table) return;
            var q = input.value.trim().toLowerCase();
            var shown = 0;
            table.querySelectorAll("tbody tr").forEach(function (row) {
                var match = q === "" || row.textContent.toLowerCase().indexOf(q) !== -1;
                row.style.display = match ? "" : "none";
                if (match) shown++;
            });
            var hint = input.closest("form").querySelector("[data-filter-hint]");
            if (hint) hint.classList.toggle("d-none", q === "" || shown > 0);
        });
    </script>
@endonce
