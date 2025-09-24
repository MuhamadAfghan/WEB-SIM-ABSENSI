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

    <div class="mb-4 flex items-center justify-between">
        <h1 class="text-3xl font-black tracking-wide">Daftar karyawan</h1>
        <div class="flex gap-2">

            <!-- Tombol Upload XLS -->
            <button onclick="openUploadModal()"
                class="rounded bg-orange-400 p-2 text-white transition-colors duration-200 ease-in-out hover:bg-orange-500">
                <img src="{{ asset('image/upload_file_white.png') }}" alt="Icon" class="inline h-5 w-5">
            </button>

            <!-- Tombol Tambah Karyawan -->
            <button onclick="openModal()" class="rounded bg-orange-400 p-2 text-white hover:bg-orange-500">
                <img src="{{ asset('image/plus_white.png') }}" alt="Icon" class="inline h-5 w-5">
            </button>

            {{-- filter --}}
            <button id="filterBtn" class="relative rounded bg-orange-400 p-2 text-white hover:bg-orange-500">
                <img src="{{ asset('image/filter_white.png') }}" alt="Icon" class="inline h-5 w-5">
                @include('components.filter-dropdown')
            </button>

            {{-- search --}}
            <div class="relative flex items-center">
                <img src="{{ asset('image/search_grey.png') }}" alt="Search"
                    class="absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 transform" />
                <input id="searchInput" type="text" placeholder="Cari Nama"
                    class="w-50 rounded border px-3 py-2 pl-10 text-sm" />
            </div>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-white">
                <tr>
                    <th class="rounded-tl-lg px-4 py-3 font-bold">NO</th>
                    <th class="px-4 py-3 font-bold">Nama</th>
                    <th class="px-4 py-3 font-bold">NIP</th>
                    <th class="px-4 py-3 font-bold">Divisi</th>
                    <th class="rounded-tr-lg px-4 py-3 font-bold">Mapel</th>
                </tr>
            </thead>
            <tbody class="divide-y border-gray-200 bg-gray-100" id="karyawanTableBody">
                <!-- Data via API -->
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="flex items-center justify-end gap-2 p-4">
        <button class="h-8 px-3 text-gray-500 hover:bg-blue-100 hover:text-gray-700">&lt;</button>
        <button class="h-8 px-3 text-gray-500 hover:bg-blue-100 hover:text-gray-700">1</button>
        <button class="h-8 px-3 text-gray-500 hover:bg-blue-100 hover:text-gray-700">2</button>
        <button class="h-8 px-3 text-gray-500 hover:bg-blue-100 hover:text-gray-700">3</button>
        <button class="h-8 px-3 text-gray-500 hover:bg-blue-100 hover:text-gray-700">&gt;</button>
    </div>

    @include('components.karyawan.form')
    <!-- ================= MODAL UPLOAD ================= -->
    <div id="uploadModal"
        class="pointer-events-none fixed inset-0 z-50 items-center justify-center bg-black/50 opacity-0 transition-opacity duration-300 ease-in-out">
        <div class="relative h-[280px] w-[460px] rounded-lg bg-white p-3 shadow-md">

            <!-- Header -->
            <div
                class="absolute left-[24px] top-[8px] flex h-[28px] w-[410px] items-center gap-[5px] border-b border-gray-300 p-[5px]">
                <div class="h-[18px] w-[18px] cursor-pointer" onclick="closeUploadModal()">
                    <img src="{{ asset('images/cancel.png') }}" alt="Cancel" class="h-[18px] w-[18px]" />
                </div>
                <div class="flex h-[18px] w-[87px] items-center justify-center">
                    <span class="text-[12px] font-bold text-gray-700">Upload XLS</span>
                </div>
            </div>

            <!-- Dropzone -->
            <div id="dropzone"
                class="absolute left-[24px] top-[61px] h-[142px] w-[410px] cursor-pointer rounded-[15px] border-[2px] border-dashed border-[#A3A3A3]"
                onclick="document.getElementById('uploadFile').click()">
            </div>

            <!-- Input File -->
            <div class="absolute left-[96px] top-[130px] z-10 flex h-[18px] w-[269px] items-center justify-center">
                <input type="file" id="uploadFile" accept=".csv,.xls,.xlsx" class="hidden" />
                <label for="uploadFile" class="cursor-pointer text-center text-[12px] font-semibold text-gray-700">
                    Seret &amp; Lepas atau
                    <span class="text-blue-600">Klik untuk mengunggah file</span>
                </label>
            </div>

            <!-- Nama file -->
            <div class="absolute left-[24px] top-[140px] w-[410px] text-center">
                <span id="fileName" class="text-[12px] font-medium text-gray-500"></span>
            </div>

            <p class="absolute left-[66px] top-[175px] w-[328px] text-center text-[12px] font-medium leading-none">
                Mendukung format: CSV atau XLS. Maks ukuran: 25 MB
            </p>

            <!-- Tombol -->
            <div
                class="absolute left-[266px] top-[223px] flex h-[30px] w-[60px] items-center justify-center rounded-md bg-[#D2D2D2]">
                <button onclick="closeUploadModal()" class="text-[12px] font-bold text-[#7B7B7B]">Batal</button>
            </div>
            <div
                class="absolute left-[335px] top-[223px] flex h-[30px] w-[100px] items-center justify-center rounded-md bg-[#60B5FF]">
                <button onclick="uploadFile()" class="text-[12px] font-bold text-white">Selanjutnya</button>
            </div>
        </div>
    </div>

    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        let selectedFile = null;

        // === Modal Control ===
        function openUploadModal() {
            const modal = document.getElementById('uploadModal');
            modal.classList.remove("opacity-0", "pointer-events-none");
            modal.classList.add("flex");
        }

        function closeUploadModal() {
            const modal = document.getElementById('uploadModal');
            modal.classList.add("opacity-0", "pointer-events-none");
            modal.classList.remove("flex");
        }

        // === Ambil file dari input ===
        document.getElementById("uploadFile").addEventListener("change", function(event) {
            selectedFile = event.target.files[0];
            if (selectedFile) {
                document.getElementById("fileName").textContent = selectedFile.name;
            }
        });

        // === Upload File ===
        function uploadFile() {
            if (!selectedFile) {
                Swal.fire("Oops!", "Silakan pilih file terlebih dahulu!", "warning");
                return;
            }

            const formData = new FormData();
            formData.append("file", selectedFile);
            formData.append("_token", "{{ csrf_token() }}");

            fetch("/api/user/import", {
                    method: "POST",
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === "success") {
                        Swal.fire("Berhasil!", "Data karyawan berhasil diimport.", "success");
                        closeUploadModal();
                        if (typeof loadDataKaryawan === "function") {
                            loadDataKaryawan();
                        }
                    } else {
                        Swal.fire("Gagal!", data.message || "Terjadi kesalahan saat import.", "error");
                    }
                })
                .catch(err => {
                    console.error(err);
                    Swal.fire("Error!", "Server error. Coba lagi nanti.", "error");
                });

        }
    </script>
    <!-- ========== SCRIPT LIST DATA ========== -->
    <script>
        let currentFilter = null;
        let currentSearch = "";

        async function loadDataKaryawan() {
            try {
                let url = '/api/user?per_page=50';
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
                        <td class="px-4 py-3">${index + 1}</td>
                        <td class="px-4 py-3">${item.name}</td>
                        <td class="px-4 py-3">${item.nip || '-'}</td>
                        <td class="px-4 py-3">${item.divisi || '-'}</td>
                        <td class="px-4 py-3">${item.mapel || '-'}</td>
                    `;
                    tbody.appendChild(row);
                });

            } catch (error) {
                let tbody = document.getElementById('karyawanTableBody');
                tbody.innerHTML =
                    `<tr><td colspan="5" class="px-4 py-3 text-center text-red-500">${error.message}</td></tr>`;
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            loadDataKaryawan();

            // filter
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

            // search
            document.getElementById('searchInput').addEventListener('keyup', () => {
                currentSearch = document.getElementById('searchInput').value.trim();
                loadDataKaryawan();
            });
        });
    </script>
@endsection
