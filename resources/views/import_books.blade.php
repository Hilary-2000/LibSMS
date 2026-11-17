@extends('layouts.app', ['active' => 'Acquisitions'])
@section('title', 'Import books')
@section('page_title', 'Import books')

@section('content')
<div class="row">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Upload a CSV or Excel file</h5>
                @if ($errors->any())
                    <div class="alert alert-danger">@foreach ($errors->all() as $message)<div>{{ $message }}</div>@endforeach</div>
                @endif
                @if (session('import_ids'))
                    <div class="alert alert-info">
                        <a href="/Acquisitions/Labels?ids={{ session('import_ids') }}" target="_blank" class="alert-link"><i class="bx bx-barcode"></i> Print barcode labels for the imported books</a>
                    </div>
                @endif
                <form method="POST" action="/Acquisitions/Import/Preview" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="import_file" class="form-label">File (.csv, .xlsx or .xls, up to {{ \App\Http\Controllers\ImportBooks::MAX_ROWS }} rows)</label>
                        <input type="file" class="form-control" name="import_file" id="import_file" accept=".csv,.xlsx,.xls" required>
                    </div>
                    <div class="mb-3">
                        <label for="library_location" class="form-label">Add the books to library</label>
                        <select class="form-control" name="library_location" id="library_location" required>
                            @foreach ($libraries as $library)
                                <option value="{{ $library->id }}">{{ $library->Name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="use_google" value="1" id="use_google" checked>
                        <label class="form-check-label" for="use_google">Fill in missing title, author, publisher and other details from Google Books using the ISBN</label>
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="bx bx-show"></i> Check the file</button>
                    <span class="text-muted small ms-2">Nothing is saved until you confirm on the next screen.</span>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">File format</h5>
                <p>The first row must be the column headings. Recognised headings:</p>
                <ul class="mb-3">
                    <li><b>title</b> &mdash; required (or give an ISBN and let Google Books fill it in)</li>
                    <li><b>call_no</b> &mdash; required, must be unique</li>
                    <li><b>copies</b> &mdash; number of copies (default 1). With 3 copies of ENG-001 the call numbers become ENG-001-1, ENG-001-2, ENG-001-3.</li>
                    <li>author, isbn_13, isbn_10, publisher, published, category, shelf, pages, language, edition, description, keywords</li>
                </ul>
                <a href="/Acquisitions/Import/Template" class="btn btn-outline-primary btn-sm"><i class="bx bx-download"></i> Download a template</a>
                <a href="/Acquisitions" class="btn btn-light btn-sm">Back to Acquisitions</a>
            </div>
        </div>
    </div>
</div>
@endsection
