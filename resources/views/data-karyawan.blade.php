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
            <button class="bg-orange-400 text-white p-2 rounded hover:bg-orange-500">
                <img src="{{ asset('image/upload_file_white.png') }}" alt="Icon" class="w-5 h-5 inline">
            </button>

            <button onclick="openModal()" class="bg-orange-400 text-white p-2 rounded hover:bg-orange-500">
                <img src="{{ asset('image/plus_white.png') }}" alt="Icon" class="w-5 h-5 inline">
            </button>

            {{-- filter --}}
            <button id="filterBtn" class="relative bg-orange-400 text-white p-2 rounded hover:bg-orange-500">
                <img src="{{ asset('image/filter_white.png') }}" alt="Icon" class="w-5 h-5 inline">
                    @include('components.filter-dropdown')
            </button>

        {{-- search --}}
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

            <tbody class="divide-y border-gray-200 bg-gray-100">
                <tr>
                    <td class="px-4 py-3">1</td>
                    <td class="px-4 py-3">Siti Nurhaliza</td>
                    <td class="px-4 py-3">123456789012134567818</td>
                    <td class="px-4 py-3">Guru</td>
                    <td class="px-4 py-3">PPLG</td>
                </tr>
                <tr>
                    <td class="px-4 py-3">2</td>
                    <td class="px-4 py-3">Siti Nurhaliza</td>
                    <td class="px-4 py-3">123456789012134567818</td>
                    <td class="px-4 py-3">Guru</td>
                    <td class="px-4 py-3">PPLG</td>
                </tr>
                <tr>
                    <td class="px-4 py-3">3</td>
                    <td class="px-4 py-3">Siti Nurhaliza</td>
                    <td class="px-4 py-3">123456789012134567818</td>
                    <td class="px-4 py-3">Guru</td>
                    <td class="px-4 py-3">PPLG</td>
                </tr>
                <tr>
                    <td class="px-4 py-3">4</td>
                    <td class="px-4 py-3">Siti Nurhaliza</td>
                    <td class="px-4 py-3">123456789012134567818</td>
                    <td class="px-4 py-3">Guru</td>
                    <td class="px-4 py-3">PPLG</td>
                </tr>
                <tr>
                    <td class="px-4 py-3">5</td>
                    <td class="px-4 py-3">Siti Nurhaliza</td>
                    <td class="px-4 py-3">123456789012134567818</td>
                    <td class="px-4 py-3">Guru</td>
                    <td class="px-4 py-3">PPLG</td>
                </tr>
                <tr>
                    <td class="px-4 py-3">6</td>
                    <td class="px-4 py-3">Siti Nurhaliza</td>
                    <td class="px-4 py-3">123456789012134567818</td>
                    <td class="px-4 py-3">Guru</td>
                    <td class="px-4 py-3">PPLG</td>
                </tr>
                <tr>
                    <td class="px-4 py-3">7</td>
                    <td class="px-4 py-3">zahran</td>
                    <td class="px-4 py-3">123456789012134567818</td>
                    <td class="px-4 py-3">Guru</td>
                    <td class="px-4 py-3">PPLG</td>
                </tr>
                <tr>
                    <td class="px-4 py-3 rounded-tr-lg">8</td>
                    <td class="px-4 py-3">safa</td>
                    <td class="px-4 py-3">123456789012134567818</td>
                    <td class="px-4 py-3">Guru</td>
                    <td class="px-4 py-3 rounded-tr-lg">PPLG</td>
                </tr>

            <tbody class="divide-y border-gray-200 bg-gray-100" id="karyawanTableBody">
                <!-- Data will be loaded here dynamically -->

            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="flex justify-end items-center p-4 gap-2">
        <button class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-500 hover:bg-blue-100 hover:text-gray-700 dark:bg-blue-100 dark:bg-blue-100 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
            &lt;
        </button>
        <button class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-500 hover:bg-blue-100 hover:text-gray-700 dark:bg-blue-100 dark:bg-blue-100 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
            1
        </button>
        <button class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-500 hover:bg-blue-100 hover:text-gray-700 dark:bg-blue-100 dark:bg-blue-100 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
            2
        </button>
        <button class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-500 hover:bg-blue-100 hover:text-gray-700 dark:bg-blue-100 dark:bg-blue-100 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
            3
        </button>
        <button class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-500 hover:bg-blue-100 hover:text-gray-700 dark:bg-blue-100 dark:bg-blue-100 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
            &gt;
        </button>
    </div>

        @include('components.karyawan.form')

    <!-- Tambahkan ini sebelum script JavaScript custom -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function openModal() {
            document.getElementById('modalTambahKaryawan').classList.remove('hidden');
        }
        
        function closeModal() {
            document.getElementById('modalTambahKaryawan').classList.add('hidden');
        }
        
        document.getElementById('togglePassword').addEventListener('click', function () {
            const passwordField = document.getElementById('password');
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                this.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                passwordField.type = 'password';
                this.classList.replace('fa-eye-slash', 'fa-eye');
            }
        });

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

                // Alert sukses
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Karyawan berhasil ditambahkan',
                    showConfirmButton: false,
                    timer: 5000
                });

                await loadDataKaryawan();

            } catch (error) {
                // Alert error
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: error.message,
                    confirmButtonColor: '#d33'
                });
                console.error('Error:', error);
            }
        }

        async function loadDataKaryawan() {
            try {
                let response = await fetch('/api/user');
                if (!response.ok) throw new Error('Gagal memuat data karyawan');
                
                let result = await response.json();
                
                if (result.status !== 'success') {
                    throw new Error(result.message || 'Gagal memuat data karyawan');
                }

                let tbody = document.getElementById('karyawanTableBody');
                tbody.innerHTML = '';

                if (result.data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="5" class="px-4 py-3 text-center">Tidak ada data karyawan</td></tr>';
                    return;
                }

                result.data.forEach((item, index) => {
                    let row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="px-4 py-3">${index + 1}</td>
                        <td class="px-4 py-3">${item.name}</td>
                        <td class="px-4 py-3">${item.nip || '-'}</td>
                        <td class="px-4 py-3">${item.divisi || '-'}</td>
                        <td class="px-4 py-3">${item.mapel || '-'}</td>
                    `;
                    tbody.appendChild(row);
                });

            } catch (error) {
                console.error('Error:', error);
                let tbody = document.getElementById('karyawanTableBody');
                tbody.innerHTML = `<tr><td colspan="5" class="px-4 py-3 text-center text-red-500">${error.message}</td></tr>`;
            }
        }

        document.addEventListener('DOMContentLoaded', loadDataKaryawan);
    </script>
@endsection

<script>
    // fitur filter
    document.addEventListener('DOMContentLoaded', () => {
        const filterBtn = document.getElementById('filterBtn');
        const filterDropdown = document.getElementById('filterDropdown');

        filterBtn.addEventListener('click', () => filterDropdown.classList.toggle('hidden'));

        document.addEventListener('click', (e) => {
            if (!filterBtn.contains(e.target) && !filterDropdown.contains(e.target))
                filterDropdown.classList.add('hidden');
        });

        // fitur search
        const searchInput = document.getElementById('searchInput');
        const tableRows = document.querySelectorAll('tbody tr');

        searchInput.addEventListener('keyup', () => {
            const keyword = searchInput.value.toLowerCase();
            let nomor = 1;

            tableRows.forEach(row => {
                const nama = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                if (nama.includes(keyword)) {
                    row.style.display = '';
                    row.querySelector('td:nth-child(1)').textContent = nomor++;
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });
</script>
