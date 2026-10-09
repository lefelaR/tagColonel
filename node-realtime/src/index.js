import { loadConfig } from './infrastructure/config/loadConfig.js';
import { createApp } from './presentation/http/createApp.js';
import { createSocketServer } from './presentation/websocket/createSocketServer.js';

// Composition root: wire config, HTTP, and Socket.io. No business rules here.
const config = loadConfig(process.env);
const app = createApp(config);
const httpServer = app.listen(config.port, '0.0.0.0');
const socketServer = createSocketServer();

socketServer.listen(config.socketPort, '0.0.0.0');

function shutdown() {
  httpServer.close();
  socketServer.close();
}

process.on('SIGTERM', shutdown);
process.on('SIGINT', shutdown);
