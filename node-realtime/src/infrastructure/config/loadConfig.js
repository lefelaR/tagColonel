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

function flag(env, key, fallback) {
  const value = env[key];
  if (value === undefined || value === '') {
    return fallback;
  }
  if (value === 'true') {
    return true;
  }
  if (value === 'false') {
    return false;
  }
  throw new Error(`${key} must be true or false`);
}

function optionalPositiveInt(env, key, fallback) {
  const value = env[key];
  if (value === undefined || value === '') {
    return fallback;
  }
  const parsed = Number(value);
  if (!Number.isInteger(parsed) || parsed < 1) {
    throw new Error(`${key} must be a positive integer`);
  }
  return parsed;
}

export function loadConfig(env) {
  return Object.freeze({
    port: requiredPort(env, 'PORT'),
    socketPort: requiredPort(env, 'SOCKET_PORT'),
    redisHost: required(env, 'REDIS_HOST'),
    redisPort: requiredPort(env, 'REDIS_PORT'),
    laravelApi: required(env, 'LARAVEL_API'),
    simulatorEnabled: flag(env, 'SIMULATOR_ENABLED', false),
    simulatorCarts: optionalPositiveInt(env, 'SIMULATOR_CARTS', 30),
    simulatorIntervalMs: optionalPositiveInt(env, 'SIMULATOR_INTERVAL_MS', 3000),
  });
}
