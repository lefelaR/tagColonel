import express from 'express';

// SRP: HTTP API only. Socket.io lives in its own server.
export function createApp(config) {
  const app = express();
  app.disable('x-powered-by');

  app.get('/health', (_req, res) => {
    res.json({
      service: 'node-realtime',
      status: 'ok',
      redisHost: config.redisHost,
      laravelApi: config.laravelApi,
    });
  });

  return app;
}
