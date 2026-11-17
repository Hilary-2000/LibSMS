@extends('layouts.app', ['active' => 'Acquisitions'])
@section('title', $take->title)
@section('page_title', 'Stock-take')

@section('content')
<div class="card">
    <div class="card-body">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
            <div>
                <h5 class="card-title mb-1">{{ $take->title }}</h5>
                <div class="text-muted small">{{ $location_name }} &middot; started {{ date("D dS M Y H:i", strtotime($take->date_started)) }}
                    @if ($take->status === 'closed') &middot; closed {{ date("D dS M Y H:i", strtotime($take->date_closed)) }} @endif</div>
            </div>
            <div class="d-flex gap-2">
                <a href="/Acquisitions/Stock-take" class="btn btn-sm btn-light">All stock-takes</a>
                @if ($take->status === 'open')
                    <form method="POST" action="/Acquisitions/Stock-take/{{ $take->stocktake_id }}/Close" onsubmit="return confirm('Close this stock-take? No more scans can be added.')">
                        @csrf
                        <button class="btn btn-sm btn-outline-danger" type="submit"><i class="bx bx-stop"></i> Close stock-take</button>
                    </form>
                @endif
            </div>
        </div>

        @if ($take->status === 'open')
            <form method="POST" action="/Acquisitions/Stock-take/{{ $take->stocktake_id }}/Scan" id="stocktake_scan" class="mt-3" autocomplete="off">
                @csrf
                <label for="st_code" class="form-label mb-1"><i class="bx bx-barcode-reader"></i> Scan each book on the shelf</label>
                <div class="input-group" style="max-width: 520px">
                    <input type="text" class="form-control" name="code" id="st_code" placeholder="Scan the label, or type LB000012 / call number" autofocus required>
                    <button class="btn btn-primary" type="submit">Add</button>
                </div>
            </form>
            <div id="scan_feed" class="mt-2" style="max-width: 720px" aria-live="polite"></div>
        @endif

        <div class="row g-2 mt-3" id="st_summary">
            @php
                $tiles = [
                    'expected' => ['Expected on shelf', 'text-body'], 'found' => ['Found', 'text-success'], 'missing' => ['Missing', 'text-danger'],
                    'misplaced' => ['Misplaced', 'text-warning'], 'on_loan' => ['Recorded on loan', 'text-warning'], 'lost' => ['Recorded lost', 'text-warning'],
                    'unknown' => ['Unknown codes', 'text-muted'],
                ];
            @endphp
            @foreach ($tiles as $key => [$label, $class])
                <div class="col-6 col-md-3 col-xl">
                    <div class="border rounded p-2 h-100">
                        <div class="small text-muted">{{ $label }}</div>
                        <div class="fs-4 fw-semibold {{ $class }}" data-summary="{{ $key }}">{{ number_format($summary[$key]) }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @php
            $tabs = ['missing' => 'Missing', 'misplaced' => 'Misplaced', 'on_loan' => 'Recorded on loan', 'lost' => 'Recorded lost', 'unknown' => 'Unknown codes', 'scans' => 'All scans'];
        @endphp
        <ul class="nav nav-tabs mb-3">
            @foreach ($tabs as $key => $label)
                <li class="nav-item"><a class="nav-link {{ $tab === $key ? 'active' : '' }}" href="?tab={{ $key }}">{{ $label }}</a></li>
            @endforeach
        </ul>
        <p class="text-muted small">
            @switch($tab)
                @case('missing') On the shelf according to the records, but not scanned. Reload this page to refresh the list after scanning. @break
                @case('misplaced') Scanned here, but recorded in another library. @break
                @case('on_loan') Scanned on the shelf, but recorded as checked out &mdash; the return was probably not recorded. @break
                @case('lost') Scanned on the shelf, but marked as lost &mdash; open the book and mark it as found. @break
                @case('unknown') Scanned codes that match no book. @break
                @default Every scan, newest first.
            @endswitch
        </p>

        @if ($tab === 'misplaced' && $take->library_location === null)
            <p class="text-muted">Not applicable: this stock-take covers all libraries.</p>
        @else
            <div class="table-responsive">
                <table class="table table-bordered table-sm align-middle mb-0">
                    @if ($tab === 'unknown')
                        <thead class="table-light"><tr><th>Code</th><th>Times scanned</th><th>First scanned</th></tr></thead>
                        <tbody>
                            @forelse ($rows as $r)
                                <tr><td>{{ $r->code }}</td><td>{{ $r->times }}</td><td>{{ date("D dS M Y H:i", strtotime($r->first_scanned)) }}</td></tr>
                            @empty
                                <tr><td colspan="3" class="text-center text-muted py-3">None.</td></tr>
                            @endforelse
                        </tbody>
                    @elseif ($tab === 'scans')
                        <thead class="table-light"><tr><th>Scanned</th><th>Code</th><th>Book</th><th>Call no.</th></tr></thead>
                        <tbody>
                            @forelse ($rows as $r)
                                <tr><td>{{ date("H:i:s D dS M", strtotime($r->date_scanned)) }}</td><td>{{ $r->code }}</td><td>{{ $r->book_title ?? 'Unknown code' }}</td><td>{{ $r->call_no }}</td></tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted py-3">Nothing scanned yet.</td></tr>
                            @endforelse
                        </tbody>
                    @else
                        <thead class="table-light"><tr><th>Label</th><th>Title</th><th>Author</th><th>Call no.</th><th>Shelf</th><th>Library</th><th></th></tr></thead>
                        <tbody>
                            @forelse ($rows as $r)
                                <tr>
                                    <td>{{ \App\Services\BookCode::encode($r->book_id) }}</td>
                                    <td>{{ $r->book_title }}</td>
                                    <td>{{ $r->book_author }}</td>
                                    <td>{{ $r->call_no }}</td>
                                    <td>{{ $r->shelf_no_location }}</td>
                                    <td>{{ $libraries[$r->library_location] ?? $r->library_location }}</td>
                                    <td><a href="/Acquisitions/Book-details/{{ $r->book_id }}" class="btn btn-sm btn-soft-primary" target="_blank">Open</a></td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="text-center text-muted py-3">None.</td></tr>
                            @endforelse
                        </tbody>
                    @endif
                </table>
            </div>
            @include('partials.pagination', ['pager' => $pager])
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Scan without reloading: each scan is posted in the background and the result shown at the top.
    (function () {
        var form = document.getElementById("stocktake_scan");
        if (!form) return;
        var input = document.getElementById("st_code");
        var feed = document.getElementById("scan_feed");
        form.addEventListener("submit", function (e) {
            e.preventDefault();
            var code = input.value.trim();
            if (!code) return;
            input.value = "";
            fetch(form.action, {
                method: "POST",
                headers: { "Accept": "application/json", "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content },
                body: new URLSearchParams({ code: code })
            }).then(function (r) { return r.json(); }).then(function (res) {
                var row = document.createElement("div");
                row.className = "alert alert-" + res.level + " py-1 px-2 mb-1 small";
                row.textContent = res.message;
                feed.prepend(row);
                while (feed.children.length > 8) feed.removeChild(feed.lastChild);
                if (res.summary) {
                    Object.keys(res.summary).forEach(function (k) {
                        var el = document.querySelector('[data-summary="' + k + '"]');
                        if (el) el.textContent = Number(res.summary[k]).toLocaleString();
                    });
                }
            }).catch(function () {
                var row = document.createElement("div");
                row.className = "alert alert-danger py-1 px-2 mb-1 small";
                row.textContent = "Could not save the scan of " + code + " - check the connection and scan it again.";
                feed.prepend(row);
            });
            input.focus();
        });
    })();
</script>
@endpush
