@extends('components.template')

@section('title', 'Dashboard - Hadir.in')

@section('content')
    @include('components.dashboard.card-statistics')

    <div class="mt-4 grid grid-cols-2 gap-4">
        <div class="h-[420px]">
            @include('components.dashboard.card-tahunan')
        </div>
        <div class="h-[420px]">
            @include('components.dashboard.card-bulanan')
        </div>
    </div>

<<<<<<<<< Temporary merge branch 1
<div class="mt-4">
    @include('components.dashboard.table-recent-absences')
</div>
=========
>>>>>>>>> Temporary merge branch 2
@endsection
