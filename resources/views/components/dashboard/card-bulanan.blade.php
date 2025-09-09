<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Card Bulanan</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');
        body { font-family: 'Poppins', sans-serif; }
        .loader {
            border: 3px solid #f3f3f3;
            border-radius: 50%;
            border-top: 3px solid #3498db;
            width: 20px;
            height: 20px;
            animation: spin 1s linear infinite;
            display: inline-block;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .card-donut {
            border-radius: 15px;    
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .chart-container {
            position: relative;
            margin: 0 auto;
            width: 100%;
            max-width: 100%;
            height: 300px; /* Samakan dengan card tahunan */
        }
        .donut-center {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            width: 140px;
        }
        .percentage {
            font-size: 28px;
            font-weight: 700;
            color: #2D3748;
            line-height: 1.2;
        }
        .percentage-label {
            font-size: 14px;
            color: #718096;
        }
        .legend-item {
            display: flex;
            align-items: center;
            padding: 6px 12px;
            border-radius: 20px;
            background: rgba(247, 250, 252, 0.7);
            transition: background 0.2s ease;
        }
        .legend-item:hover {
            background: rgba(247, 250, 252, 1);
        }
        .color-badge {
            width: 16px;
            height: 16px;
            border-radius: 50%;
            margin-right: 8px;
        }
        .dropdown-select {
            background: #F7FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 12px;
            padding: 8px 12px;
            font-size: 14px;
            color: #4A5568;
            cursor: pointer;
            transition: all 0.2s ease;
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 8px center;
            background-size: 16px;
            padding-right: 32px;
        }
        .dropdown-select:hover {
            border-color: #CBD5E0;
        }
        .dropdown-select:focus {
            outline: none;
            border-color: #4299E1;
            box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.2);
        }
    </style>
</head>
<body class="font-poppins bg-gray-100 p-8 font-bold">
    <!-- Card Donat Bulanan -->
    <div class="card-donut w-full max-w-md" style="max-width: 100%;">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-gray-800">Chart Bulanan</h3>
                <div class="flex gap-2">
                    <select id="monthSelect" class="dropdown-select">
                        <option value="1">Jan</option>
                        <option value="2">Feb</option>
                        <option value="3">Mar</option>
                        <option value="4">Apr</option>
                        <option value="5">Mei</option>
                        <option value="6">Jun</option>
                        <option value="7">Jul</option>
                        <option value="8">Agu</option>
                        <option value="9">Sep</option>
                        <option value="10">Okt</option>
                        <option value="11">Nov</option>
                        <option value="12">Des</option>
                    </select>
                    <select id="yearSelect" class="dropdown-select">
                        <!-- Tahun akan diisi oleh JavaScript -->
                    </select>
                </div>
            </div>
            
            <div class="relative bg-gray-100 rounded-2xl p-4 mb-6">
                <div id="chartContainer" class="chart-container" style="width: 100%; max-width: 100%; height: 180px;">
                    <canvas id="monthlyDonutChart" class="h-72 w-full"></canvas>
                    <div class="donut-center">
                        <span class="percentage" id="donutPercentage">0%</span>
                        <div class="percentage-label">Kehadiran</div>
                    </div>
                </div>
                
                <div id="loadingIndicator" class="hidden absolute inset-0 flex flex-col items-center justify-center bg-gray-100 bg-opacity-90 rounded-2xl">
                    <div class="loader mb-3"></div>
                    <p class="text-gray-600 text-sm">Memuat data...</p>
                </div>
                
                <div id="errorMessage" class="hidden absolute inset-0 flex flex-col items-center justify-center bg-gray-100 bg-opacity-90 rounded-2xl p-4">
                    <p class="text-red-500 text-center mb-3">Terjadi kesalahan saat memuat data statistik.</p>
                    <button id="retryButton" class="px-4 py-2 bg-blue-500 text-white rounded-lg text-sm hover:bg-blue-600 transition-colors">
                        Coba Lagi
                    </button>
                </div>
            </div>
            
            <!-- Legend -->
            <div class="grid grid-cols-2 gap-3">
                <div class="legend-item">
                    <span class="color-badge bg-green-300"></span>
                    <span class="text-sm text-gray-700">Hadir</span>
                </div>
                <div class="legend-item">
                    <span class="color-badge bg-yellow-400"></span>
                    <span class="text-sm text-gray-700">Sakit</span>
                </div>
                <div class="legend-item">
                    <span class="color-badge bg-blue-400"></span>
                    <span class="text-sm text-gray-700">Izin</span>
                </div>
                <div class="legend-item">
                    <span class="color-badge bg-red-500"></span>
                    <span class="text-sm text-gray-700">Tanpa Keterangan</span>
                </div>
            </div>
        </div>
    </div>

    <script>
    let monthlyChart;

    document.addEventListener('DOMContentLoaded', function() {
        initializeYearDropdown();
        initializeMonthDropdown();
        initializeChart();
        loadMonthlyData();

        document.getElementById('monthSelect').addEventListener('change', loadMonthlyData);
        document.getElementById('yearSelect').addEventListener('change', loadMonthlyData);
        document.getElementById('retryButton').addEventListener('click', loadMonthlyData);
    });

    function initializeYearDropdown() {
        const yearSelect = document.getElementById('yearSelect');
        const currentYear = new Date().getFullYear();
        for (let year = currentYear - 5; year <= currentYear + 5; year++) {
            const option = document.createElement('option');
            option.value = year;
            option.textContent = year;
            yearSelect.appendChild(option);
            if (year === currentYear) option.selected = true;
        }
    }

    function initializeMonthDropdown() {
        const monthSelect = document.getElementById('monthSelect');
        const currentMonth = new Date().getMonth() + 1;
        monthSelect.value = currentMonth;
    }

    function initializeChart() {
        const monthlyCtx = document.getElementById('monthlyDonutChart').getContext('2d');
        monthlyChart = new Chart(monthlyCtx, {
            type: 'doughnut',
            data: {
                labels: ['Hadir', 'Sakit', 'Izin', 'Tanpa Keterangan'],
                datasets: [{
                    data: [0, 0, 0, 0],
                    backgroundColor: [
                        '#90EE90',
                        '#FFD700',
                        '#3498db',
                        '#FF6347'
                    ],
                    borderWidth: 0,
                    hoverOffset: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '75%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) label += ': ';
                                label += context.formattedValue + ' hari';
                                return label;
                            }
                        }
                    }
                }
            }
        });
    }

   async function loadMonthlyData() {
    showLoading();
    hideError();

    const monthNum = document.getElementById('monthSelect').value;
    const year = document.getElementById('yearSelect').value;

    // Mapping angka → nama bulan (harus lowercase sesuai controller)
    const monthMap = {
        1: "januari", 2: "februari", 3: "maret", 4: "april",
        5: "mei", 6: "juni", 7: "juli", 8: "agustus",
        9: "september", 10: "oktober", 11: "november", 12: "desember"
    };
    const monthName = monthMap[monthNum];

    try {
        const response = await fetch(`/api/statistik-bulanan?month=${monthName}&year=${year}`, {
            headers: {
                "Accept": "application/json",
                "Authorization": "Bearer " + (localStorage.getItem("token") ?? "")
            }
        });

        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

        const apiData = await response.json();
        console.log("API Response:", apiData);

        if (apiData.status === "success" && apiData.data) {
            updateChart(apiData.data);
            showChart();
        } else {
            throw new Error(apiData.message || "Gagal mengambil data");
        }
    } catch (error) {
        console.error("Error fetching monthly statistics:", error);
        showError();
        hideChart();
    }
}


    function updateChart(statsData) {
        const total = (statsData.hadir || 0) + (statsData.sakit || 0) + (statsData.izin || 0) + (statsData.tanpa_keterangan || 0);
        let chartData;
        if (total === 0) {
            chartData = [1, 0, 0, 0]; 
        } else {
            chartData = [
                statsData.hadir || 0,
                statsData.sakit || 0,
                statsData.izin || 0,
                statsData.tanpa_keterangan || 0
            ];
        }
        monthlyChart.data.datasets[0].data = chartData;
        monthlyChart.update();
        updateDonutPercentage(statsData);
    }

    function updateDonutPercentage(statsData) {
        const total = (statsData.hadir || 0) + (statsData.sakit || 0) + (statsData.izin || 0) + (statsData.tanpa_keterangan || 0);
        const percentage = total > 0 ? Math.round((statsData.hadir / total) * 100) : 0;
        document.getElementById('donutPercentage').textContent = `${percentage}%`;
    }

    function showLoading() {
        document.getElementById('loadingIndicator').classList.remove('hidden');
        document.getElementById('loadingIndicator').classList.add('flex');
        hideChart();
        hideError();
    }

    function hideLoading() {
        document.getElementById('loadingIndicator').classList.add('hidden');
        document.getElementById('loadingIndicator').classList.remove('flex');
    }

    function showChart() {
        document.getElementById('chartContainer').classList.remove('hidden');
        hideLoading();
        hideError();
    }

    function hideChart() {
        document.getElementById('chartContainer').classList.add('hidden');
    }

    function showError() {
        document.getElementById('errorMessage').classList.remove('hidden');
        hideLoading();
        hideChart();
    }

    function hideError() {
        document.getElementById('errorMessage').classList.add('hidden');
    }
</script>

</body>
</html>