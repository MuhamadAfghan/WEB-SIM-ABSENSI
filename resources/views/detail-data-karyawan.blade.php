@extends('components.template')

@section('title', 'Detail Karyawan - Hadir.in')

@section('content')
    <div class="mb-5 rounded-xl bg-white">
        <!-- Header -->
        <div class="flex items-center justify-between border-b border-gray-200 p-4">
            <div class="flex items-center space-x-4">
                <a href="{{ route('account.management') }}" class="text-gray-600 hover:text-gray-800">
                    <i class="fas fa-chevron-left text-xl"></i>
                </a>
                <h1 id="employeeNameHeader" class="text-xl font-semibold text-gray-800">Loading…</h1>
            </div>
            <button class="flex items-center space-x-2 rounded-lg bg-blue-500 px-4 py-2 text-white hover:bg-blue-600">
                <i class="fas fa-edit"></i>
                <span>Edit</span>
            </button>
        </div>

        <!-- Employee Information -->
        <div class="p-6">
            <div class="mb-8 flex items-start space-x-6">
                <!-- Profile Picture -->
                <div class="h-24 w-24 flex-shrink-0 rounded-lg bg-gray-200">
                    <!-- Placeholder for profile image -->
                </div>

                <!-- Employee Details -->
                <div class="flex-1 space-y-3">
                    <div>
                        <span class="text-gray-600">NIP : </span>
                        <span id="employeeNip" class="font-medium text-gray-800">-</span>
                    </div>
                    <div>
                        <span class="text-gray-600">Nama : </span>
                        <span id="employeeName" class="font-medium text-gray-800">-</span>
                    </div>
                    <div>
                        <span class="text-gray-600">Email : </span>
                        <span id="employeeEmail" class="font-medium text-gray-800">-</span>
                    </div>
                    <div>
                        <span class="text-gray-600">Divisi: </span>
                        <span id="employeeDivisi" class="font-medium text-gray-800">-</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="rounded-xl bg-white p-4">
        <!-- Attendance History Section -->
        <div>
            <h2 class="mb-4 text-lg font-semibold text-gray-800">Riwayat Absensi</h2>
            <!-- Table Container -->
            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-sm">
                    <!-- Table Header -->
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="rounded-tl-lg px-4 py-3 font-bold">NO</th>
                            <th class="px-4 py-3 font-bold">Tanggal</th>
                            <th class="px-4 py-3 font-bold">Waktu</th>
                            <th class="px-4 py-3 font-bold">Metode Absen</th>
                            <th class="px-4 py-3 font-bold">Lokasi</th>
                            <th class="rounded-tr-lg px-4 py-3 font-bold">Keterangan</th>
                        </tr>
                    </thead>
                    <!-- Table Body -->
                    <tbody id="attendanceBody">
                        <tr>
                            <td colspan="6"
                                class="border-b-2 border-gray-200 bg-gray-50 px-4 py-6 text-center text-gray-500">
                                Loading attendance…
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script>
        (function() {
            const USER_ID = @json($id);
            const apiBase = `${window.location.origin}/api`;

            const els = {
                nameHeader: document.getElementById('employeeNameHeader'),
                name: document.getElementById('employeeName'),
                nip: document.getElementById('employeeNip'),
                email: document.getElementById('employeeEmail'),
                divisi: document.getElementById('employeeDivisi'),
                tbody: document.getElementById('attendanceBody'),
            };

            async function fetchDetail() {
                try {
                    const res = await fetch(`${apiBase}/user/${USER_ID}/absences`, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    if (!res.ok) throw new Error(`HTTP ${res.status}`);
                    const json = await res.json();

                    const user = json?.data?.user || {};
                    const history = Array.isArray(json?.data?.riwayat_absensi) ? json.data.riwayat_absensi : [];

                    // Fill header and profile
                    els.nameHeader.textContent = user.name || 'Tidak diketahui';
                    // Optional detail section (if present)
                    if (els.name) els.name.textContent = user.name || '-';
                    if (els.nip) els.nip.textContent = user.nip || '-';
                    if (els.email) els.email.textContent = user.email || '-';
                    if (els.divisi) els.divisi.textContent = user.divisi || '-';

                    // Fill table
                    if (!history.length) {
                        els.tbody.innerHTML =
                            `<tr><td colspan="6" class="border-b-2 border-gray-200 bg-gray-50 px-4 py-6 text-center text-gray-500">Belum ada riwayat</td></tr>`;
                    } else {
                        els.tbody.innerHTML = history.map((row, idx) => {
                            const ket = row?.keterangan ?? row?.status ?? '-';
                            const metode = row?.["metode-absen"] ?? '-';
                            return `
                                <tr class="hover:bg-gray-50">
                                    <td class="border-b-2 border-gray-200 bg-gray-50 px-4 py-3 text-center text-sm text-gray-800">${idx + 1}</td>
                                    <td class="border-b-2 border-gray-200 bg-gray-50 px-4 py-3 text-center text-sm text-gray-800">${row?.tanggal ?? '-'}</td>
                                    <td class="border-b-2 border-gray-200 bg-gray-50 px-4 py-3 text-center text-sm text-gray-800">${row?.waktu ?? '-'}</td>
                                    <td class="border-b-2 border-gray-200 bg-gray-50 px-4 py-3 text-center text-sm text-gray-800">${metode}</td>
                                    <td class="border-b-2 border-gray-200 bg-gray-50 px-4 py-3 text-center text-sm text-gray-800">${row?.lokasi ?? '-'}</td>
                                    <td class="border-b-2 border-gray-200 bg-gray-50 px-4 py-3 text-center text-sm text-gray-800">${ket}</td>
                                </tr>
                            `;
                        }).join('');
                    }
                } catch (err) {
                    els.tbody.innerHTML =
                        `<tr><td colspan="6" class="border-b-2 border-gray-200 bg-gray-50 px-4 py-6 text-center text-red-500">Gagal memuat data: ${err.message}</td></tr>`;
                }
            }

            fetchDetail();
            const interval = setInterval(fetchDetail, 30000);
            document.addEventListener('visibilitychange', () => {
                if (!document.hidden) fetchDetail();
            });
            window.addEventListener('beforeunload', () => clearInterval(interval));
        })();
    </script>
@endsection
