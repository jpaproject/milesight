<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <style>
            .filter-container {
                background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
                border-radius: 12px;
                padding: 20px;
                margin-bottom: 20px;
                border: 1px solid #e2e8f0;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            }

            .dark .filter-container {
                background: linear-gradient(135deg, rgba(255, 255, 255, 0.02) 0%, rgba(255, 255, 255, 0.05) 100%);
                border-color: #374151;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
            }

            .filter-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 16px;
                align-items: end;
            }

            .filter-group label {
                display: flex;
                align-items: center;
                font-size: 0.875rem;
                font-weight: 500;
                color: #374151;
                margin-bottom: 6px;
            }

            .dark .filter-group label {
                color: #d1d5db;
            }

            .filter-group select,
            .filter-group input {
                width: 100%;
                padding: 10px 12px;
                border: 1px solid #d1d5db;
                border-radius: 8px;
                font-size: 0.875rem;
                background: white;
                transition: all 0.2s ease;
            }

            .dark .filter-group select,
            .dark .filter-group input {
                background: rgba(255, 255, 255, 0.05);
                border-color: #4b5563;
                color: #f3f4f6;
            }

            .filter-group select:focus,
            .filter-group input:focus {
                outline: none;
                border-color: #3b82f6;
                box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
                transform: translateY(-1px);
            }

            .filter-actions button {
                padding: 10px 16px;
                border-radius: 8px;
                font-size: 0.875rem;
                font-weight: 500;
                transition: all 0.2s ease;
                cursor: pointer;
                display: inline-flex;
                align-items: center;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            }

            .filter-actions button:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            }

            .btn-apply {
                background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
                color: white;
                border: none;
            }

            .btn-apply:hover {
                background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            }

            #filterStatus {
                padding: 8px 12px;
                background: rgba(59, 130, 246, 0.1);
                border: 1px solid rgba(59, 130, 246, 0.2);
                border-radius: 6px;
                font-size: 0.8rem;
                color: #1e40af;
                animation: slideIn 0.3s ease-out;
            }

            .dark #filterStatus {
                background: rgba(59, 130, 246, 0.2);
                border-color: rgba(59, 130, 246, 0.3);
                color: #93c5fd;
            }

            @keyframes slideIn {
                from {
                    opacity: 0;
                    transform: translateY(-10px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
        </style>
    @endpush

    <div x-data="{ pageName: `Trending` }">
        <x-breadcrumb />
    </div>

    <div class="filter-container">
        <div class="flex items-center justify-between mb-4">
            <h4 class="text-lg font-medium text-gray-900 dark:text-white">Filter Data</h4>
            <div class="text-sm text-gray-500 dark:text-gray-400">
                <span>Total: {{ $totalRecords }} records</span>
                @if ($showingToday ?? false)
                    <span
                        class="ml-2 px-2 py-1 bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 rounded-full text-xs">
                        📅 Today's Data Only
                    </span>
                @endif
            </div>
        </div>

        <form id="filterForm" method="GET" action="{{ route('trending.index') }}">
            <div class="filter-grid">
                <div class="filter-group">
                    <label for="metric">
                        <i class="fas fa-chart-line mr-1"></i>Parameter
                    </label>
                    <select id="metric" name="metric">
                        <option value="temperature" {{ $metric === 'temperature' ? 'selected' : '' }}>Temperature</option>
                        <option value="battery" {{ $metric === 'battery' ? 'selected' : '' }}>Battery</option>
                        <option value="humidity" {{ $metric === 'humidity' ? 'selected' : '' }}>Humidity</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="startDate">
                        <i class="fas fa-calendar-alt mr-1"></i>Start Date
                    </label>
                    <input type="date" id="startDate" name="start_date" class="datepicker" value="{{ $startDate }}"
                        placeholder="Select start date">
                </div>

                <div class="filter-group">
                    <label for="endDate">
                        <i class="fas fa-calendar-alt mr-1"></i>End Date
                    </label>
                    <input type="date" id="endDate" name="end_date" class="datepicker" value="{{ $endDate }}"
                        placeholder="Select end date">
                </div>

                <div class="filter-group filter-actions">
                    <div class="flex gap-2">
                        <button type="submit" class="btn-apply" title="Apply filters">
                            <i class="fas fa-filter mr-1"></i>Apply Filter
                        </button>
                        <a href="{{ route('trending.index') }}"
                            class="btn bg-green-500 hover:bg-green-600 text-white px-3 py-2 rounded text-sm inline-flex items-center"
                            title="Show today's data">
                            <i class="fas fa-list mr-1"></i>Today's Data
                        </a>
                    </div>
                </div>
            </div>
        </form>

        <div id="filterStatus" class="mt-3 text-sm text-blue-600 dark:text-blue-400" style="display: none;"></div>
    </div>

    <div
        class="mt-6 rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] overflow-hidden shadow-md">
        <div class="px-6 py-4 mb-2">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Trending - {{ $metricLabel }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Trend per device untuk parameter {{ strtolower($metricLabel) }}
                    </p>
                </div>
            </div>
        </div>
        <div class="px-6 pb-6">
            <div id="trendingChart" class="w-full"></div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script>
            const metric = @json($metric);
            const series = @json($series);

            const ySuffix = metric === 'temperature' ? '°C' : (metric === 'humidity' ? '%' : '%');

            const chartOptions = {
                chart: {
                    type: 'line',
                    height: 420,
                    toolbar: {
                        show: true
                    },
                    zoom: {
                        enabled: true
                    }
                },
                series,
                stroke: {
                    curve: 'smooth',
                    width: 2
                },
                markers: {
                    size: 0
                },
                xaxis: {
                    type: 'datetime',
                    labels: {
                        datetimeUTC: false
                    }
                },
                yaxis: {
                    labels: {
                        formatter: function(value) {
                            if (metric === 'battery') return Math.round(value) + ySuffix;
                            if (metric === 'humidity') return Math.round(value) + ySuffix;
                            return value.toFixed(1) + ySuffix;
                        }
                    }
                },
                tooltip: {
                    x: {
                        format: 'dd/MM/yyyy HH:mm:ss'
                    },
                    y: {
                        formatter: function(value) {
                            if (metric === 'battery') return Math.round(value) + ySuffix;
                            if (metric === 'humidity') return Math.round(value) + ySuffix;
                            return value.toFixed(1) + ySuffix;
                        }
                    }
                },
                legend: {
                    position: 'top'
                },
                colors: ['#2563eb', '#f59e0b', '#10b981', '#ef4444', '#8b5cf6']
            };

            const chartEl = document.querySelector('#trendingChart');
            if (chartEl) {
                const chart = new ApexCharts(chartEl, chartOptions);
                chart.render();
            }

            function updateFilterStatus() {
                const activeFilters = [];
                const metricLabel = document.querySelector('#metric option:checked')?.textContent;
                const startDate = document.querySelector('#startDate').value;
                const endDate = document.querySelector('#endDate').value;

                if (metricLabel) activeFilters.push('Parameter: ' + metricLabel);
                if (startDate) activeFilters.push('From: ' + startDate);
                if (endDate) activeFilters.push('To: ' + endDate);

                const statusElement = document.getElementById('filterStatus');
                if (activeFilters.length > 0) {
                    statusElement.innerHTML = '<i class="fas fa-filter mr-1"></i>Active filters: ' + activeFilters.join(', ');
                    statusElement.style.display = 'block';
                } else {
                    statusElement.style.display = 'none';
                }
            }

            updateFilterStatus();

            flatpickr(".datepicker", {
                dateFormat: "Y-m-d",
                allowInput: true
            });
        </script>
    @endpush
</x-app-layout>
