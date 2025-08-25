@extends('components.template')

@section('title', 'Data Absensi - Hadir.in')

@section('content')
    <style>
        table th:nth-child(1),
        table td:nth-child(1) {
            width: 50px;
            text-align: center;
        }
        table * {
            border-color: #e5e7eb;
        }
        .status-sakit { color: #F87171; }
        .status-hadir { color: #22C55E; }
        .status-izin { color: #F59E42; }
    </style>

    <!-- Pastikan Alpine.js terpasang -->
    <script src="//unpkg.com/alpinejs" defer></script>

    <div class="flex justify-between items-center mb-4" x-data="{ openFilter: false }">
        <h1 class="text-3xl font-black tracking-wide">Data Absensi</h1>
        <div class="flex gap-2 relative">
            <!-- Filter -->
            <div class="relative">
                <button @click="openFilter = !openFilter" class="bg-white border px-4 py-2 rounded">
                    <img src="{{ asset('image/filter_black.png') }}" alt="Icon" class="w-5 h-5 inline text-gray-500">
                </button>
                <!-- Dropdown Filter -->
                <div x-show="openFilter" @click.away="openFilter = false" 
                     class="absolute z-10 mt-2 w-48 bg-white border rounded shadow-lg text-sm">
                    <button class="block w-full text-left px-4 py-2 hover:bg-blue-50" @click="openFilter = false">
                        7 hari yang lalu
                    </button>
                    <button class="block w-full text-left px-4 py-2 hover:bg-blue-50" @click="openFilter = false">
                        30 hari yang lalu
                    </button>
                    <button class="block w-full text-left px-4 py-2 hover:bg-blue-50" @click="openFilter = false">
                        100 hari yang lalu
                    </button>
                </div>
            </div>

            <!-- Search -->
            <button class="bg-white border px-4 py-2 rounded">
                <img src="{{ asset('image/search_black.png') }}" class="w-5 h-5 inline text-gray-500">
            </button>

            <!-- Export -->
            <button class="bg-white border px-4 py-2 rounded flex items-center gap-2 text-sm">
                <img src="{{ asset('image/upload_file_black.png') }}" alt="Icon" class="w-5 h-5 inline text-gray-500">
                Export berdasarkan waktu
            </button>

            <!-- Cari berdasarkan waktu -->
            <div class="relative" x-data="{ isDate: false, dateValue: '' }">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 4h10M5 11h14m-9 4h4m-7 4h10a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                <input
                    :type="isDate ? 'date' : 'text'"
                    x-model="dateValue"
                    @focus="isDate = true"
                    @blur="if(!dateValue) isDate = false"
                    placeholder="Cari berdasarkan waktu"
                    class="rounded px-3 py-2 text-sm border pl-10 w-56 bg-white"
                />
            </div>
        </div>
    </div>

    {{-- Tabel Data --}}
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-white">
                <tr>
                    <th class="px-4 py-3 font-bold rounded-tl-lg border-b border-gray-200">NO</th>
                    <th class="px-4 py-3 font-bold border-b border-gray-200">Nama</th>
                    <th class="px-4 py-3 font-bold border-b border-gray-200">Keterangan</th>
                    <th class="px-4 py-3 font-bold border-b border-gray-200">Waktu Kedatangan</th>
                    <th class="px-4 py-3 font-bold rounded-tr-lg border-b border-gray-200"></th>
                </tr>
            </thead>
            <tbody class="divide-y border-gray-200 bg-gray-100">
                <tr>
                    <td class="px-4 py-1.5">1</td>
                    <td class="px-4 py-1.5">
                        Siti Nurhaliza<br>
                        <span class="text-xs text-gray-400">sitinurhaliza@smkwikrama.sch.id</span>
                    </td>
                    <td class="px-4 py-1.5 status-sakit">Sakit</td>
                    <td class="px-4 py-1.5">--:--</td>
                    <td class="px-4 py-1.5">--:--</td>
                </tr>
                <tr>
                    <td class="px-4 py-1.5">2</td>
                    <td class="px-4 py-1.5">
                        Siti Nurhaliza<br>
                        <span class="text-xs text-gray-400">sitinurhaliza@smkwikrama.sch.id</span>
                    </td>
                    <td class="px-4 py-1.5 status-sakit">Sakit</td>
                    <td class="px-4 py-1.5">--:--</td>
                    <td class="px-4 py-1.5">--:--</td>
                </tr>
                <tr>
                    <td class="px-4 py-1.5">3</td>
                    <td class="px-4 py-1.5">
                        Siti Nurhaliza<br>
                        <span class="text-xs text-gray-400">sitinurhaliza@smkwikrama.sch.id</span>
                    </td>
                    <td class="px-4 py-1.5 status-hadir">Hadir</td>
                    <td class="px-4 py-1.5">09.00</td>
                    <td class="px-4 py-1.5">15.30</td>
                </tr>
                <tr>
                    <td class="px-4 py-1.5">4</td>
                    <td class="px-4 py-1.5">
                        Siti Nurhaliza<br>
                        <span class="text-xs text-gray-400">sitinurhaliza@smkwikrama.sch.id</span>
                    </td>
                    <td class="px-4 py-1.5 status-hadir">Hadir</td>
                    <td class="px-4 py-1.5">09.00</td>
                    <td class="px-4 py-1.5">15.30</td>
                </tr>
                <tr>
                    <td class="px-4 py-1.5">5</td>
                    <td class="px-4 py-1.5">
                        Siti Nurhaliza<br>
                        <span class="text-xs text-gray-400">sitinurhaliza@smkwikrama.sch.id</span>
                    </td>
                    <td class="px-4 py-1.5 status-hadir">Hadir</td>
                    <td class="px-4 py-1.5">09.00</td>
                    <td class="px-4 py-1.5">15.30</td>
                </tr>
                <tr>
                    <td class="px-4 py-1.5">6</td>
                    <td class="px-4 py-1.5">
                        Siti Nurhaliza<br>
                        <span class="text-xs text-gray-400">sitinurhaliza@smkwikrama.sch.id</span>
                    </td>
                    <td class="px-4 py-1.5 status-izin">Izin</td>
                    <td class="px-4 py-1.5">--:--</td>
                    <td class="px-4 py-1.5">--:--</td>
                </tr>
                <tr>
                    <td class="px-4 py-1.5">7</td>
                    <td class="px-4 py-1.5">
                        Siti Nurhaliza<br>
                        <span class="text-xs text-gray-400">sitinurhaliza@smkwikrama.sch.id</span>
                    </td>
                    <td class="px-4 py-1.5 status-izin">Izin</td>
                    <td class="px-4 py-1.5">--:--</td>
                    <td class="px-4 py-1.5">--:--</td>
                </tr>
            </tbody>
        </table>
    </div>

  <!-- Pagination -->
    <div class="flex justify-end items-center p-4 gap-2">
        <button
            class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-500  hover:bg-blue-100 hover:text-gray-700 dark:bg-blue-100 dark:bg-blue-100 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">&lt;</button>
        <button
            class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-500  hover:bg-blue-100 hover:text-gray-700 dark:bg-blue-100 dark:bg-blue-100 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">1</button>
        <button
            class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-500  hover:bg-blue-100 hover:text-gray-700 dark:bg-blue-100 dark:bg-blue-100 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">2</button>
        <button
            class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-500  hover:bg-blue-100 hover:text-gray-700 dark:bg-blue-100 dark:bg-blue-100 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">3</button>
        <button
            class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-500  hover:bg-blue-100 hover:text-gray-700 dark:bg-blue-100 dark:bg-blue-100 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">&gt;</button>
    </div>
@endsection
