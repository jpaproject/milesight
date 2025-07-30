import mqtt from "mqtt";
import { io } from "socket.io-client";
import axios from "axios";
import { DateTime } from "luxon";
import cron from "node-cron";
import dotenv from "dotenv";
dotenv.config({ path: "../../.env" });

const TIMEZONE = "Asia/Jakarta";

let isShuttingDown = false;
let mqttClient = null;
let socketClient = null;
let lastMessageTime = null;
let dataBuffer = [];

// ensuring that only authorized mqtt.js services can access specific API endpoints by including an API key with each request.
const API_KEY = process.env.MQTT_SERVICE_API_KEY;

const apiRequest = axios.create({
    baseURL: process.env.APP_URL + "/api",
    headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
        "X-API-Key": API_KEY,
    },
});

// MQTT Configuration
const config = {
    host: process.env.MQTT_HOST || "public.grootech.id",
    port: process.env.MQTT_PORT || "1883",
    clientId: `device_${Math.random().toString(16).slice(3)}`,
    topic: process.env.MQTT_TOPIC_SIGNATURE || "sensor/ambience",
    options: {
        clean: true,
        connectTimeout: 10000,
        username: process.env.MQTT_USERNAME,
        password: process.env.MQTT_PASSWORD,
        reconnectPeriod: 1000,
        keepalive: 30,
    },
};

const wsConfig = {
    url: process.env.WEBSOCKET_SERVER_URL || "http://localhost:3000",
    options: {
        reconnection: true,
        reconnectionAttempts: 10,
        reconnectionDelay: 1000,
        reconnectionDelayMax: 10000,
        timeout: 20000,
    },
};

function validatePayload(payload) {
    if (!payload || typeof payload !== "object") {
        return { valid: false, reason: "Invalid payload structure" };
    }

    const expectedFields = ["deviceName", "temperature", "humidity", "battery"];
    const hasValidField = expectedFields.some((field) =>
        payload.hasOwnProperty(field)
    );

    if (!hasValidField) {
        return { valid: false, reason: "No expected fields found" };
    }

    return { valid: true };
}

async function handleMQTTMessage(topic, message) {
    if (isShuttingDown) return;

    try {
        const payload = JSON.parse(message.toString());
        const validation = validatePayload(payload);

        if (!validation.valid) {
            console.warn(`Invalid payload from ${topic}:`, validation.reason);
            return;
        }

        const { data: deviceExists } = await apiRequest.get("/device-exists", {
            params: { name: payload.deviceName },
        });

        if (!deviceExists) {
            console.warn(
                `Device "${payload.deviceName}" not registered in DB. Ignored.`
            );
            return;
        }

        const enrichedPayload = {
            ...payload,
            receivedAt: new Date().toISOString(),
        };

        const index = dataBuffer.findIndex(
            (d) => d.deviceName === enrichedPayload.deviceName
        );

        if (index !== -1) {
            // deviceName ada → update data
            dataBuffer[index] = enrichedPayload;
        } else {
            // deviceName tidak ada → tambahkan data baru
            dataBuffer.push(enrichedPayload);
        }

        // Forward to WebSocket only if connected
        if (socketClient?.connected) {
            socketClient.emit("mqtt_data", enrichedPayload);
            lastMessageTime = Date.now();
        } else {
            console.warn("WebSocket not connected, message dropped");
        }
    } catch (error) {
        console.error(`Failed to parse message from ${topic}:`, error.message);
        return;
    }
}

async function sendDataToAPI() {
    if (!dataBuffer) {
        console.log("No sensor data available to store");
        return;
    }

    try {
        const response = await apiRequest.post("/device-readings", dataBuffer);

        if (response.status === 200 || response.status === 201) {
            console.log(
                `✅ Successfully sent ${data.length} records to database`
            );
            return true;
        } else {
            console.error(`❌ API returned status: ${response.status}`);
            return false;
        }
    } catch (error) {
        console.error("❌ Failed to send data to API:", error.message);

        // Log detail error untuk debugging
        if (error.response) {
            console.error(`API Error Status: ${error.response.status}`);
            console.error(`API Error Data:`, error.response.data);
        } else if (error.request) {
            console.error("No response received from API");
        }

        return false;
    }
}

// MQTT connection setup
function initMQTT() {
    const connectUrl = `mqtt://${config.host}:${config.port}`;

    mqttClient = mqtt.connect(connectUrl, {
        clientId: config.clientId,
        ...config.options,
    });

    mqttClient.on("connect", () => {
        console.log(`✅ MQTT Connected to ${connectUrl}`);
        console.log(`📡 Client ID: ${config.clientId}`);

        mqttClient.subscribe(config.topic, { qos: 1 }, (err) => {
            if (err) {
                console.error("❌ Subscribe error:", err.message);
                setTimeout(() => mqttClient.subscribe(config.topic), 5000);
            } else {
                console.log(`📨 Subscribed to: ${config.topic}`);
            }
        });
    });

    mqttClient.on("error", (error) => {
        console.error("❌ MQTT Error:", error.message);
    });

    mqttClient.on("reconnect", () => {
        console.log("🔄 MQTT Reconnecting...");
    });

    mqttClient.on("offline", () => {
        console.log("📴 MQTT Offline");
    });

    mqttClient.on("message", handleMQTTMessage);
}

// WebSocket connection setup
function initWebSocket() {
    socketClient = io(wsConfig.url, wsConfig.options);

    socketClient.on("connect", () => {
        console.log(`✅ WebSocket Connected to ${wsConfig.url}`);
        console.log(`🆔 Socket ID: ${socketClient.id}`);
    });

    // Hapus manual reconnection logic - biar Socket.IO yang handle
    socketClient.on("connect_error", (error) => {
        console.error("❌ WebSocket Error:", error.message);
    });

    socketClient.on("disconnect", (reason) => {
        console.log(`📴 WebSocket Disconnected: ${reason}`);
    });

    socketClient.on("reconnect", (attemptNumber) => {
        console.log(`🔄 WebSocket Reconnected after ${attemptNumber} attempts`);
    });
}

// Health monitoring
function startHealthCheck() {
    setInterval(() => {
        const mqttStatus = mqttClient?.connected ? "✅" : "❌";
        const wsStatus = socketClient?.connected ? "✅" : "❌";
        const lastMsg = lastMessageTime
            ? `${Math.round((Date.now() - lastMessageTime) / 1000)}s ago`
            : "Never";

        console.log(
            `🏥 Status: MQTT ${mqttStatus} | WS ${wsStatus}  | Last: ${lastMsg}`
        );
    }, 30000);
}

// Graceful shutdown
async function shutdown() {
    console.log("🛑 Shutting down...");
    isShuttingDown = true;

    // Close MQTT
    if (mqttClient) {
        mqttClient.end(false, () => {
            console.log("✅ MQTT closed");
        });
    }

    // Close WebSocket
    if (socketClient) {
        socketClient.disconnect();
        console.log("✅ WebSocket closed");
    }

    setTimeout(() => {
        console.log("✅ Shutdown complete");
        process.exit(0);
    }, 1000);
}

// Set up cron job to store data every 2 minutes (at 00, 02, 04, ..., 58 minutes of each hour)
// Schedule format: sec min hour day month day-of-week
cron.schedule("0 */2 * * * *", sendDataToAPI, {
    scheduled: true,
    timezone: TIMEZONE, // Adjust to your timezone
});

// Error handlers
process.on("SIGINT", shutdown);
process.on("SIGTERM", shutdown);
process.on("uncaughtException", (error) => {
    console.error("💥 Uncaught Exception:", error.message);
    shutdown();
});
process.on("unhandledRejection", (reason) => {
    console.error("💥 Unhandled Rejection:", reason);
    shutdown();
});

// Start everything
console.log("🚀 Starting MQTT-WebSocket Bridge...");
initMQTT();
initWebSocket();
startHealthCheck();
