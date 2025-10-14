<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Monitoring Dashboard Overview') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <!-- SECTION T1A -->
            <div>
                <h3 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-4">Grafik Terminal 1A</h3>
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h4 class="text-lg font-semibold text-gray-700 dark:text-gray-300 text-center mb-4">Status Perangkat T1A</h4>
                        <div class="relative h-72">
                            <canvas id="statusChartT1A"></canvas>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h4 class="text-lg font-semibold text-gray-700 dark:text-gray-300 text-center mb-4">Suhu T1A (°C)</h4>
                        <div class="relative h-72">
                            <canvas id="temperatureChartT1A"></canvas>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h4 class="text-lg font-semibold text-gray-700 dark:text-gray-300 text-center mb-4">Kelembapan T1A (%)</h4>
                        <div class="relative h-72">
                            <canvas id="humidityChartT1A"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION T1B -->
            <div>
                <h3 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-4 mt-6">Grafik Terminal 1B</h3>
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h4 class="text-lg font-semibold text-gray-700 dark:text-gray-300 text-center mb-4">Status Perangkat T1B</h4>
                        <div class="relative h-72">
                            <canvas id="statusChartT1B"></canvas>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h4 class="text-lg font-semibold text-gray-700 dark:text-gray-300 text-center mb-4">Suhu T1B (°C)</h4>
                        <div class="relative h-72">
                            <canvas id="temperatureChartT1B"></canvas>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h4 class="text-lg font-semibold text-gray-700 dark:text-gray-300 text-center mb-4">Kelembapan T1B (%)</h4>
                        <div class="relative h-72">
                            <canvas id="humidityChartT1B"></canvas>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.socket.io/4.7.4/socket.io.min.js"></script>

    <script>
        let statusDoughnutChartT1A, temperatureChartT1A, humidityChartT1A;
        let statusDoughnutChartT1B, temperatureChartT1B, humidityChartT1B;
        
        // Fungsi untuk status chart (tidak berubah)
        function processStatusForChart(payload, terminalPrefix) {
            const finalResult = { labels: [], data: [], colors: [] };
            const definedCategories = [
                { key: 'cc_atas_online', label: 'CC Atas Online', color: '#10B981' }, { key: 'cc_atas_offline', label: 'CC Atas Offline', color: '#F87171' },
                { key: 'cc_bawah_online', label: 'CC Bawah Online', color: '#34D399' }, { key: 'cc_bawah_offline', label: 'CC Bawah Offline', color: '#FCA5A5' },
                { key: 'transit_online', label: 'Transit Online', color: '#6EE7B7' }, { key: 'transit_offline', label: 'Transit Offline', color: '#FECACA' },
            ];
            for (const category of definedCategories) {
                const fullKey = `${terminalPrefix}_${category.key}`;
                const value = payload.status[fullKey] ?? 0;
                finalResult.labels.push(category.label);
                finalResult.data.push(value);
                finalResult.colors.push(category.color);
            }
            return finalResult;
        }

        // --- REVISI: Fungsi untuk memproses data stacked bar ---
        function processStackedData(payload, terminalPrefix) {
            const labels = [], tempMins = [], tempAvgDiffs = [], tempMaxDiffs = [], humMins = [], humAvgDiffs = [], humMaxDiffs = [];
            const locations = ['cc_atas', 'cc_bawah', 'transit'];

            locations.forEach(loc => {
                const fullKey = `${terminalPrefix}_${loc}`;
                const summaryItem = payload.summary.find(s => s.location === fullKey);
                
                labels.push(loc.replace('_', ' ').toUpperCase());

                if (summaryItem) {
                    // Suhu
                    tempMins.push(summaryItem.temperature.min);
                    tempAvgDiffs.push(summaryItem.temperature.avg);
                    tempMaxDiffs.push(summaryItem.temperature.max);
                    // Kelembapan
                    humMins.push(summaryItem.humidity.min);
                    humAvgDiffs.push(summaryItem.humidity.avg);
                    humMaxDiffs.push(summaryItem.humidity.max);
                } else {
                    [tempMins, tempAvgDiffs, tempMaxDiffs, humMins, humAvgDiffs, humMaxDiffs].forEach(arr => arr.push(null));
                }
            });
            return { labels, tempMins, tempAvgDiffs, tempMaxDiffs, humMins, humAvgDiffs, humMaxDiffs };
        }

        function updateCharts(payload) {
            if (!payload || !payload.status || !payload.summary) return;

            // Proses data status chart (tidak berubah)
            const statusDataT1A = processStatusForChart(payload, 't1a');
            statusDoughnutChartT1A.data.labels = statusDataT1A.labels;
            statusDoughnutChartT1A.data.datasets[0].data = statusDataT1A.data;
            statusDoughnutChartT1A.data.datasets[0].backgroundColor = statusDataT1A.colors;
            const statusDataT1B = processStatusForChart(payload, 't1b');
            statusDoughnutChartT1B.data.labels = statusDataT1B.labels;
            statusDoughnutChartT1B.data.datasets[0].data = statusDataT1B.data;
            statusDoughnutChartT1B.data.datasets[0].backgroundColor = statusDataT1B.colors;

            // --- REVISI: Update chart suhu dan kelembapan dengan data stacked ---
            const stackedDataT1A = processStackedData(payload, 't1a');
            temperatureChartT1A.data.labels = stackedDataT1A.labels;
            temperatureChartT1A.data.datasets[0].data = stackedDataT1A.tempMins;
            temperatureChartT1A.data.datasets[1].data = stackedDataT1A.tempAvgDiffs;
            temperatureChartT1A.data.datasets[2].data = stackedDataT1A.tempMaxDiffs;
            humidityChartT1A.data.labels = stackedDataT1A.labels;
            humidityChartT1A.data.datasets[0].data = stackedDataT1A.humMins;
            humidityChartT1A.data.datasets[1].data = stackedDataT1A.humAvgDiffs;
            humidityChartT1A.data.datasets[2].data = stackedDataT1A.humMaxDiffs;
            
            const stackedDataT1B = processStackedData(payload, 't1b');
            temperatureChartT1B.data.labels = stackedDataT1B.labels;
            temperatureChartT1B.data.datasets[0].data = stackedDataT1B.tempMins;
            temperatureChartT1B.data.datasets[1].data = stackedDataT1B.tempAvgDiffs;
            temperatureChartT1B.data.datasets[2].data = stackedDataT1B.tempMaxDiffs;
            humidityChartT1B.data.labels = stackedDataT1B.labels;
            humidityChartT1B.data.datasets[0].data = stackedDataT1B.humMins;
            humidityChartT1B.data.datasets[1].data = stackedDataT1B.humAvgDiffs;
            humidityChartT1B.data.datasets[2].data = stackedDataT1B.humMaxDiffs;
            
            // Update Semua Chart
            statusDoughnutChartT1A.update(); statusDoughnutChartT1B.update();
            temperatureChartT1A.update(); humidityChartT1A.update();
            temperatureChartT1B.update(); humidityChartT1B.update();
        }
        
        function initializeCharts() {
            Chart.defaults.color = document.documentElement.classList.contains('dark') ? '#cbd5e1' : '#6b7280';
            const chartBorderColor = document.documentElement.classList.contains('dark') ? '#1f2937' : '#fff';

            // Inisialisasi chart status (tidak berubah)
            const doughnutOptions = { responsive: true, maintainAspectRatio: false, plugins: { legend: { labels: { filter: null } } } };
            statusDoughnutChartT1A = new Chart(document.getElementById('statusChartT1A').getContext('2d'), { type: 'doughnut', data: { labels: [], datasets: [{ data: [], borderColor: chartBorderColor, borderWidth: 2 }] }, options: doughnutOptions });
            statusDoughnutChartT1B = new Chart(document.getElementById('statusChartT1B').getContext('2d'), { type: 'doughnut', data: { labels: [], datasets: [{ data: [], borderColor: chartBorderColor, borderWidth: 2 }] }, options: doughnutOptions });

            // --- REVISI: Inisialisasi Chart Suhu & Kelembapan menjadi Stacked Bar ---
            const stackedBarOptions = {
                responsive: true, maintainAspectRatio: false,
                scales: { 
                    x: { stacked: true },
                    y: { stacked: false, beginAtZero: false, ticks: { callback: function(value) { return value + (this.chart.canvas.id.includes('temperature') ? '°C' : '%'); } } }
                },
                plugins: { 
                    tooltip: { 
                        mode: 'index',
                        itemSort: function(a, b) { return b.datasetIndex - a.datasetIndex; },
			callbacks: {
                	   label: function(context) {
                              const val = context.raw;
                              if (context.chart.canvas.id.includes('temperature')) {
                                 return context.dataset.label + ': ' + val.toFixed(1) + '°C'; // 1 angka di belakang koma
                              } else {
                                 return context.dataset.label + ': ' + Math.round(val) + '%'; // bulat untuk humidity
                              }
                           }
                        }
                    }
                }
            };

            // Chart T1A
            temperatureChartT1A = new Chart(document.getElementById('temperatureChartT1A').getContext('2d'), {
                type: 'bar',
                data: { labels: [], datasets: [
                    { label: 'Min', data: [], backgroundColor: '#FDBA74' }, // Orange-300
                    { label: 'Avg', data: [], backgroundColor: '#F59E0B' }, // Orange-500
                    { label: 'Max', data: [], backgroundColor: '#D97706' }, // Orange-700
                ]},
                options: stackedBarOptions
            });
            humidityChartT1A = new Chart(document.getElementById('humidityChartT1A').getContext('2d'), {
                type: 'bar',
                data: { labels: [], datasets: [
                    { label: 'Min', data: [], backgroundColor: '#93C5FD' }, // Blue-300
                    { label: 'Avg', data: [], backgroundColor: '#3B82F6' }, // Blue-500
                    { label: 'Max', data: [], backgroundColor: '#1D4ED8' }, // Blue-700
                ]},
                options: stackedBarOptions
            });

            // Chart T1B
            temperatureChartT1B = new Chart(document.getElementById('temperatureChartT1B').getContext('2d'), { type: 'bar', data: { labels: [], datasets: [ { label: 'Min', data: [], backgroundColor: '#FDBA74' }, { label: 'Avg', data: [], backgroundColor: '#F59E0B' }, { label: 'Max', data: [], backgroundColor: '#D97706' }, ]}, options: stackedBarOptions });
            humidityChartT1B = new Chart(document.getElementById('humidityChartT1B').getContext('2d'), { type: 'bar', data: { labels: [], datasets: [ { label: 'Min', data: [], backgroundColor: '#93C5FD' }, { label: 'Avg', data: [], backgroundColor: '#3B82F6' }, { label: 'Max', data: [], backgroundColor: '#1D4ED8' }, ]}, options: stackedBarOptions });
        }

        document.addEventListener('DOMContentLoaded', () => {
            initializeCharts();
            const socket = io("{{ env('WEBSOCKET_SERVER_URL', 'http://localhost:3000') }}");
            socket.on('connect', () => console.log('✅ Terhubung ke WebSocket server!'));
            socket.on('dashboard_overview_update', (payload) => {
                console.log('📊 Menerima update overview dashboard:', payload);
                updateCharts(payload);
            });
            socket.on('disconnect', () => console.log('Koneksi WebSocket terputus.'));
            socket.on('connect_error', (err) => console.error('Gagal terhubung ke WebSocket:', err.message));
        });
    </script>
    @endpush
</x-app-layout>
