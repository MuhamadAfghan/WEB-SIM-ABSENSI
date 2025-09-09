<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Chart Tahun 2025</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @vite('resources/css/app.css')
</head>

<body class="font-poppins bg-gray-100 p-8 font-bold">

    <div class="w-full rounded-xl bg-white p-6 shadow-md">
        <h3 class="mb-4 text-xl font-bold">Chart Tahun 2025</h3>
        <div class="w-full rounded-xl p-6" style="background-color: #EEEEEE; height: 288px;">
    <canvas id="myChart" class="h-full w-full"></canvas>
</div>
        <!-- legend -->
        <div class="mt-5 flex justify-center gap-5 text-sm" id="customLegend">
            <div class="flex items-center gap-2">
                <span class="h-4 w-4 rounded-full" style="background-color: #90EE90"></span>
                <span>Hadir</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="h-4 w-4 rounded-full" style="background-color: #FF6347"></span>
                <span>Tidak Hadir</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="h-4 w-4 rounded-full" style="background-color: #F6995C"></span>
                <span>Terlambat</span>
            </div>
        </div>
    </div>

    <div>
        <script>
            const ctx = document.getElementById('myChart').getContext('2d');

            fetch("/api/statistik-tahunan?year=2025")
                .then(res => res.json())
                .then(res => {
                    console.log("Data dari API:", res);
                    // ambil array bulan
                    const bulan = res.data.data_bulanan.map(item => item.bulan);

                    // mapping ke dataset sesuai requestmu
                    const hadir = res.data.data_bulanan.map(item => item.total_hadir);
                    const tidakHadir = res.data.data_bulanan.map(item => item.total_tidak_hadir);
                    const terlambat = res.data.data_bulanan.map(item => item.total_terlambat);

                    const myChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: bulan,
                            datasets: [{
                                    label: 'Hadir',
                                    data: hadir,
                                    backgroundColor: '#90EE90',
                                    borderRadius: 3,
                                    barPercentage: 1,
                                    categoryPercentage: 0.9
                                },
                                {
                                    label: 'Tidak Hadir',
                                    data: tidakHadir,
                                    backgroundColor: '#FF6347',
                                    borderRadius: 3,
                                    barPercentage: 1,
                                    categoryPercentage: 0.9
                                },
                                {
                                    label: 'Terlambat',
                                    data: terlambat,
                                    backgroundColor: '#F6995C',
                                    borderRadius: 3,
                                    barPercentage: 1,
                                    categoryPercentage: 0.9
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    enabled: true
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        font: {
                                            weight: 'bold'
                                        }
                                    }
                                },
                                x: {
                                    offset: true,
                                    ticks: {
                                        font: {
                                            weight: 'bold'
                                        },
                                        grid: {
                                            drawTicks: true
                                        },
                                    }
                                },
                            },
                            interaction: {
                                mode: 'index',
                                intersect: false
                            }
                        }
                    });
                })
                .catch(err => console.error("Gagal ambil data:", err));
        </script>
    </div>
</body>

</html>
