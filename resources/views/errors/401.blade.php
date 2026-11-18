{{-- 401 error page. Layout and styling: errors/layout.blade.php --}}
@extends('errors.layout')

@section('code', '401')
@section('code_left', '4')
@section('code_right', '1')
@section('icon', 'bx-lock-alt bx-tada')
@section('title', "Please sign in")

@section('message')
    You need to be signed in to the library to open this page. Your session may have ended after a period of inactivity.
@endsection

@section('tips')
    <li><i class="bx bx-check-circle"></i><span>Sign in again with your library username and password.</span></li>
    <li><i class="bx bx-check-circle"></i><span>If you were in the middle of something, you may need to repeat the last step.</span></li>
@endsection

@section('actions')
    <a class="btn btn-primary waves-effect waves-light" href="/"><i class="bx bx-log-in me-1"></i> Go to Login</a>
@endsection
