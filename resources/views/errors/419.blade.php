{{-- 419 error page. Layout and styling: errors/layout.blade.php --}}
@extends('errors.layout')

@section('code', '419')
@section('code_left', '4')
@section('code_right', '9')
@section('icon', 'bx-time-five bx-spin')
@section('title', "This page has expired")

@section('message')
    The form was open for too long, so it was not saved for your security. Nothing was changed.
@endsection

@section('tips')
    <li><i class="bx bx-check-circle"></i><span>Go back, refresh the page, and submit the form again.</span></li>
    <li><i class="bx bx-check-circle"></i><span>If it keeps happening, sign out and sign in again.</span></li>
@endsection

@section('actions')
    <button type="button" class="btn btn-primary waves-effect waves-light" onclick="history.length > 1 ? history.back() : location.assign('/')"><i class="bx bx-revision me-1"></i> Go back and try again</button>
@endsection
