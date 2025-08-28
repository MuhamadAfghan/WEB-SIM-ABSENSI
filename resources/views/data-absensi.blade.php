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

        .status-sakit {
            color: #F87171;
        }

        .status-hadir {
            color: #22C55E;
        }

        .status-izin {
            color: #F59E42;
        }

        .status-pending {
            color: #6B7280;
        }

        .loading-spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3B82F6;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .error-message {
            background-color: #FEF2F2;
            border: 1px solid #FECACA;
            color: #DC2626;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
    </style>

    <!-- Pastikan Alpine.js terpasang -->
    <script src="//unpkg.com/alpinejs" defer></script>

    <div class="mb-4 flex items-center justify-between" x-data="{ openFilter: false }">
        <h1 class="text-3xl font-black tracking-wide">Data Absensi</h1>
        <div class="relative flex gap-2">
            <!-- Filter -->
            <div class="relative">
                <button @click="openFilter = !openFilter" class="rounded border bg-white px-4 py-2">
                    <img src="{{ asset('image/filter_black.png') }}" alt="Icon" class="inline h-5 w-5 text-gray-500">
                </button>
                <!-- Dropdown Filter -->
                <div x-show="openFilter" @click.away="openFilter = false"
                    class="absolute z-10 mt-2 w-48 rounded border bg-white text-sm shadow-lg">
                    <button class="block w-full px-4 py-2 text-left hover:bg-blue-50"
                        @click="setFilter('7days'); openFilter = false">
                        7 hari yang lalu
                    </button>
                    <button class="block w-full px-4 py-2 text-left hover:bg-blue-50"
                        @click="setFilter('30days'); openFilter = false">
                        30 hari yang lalu
                    </button>
                    <button class="block w-full px-4 py-2 text-left hover:bg-blue-50"
                        @click="setFilter('all'); openFilter = false">
                        Semua Data
                    </button>
                </div>
            </div>

            <!-- Search -->
            <div class="relative">
                <input type="text" id="searchInput" placeholder="Cari nama..."
                    class="w-56 rounded border bg-white px-3 py-2 pl-10 text-sm">
                <svg xmlns="http://www.w3.org/2000/svg"
                    class="absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 transform text-gray-500" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>

            <!-- Export -->
            <button class="flex items-center gap-2 rounded border bg-white px-4 py-2 text-sm" onclick="exportData()">
                <img src="{{ asset('image/upload_file_black.png') }}" alt="Icon" class="inline h-5 w-5 text-gray-500">
                Export berdasarkan waktu
            </button>

            <!-- Cari berdasarkan waktu -->
            <div class="relative">
                <svg xmlns="http://www.w3.org/2000/svg"
                    class="absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 transform text-gray-500" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 4h10M5 11h14m-9 4h4m-7 4h10a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                <input type="date" id="dateFilter" onchange="filterByDate(this.value)"
                    class="w-56 rounded border bg-white px-3 py-2 pl-10 text-sm" />
            </div>
        </div>
    </div>

    {{-- Container untuk status loading/error --}}
    <div id="statusContainer"></div>

    {{-- Tabel Data --}}
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm" id="attendanceTable">
            <thead class="bg-white">
                <tr>
                    <th class="rounded-tl-lg border-b border-gray-200 px-4 py-3 font-bold">NO</th>
                    <th class="border-b border-gray-200 px-4 py-3 font-bold">Nama</th>
                    <th class="border-b border-gray-200 px-4 py-3 font-bold">Keterangan</th>
                    <th class="border-b border-gray-200 px-4 py-3 font-bold">Waktu Kedatangan</th>
                    <th class="border-b border-gray-200 px-4 py-3 font-bold">Waktu Kepulangan</th>
                    <th class="border-b border-gray-200 px-4 py-3 font-bold">Metode Absen</th>
                </tr>
            </thead>
            <tbody id="attendanceBody" class="divide-y border-gray-200 bg-gray-100">
                <!-- Data akan diisi oleh JavaScript -->
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="flex items-center justify-end gap-2 p-4" id="paginationContainer">
        <!-- Pagination akan diisi oleh JavaScript -->
    </div>

    <script>
        // Variabel global untuk menyimpan data
        let allAttendanceData = [];
        let currentPage = 1;
        const itemsPerPage = 10;
        let currentFilter = 'all';
        let currentSearch = '';
        let currentDate = '';

        // Fungsi untuk memuat data absensi
        async function fetchAttendanceData() {
            showLoading();
            try {
                const response = await fetch("/api/absences");
                const contentType = response.headers.get("content-type");
                if (!response.ok) {
                    throw new Error('Gagal memuat data absensi: ' + response.status);
                }
                if (!contentType || !contentType.includes("application/json")) {
                    const text = await response.text();
                    if (text.includes('<!DOCTYPE html')) {
                        showError('Sesi login Anda mungkin sudah habis. Silakan login ulang.');
                        return;
                    }
                    throw new Error('Respons server tidak valid.');
                }
                const result = await response.json();
                if (result.status === 'success') {
                    // Ubah object ke array
                    allAttendanceData = result.data.items ? Object.values(result.data.items) : [];
                    renderAttendanceData();
                } else {
                    showError('Gagal memuat data absensi: ' + result.message);
                }
            } catch (error) {
                showError('Terjadi kesalahan: ' + error.message);
            }
        }

        // Fungsi untuk menampilkan data
        function renderAttendanceData() {
            const tableBody = document.getElementById('attendanceBody');
            const paginationContainer = document.getElementById('paginationContainer');
            let filteredData = allAttendanceData;

            // Filter pencarian nama
            if (currentSearch) {
                const searchTerm = currentSearch.toLowerCase();
                filteredData = filteredData.filter(item =>
                    (item.user && item.user.name && item.user.name.toLowerCase().includes(searchTerm))
                );
            }

            // Filter tanggal (berdasarkan item.date)
            if (currentDate) {
                filteredData = filteredData.filter(item =>
                    item.date && item.date.startsWith(currentDate)
                );
            }

            // Filter periode waktu
            if (currentFilter !== 'all') {
                const today = new Date();
                let filterDate = new Date();
                if (currentFilter === '7days') {
                    filterDate.setDate(today.getDate() - 7);
                } else if (currentFilter === '30days') {
                    filterDate.setDate(today.getDate() - 30);
                }
                filteredData = filteredData.filter(item => {
                    const itemDate = new Date(item.date);
                    return itemDate >= filterDate;
                });
            }

            // Pagination
            const totalPages = Math.ceil(filteredData.length / itemsPerPage);
            if (currentPage > totalPages) currentPage = totalPages;
            if (currentPage < 1) currentPage = 1;
            const startIndex = (currentPage - 1) * itemsPerPage;
            const endIndex = startIndex + itemsPerPage;
            const currentData = filteredData.slice(startIndex, endIndex);

            tableBody.innerHTML = '';
            if (currentData.length === 0) {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center py-4">Tidak ada data absensi</td>
                    </tr>
                `;
            } else {
                currentData.forEach((item, index) => {
                    const rowNumber = startIndex + index + 1;
                    const userName = item.user && item.user.name ? item.user.name : '-';
                    const userId = item.user && item.user.id ? item.user.id : '-';
                    const tipe = item.absence_status || '-';
                    const type = item.type || '-';
                    const arrivalTime = item.check_in_time || '--:--';
                    const departureTime = item.check_out_time || '--:--';
                    const lokasi = item.lokasi || '-';

                    tableBody.innerHTML += `
                        <tr>
                            <td class="px-4 py-1.5">${rowNumber}</td>
                            <td class="px-4 py-1.5">
                                ${userName}
                            </td>
                            <td class="px-4 py-1.5">${tipe}</td>
                            <td class="px-4 py-1.5">${arrivalTime}</td>
                            <td class="px-4 py-1.5">${departureTime}</td>
                            <td class="px-4 py-1.5">${tipe == 'hadir' ? tipe : type}</td>
                        </tr>
                    `;
                });
            }

            renderPagination(totalPages);
            document.getElementById('statusContainer').innerHTML = '';
        }

        // Fungsi untuk render pagination
        function renderPagination(totalPages) {
            const paginationContainer = document.getElementById('paginationContainer');

            if (totalPages <= 1) {
                paginationContainer.innerHTML = '';
                return;
            }

            let paginationHTML = `
                <button
                    onclick="changePage(${currentPage - 1})"
                    ${currentPage === 1 ? 'disabled' : ''}
                    class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-500 hover:bg-blue-100 hover:text-gray-700 ${currentPage === 1 ? 'opacity-50 cursor-not-allowed' : ''}"
                >
                    &lt;
                </button>
            `;

            for (let i = 1; i <= totalPages; i++) {
                paginationHTML += `
                    <button
                        onclick="changePage(${i})"
                        class="flex items-center justify-center px-3 h-8 ms-0 leading-tight ${currentPage === i ? 'bg-blue-500 text-white' : 'text-gray-500 hover:bg-blue-100 hover:text-gray-700'}"
                    >
                        ${i}
                    </button>
                `;
            }

            paginationHTML += `
                <button
                    onclick="changePage(${currentPage + 1})"
                    ${currentPage === totalPages ? 'disabled' : ''}
                    class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-500 hover:bg-blue-100 hover:text-gray-700 ${currentPage === totalPages ? 'opacity-50 cursor-not-allowed' : ''}"
                >
                    &gt;
                </button>
            `;

            paginationContainer.innerHTML = paginationHTML;
        }

        // Fungsi untuk mengganti halaman
        function changePage(page) {
            currentPage = page;
            renderAttendanceData();
        }

        // Fungsi untuk menyetel filter
        function setFilter(filterType) {
            currentFilter = filterType;
            currentPage = 1;
            renderAttendanceData();
        }

        // Fungsi untuk filter berdasarkan tanggal
        function filterByDate(date) {
            currentDate = date;
            currentPage = 1;
            renderAttendanceData();
        }

        // Fungsi untuk export data
        function exportData() {
            let exportData = allAttendanceData;
            if (currentSearch) {
                const searchTerm = currentSearch.toLowerCase();
                exportData = exportData.filter(item =>
                    (item.user && item.user.name && item.user.name.toLowerCase().includes(searchTerm))
                );
            }
            if (currentDate) {
                exportData = exportData.filter(item =>
                    item.date && item.date.startsWith(currentDate)
                );
            }
            if (currentFilter !== 'all') {
                const today = new Date();
                let filterDate = new Date();
                if (currentFilter === '7days') {
                    filterDate.setDate(today.getDate() - 7);
                } else if (currentFilter === '30days') {
                    filterDate.setDate(today.getDate() - 30);
                }
                exportData = exportData.filter(item => {
                    const itemDate = new Date(item.date);
                    return itemDate >= filterDate;
                });
            }
            const csvContent = convertToCSV(exportData);
            const blob = new Blob([csvContent], {
                type: 'text/csv;charset=utf-8;'
            });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.setAttribute('href', url);
            link.setAttribute('download', `data_absensi_${new Date().toISOString().split('T')[0]}.csv`);
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        // Fungsi untuk konversi data ke CSV
        function convertToCSV(data) {
            const headers = ['Nama', 'User ID', 'Tipe', 'Tanggal', 'Check In', 'Check Out', 'Keterangan', 'Lokasi'];
            const rows = data.map(item => [
                `"${item.user && item.user.name ? item.user.name : ''}"`,
                item.user && item.user.id ? item.user.id : '',
                item.type || '',
                item.date || '',
                item.check_in_time || '',
                item.check_out_time || '',
                `"${item.keterangan || ''}"`,
                `"${item.lokasi || ''}"`
            ]);
            return [headers, ...rows].map(e => e.join(',')).join('\n');
        }

        // Fungsi untuk menampilkan loading
        function showLoading() {
            document.getElementById('statusContainer').innerHTML = `
                <div class="loading-spinner"></div>
                <p class="text-center text-gray-500">Memuat data absensi...</p>
            `;
            document.getElementById('attendanceBody').innerHTML = '';
            document.getElementById('paginationContainer').innerHTML = '';
        }

        // Fungsi untuk menampilkan error
        function showError(message) {
            document.getElementById('statusContainer').innerHTML = `
                <div class="error-message">
                    <strong>Error:</strong> ${message}
                </div>
            `;
            document.getElementById('attendanceBody').innerHTML = '';
            document.getElementById('paginationContainer').innerHTML = '';
        }

        // Inisialisasi ketika halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            fetchAttendanceData();

            // Setup pencarian real-time
            const searchInput = document.getElementById('searchInput');
            let searchTimeout;

            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    currentSearch = this.value;
                    currentPage = 1;
                    renderAttendanceData();
                }, 500);
            });
        });
    </script>
@endsection
