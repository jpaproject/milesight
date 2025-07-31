const mqtt = require("mqtt");

// === Konfigurasi MQTT Broker ===
const brokerUrl = "mqtt://public.grootech.id:1883";
const topic = "AP2/T1/Data";

const deviceNames = [
    "BoardingLounge-B1-T1A-EM300-TH",
    "BoardingLounge-B2-T1A-EM300-TH",
    "Lingkingatas-B1-T1A-EM300-TH",
    "BoardingLounge-B1-T1B-AM102",
    "BoardingLounge-B2-T1B-AM102",
    "Lingkingatas-B1-T1B-AM102",
];

// === Fungsi generate data random ===
function generatePayload(deviceName) {
    return {
        battery: Math.floor(Math.random() * 100),
        devEUI: Math.random().toString(16).substr(2, 16), // optional
        deviceName,
        humidity: parseFloat((Math.random() * 40 + 30).toFixed(1)), // 30–70%
        temperature: parseFloat((Math.random() * 10 + 24).toFixed(1)), // 24–34°C
    };
}

const client = mqtt.connect(brokerUrl);

client.on("connect", () => {
    console.log(`✅ Connected to MQTT Broker at ${brokerUrl}`);

    setInterval(() => {
        deviceNames.forEach((device) => {
            const payload = generatePayload(device);
            client.publish(topic, JSON.stringify(payload));
            console.log(`📤 Published for ${device} →`, payload);
        });
    }, 5000);
});

client.on("error", (err) => {
    console.error("❌ MQTT Error:", err.message);
});
