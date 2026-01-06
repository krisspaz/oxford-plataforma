<?php

namespace App\DTO;

class PaginatedResponse
{
    public function __construct(
        public array $items,
        public int $total,
        public int $page,
        public int $limit,
        public int $totalPages,
    ) {}
    
    public static function create(array $items, int $total, int $page, int $limit): self
    {
        return new self(
            items: $items,
            total: $total,
            page: $page,
            limit: $limit,
            totalPages: (int) ceil($total / $limit),
        );
    }
}
