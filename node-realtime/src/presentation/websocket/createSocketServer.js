import { createServer } from 'node:http';
import { Server } from 'socket.io';
import { registerSocketHandlers } from './socketHandler.js';

// SRP: Socket.io listener only. No pace or geofence logic in the connection handler.
export function createSocketServer() {
  const httpServer = createServer((req, res) => {
    if (req.url === '/health') {
      res.writeHead(200, { 'Content-Type': 'application/json' });
      res.end(JSON.stringify({ service: 'socket.io', status: 'ok' }));
      return;
    }
    res.writeHead(404);
    res.end();
  });

  const io = new Server(httpServer, {
    cors: { origin: true },
  });

  registerSocketHandlers(io);

  return { httpServer, io };
}
