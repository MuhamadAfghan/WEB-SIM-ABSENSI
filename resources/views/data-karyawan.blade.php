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

            <!-- Tombol Upload XLS -->
            <button onclick="openUploadModal()"
                class="bg-orange-400 text-white p-2 rounded hover:bg-orange-500 transition-colors duration-200 ease-in-out">
                <img src="{{ asset('image/upload_file_white.png') }}" alt="Icon" class="w-5 h-5 inline">
            </button>

            <!-- Tombol Tambah Karyawan -->
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
            <tbody class="divide-y border-gray-200 bg-gray-100" id="karyawanTableBody">
                <!-- Data via API -->
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="flex justify-end items-center p-4 gap-2">
        <button class="px-3 h-8 text-gray-500 hover:bg-blue-100 hover:text-gray-700">&lt;</button>
        <button class="px-3 h-8 text-gray-500 hover:bg-blue-100 hover:text-gray-700">1</button>
        <button class="px-3 h-8 text-gray-500 hover:bg-blue-100 hover:text-gray-700">2</button>
        <button class="px-3 h-8 text-gray-500 hover:bg-blue-100 hover:text-gray-700">3</button>
        <button class="px-3 h-8 text-gray-500 hover:bg-blue-100 hover:text-gray-700">&gt;</button>
    </div>

    @include('components.karyawan.form')
    <!-- ================= MODAL UPLOAD ================= -->
    <div id="uploadModal"
        class="fixed inset-0 bg-black/50 z-50 items-center justify-center opacity-0 pointer-events-none transition-opacity duration-300 ease-in-out">
        <div class="w-[460px] h-[280px] bg-white shadow-md rounded-lg p-3 relative">

            <!-- Header -->
            <div
                class="absolute top-[8px] left-[24px] w-[410px] h-[28px] p-[5px] border-b border-gray-300 flex items-center gap-[5px]">
                <div class="w-[18px] h-[18px] cursor-pointer" onclick="closeUploadModal()">
                    <img src="{{ asset('images/cancel.png') }}" alt="Cancel" class="w-[18px] h-[18px]" />
                </div> 
                <div class="w-[87px] h-[18px] flex items-center justify-center">
                    <span class="font-bold text-[12px] text-gray-700">Upload XLS</span>
                </div>
            </div>

            <!-- Dropzone -->
            <div id="dropzone"
                class="absolute top-[61px] left-[24px] w-[410px] h-[142px] border-[2px] border-dashed border-[#A3A3A3] rounded-[15px] cursor-pointer"
                onclick="document.getElementById('uploadFile').click()">
            </div>

            <!-- Input File -->
            <div class="absolute top-[130px] left-[96px] w-[269px] h-[18px] flex items-center justify-center z-10">
                <input type="file" id="uploadFile" accept=".csv,.xls,.xlsx" class="hidden" />
                <label for="uploadFile" class="font-semibold text-[12px] text-center text-gray-700 cursor-pointer">
                    Seret &amp; Lepas atau
                    <span class="text-blue-600">Klik untuk mengunggah file</span>
                </label>
            </div>

            <!-- Nama file -->
            <div class="absolute top-[140px] left-[24px] w-[410px] text-center">
                <span id="fileName" class="text-[12px] text-gray-500 font-medium"></span>
            </div>

            <p class="w-[328px] absolute top-[175px] left-[66px] text-center text-[12px] font-medium leading-none">
                Mendukung format: CSV atau XLS. Maks ukuran: 25 MB
            </p>

            <!-- Tombol -->
            <div
                class="absolute top-[223px] left-[266px] w-[60px] h-[30px] bg-[#D2D2D2] rounded-md flex items-center justify-center">
                <button onclick="closeUploadModal()" class="text-[#7B7B7B] font-bold text-[12px]">Batal</button>
            </div>
            <div
                class="absolute top-[223px] left-[335px] w-[100px] h-[30px] bg-[#60B5FF] rounded-md flex items-center justify-center">
                <button onclick="uploadFile()" class="text-white font-bold text-[12px]">Selanjutnya</button>
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
