<?php

namespace App\DTO\Response;

/**
 * API Response wrapper DTO
 */
class ApiResponse
{
    public bool $success;
    public ?string $message;
    public mixed $data;
    public ?array $errors;
    public ?array $meta;

    private function __construct()
    {
    }

    public static function success(mixed $data = null, ?string $message = null, ?array $meta = null): self
    {
        $response = new self();
        $response->success = true;
        $response->message = $message;
        $response->data = $data;
        $response->errors = null;
        $response->meta = $meta;
        return $response;
    }

    public static function error(string $message, ?array $errors = null, mixed $data = null): self
    {
        $response = new self();
        $response->success = false;
        $response->message = $message;
        $response->data = $data;
        $response->errors = $errors;
        $response->meta = null;
        return $response;
    }

    public static function paginated(array $items, int $total, int $page, int $perPage): self
    {
        return self::success($items, null, [
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage,
            'totalPages' => (int)ceil($total / $perPage),
        ]);
    }

    public function toArray(): array
    {
        $result = [
            'success' => $this->success,
        ];

        if ($this->message !== null) {
            $result['message'] = $this->message;
        }

        if ($this->data !== null) {
            $result['data'] = $this->data;
        }

        if ($this->errors !== null) {
            $result['errors'] = $this->errors;
        }

        if ($this->meta !== null) {
            $result['meta'] = $this->meta;
        }

        return $result;
    }
}
