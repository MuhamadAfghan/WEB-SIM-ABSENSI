<!-- Modal Tambah Karyawan -->
<div id="modalTambahKaryawan" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-2xl shadow-lg w-full max-w-sm p-4 animate-scaleIn">
        <!-- Header -->
        <div class="flex items-center justify-between border-b pb-2">
            <h5 class="text-base font-bold">Tambah Karyawan</h5>
            <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <!-- Body -->
        <form id="formTambahKaryawan" method="POST" onsubmit="submitForm(event)" class="mt-3 space-y-2">
            @csrf
            <!-- Nama -->
            <div>
                <label for="name" class="block text-sm font-semibold">Nama</label>
                <input type="text" name="name" id="name" required
                    class="w-full text-sm rounded-lg border border-gray-200 bg-gray-100 px-3 py-2 shadow-sm focus:border-blue-400 focus:ring focus:ring-blue-300">
            </div>

            <!-- Username -->
            <div>
                <label for="username" class="block text-sm font-semibold">Username</label>
                <input type="text" name="username" id="username" required
                    class="w-full text-sm rounded-lg border border-gray-200 bg-gray-100 px-3 py-2 shadow-sm focus:border-blue-400 focus:ring focus:ring-blue-300">
            </div>

            <!-- NIP -->
            <div>
                <label for="nip" class="block text-sm font-semibold">NIP</label>
                <input type="text" name="nip" id="nip"
                    class="w-full text-sm rounded-lg border border-gray-200 bg-gray-100 px-3 py-2 shadow-sm focus:border-blue-400 focus:ring focus:ring-blue-300">
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-semibold">Email</label>
                <input type="email" name="email" id="email" required
                    class="w-full text-sm rounded-lg border border-gray-200 bg-gray-100 px-3 py-2 shadow-sm focus:border-blue-400 focus:ring focus:ring-blue-300">
            </div>

            <!-- Telepon -->
            <div>
                <label for="telepon" class="block text-sm font-semibold">No. Telepon</label>
                <input type="text" name="telepon" id="telepon"
                    class="w-full text-sm rounded-lg border border-gray-200 bg-gray-100 px-3 py-2 shadow-sm focus:border-blue-400 focus:ring focus:ring-blue-300">
            </div>

            <!-- Divisi -->
            <div>
                <label for="divisi" class="block text-sm font-semibold">Divisi</label>
                <select name="divisi" id="divisi"
                    class="w-full text-sm rounded-lg border border-gray-200 bg-gray-100 px-3 py-2 shadow-sm focus:border-blue-400 focus:ring focus:ring-blue-300">
                    <option value="" disabled selected>Pilih Divisi</option>
                    <option value="GURU">Guru</option>
                    <option value="STAFF">Staff</option>
                </select>
            </div>

            <!-- Mapel -->
            <div>
                <label for="mapel" class="block text-sm font-semibold">Mapel</label>
                <input type="text" name="mapel" id="mapel"
                    class="w-full text-sm rounded-lg border border-gray-200 bg-gray-100 px-3 py-2 shadow-sm focus:border-blue-400 focus:ring focus:ring-blue-300">
            </div>

            <!-- Password -->
            <div class="relative">
                <label for="password" class="block text-sm font-semibold">Password</label>
                <input type="password" name="password" id="password" required
                    class="w-full text-sm rounded-lg border border-gray-200 bg-gray-100 px-3 py-2 pr-10 shadow-sm focus:border-blue-400 focus:ring focus:ring-blue-300">
                <i id="togglePassword" class="fa-solid fa-eye absolute right-3 top-8 text-gray-500 cursor-pointer"></i>
            </div>

            <!-- Tombol -->
            <div class="pt-1">
                <button type="submit"
                    class="w-full bg-blue-400 hover:bg-blue-500 text-white font-bold py-2 rounded-lg shadow text-sm">
                    Tambah
                </button>
            </div>
        </form>
    </div>
</div>
