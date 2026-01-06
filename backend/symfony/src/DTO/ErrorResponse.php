<?php

namespace App\DTO;

class ErrorResponse
{
    public function __construct(
        public string $error,
        public string $message,
        public int $statusCode,
        public ?array $details = null,
        public ?string $traceId = null,
    ) {}
}
