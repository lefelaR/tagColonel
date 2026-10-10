import { paceStatus } from '../../domain/pace/PaceService.js';

const PACE_RANK = {
  on_pace: 0,
  slow: 1,
  very_slow: 2,
};

// SOLID DIP: Redis, Laravel, and Socket.io are injected. This file only orchestrates.
export function createIngestGpsUseCase({ liveCarts, laravelApi, broadcaster }) {
  return {
    async execute(dto) {
      const previousPace = dto.actualSeconds === null
        ? null
        : await liveCarts.getPaceStatus(dto.courseId, dto.cartId);
      const nextPace = dto.actualSeconds === null
        ? null
        : paceStatus(dto.actualSeconds, dto.parSeconds);
      const shouldAlert = nextPace !== null && (PACE_RANK[nextPace] ?? 0) > (PACE_RANK[previousPace] ?? 0);

      await liveCarts.saveLivePosition(dto);

      const update = {
        cart_id: dto.cartId,
        course_id: dto.courseId,
        lat: dto.lat,
        lng: dto.lng,
        speed: dto.speed,
        hole: dto.hole,
        pace_status: nextPace,
      };

      await liveCarts.publish(dto.courseId, update);
      broadcaster.emitCartUpdate(dto.courseId, update);

      let alertSent = false;

      if (shouldAlert) {
        const message = 'Slow play detected.';
        await laravelApi.createAlert({
          courseId: dto.courseId,
          cartId: dto.cartId,
          paceStatus: nextPace,
          message,
        });
        broadcaster.emitMarshalMessage(dto.courseId, {
          course_id: dto.courseId,
          cart_id: dto.cartId,
          pace_status: nextPace,
          message,
        });
        alertSent = true;
      }

      if (nextPace !== null) {
        await liveCarts.savePaceStatus(dto.courseId, dto.cartId, nextPace);
      }

      return { ...update, alert_sent: alertSent };
    },
  };
}
