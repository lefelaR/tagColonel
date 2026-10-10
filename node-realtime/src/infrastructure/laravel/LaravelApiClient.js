// SOLID DIP: the use case depends on createAlert(), not on axios.
export function createLaravelApiClient({ baseUrl, http, timeoutMs = 2000 }) {
  const root = baseUrl.replace(/\/$/, '');

  return {
    async createAlert({ courseId, cartId, paceStatus, message }) {
      await http.post(`${root}/api/alerts`, {
        course_id: courseId,
        cart_id: cartId,
        pace_status: paceStatus,
        message,
      }, { timeout: timeoutMs });
    },
  };
}
