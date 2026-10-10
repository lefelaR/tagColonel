// DDD: pure ray cast. Same crossing rule as the Laravel Polygon value object.

export function pointInPolygon(point, polygon) {
  let inside = false;
  const count = polygon.length;

  for (let i = 0, j = count - 1; i < count; j = i++) {
    const yi = polygon[i].lat;
    const xi = polygon[i].lng;
    const yj = polygon[j].lat;
    const xj = polygon[j].lng;
    const crossesLatitude = (yi > point.lat) !== (yj > point.lat);

    if (!crossesLatitude) {
      continue;
    }

    const intersectionLng = ((xj - xi) * (point.lat - yi)) / (yj - yi) + xi;

    if (point.lng < intersectionLng) {
      inside = !inside;
    }
  }

  return inside;
}
