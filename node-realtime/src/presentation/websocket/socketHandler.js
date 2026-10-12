function courseRoom(courseId) {
  return `course:${courseId}`;
}

function courseIdFrom(value) {
  const courseId = Number(value);

  if (!Number.isInteger(courseId) || courseId < 1) {
    return null;
  }

  return courseId;
}

// Socket transport only. Pace and geofence decisions stay in use cases.
export function registerSocketHandlers(io) {
  io.on('connection', (socket) => {
    socket.on('join:course', (courseId) => {
      const id = courseIdFrom(courseId);

      if (id === null) {
        return;
      }

      for (const room of socket.rooms ?? []) {
        if (typeof room === 'string' && room.startsWith('course:')) {
          socket.leave(room);
        }
      }

      socket.join(courseRoom(id));
    });

    socket.on('marshal:message', (payload) => {
      const courseId = courseIdFrom(payload?.course_id);
      const message = typeof payload?.message === 'string' ? payload.message.trim() : '';

      if (courseId === null || message === '') {
        return;
      }

      io.to(courseRoom(courseId)).emit('marshal:message', {
        course_id: courseId,
        message: message.slice(0, 255),
      });
    });
  });
}
