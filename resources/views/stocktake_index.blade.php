@extends('layouts.app', ['active' => 'Acquisitions'])
@section('title', 'Stock-take')
@section('page_title', 'Stock-take')

@section('content')
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
                <h5 class="card-title">Previous stock-takes</h5>
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
                                    <td><span class="badge {{ $s->status === 'open' ? 'bg-warning text-dark' : 'bg-secondary' }}">{{ $s->status }}</span></td>
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
