{{-- One-page library report for the headteacher. Print it, or choose "Save as PDF" in the print dialog. Always light. --}}
@php
    $t = $stats['tiles'];
    $tp = $stats['tiles_prev'];
    $p = $stats['period'];
    $chg = function ($now, $before, $unit = '') {
        if ($now === null || $before === null) return '';
        $d = round($now - $before, 1);
        return $d == 0 ? '(no change)' : '('.($d > 0 ? '▲ +' : '▼ ').rtrim(rtrim(number_format($d, 1), '0'), '.').$unit.')';
    };
@endphp
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Library report – {{ $p['label'] }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/ladybird-favicon.png') }}?v=4">
    <style>
        @page { size: A4; margin: 14mm 14mm 16mm; }
        * { box-sizing: border-box; }
        body { font-family: Arial, Helvetica, sans-serif; color: #111; margin: 0; background: #f3f4f6; font-size: 10.5pt; }
        .toolbar { position: sticky; top: 0; background: #2a3042; color: #fff; padding: 10px 16px; display: flex; gap: 10px; align-items: center; }
        .toolbar button, .toolbar a { background: #5F9EA0; color: #fff; border: 0; padding: 7px 14px; border-radius: 4px; cursor: pointer; text-decoration: none; font-size: 14px; }
        .page { width: 210mm; min-height: 297mm; margin: 16px auto; background: #fff; padding: 14mm; }
        header { display: flex; justify-content: space-between; align-items: flex-end; border-bottom: 2px solid #111; padding-bottom: 6px; margin-bottom: 12px; }
        header h1 { font-size: 16pt; margin: 0; }
        header .meta { text-align: right; font-size: 9pt; color: #444; }
        h2 { font-size: 11.5pt; margin: 14px 0 6px; border-bottom: 1px solid #ccc; padding-bottom: 3px; }
        .tiles { display: grid; grid-template-columns: repeat(5, 1fr); gap: 6px; }
        .tile { border: 1px solid #ccc; border-radius: 4px; padding: 6px 8px; }
        .tile .l { font-size: 8.5pt; color: #444; }
        .tile .v { font-size: 15pt; font-weight: bold; margin: 2px 0; }
        .tile .c { font-size: 8pt; color: #444; }
        .cols { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
        table { width: 100%; border-collapse: collapse; font-size: 9pt; }
        th, td { text-align: left; padding: 3px 5px; border-bottom: 1px solid #e3e3e3; }
        th { background: #f2f2f2; }
        td.n, th.n { text-align: right; }
        .note { font-size: 8.5pt; color: #555; margin-top: 4px; }
        footer { margin-top: 14px; font-size: 8pt; color: #666; border-top: 1px solid #ccc; padding-top: 4px; display: flex; justify-content: space-between; }
        @media print { body { background: #fff; } .toolbar { display: none; } .page { margin: 0; padding: 0; width: auto; min-height: 0; } }
    </style>
</head>
<body>
<div class="toolbar">
    <button onclick="window.print()">Print / Save as PDF</button>
    <a href="/Circulation/Stats?{{ http_build_query(request()->only(['period', 'from', 'to'])) }}">Back to statistics</a>
</div>
<div class="page">
    <header>
        <div>
            <h1>Library report &mdash; {{ $p['label'] }}</h1>
            <div>{{ $school_name }}</div>
        </div>
        <div class="meta">
            {{ date('d M Y', strtotime($p['from'])) }} – {{ date('d M Y', strtotime($p['to'])) }}<br>
            Compared with: {{ $stats['previous']['label'] }}
        </div>
    </header>

    <div class="tiles">
        <div class="tile"><div class="l">Loans</div><div class="v">{{ number_format($t['loans']) }}</div><div class="c">{{ $chg($t['loans'], $tp['loans']) }}</div></div>
        <div class="tile"><div class="l">On-time returns</div><div class="v">{{ $t['on_time_rate'] === null ? '–' : $t['on_time_rate'].'%' }}</div><div class="c">{{ $chg($t['on_time_rate'], $tp['on_time_rate'], ' pts') }}</div></div>
        <div class="tile"><div class="l">Overdue now</div><div class="v">{{ number_format($stats['overdue_now']) }}</div><div class="c">on {{ date('d M Y') }}</div></div>
        <div class="tile"><div class="l">Students who borrowed</div><div class="v">{{ $t['participation'] === null ? '–' : $t['participation'].'%' }}</div><div class="c">{{ $t['readers'] }} of {{ $t['students'] }} {{ $chg($t['participation'], $tp['participation'], ' pts') }}</div></div>
        <div class="tile"><div class="l">Average loan</div><div class="v">{{ $t['loan_days'] === null ? '–' : number_format($t['loan_days'], 1).' d' }}</div><div class="c">{{ $chg($t['loan_days'], $tp['loan_days'], ' d') }}</div></div>
    </div>

    <div class="cols">
        <div>
            <h2>Top readers</h2>
            <table><thead><tr><th>#</th><th>Name</th><th>Class</th><th class="n">Loans</th></tr></thead><tbody>
                @forelse ($stats['top_readers'] as $i => $r)<tr><td>{{ $i + 1 }}</td><td>{{ $r['name'] }}</td><td>{{ $r['class'] }}</td><td class="n">{{ $r['loans'] }}</td></tr>@empty<tr><td colspan="4">No loans.</td></tr>@endforelse
            </tbody></table>
        </div>
        <div>
            <h2>Most borrowed titles</h2>
            <table><thead><tr><th>#</th><th>Title</th><th class="n">Loans</th></tr></thead><tbody>
                @forelse ($stats['top_titles'] as $i => $r)<tr><td>{{ $i + 1 }}</td><td>{{ $r['title'] }}</td><td class="n">{{ $r['loans'] }}</td></tr>@empty<tr><td colspan="3">No loans.</td></tr>@endforelse
            </tbody></table>
        </div>
    </div>

    <h2>Reading by class</h2>
    @php $late = collect($stats['late_by_class'])->keyBy('class'); @endphp
    <table><thead><tr><th>Class</th><th class="n">Students</th><th class="n">Loans</th><th class="n">Loans per student</th><th class="n">Students who borrowed</th><th class="n">Late returns</th></tr></thead><tbody>
        @foreach ($stats['by_class'] as $r)
            <tr><td>{{ $r['class'] }}</td><td class="n">{{ $r['students'] }}</td><td class="n">{{ $r['loans'] }}</td><td class="n">{{ $r['per_student'] }}</td><td class="n">{{ $r['participation'] }}%</td>
                <td class="n">{{ isset($late[$r['class']]) ? $late[$r['class']]['late'].' of '.$late[$r['class']]['due'].' ('.$late[$r['class']]['rate'].'%)' : '–' }}</td></tr>
        @endforeach
    </tbody></table>

    <div class="cols">
        <div>
            <h2>Repeat late returners</h2>
            <table><thead><tr><th>Name</th><th>Class</th><th class="n">Late</th><th class="n">Avg days</th></tr></thead><tbody>
                @forelse (array_slice($stats['repeat_late'], 0, 10) as $r)<tr><td>{{ $r['name'] }}</td><td>{{ $r['class'] }}</td><td class="n">{{ $r['late'] }}</td><td class="n">{{ number_format($r['avg_days'], 1) }}</td></tr>@empty<tr><td colspan="4">None.</td></tr>@endforelse
            </tbody></table>
        </div>
        <div>
            <h2>Collection</h2>
            <table><tbody>
                <tr><td>Copies borrowed in the last 12 months</td><td class="n">{{ $stats['collection_use']['rate'] === null ? '–' : $stats['collection_use']['rate'].'%' }}</td></tr>
                <tr><td>Copies never borrowed in 12 months</td><td class="n">{{ number_format($stats['never_borrowed']['count']) }}</td></tr>
                <tr><td>Busiest time at the desk</td><td class="n">{{ $stats['busy']['busiest']['label'] ?? '–' }}</td></tr>
                @foreach (array_slice($stats['top_categories'], 0, 3) as $i => $r)<tr><td>Category #{{ $i + 1 }}</td><td class="n">{{ $r['category'] }} ({{ $r['loans'] }})</td></tr>@endforeach
            </tbody></table>
            <p class="note">On-time returns: books due in the period that came back on or before the due day. Students who borrowed: current students with at least one loan.</p>
        </div>
    </div>

    <footer><span>Ladybird Library System</span><span>Printed {{ date('d M Y H:i') }} by {{ $printed_by }}</span></footer>
</div>
</body>
</html>
