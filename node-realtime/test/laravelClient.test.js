import assert from 'node:assert/strict';
import test from 'node:test';
import { createLaravelApiClient } from '../src/infrastructure/laravel/LaravelApiClient.js';

test('Laravel client posts an alert to /api/alerts', async () => {
  const posts = [];
  const client = createLaravelApiClient({
    baseUrl: 'http://laravel:8000/',
    http: {
      async post(url, body, options) {
        posts.push({ url, body, options });
      },
    },
  });

  await client.createAlert({
    courseId: 1,
    cartId: 29,
    paceStatus: 'very_slow',
    message: 'Slow play detected.',
  });

  assert.equal(posts[0].url, 'http://laravel:8000/api/alerts');
  assert.deepEqual(posts[0].body, {
    course_id: 1,
    cart_id: 29,
    pace_status: 'very_slow',
    message: 'Slow play detected.',
  });
});
