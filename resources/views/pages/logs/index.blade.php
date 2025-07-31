<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <style>
            /* Enhanced Filter Styles */
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

            /* DataTable Search Enhancement */
            .dataTables_filter {
                position: relative;
                display: flex;
                align-items: center;
            }

            .dataTables_filter input {
                padding-left: 35px !important;
                border-radius: 8px !important;
                border: 1px solid #d1d5db !important;
                transition: all 0.2s ease !important;
            }

            .dataTables_filter input:focus {
                border-color: #3b82f6 !important;
                box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
            }

            .clear-search {
                background: #f3f4f6 !important;
                border: 1px solid #d1d5db !important;
                border-radius: 6px !important;
                padding: 8px 10px !important;
                font-size: 12px !important;
                transition: all 0.2s ease !important;
                cursor: pointer !important;
            }

            .clear-search:hover {
                background: #e5e7eb !important;
                transform: scale(1.05);
            }

            /* Filter Status Styling */
            #filterStatus {
                padding: 8px 12px;
                background: rgba(59, 130, 246, 0.1);
                border: 1px solid rgba(59, 130, 246, 0.2);
                border-radius: 6px;
                font-size: 0.8rem;
                color: #1e40af;
            }

            .dark #filterStatus {
                background: rgba(59, 130, 246, 0.2);
                border-color: rgba(59, 130, 246, 0.3);
                color: #93c5fd;
            }

            /* Table Row Hover Effects */
            #logsTable tbody tr:hover {
                background: linear-gradient(90deg, #f8fafc 0%, #f1f5f9 100%) !important;
                transform: scale(1.002);
                transition: all 0.2s ease;
            }

            .dark #logsTable tbody tr:hover {
                background: linear-gradient(90deg, rgba(255, 255, 255, 0.02) 0%, rgba(255, 255, 255, 0.05) 100%) !important;
            }

            /* Responsive Adjustments */
            @media (max-width: 768px) {
                .filter-grid {
                    grid-template-columns: 1fr;
                }

                .filter-actions {
                    margin-top: 16px;
                }

                .filter-actions .flex {
                    flex-direction: column;
                    gap: 8px;
                }

                .filter-actions button {
                    width: 100%;
                    justify-content: center;
                }
            }

            /* Animation for filter status */
            #filterStatus {
                animation: slideIn 0.3s ease-out;
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

            /* Battery indicator styling */
            .battery-indicator {
                position: relative;
                overflow: hidden;
            }
        </style>
    @endpush

    <div x-data="{ pageName: `Logs` }">
        <x-breadcrumb />
    </div>

    <!-- Backend Filter Section -->
    <div class="filter-container">
        <div class="flex items-center justify-between mb-4">
            <h4 class="text-lg font-medium text-gray-900 dark:text-white">Filter Data</h4>
            <div class="text-sm text-gray-500 dark:text-gray-400">
                <span id="totalRecords">Total: {{ count($logs) }} records</span>
                @if ($showingToday ?? false)
                    <span
                        class="ml-2 px-2 py-1 bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 rounded-full text-xs">
                        📅 Today's Data Only
                    </span>
                @endif
            </div>
        </div>

        <form id="filterForm" method="GET" action="{{ route('logs.index') }}">
            <div class="filter-grid">
                <div class="filter-group">
                    <label for="areaFilter">
                        <i class="fas fa-map-marker-alt mr-1"></i>Area
                    </label>
                    <select id="areaFilter" name="area_id">
                        <option value="all">All Areas</option>
                        @foreach ($areas as $area)
                            <option value="{{ $area->name }}"
                                {{ request('area_id') == $area->name ? 'selected' : '' }}>
                                {{ $area->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-group">
                    <label for="startDate">
                        <i class="fas fa-calendar-alt mr-1"></i>Start Date
                    </label>
                    <input type="date" id="startDate" name="start_date" class="datepicker"
                        value="{{ request('start_date') }}" placeholder="Select start date">
                </div>

                <div class="filter-group">
                    <label for="endDate">
                        <i class="fas fa-calendar-alt mr-1"></i>End Date
                    </label>
                    <input type="date" id="endDate" name="end_date" class="datepicker"
                        value="{{ request('end_date') }}" placeholder="Select end date">
                </div>

                <div class="filter-group filter-actions">
                    <div class="flex gap-2">
                        <button type="button" id="applyFilter" class="btn-apply" title="Apply filters">
                            <i class="fas fa-filter mr-1"></i>Apply Filter
                        </button>
                        <a href="{{ route('logs.index') }}"
                            class="btn bg-green-500 hover:bg-green-600 text-white px-3 py-2 rounded text-sm inline-flex items-center"
                            title="Show all data">
                            <i class="fas fa-list mr-1"></i>Today's Data
                        </a>
                    </div>
                </div>
            </div>
        </form>

        <!-- Filter Status Display -->
        <div id="filterStatus" class="mt-3 text-sm text-blue-600 dark:text-blue-400" style="display: none;"></div>

        <!-- Info Section -->
        <div class="mt-3 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
            <div class="flex items-start">
                <i class="fas fa-info-circle text-blue-500 mr-2 mt-0.5"></i>
                <div class="text-sm text-blue-700 dark:text-blue-300">
                    <strong>Filter Guide:</strong>
                    <ul class="mt-1 space-y-1">
                        <li>• <strong>Backend Filters</strong> (Area, Date Range): Filters data from server - affects
                            what data is loaded</li>
                        <li>• <strong>Search Box</strong>: Searches within currently loaded data only</li>
                        <li>• Default view shows today's data only. Use filters to see other dates</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Header Section -->
    <div
        class="mt-6 rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] overflow-hidden shadow-md">
        <div class="px-6 py-4 mb-5">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Device Logs</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Monitor and track device sensor readings
                        <span class="ml-2 text-xs bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">
                            💡 Use search box to find within loaded data
                        </span>
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <button onclick="refreshTable()"
                        class="inline-flex items-center px-3 py-2 border border-transparent shadow-sm text-sm leading-4 font-medium rounded-md text-white bg-yellow-500 hover:bg-yellow-600 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Refresh
                    </button>
                </div>
            </div>
        </div>

        <!-- Loading Overlay Container -->
        <div class="relative">
            <div class="overflow-x-auto px-6 py-4">
                <table id="logsTable" class="min-w-full border dark:border-gray-600 rounded">
                    <thead class="bg-gray-200 dark:bg-gray-800">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-12">
                                No</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-28">
                                Area Name</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Device Name</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider whitespace-normal">
                                Battery</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Temperature</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Humidity</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Received at</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-transparent">
                        @foreach ($logs as $index => $log)
                            <tr class="hover:bg-gray-50 transition-colors" data-area="{{ $log->area->name }}"
                                data-device="{{ $log->device->name }}">
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center dark:text-white">
                                    {{ $index + 1 }}
                                </td>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-center dark:text-white">
                                    {{ $log->area->name }}
                                </td>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-center dark:text-white">
                                    {{ $log->device->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center dark:text-white">
                                    @php
                                        $batteryColor = match (true) {
                                            $log->battery > 60 => [
                                                'text' => 'text-green-600 dark:text-green-500',
                                                'bg' => 'bg-green-50 dark:bg-green-500/10',
                                            ],
                                            $log->battery > 30 => [
                                                'text' => 'text-yellow-500 dark:text-yellow-400',
                                                'bg' => 'bg-yellow-50 dark:bg-yellow-500/10',
                                            ],
                                            default => [
                                                'text' => 'text-red-500 dark:text-red-400',
                                                'bg' => 'bg-red-50 dark:bg-red-500/10',
                                            ],
                                        };
                                    @endphp

                                    <span
                                        class="px-2 py-1 text-xs rounded-full battery-indicator {{ $batteryColor['bg'] }} {{ $batteryColor['text'] }}">
                                        {{ $log->battery }}%
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center dark:text-white">
                                    {{ $log->temperature }}°C
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center dark:text-white">
                                    {{ $log->humidity }}%
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center dark:text-white"
                                    data-order="{{ $log->received_at }}">
                                    {{ \Carbon\Carbon::parse($log->received_at)->format('d/m/Y H:i:s') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

        <script>
            $(document).ready(function() {
                // Initialize DataTable with client-side features only
                var table = $('#logsTable').DataTable({
                    processing: true,
                    pageLength: 10,
                    lengthMenu: [
                        [10, 25, 50, 100, -1],
                        [10, 25, 50, 100, "All"]
                    ],
                    order: [
                        [6, 'desc']
                    ],
                    columnDefs: [{
                        targets: [0],
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    }],
                    dom: '<"flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4"<"flex sm:flex-row sm:items-center gap-5"lB><"search-container"f>>rtip',
                    buttons: [{
                        extend: 'collection',
                        text: 'Export',
                        className: '',
                        autoClose: true,
                        buttons: [{
                                extend: 'copy',
                                text: '📋 Copy to clipboard',
                                exportOptions: {
                                    columns: [1, 2, 3, 4, 5, 6]
                                }
                            },
                            {
                                extend: 'excel',
                                text: '📄 Export as Excel',
                                exportOptions: {
                                    columns: [1, 2, 3, 4, 5, 6]
                                }
                            },
                            {
                                extend: 'pdf',
                                text: '🧾 Export as PDF',
                                orientation: 'landscape',
                                pageSize: 'A4',
                                exportOptions: {
                                    columns: [0, 1, 2, 3, 4, 5, 6]
                                }
                            },
                            {
                                extend: 'print',
                                text: '🖨️ Print',
                                exportOptions: {
                                    columns: [0, 1, 2, 3, 4, 5, 6]
                                }
                            }
                        ]
                    }],
                    search: {
                        caseInsensitive: true
                    },
                    initComplete: function() {
                        // Enhance search input
                        $('.dataTables_filter input').attr('placeholder', 'Search in current data...');
                        $('.dataTables_filter input').addClass(
                            'px-3 py-2 border border-gray-300 rounded-md text-sm');
                    }
                });

                // Initialize Flatpickr for date inputs
                flatpickr(".datepicker", {
                    dateFormat: "Y-m-d",
                    allowInput: true
                });

                function applyFiltersWithReload() {
                    var url = new URL(window.location.href);

                    // Clear existing parameters
                    url.searchParams.delete('area_id');
                    url.searchParams.delete('start_date');
                    url.searchParams.delete('end_date');

                    // Add new parameters
                    var areaFilter = $('#areaFilter').val();
                    var startDate = $('#startDate').val();
                    var endDate = $('#endDate').val();

                    if (areaFilter) url.searchParams.set('area_id', areaFilter);
                    if (startDate) url.searchParams.set('start_date', startDate);
                    if (endDate) url.searchParams.set('end_date', endDate);

                    window.location.href = url.toString();
                }

                // Update filter status display
                function updateFilterStatus() {
                    var activeFilters = [];

                    if ($('#areaFilter').val()) {
                        activeFilters.push('Area: ' + $('#areaFilter option:selected').text());
                    }
                    if ($('#startDate').val()) {
                        activeFilters.push('From: ' + $('#startDate').val());
                    }
                    if ($('#endDate').val()) {
                        activeFilters.push('To: ' + $('#endDate').val());
                    }

                    var statusElement = $('#filterStatus');
                    if (statusElement.length === 0) {
                        $('.filter-container').append(
                            '<div id="filterStatus" class="mt-2 text-sm text-blue-600 dark:text-blue-400"></div>');
                        statusElement = $('#filterStatus');
                    }

                    if (activeFilters.length > 0) {
                        statusElement.html('<i class="fas fa-filter mr-1"></i>Active filters: ' + activeFilters.join(
                            ', '));
                        statusElement.show();
                    } else {
                        statusElement.hide();
                    }
                }

                // Update URL without reload
                function updateURL() {
                    var url = new URL(window.location.href);

                    // Update URL parameters
                    var areaFilter = $('#areaFilter').val();
                    var startDate = $('#startDate').val();
                    var endDate = $('#endDate').val();

                    if (areaFilter) {
                        url.searchParams.set('area_id', areaFilter);
                    } else {
                        url.searchParams.delete('area_id');
                    }

                    if (startDate) {
                        url.searchParams.set('start_date', startDate);
                    } else {
                        url.searchParams.delete('start_date');
                    }

                    if (endDate) {
                        url.searchParams.set('end_date', endDate);
                    } else {
                        url.searchParams.delete('end_date');
                    }

                    // Update browser history
                    window.history.replaceState({}, '', url.toString());
                }

                // Update record count display
                function updateRecordCount(count) {
                    $('#totalRecords').text('Total: ' + count + ' records');
                }

                // Event handlers
                $('#applyFilter').on('click', function() {
                    applyFiltersWithReload();
                });

                // Restore filters from URL on page load
                function restoreFiltersFromURL() {
                    var urlParams = new URLSearchParams(window.location.search);

                    if (urlParams.get('area_id')) {
                        $('#areaFilter').val(urlParams.get('area_id'));
                    }
                    if (urlParams.get('start_date')) {
                        $('#startDate').val(urlParams.get('start_date'));
                    }
                    if (urlParams.get('end_date')) {
                        $('#endDate').val(urlParams.get('end_date'));
                    }

                    updateFilterStatus();
                }

                // Enhanced refresh function
                window.refreshTable = function() {
                    location.reload();
                };

                // Initialize
                restoreFiltersFromURL();
            });
        </script>
    @endpush
</x-app-layout>
