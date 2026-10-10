function fail(message) {
  return { ok: false, message };
}

function requiredInteger(value, name, min, max) {
  if (typeof value !== 'number' || !Number.isInteger(value)) {
    return `${name} must be an integer`;
  }

  if (value < min || value > max) {
    return `${name} must be between ${min} and ${max}`;
  }

  return null;
}

function requiredNumber(value, name, min, max) {
  if (typeof value !== 'number' || !Number.isFinite(value) || value < min || value > max) {
    return `${name} must be between ${min} and ${max}`;
  }

  return null;
}

export function parseIngestBody(body) {
  if (body === null || typeof body !== 'object' || Array.isArray(body)) {
    return fail('Ingest body must be an object');
  }

  const cartError = requiredInteger(body.cart_id, 'cart_id', 1, Number.MAX_SAFE_INTEGER);
  if (cartError) return fail(cartError);

  const courseError = requiredInteger(body.course_id, 'course_id', 1, Number.MAX_SAFE_INTEGER);
  if (courseError) return fail(courseError);

  const latError = requiredNumber(body.lat, 'lat', -90, 90);
  if (latError) return fail(latError);

  const lngError = requiredNumber(body.lng, 'lng', -180, 180);
  if (lngError) return fail(lngError);

  const speedError = requiredNumber(body.speed, 'speed', 0, 15);
  if (speedError) return fail(speedError);

  const holeError = requiredInteger(body.hole, 'hole', 1, 18);
  if (holeError) return fail(holeError);

  const hasActual = body.actual_seconds !== undefined && body.actual_seconds !== null;
  const hasPar = body.par_seconds !== undefined && body.par_seconds !== null;

  if (hasActual !== hasPar) {
    return fail('actual_seconds and par_seconds must be sent together');
  }

  let actualSeconds = null;
  let parSeconds = null;

  if (hasActual) {
    const actualError = requiredInteger(body.actual_seconds, 'actual_seconds', 0, Number.MAX_SAFE_INTEGER);
    if (actualError) return fail(actualError);

    const parError = requiredInteger(body.par_seconds, 'par_seconds', 1, Number.MAX_SAFE_INTEGER);
    if (parError) return fail(parError);

    actualSeconds = body.actual_seconds;
    parSeconds = body.par_seconds;
  }

  return {
    ok: true,
    value: {
      cartId: body.cart_id,
      courseId: body.course_id,
      lat: body.lat,
      lng: body.lng,
      speed: body.speed,
      hole: body.hole,
      actualSeconds,
      parSeconds,
    },
  };
}
