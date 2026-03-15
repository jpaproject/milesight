const mqtt = require("mqtt");

// === Konfigurasi MQTT Broker ===
const brokerUrl = "mqtt://public.grootech.id:1883";
const topic = "AP2/T1/Data";

const devices = [
    {
        name: "SENSOR-1",
        battery: 85,
        humidity: 50,
        temperature: 25,
    },
    {
        name: "SENSOR-2",
        battery: 90,
        humidity: 55,
        temperature: 27,
    },
    // {
    //     name: "SENSOR-3",
    //     battery: 75,
    //     humidity: 60,
    //     temperature: 30,
    // },
];

// === Fungsi generate payload ===
function generatePayload(device, sensorNumber) {
    return {
        [`battery-${sensorNumber}`]: device.battery,
        [`devEUI-${sensorNumber}`]: Math.random().toString(16).substr(2, 16),
        [`deviceName-${sensorNumber}`]: device.name,
        [`humidity-${sensorNumber}`]: device.humidity,
        [`temperature-${sensorNumber}`]: device.temperature,
    };
}

const client = mqtt.connect(brokerUrl);

client.on("connect", () => {
    console.log(`✅ Connected to MQTT Broker at ${brokerUrl}`);

    setInterval(() => {
        devices.forEach((device, index) => {
            const sensorNumber = index + 1;
            const payload = generatePayload(device, sensorNumber);
            client.publish(topic, JSON.stringify(payload));
            console.log(`📤 Published for ${device.name} →`, payload);
        });
    }, 5000);
});

client.on("error", (err) => {
    console.error("❌ MQTT Error:", err.message);
});
