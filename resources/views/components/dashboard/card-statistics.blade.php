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
        <p class="mt-1 text-lg text-gray-600">Ringkasan data absensi hari ini</p>
    </div>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4" id="statisticsCards">
        <!-- Card 1 -->
        <div class="flex items-center justify-between rounded-xl bg-blue-500 p-6 text-white shadow-md">
            <div>
                <p class="text-sm font-medium">Total Karyawan</p>
                <h2 class="text-3xl font-bold" id="total-karyawan">-</h2>
            </div>
            <i class="fas fa-users text-4xl"></i>
        </div>

        <!-- Card 2 -->
        <div class="flex items-center justify-between rounded-xl bg-green-500 p-6 text-white shadow-md">
            <div>
                <p class="text-sm font-medium">Hadir Hari Ini</p>
                <h2 class="text-3xl font-bold" id="total-hadir">-</h2>
            </div>
            <i class="fas fa-user-check text-4xl"></i>
        </div>

        <!-- Card 3 -->
        <div class="flex items-center justify-between rounded-xl bg-red-500 p-6 text-white shadow-md">
            <div>
                <p class="text-sm font-medium">Terlambat Hari Ini</p>
                <h2 class="text-3xl font-bold" id="total-terlambat">-</h2>
            </div>
            <i class="fas fa-user-clock text-4xl"></i>
        </div>

        <!-- Card 4 -->
        <div class="flex items-center justify-between rounded-xl bg-yellow-500 p-6 text-white shadow-md">
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
            fetch('/api/dashboard-statistik')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    console.log(response);
                    return response.json();
                })
                .then(data => {
                    // Update the statistics cards with data from API
                    document.getElementById('total-karyawan').textContent = data.data.total_karyawan;
                    document.getElementById('total-hadir').textContent = data.data.total_hadir;
                    document.getElementById('total-terlambat').textContent = data.data.total_terlambat;
                    document.getElementById('total-tidak-hadir').textContent = data.data.total_tidak_hadir;
                })
                .catch(error => {
                    console.error('Error fetching daily statistics:', error);
                });
        }
    </script>
</body>

</html>
