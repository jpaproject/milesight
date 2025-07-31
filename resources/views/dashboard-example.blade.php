<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IoT Device Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
</head>

<body class="min-h-screen bg-gray-50">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <header class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">IoT Device Dashboard</h1>
            <p class="text-gray-600">Monitor your connected devices and sensors in real-time</p>
            <div class="mt-4 flex items-center gap-4 text-sm text-gray-500">
                <span id="device-count">Loading devices...</span>
                <span id="last-refresh">Last updated: --:--:--</span>
            </div>
        </header>

        <!-- Device Grid -->
        <div id="device-grid"
            class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-4">
            <!-- Devices will be populated by JavaScript -->
        </div>
    </div>

    <script>
        // Generate sample device data
        function generateDevices() {
            const devices = [];
            const locations = ["PerkantoranSayapKiri", "PerkantoranSayapKanan", "RuangMeeting", "Lobby", "Gudang"];
            const areas = ["Keberangkatan", "Kedatangan", "Utara", "Selatan", "Tengah"];
            const deviceTypes = ["EM300-TH", "EM500-UDL", "WS301", "AM107", "VS121"];

            for (let i = 1; i <= 100; i++) {
                const location = locations[Math.floor(Math.random() * locations.length)];
                const area = areas[Math.floor(Math.random() * areas.length)];
                const deviceType = deviceTypes[Math.floor(Math.random() * deviceTypes.length)];

                devices.push({
                    id: i,
                    name: `${location}${area}-T${Math.floor(Math.random() * 5) + 1}${String.fromCharCode(65 + Math.floor(Math.random() * 3))}-${deviceType}`,
                    battery: Math.floor(Math.random() * 100) + 1,
                    temperature: (Math.random() * 15 + 20).toFixed(1),
                    humidity: Math.floor(Math.random() * 40) + 30,
                    lastUpdate: new Date(Date.now() - Math.random() * 3600000).toLocaleTimeString('id-ID', {
                        hour: '2-digit',
                        minute: '2-digit'
                    }),
                    isOnline: Math.random() > 0.1 // 90% online rate
                });
            }

            return devices;
        }

        // Get battery color classes
        function getBatteryColor(battery) {
            if (battery > 60) return {
                text: "text-blue-600",
                bg: "bg-blue-50",
                fill: "bg-blue-600"
            };
            if (battery > 30) return {
                text: "text-yellow-500",
                bg: "bg-yellow-50",
                fill: "bg-yellow-500"
            };
            return {
                text: "text-red-500",
                bg: "bg-red-50",
                fill: "bg-red-500"
            };
        }

        // Create device card HTML
        function createDeviceCard(device) {
            const batteryColors = getBatteryColor(device.battery);
            const statusColor = device.isOnline ? "bg-green-500" : "bg-red-500";
            const wifiIcon = device.isOnline ? "📶" : "📵";

            return `
                <div class="device-card bg-white rounded-lg border border-gray-200 p-4 shadow-sm hover:shadow-md">
                    <!-- Header with status indicator -->
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 ${statusColor} rounded-full"></div>
                            <span class="text-sm">${wifiIcon}</span>
                        </div>
                        <span class="text-xs text-gray-500">${device.lastUpdate}</span>
                    </div>

                    <!-- Device Name -->
                    <h3 class="font-medium text-gray-900 text-sm mb-4 leading-tight break-words">${device.name}</h3>

                    <!-- Metrics -->
                    <div class="space-y-3">
                        <!-- Battery -->
                        <div class="flex items-center justify-between p-2 rounded-md ${batteryColors.bg}">
                            <div class="flex items-center gap-2">
                                <div class="battery-icon ${batteryColors.text}">
                                    <div class="battery-fill ${batteryColors.fill}" style="width: ${device.battery}%"></div>
                                </div>
                                <span class="text-sm font-medium text-gray-700">Battery</span>
                            </div>
                            <span class="text-sm font-bold ${batteryColors.text}">${device.battery}%</span>
                        </div>

                        <!-- Temperature -->
                        <div class="flex items-center justify-between p-2 rounded-md bg-blue-50">
                            <div class="flex items-center gap-2">
                                <span class="text-blue-600">🌡️</span>
                                <span class="text-sm font-medium text-gray-700">Temp</span>
                            </div>
                            <span class="text-sm font-bold text-blue-600">${device.temperature}°C</span>
                        </div>

                        <!-- Humidity -->
                        <div class="flex items-center justify-between p-2 rounded-md bg-gray-50">
                            <div class="flex items-center gap-2">
                                <span class="text-gray-600">💧</span>
                                <span class="text-sm font-medium text-gray-700">Humidity</span>
                            </div>
                            <span class="text-sm font-bold text-gray-600">${device.humidity}%</span>
                        </div>
                    </div>
                </div>
            `;
        }

        // Render devices
        function renderDevices() {
            const devices = generateDevices();
            const grid = document.getElementById('device-grid');
            const deviceCount = document.getElementById('device-count');
            const lastRefresh = document.getElementById('last-refresh');

            // Update device count
            const onlineDevices = devices.filter(d => d.isOnline).length;
            deviceCount.textContent = `${devices.length} devices (${onlineDevices} online)`;

            // Update last refresh time
            lastRefresh.textContent = `Last updated: ${new Date().toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            })}`;

            // Render device cards
            grid.innerHTML = devices.map(device => createDeviceCard(device)).join('');
        }

        // Auto refresh every 30 seconds
        function startAutoRefresh() {
            setInterval(() => {
                renderDevices();
            }, 30000);
        }

        // Initialize dashboard
        document.addEventListener('DOMContentLoaded', function() {
            renderDevices();
            startAutoRefresh();
        });

        // Add click event for device cards
        document.addEventListener('click', function(e) {
            const deviceCard = e.target.closest('.device-card');
            if (deviceCard) {
                deviceCard.style.transform = 'scale(0.98)';
                setTimeout(() => {
                    deviceCard.style.transform = '';
                }, 150);
            }
        });
    </script>
</body>

</html>
