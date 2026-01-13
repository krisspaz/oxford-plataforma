<?php

namespace App\Service\Performance;

class RedisCacheService
{
    public function get(string $key)
    {
        // Redis get stub
        return null;
    }

    public function set(string $key, $value, int $ttl = 3600): void
    {
        // Redis set stub
    }
}
