import { pointInPolygon } from '../../domain/geofence/GeofenceService.js';

// SOLID SRP: one question, is this point inside the polygon.
export function createCheckGeofenceUseCase() {
  return {
    execute(dto) {
      return pointInPolygon({ lat: dto.lat, lng: dto.lng }, dto.polygon);
    },
  };
}
