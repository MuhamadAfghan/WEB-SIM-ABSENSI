@extends('components.template')

@section('title', 'Dashboard - Hadir.in')

@section('content')
@include('components.dashboard.card-statistics')

<div class="grid grid-cols-2 gap-4 mt-4">
    <div class="h-[420px]">
        @include('components.dashboard.card-tahunan')
    </div>
    <div class="h-[420px]">
        @include('components.dashboard.card-bulanan')
    </div>
</div>

@endsection
