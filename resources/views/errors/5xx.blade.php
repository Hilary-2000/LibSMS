{{-- Fallback for any 5xx status without its own page. Layout: errors/layout.blade.php --}}
@extends('errors.layout')

@section('code', isset($exception) && method_exists($exception, 'getStatusCode') ? (string) $exception->getStatusCode() : '500')
@section('code_left', '5')
@section('code_right', '0')
@section('icon', 'bx-buoy bx-spin')
@section('title', "Something went wrong")

@section('message')
    The library ran into a problem and couldn't finish your request. It's not something you did.
@endsection

@section('tips')
    <li><i class="bx bx-check-circle"></i><span>Refresh the page or try again in a few minutes.</span></li>
    <li><i class="bx bx-check-circle"></i><span>If it keeps happening, tell your administrator what you were doing.</span></li>
@endsection
