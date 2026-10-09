// DIP: callers depend on this frozen config, not on process.env scattered through the app.

function required(env, key) {
  const value = env[key];
  if (value === undefined || value === '') {
    throw new Error(`Missing required environment variable ${key}`);
  }
  return value;
}

function requiredPort(env, key) {
  const port = Number(required(env, key));
  if (!Number.isInteger(port) || port < 1 || port > 65535) {
    throw new Error(`${key} must be a TCP port`);
  }
  return port;
}

export function loadConfig(env) {
  return Object.freeze({
    port: requiredPort(env, 'PORT'),
    socketPort: requiredPort(env, 'SOCKET_PORT'),
    redisHost: required(env, 'REDIS_HOST'),
    redisPort: requiredPort(env, 'REDIS_PORT'),
    laravelApi: required(env, 'LARAVEL_API'),
  });
}
