// Rondebosch stays first. The other three are selectable Cape Town courses.
export const CAPE_TOWN_COURSES = [
  { courseId: 1, lat: -33.956, lng: 18.4943 },
  { courseId: 2, lat: -34.0136, lng: 18.4851 },
  { courseId: 3, lat: -33.8617, lng: 18.4927 },
  { courseId: 4, lat: -34.0722, lng: 18.4279 },
];

const STEP_DEGREES = 0.0002;

function paceSample(index, cartCount) {
  if (index >= cartCount - 2) {
    return { actual_seconds: 130, par_seconds: 100 };
  }

  if (index >= cartCount - 5) {
    return { actual_seconds: 115, par_seconds: 100 };
  }

  return { actual_seconds: 100, par_seconds: 100 };
}

function createCarts(course, cartCount, random) {
  return Array.from({ length: cartCount }, (_unused, index) => {
    const cartId = ((course.courseId - 1) * cartCount) + index + 1;
    return {
      cartId,
      courseId: course.courseId,
      lat: course.lat + ((index - cartCount / 2) * 0.0001),
      lng: course.lng + ((index - cartCount / 2) * 0.0001),
      speed: 1 + (cartId % 5),
      hole: (cartId % 18) + 1,
      pace: paceSample(index, cartCount),
      random,
    };
  });
}

function step(cart) {
  cart.lat = Math.min(90, Math.max(-90, cart.lat + ((cart.random() - 0.5) * STEP_DEGREES)));
  cart.lng = Math.min(180, Math.max(-180, cart.lng + ((cart.random() - 0.5) * STEP_DEGREES)));
}

// Demo adapter. Talks to this service over HTTP so ingest validation still runs.
export function startGpsSimulator({
  port,
  cartCount = 30,
  intervalMs = 3000,
  courses,
  fetchImpl = globalThis.fetch,
  random = Math.random,
}) {
  const fleets = courses ?? [CAPE_TOWN_COURSES[0]];
  const carts = fleets.flatMap((course) => createCarts(course, cartCount, random));

  async function postCart(cart) {
    step(cart);
    const response = await fetchImpl(`http://127.0.0.1:${port}/ingest`, {
      method: 'POST',
      headers: { 'content-type': 'application/json' },
      body: JSON.stringify({
        cart_id: cart.cartId,
        course_id: cart.courseId,
        lat: cart.lat,
        lng: cart.lng,
        speed: cart.speed,
        hole: cart.hole,
        ...cart.pace,
      }),
    });

    if (!response.ok) {
      throw new Error(`Ingest failed for cart ${cart.cartId} with status ${response.status}`);
    }
  }

  async function tick() {
    const results = await Promise.allSettled(carts.map((cart) => postCart(cart)));

    for (const result of results) {
      if (result.status === 'rejected') {
        console.error('GPS simulator', result.reason);
      }
    }
  }

  const timer = setInterval(() => {
    void tick();
  }, intervalMs);

  void tick();

  return () => clearInterval(timer);
}
