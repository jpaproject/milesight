<x-app-layout>
    @push('styles')
        <style>
            .battery-icon {
                width: 16px;
                height: 10px;
                border: 1px solid currentColor;
                border-radius: 2px;
                position: relative;
            }

            .battery-icon::after {
                content: '';
                position: absolute;
                right: -3px;
                top: 2px;
                width: 2px;
                height: 6px;
                background: currentColor;
                border-radius: 0 1px 1px 0;
            }

            .battery-fill {
                height: 100%;
                border-radius: 1px;
                transition: width 0.3s ease;
            }

            .device-card {
                transition: all 0.2s ease;
            }

            .device-card:hover {
                transform: translateY(-2px);
            }

            /* Search styles */
            .search-container {
                position: relative;
            }

            .search-input {
                transition: all 0.2s ease;
            }

            .search-input:focus {
                transform: translateY(-1px);
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            }

            .search-clear-btn {
                transition: all 0.2s ease;
            }

            .search-clear-btn:hover {
                background-color: rgba(239, 68, 68, 0.1);
            }

            .device-card.hidden {
                display: none;
            }

            .no-results {
                animation: fadeIn 0.3s ease;
            }

            @keyframes fadeIn {
                from {
                    opacity: 0;
                    transform: translateY(10px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            /* Accordion styles */
            .area-header {
                cursor: pointer;
                transition: all 0.2s ease;
                user-select: none;
            }

            .area-header:hover {
                background-color: rgba(255, 255, 255, 0.1);
            }

            .area-content {
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                overflow: hidden;
            }

            .area-content.collapsed {
                max-height: 0;
                opacity: 0;
                margin-top: 0;
                padding-top: 0;
                padding-bottom: 0;
            }

            .area-content.expanded {
                max-height: 2000px;
                opacity: 1;
                margin-top: 1rem;
            }

            .chevron-icon {
                transition: transform 0.2s ease;
            }

            .chevron-icon.rotated {
                transform: rotate(180deg);
            }

            .area-badge {
                transition: all 0.2s ease;
            }

            /* Improved hover effects */
            .area-container {
                transition: all 0.2s ease;
            }

            .area-container:hover {
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
                transform: translateY(-1px);
            }
        </style>
    @endpush

    <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">
        <a href={{ route('dashboard.index') }}>Dashboard</a> &rarr; {{ $terminal->name }}
    </h2>

    @php
        $colors = [
            'bg-blue-50 dark:bg-blue-900/20',
            'bg-yellow-50 dark:bg-yellow-900/20',
            'bg-green-50 dark:bg-green-900/20',
            'bg-purple-50 dark:bg-purple-900/20',
            'bg-pink-50 dark:bg-pink-900/20',
            'bg-red-50 dark:bg-red-900/20',
        ];
    @endphp

    @foreach ($terminal->areas as $index => $area)
        @php
            $bgColor = $colors[$index % count($colors)];
        @endphp

        <div class="area-container mt-5 rounded-xl border border-gray-200 dark:border-gray-700 {{ $bgColor }}"
            data-area-container>

            {{-- Accordion Header --}}
            <div class="area-header flex items-center justify-between p-4 rounded-t-xl"
                data-area-toggle="{{ $area->id }}">
                <div class="flex items-center gap-3">
                    <h3 class="text-xl font-bold text-gray-800 dark:text-white">
                        {{ $area->name }}
                    </h3>
                    <span
                        class="area-badge inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-white/50 dark:bg-white/10 text-gray-700 dark:text-gray-300"
                        data-area-count="{{ $area->id }}">
                        {{ count($area->devices) }} devices
                    </span>
                </div>

                {{-- Chevron Icon --}}
                <div class="flex items-center gap-2">
                    <svg class="chevron-icon w-5 h-5 text-gray-500 dark:text-gray-400" data-chevron="{{ $area->id }}"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
            </div>

            {{-- Accordion Content --}}
            <div class="area-content expanded px-4 pb-4" data-area-content="{{ $area->id }}">
                {{-- Search Input --}}
                <div class="search-container flex justify-start md:justify-end mb-4">
                    <div class="relative w-full md:w-auto">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text"
                            class="search-input block w-full sm:w-64 pl-10 pr-10 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent"
                            placeholder="Search devices in {{ $area->name }}..."
                            data-area-search="{{ $area->id }}">
                        <button type="button"
                            class="search-clear-btn absolute inset-y-0 right-0 pr-3 flex items-center opacity-0 pointer-events-none text-gray-400 hover:text-red-500 transition-all duration-200"
                            data-clear-search="{{ $area->id }}">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- List Devices --}}
                <div class="device-grid grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-4"
                    data-area-devices="{{ $area->id }}">
                    @forelse ($area->devices as $device)
                        <div class="device-card bg-white dark:bg-white/[0.03] rounded-lg border border-gray-200 dark:border-gray-800 p-4 shadow-sm hover:shadow-md"
                            id="device-{{ $device->name }}" data-device-name="{{ strtolower($device->name) }}"
                            data-area-id="{{ $area->id }}">

                            <h3
                                class="device-name font-medium text-gray-900 dark:text-white text-sm mb-4 leading-tight break-words">
                                {{ $device->name }}
                            </h3>

                            <div class="space-y-3">
                                <!-- Battery -->
                                <div
                                    class="battery flex items-center justify-between p-2 rounded-md bg-green-50 dark:bg-green-500/10">
                                    <div class="flex items-center gap-2">
                                        <div class="battery-icon">
                                            <div class="battery-fill bg-green-600" style="width: 50%"></div>
                                        </div>
                                        <span
                                            class="text-sm font-medium text-gray-700 dark:text-gray-400">Battery</span>
                                    </div>
                                    <span class="battery-value text-sm font-bold text-green-600">50%</span>
                                </div>

                                <!-- Temperature -->
                                <div
                                    class="temperature flex items-center justify-between p-2 rounded-md bg-blue-50 dark:bg-blue-500/10">
                                    <div class="flex items-center gap-2">
                                        <span class="text-blue-600">🌡️</span>
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-400">Temp</span>
                                    </div>
                                    <span
                                        class="temperature-value text-sm font-bold text-blue-600 dark:text-blue-500">25°C</span>
                                </div>

                                <!-- Humidity -->
                                <div
                                    class="humidity flex items-center justify-between p-2 rounded-md bg-gray-50 dark:bg-gray-300">
                                    <div class="flex items-center gap-2">
                                        <span class="text-gray-600">💧</span>
                                        <span class="text-sm font-medium text-gray-700">Humidity</span>
                                    </div>
                                    <span class="humidity-value text-sm font-bold text-gray-600">50%</span>
                                </div>

                                <div
                                    class="timestamp flex items-center justify-between p-2 rounded-md bg-gray-50 dark:bg-gray-300">
                                    <div class="flex items-center gap-2">
                                        <span class="text-gray-600">🕒</span>
                                        <span class="text-sm font-medium text-gray-700">Timestamp</span>
                                    </div>
                                    <span class="timestamp-value text-sm font-bold text-gray-600">00:00:00</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-800 dark:text-white/90">No devices found.</p>
                    @endforelse
                </div>

                {{-- No Results Message --}}
                <div class="no-results hidden mt-4 text-center py-8" data-no-results="{{ $area->id }}">
                    <div class="text-gray-400 dark:text-gray-500">
                        <svg class="mx-auto h-12 w-12 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <h3 class="text-sm font-medium text-gray-900 dark:text-gray-300">No devices found</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Try searching with different keywords
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    @push('scripts')
        <script src="https://cdn.socket.io/4.7.4/socket.io.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Initialize device loading states
                document.querySelectorAll('[id^="device-"]').forEach(card => {
                    card.querySelector('.battery-value').innerText = 'Loading...';
                    card.querySelector('.temperature-value').innerText = 'Loading...';
                    card.querySelector('.humidity-value').innerText = 'Loading...';
                    card.querySelector('.timestamp-value').innerText = 'Loading...';
                });

                // Accordion functionality
                function initializeAccordion() {
                    const accordionHeaders = document.querySelectorAll('[data-area-toggle]');

                    accordionHeaders.forEach(header => {
                        const areaId = header.getAttribute('data-area-toggle');
                        const content = document.querySelector(`[data-area-content="${areaId}"]`);
                        const chevron = document.querySelector(`[data-chevron="${areaId}"]`);

                        header.addEventListener('click', function() {
                            const isExpanded = content.classList.contains('expanded');

                            if (isExpanded) {
                                // Collapse
                                content.classList.remove('expanded');
                                content.classList.add('collapsed');
                                chevron.classList.remove('rotated');
                            } else {
                                // Expand
                                content.classList.remove('collapsed');
                                content.classList.add('expanded');
                                chevron.classList.add('rotated');
                            }
                        });
                    });
                }

                // Search functionality
                function initializeSearch() {
                    // Get all search inputs
                    const searchInputs = document.querySelectorAll('[data-area-search]');

                    searchInputs.forEach(searchInput => {
                        const areaId = searchInput.getAttribute('data-area-search');
                        const clearButton = document.querySelector(`[data-clear-search="${areaId}"]`);
                        const noResultsDiv = document.querySelector(`[data-no-results="${areaId}"]`);
                        const deviceGrid = document.querySelector(`[data-area-devices="${areaId}"]`);
                        const areaCount = document.querySelector(`[data-area-count="${areaId}"]`);

                        // Search function
                        function performSearch() {
                            const searchTerm = searchInput.value.toLowerCase().trim();
                            const devices = deviceGrid.querySelectorAll('.device-card');
                            let visibleCount = 0;

                            devices.forEach(device => {
                                const deviceName = device.getAttribute('data-device-name');
                                const isMatch = deviceName.includes(searchTerm);

                                if (isMatch) {
                                    device.classList.remove('hidden');
                                    visibleCount++;
                                } else {
                                    device.classList.add('hidden');
                                }
                            });

                            // Update area badge count
                            if (searchTerm === '') {
                                areaCount.textContent = `${devices.length} devices`;
                            } else {
                                areaCount.textContent = `${visibleCount} of ${devices.length} devices`;
                            }

                            // Show/hide no results message
                            if (visibleCount === 0 && searchTerm !== '') {
                                noResultsDiv.classList.remove('hidden');
                            } else {
                                noResultsDiv.classList.add('hidden');
                            }

                            // Show/hide clear button
                            if (searchTerm !== '') {
                                clearButton.style.opacity = '1';
                                clearButton.style.pointerEvents = 'auto';
                            } else {
                                clearButton.style.opacity = '0';
                                clearButton.style.pointerEvents = 'none';
                            }
                        }

                        // Event listeners
                        searchInput.addEventListener('input', performSearch);
                        searchInput.addEventListener('keyup', performSearch);

                        // Clear search
                        clearButton.addEventListener('click', function() {
                            searchInput.value = '';
                            searchInput.focus();
                            performSearch();
                        });

                        // Clear search on Escape key
                        searchInput.addEventListener('keydown', function(e) {
                            if (e.key === 'Escape') {
                                searchInput.value = '';
                                performSearch();
                                searchInput.blur();
                            }
                        });
                    });
                }

                // Initialize accordion and search
                initializeAccordion();
                initializeSearch();

                // Socket.IO connection
                const socket = io("{{ env('WEBSOCKET_SERVER_URL') }}", {
                    transports: ['websocket', 'polling'],
                    reconnection: true,
                    reconnectionAttempts: Infinity,
                    reconnectionDelay: 1000,
                    reconnectionDelayMax: 5000,
                    timeout: 20000,
                });

                // Connection status handlers
                socket.on('connect', () => {
                    console.log('Socket.IO Connected successfully');
                });

                socket.on('disconnect', () => {
                    console.log('Socket.IO Disconnected');
                });

                socket.on('connect_error', (error) => {
                    console.error('Connection error:', error);
                });

                socket.on('sensor_data', function(data) {
                    console.log('Received sensor data:', data);
                    const card = document.getElementById(`device-${data.deviceName}`);

                    if (card) {
                        // Battery
                        if (data.battery !== undefined) {
                            card.querySelector('.battery-value').innerText = `${data.battery}%`;
                        } else {
                            card.querySelector('.battery-value').innerText = "Error";
                        }

                        // Temperature
                        if (data.temperature !== undefined) {
                            card.querySelector('.temperature-value').innerText = `${data.temperature}°C`;
                        } else {
                            card.querySelector('.temperature-value').innerText = "N/A";
                        }

                        // Humidity
                        if (data.humidity !== undefined) {
                            card.querySelector('.humidity-value').innerText = `${data.humidity}%`;
                        } else {
                            card.querySelector('.humidity-value').innerText = "N/A";
                        }

                        // timestamp
                        if (data.timestamp !== undefined) {
                            card.querySelector('.timestamp-value').innerText = new Date(data.receivedAt)
                                .toLocaleString();
                        }

                        // Battery color & progress bar (hanya kalau ada data.battery)
                        const batteryBg = card.querySelector('.battery');
                        const batteryIcon = card.querySelector('.battery-icon');
                        const batteryFill = card.querySelector('.battery-fill');
                        const batteryValue = card.querySelector('.battery-value');

                        if (data.battery !== undefined) {
                            const color = getBatteryColor(data.battery);

                            batteryBg.className =
                                `battery flex items-center justify-between p-2 rounded-md gap-2 ${color.bg}`;
                            batteryIcon.className = `battery-icon ${color.text}`;
                            batteryFill.className = `battery-fill ${color.fill}`;
                            batteryFill.style.width = `${data.battery}%`;
                            batteryValue.className = `battery-value text-sm font-bold ${color.text}`;
                        } else {
                            // Kalau battery tidak ada → reset warna/width biar gak kacau
                            batteryBg.className =
                                "battery flex items-center justify-between p-2 rounded-md gap-2";
                            batteryIcon.className = "battery-icon text-gray-400";
                            batteryFill.className = "battery-fill bg-gray-300";
                            batteryFill.style.width = "0%";
                            batteryValue.className = "battery-value text-sm font-bold text-gray-400";
                        }
                    }
                });


                function getBatteryColor(battery) {
                    if (battery > 60) return {
                        text: "text-green-600 dark:text-green-500",
                        bg: "bg-green-50 dark:bg-green-500/10",
                        fill: "bg-green-600 dark:bg-green-500"
                    };
                    if (battery > 30) return {
                        text: "text-yellow-500 dark:text-yellow-400",
                        bg: "bg-yellow-50 dark:bg-yellow-500/10",
                        fill: "bg-yellow-500 dark:bg-yellow-400"
                    };
                    return {
                        text: "text-red-500 dark:text-red-400",
                        bg: "bg-red-50 dark:bg-red-500/10",
                        fill: "bg-red-500 dark:bg-red-400"
                    };
                }
            })
        </script>
    @endpush
</x-app-layout>
