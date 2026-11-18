{{-- Fallback for any 4xx status without its own page. Layout: errors/layout.blade.php --}}
@extends('errors.layout')

@section('code', isset($exception) && method_exists($exception, 'getStatusCode') ? (string) $exception->getStatusCode() : '400')
@section('code_left', '4')
@section('code_right', '0')
@section('icon', 'bx-error-circle bx-tada')
@section('title', "This request couldn't be completed")

@section('message')
    Something about the request wasn't right, so the library couldn't open this page.
@endsection

@section('tips')
    <li><i class="bx bx-check-circle"></i><span>Go back and try again.</span></li>
    <li><i class="bx bx-check-circle"></i><span>Open the page from the menu instead of a saved link.</span></li>
@endsection
