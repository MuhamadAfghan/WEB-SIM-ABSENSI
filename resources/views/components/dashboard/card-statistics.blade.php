<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Dashboard Absensi Karyawan</h1>
        <p class="text-lg text-gray-600 mt-1">Ringkasan data absensi hari ini</p>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <!-- Card 1 -->
    <div class="rounded-xl bg-blue-500 text-white p-6 shadow-md flex items-center justify-between">
        <div>
            <p class="text-sm font-medium">Total Karyawan</p>
            <h2 class="text-3xl font-bold">45</h2>
        </div>
        <i class="fas fa-users text-4xl"></i>
    </div>

    <!-- Card 2 -->
    <div class="rounded-xl bg-green-500 text-white p-6 shadow-md flex items-center justify-between">
        <div>
            <p class="text-sm font-medium">Hadir Hari Ini</p>
            <h2 class="text-3xl font-bold">38</h2>
        </div>
        <i class="fas fa-user-check text-4xl"></i>
    </div>

    <!-- Card 3 -->
    <div class="rounded-xl bg-red-500 text-white p-6 shadow-md flex items-center justify-between">
        <div>
            <p class="text-sm font-medium">Terlambat Hari Ini</p>
            <h2 class="text-3xl font-bold">5</h2>
        </div>
        <i class="fas fa-user-clock text-4xl"></i>
    </div>

    <!-- Card 4 -->
    <div class="rounded-xl bg-yellow-500 text-white p-6 shadow-md flex items-center justify-between">
        <div>
            <p class="text-sm font-medium">Tidak Hadir</p>
            <h2 class="text-3xl font-bold">2</h2>
        </div>
        <i class="fas fa-user-times text-4xl"></i>
    </div>
</div>

</body>
</html>