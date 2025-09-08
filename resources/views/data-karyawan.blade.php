@extends('components.template')

@section('title', 'Data Karyawan - Hadir.in')

@section('content')
    <style>
        table th:nth-child(1),
        table td:nth-child(1) {
            width: 50px;
            /* lebar kolom NO */
            text-align: center;
            /* nomor rata tengah */
        }

        table * {
            border-color: #e5e7eb;
        }
    </style>
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-3xl font-black tracking-wide">Daftar karyawan</h1>
        <div class="flex gap-2">
            <!-- Tombol Upload -->
<button onclick="openUploadModal()"
    class="bg-orange-400 text-white p-2 rounded hover:bg-orange-500 transition-colors duration-200 ease-in-out">
    <img src="{{ asset('image/upload_file_white.png') }}" alt="Icon" class="w-5 h-5 inline">
</button><!-- Overlay Modal Upload -->
<div id="uploadModal"
  class="fixed inset-0 bg-black/50 z-50 items-center justify-center opacity-0 pointer-events-none transition-opacity duration-300 ease-in-out">

  <!-- Modal Content Upload -->
  <div class="w-[460px] h-[280px] bg-white shadow-md rounded-lg p-3 relative">
    <!-- Icon Upload -->
    <div class="absolute top-[92px] left-[211px] w-[40px] h-[40px]">
      <img src="{{ asset('images/file.png') }}" alt="Upload" class="w-full h-full object-contain" />
    </div>

    <!-- Header -->
    <div class="absolute top-[8px] left-[24px] w-[410px] h-[28px] p-[5px] border-b border-gray-300 flex items-center gap-[5px]">
      <div class="w-[18px] h-[18px] cursor-pointer" onclick="closeModal()">
        <img src="{{ asset('images/cancel.png') }}" alt="Cancel" class="w-[18px] h-[18px] object-contain" />
      </div>
      <div class="w-[87px] h-[18px] flex items-center justify-center">
        <span class="font-poppins font-bold text-[12px] leading-[12px] text-gray-700">Upload XLS</span>
      </div>
    </div>

    <!-- Dropzone (klik memicu input file) -->
    <div id="dropzone"
      class="absolute top-[61px] left-[24px] w-[410px] h-[142px] border-[2px] border-dashed border-[#A3A3A3] rounded-[15px] cursor-pointer"
      onclick="document.getElementById('uploadFile').click()">
    </div>

    <!-- Upload Text + INPUT FILE (HANYA SATU) -->
    <div class="absolute top-[145px] left-[96px] w-[269px] h-[18px] flex items-center justify-center z-10">
      <input type="file" id="uploadFile" accept=".csv,.xls,.xlsx" class="hidden" />
      <label for="uploadFile"
        class="font-poppins font-semibold text-[12px] leading-[12px] text-center text-gray-700 cursor-pointer">
        Seret &amp; Lepas atau
        <span class="text-blue-600">Klik untuk mengunggah file</span>
      </label>
    </div>

    <!-- TAMBAHAN: Nama file muncul di sini -->
    <div class="absolute top-[170px] left-[24px] w-[410px] text-center">
      <span id="fileName" class="text-[12px] text-gray-500 font-medium"></span>
    </div>

    <!-- Info format -->
    <p class="w-[328px] h-[18px] absolute top-[164px] left-[66px] text-center text-[12px] font-medium leading-none">
      Mendukung format: CSV atau XLS. Maks ukuran: 25 MB
    </p>


    <!-- Tombol -->
<div class="absolute top-[223px] left-[266px] w-[60px] h-[30px] p-[5px] rounded-[4px] bg-[#D2D2D2] flex items-center justify-center">
  <button onclick="closeModal()" class="w-[33px] h-[18px] font-poppins font-bold text-[12px] leading-[12px] text-[#7B7B7B]">
    Batal
  </button>
</div>
<div class="absolute top-[223px] left-[335px] w-[100px] h-[30px] p-[5px] rounded-[4px] bg-[#60B5FF] flex items-center justify-center">
<button onclick="goToProcessModal()"
  class="w-[75px] h-[18px] rounded font-poppins font-bold text-[12px] leading-[12px] text-white">
  Selanjutnya
</button>

</div>

  </div>
</div>


<!-- Modal Process -->
<div id="processModal"
    class="fixed inset-0 bg-opacity-50 flex items-center justify-center opacity-0 pointer-events-none transition-opacity duration-300 ease-in-out">

    <div
        class="relative w-[460px] h-[523px] bg-white shadow-xl rounded-xl overflow-hidden border border-gray-200">

        <!-- Header -->
        <div
            class="absolute top-[9px] left-[25px] w-[410px] h-[28px] p-[5px] border-b border-[#B3B0B0] flex items-center gap-[5px]">
            <!-- Cancel -->
            <div class="w-[18px] h-[18px] cursor-pointer" onclick="closeProcessModal()">
                <img src="{{ asset('images/cancel.png') }}" alt="Cancel"
                    class="w-[18px] h-[18px] object-contain" />
            </div>
            <!-- Title -->
            <div class="w-[87px] h-[18px] flex items-center justify-center">
                <span class="font-poppins font-bold text-[12px] text-black">Upload XLS</span>
            </div>
        </div>

        <!-- Dropzone -->
        <div
            class="absolute top-[61px] left-[25px] w-[410px] h-[142px] border-[2px] border-dashed border-[#A3A3A3] rounded-[15px]">
        </div>

        <!-- Upload Icon -->
        <div class="absolute top-[92px] left-[211px] w-[40px] h-[40px]">
            <img src="{{ asset('images/file.png') }}" alt="">
        </div>

        <!-- Upload Text -->
        <div class="absolute top-[145px] left-[96px] w-[269px] h-[18px] flex items-center justify-center">
            <input type="file" id="fileUpload" accept=".csv,.xls,.xlsx" class="hidden" />
            <label for="fileUpload" class="font-poppins font-semibold text-[12px] text-gray-700 cursor-pointer">
                Seret & Lepas atau
                <a href="#" class="text-blue-600 no-underline"> Klik untuk mengunggah file </a>
            </label>
        </div>
        <p
            class="w-[328px] h-[18px] absolute top-[164px] left-[66px] text-center text-[12px] font-medium leading-none">
            Mendukung format: CSV atau XLS. Maks ukuran: 25 MB
        </p>

        <!-- File Progress -->
        <div class="absolute top-[236px] left-[25px] w-[410px] h-[65px] bg-[#D1D1D1] rounded-[6px] status-progress hidden">
            <div class="absolute top-[46px] left-[80px] w-[315px] h-[8px] bg-[#D9D9D9] rounded-[12px]"></div>
            <div class="absolute top-[46px] left-[80px] h-[8px] bg-[#60B5FF] rounded-[12px] progress-blue"></div>
            <div class="absolute top-[26px] left-[55px] text-[#A3A3A3] font-semibold text-[12px] file-size">-</div>
            <div class="absolute top-[10px] left-[55px] text-black font-semibold text-[12px] file-name">-</div>
        </div>

        <!-- File Success -->
        <div class="absolute top-[314px] left-[25px] w-[410px] h-[65px] bg-[#D1D1D1] rounded-[6px] hidden status-success">
            <div class="absolute top-[26px] left-[55px] text-[#A3A3A3] font-semibold text-[12px] file-size">-</div>
            <div class="absolute top-[10px] left-[55px] text-black font-semibold text-[12px] file-name">-</div>
        </div>

        <!-- File Failed -->
        <div class="absolute top-[391px] left-[25px] w-[410px] h-[65px] bg-[#D1D1D1] rounded-[6px] hidden status-failed">
            <div class="absolute top-[32px] left-[353px] w-[18px] h-[18px]">
                <img src="{{ asset('images/mage_reload.png') }}" alt="">
            </div>
            <div class="absolute top-[32px] left-[378px] w-[18px] h-[18px]">
                <img src="{{ asset('images/delete.png') }}" alt="">
            </div>
            <div class="absolute top-[30px] left-[55px] text-[#FF6347] font-medium text-[10px]">Gagal unggah
                file</div>
            <div class="absolute top-[10px] left-[55px] text-black font-semibold text-[12px] file-name">-</div>
            <div class="absolute top-[12px] left-[9px] w-[33px] h-[34px] bg-[#FFFFFF]"></div>
        </div>

        <!-- Footer Buttons -->
        <div onclick="closeProcessModal()"
            class="absolute top-[481px] left-[266px] w-[60px] h-[30px] bg-[#D2D2D2] rounded-md flex items-center justify-center">
            <button class="text-[#7B7B7B] font-bold text-[12px]">Batal</button>
        </div>
        <a href="/data-karyawan"
            class="absolute top-[481px] left-[335px] w-[100px] h-[30px] bg-[#60B5FF] rounded-md flex items-center justify-center">
            <button class="text-white font-bold text-[12px]">Selanjutnya</button>
        </a>
    </div>
</div>
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

    function openProcessModal() {
        const modal = document.getElementById('processModal');
        modal.classList.remove("opacity-0", "pointer-events-none");
        modal.classList.add("flex");
    }

    function closeProcessModal() {
        const modal = document.getElementById('processModal');
        modal.classList.add("opacity-0", "pointer-events-none");
        modal.classList.remove("flex");
    }

    // === Ambil file dari input upload (pastikan ID input = "fileUpload") ===
    document.getElementById("uploadFile").addEventListener("change", function (event) {
    selectedFile = event.target.files[0];
    if (selectedFile) {
        document.getElementById("fileName").textContent = selectedFile.name;
    }
});
    // === Klik Selanjutnya ===
    function goToProcessModal() {
        if (!selectedFile) {
            alert("Silakan pilih file terlebih dahulu!");
            return;
        }

        closeUploadModal();
        openProcessModal();

        // ambil elemen status
        const progressBox = document.querySelector("#processModal .status-progress");
        const successBox  = document.querySelector("#processModal .status-success");
        const failedBox   = document.querySelector("#processModal .status-failed");

        // reset state
        progressBox.classList.remove("hidden");
        successBox.classList.add("hidden");
        failedBox.classList.add("hidden");

        // isi nama file & size di progress
        document.querySelector("#processModal .status-progress .file-name").textContent = selectedFile.name;
        document.querySelector("#processModal .status-progress .file-size").textContent = (selectedFile.size / 1024).toFixed(1) + " KB";

        const formData = new FormData();
        formData.append("file", selectedFile);
        formData.append("_token", "{{ csrf_token() }}");

        fetch("{{ route('users.upload') }}", {
            method: "POST",
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            progressBox.classList.add("hidden");

            if (data.status === "success") {
                document.querySelector("#processModal .status-success .file-name").textContent = selectedFile.name;
                document.querySelector("#processModal .status-success .file-size").textContent = (selectedFile.size / 1024).toFixed(1) + " KB";
                successBox.classList.remove("hidden");
            } else {
                failedBox.classList.remove("hidden");
            }
        })
        .catch(err => {
            console.error(err);
            progressBox.classList.add("hidden");
            failedBox.classList.remove("hidden");
        });
    }
</script>



            <button class="bg-orange-400 text-white p-2 rounded hover:bg-orange-500">
                <img src="{{ asset('image/plus_white.png') }}" alt="Icon" class="w-5 h-5 inline">
            </button>
            <button class="bg-orange-400 text-white p-2 rounded hover:bg-orange-500">
                <img src="{{ asset('image/filter_white.png') }}" alt="Icon" class="w-5 h-5 inline">
            </button>
            <div class="relative">
                <img src="{{ asset('image/search_grey.png') }}" alt="Search"
                    class="w-5 h-5 absolute left-3 top-1/2 transform -translate-y-1/2">
                <input type="text" placeholder="Cari Guru" class="rounded px-3 py-2 text-sm border pl-10 w-50" />
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
   
</tbody>

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
                    <td class="px-4 py-3">Siti Nurhaliza</td>
                    <td class="px-4 py-3">123456789012134567818</td>
                    <td class="px-4 py-3">Guru</td>
                    <td class="px-4 py-3">PPLG</td>
                </tr>
                <tr>
                    <td class="px-4 py-3 rounded-tr-lg">8</td>
                    <td class="px-4 py-3">Siti Nurhaliza</td>
                    <td class="px-4 py-3">123456789012134567818</td>
                    <td class="px-4 py-3">Guru</td>
                    <td class="px-4 py-3 rounded-tr-lg">PPLG</td>
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
