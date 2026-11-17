@extends('layouts.app', ['active' => 'Acquisitions'])
@section('title', 'Stock-take')
@section('page_title', 'Stock-take')

@section('content')
@if (count($open) > 0)
    <div class="card border border-primary">
        <div class="card-body">
            <h5 class="card-title mb-1"><i class="bx bx-loader-circle text-primary"></i> In progress</h5>
            <p class="text-muted small">Every scan is saved as it happens. Carry on where you stopped &mdash; from this or any other computer.</p>
            @foreach ($open as $o)
                @php $pct = $o->summary['expected'] > 0 ? min(100, round(100 * $o->summary['found'] / $o->summary['expected'])) : 0; @endphp
                <div class="d-flex flex-wrap align-items-center gap-3 py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <div class="flex-grow-1" style="min-width: 260px">
                        <div class="fw-semibold">{{ $o->title }} <span class="text-muted small fw-normal">&middot; {{ $o->library_location === null ? 'All libraries' : ($libraries[$o->library_location] ?? $o->library_location) }}</span></div>
                        <div class="progress my-1" style="height: 8px" role="progressbar" aria-label="Progress of {{ $o->title }}" aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100"><div class="progress-bar" style="width: {{ $pct }}%"></div></div>
                        <div class="small text-muted">{{ number_format($o->summary['found']) }} of {{ number_format($o->summary['expected']) }} books found ({{ $pct }}%) &middot; started {{ date("d M", strtotime($o->date_started)) }} &middot; last scan {{ $o->last_scan ? date("D d M, H:i", strtotime($o->last_scan)) : 'none yet' }}</div>
                    </div>
                    <a class="btn btn-primary" href="/Acquisitions/Stock-take/{{ $o->stocktake_id }}"><i class="bx bx-play"></i> Continue</a>
                </div>
            @endforeach
        </div>
    </div>
@endif
<div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Start a stock-take</h5>
                <p class="text-muted small">Scan every book on the shelves. When you are done, the results list the books that are missing, misplaced, or wrongly recorded as on loan or lost.</p>
                <form method="POST" action="/Acquisitions/Stock-take/Start">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label" for="st_title">Name</label>
                        <input class="form-control" name="title" id="st_title" value="Stock-take {{ date('d M Y') }}" maxlength="200">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="st_location">Library</label>
                        <select class="form-control" name="library_location" id="st_location">
                            <option value="">All libraries</option>
                            @foreach ($libraries as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button class="btn btn-primary" type="submit"><i class="bx bx-play"></i> Start</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">All stock-takes</h5>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-0">
                        <thead class="table-light"><tr><th>Name</th><th>Library</th><th>Started</th><th>Books scanned</th><th>Status</th><th></th></tr></thead>
                        <tbody>
                            @forelse ($sessions as $s)
                                <tr>
                                    <td>{{ $s->title }}</td>
                                    <td>{{ $s->library_location === null ? 'All libraries' : ($libraries[$s->library_location] ?? $s->library_location) }}</td>
                                    <td>{{ date("D dS M Y H:i", strtotime($s->date_started)) }}</td>
                                    <td>{{ number_format($s->scanned) }}</td>
                                    <td><span class="badge {{ $s->status === 'open' ? 'bg-warning text-dark' : 'bg-secondary' }}">{{ $s->status === 'open' ? 'in progress' : 'finished' }}</span></td>
                                    <td><a class="btn btn-sm btn-soft-primary" href="/Acquisitions/Stock-take/{{ $s->stocktake_id }}">{{ $s->status === 'open' ? 'Continue' : 'Results' }}</a></td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center text-muted py-4">No stock-takes yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @include('partials.pagination', ['pager' => $pager])
            </div>
        </div>
    </div>
</div>
@endsection
