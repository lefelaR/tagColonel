function courseRoom(courseId) {
  return `course:${courseId}`;
}

// Port the ingest use case calls. The room name stays in this adapter.
export function createSocketBroadcaster(io) {
  return {
    emitCartUpdate(courseId, update) {
      io.to(courseRoom(courseId)).emit('cart:update', update);
    },

    emitMarshalMessage(courseId, message) {
      io.to(courseRoom(courseId)).emit('marshal:message', message);
    },
  };
}
