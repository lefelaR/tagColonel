import express from 'express';

// SRP: HTTP API only. Socket.io lives in its own server.
export function createApp({ config, controller }) {
  const app = express();
  app.disable('x-powered-by');
  app.use(express.json({ limit: '100kb' }));

  app.get('/health', (_req, res) => {
    res.json({
      service: 'node-realtime',
      status: 'ok',
      redisHost: config.redisHost,
      laravelApi: config.laravelApi,
    });
  });

  app.post('/ingest', controller.ingest);
  app.post('/geofence/check', controller.geofence);

  app.use((error, _req, res, next) => {
    if (error instanceof SyntaxError && Object.hasOwn(error, 'body')) {
      res.status(400).json({ message: 'Request body must be JSON' });
      return;
    }

    next(error);
  });

  return app;
}
