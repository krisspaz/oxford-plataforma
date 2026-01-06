<?php

namespace App\DTO;

class ApiResponse
{
    public function __construct(
        public bool $success,
        public mixed $data = null,
        public ?string $message = null,
        public ?array $meta = null,
    ) {}
    
    public static function success(mixed $data, ?string $message = null, ?array $meta = null): self
    {
        return new self(true, $data, $message, $meta);
    }
    
    public static function error(string $message, mixed $data = null): self
    {
        return new self(false, $data, $message);
    }
}
