{{-- 429 error page. Layout and styling: errors/layout.blade.php --}}
@extends('errors.layout')

@section('code', '429')
@section('code_left', '4')
@section('code_right', '9')
@section('icon', 'bx-stopwatch bx-tada')
@section('title', "Too many attempts")

@section('message')
    There were too many requests in a short time, so we've paused them for a moment to keep accounts safe.
@endsection

@section('tips')
    <li><i class="bx bx-check-circle"></i><span>Wait about a minute, then try again.</span></li>
    <li><i class="bx bx-check-circle"></i><span>If you were signing in, double-check your username and password before the next try.</span></li>
@endsection
