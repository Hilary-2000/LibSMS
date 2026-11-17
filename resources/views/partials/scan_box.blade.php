{{--
    Front-desk barcode scanner box. A USB/Bluetooth scanner "types" the label code and presses Enter,
    which submits the form: a copy on the shelf opens check-out, a copy on loan opens its check-in.
    Usage: @include('partials.scan_box', ['back' => '/Circulation'])
--}}
<form method="GET" action="/Circulation/scan" class="my-3" autocomplete="off">
    <input type="hidden" name="back" value="{{ $back ?? '/Circulation' }}">
    <label for="scan_code" class="form-label mb-1"><i class="bx bx-barcode-reader"></i> Scan a book label to check it out or in</label>
    <div class="input-group" style="max-width: 520px">
        <input type="text" name="code" id="scan_code" class="form-control" placeholder="Scan the barcode, or type LB000012 / call number / ISBN" autofocus required>
        <button class="btn btn-primary" type="submit"><i class="bx bx-search"></i> Go</button>
    </div>
</form>
