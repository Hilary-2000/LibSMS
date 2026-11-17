{{--
    Page links + page size for a server-paginated table.
    Usage: @include('partials.pagination', ['pager' => $pager])
--}}
@if (isset($pager) && $pager->total() > 0)
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mt-3">
        <div class="text-muted small">
            Showing <b>{{ $pager->firstItem() }}</b>–<b>{{ $pager->lastItem() }}</b> of <b>{{ number_format($pager->total()) }}</b>
            <form method="GET" action="{{ $pager->path() }}" class="d-inline ms-2">
                @foreach (request()->except([$pager->getPageName(), 'per_page']) as $k => $v)
                    @if (!is_array($v))<input type="hidden" name="{{ $k }}" value="{{ $v }}">@endif
                @endforeach
                <select name="per_page" class="form-select form-select-sm d-inline-block w-auto" onchange="this.form.submit()" aria-label="Rows per page">
                    @foreach (\App\Support\RawPaginator::SIZES as $size)
                        <option value="{{ $size }}" @selected($pager->perPage() == $size)>{{ $size }} per page</option>
                    @endforeach
                </select>
            </form>
        </div>
        @if ($pager->hasPages())
            <div>{{ $pager->onEachSide(1)->links() }}</div>
        @endif
    </div>
@endif
