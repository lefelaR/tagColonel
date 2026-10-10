// Same keys as Laravel LiveCartRedisRepository, so both services share one live map.

function geoKey(courseId) {
  return `fairwayflow:course:${courseId}:live`;
}

function cartKey(courseId, cartId) {
  return `fairwayflow:course:${courseId}:cart:${cartId}`;
}

function updateChannel(courseId) {
  return `fairwayflow:course:${courseId}:updates`;
}

export function createLiveCartRedisRepo(redis) {
  return {
    async saveLivePosition(position) {
      const added = await redis.geoAdd(geoKey(position.courseId), {
        longitude: position.lng,
        latitude: position.lat,
        member: String(position.cartId),
      });

      if (added === null || added === undefined) {
        throw new Error('Redis GEOADD failed for a live cart position.');
      }

      await redis.hSet(cartKey(position.courseId, position.cartId), {
        speed: String(position.speed),
      });
    },

    async getPaceStatus(courseId, cartId) {
      const status = await redis.hGet(cartKey(courseId, cartId), 'pace_status');
      return status ?? null;
    },

    async savePaceStatus(courseId, cartId, status) {
      await redis.hSet(cartKey(courseId, cartId), { pace_status: status });
    },

    async publish(courseId, payload) {
      await redis.publish(updateChannel(courseId), JSON.stringify(payload));
    },
  };
}
