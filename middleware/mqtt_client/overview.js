// mqtt_client/overview.js
import mqtt from 'mqtt';
import { io } from 'socket.io-client';
import dotenv from 'dotenv';

// Muat konfigurasi dari file .env
dotenv.config({ path: "../../.env" });


// --- Konfigurasi dari .env ---
const mqttOptions = {
    host: process.env.MQTT_HOST,
    port: process.env.MQTT_PORT,
    username: process.env.MQTT_USERNAME,
    password: process.env.MQTT_PASSWORD,
    protocol: 'mqtt',
};
const MQTT_TOPIC = 'AP2/T1/Overview';
const WEBSOCKET_SERVER_URL = process.env.WEBSOCKET_SERVER_URL;

// 1. Hubungkan ke WebSocket Server (tidak ada perubahan)
console.log(`Menghubungkan ke WebSocket Server di ${WEBSOCKET_SERVER_URL}...`);
const socket = io(WEBSOCKET_SERVER_URL);
socket.on('connect', () => console.log(`✅ Terhubung ke WebSocket Server dengan ID: ${socket.id}`));
// ... (event handler socket.io lainnya tetap sama)

// 2. Hubungkan ke MQTT Broker dengan konfigurasi baru
console.log(`Menghubungkan ke MQTT Broker di ${mqttOptions.host}...`);
const mqttClient = mqtt.connect(mqttOptions);

mqttClient.on('connect', () => {
    console.log('✅ Terhubung ke MQTT Broker.');
    mqttClient.subscribe(MQTT_TOPIC, (err) => {
        if (!err) {
            console.log(`Berhasil subscribe ke topik: ${MQTT_TOPIC}`);
        } else {
            console.error('Gagal subscribe ke topik:', err);
        }
    });
});

// 3. Logika baru untuk membedakan payload
mqttClient.on('message', (topic, message) => {
    try {
        const payloadString = message.toString();
        const jsonData = JSON.parse(payloadString);
        console.log('📥 Pesan diterima dari MQTT:', jsonData);
        
        socket.emit('overview_data', jsonData);

    } catch (err) {
        console.error('Gagal memproses pesan MQTT:', err.message);
    }
});

mqttClient.on('error', (err) => {
    console.error('MQTT Client Error:', err);
});