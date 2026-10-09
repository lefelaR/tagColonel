<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Redis;

use Domain\Cart\Repositories\CartRepositoryInterface;
use Domain\Cart\ValueObjects\CartId;
use Domain\Cart\ValueObjects\Speed;
use Domain\Course\ValueObjects\CourseId;
use Domain\Course\ValueObjects\GeoCoordinate;
use Illuminate\Redis\Connections\Connection;

// Redis GEO adapter for the live map. GEOADD writes; GEORADIUS reads one course.
final class LiveCartRedisRepository implements CartRepositoryInterface
{
    private const COURSE_RADIUS_METERS = 25000;

    public function __construct(private readonly Connection $redis) {}

    public function saveLivePosition(
        CartId $cartId,
        CourseId $courseId,
        GeoCoordinate $coordinate,
        Speed $speed,
    ): void {
        $geoKey = $this->geoKey($courseId);
        $added = $this->redis->command('geoadd', [
            $geoKey,
            $coordinate->lng,
            $coordinate->lat,
            (string) $cartId,
        ]);

        if ($added === false) {
            throw new \RuntimeException('Redis GEOADD failed for a live cart position.');
        }

        $stored = $this->redis->command('hset', [
            $this->speedKey($courseId, $cartId),
            'speed',
            (string) $speed->metersPerSecond,
        ]);

        if ($stored === false) {
            throw new \RuntimeException('Redis HSET failed for a live cart speed.');
        }
    }

    public function findLiveByCourse(CourseId $courseId): array
    {
        $geoKey = $this->geoKey($courseId);
        $members = $this->redis->command('zrange', [$geoKey, 0, 0]);

        if (! is_array($members) || $members === []) {
            return [];
        }

        $origin = $this->redis->command('geopos', [$geoKey, (string) $members[0]]);

        if (! isset($origin[0][0], $origin[0][1])) {
            return [];
        }

        $rows = $this->redis->command('georadius', [
            $geoKey,
            (float) $origin[0][0],
            (float) $origin[0][1],
            self::COURSE_RADIUS_METERS,
            'm',
            ['WITHCOORD'],
        ]);

        if (! is_array($rows)) {
            return [];
        }

        $live = [];

        foreach ($rows as $row) {
            if (! is_array($row)) {
                continue;
            }

            $parsed = $this->parseRadiusRow($row);

            if ($parsed === null || $parsed['cart_id'] < 1) {
                continue;
            }

            $speed = $this->redis->command('hget', [
                $this->speedKey($courseId, new CartId($parsed['cart_id'])),
                'speed',
            ]);

            $live[] = [
                'cart_id' => $parsed['cart_id'],
                'latitude' => $parsed['latitude'],
                'longitude' => $parsed['longitude'],
                'speed' => is_numeric($speed) ? (float) $speed : 0.0,
            ];
        }

        return $live;
    }

    /**
     * @param array<int, mixed> $row
     * @return array{cart_id: int, latitude: float, longitude: float}|null
     */
    private function parseRadiusRow(array $row): ?array
    {
        $member = $row[0] ?? null;

        if (! is_string($member) && ! is_int($member)) {
            return null;
        }

        $coord = null;

        foreach ($row as $part) {
            if (is_array($part) && isset($part[0], $part[1]) && is_numeric($part[0]) && is_numeric($part[1])) {
                $coord = $part;
            }
        }

        if ($coord === null) {
            return null;
        }

        return [
            'cart_id' => (int) $member,
            'longitude' => (float) $coord[0],
            'latitude' => (float) $coord[1],
        ];
    }

    private function geoKey(CourseId $courseId): string
    {
        return 'fairwayflow:course:'.$courseId->value.':live';
    }

    private function speedKey(CourseId $courseId, CartId $cartId): string
    {
        return 'fairwayflow:course:'.$courseId->value.':cart:'.$cartId->value;
    }
}
