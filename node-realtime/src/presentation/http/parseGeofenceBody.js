function fail(message) {
  return { ok: false, message };
}

function coordinate(value, name, min, max) {
  if (typeof value !== 'number' || !Number.isFinite(value) || value < min || value > max) {
    return `${name} must be between ${min} and ${max}`;
  }

  return null;
}

export function parseGeofenceBody(body) {
  if (body === null || typeof body !== 'object' || Array.isArray(body)) {
    return fail('Geofence body must be an object');
  }

  const latError = coordinate(body.lat, 'lat', -90, 90);
  if (latError) return fail(latError);

  const lngError = coordinate(body.lng, 'lng', -180, 180);
  if (lngError) return fail(lngError);

  if (!Array.isArray(body.polygon) || body.polygon.length < 3) {
    return fail('polygon must contain at least 3 points');
  }

  const polygon = [];

  for (const [index, vertex] of body.polygon.entries()) {
    if (vertex === null || typeof vertex !== 'object' || Array.isArray(vertex)) {
      return fail(`polygon[${index}] must be a point`);
    }

    const vertexLatError = coordinate(vertex.lat, `polygon[${index}].lat`, -90, 90);
    if (vertexLatError) return fail(vertexLatError);

    const vertexLngError = coordinate(vertex.lng, `polygon[${index}].lng`, -180, 180);
    if (vertexLngError) return fail(vertexLngError);

    polygon.push({ lat: vertex.lat, lng: vertex.lng });
  }

  return {
    ok: true,
    value: { lat: body.lat, lng: body.lng, polygon },
  };
}
