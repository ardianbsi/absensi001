@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    @php
        $role = auth()->user()->getRoleNames()->first() ?? 'employee';
    @endphp

    @if(in_array($role, ['admin', 'super_admin']))
        @include('dashboard.admin')
    @elseif($role === 'manager')
        @include('dashboard.manager')
    @else
        @include('dashboard.employee')
    @endif
@endsection
