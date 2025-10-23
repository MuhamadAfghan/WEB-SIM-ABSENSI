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

        .status-tidak-hadir {
            color: #EF4444;
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

    <script src="//unpkg.com/alpinejs" defer></script>

    <div class="mb-4 flex items-center justify-between" x-data="{ openFilter: false }">
        <h1 class="text-3xl font-black tracking-wide">Data Absensi</h1>
        <div class="relative flex gap-2">
            <div class="relative">
                <button @click="openFilter = !openFilter" class="rounded border bg-white px-4 py-2">
                    <img src="{{ asset('image/filter_black.png') }}" alt="Icon" class="inline h-5 w-5 text-gray-500">
                </button>
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
                    <hr class="my-1">
                    <button class="block w-full text-left px-4 py-2 hover:bg-blue-50" @click="openCustomDateRange(); openFilter = false">
                        Rentang Tanggal Kustom
                    </button>
                </div>
            </div>

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

            <button class="flex items-center gap-2 rounded border bg-white px-4 py-2 text-sm" onclick="exportData()">
                <img src="{{ asset('image/upload_file_black.png') }}" alt="Icon" class="inline h-5 w-5 text-gray-500">
                Export berdasarkan waktu
            </button>

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

    <div id="statusContainer"></div>

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
            </tbody>
        </table>
    </div>

    @include('components.absensi.modal-absensi')

    <div class="flex items-center justify-center gap-2 p-4" id="paginationContainer">
    </div>

    <script>
        let attendanceData = [];
        let paginationMeta = null;
        let currentPage = 1;
        const itemsPerPage = 10;
        let currentFilter = 'all';
        let currentSearch = '';
        let currentDate = '';
        let dateStart = '';
        let dateEnd = '';
        let currentType = '';

        function getAuthToken() {
            const cookies = document.cookie.split(';');
            for (let cookie of cookies) {
                const [name, value] = cookie.trim().split('=');
                if (name === 'auth_token') {
                    return value;
                }
            }
            const localStorageToken = localStorage.getItem('auth_token');
            if (localStorageToken) {
                return localStorageToken;
            }
            const sessionStorageToken = sessionStorage.getItem('auth_token');
            if (sessionStorageToken) {
                return sessionStorageToken;
            }
            return null;
        }

        async function fetchAttendanceData(page = 1) {
            showLoading();
            try {
                const authToken = getAuthToken();
                if (!authToken) {
                    showError('Token autentikasi tidak ditemukan. Silakan login kembali.');
                    return;
                }

                let url = "/api/absences";
                const params = [];
                params.push(`page=${page}`);
                params.push(`per_page=${itemsPerPage}`);

                if (dateStart && dateEnd) {
                    params.push(`date_start=${encodeURIComponent(dateStart)}`);
                    params.push(`date_end=${encodeURIComponent(dateEnd)}`);
                }

                if (currentType) {
                    params.push(`type=${encodeURIComponent(currentType)}`);
                }

                if (currentSearch) {
                    params.push(`search=${encodeURIComponent(currentSearch)}`);
                }

                if (currentDate) {
                    params.push(`date=${encodeURIComponent(currentDate)}`);
                }

                url += '?' + params.join('&');

                const response = await fetch(url, {
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': `Bearer ${authToken}`,
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    credentials: 'include'
                });

                const contentType = response.headers.get("content-type");

                if (!response.ok) {
                    const errorData = await response.json().catch(() => ({}));
                    const errorMessage = errorData.message || `HTTP ${response.status}: Gagal memuat data absensi`;
                    throw new Error(errorMessage);
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
                    attendanceData = Array.isArray(result.data) ? result.data : (result.data.items ? Object.values(result.data.items) : []);
                    paginationMeta = result.meta || result.data?.meta || null;
                    currentPage = page;
                    renderAttendanceData();
                } else {
                    showError('Gagal memuat data absensi: ' + (result.message || 'Unknown'));
                }
            } catch (error) {
                console.error('Fetch attendance data error:', error);
                showError('Terjadi kesalahan: ' + error.message);
            }
        }

        function renderAttendanceData() {
            const tableBody = document.getElementById('attendanceBody');
            tableBody.innerHTML = '';

            if (!attendanceData || attendanceData.length === 0) {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center py-4">Tidak ada data absensi</td>
                    </tr>
                `;
            } else {
                const startNumber = (paginationMeta && typeof paginationMeta.from === 'number') ? paginationMeta.from : ((currentPage - 1) * itemsPerPage + 1);

                attendanceData.forEach((item, index) => {
                    const rowNumber = startNumber + index;
                    const userName = item.user && item.user.name ? item.user.name : '-';
                    const userId = item.user && item.user.id ? item.user.id : '-';
                    const tipe = item.absence_status || '-';
                    const type = item.type || '-';
                    const arrivalTime = item.check_in_time || '--:--';
                    const departureTime = item.check_out_time || '--:--';
                    const lokasi = item.lokasi || '-';
                    const note = item.description || item.keterangan || '';
                    const isAbsence = tipe !== 'hadir';

                    console.log('Item data:', item);
                    const openAction = isAbsence
                        ? `loadAbsenceDetailAndOpen(${item.id}, \`${userName}\`)`
                        : `AbsenceModal.open({kind:'attendance', id:${item.id}, userName:\`${userName}\`, note:\`${note}\`, imageUrl:''})`;

                    let statusClass = '';
                    if (tipe === 'hadir') {
                        statusClass = 'status-hadir';
                    } else if (tipe === 'sakit') {
                        statusClass = 'status-sakit';
                    } else if (tipe === 'izin') {
                        statusClass = 'status-izin';
                    } else if (tipe === 'tidak hadir') {
                        statusClass = 'status-tidak-hadir';
                    } else {
                        statusClass = 'status-pending';
                    }

                    tableBody.innerHTML += `
                        <tr>
                            <td class="px-4 py-1.5">${rowNumber}</td>
                            <td class="px-4 py-1.5">
                                ${userName}
                            </td>
                            <td class="px-4 py-1.5">
                                <button class="${statusClass} hover:opacity-80 font-medium" onclick="${openAction}">${tipe}</button>
                            </td>
                            <td class="px-4 py-1.5">${arrivalTime}</td>
                            <td class="px-4 py-1.5">${departureTime}</td>
                            <td class="px-4 py-1.5">${tipe == 'hadir' ? tipe : type}</td>
                        </tr>
                    `;
                });
            }

            renderPagination(paginationMeta);
            document.getElementById('statusContainer').innerHTML = '';
        }

        function renderPagination(meta) {
            const pagination = document.getElementById('paginationContainer');
            pagination.innerHTML = '';

            if (!meta || meta.last_page <= 1) {
                return;
            }

            const buttonHtml = (label, page, { disabled = false, active = false } = {}) => {
                const base = 'px-3 py-2 rounded-lg border text-sm';
                const classes = [
                    base,
                    active ? 'bg-[#60B5FF] text-white border-[#60B5FF]' :
                    'bg-white text-gray-700 border-gray-200 hover:bg-blue-50',
                    disabled ? 'opacity-50 cursor-not-allowed' : ''
                ].join(' ');
                return `<button class="${classes}" ${disabled ? 'disabled' : ''} onclick="fetchAttendanceData(${page})">${label}</button>`;
            };

            const current = meta.current_page;
            const last = meta.last_page;

            const windowSize = 5;
            const half = Math.floor(windowSize / 2);
            let start = Math.max(1, current - half);
            let end = Math.min(last, start + windowSize - 1);
            start = Math.max(1, end - windowSize + 1);

            let html = '';
            html += buttonHtml('Prev', Math.max(1, current - 1), { disabled: current === 1 });
            for (let p = start; p <= end; p++) {
                html += buttonHtml(p, p, { active: p === current });
            }
            html += buttonHtml('Next', Math.min(last, current + 1), { disabled: current === last });

            pagination.innerHTML = html;
        }

        function changePage(page) {
            fetchAttendanceData(page);
        }

        function setFilter(filterType) {
            currentFilter = filterType;
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

            fetchAttendanceData(1);
        }

        function filterByDate(date) {
            dateStart = date;
            dateEnd = date;
            currentPage = 1;
            fetchAttendanceData(1);
        }

        function openCustomDateRange() {
            document.getElementById('customDateRangeModal').classList.remove('hidden');
        }

        function closeCustomDateRange() {
            document.getElementById('customDateRangeModal').classList.add('hidden');
        }

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
            closeCustomDateRange();
            fetchAttendanceData(1);
        }

        function exportData() {
            let exportDataArr = attendanceData || [];
            const csvContent = convertToCSV(exportDataArr);
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

        function formatDate(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }

        function showLoading() {
            document.getElementById('statusContainer').innerHTML = `
                <div class="loading-spinner"></div>
                <p class="text-center text-gray-500">Memuat data absensi...</p>
            `;
            document.getElementById('attendanceBody').innerHTML = '';
            document.getElementById('paginationContainer').innerHTML = '';
        }

        function showError(message) {
            document.getElementById('statusContainer').innerHTML = `
                <div class="error-message">
                    <strong>Error:</strong> ${message}
                </div>
            `;
            document.getElementById('attendanceBody').innerHTML = '';
            document.getElementById('paginationContainer').innerHTML = '';
        }

        document.addEventListener('DOMContentLoaded', function() {
            fetchAttendanceData(1);

            const searchInput = document.getElementById('searchInput');
            let searchTimeout;

            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    currentSearch = this.value;
                    fetchAttendanceData(1);
                }, 500);
            });
        });
    </script>
@endsection
