<!-- Modal Tambah Karyawan -->
<div id="modalTambahKaryawan" class="fixed inset-0 z-50 flex hidden items-center justify-center bg-black/50">
    <div class="animate-scaleIn w-full max-w-sm rounded-2xl bg-white p-4 shadow-lg">
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
                    class="w-full rounded-lg border border-gray-200 bg-gray-100 px-3 py-2 text-sm shadow-sm focus:border-blue-400 focus:ring focus:ring-blue-300">
            </div>


            <!-- NIP -->
            <div>
                <label for="nip" class="block text-sm font-semibold">NIP</label>
                <input type="text" name="nip" id="nip"
                    class="w-full rounded-lg border border-gray-200 bg-gray-100 px-3 py-2 text-sm shadow-sm focus:border-blue-400 focus:ring focus:ring-blue-300">
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-semibold">Email</label>
                <input type="email" name="email" id="email" required
                    class="w-full rounded-lg border border-gray-200 bg-gray-100 px-3 py-2 text-sm shadow-sm focus:border-blue-400 focus:ring focus:ring-blue-300">
            </div>

            <!-- Telepon -->
            <div>
                <label for="telepon" class="block text-sm font-semibold">No. Telepon</label>
                <input type="text" name="telepon" id="telepon"
                    class="w-full rounded-lg border border-gray-200 bg-gray-100 px-3 py-2 text-sm shadow-sm focus:border-blue-400 focus:ring focus:ring-blue-300">
            </div>

            <!-- Divisi -->
            <div>
                <label for="divisi" class="block text-sm font-semibold">Divisi</label>
                <select name="divisi" id="divisi"
                    class="w-full rounded-lg border border-gray-200 bg-gray-100 px-3 py-2 text-sm shadow-sm focus:border-blue-400 focus:ring focus:ring-blue-300">
                    <option value="" disabled selected>Pilih Divisi</option>
                    <option value="GURU">Guru</option>
                    <option value="STAFF">Staff</option>
                </select>
            </div>

            <!-- Mapel -->
            <div>
                <label for="mapel" class="block text-sm font-semibold">Mapel</label>
                <select name="mapel" id="mapel"
                    class="w-full rounded-lg border border-gray-200 bg-gray-100 px-3 py-2 text-sm shadow-sm focus:border-blue-400 focus:ring focus:ring-blue-300">
                    <option value="" disabled selected>Pilih Mapel</option>
                    <option value="PPLG">PPLG</option>
                    <option value="TJKT">TJKT</option>
                    <option value="DKV">DKV</option>
                    <option value="KULINER">KULINER</option>
                    <option value="HOTEL">HOTEL</option>
                    <option value="PMN">PMN</option>
                    <option value="MPLB">MPLB</option>
                    <option value="MATEMATIKA">MATEMATIKA</option>
                    <option value="PKK">PKK</option>
                    <option value="PJOK">PJOK</option>
                    <option value="SEJARAH">SEJARAH</option>
                    <option value="B INGGRIS">B INGGRIS</option>
                    <option value="B INDONESIA">B INDONESIA</option>
                    <option value="PP">PP</option>
                    <option value="PABP">PABP</option>
                    <option value="B SUNDA">B SUNDA</option>
                    <option value="INFORMATIKA">INFORMATIKA</option>
                </select>
            </div>

            <!-- Password -->
            <div class="relative">
                <label for="password" class="block text-sm font-semibold">Password <span
                        class="text-xs text-gray-500">(Opsional, akan diisi NIP)</span></label>
                <input type="password" name="password" id="password"
                    class="w-full rounded-lg border border-gray-200 bg-gray-100 px-3 py-2 pr-10 text-sm shadow-sm focus:border-blue-400 focus:ring focus:ring-blue-300"
                    placeholder="Kosongkan untuk password default (NIP)">
                <i id="togglePassword" class="fa-solid fa-eye absolute right-3 top-8 cursor-pointer text-gray-500"></i>
            </div>

            <!-- Tombol -->
            <div class="pt-1">
                <button type="submit"
                    class="w-full rounded-lg bg-blue-400 py-2 text-sm font-bold text-white shadow hover:bg-blue-500">
                    Tambah
                </button>
            </div>
        </form>
    </div>
</div>
