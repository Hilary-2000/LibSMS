{{-- 503 error page. Layout and styling: errors/layout.blade.php --}}
@extends('errors.layout')

@section('code', '503')
@section('code_left', '5')
@section('code_right', '3')
@section('icon', 'bx-cog bx-spin')
@section('title', "The library is being updated")

@section('message')
    We're carrying out maintenance to improve the library system. It will be back shortly, and no data has been lost.
@endsection

@section('tips')
    <li><i class="bx bx-check-circle"></i><span>Try again in a few minutes.</span></li>
    <li><i class="bx bx-check-circle"></i><span>Books you lent or returned before the update are safely saved.</span></li>
@endsection

@section('actions')
    <button type="button" class="btn btn-primary waves-effect waves-light" onclick="location.reload()"><i class="bx bx-revision me-1"></i> Try again</button>
@endsection

@section('image', '/assets/images/maintenance.svg')
