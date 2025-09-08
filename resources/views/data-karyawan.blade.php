@extends('components.template')

@section('title', 'Data Karyawan - Hadir.in')

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
</style>

<div class="flex justify-between items-center mb-4">
    <h1 class="text-3xl font-black tracking-wide">Daftar karyawan</h1>
    <div class="flex gap-2">
        {{-- Upload --}}
        <button class="bg-orange-400 text-white p-2 rounded hover:bg-orange-500">
            <img src="{{ asset('image/upload_file_white.png') }}" alt="Icon" class="w-5 h-5 inline">
        </button>

        {{-- Tambah --}}
        <button onclick="openModal()" class="bg-orange-400 text-white p-2 rounded hover:bg-orange-500">
            <img src="{{ asset('image/plus_white.png') }}" alt="Icon" class="w-5 h-5 inline">
        </button>

        {{-- Filter --}}
        <button id="filterBtn" class="relative bg-orange-400 text-white p-2 rounded hover:bg-orange-500">
            <img src="{{ asset('image/filter_white.png') }}" alt="Icon" class="w-5 h-5 inline">
            @include('components.filter-dropdown')
        </button>

        {{-- Search --}}
        <div class="relative flex items-center">
            <img src="{{ asset('image/search_grey.png') }}" alt="Search"
                class="w-5 h-5 absolute left-3 top-1/2 transform -translate-y-1/2" />
            <input id="searchInput" type="text" placeholder="Cari Nama"
                class="rounded px-3 py-2 text-sm border pl-10 w-50" />
        </div>
    </div>
</div>

<div class="overflow-x-auto">
    <table class="w-full text-left text-sm">
        <thead class="bg-white">
            <tr>
                <th class="px-4 py-3 font-bold rounded-tl-lg">NO</th>
                <th class="px-4 py-3 font-bold">Nama</th>
                <th class="px-4 py-3 font-bold">NIP</th>
                <th class="px-4 py-3 font-bold">Divisi</th>
                <th class="px-4 py-3 font-bold rounded-tr-lg">Mapel</th>
            </tr>
        </thead>

        <tbody class="divide-y border-gray-200 bg-gray-100" id="karyawanTableBody">
            <!-- Data dari API -->
        </tbody>
    </table>
</div>

<!-- Pagination -->
<div class="flex justify-center items-center p-4 gap-2" id="paginationContainer">
    <!-- Akan diisi via JS -->
</div>

@include('components.karyawan.form')

<!-- SweetAlert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function openModal() {
        document.getElementById('modalTambahKaryawan').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('modalTambahKaryawan').classList.add('hidden');
    }

    async function submitForm(e) {
        e.preventDefault();

        const form = document.getElementById('formTambahKaryawan');
        const formData = new FormData(form);

        try {
            let response = await fetch('/api/user', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json'
                },
                body: formData
            });

            let result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || 'Gagal menambah karyawan');
            }

            form.reset();
            closeModal();

            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Karyawan berhasil ditambahkan',
                showConfirmButton: false,
                timer: 3000
            });

            await loadDataKaryawan();

        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: error.message,
                confirmButtonColor: '#d33'
            });
            console.error('Error:', error);
        }
    }
</script>

<script>
    let currentFilter = null;
    let currentSearch = "";

    async function loadDataKaryawan(page = 1) {
        try {
            let url = `/api/user?page=${page}&per_page=10`;

            if (currentFilter) url += `&mapel=${encodeURIComponent(currentFilter)}`;
            if (currentSearch) url += `&search=${encodeURIComponent(currentSearch)}`;

            let response = await fetch(url);
            if (!response.ok) throw new Error('Gagal memuat data karyawan');

            let result = await response.json();
            if (result.status !== 'success') throw new Error(result.message || 'Gagal memuat data karyawan');

            let tbody = document.getElementById('karyawanTableBody');
            tbody.innerHTML = '';

            if (result.data.length === 0) {
                tbody.innerHTML =
                    '<tr><td colspan="5" class="px-4 py-3 text-center">Tidak ada data karyawan</td></tr>';
                return;
            }

            result.data.forEach((item, index) => {
                let row = document.createElement('tr');
                row.innerHTML = `
                    <td class="px-4 py-3">${(result.meta.from || 1) + index}</td>
                    <td class="px-4 py-3">${item.name}</td>
                    <td class="px-4 py-3">${item.nip || '-'}</td>
                    <td class="px-4 py-3">${item.divisi || '-'}</td>
                    <td class="px-4 py-3">${item.mapel || '-'}</td>
                `;
                tbody.appendChild(row);
            });

            renderPagination(result.meta);

        } catch (error) {
            console.error('Error:', error);
            let tbody = document.getElementById('karyawanTableBody');
            tbody.innerHTML =
                `<tr><td colspan="5" class="px-4 py-3 text-center text-red-500">${error.message}</td></tr>`;
        }
    }

    function renderPagination(meta) {
        let pagination = document.getElementById('paginationContainer');
        pagination.innerHTML = '';

        if (meta.last_page <= 1) return;

        const buttonHtml = (label, page, { disabled = false, active = false } = {}) => {
            const base = 'px-3 py-2 rounded-lg border text-sm';
            const classes = [
                base,
                active ? 'bg-[#60B5FF] text-white border-[#60B5FF]' :
                    'bg-white text-gray-700 border-gray-200 hover:bg-blue-50',
                disabled ? 'opacity-50 cursor-not-allowed' : ''
            ].join(' ');
            return `<button class="${classes}" ${disabled ? 'disabled' : ''} onclick="loadDataKaryawan(${page})">${label}</button>`;
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

    document.addEventListener('DOMContentLoaded', () => {
        loadDataKaryawan();

        const filterBtn = document.getElementById('filterBtn');
        const filterDropdown = document.getElementById('filterDropdown');

        filterBtn.addEventListener('click', () => filterDropdown.classList.toggle('hidden'));
        document.addEventListener('click', (e) => {
            if (!filterBtn.contains(e.target) && !filterDropdown.contains(e.target)) {
                filterDropdown.classList.add('hidden');
            }
        });

        document.querySelectorAll('.filter-item').forEach(item => {
            item.addEventListener('click', () => {
                currentFilter = item.getAttribute('data-mapel');
                loadDataKaryawan();
                filterDropdown.classList.add('hidden');
            });
        });

        document.getElementById('searchInput').addEventListener('keyup', () => {
            currentSearch = document.getElementById('searchInput').value.trim();
            loadDataKaryawan();
        });
    });
</script>
@endsection
