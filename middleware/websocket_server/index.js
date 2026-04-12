import { Server } from "socket.io";
import { createServer } from "http";
import express from "express";
import dotenv from "dotenv";
dotenv.config({ path: "../../.env" });

const app = express();
const server = createServer(app);
const io = new Server(server, {
    cors: {
        origin: "*",
        methods: ["GET", "POST"],
    },
});

// Store connected clients by room/device
const connectedClients = new Map();
let messageStats = {
    total: 0,
    lastReceived: null,
};

const latestData = new Map();
// Cache untuk data overview dashboard (baru ditambahkan)
let lastOverviewPayload = null;

io.on("connection", (socket) => {
    console.log(`🔌 Client connected: ${socket.id}`);

    if (latestData.size > 0) {
        latestData.forEach((data) => {
            socket.emit("sensor_data", data);
        });
    }

    if (lastOverviewPayload) {
        console.log(
            `📦 Mengirim data overview terakhir ke client ${socket.id}`,
        );
        socket.emit("dashboard_overview_update", lastOverviewPayload);
    }

    // Handle MQTT data from bridge
    socket.on("mqtt_data", (data) => {
        messageStats.total++;
        messageStats.lastReceived = new Date().toISOString();
        console.log("data", data);

        if (!data || typeof data !== "object") return;

        const key = data.deviceName;
        latestData.set(key, {
            ...data,
        });

        // Broadcast to frontend clients
        // Option A: Broadcast to all clients
        socket.broadcast.emit("sensor_data", data);
    });

    // Handle data overview yang sudah diagregasi
    socket.on("overview_data", (payload) => {
        console.log("📊 Menerima data overview agregat.");
        lastOverviewPayload = payload;
        // Langsung broadcast payload ini ke semua client dengan event baru
        io.emit("dashboard_overview_update", payload);
    });

    // Handle frontend client room joining
    socket.on("join_device_room", (deviceName) => {
        const roomName = `device_${deviceName}`;
        socket.join(roomName);
        console.log(`👥 Client ${socket.id} joined room: ${roomName}`);

        // Track clients per room
        if (!connectedClients.has(roomName)) {
            connectedClients.set(roomName, new Set());
        }
        connectedClients.get(roomName).add(socket.id);
    });

    socket.on("join_topic_room", (topic) => {
        const roomName = `topic_${topic.replace("/", "_")}`;
        socket.join(roomName);
        console.log(`📡 Client ${socket.id} joined topic: ${roomName}`);
    });

    // Health check endpoint
    socket.on("get_stats", () => {
        socket.emit("stats", {
            totalMessages: messageStats.total,
            lastReceived: messageStats.lastReceived,
            connectedClients: io.engine.clientsCount,
            rooms: Array.from(connectedClients.keys()),
        });
    });

    socket.on("disconnect", (reason) => {
        console.log(`🔌 Client disconnected: ${socket.id} (${reason})`);

        // Clean up client from rooms
        connectedClients.forEach((clients, room) => {
            clients.delete(socket.id);
            if (clients.size === 0) {
                connectedClients.delete(room);
            }
        });
    });
});

// Simple health endpoint
app.get("/health", (req, res) => {
    res.json({
        status: "ok",
        uptime: process.uptime(),
        clients: io.engine.clientsCount,
        messages: messageStats,
    });
});

const PORT = process.env.WEBSOCKET_PORT || 3000;
server.listen(PORT, () => {
    console.log(`🚀 WebSocket Server running on port ${PORT}`);
});
