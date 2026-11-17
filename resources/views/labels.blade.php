{{-- Printable barcode labels: A4, 3 columns x 8 rows (63.5 x 33.9 mm, the common "24 per sheet" label stock). --}}
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Book labels ({{ count($books) }})</title>
    <link rel="icon" type="image/png" href="{{ asset('images/ladybird-favicon.png') }}?v=4">
    <style>
        @page { size: A4; margin: 12.9mm 7.2mm; }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Arial, Helvetica, sans-serif; color: #111; background: #f3f4f6; }
        .toolbar { position: sticky; top: 0; background: #2a3042; color: #fff; padding: 10px 16px; display: flex; gap: 12px; align-items: center; flex-wrap: wrap; font-size: 14px; }
        .toolbar button, .toolbar a { background: #5F9EA0; color: #fff; border: 0; padding: 7px 14px; border-radius: 4px; font-size: 14px; cursor: pointer; text-decoration: none; }
        .toolbar form { display: inline-flex; gap: 6px; align-items: center; }
        .toolbar input { width: 56px; padding: 5px; border-radius: 4px; border: 0; }
        .sheet { width: 195.6mm; margin: 16px auto; display: grid; grid-template-columns: repeat(3, 63.5mm); grid-auto-rows: 33.9mm; column-gap: 2.5mm; background: #fff; padding: 0; }
        .label { padding: 2mm 3mm; overflow: hidden; display: flex; flex-direction: column; justify-content: space-between; border: 1px dashed #d1d5db; }
        .label .school { font-size: 6.5pt; text-transform: uppercase; color: #555; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .label .title { font-size: 8pt; font-weight: bold; line-height: 1.15; max-height: 2.3em; overflow: hidden; }
        .label .meta { font-size: 7pt; display: flex; justify-content: space-between; gap: 4px; }
        .label .barcode { text-align: center; line-height: 0; }
        .label .barcode svg { max-width: 100%; height: 9mm; }
        .label .code { text-align: center; font-family: "Courier New", monospace; font-size: 8pt; letter-spacing: 1px; }
        .empty { color: #555; text-align: center; padding: 40px; font-size: 15px; }
        @media print {
            body { background: #fff; }
            .toolbar { display: none; }
            .sheet { margin: 0; page-break-after: always; }
            .label { border: 0; }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <strong>{{ count($books) }} label(s)</strong>
        <button onclick="window.print()">Print</button>
        <form method="GET">
            @foreach (array_diff_key(\App\Support\RawPaginator::urlQuery(), ['skip' => 1]) as $k => $v)@if (!is_array($v))<input type="hidden" name="{{ $k }}" value="{{ $v }}">@endif @endforeach
            <label>Skip first <input type="number" name="skip" min="0" max="23" value="{{ $skip }}"> labels (part-used sheet)</label>
            <button type="submit">Apply</button>
        </form>
        <a href="/Acquisitions">Back to Acquisitions</a>
    </div>

    @if (count($books) === 0)
        <p class="empty">No books selected. Tick books in the Acquisitions list and choose "Print labels".</p>
    @else
        @foreach (array_chunk(array_merge(array_fill(0, $skip, null), $books), 24) as $page)
            <div class="sheet">
                @foreach ($page as $book)
                    @if ($book === null)
                        <div class="label" style="border-color:transparent"></div>
                    @else
                        <div class="label">
                            <div class="school">{{ $school_name }}</div>
                            <div class="title">{{ $book->book_title }}</div>
                            <div class="barcode">{!! $book->barcode_svg !!}</div>
                            <div class="code">{{ $book->code }}</div>
                            <div class="meta"><span>Call No: {{ $book->call_no ?: '-' }}</span><span>{{ $book->isbn_13 }}</span></div>
                        </div>
                    @endif
                @endforeach
            </div>
        @endforeach
    @endif
</body>
</html>
