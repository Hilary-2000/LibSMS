@extends('layouts.app', ['active' => 'Acquisitions'])
@section('title', 'Import books - check')
@section('page_title', 'Import books')

@section('content')
<div class="card">
    <div class="card-body">
        <h5 class="card-title">Check before importing</h5>
        <p>
            <b>{{ $ok_rows }}</b> of {{ count($rows) }} row(s) are ready, adding <b>{{ $ok_copies }}</b> book cop{{ $ok_copies === 1 ? 'y' : 'ies' }}.
            Rows with errors are skipped &mdash; fix them in the file and import it again.
            @if ($lookups) Looked up {{ $lookups }} ISBN(s) on Google Books. @endif
            @if ($truncated) <span class="text-danger">Only the first {{ \App\Http\Controllers\ImportBooks::MAX_ROWS }} rows were read.</span> @endif
        </p>
        <div class="d-flex gap-2 mb-3">
            @if ($ok_copies > 0)
                <form method="POST" action="/Acquisitions/Import/Confirm">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    <button type="submit" class="btn btn-success"><i class="bx bx-check"></i> Import {{ $ok_copies }} cop{{ $ok_copies === 1 ? 'y' : 'ies' }}</button>
                </form>
            @endif
            <a href="/Acquisitions/Import" class="btn btn-light">Cancel</a>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-sm align-middle">
                <thead class="table-light">
                    <tr><th>Line</th><th>Status</th><th>Title</th><th>Author</th><th>ISBN-13</th><th>Call no(s)</th><th>Shelf</th></tr>
                </thead>
                <tbody>
                    @foreach ($rows as $r)
                        <tr class="{{ $r['errors'] ? 'table-danger' : '' }}">
                            <td>{{ $r['line'] }}</td>
                            <td>
                                @if ($r['errors'])
                                    @foreach ($r['errors'] as $e)<div class="text-danger small">{{ $e }}</div>@endforeach
                                @else
                                    <span class="badge bg-success">Ready</span>
                                @endif
                                @foreach ($r['notes'] as $note)<div class="text-muted small">{{ $note }}</div>@endforeach
                            </td>
                            <td>{{ $r['title'] }}</td>
                            <td>{{ $r['author'] }}</td>
                            <td>{{ $r['isbn_13'] }}</td>
                            <td>{{ implode(', ', $r['call_numbers']) }}</td>
                            <td>{{ $r['shelf'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
