<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>

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
</style>

<body>
    <h1 class="text-2xl font-black tracking-wide">Data Absensi Terbaru</h1>
    <div class="overflow-x-auto mt-3">
        <table class="w-full text-left text-sm" id="attendanceTable">
            <thead class="bg-white">
                <tr>
                    <th class="px-4 py-3 font-bold rounded-tl-lg border-b border-gray-200">NO</th>
                    <th class="px-4 py-3 font-bold border-b border-gray-200">Nama</th>
                    <th class="px-4 py-3 font-bold border-b border-gray-200">Keterangan</th>
                    <th class="px-4 py-3 font-bold border-b border-gray-200">Waktu Kedatangan</th>
                    <th class="px-4 py-3 font-bold border-b border-gray-200">Waktu Kepulangan</th>
                    <th class="px-4 py-3 font-bold rounded-tr-lg border-b border-gray-200">Metode Absen</th>
                </tr>
            </thead>
            <tbody id="attendanceBody" class="divide-y border-gray-200 bg-gray-100">
                <!-- Data akan diisi oleh JavaScript -->
            </tbody>
        </table>
    </div>

    <script src="//unpkg.com/alpinejs" defer></script>
    <script>
        // Variabel global untuk menyimpan data
        let allAttendanceData = [];

        // Fungsi untuk memuat data absensi
        async function fetchAttendanceData() {
            showLoading();
            try {
                const response = await fetch("/api/absences/today");
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
                    // Ambil data dari result.data.items
                    allAttendanceData = Array.isArray(result.data.items) ? result.data.items : [];
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
            let currentData = [...allAttendanceData];

            // Ambil hanya 5 data terbaru
            currentData = currentData.slice(0, 5);

            tableBody.innerHTML = '';
            if (currentData.length === 0) {
                tableBody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center py-4">Tidak ada data absensi</td>
            </tr>
        `;
            } else {
                currentData.forEach((item, index) => {
                    const rowNumber = index + 1;
                    const userName = item.user && item.user.name ? item.user.name : '-';
                    const keterangan = item.keterangan || '-';
                    const arrivalTime = item.check_in_time || '--:--';
                    const departureTime = item.check_out_time || '--:--';
                    const tipe = item.type || '-';

                    tableBody.innerHTML += `
                <tr>
                    <td class="px-4 py-1.5">${rowNumber}</td>
                    <td class="px-4 py-1.5">${userName}</td>
                    <td class="px-4 py-1.5">${keterangan}</td>
                    <td class="px-4 py-1.5">${arrivalTime}</td>
                    <td class="px-4 py-1.5">${departureTime}</td>
                    <td class="px-4 py-1.5">${tipe}</td>
                </tr>
            `;
                });
            }
        }

        // Fungsi untuk menampilkan loading
        function showLoading() {
            const tableBody = document.getElementById('attendanceBody');
            tableBody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center py-4">
                    <div class="loading-spinner"></div>
                    <p class="text-gray-500">Memuat data absensi...</p>
                </td>
            </tr>
        `;
        }

        // Fungsi untuk menampilkan error
        function showError(message) {
            const tableBody = document.getElementById('attendanceBody');
            tableBody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center py-4 text-red-500">
                    <strong>Error:</strong> ${message}
                </td>
            </tr>
        `;
        }

        // Inisialisasi ketika halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            fetchAttendanceData();
        });
    </script>
</body>

</html>
