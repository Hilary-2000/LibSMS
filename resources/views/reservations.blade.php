@extends('layouts.app', ['active' => 'Circulation'])
@section('title', 'Reservations')
@section('page_title', 'Reservations')

@section('content')
<div class="card">
    <div class="card-body">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
            <h5 class="card-title mb-0">Waiting list</h5>
            <div class="btn-group btn-group-sm" role="group" aria-label="Filter by status">
                @php $filters = ["open" => "Open", "ready" => "Ready for pickup", "waiting" => "Waiting", "fulfilled" => "Fulfilled", "cancelled" => "Cancelled", "all" => "All"]; @endphp
                @foreach ($filters as $key => $label)
                    @php
                        $n = $key === "open" ? (($counts["waiting"] ?? 0) + ($counts["ready"] ?? 0)) : ($key === "all" ? array_sum($counts) : ($counts[$key] ?? 0));
                    @endphp
                    <a href="?status={{ $key }}" class="btn {{ $status === $key ? 'btn-primary' : 'btn-outline-primary' }}">{{ $label }} <span class="badge bg-light text-dark ms-1">{{ $n }}</span></a>
                @endforeach
            </div>
        </div>
        <p class="text-muted small mb-3">
            To reserve a book, open it (Acquisitions or a loan) and choose <b>Reserve</b> while every copy is out.
            When a copy is checked in, it is kept aside for the first person waiting and you get a notification.
        </p>

        <div class="table-responsive">
            <table class="table table-bordered align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No.</th>
                        <th>Book</th>
                        <th>Reserved for</th>
                        <th>Status</th>
                        <th>Reserved on</th>
                        <th>Comments</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rows as $i => $row)
                        <tr>
                            <td>{{ ($pager->firstItem() ?? 1) + $i }}</td>
                            <td>{{ $row->book_title }}<br><small class="text-muted">{{ str_starts_with($row->book_key, 'id:') ? 'Copy '.substr($row->book_key, 3) : 'ISBN '.$row->book_key }}</small></td>
                            <td>{{ $row->borrower_name }} <span class="badge badge-soft-secondary">{{ $row->borrower_type }}</span></td>
                            <td>
                                @if ($row->status === 'ready')
                                    <span class="badge bg-success">Ready for pickup</span>
                                    <div class="small mt-1">Kept aside: <b>{{ $row->held_code }}</b> since {{ date("D dS M Y", strtotime($row->date_ready)) }}</div>
                                @elseif ($row->status === 'waiting')
                                    <span class="badge bg-warning text-dark">Waiting</span> <span class="small">#{{ $row->position }} in queue</span>
                                @elseif ($row->status === 'fulfilled')
                                    <span class="badge bg-primary">Fulfilled</span>
                                @else
                                    <span class="badge bg-secondary">Cancelled</span>
                                @endif
                            </td>
                            <td>{{ date("D dS M Y", strtotime($row->date_reserved)) }}</td>
                            <td>{{ $row->comments }}</td>
                            <td class="text-nowrap">
                                @if ($row->status === 'ready')
                                    <a href="/Circulation/check-out/{{ $row->held_book_id }}" class="btn btn-sm btn-soft-success" title="Check the kept copy out to them"><i class="bx bx-log-out"></i> Lend</a>
                                @endif
                                @if (in_array($row->status, ['waiting', 'ready']))
                                    <form method="POST" action="/Circulation/Reservations/{{ $row->reservation_id }}/Cancel" class="d-inline" onsubmit="return confirm('Cancel this reservation?')">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-soft-danger"><i class="bx bx-x"></i> Cancel</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">No reservations here.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @include('partials.pagination', ['pager' => $pager])
    </div>
</div>
@endsection
