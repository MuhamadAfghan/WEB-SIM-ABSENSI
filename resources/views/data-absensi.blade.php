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
        .status-pending { color: #6B7280; }
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
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .error-message {
            background-color: #FEF2F2;
            border: 1px solid #FECACA;
            color: #DC2626;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        .date-range-container {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .date-range-input {
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 8px;
            width: 150px;
        }
        .apply-filter-btn {
            background-color: #3B82F6;
            color: white;
            border: none;
            border-radius: 6px;
            padding: 8px 12px;
            cursor: pointer;
        }
        .apply-filter-btn:hover {
            background-color: #2563EB;
        }
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
                    <button class="block w-full text-left px-4 py-2 hover:bg-blue-50" @click="setFilter('7days'); openFilter = false">
                        7 hari yang lalu
                    </button>
                    <button class="block w-full text-left px-4 py-2 hover:bg-blue-50" @click="setFilter('30days'); openFilter = false">
                        30 hari yang lalu
                    </button>
                    <button class="block w-full text-left px-4 py-2 hover:bg-blue-50" @click="setFilter('all'); openFilter = false">
                        Semua Data
                    </button>
                    <hr class="my-1">
                    <button class="block w-full text-left px-4 py-2 hover:bg-blue-50" @click="openCustomDateRange(); openFilter = false">
                        Rentang Tanggal Kustom
                    </button>
                </div>
            </div>

            <!-- Search -->
            <div class="relative">
                <input 
                    type="text" 
                    id="searchInput"
                    placeholder="Cari nama..." 
                    class="rounded px-3 py-2 text-sm border pl-10 w-56 bg-white"
                >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>

            <!-- Export -->
            <button class="bg-white border px-4 py-2 rounded flex items-center gap-2 text-sm" onclick="exportData()">
                <img src="{{ asset('image/upload_file_black.png') }}" alt="Icon" class="w-5 h-5 inline text-gray-500">
                Export berdasarkan waktu
            </button>

            <!-- Cari berdasarkan waktu -->
            <div class="relative">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 4h10M5 11h14m-9 4h4m-7 4h10a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                <input
                    type="date"
                    id="dateFilter"
                    onchange="filterByDate(this.value)"
                    class="rounded px-3 py-2 text-sm border pl-10 w-56 bg-white"
                />
            </div>
        </div>
    </div>

    <!-- Modal untuk rentang tanggal kustom -->
    <div id="customDateRangeModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white p-6 rounded-lg shadow-xl w-96">
            <h3 class="text-lg font-bold mb-4">Filter Rentang Tanggal</h3>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Tanggal Mulai</label>
                <input type="date" id="dateStart" class="date-range-input w-full">
            </div>
            <div class="mb-6">
                <label class="block text-sm font-medium mb-1">Tanggal Akhir</label>
                <input type="date" id="dateEnd" class="date-range-input w-full">
            </div>
            <div class="flex justify-end gap-2">
                <button onclick="closeCustomDateRange()" class="px-4 py-2 border rounded">Batal</button>
                <button onclick="applyCustomDateRange()" class="apply-filter-btn px-4 py-2">Terapkan</button>
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
                    <th class="px-4 py-3 font-bold rounded-tl-lg border-b border-gray-200">NO</th>
                    <th class="px-4 py-3 font-bold border-b border-gray-200">Nama</th>
                    <th class="px-4 py-3 font-bold border-b border-gray-200">Keterangan</th>
                    <th class="px-4 py-3 font-bold border-b border-gray-200">Waktu Kedatangan</th>
                    <th class="px-4 py-3 font-bold border-b border-gray-200">Waktu Kepulangan</th>
                    <th class="px-4 py-3 font-bold border-b border-gray-200">Metode Absen</th>
                </tr>
            </thead>
            <tbody id="attendanceBody" class="divide-y border-gray-200 bg-gray-100">
                <!-- Data akan diisi oleh JavaScript -->
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="flex justify-end items-center p-4 gap-2" id="paginationContainer">
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
        let dateStart = '';
        let dateEnd = '';
        let currentType = '';

        // Fungsi untuk memuat data absensi
        async function fetchAttendanceData() {
            showLoading();
            try {
                // Build URL dengan parameter
                let url = "/api/absences";
                const params = [];
                
                if (dateStart && dateEnd) {
                    params.push(`date_start=${dateStart}`);
                    params.push(`date_end=${dateEnd}`);
                }
                
                if (currentType) {
                    params.push(`type=${currentType}`);
                }
                
                if (params.length > 0) {
                    url += '?' + params.join('&');
                }
                
                const response = await fetch(url);
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
                            <td class="px-4 py-1.5">${type}</td>
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
            
            // Set tanggal mulai dan akhir berdasarkan filter
            const today = new Date();
            dateStart = '';
            dateEnd = '';
            
            if (filterType === '7days') {
                const sevenDaysAgo = new Date();
                sevenDaysAgo.setDate(today.getDate() - 7);
                dateStart = formatDate(sevenDaysAgo);
                dateEnd = formatDate(today);
            } else if (filterType === '30days') {
                const thirtyDaysAgo = new Date();
                thirtyDaysAgo.setDate(today.getDate() - 30);
                dateStart = formatDate(thirtyDaysAgo);
                dateEnd = formatDate(today);
            }
            
            fetchAttendanceData();
        }

        // Fungsi untuk filter berdasarkan tanggal
        function filterByDate(date) {
            currentDate = date;
            currentPage = 1;
            renderAttendanceData();
        }

        // Fungsi untuk membuka modal rentang tanggal kustom
        function openCustomDateRange() {
            document.getElementById('customDateRangeModal').classList.remove('hidden');
        }

        // Fungsi untuk menutup modal rentang tanggal kustom
        function closeCustomDateRange() {
            document.getElementById('customDateRangeModal').classList.add('hidden');
        }

        // Fungsi untuk menerapkan rentang tanggal kustom
        function applyCustomDateRange() {
            dateStart = document.getElementById('dateStart').value;
            dateEnd = document.getElementById('dateEnd').value;
            
            if (!dateStart || !dateEnd) {
                alert('Harap isi kedua tanggal');
                return;
            }
            
            if (dateStart > dateEnd) {
                alert('Tanggal mulai tidak boleh lebih besar dari tanggal akhir');
                return;
            }
            
            currentFilter = 'custom';
            currentPage = 1;
            closeCustomDateRange();
            fetchAttendanceData();
        }

        // Fungsi untuk mengekspor data ke CSV
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
            
            const csvContent = convertToCSV(exportData);
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
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
            const headers = ['Nama', 'User ID', 'Metode Absen', 'Tanggal', 'Check In', 'Check Out', 'Keterangan', 'Lokasi'];
            const rows = data.map(item => [
                `"${item.user && item.user.name ? item.user.name : ''}"`,
                item.user && item.user.id ? item.user.id : '',
                item.type || '',
                item.date || '',
                item.check_in_time || '',
                item.check_out_time || '',
                `"${item.absence_status || ''}"`,
                `"${item.lokasi || ''}"`
            ]);
            return [headers, ...rows].map(e => e.join(',')).join('\n');
        }

        // Fungsi untuk memformat tanggal ke YYYY-MM-DD
        function formatDate(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
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