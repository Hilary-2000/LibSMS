{{-- 404 error page. Layout and styling: errors/layout.blade.php --}}
@extends('errors.layout')

@section('code', '404')
@section('code_left', '4')
@section('code_right', '4')
@section('icon', 'bx-buoy bx-spin')
@section('title', "Sorry, page not found")

@section('message')
    The page you are looking for doesn't exist or has been moved. The link may be old, or the address may have a typo.
@endsection

@section('tips')
    <li><i class="bx bx-check-circle"></i><span>Check the address for spelling mistakes.</span></li>
    <li><i class="bx bx-check-circle"></i><span>Open the page from the menu instead of a saved link.</span></li>
    <li><i class="bx bx-check-circle"></i><span>If a book or borrower was deleted, its page is no longer available.</span></li>
@endsection
