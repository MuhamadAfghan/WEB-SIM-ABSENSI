@extends('components.template')

@section('title', 'Manajemen Akun - Hadir.in')

@section('content')
<div class="mx-auto w-full max-w-7xl">
    <!-- Header + Actions -->
    <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <h2 class="text-3xl md:text-4xl font-bold text-gray-800 ml-2 md:ml-4">Manajemen Akun</h2>

        <div class="flex w-full flex-col gap-3 sm:flex-row md:w-auto">
            <!-- Search -->
            <div class="relative w-full md:w-96 lg:w-[400px]">
                <i class="fas fa-search pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" placeholder="Cari" class="w-full rounded-xl border-0 bg-white py-3 pl-11 pr-4 text-gray-700 shadow-sm ring-1 ring-transparent focus:ring-2 focus:ring-blue-400" />
            </div>

            <!-- Role Filter -->
            <div class="relative min-w-[140px]">
                <select class="w-full appearance-none rounded-xl border-0 bg-white py-3 pl-4 pr-10 text-gray-700 shadow-sm ring-1 ring-transparent focus:ring-2 focus:ring-blue-400">
                    <option selected>Guru</option>
                    <option>Staff</option>
                    <option>Admin</option>
                </select>
                <i class="fas fa-chevron-down pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            </div>

            <!-- Division Filter -->
            <div class="relative min-w-[140px]">
                <select class="w-full appearance-none rounded-xl border-0 bg-white py-3 pl-4 pr-10 text-gray-700 shadow-sm ring-1 ring-transparent focus:ring-2 focus:ring-blue-400">
                    <option selected>Divisi</option>
                    <option>Akademik</option>
                    <option>Keuangan</option>
                    <option>Operasional</option>
                </select>
                <i class="fas fa-chevron-down pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            </div>
        </div>
    </div>

    <!-- Cards Grid -->
    <div class="mt-15 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3">
        @for ($i = 0; $i < 9; $i++)
            <div class="rounded-2xl bg-white p-5 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 text-blue-500">
                        <i class="fas fa-user"></i>
                    </div>
                    <p class="text-base font-semibold text-gray-800">Siti Nurhaliza</p>
                </div>

                <div class="mt-4 space-y-1 text-sm">
                    <div class="flex items-center justify-between text-black-600">
                        <span>Divisi :</span>
                        <span class="font-medium text-black-800">Guru</span>
                    </div>
                    <div class="flex items-center justify-between text-black-600">
                        <span>NIP    :</span>
                        <span class="font-medium text-black-800">123456789101112131415161718</span>
                    </div>
                </div>

                <button type="button" class="mt-4 w-full rounded-xl bg-[#60B5FF] px-4 py-2.5 font-medium text-white transition-colors hover:bg-blue-400">
                    <img src="{{ asset('img-icon-acount-management/edit_white.png') }}" alt="Edit" class=" inline h-5 w-5 mr-1">
                    Profil
                </button>
            </div>
        @endfor
    </div>
</div>
@endsection
