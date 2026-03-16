<x-app-layout>
    @push('styles')
        <style>
            .blinking-red {
                color: red;
            }

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

            .status-pill {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                padding: 4px 10px;
                border-radius: 9999px;
                font-size: 12px;
                font-weight: 600;
                border: 1px solid transparent;
            }

            .status-live {
                color: #166534;
                background: #dcfce7;
                border-color: #86efac;
            }

            .status-stale {
                color: #991b1b;
                background: #fee2e2;
                border-color: #fecaca;
            }

            .status-disconnected {
                color: #374151;
                background: #e5e7eb;
                border-color: #d1d5db;
            }

            .status-waiting {
                color: #1f2937;
                background: #f3f4f6;
                border-color: #e5e7eb;
            }

            .status-dot {
                width: 8px;
                height: 8px;
                border-radius: 9999px;
                background: currentColor;
            }
        </style>
    @endpush

    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">Dashboard</h2>
        <div class="flex items-center gap-3">
            <span id="realtimeStatus" class="status-pill status-waiting">
                <span class="status-dot"></span>
                <span class="status-text">Waiting</span>
            </span>
            <span class="text-xs text-gray-500 dark:text-gray-400">
                Last update: <span id="lastUpdate">—</span>
            </span>
        </div>
    </div>

    <div class="mt-5">
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
                    placeholder="Search devices..." data-device-search>
                <button type="button"
                    class="search-clear-btn absolute inset-y-0 right-0 pr-3 flex items-center opacity-0 pointer-events-none text-gray-400 hover:text-red-500 transition-all duration-200"
                    data-clear-search>
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <div class="device-grid grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-4"
            data-device-grid>
            @forelse ($devices as $device)
                <div class="device-card bg-white dark:bg-white/[0.03] rounded-lg border border-gray-200 dark:border-gray-800 p-4 shadow-sm hover:shadow-md"
                    id="device-{{ $device->name }}" data-device-name="{{ strtolower($device->name) }}">

                    <div class="flex items-start justify-between gap-2">
                        <h3
                            class="device-name font-medium text-gray-900 dark:text-white text-sm mb-4 leading-tight break-words">
                            {{ $device->name }}
                        </h3>
                        {{-- <span
                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                            {{ $device->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-200' }}">
                            {{ $device->is_active ? 'Active' : 'Inactive' }}
                        </span> --}}
                    </div>

                    <div class="space-y-3">
                        <!-- Battery -->
                        <div
                            class="battery flex items-center justify-between p-2 rounded-md bg-green-50 dark:bg-green-500/10">
                            <div class="flex items-center gap-2">
                                <div class="battery-icon">
                                    <div class="battery-fill bg-green-600" style="width: 50%"></div>
                                </div>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-400">Battery</span>
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
                        <div class="humidity flex items-center justify-between p-2 rounded-md bg-gray-50 dark:bg-gray-300">
                            <div class="flex items-center gap-2">
                                <span class="text-gray-600">💧</span>
                                <span class="text-sm font-medium text-gray-700">Humidity</span>
                            </div>
                            <span class="humidity-value text-sm font-bold text-gray-600">50%</span>
                        </div>

                        <!-- Timestamp -->
                        <div class="timestamp flex items-center justify-between p-2 rounded-md bg-gray-50 dark:bg-gray-300">
                            <div class="flex items-center gap-2">
                                <span class="text-gray-600">🕒</span>
                                <span class="text-sm font-medium text-gray-700">Timestamp</span>
                            </div>
                            <span class="timestamp-value text-sm font-bold text-gray-600">—</span>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-gray-800 dark:text-white/90">No devices found.</p>
            @endforelse
        </div>

        <div class="no-results hidden mt-4 text-center py-8" data-no-results>
            <div class="text-gray-400 dark:text-gray-500">
                <svg class="mx-auto h-12 w-12 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <h3 class="text-sm font-medium text-gray-900 dark:text-gray-300">No devices found</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Try searching with different keywords</p>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.socket.io/4.7.4/socket.io.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const STALE_MS = 5 * 60 * 1000;
                let socketConnected = false;
                let lastGlobalUpdate = null;

                // Initialize device loading states
                document.querySelectorAll('[id^="device-"]').forEach(card => {
                    card.querySelector('.battery-value').innerText = 'No data';
                    card.querySelector('.temperature-value').innerText = 'No data';
                    card.querySelector('.humidity-value').innerText = 'No data';
                    card.querySelector('.timestamp-value').innerText = '—';
                });

                // Search functionality
                const searchInput = document.querySelector('[data-device-search]');
                const clearButton = document.querySelector('[data-clear-search]');
                const noResultsDiv = document.querySelector('[data-no-results]');
                const deviceGrid = document.querySelector('[data-device-grid]');

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

                    if (visibleCount === 0 && searchTerm !== '') {
                        noResultsDiv.classList.remove('hidden');
                    } else {
                        noResultsDiv.classList.add('hidden');
                    }

                    if (searchTerm !== '') {
                        clearButton.style.opacity = '1';
                        clearButton.style.pointerEvents = 'auto';
                    } else {
                        clearButton.style.opacity = '0';
                        clearButton.style.pointerEvents = 'none';
                    }
                }

                searchInput.addEventListener('input', performSearch);
                searchInput.addEventListener('keyup', performSearch);
                clearButton.addEventListener('click', function() {
                    searchInput.value = '';
                    searchInput.focus();
                    performSearch();
                });
                searchInput.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') {
                        searchInput.value = '';
                        performSearch();
                        searchInput.blur();
                    }
                });

                function setStatusPill(el, status, text) {
                    el.classList.remove('status-live', 'status-stale', 'status-disconnected', 'status-waiting');
                    el.classList.add(status);
                    const label = el.querySelector('.status-text');
                    if (label) label.textContent = text;
                }

                function formatLastUpdate(date) {
                    return date.toLocaleString('en-CA', {
                        year: 'numeric',
                        month: '2-digit',
                        day: '2-digit',
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit',
                        hour12: false
                    }).replace(',', '');
                }

                function updateGlobalStatus() {
                    const statusEl = document.getElementById('realtimeStatus');
                    const lastUpdateEl = document.getElementById('lastUpdate');
                    if (!statusEl || !lastUpdateEl) return;

                    if (!socketConnected) {
                        setStatusPill(statusEl, 'status-disconnected', 'Disconnected');
                        return;
                    }

                    if (!lastGlobalUpdate) {
                        setStatusPill(statusEl, 'status-waiting', 'No data');
                        return;
                    }

                    const diff = Date.now() - lastGlobalUpdate.getTime();
                    if (diff > STALE_MS) {
                        setStatusPill(statusEl, 'status-stale', 'Stale');
                    } else {
                        setStatusPill(statusEl, 'status-live', 'Live');
                    }

                    lastUpdateEl.textContent = formatLastUpdate(lastGlobalUpdate);
                }

                function refreshAllStatuses() {
                    updateGlobalStatus();
                }

                // Socket.IO connection
                const socket = io("{{ env('WEBSOCKET_SERVER_URL') }}", {
                    transports: ['websocket', 'polling'],
                    reconnection: true,
                    reconnectionAttempts: Infinity,
                    reconnectionDelay: 1000,
                    reconnectionDelayMax: 5000,
                    timeout: 20000,
                });

                socket.on('connect', () => {
                    console.log('Socket.IO Connected successfully');
                    socketConnected = true;
                    updateGlobalStatus();
                });

                socket.on('disconnect', () => {
                    console.log('Socket.IO Disconnected');
                    socketConnected = false;
                    updateGlobalStatus();
                });

                socket.on('connect_error', (error) => {
                    console.error('Connection error:', error);
                    socketConnected = false;
                    updateGlobalStatus();
                });

                socket.on('sensor_data', function(data) {
                    const card = document.getElementById(`device-${data.deviceName}`);

                    if (card) {
                        const receivedAt = data.receivedAt ? new Date(data.receivedAt) : null;
                        if (receivedAt && !isNaN(receivedAt)) {
                            lastGlobalUpdate = receivedAt;
                        } else {
                            lastGlobalUpdate = new Date();
                        }

                        if (data.battery !== undefined) {
                            card.querySelector('.battery-value').innerText = `${data.battery}%`;
                        } else {
                            card.querySelector('.battery-value').innerText = "Error";
                        }

                        if (data.temperature !== undefined) {
                            card.querySelector('.temperature-value').innerText = `${data.temperature}°C`;
                        } else {
                            card.querySelector('.temperature-value').innerText = "N/A";
                        }

                        if (data.humidity !== undefined) {
                            card.querySelector('.humidity-value').innerText = `${data.humidity}%`;
                        } else {
                            card.querySelector('.humidity-value').innerText = "N/A";
                        }

                        if (data.receivedAt !== undefined) {
                            const tsElement = card.querySelector('.timestamp-value');
                            const date = new Date(data.receivedAt);
                            tsElement.innerText = date.toLocaleString('en-CA', {
                                year: 'numeric',
                                month: '2-digit',
                                day: '2-digit',
                                hour: '2-digit',
                                minute: '2-digit',
                                hour12: false
                            }).replace(',', '');
                        }

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
                            batteryBg.className =
                                "battery flex items-center justify-between p-2 rounded-md gap-2";
                            batteryIcon.className = "battery-icon text-gray-400";
                            batteryFill.className = "battery-fill bg-gray-300";
                            batteryFill.style.width = "0%";
                            batteryValue.className = "battery-value text-sm font-bold text-gray-400";
                        }

                        updateGlobalStatus();
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

                refreshAllStatuses();
                setInterval(refreshAllStatuses, 30 * 1000);
            })
        </script>
    @endpush
</x-app-layout>
