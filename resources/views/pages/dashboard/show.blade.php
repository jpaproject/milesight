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
        </style>
    @endpush

    <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">{{ $area->terminal->name }} / {{ $area->name }}
    </h2>

    <div class="mt-10 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-4">
        @forelse ($area->devices as $device)
            <div class="device-card bg-white dark:bg-white/[0.03] rounded-lg border border-gray-200 dark:border-gray-800 p-4 shadow-sm hover:shadow-md"
                id="device-{{ $device->name }}">

                <h3 class="device-name font-medium text-gray-900 dark:text-white text-sm mb-4 leading-tight break-words">
                    {{ $device->name }}</h3>

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
                        <span class="temperature-value text-sm font-bold text-blue-600 dark:text-blue-500">25°C</span>
                    </div>

                    <!-- Humidity -->
                    <div class="humidity flex items-center justify-between p-2 rounded-md bg-gray-50 dark:bg-gray-300">
                        <div class="flex items-center gap-2">
                            <span class="text-gray-600">💧</span>
                            <span class="text-sm font-medium text-gray-700">Humidity</span>
                        </div>
                        <span class="humidity-value text-sm font-bold text-gray-600">50%</span>
                    </div>
                </div>
            </div>
        @empty
            <p>No devices found.</p>
        @endforelse
    </div>

    @push('scripts')
        <script src="https://cdn.socket.io/4.7.4/socket.io.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('[id^="device-"]').forEach(card => {
                    card.querySelector('.battery-value').innerText = 'Loading...';
                    card.querySelector('.temperature-value').innerText = 'Loading...';
                    card.querySelector('.humidity-value').innerText = 'Loading...';
                });


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
                    const card = document.getElementById(`device-${data.deviceName}`);

                    if (card) {
                        card.querySelector('.battery-value').innerText = `${data.battery}%`;
                        card.querySelector('.temperature-value').innerText = `${data.temperature}°C`;
                        card.querySelector('.humidity-value').innerText = `${data.humidity}%`;

                        const batteryBg = card.querySelector('.battery');
                        batteryBg.className =
                            `battery flex items-center justify-between p-2 rounded-md gap-2 ${getBatteryColor(data.battery).bg}`;

                        const batteryIcon = card.querySelector('.battery-icon');
                        batteryIcon.className =
                            `battery-icon ${getBatteryColor(data.battery).text}`;

                        const batteryFill = card.querySelector('.battery-fill');
                        batteryFill.className =
                            `battery-fill ${getBatteryColor(data.battery).fill}`;
                        batteryFill.style.width = `${data.battery}%`;

                        const batteryValue = card.querySelector('.battery-value');
                        batteryValue.className =
                            `battery-value text-sm font-bold ${getBatteryColor(data.battery).text}`;
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
