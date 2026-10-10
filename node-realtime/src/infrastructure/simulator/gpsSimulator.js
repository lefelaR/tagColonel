const ORIGIN = { lat: -33.9249, lng: 18.4241 };
const STEP_DEGREES = 0.0002;

function paceSample(cartId) {
  if (cartId >= 29) {
    return { actual_seconds: 130, par_seconds: 100 };
  }

  if (cartId >= 26) {
    return { actual_seconds: 115, par_seconds: 100 };
  }

  return { actual_seconds: 100, par_seconds: 100 };
}

function createCarts(cartCount, random) {
  return Array.from({ length: cartCount }, (_unused, index) => {
    const cartId = index + 1;
    return {
      cartId,
      lat: ORIGIN.lat + ((index - cartCount / 2) * 0.0001),
      lng: ORIGIN.lng + ((index - cartCount / 2) * 0.0001),
      speed: 1 + (cartId % 5),
      hole: (cartId % 18) + 1,
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
  fetchImpl = globalThis.fetch,
  random = Math.random,
}) {
  const carts = createCarts(cartCount, random);

  async function postCart(cart) {
    step(cart);
    const response = await fetchImpl(`http://127.0.0.1:${port}/ingest`, {
      method: 'POST',
      headers: { 'content-type': 'application/json' },
      body: JSON.stringify({
        cart_id: cart.cartId,
        course_id: 1,
        lat: cart.lat,
        lng: cart.lng,
        speed: cart.speed,
        hole: cart.hole,
        ...paceSample(cart.cartId),
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
