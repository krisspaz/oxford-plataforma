<?php

namespace App\DTO;

class NotificationResponse
{
    public function __construct(
        public int $id,
        public string $type,
        public string $title,
        public string $message,
        public bool $read,
        public string $createdAt,
    ) {}
}
