@extends('layouts.app', ['active' => 'Circulation'])
@section('title', 'Library statistics')
@section('page_title', 'Statistics')

@php
    $t = $stats['tiles'];
    $tp = $stats['tiles_prev'];
    $query = request()->only(['period', 'from', 'to']);
    // change vs the previous period: arrow + text, coloured only as good / bad, never colour alone
    $delta = function ($now, $before, $unit = '', $higherIsGood = true) use ($stats) {
        if ($now === null || $before === null) return '<span class="text-muted">no earlier data</span>';
        $d = round($now - $before, 1);
        if ($d == 0) return '<span class="text-muted"><i class="bx bx-minus"></i> same as before</span>';
        $good = ($d > 0) === $higherIsGood;
        return '<span class="'.($good ? 'text-success' : 'text-danger').'"><i class="bx '.($d > 0 ? 'bx-up-arrow-alt' : 'bx-down-arrow-alt').'"></i>'
            .e(($d > 0 ? '+' : '').rtrim(rtrim(number_format($d, 1), '0'), '.').$unit).'</span> <span class="text-muted">vs '.e($stats['previous']['label']).'</span>';
    };
    $toggle = fn ($id) => '<button class="btn btn-sm btn-light" type="button" data-bs-toggle="collapse" data-bs-target="#'.$id.'" aria-expanded="false">View as table</button>';
@endphp

@section('content')
{{-- period + actions --}}
<div class="card">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end" id="period_form">
            <div class="col-md-3">
                <label class="form-label" for="period">Period</label>
                <select class="form-select" name="period" id="period" onchange="document.getElementById('custom_dates').classList.toggle('d-none', this.value !== 'custom'); if (this.value !== 'custom') this.form.submit();">
                    @foreach ($periods as $key => $label)
                        <option value="{{ $key }}" @selected($stats['period']['key'] === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-5 {{ $stats['period']['key'] === 'custom' ? '' : 'd-none' }}" id="custom_dates">
                <div class="row g-2">
                    <div class="col"><label class="form-label" for="from">From</label><input type="date" class="form-control" name="from" id="from" value="{{ date('Y-m-d', strtotime($stats['period']['from'])) }}"></div>
                    <div class="col"><label class="form-label" for="to">To</label><input type="date" class="form-control" name="to" id="to" value="{{ date('Y-m-d', strtotime($stats['period']['to'])) }}"></div>
                    <div class="col-auto"><button class="btn btn-primary" type="submit">Apply</button></div>
                </div>
            </div>
            <div class="col-md text-md-end">
                <div class="small text-muted mb-1">
                    @if ($stats['period']['key'] === 'custom'){{ $stats['period']['label'] }}@else{{ $stats['period']['label'] }}: {{ date('d M Y', strtotime($stats['period']['from'])) }} – {{ date('d M Y', strtotime($stats['period']['to'])) }}@endif
                </div>
                <a class="btn btn-outline-success btn-sm" href="/Circulation/Stats/Export?{{ http_build_query($query) }}"><i class="mdi mdi-file-excel"></i> Excel</a>
                <a class="btn btn-outline-primary btn-sm" href="/Circulation/Stats/Report?{{ http_build_query($query) }}" target="_blank"><i class="mdi mdi-printer"></i> Term report (print / PDF)</a>
            </div>
        </form>
    </div>
</div>

{{-- 1. headline tiles --}}
<div class="row">
    @php
        $tiles = [
            ['Loans', number_format($t['loans']), 'bx-book-open', $delta($t['loans'], $tp['loans'])],
            ['On-time returns', $t['on_time_rate'] === null ? '–' : $t['on_time_rate'].'%', 'bx-time-five', $t['on_time_rate'] === null ? '<span class="text-muted">no books due yet</span>' : $delta($t['on_time_rate'], $tp['on_time_rate'], ' pts')],
            ['Overdue right now', number_format($stats['overdue_now']), 'bx-error-circle', '<a href="/Circulation#checked_out">see who has them</a>'],
            ['Reading participation', $t['participation'] === null ? '–' : $t['participation'].'%', 'bx-group', '<span class="text-muted">'.$t['readers'].' of '.$t['students'].' students</span> &middot; '.$delta($t['participation'], $tp['participation'], ' pts')],
            ['Average loan', $t['loan_days'] === null ? '–' : number_format($t['loan_days'], 1).' days', 'bx-calendar', $delta($t['loan_days'], $tp['loan_days'], ' d', false)],
        ];
    @endphp
    @foreach ($tiles as [$label, $value, $icon, $sub])
        <div class="col-md-6 col-xl">
            <div class="card mini-stats-wid">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-muted fw-medium mb-1">{{ $label }}</p>
                            <h4 class="mb-1">{{ $value }}</h4>
                            <div class="small">{!! $sub !!}</div>
                        </div>
                        <div class="avatar-sm rounded-circle bg-primary align-self-center mini-stat-icon flex-shrink-0">
                            <span class="avatar-title rounded-circle bg-primary"><i class="bx {{ $icon }} font-size-22"></i></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

{{-- 2. who's reading --}}
<h5 class="mt-2 mb-3 insights-heading"><i class="bx bx-user-voice text-primary"></i> Who's reading</h5>
<div class="row">
    <div class="col-xl-5">
        <div class="card"><div class="card-body">
            <div class="d-flex justify-content-between"><div><h5 class="card-title mb-1">Top readers</h5><p class="text-muted small">Students with the most loans &middot; click a name for their history</p></div>{!! $toggle('t_readers') !!}</div>
            <div id="ch_readers" style="min-height: 320px"></div>
            <div class="collapse" id="t_readers"><table class="table table-sm mb-0"><thead><tr><th>Name</th><th>Class</th><th>Loans</th></tr></thead><tbody>
                @forelse ($stats['top_readers'] as $r)<tr><td><a href="/Circulation/Stats/View/Student/{{ $r['adm_no'] }}">{{ $r['name'] }}</a></td><td>{{ $r['class'] }}</td><td>{{ $r['loans'] }}</td></tr>@empty<tr><td colspan="3" class="text-muted">No loans in this period.</td></tr>@endforelse
            </tbody></table></div>
        </div></div>
    </div>
    <div class="col-xl-4">
        <div class="card"><div class="card-body">
            <div class="d-flex justify-content-between"><div><h5 class="card-title mb-1">Loans per student, by class</h5><p class="text-muted small">Fair across class sizes &middot; hover for participation</p></div>{!! $toggle('t_classes') !!}</div>
            <div id="ch_classes" style="min-height: 320px"></div>
            <div class="collapse" id="t_classes"><table class="table table-sm mb-0"><thead><tr><th>Class</th><th>Students</th><th>Loans</th><th>Per student</th><th>Borrowed</th></tr></thead><tbody>
                @foreach ($stats['by_class'] as $r)<tr><td>{{ $r['class'] }}</td><td>{{ $r['students'] }}</td><td>{{ $r['loans'] }}</td><td>{{ $r['per_student'] }}</td><td>{{ $r['participation'] }}%</td></tr>@endforeach
            </tbody></table></div>
        </div></div>
    </div>
    <div class="col-xl-3">
        <div class="card"><div class="card-body">
            <div class="d-flex justify-content-between"><div><h5 class="card-title mb-1">Students vs staff</h5><p class="text-muted small">Loans in this period</p></div>{!! $toggle('t_types') !!}</div>
            <div id="ch_types" style="min-height: 300px"></div>
            <div class="collapse" id="t_types"><table class="table table-sm mb-0"><tbody><tr><td>Students</td><td>{{ $stats['borrower_types']['student'] }}</td></tr><tr><td>Staff</td><td>{{ $stats['borrower_types']['staff'] }}</td></tr></tbody></table></div>
        </div></div>
    </div>
</div>

{{-- 3. what's being read --}}
<h5 class="mt-2 mb-3 insights-heading"><i class="bx bx-book-open text-primary"></i> What's being read</h5>
<div class="row">
    <div class="col-xl-6">
        <div class="card"><div class="card-body">
            <div class="d-flex justify-content-between"><div><h5 class="card-title mb-1">Top titles</h5><p class="text-muted small">Click a title to open the book</p></div>{!! $toggle('t_titles') !!}</div>
            <div id="ch_titles" style="min-height: 320px"></div>
            <div class="collapse" id="t_titles"><table class="table table-sm mb-0"><thead><tr><th>Title</th><th>Loans</th></tr></thead><tbody>
                @forelse ($stats['top_titles'] as $r)<tr><td><a href="/Acquisitions/Book-details/{{ $r['book_id'] }}">{{ $r['title'] }}</a></td><td>{{ $r['loans'] }}</td></tr>@empty<tr><td colspan="2" class="text-muted">No loans in this period.</td></tr>@endforelse
            </tbody></table></div>
        </div></div>
    </div>
    <div class="col-xl-6">
        <div class="card"><div class="card-body">
            <div class="d-flex justify-content-between"><div><h5 class="card-title mb-1">Top categories</h5><p class="text-muted small">Guides what to buy more of</p></div>{!! $toggle('t_categories') !!}</div>
            <div id="ch_categories" style="min-height: 320px"></div>
            <div class="collapse" id="t_categories"><table class="table table-sm mb-0"><thead><tr><th>Category</th><th>Loans</th></tr></thead><tbody>
                @forelse ($stats['top_categories'] as $r)<tr><td>{{ $r['category'] }}</td><td>{{ $r['loans'] }}</td></tr>@empty<tr><td colspan="2" class="text-muted">No loans in this period.</td></tr>@endforelse
            </tbody></table></div>
        </div></div>
    </div>
</div>
<div class="row">
    <div class="col-xl-4">
        <div class="card"><div class="card-body">
            <h5 class="card-title mb-1">Collection use</h5>
            <p class="text-muted small">Copies borrowed at least once in the last 12 months</p>
            @php $cu = $stats['collection_use']; @endphp
            <div class="display-6 fw-semibold mb-1">{{ $cu['rate'] === null ? '–' : $cu['rate'].'%' }}</div>
            <div class="text-muted mb-3">{{ number_format($cu['used']) }} of {{ number_format($cu['copies']) }} copies</div>
            <div class="progress" style="height: 10px" role="progressbar" aria-label="Collection used" aria-valuenow="{{ $cu['rate'] ?? 0 }}" aria-valuemin="0" aria-valuemax="100">
                <div class="progress-bar" style="width: {{ $cu['rate'] ?? 0 }}%"></div>
            </div>
            <p class="small text-muted mt-3 mb-0">A low figure means many books sit unused: promote them, move them, or buy fewer of that kind.</p>
        </div></div>
    </div>
    <div class="col-xl-8">
        <div class="card"><div class="card-body">
            <h5 class="card-title mb-1">Never borrowed <span class="badge badge-soft-secondary">{{ number_format($stats['never_borrowed']['count']) }}</span></h5>
            <p class="text-muted small">Copies nobody has taken out in the last 12 months, oldest first{{ $stats['never_borrowed']['count'] > 50 ? ' (first 50 shown; all are in the Excel file)' : '' }}</p>
            <div class="table-responsive" style="max-height: 300px">
                <table class="table table-sm align-middle mb-0">
                    <thead class="table-light"><tr><th>Title</th><th>Call no.</th><th>Recorded</th><th>Last borrowed</th></tr></thead>
                    <tbody>
                        @forelse ($stats['never_borrowed']['rows'] as $r)
                            <tr>
                                <td><a href="/Acquisitions/Book-details/{{ $r->book_id }}">{{ $r->book_title }}</a></td>
                                <td>{{ $r->call_no }}</td>
                                <td>{{ $r->date_recorded ? date('M Y', strtotime($r->date_recorded)) : '' }}</td>
                                <td>{{ $r->last_borrowed ? date('d M Y', strtotime($r->last_borrowed)) : 'never' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-muted">Every copy was borrowed in the last 12 months.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div></div>
    </div>
</div>

{{-- 4. late returns --}}
<h5 class="mt-2 mb-3 insights-heading"><i class="bx bx-alarm text-primary"></i> Late returns</h5>
<div class="row">
    <div class="col-xl-8">
        <div class="card"><div class="card-body">
            <div class="d-flex justify-content-between"><div><h5 class="card-title mb-1">Books due each {{ $stats['weekly_due']['unit'] }}</h5><p class="text-muted small">Returned on time vs late or still out</p></div>{!! $toggle('t_weekly') !!}</div>
            <div id="ch_weekly" style="min-height: 320px"></div>
            <div class="collapse" id="t_weekly"><table class="table table-sm mb-0"><thead><tr><th>{{ ucfirst($stats['weekly_due']['unit']) }} starting</th><th>On time</th><th>Late / still out</th></tr></thead><tbody>
                @foreach ($stats['weekly_due']['labels'] as $k => $label)<tr><td>{{ $label }}</td><td>{{ $stats['weekly_due']['on_time'][$k] }}</td><td>{{ $stats['weekly_due']['late'][$k] }}</td></tr>@endforeach
            </tbody></table></div>
        </div></div>
    </div>
    <div class="col-xl-4">
        <div class="card"><div class="card-body">
            <div class="d-flex justify-content-between"><div><h5 class="card-title mb-1">Late-return rate by class</h5><p class="text-muted small">Share of books due that came back late</p></div>{!! $toggle('t_lateclass') !!}</div>
            <div id="ch_lateclass" style="min-height: 320px"></div>
            <div class="collapse" id="t_lateclass"><table class="table table-sm mb-0"><thead><tr><th>Class</th><th>Due</th><th>Late</th><th>%</th></tr></thead><tbody>
                @forelse ($stats['late_by_class'] as $r)<tr><td>{{ $r['class'] }}</td><td>{{ $r['due'] }}</td><td>{{ $r['late'] }}</td><td>{{ $r['rate'] }}%</td></tr>@empty<tr><td colspan="4" class="text-muted">No books were due in this period.</td></tr>@endforelse
            </tbody></table></div>
        </div></div>
    </div>
</div>
<div class="card"><div class="card-body">
    <h5 class="card-title mb-1">Repeat late returners</h5>
    <p class="text-muted small">Borrowers with two or more late returns among books due in this period &mdash; worth a word with the class teacher.</p>
    <div class="table-responsive">
        <table class="table table-sm align-middle mb-0">
            <thead class="table-light"><tr><th>Name</th><th>Class</th><th>Late returns</th><th>Still out</th><th>Average days late</th></tr></thead>
            <tbody>
                @forelse ($stats['repeat_late'] as $r)
                    <tr>
                        <td><a href="/Circulation/Stats/View/{{ $r['type'] === 'staff' ? 'Staff' : 'Student' }}/{{ $r['id'] }}">{{ $r['name'] }}</a></td>
                        <td>{{ $r['class'] }}</td>
                        <td><b>{{ $r['late'] }}</b></td>
                        <td>{{ $r['still_out'] ?: '–' }}</td>
                        <td>{{ number_format($r['avg_days'], 1) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-muted">Nobody returned two or more books late in this period.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div></div>

{{-- 5. when the library is busy --}}
<h5 class="mt-2 mb-3 insights-heading"><i class="bx bx-time text-primary"></i> When the library is busy</h5>
<div class="card"><div class="card-body">
    <div class="d-flex justify-content-between"><div>
        <h5 class="card-title mb-1">Check-outs and returns by day and hour</h5>
        <p class="text-muted small">Darker = busier. {!! $stats['busy']['busiest'] ? 'Busiest slot: <b>'.e($stats['busy']['busiest']['label']).'</b> ('.$stats['busy']['busiest']['n'].' transactions).' : 'No transactions in this period.' !!} Use it to staff the desk and plan class visits.</p>
    </div>{!! $toggle('t_busy') !!}</div>
    <div id="ch_busy" style="min-height: 330px"></div>
    <div class="collapse" id="t_busy"><div class="table-responsive"><table class="table table-sm mb-0">
        <thead><tr><th>Day</th>@foreach (($stats['busy']['series'][0]['data'] ?? []) as $cell)<th>{{ $cell['x'] }}</th>@endforeach</tr></thead>
        <tbody>@foreach (array_reverse($stats['busy']['series']) as $day)<tr><td>{{ $day['name'] }}</td>@foreach ($day['data'] as $cell)<td>{{ $cell['y'] ?: '' }}</td>@endforeach</tr>@endforeach</tbody>
    </table></div></div>
</div></div>

{{-- individual look-up (the previous Statistics page) --}}
<div class="card"><div class="card-body">
    <h5 class="card-title mb-1">Look up one borrower</h5>
    <p class="text-muted small">Full borrowing history and statistics for a student or staff member.</p>
    <div class="row g-2">
        <div class="col-md-6">
            <select class="form-control select2" id="lookup_student" aria-label="Student" onchange="if (this.value) location.href = '/Circulation/Stats/View/Student/' + encodeURIComponent(this.value)">
                <option value="">Find a student…</option>
                @foreach ($students as $group)
                    <optgroup label="{{ $group->class_name }}">
                        @foreach ($group->student_data as $s)<option value="{{ $s->adm_no }}">{{ ucwords(strtolower($s->first_name.' '.$s->second_name)) }} ({{ $s->adm_no }})</option>@endforeach
                    </optgroup>
                @endforeach
            </select>
        </div>
        <div class="col-md-6">
            <select class="form-control select2" id="lookup_staff" aria-label="Staff member" onchange="if (this.value) location.href = '/Circulation/Stats/View/Staff/' + encodeURIComponent(this.value)">
                <option value="">Find a staff member…</option>
                @foreach ($staff as $s)<option value="{{ $s->user_id }}">{{ ucwords(strtolower($s->fullname)) }}</option>@endforeach
            </select>
        </div>
    </div>
</div></div>
@endsection

@push('styles')
<style>
    .insights-heading { font-weight: 600; display: flex; align-items: center; gap: .5rem; }
    .insights-heading i { font-size: 1.35rem; }
</style>
@endpush

@push('scripts')
<script src="/assets/libs/apexcharts/apexcharts.min.js"></script>
<script>
    $(function () { $(".select2").select2({ width: "100%" }).on("select2:select", function () { this.onchange && this.onchange(); }); });

    // Charts. Colours: validated categorical palette, light and dark steps (see the dashboard);
    // the heatmap uses one hue (blue) from light to dark. Each chart has a "View as table" toggle.
    (function () {
        var S = @json($stats);
        var charts = [];
        function palette() {
            var dark = document.documentElement.getAttribute("data-bs-theme") === "dark";
            return dark
                ? { series: ["#3987e5", "#d95926", "#199e70"], ink: "#c3cbe4", grid: "#32394e", surface: "#2a3042", mode: "dark",
                    heat: ["#32394e", "#184f95", "#256abf", "#3987e5", "#6da7ec", "#9ec5f4"] }
                : { series: ["#2a78d6", "#eb6834", "#1baf7a"], ink: "#52514e", grid: "#e9e9e7", surface: "#fcfcfb", mode: "light",
                    heat: ["#f1f1ef", "#cde2fb", "#86b6ef", "#3987e5", "#256abf", "#104281"] };
        }
        var whole = function (v) { return Math.round(v); };
        function maxOf(arr) { return Math.max.apply(null, (arr || []).concat([0])); }

        function render() {
            charts.forEach(function (c) { c.destroy(); });
            charts = [];
            var P = palette();
            var base = {
                theme: { mode: P.mode }, tooltip: { theme: P.mode },
                chart: { toolbar: { show: false }, fontFamily: "inherit", foreColor: P.ink, background: "transparent", animations: { enabled: false } },
                grid: { borderColor: P.grid, strokeDashArray: 3 },
                dataLabels: { enabled: false },
                legend: { position: "top", horizontalAlign: "left", markers: { radius: 12 } },
                noData: { text: "Nothing in this period" }
            };
            function draw(id, opts) {
                var el = document.getElementById(id);
                if (!el) return;
                var c = new ApexCharts(el, $.extend(true, {}, base, opts));
                c.render();
                charts.push(c);
            }
            function hbar(id, labels, values, name, onClick) {
                draw(id, {
                    chart: { type: "bar", height: 320, events: onClick ? { dataPointSelection: function (e, ctx, cfg) { onClick(cfg.dataPointIndex); } } : {} },
                    series: [{ name: name, data: values }],
                    colors: [P.series[0]],
                    plotOptions: { bar: { horizontal: true, borderRadius: 4, borderRadiusApplication: "end", barHeight: "62%" } },
                    xaxis: { categories: labels, max: maxOf(values) < 4 ? 4 : undefined, tickAmount: 4, labels: { formatter: whole } },
                    yaxis: { labels: { maxWidth: 220 } },
                    grid: { padding: { left: 16 } },
                    stroke: { width: 2, colors: [P.surface] },
                    states: onClick ? { hover: { filter: { type: "darken", value: 0.85 } } } : {}
                });
            }

            // who's reading
            hbar("ch_readers", S.top_readers.map(function (r) { return r.name + " (" + r.class + ")"; }), S.top_readers.map(function (r) { return r.loans; }), "Loans",
                function (i) { location.href = "/Circulation/Stats/View/Student/" + encodeURIComponent(S.top_readers[i].adm_no); });
            draw("ch_classes", {
                chart: { type: "bar", height: 320 },
                series: [{ name: "Loans per student", data: S.by_class.map(function (r) { return r.per_student; }) }],
                colors: [P.series[0]],
                plotOptions: { bar: { borderRadius: 4, borderRadiusApplication: "end", columnWidth: "55%" } },
                xaxis: { categories: S.by_class.map(function (r) { return r.class; }) },
                yaxis: { min: 0, tickAmount: 4, forceNiceScale: true, decimalsInFloat: 2 },
                tooltip: { y: { formatter: function (v, o) { var r = S.by_class[o.dataPointIndex]; return v + " per student (" + r.loans + " loans, " + r.participation + "% of " + r.students + " borrowed)"; } } },
                stroke: { width: 2, colors: [P.surface] }
            });
            draw("ch_types", {
                chart: { type: "donut", height: 300 },
                series: [S.borrower_types.student, S.borrower_types.staff],
                labels: ["Students", "Staff"],
                colors: P.series.slice(0, 2),
                stroke: { width: 2, colors: [P.surface] },
                legend: { position: "bottom" },
                dataLabels: { enabled: true, formatter: function (v) { return Math.round(v) + "%"; }, dropShadow: { enabled: false } },
                plotOptions: { pie: { donut: { size: "62%", labels: { show: true, total: { show: true, label: "Loans", color: P.ink } } } } }
            });

            // what's being read
            hbar("ch_titles", S.top_titles.map(function (r) { return r.title.length > 38 ? r.title.slice(0, 37) + "…" : r.title; }), S.top_titles.map(function (r) { return r.loans; }), "Loans",
                function (i) { location.href = "/Acquisitions/Book-details/" + encodeURIComponent(S.top_titles[i].book_id); });
            hbar("ch_categories", S.top_categories.map(function (r) { return r.category; }), S.top_categories.map(function (r) { return r.loans; }), "Loans");

            // late returns
            draw("ch_weekly", {
                chart: { type: "bar", height: 320, stacked: true },
                series: [{ name: "Returned on time", data: S.weekly_due.on_time }, { name: "Late / still out", data: S.weekly_due.late }],
                colors: [P.series[0], P.series[1]],
                plotOptions: { bar: { borderRadius: 3, borderRadiusApplication: "end", borderRadiusWhenStacked: "last", columnWidth: "60%" } },
                xaxis: { categories: S.weekly_due.labels, labels: { rotate: -45, hideOverlappingLabels: true } },
                yaxis: { min: 0, max: maxOf(S.weekly_due.on_time.map(function (v, i) { return v + S.weekly_due.late[i]; })) < 4 ? 4 : undefined, tickAmount: 4, labels: { formatter: whole } },
                tooltip: { shared: true, intersect: false, theme: P.mode },
                stroke: { width: 2, colors: [P.surface] }
            });
            draw("ch_lateclass", {
                chart: { type: "bar", height: 320 },
                series: [{ name: "Late (%)", data: S.late_by_class.map(function (r) { return r.rate; }) }],
                colors: [P.series[0]],
                plotOptions: { bar: { borderRadius: 4, borderRadiusApplication: "end", columnWidth: "55%" } },
                xaxis: { categories: S.late_by_class.map(function (r) { return r.class; }) },
                yaxis: { min: 0, max: 100, tickAmount: 4, labels: { formatter: function (v) { return Math.round(v) + "%"; } } },
                tooltip: { y: { formatter: function (v, o) { var r = S.late_by_class[o.dataPointIndex]; return v + "% (" + r.late + " of " + r.due + " due)"; } } },
                stroke: { width: 2, colors: [P.surface] }
            });

            // busy hours heatmap: one hue, light -> dark
            var top = 0;
            S.busy.series.forEach(function (d) { d.data.forEach(function (c) { top = Math.max(top, c.y); }); });
            var step = Math.max(1, Math.ceil(top / 5));
            var ranges = [{ from: 0, to: 0, color: P.heat[0], name: "none" }];
            for (var i = 1; i <= 5; i++) {
                ranges.push({ from: (i - 1) * step + 1, to: i * step, color: P.heat[i], name: ((i - 1) * step + 1) + "–" + (i * step) });
            }
            draw("ch_busy", {
                chart: { type: "heatmap", height: 330 },
                series: S.busy.series,
                plotOptions: { heatmap: { radius: 3, enableShades: false, colorScale: { ranges: ranges } } },
                stroke: { width: 2, colors: [P.surface] },
                legend: { position: "bottom" },
                tooltip: { y: { formatter: function (v) { return v + " transaction" + (v === 1 ? "" : "s"); } } }
            });
        }
        render();
        document.addEventListener("library:themechange", render);
    })();
</script>
@endpush
