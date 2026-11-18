{{-- 500 error page. Layout and styling: errors/layout.blade.php --}}
@extends('errors.layout')

@section('code', '500')
@section('code_left', '5')
@section('code_right', '0')
@section('icon', 'bx-buoy bx-spin')
@section('title', "Something went wrong")

@section('message')
    The library ran into a problem and couldn't finish your request. It's not something you did, and the error has been recorded.
@endsection

@section('tips')
    <li><i class="bx bx-check-circle"></i><span>Refresh the page or try again in a few minutes.</span></li>
    <li><i class="bx bx-check-circle"></i><span>If it keeps happening, tell your administrator what you were doing and the time shown below.</span></li>
@endsection

@section('reference')
    Time of error: {{ date('d M Y, H:i') }} &middot;
@endsection
