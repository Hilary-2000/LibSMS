{{-- 403 error page. Layout and styling: errors/layout.blade.php --}}
@extends('errors.layout')

@section('code', '403')
@section('code_left', '4')
@section('code_right', '3')
@section('icon', 'bx-shield-quarter bx-tada')
@section('title', "You don't have access to this page")

@section('message')
    Your account is not allowed to open this section of the library. Access to each menu section is set by your administrator.
@endsection

@section('tips')
    <li><i class="bx bx-check-circle"></i><span>Ask your administrator to give you the privilege under <b>Settings &rarr; User Management</b>.</span></li>
    <li><i class="bx bx-check-circle"></i><span>Use the menu on the Dashboard to see the sections you can open.</span></li>
@endsection
