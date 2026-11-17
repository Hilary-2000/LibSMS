@extends('layouts.app')
@section('title', 'Audit log')
@section('page_title', 'Audit log')

@section('content')
<div class="card">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end mb-3">
            <div class="col-md-3">
                <label class="form-label" for="f_action">Action</label>
                <select class="form-control" name="action" id="f_action">
                    <option value="">All actions</option>
                    @foreach ($actions as $action)
                        <option value="{{ $action }}" @selected(request('action') === $action)>{{ $action }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label" for="f_user">Done by</label>
                <input class="form-control" name="user" id="f_user" value="{{ request('user') }}" placeholder="Name">
            </div>
            <div class="col-md-2">
                <label class="form-label" for="f_search">Book / record</label>
                <input class="form-control" name="search" id="f_search" value="{{ request('search') }}" placeholder="id, title, call no.">
            </div>
            <div class="col-md-2">
                <label class="form-label" for="f_from">From</label>
                <input type="date" class="form-control" name="from" id="f_from" value="{{ request('from') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label" for="f_to">To</label>
                <input type="date" class="form-control" name="to" id="f_to" value="{{ request('to') }}">
            </div>
            <div class="col-md-1 d-flex gap-1">
                <button class="btn btn-primary" type="submit" title="Filter"><i class="bx bx-filter-alt"></i></button>
                <a class="btn btn-light" href="/Settings/Audit-log" title="Clear filters"><i class="bx bx-x"></i></a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-sm align-middle mb-0">
                <thead class="table-light">
                    <tr><th>When</th><th>Who</th><th>Action</th><th>Record</th><th>Details</th><th>Result</th></tr>
                </thead>
                <tbody>
                    @forelse ($rows as $row)
                        <tr>
                            <td class="text-nowrap">{{ date("D dS M Y H:i:s", strtotime($row->date_created)) }}</td>
                            <td>{{ ucwords(strtolower((string) $row->user_name)) }}</td>
                            <td>{{ $row->action }}</td>
                            <td>{{ $row->target }}</td>
                            <td class="small">
                                @php $details = json_decode((string) $row->details, true); @endphp
                                @if (is_array($details))
                                    @foreach ($details as $k => $v)<div><span class="text-muted">{{ str_replace('_', ' ', $k) }}:</span> {{ is_scalar($v) ? $v : json_encode($v) }}</div>@endforeach
                                @else
                                    {{ $row->details }}
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $row->outcome === 'ok' ? 'bg-success' : 'bg-danger' }}">{{ $row->outcome === 'ok' ? 'done' : 'failed' }}</span>
                                <div class="small text-muted">{{ $row->ip_address }}</div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">Nothing recorded yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @include('partials.pagination', ['pager' => $pager])
    </div>
</div>
@endsection
