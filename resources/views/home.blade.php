@extends('layouts.app')

@section('breadcrumbs')
<li class="breadcrumb-item">
<!-- if breadcrumb is single--><a href="#">Inicio</a>
</li>

<li class="breadcrumb-item active"><span>Dashboard</span></li>
@endsection

@section('content')
    @include('new-dashboard')
@endsection

@push('after-scripts')
    @vite('resources/js/dashboard.js')
@endpush
