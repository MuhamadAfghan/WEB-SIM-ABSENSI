<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Dashboard Absensi Karyawan</h1>
        <p class="text-lg text-gray-600 mt-1">Ringkasan data absensi hari ini</p>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4" id="statisticsCards">
    <!-- Card 1 -->
    <div class="rounded-xl bg-blue-500 text-white p-6 shadow-md flex items-center justify-between">
        <div>
            <p class="text-sm font-medium">Total Karyawan</p>
            <h2 class="text-3xl font-bold" id="total-karyawan">-</h2>
        </div>
        <i class="fas fa-users text-4xl"></i>
    </div>

    <!-- Card 2 -->
    <div class="rounded-xl bg-green-500 text-white p-6 shadow-md flex items-center justify-between">
        <div>
            <p class="text-sm font-medium">Hadir Hari Ini</p>
            <h2 class="text-3xl font-bold" id="total-hadir">-</h2>
        </div>
        <i class="fas fa-user-check text-4xl"></i>
    </div>

    <!-- Card 3 -->
    <div class="rounded-xl bg-red-500 text-white p-6 shadow-md flex items-center justify-between">
        <div>
            <p class="text-sm font-medium">Terlambat Hari Ini</p>
            <h2 class="text-3xl font-bold" id="total-terlambat">-</h2>
        </div>
        <i class="fas fa-user-clock text-4xl"></i>
    </div>

    <!-- Card 4 -->
    <div class="rounded-xl bg-yellow-500 text-white p-6 shadow-md flex items-center justify-between">
        <div>
            <p class="text-sm font-medium">Tidak Hadir</p>
            <h2 class="text-3xl font-bold" id="total-tidak-hadir">-</h2>
        </div>
        <i class="fas fa-user-times text-4xl"></i>
    </div>
</div>

<script>
    // Fetch data from API when the page loads
    document.addEventListener('DOMContentLoaded', function() {
        fetchDailyStatistics();
    });

    function fetchDailyStatistics() {
        fetch('/api/jumlah-harian')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                // Update the statistics cards with data from API
                document.getElementById('total-karyawan').textContent = data.total_karyawan;
                document.getElementById('total-hadir').textContent = data.total_hadir;
                document.getElementById('total-terlambat').textContent = data.total_terlambat;
                document.getElementById('total-tidak-hadir').textContent = data.total_tidak_hadir;
            })
            .catch(error => {
                console.error('Error fetching daily statistics:', error);
            });
    }
</script>
</body>
</html>