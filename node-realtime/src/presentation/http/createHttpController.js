import { parseGeofenceBody } from './parseGeofenceBody.js';
import { parseIngestBody } from './parseIngestBody.js';

function asyncRoute(handler) {
  return (req, res, next) => {
    Promise.resolve(handler(req, res)).catch(next);
  };
}

// Presentation only: parse the body, call the use case, write the status code.
export function createHttpController({ ingestGps, checkGeofence }) {
  return {
    ingest: asyncRoute(async (req, res) => {
      const parsed = parseIngestBody(req.body);

      if (!parsed.ok) {
        res.status(422).json({ message: parsed.message });
        return;
      }

      res.status(201).json(await ingestGps.execute(parsed.value));
    }),

    geofence: asyncRoute(async (req, res) => {
      const parsed = parseGeofenceBody(req.body);

      if (!parsed.ok) {
        res.status(422).json({ message: parsed.message });
        return;
      }

      res.json({ inside: checkGeofence.execute(parsed.value) });
    }),
  };
}
